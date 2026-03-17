<?php
declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\Entry;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Mailer\Transport\SmtpTransport;
use Cake\Mailer\TransportFactory;
use Exception;

class BookingMailer extends Mailer
{
    /**
     * @var array<string, string>
     */
    private const SUBJECT_PREFIXES = [
        'created' => 'Booking Confirmation',
        'updated' => 'Booking Update',
        'merged' => 'Booking Update',
        'reminder' => 'Event Reminder',
    ];

    /**
     * @param \App\Model\Entity\Entry $entry
     * @param string $notificationType
     * @param \App\Model\Entity\Entry|null $mergedEntry
     * @return \Cake\Mailer\Mailer
     */
    public function confirmation(Entry $entry, string $notificationType = 'created', ?Entry $mergedEntry = null): Mailer
    {
        if (!isset(self::SUBJECT_PREFIXES[$notificationType])) {
            $notificationType = 'created';
        }

        // Grab the configured transport (e.g., 'smtp')
        $transport = TransportFactory::get('smtp');

        // Only test connection before first send (optional)
        if ($transport instanceof SmtpTransport) {
            try {
                $transport->connect();
                Log::debug('SMTP connection successful.');
                $transport->disconnect();
            } catch (Exception $e) {
                Log::error('SMTP connection failed: ' . $e->getMessage());
            }
        }

        $subjectPrefix = self::SUBJECT_PREFIXES[$notificationType];
        $mailer = $this->setTo($entry->entry_email)
            ->setFrom(['greenway@lbdscouts.org.uk' => 'LBD Scouts - Greenway Team'])
            ->setSubject("{$subjectPrefix} for {$entry->event->event_name}")
            ->setEmailFormat('both')
            ->setViewVars(compact('entry', 'notificationType', 'mergedEntry'));

        $mailer->viewBuilder()
            ->addHelpers(['Html', 'Text', 'Time'])
            ->setTemplate('confirmation')
            ->setLayout('default');

        return $mailer;
    }
}
