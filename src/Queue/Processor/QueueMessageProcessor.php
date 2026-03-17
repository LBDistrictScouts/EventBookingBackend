<?php
declare(strict_types=1);

namespace App\Queue\Processor;

use App\Mailer\BookingMailer;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\I18n\DateTime;
use Cake\Log\LogTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;
use JsonException;
use Throwable;

class QueueMessageProcessor implements Processor
{
    use LocatorAwareTrait;
    use LogTrait;

    /**
     * @param \Interop\Queue\Message $message Queue message.
     * @param \Interop\Queue\Context $context Queue context.
     * @return string
     */
    public function process(Message $message, Context $context): string
    {
        try {
            $data = json_decode($message->getBody(), associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->log('Queue message body is not valid JSON.', LOG_WARNING);

            return self::REJECT;
        }

        if (!is_array($data)) {
            return self::REJECT;
        }

        if (($data['type'] ?? null) === 'entry_reminder') {
            return $this->processEntryReminder($data);
        }

        return $this->processCheckIn($message, $context, $data);
    }

    /**
     * @param array<string, mixed> $data
     * @return string
     */
    private function processEntryReminder(array $data): string
    {
        $entryId = $data['entry_id'] ?? null;
        if (!is_string($entryId) || $entryId === '') {
            return self::REJECT;
        }

        /** @var \App\Model\Table\EntriesTable $entriesTable */
        $entriesTable = $this->getTableLocator()->get('Entries');

        try {
            $entry = $entriesTable->getApiEntryById($entryId, false);
        } catch (RecordNotFoundException) {
            return self::REJECT;
        }

        if ($entry->reminder_sent !== null || $entry->event->finished) {
            return self::ACK;
        }

        try {
            $mailer = new BookingMailer();
            $mailer->send('confirmation', [$entry, 'reminder']);
            $updated = $entriesTable->updateAll(
                ['reminder_sent' => DateTime::now()],
                ['id' => $entry->id, 'reminder_sent IS' => null],
            );

            return $updated === 1 ? self::ACK : self::REJECT;
        } catch (Throwable $exception) {
            $this->log(
                sprintf('Failed to send queued reminder for entry %s: %s', $entryId, $exception->getMessage()),
                LOG_ERR,
            );

            return self::REJECT;
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return string
     */
    private function processCheckIn(Message $message, Context $context, array $data): string
    {
        $validator = Configure::read('Queue.Validator');
        /** @var \Opis\JsonSchema\ValidationResult $result */
        $result = $validator->validate($data, 'https://greenway.lbdscouts.org.uk/check-in-schema.json');

        if (!$result->isValid()) {
            $this->log(
                message: 'Message Received Not Valid',
                level: LOG_WARNING,
                context: ['validationResult' => (string)json_encode($result->error(), JSON_THROW_ON_ERROR)],
            );

            return self::REJECT;
        }

        $checkInProcessor = new CheckInProcessor();

        return $checkInProcessor->process($message, $context);
    }
}
