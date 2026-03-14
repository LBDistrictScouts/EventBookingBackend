<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use App\Command\QueueWorkerCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Interop\Queue\Consumer;
use Interop\Queue\Context;
use Interop\Queue\Destination;
use Interop\Queue\Exception\PurgeQueueNotSupportedException;
use Interop\Queue\Exception\SubscriptionConsumerNotSupportedException;
use Interop\Queue\Exception\TemporaryQueueNotSupportedException;
use Interop\Queue\Message;
use Interop\Queue\Producer;
use Interop\Queue\Queue;
use Interop\Queue\SubscriptionConsumer;
use Interop\Queue\Topic;
use RuntimeException;

/**
 * App\Command\QueueWorkerCommand Test Case
 *
 * @uses \App\Command\QueueWorkerCommand
 */
class QueueWorkerCommandTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        Configure::delete('Queue.Context');
        Configure::delete('Queue.QueueName');
    }

    /**
     * Test defaultName method
     *
     * @return void
     * @uses \App\Command\QueueWorkerCommand::defaultName()
     */
    public function testDefaultName(): void
    {
        $this->assertSame('queue_worker', QueueWorkerCommand::defaultName());
    }

    /**
     * Test getDescription method
     *
     * @return void
     * @uses \App\Command\QueueWorkerCommand::getDescription()
     */
    public function testGetDescription(): void
    {
        $this->assertSame('Command description here.', QueueWorkerCommand::getDescription());
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     * @uses \App\Command\QueueWorkerCommand::buildOptionParser()
     */
    public function testBuildOptionParser(): void
    {
        $command = new QueueWorkerCommand();
        $parser = new ConsoleOptionParser('queue_worker');

        $result = $command->buildOptionParser($parser);

        $this->assertSame($parser, $result);
        $this->assertSame(QueueWorkerCommand::getDescription(), $result->getDescription());
    }

    /**
     * Test execute method
     *
     * @return void
     * @uses \App\Command\QueueWorkerCommand::execute()
     */
    public function testExecute(): void
    {
        $queue = new class implements Queue {
            public function __construct(private string $queueName = 'test-queue')
            {
            }

            public function getQueueName(): string
            {
                return $this->queueName;
            }
        };

        $consumer = new class($queue) implements Consumer {
            public function __construct(private Queue $queue)
            {
            }

            public function getQueue(): Queue
            {
                return $this->queue;
            }

            public function receive(int $timeout = 0): ?Message
            {
                return null;
            }

            public function receiveNoWait(): ?Message
            {
                return null;
            }

            public function acknowledge(Message $message): void
            {
            }

            public function reject(Message $message, bool $requeue = false): void
            {
            }
        };

        $subscriptionConsumer = $this->createMock(SubscriptionConsumer::class);
        $subscriptionConsumer->expects($this->once())
            ->method('subscribe')
            ->with($consumer, $this->callback(is_callable(...)));
        $subscriptionConsumer->expects($this->once())
            ->method('consume')
            ->with(10000)
            ->willThrowException(new RuntimeException('stop'));
        $subscriptionConsumer->expects($this->never())
            ->method('unsubscribe');
        $subscriptionConsumer->expects($this->never())
            ->method('unsubscribeAll');

        $context = new class($queue, $consumer, $subscriptionConsumer) implements Context {
            public function __construct(
                private Queue $queue,
                private Consumer $consumer,
                private SubscriptionConsumer $subscriptionConsumer,
            ) {
            }

            public function createMessage(string $body = '', array $properties = [], array $headers = []): Message
            {
                throw new RuntimeException('Not implemented in test context.');
            }

            public function createTopic(string $topicName): Topic
            {
                throw new RuntimeException('Not implemented in test context.');
            }

            public function createQueue(string $queueName): Queue
            {
                return $this->queue;
            }

            public function createTemporaryQueue(): Queue
            {
                throw new TemporaryQueueNotSupportedException('Temporary queues are not required in this test.');
            }

            public function createProducer(): Producer
            {
                throw new RuntimeException('Not implemented in test context.');
            }

            public function createConsumer(Destination $destination): Consumer
            {
                return $this->consumer;
            }

            public function createSubscriptionConsumer(): SubscriptionConsumer
            {
                return $this->subscriptionConsumer;
            }

            public function purgeQueue(Queue $queue): void
            {
                throw new PurgeQueueNotSupportedException('Queue purging is not required in this test.');
            }

            public function close(): void
            {
            }
        };

        Configure::write('Queue.Context', $context);
        Configure::write('Queue.QueueName', 'test-queue');

        $command = new QueueWorkerCommand();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('stop');
        $command->execute(new Arguments([], [], []), new ConsoleIo());
    }
}
