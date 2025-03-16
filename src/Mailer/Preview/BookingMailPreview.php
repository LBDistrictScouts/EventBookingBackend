<?php
declare(strict_types=1);

namespace App\Mailer\Preview;

use Cake\Mailer\Mailer;
use DebugKit\Mailer\MailPreview;

class BookingMailPreview extends MailPreview
{
    /**
     * @return \Cake\Mailer\Mailer
     */
    public function confirmation(): Mailer
    {
        $entries = $this->getTableLocator()->get('Entries');
        $entry = $entries->find()->first();

        return $this->getMailer('Booking')
            ->confirmation($entry)
            ->setViewVars(compact('entry'));
    }
}
