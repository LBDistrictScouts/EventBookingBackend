<?php
declare(strict_types=1);

namespace App\Test\TestCase\Mailer;

use App\Mailer\BookingMailer;
use Cake\TestSuite\TestCase;

/**
 * App\Mailer\BookingMailer Test Case
 */
class BookingMailerTest extends TestCase
{
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
        $this->markTestIncomplete('Not implemented yet.');
    }
}
