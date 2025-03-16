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
