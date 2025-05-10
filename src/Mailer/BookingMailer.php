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
     * @param \App\Model\Entity\Entry $entry
     * @return \Cake\Mailer\Mailer
     */
    public function confirmation(Entry $entry): Mailer
    {
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

        $mailer = $this->setTo($entry->entry_email)
            ->setFrom(['greenway@lbdscouts.org.uk' => 'LBD Scouts - Greenway Team'])
            ->setSubject("Booking Confirmation for {$entry->event->event_name}")
            ->setEmailFormat('both')
            ->setViewVars(compact('entry'));

        $mailer->viewBuilder()
            ->addHelpers(['Html', 'Text', 'Time'])
            ->setTemplate('confirmation')
            ->setLayout('default');

        return $mailer;
    }
}
