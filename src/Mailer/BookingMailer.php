<?php
declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\Entry;
use Cake\Mailer\Mailer;

class BookingMailer extends Mailer
{
    /**
     * @param \App\Model\Entity\Entry $entry
     * @return \Cake\Mailer\Mailer
     */
    public function confirmation(Entry $entry): Mailer
    {
        $mailer = $this->setTo($entry->entry_email)
            ->setSubject("Booking Confirmation for {$entry->event->event_name}")
            ->setViewVars(compact('entry'));

        $mailer->viewBuilder()
            ->setTemplate('confirmation') // By default template with same name as method name is used.
            ->setLayout('custom');

        return $mailer;
    }
}
