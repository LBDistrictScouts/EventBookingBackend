<?php
declare(strict_types=1);

namespace App\Test\TestCase\Mailer;

use App\Mailer\BookingMailer;
use App\Model\Entity\Entry;
use App\Model\Entity\Event;
use App\Model\Entity\Group;
use App\Model\Entity\Participant;
use App\Model\Entity\ParticipantType;
use App\Model\Entity\Section;
use Cake\Core\Configure;
use Cake\I18n\DateTime;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Mailer\BookingMailer Test Case
 */
class BookingMailerTest extends TestCase
{
    use EmailTrait;

    protected function setUp(): void
    {
        parent::setUp();
        Configure::write('App.frontendBaseUrl', false);
    }

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
            'id' => 'c3cbb44d-7ef8-4c45-b53b-fc33df4847bf',
            'entry_name' => '1st Wanstead',
            'entry_email' => 'leader@example.com',
            'entry_mobile' => '07123456789',
            'reference_number' => 42,
            'security_code' => 'ABCDE',
            'created' => new DateTime('2026-03-14 09:30:00', 'Europe/London'),
            'modified' => new DateTime('2026-03-14 09:30:00', 'Europe/London'),
            'event' => new Event([
                'event_name' => 'Greenway Challenge',
                'booking_code' => 'GW',
                'start_time' => new DateTime('2026-06-20 10:00:00', 'Europe/London'),
            ]),
            'participants' => [
                new Participant([
                    'first_name' => 'Alex',
                    'last_name' => 'Walker',
                ]),
            ],
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
        $this->assertMailContainsHtml('Participants');
        $this->assertMailContainsText('Alex Walker');
    }

    public function testConfirmationUsesFrontendEditUrlWhenConfigured(): void
    {
        Configure::write('App.frontendBaseUrl', 'http://localhost:5173');

        $entry = new Entry([
            'id' => 'c3cbb44d-7ef8-4c45-b53b-fc33df4847bf',
            'entry_name' => '1st Wanstead',
            'entry_email' => 'leader@example.com',
            'entry_mobile' => '07123456789',
            'reference_number' => 42,
            'security_code' => 'ABCDE',
            'created' => new DateTime('2026-03-14 09:30:00', 'Europe/London'),
            'modified' => new DateTime('2026-03-14 09:30:00', 'Europe/London'),
            'event' => new Event([
                'event_name' => 'Greenway Challenge',
                'booking_code' => 'GW',
                'start_time' => new DateTime('2026-06-20 10:00:00', 'Europe/London'),
            ]),
        ]);

        $mailer = new BookingMailer();
        $mailer->send('confirmation', [$entry]);

        $this->assertMailContainsText('http://localhost:5173/edit/c3cbb44d-7ef8-4c45-b53b-fc33df4847bf');
        $this->assertMailContainsHtml('http://localhost:5173/edit/c3cbb44d-7ef8-4c45-b53b-fc33df4847bf');
    }

    public function testConfirmationFallsBackToFullBaseUrlWhenFrontendBaseUrlIsUnset(): void
    {
        Configure::write('App.frontendBaseUrl', false);

        $entry = new Entry([
            'id' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
            'entry_name' => '1st Wanstead',
            'entry_email' => 'leader@example.com',
            'entry_mobile' => '07123456789',
            'reference_number' => 42,
            'security_code' => 'ABCDE',
            'created' => new DateTime('2026-03-14 09:30:00', 'Europe/London'),
            'modified' => new DateTime('2026-03-14 09:30:00', 'Europe/London'),
            'event' => new Event([
                'event_name' => 'Greenway Challenge',
                'booking_code' => 'GW',
                'start_time' => new DateTime('2026-06-20 10:00:00', 'Europe/London'),
            ]),
        ]);

        $mailer = new BookingMailer();
        $mailer->send('confirmation', [$entry]);

        $this->assertMailContainsText('http://localhost:8765/edit/aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');
        $this->assertMailContainsHtml('http://localhost:8765/edit/aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');
    }

    public function testConfirmationForUpdatedEntryUsesUpdateCopy(): void
    {
        $entry = new Entry([
            'id' => '3f05f0f9-cfcf-4e5a-ae23-c6bd4eb8418b',
            'entry_name' => '1st Wanstead',
            'entry_email' => 'leader@example.com',
            'entry_mobile' => '07123456789',
            'reference_number' => 42,
            'security_code' => 'ABCDE',
            'created' => new DateTime('2026-03-14 09:30:00', 'Europe/London'),
            'modified' => new DateTime('2026-03-15 14:45:00', 'Europe/London'),
            'event' => new Event([
                'event_name' => 'Greenway Challenge',
                'booking_code' => 'GW',
                'start_time' => new DateTime('2026-06-20 10:00:00', 'Europe/London'),
            ]),
        ]);

        $mailer = new BookingMailer();
        $mailer->send('confirmation', [$entry, 'updated']);

        $this->assertMailSubjectContains('Booking Update for Greenway Challenge');
        $this->assertMailContainsText('Registration Updated');
        $this->assertMailContainsHtml('Registration Updated');
    }

    public function testConfirmationForReminderUsesReminderCopy(): void
    {
        $entry = new Entry([
            'id' => '76398db1-470d-4210-9ef0-7bd33f4d26b3',
            'entry_name' => '1st Wanstead',
            'entry_email' => 'leader@example.com',
            'entry_mobile' => '07123456789',
            'reference_number' => 42,
            'security_code' => 'ABCDE',
            'created' => new DateTime('2026-03-14 09:30:00', 'Europe/London'),
            'modified' => new DateTime('2026-03-15 14:45:00', 'Europe/London'),
            'event' => new Event([
                'event_name' => 'Greenway Challenge',
                'booking_code' => 'GW',
                'start_time' => new DateTime('2026-06-20 10:00:00', 'Europe/London'),
            ]),
        ]);

        $mailer = new BookingMailer();
        $mailer->send('confirmation', [$entry, 'reminder']);

        $this->assertMailSubjectContains('Event Reminder for Greenway Challenge');
        $this->assertMailContainsText('This is your reminder that the event starts in around 12 hours.');
        $this->assertMailContainsHtml('Event Reminder');
        $this->assertMailContainsText('Event Start Time: 10:00 on 20-Jun-26');
    }

    public function testSectionSignupNotificationIncludesFullRoster(): void
    {
        $recipientSection = new Section([
            'id' => '95116a77-0675-4e1a-9d0c-74e3d40d92c1',
            'section_name' => 'Cubs',
            'notification_email' => 'cubs@example.com',
            'group' => new Group(['sort_order' => 1]),
            'participant_type' => new ParticipantType(['sort_order' => 2]),
        ]);

        $otherSection = new Section([
            'id' => '62afe882-774c-4b39-a00a-b4377099d7d1',
            'section_name' => 'Scouts',
            'notification_email' => 'scouts@example.com',
            'group' => new Group(['sort_order' => 1]),
            'participant_type' => new ParticipantType(['sort_order' => 3]),
        ]);

        $entry = new Entry([
            'id' => 'cbd5f848-65a4-4549-9ca3-0836ec0402be',
            'entry_name' => '1st Wanstead',
            'entry_email' => 'leader@example.com',
            'entry_mobile' => '07123456789',
            'event' => new Event([
                'event_name' => 'Greenway Challenge',
            ]),
            'participants' => [
                new Participant([
                    'first_name' => 'Alex',
                    'last_name' => 'Walker',
                    'section_id' => $recipientSection->id,
                    'section' => $recipientSection,
                    'participant_type' => new ParticipantType(['participant_type' => 'Young Person']),
                ]),
                new Participant([
                    'first_name' => 'Sam',
                    'last_name' => 'Rivers',
                    'section_id' => $otherSection->id,
                    'section' => $otherSection,
                    'participant_type' => new ParticipantType(['participant_type' => 'Young Person']),
                ]),
                new Participant([
                    'first_name' => 'Pat',
                    'last_name' => 'Jones',
                    'section_id' => null,
                    'section' => null,
                    'participant_type' => new ParticipantType(['participant_type' => 'Leader']),
                ]),
            ],
        ]);

        $mailer = new BookingMailer();
        $mailer->send('sectionSignupNotification', [$entry, [$recipientSection]]);

        $this->assertMailCount(1);
        $this->assertMailSentTo('cubs@example.com');
        $this->assertMailSubjectContains('New Signup for Cubs');
        $this->assertMailContainsText('Team 1st Wanstead signed up for Greenway Challenge.');
        $this->assertMailContainsText('Alex Walker');
        $this->assertMailContainsText('Sam Rivers');
        $this->assertMailContainsText('Pat Jones');
        $this->assertMailContainsText('Section: Scouts');
        $this->assertMailContainsText('Section: No section');
        $this->assertMailContainsText('Log in to view Cubs: http://localhost:8765/auth/login?redirect=%2Fsections%2Fview%2F95116a77-0675-4e1a-9d0c-74e3d40d92c1');
    }

    public function testSectionSignupNotificationAggregatesMultipleSections(): void
    {
        $beavers = new Section([
            'id' => '11111111-1111-1111-1111-111111111111',
            'section_name' => '4th Letchworth Beavers',
            'notification_email' => 'shared@example.com',
            'group' => new Group(['sort_order' => 1]),
            'participant_type' => new ParticipantType(['sort_order' => 1]),
        ]);

        $cubs = new Section([
            'id' => '22222222-2222-2222-2222-222222222222',
            'section_name' => '4th Letchworth Cubs',
            'notification_email' => 'shared@example.com',
            'group' => new Group(['sort_order' => 1]),
            'participant_type' => new ParticipantType(['sort_order' => 2]),
        ]);

        $entry = new Entry([
            'id' => '33333333-3333-3333-3333-333333333333',
            'entry_name' => '4th Letchworth Team',
            'entry_email' => 'leader@example.com',
            'entry_mobile' => '07123456789',
            'event' => new Event([
                'event_name' => 'Greenway Challenge',
            ]),
            'participants' => [
                new Participant([
                    'first_name' => 'Bea',
                    'last_name' => 'River',
                    'section_id' => $beavers->id,
                    'section' => $beavers,
                    'participant_type' => new ParticipantType(['participant_type' => 'Young Person']),
                ]),
                new Participant([
                    'first_name' => 'Cub',
                    'last_name' => 'Hill',
                    'section_id' => $cubs->id,
                    'section' => $cubs,
                    'participant_type' => new ParticipantType(['participant_type' => 'Young Person']),
                ]),
                new Participant([
                    'first_name' => 'Pat',
                    'last_name' => 'Jones',
                    'section_id' => null,
                    'section' => null,
                    'participant_type' => new ParticipantType(['participant_type' => 'Leader']),
                ]),
            ],
        ]);

        $mailer = new BookingMailer();
        $mailer->send('sectionSignupNotification', [$entry, [$beavers, $cubs]]);

        $this->assertMailCount(1);
        $this->assertMailSentTo('shared@example.com');
        $this->assertMailSubjectContains('New Signup for 4th Letchworth Beavers & 4th Letchworth Cubs');
        $this->assertMailContainsText('A new booking has been received for 4th Letchworth Beavers & 4th Letchworth Cubs.');
        $this->assertMailContainsText('Participants: 3 total, 1 from 4th Letchworth Beavers, 1 from 4th Letchworth Cubs.');
        $this->assertMailContainsText('Log in to view 4th Letchworth Beavers: http://localhost:8765/auth/login?redirect=%2Fsections%2Fview%2F11111111-1111-1111-1111-111111111111');
        $this->assertMailContainsText('Log in to view 4th Letchworth Cubs: http://localhost:8765/auth/login?redirect=%2Fsections%2Fview%2F22222222-2222-2222-2222-222222222222');
    }
}
