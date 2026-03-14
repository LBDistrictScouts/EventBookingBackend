<?php
declare(strict_types=1);

namespace App\Mailer\Preview;

use Cake\Mailer\Mailer;
use DebugKit\Mailer\MailPreview;
use RuntimeException;

class BookingMailPreview extends MailPreview
{
    /**
     * @return \Cake\Mailer\Mailer
     */
    public function confirmation(): Mailer
    {
        $entries = $this->getTableLocator()->get('Entries');
        $entry = $entries->find()->orderByDesc('Entries.created')->contain([
            'Events' => ['Checkpoints', 'Questions'],
            'CheckIns',
            'Participants',
        ])->first();

        if ($entry === null) {
            throw new RuntimeException('No entry available for booking mail preview.');
        }

        /** @var \App\Mailer\BookingMailer $mailer */
        $mailer = $this->getMailer('Booking');

        return $mailer->confirmation($entry);
    }
}
