<?php
declare(strict_types=1);

namespace App\Test\TestCase\Mailer;

use App\Mailer\BookingMailer;
use App\Model\Entity\Entry;
use App\Model\Entity\Event;
use Cake\I18n\DateTime;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Mailer\BookingMailer Test Case
 */
class BookingMailerTest extends TestCase
{
    use EmailTrait;

    /**
     * Test subject
     *
     * @var \App\Mailer\BookingMailer
     */
    protected $Booking;

    /**
     * Test confirmation method
     *
     * @return void
     * @uses \App\Mailer\BookingMailer::confirmation()
     */
    public function testConfirmation(): void
    {
        $entry = new Entry([
            'entry_name' => '1st Wanstead',
            'entry_email' => 'leader@example.com',
            'entry_mobile' => '07123456789',
            'reference_number' => 42,
            'security_code' => 'ABCDE',
            'created' => new DateTime('2026-03-14 09:30:00', 'Europe/London'),
            'event' => new Event([
                'event_name' => 'Greenway Challenge',
                'booking_code' => 'GW',
                'start_time' => new DateTime('2026-06-20 10:00:00', 'Europe/London'),
            ]),
        ]);

        $mailer = new BookingMailer();
        $mailer->send('confirmation', [$entry]);

        $this->assertMailCount(1);
        $this->assertMailSentTo('leader@example.com');
        $this->assertMailSentFrom('greenway@lbdscouts.org.uk');
        $this->assertMailSubjectContains('Booking Confirmation for Greenway Challenge');
        $this->assertMailContainsText('Booking Reference: GW-42');
        $this->assertMailContainsText('Security Code: ABCDE');
        $this->assertMailContainsHtml('Registration Confirmed');
        $this->assertMailContainsHtml('1st Wanstead');
    }
}
