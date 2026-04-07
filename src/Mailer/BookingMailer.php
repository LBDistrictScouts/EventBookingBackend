<?php
declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\Entry;
use App\Model\Entity\Section;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Mailer\Transport\SmtpTransport;
use Cake\Mailer\TransportFactory;
use Cake\Routing\Router;
use Exception;
use InvalidArgumentException;

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
        $frontendBaseUrl = Configure::read('App.frontendBaseUrl');
        $fullBaseUrl = Router::fullBaseUrl();
        $publicBaseUrl = is_string($frontendBaseUrl) && trim($frontendBaseUrl) !== ''
            ? trim($frontendBaseUrl)
            : $fullBaseUrl;

        if ($publicBaseUrl !== '') {
            $editUrl = rtrim($publicBaseUrl, '/') . '/edit/' . $entry->id;
        } else {
            $editUrl = Router::url('/booking/edit/' . $entry->id, true);
        }
        $mailer = $this->setTo($entry->entry_email)
            ->setFrom(['greenway@lbdscouts.org.uk' => 'LBD Scouts - Greenway Team'])
            ->setSubject("{$subjectPrefix} for {$entry->event->event_name}")
            ->setEmailFormat('both')
            ->setViewVars(compact('entry', 'notificationType', 'mergedEntry', 'editUrl'));

        $mailer->viewBuilder()
            ->addHelpers(['Html', 'Text', 'Time'])
            ->setTemplate('confirmation')
            ->setLayout('default');

        return $mailer;
    }

    /**
     * @param \App\Model\Entity\Entry $entry
     * @param list<\App\Model\Entity\Section> $sections
     * @return \Cake\Mailer\Mailer
     */
    public function sectionSignupNotification(Entry $entry, array $sections): Mailer
    {
        if ($sections === []) {
            throw new InvalidArgumentException('At least one section is required for section signup notifications.');
        }

        $notificationEmail = trim((string)$sections[0]->notification_email);
        if ($notificationEmail === '') {
            return $this;
        }

        $sectionNames = array_map(
            static fn(Section $section): string => (string)$section->section_name,
            $sections,
        );
        $sectionList = implode(' & ', $sectionNames);
        $sectionLoginLinks = array_map(function (Section $section): array {
            $sectionPath = '/sections/view/' . $section->id;

            return [
                'section_name' => (string)$section->section_name,
                'login_url' => rtrim(Router::fullBaseUrl(), '/') . '/auth/login?redirect=' . rawurlencode($sectionPath),
            ];
        }, $sections);

        $mailer = $this->setTo($notificationEmail)
            ->setFrom(['greenway@lbdscouts.org.uk' => 'LBD Scouts - Greenway Team'])
            ->setSubject("New Signup for {$sectionList}: {$entry->event->event_name}")
            ->setEmailFormat('both')
            ->setViewVars(compact('entry', 'sections', 'sectionList', 'sectionLoginLinks'));

        $mailer->viewBuilder()
            ->addHelpers(['Html', 'Time'])
            ->setTemplate('section_signup_notification')
            ->setLayout('default');

        return $mailer;
    }
}
