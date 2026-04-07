<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\I18n\DateTime;
use Cake\ORM\Query\SelectQuery;
use Interop\Queue\Context;
use RuntimeException;
use Throwable;

class SendEventRemindersCommand extends Command
{
    /**
     * @return string
     */
    public static function defaultName(): string
    {
        return 'send_event_reminders';
    }

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Send reminder emails for entries whose event starts within the reminder window.';
    }

    /**
     * @param \Cake\Console\ConsoleOptionParser $parser Option parser.
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser)
            ->setDescription(static::getDescription())
            ->addOption('lead-hours', [
                'help' => 'How many hours before the event the reminder should be sent.',
                'default' => 12,
            ])
            ->addOption('window-minutes', [
                'help' => 'How wide the reminder send window should be.',
                'default' => 60,
            ])
            ->addOption('now', [
                'help' => 'Override the current time for testing, in a parseable datetime format.',
                'default' => null,
            ])
            ->addOption('dry-run', [
                'help' => 'List due reminders without sending any email.',
                'boolean' => true,
                'default' => false,
            ])
            ->addOption('fail-on-errors', [
                'help' => 'Exit non-zero when one or more reminder enqueues fail.',
                'boolean' => true,
                'default' => false,
            ]);
    }

    /**
     * @param \Cake\Console\Arguments $args Command arguments.
     * @param \Cake\Console\ConsoleIo $io Console IO.
     * @return int
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $leadHours = (int)$args->getOption('lead-hours');
        $windowMinutes = (int)$args->getOption('window-minutes');
        $dryRun = (bool)$args->getOption('dry-run');
        $failOnErrors = (bool)$args->getOption('fail-on-errors');
        $nowOption = $args->getOption('now');

        $now = is_string($nowOption) && $nowOption !== ''
            ? new DateTime($nowOption)
            : DateTime::now();

        $windowStart = $now->addHours($leadHours);
        $windowEnd = $windowStart->addMinutes($windowMinutes);

        /** @var mixed $configuredContext */
        $configuredContext = Configure::read('Queue.Context');
        /** @var mixed $configuredQueueName */
        $configuredQueueName = Configure::read('Queue.QueueName');
        $queueContext = $configuredContext instanceof Context ? $configuredContext : null;
        $queueName = is_string($configuredQueueName) && $configuredQueueName !== '' ? $configuredQueueName : null;
        if (!$dryRun && ($queueContext === null || $queueName === null)) {
            $io->err(sprintf(
                'Queue context is not configured. Context present: %s, Queue name present: %s',
                $queueContext !== null ? 'yes' : 'no',
                $queueName !== null ? 'yes' : 'no',
            ));

            return static::CODE_ERROR;
        }

        $io->out(sprintf(
            'Starting reminder run. Dry-run: %s, Fail-on-errors: %s, Queue: %s, Window: %s to %s',
            $dryRun ? 'yes' : 'no',
            $failOnErrors ? 'yes' : 'no',
            $queueName ?? 'n/a',
            $windowStart->format('Y-m-d H:i:s'),
            $windowEnd->format('Y-m-d H:i:s'),
        ));

        /** @var \App\Model\Table\EntriesTable $entriesTable */
        $entriesTable = $this->fetchTable('Entries');

        /** @var \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Entry> $query */
        $query = $entriesTable->find()
            ->contain([
                'Events',
                'Participants' => ['ParticipantTypes', 'Sections'],
            ])
            ->innerJoinWith('Events', function (SelectQuery $query) use ($windowStart, $windowEnd): SelectQuery {
                return $query->where([
                    'Events.finished' => false,
                    'Events.start_time >=' => $windowStart,
                    'Events.start_time <' => $windowEnd,
                ]);
            })
            ->where([
                'Entries.reminder_sent IS' => null,
            ]);

        $queuedCount = 0;
        $failedCount = 0;

        foreach ($query->all() as $entry) {
            $reference = $entry->event->booking_code . '-' . $entry->reference_number;

            if ($dryRun) {
                $io->out(sprintf('DRY RUN %s <%s>', $reference, (string)$entry->entry_email));
                $queuedCount++;

                continue;
            }

            try {
                if ($queueContext === null || $queueName === null) {
                    throw new RuntimeException('Queue context is not configured.');
                }

                $message = $queueContext->createMessage(json_encode([
                    'type' => 'entry_reminder',
                    'entry_id' => $entry->id,
                ], JSON_THROW_ON_ERROR));
                $producer = $queueContext->createProducer();
                $producer->send($queueContext->createQueue($queueName), $message);

                $queuedCount++;
                $io->out(sprintf('Queued reminder for %s <%s>', $reference, (string)$entry->entry_email));
            } catch (Throwable $exception) {
                $failedCount++;
                $io->err(sprintf(
                    'Failed reminder for %s <%s>: %s: %s',
                    $reference,
                    (string)$entry->entry_email,
                    $exception::class,
                    $exception->getMessage(),
                ));
            }
        }

        $summary = sprintf(
            'Reminder run complete. Queued: %d, Failed: %d, Window: %s to %s',
            $queuedCount,
            $failedCount,
            $windowStart->format('Y-m-d H:i:s'),
            $windowEnd->format('Y-m-d H:i:s'),
        );

        if ($failedCount > 0) {
            $io->err($summary);

            if ($failOnErrors) {
                return static::CODE_ERROR;
            }

            $io->warning(
                'Reminder enqueue failures occurred, but the command is exiting successfully to avoid CronJob retries.',
            );

            return static::CODE_SUCCESS;
        }

        $io->success($summary);

        return static::CODE_SUCCESS;
    }
}
