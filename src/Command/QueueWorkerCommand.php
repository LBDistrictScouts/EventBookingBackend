<?php
declare(strict_types=1);

namespace App\Command;

use App\Queue\Processor\CheckInProcessor;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Enqueue\Consumption\ChainExtension;
use Enqueue\Consumption\Extension\ReplyExtension;
use Enqueue\Consumption\QueueConsumer;

/**
 * QueueWorker command.
 */
class QueueWorkerCommand extends Command
{
    /**
     * The name of this command.
     *
     * @var string
     */
    protected string $name = 'queue_worker';

    /**
     * Get the default command name.
     *
     * @return string
     */
    public static function defaultName(): string
    {
        return 'queue_worker';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Command description here.';
    }

    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/5/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser)
            ->setDescription(static::getDescription());
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int The exit code or null for success
     * @throws \Exception
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        /** @var \Enqueue\Sqs\SqsContext $context */
        $context = Configure::read('Queue.Context');

        /** @var string $queueName */
        $queueName = Configure::read('Queue.QueueName');

//        $queue = $context->createQueue($queueName);
//        $consumer = $context->createConsumer($queue);
//
//        /** @var \Enqueue\Sqs\SqsMessage $message */
//        $message = $consumer->receive();
//
//        $consumer->acknowledge($message);

        $queueConsumer = new QueueConsumer($context, new ChainExtension([
            new ReplyExtension(),
        ]));

        $queueConsumer->bind($queueName, new CheckInProcessor());

        $queueConsumer->consume();

        return 0;
    }
}
