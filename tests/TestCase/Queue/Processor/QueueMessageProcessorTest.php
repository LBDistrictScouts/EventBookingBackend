<?php
declare(strict_types=1);

namespace App\Test\TestCase\Queue\Processor;

use App\Queue\Processor\QueueMessageProcessor;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\TestCase;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;

class QueueMessageProcessorTest extends TestCase
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

    public function testProcessEntryReminderSendsMailAndMarksReminderSent(): void
    {
        $entries = $this->getTableLocator()->get('Entries');
        $entries->updateAll(
            ['entry_email' => 'queued-reminder@example.com', 'reminder_sent' => null],
            ['id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce'],
        );

        $processor = new QueueMessageProcessor();
        $message = $this->createConfiguredStub(Message::class, [
            'getBody' => json_encode([
                'type' => 'entry_reminder',
                'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            ], JSON_THROW_ON_ERROR),
        ]);
        $context = $this->createStub(Context::class);

        $result = $processor->process($message, $context);

        $this->assertSame(Processor::ACK, $result);
        $this->assertMailCount(1);

        $row = $entries->getConnection()
            ->execute(
                'SELECT reminder_sent FROM entries WHERE id = :id',
                ['id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce'],
            )
            ->fetch('assoc');
        $this->assertIsArray($row);
        $this->assertNotNull($row['reminder_sent'] ?? null);
    }

    public function testProcessEntryReminderAcknowledgesAlreadySentReminder(): void
    {
        $entries = $this->getTableLocator()->get('Entries');
        $entries->updateAll(
            ['reminder_sent' => '2026-03-17 10:00:00'],
            ['id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce'],
        );

        $processor = new QueueMessageProcessor();
        $message = $this->createConfiguredStub(Message::class, [
            'getBody' => json_encode([
                'type' => 'entry_reminder',
                'entry_id' => '2342ad37-13f0-4fd1-bd3f-2032273626ce',
            ], JSON_THROW_ON_ERROR),
        ]);
        $context = $this->createStub(Context::class);

        $result = $processor->process($message, $context);

        $this->assertSame(Processor::ACK, $result);
        $this->assertMailCount(0);
    }
}
