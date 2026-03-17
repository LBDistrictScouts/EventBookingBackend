<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use App\Command\SendEventRemindersCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\ConsoleOutput;
use Cake\Core\Configure;
use Cake\I18n\DateTime;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\TestCase;
use Interop\Queue\Consumer;
use Interop\Queue\Context;
use Interop\Queue\Destination;
use Interop\Queue\Exception\PurgeQueueNotSupportedException;
use Interop\Queue\Exception\TemporaryQueueNotSupportedException;
use Interop\Queue\Message;
use Interop\Queue\Producer;
use Interop\Queue\Queue;
use Interop\Queue\SubscriptionConsumer;
use Interop\Queue\Topic;
use RuntimeException;

class SendEventRemindersCommandTest extends TestCase
{
    use EmailTrait;

    /**
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Groups',
        'app.ParticipantTypes',
        'app.Sections',
        'app.Events',
        'app.EventsSections',
        'app.Checkpoints',
        'app.Entries',
        'app.Participants',
        'app.CheckIns',
        'app.ParticipantsCheckIns',
        'app.Questions',
    ];

    public function tearDown(): void
    {
        parent::tearDown();

        Configure::delete('Queue.Context');
        Configure::delete('Queue.QueueName');
    }

    public function testDefaultName(): void
    {
        $this->assertSame('send_event_reminders', SendEventRemindersCommand::defaultName());
    }

    public function testGetDescription(): void
    {
        $this->assertSame(
            'Send reminder emails for entries whose event starts within the reminder window.',
            SendEventRemindersCommand::getDescription(),
        );
    }

    public function testBuildOptionParser(): void
    {
        $command = new SendEventRemindersCommand();
        $parser = new ConsoleOptionParser('send_event_reminders');

        $result = $command->buildOptionParser($parser);

        $this->assertSame($parser, $result);
        $this->assertSame(SendEventRemindersCommand::getDescription(), $result->getDescription());
        $this->assertArrayHasKey('lead-hours', $result->options());
        $this->assertArrayHasKey('window-minutes', $result->options());
        $this->assertArrayHasKey('dry-run', $result->options());
    }

    public function testExecuteSendsReminderAndMarksEntrySent(): void
    {
        $queueState = $this->configureQueueContext();

        $events = $this->getTableLocator()->get('Events');
        /** @var \App\Model\Entity\Event $event */
        $event = $events->get('3a6d9419-b621-45cf-a13e-4db9647bf5bc');
        $event->start_time = new DateTime('2026-03-18 20:00:00');
        $event->finished = false;
        $events->saveOrFail($event);

        $entries = $this->getTableLocator()->get('Entries');
        /** @var \App\Model\Entity\Entry $entry */
        $entry = $entries->get('2342ad37-13f0-4fd1-bd3f-2032273626ce');
        $entry->entry_email = 'reminder@example.com';
        $entry->reminder_sent = null;
        $entries->saveOrFail($entry);

        $command = new SendEventRemindersCommand();
        $result = $command->execute(
            new Arguments([], [
                'lead-hours' => '12',
                'window-minutes' => '60',
                'now' => '2026-03-18 08:00:00',
                'dry-run' => false,
            ], []),
            $this->createConsoleIo(),
        );

        $this->assertSame(0, $result);
        $this->assertMailCount(0);
        $this->assertCount(1, $queueState->bodies);
        $payload = json_decode($queueState->bodies[0], true);
        $this->assertSame('entry_reminder', $payload['type']);
        $this->assertSame('2342ad37-13f0-4fd1-bd3f-2032273626ce', $payload['entry_id']);

        $secondResult = $command->execute(
            new Arguments([], [
                'lead-hours' => '12',
                'window-minutes' => '60',
                'now' => '2026-03-18 08:00:00',
                'dry-run' => false,
            ], []),
            $this->createConsoleIo(),
        );

        $this->assertSame(0, $secondResult);
        $this->assertMailCount(0);
        $this->assertCount(2, $queueState->bodies);

        $row = $entries->getConnection()
            ->execute(
                'SELECT reminder_sent FROM entries WHERE id = :id',
                ['id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce'],
            )
            ->fetch('assoc');
        $this->assertIsArray($row);
        $this->assertNull($row['reminder_sent'] ?? null);
    }

    public function testExecuteDryRunDoesNotSendOrMarkEntry(): void
    {
        $queueState = $this->configureQueueContext();

        $events = $this->getTableLocator()->get('Events');
        /** @var \App\Model\Entity\Event $event */
        $event = $events->get('3a6d9419-b621-45cf-a13e-4db9647bf5bc');
        $event->start_time = new DateTime('2026-03-18 20:00:00');
        $event->finished = false;
        $events->saveOrFail($event);

        $entries = $this->getTableLocator()->get('Entries');
        /** @var \App\Model\Entity\Entry $entry */
        $entry = $entries->get('2342ad37-13f0-4fd1-bd3f-2032273626ce');
        $entry->reminder_sent = null;
        $entries->saveOrFail($entry);

        $command = new SendEventRemindersCommand();
        $result = $command->execute(
            new Arguments([], [
                'lead-hours' => '12',
                'window-minutes' => '60',
                'now' => '2026-03-18 08:00:00',
                'dry-run' => true,
            ], []),
            $this->createConsoleIo(),
        );

        $this->assertSame(0, $result);
        $this->assertMailCount(0);
        $this->assertCount(0, $queueState->bodies);

        $row = $entries->getConnection()
            ->execute(
                'SELECT reminder_sent FROM entries WHERE id = :id',
                ['id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce'],
            )
            ->fetch('assoc');
        $this->assertIsArray($row);
        $this->assertNull($row['reminder_sent'] ?? null);
    }

    /**
     * @return object{bodies: array<int, string>}
     */
    private function configureQueueContext(): object
    {
        $queueState = new class {
            /**
             * @var array<int, string>
             */
            public array $bodies = [];
        };

        $queue = new class implements Queue {
            public function __construct(private string $queueName = 'test-queue')
            {
            }

            public function getQueueName(): string
            {
                return $this->queueName;
            }
        };

        $producer = new class ($queueState) implements Producer {
            private ?int $deliveryDelay = null;
            private ?int $priority = null;
            private ?int $timeToLive = null;

            /**
             * @param object{bodies: array<int, string>} $queueState
             */
            public function __construct(private object $queueState)
            {
            }

            public function send(Destination $destination, Message $message): void
            {
                $this->queueState->bodies[] = $message->getBody();
            }

            public function setDeliveryDelay(?int $deliveryDelay = null): self
            {
                $this->deliveryDelay = $deliveryDelay;

                return $this;
            }

            public function getDeliveryDelay(): ?int
            {
                return $this->deliveryDelay;
            }

            public function setPriority(?int $priority = null): self
            {
                $this->priority = $priority;

                return $this;
            }

            public function getPriority(): ?int
            {
                return $this->priority;
            }

            public function setTimeToLive(?int $timeToLive = null): self
            {
                $this->timeToLive = $timeToLive;

                return $this;
            }

            public function getTimeToLive(): ?int
            {
                return $this->timeToLive;
            }
        };

        $context = new class ($queue, $producer) implements Context {
            public function __construct(private Queue $queue, private Producer $producer)
            {
            }

            /**
             * @param array<string, mixed> $properties
             * @param array<string, mixed> $headers
             */
            public function createMessage(string $body = '', array $properties = [], array $headers = []): Message
            {
                return new class ($body) implements Message {
                    /**
                     * @var array<string, mixed>
                     */
                    private array $properties = [];

                    /**
                     * @var array<string, mixed>
                     */
                    private array $headers = [];

                    private ?string $correlationId = null;
                    private ?string $messageId = null;
                    private ?int $timestamp = null;
                    private ?string $replyTo = null;
                    private bool $redelivered = false;

                    public function __construct(private string $body)
                    {
                    }

                    public function getBody(): string
                    {
                        return $this->body;
                    }

                    public function setBody(string $body): void
                    {
                        $this->body = $body;
                    }

                    /**
                     * @return array<string, mixed>
                     */
                    public function getProperties(): array
                    {
                        return $this->properties;
                    }

                    /**
                     * @param array<string, mixed> $properties
                     */
                    public function setProperties(array $properties): void
                    {
                        $this->properties = $properties;
                    }

                    public function setProperty(string $name, mixed $value): void
                    {
                        $this->properties[$name] = $value;
                    }

                    public function getProperty(string $name, mixed $default = null): mixed
                    {
                        return $this->properties[$name] ?? $default;
                    }

                    /**
                     * @return array<string, mixed>
                     */
                    public function getHeaders(): array
                    {
                        return $this->headers;
                    }

                    /**
                     * @param array<string, mixed> $headers
                     */
                    public function setHeaders(array $headers): void
                    {
                        $this->headers = $headers;
                    }

                    public function setHeader(string $name, mixed $value): void
                    {
                        $this->headers[$name] = $value;
                    }

                    public function getHeader(string $name, mixed $default = null): mixed
                    {
                        return $this->headers[$name] ?? $default;
                    }

                    public function setRedelivered(bool $redelivered): void
                    {
                        $this->redelivered = $redelivered;
                    }

                    public function isRedelivered(): bool
                    {
                        return $this->redelivered;
                    }

                    public function setCorrelationId(?string $correlationId = null): void
                    {
                        $this->correlationId = $correlationId;
                    }

                    public function getCorrelationId(): ?string
                    {
                        return $this->correlationId;
                    }

                    public function setMessageId(?string $messageId = null): void
                    {
                        $this->messageId = $messageId;
                    }

                    public function getMessageId(): ?string
                    {
                        return $this->messageId;
                    }

                    public function setTimestamp(?int $timestamp = null): void
                    {
                        $this->timestamp = $timestamp;
                    }

                    public function getTimestamp(): ?int
                    {
                        return $this->timestamp;
                    }

                    public function setReplyTo(?string $replyTo = null): void
                    {
                        $this->replyTo = $replyTo;
                    }

                    public function getReplyTo(): ?string
                    {
                        return $this->replyTo;
                    }
                };
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
                return $this->producer;
            }

            public function createConsumer(Destination $destination): Consumer
            {
                throw new RuntimeException('Not implemented in test context.');
            }

            public function createSubscriptionConsumer(): SubscriptionConsumer
            {
                throw new RuntimeException('Not implemented in test context.');
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

        return $queueState;
    }

    private function createConsoleIo(): ConsoleIo
    {
        $stdoutPath = tempnam(sys_get_temp_dir(), 'stdout-');
        $stderrPath = tempnam(sys_get_temp_dir(), 'stderr-');

        if ($stdoutPath === false || $stderrPath === false) {
            throw new RuntimeException('Failed to create temporary console streams.');
        }

        return new ConsoleIo(
            new ConsoleOutput($stdoutPath),
            new ConsoleOutput($stderrPath),
        );
    }
}
