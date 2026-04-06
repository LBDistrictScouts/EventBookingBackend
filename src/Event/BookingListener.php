<?php
declare(strict_types=1);

namespace App\Event;

use App\Mailer\BookingMailer;
use App\Model\Entity\Entry;
use App\Model\Entity\Participant;
use App\Model\Entity\Section;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;

class BookingListener implements EventListenerInterface
{
    public const EVENT_CREATED = 'Booking.created';

    /**
     * @return array<string, string>
     */
    public function implementedEvents(): array
    {
        return [
            self::EVENT_CREATED => 'onBookingCreated',
        ];
    }

    /**
     * @param \Cake\Event\EventInterface<object> $event
     * @return void
     */
    public function onBookingCreated(EventInterface $event): void
    {
        $entry = $event->getData('entry');
        if (!$entry instanceof Entry) {
            return;
        }

        foreach ($this->collectSectionsToNotify($entry) as $sections) {
            (new BookingMailer())->send('sectionSignupNotification', [$entry, $sections]);
        }
    }

    /**
     * @param \App\Model\Entity\Entry $entry
     * @return list<list<\App\Model\Entity\Section>>
     * @phpstan-return list<list<\App\Model\Entity\Section>>
     */
    private function collectSectionsToNotify(Entry $entry): array
    {
        /** @var array<string, array<string, \App\Model\Entity\Section>> $sectionsByEmail */
        $sectionsByEmail = [];

        foreach ((array)$entry->participants as $participant) {
            if (!$participant instanceof Participant) {
                continue;
            }

            $section = $participant->section ?? null;
            if (!$section instanceof Section) {
                continue;
            }

            $notificationEmail = strtolower(trim((string)$section->notification_email));
            if ($notificationEmail === '') {
                continue;
            }

            $sectionId = (string)$section->id;
            if ($sectionId === '') {
                continue;
            }

            $sectionsByEmail[$notificationEmail] ??= [];
            if (isset($sectionsByEmail[$notificationEmail][$sectionId])) {
                continue;
            }

            $sectionsByEmail[$notificationEmail][$sectionId] = $section;
        }

        /** @var list<list<\App\Model\Entity\Section>> $orderedSections */
        $orderedSections = [];
        foreach ($sectionsByEmail as $emailSections) {
            if ($emailSections === []) {
                continue;
            }

            $sortedSections = array_values($emailSections);
            usort($sortedSections, [$this, 'compareSections']);
            $orderedSections[] = $sortedSections;
        }

        return $orderedSections;
    }

    /**
     * @param \App\Model\Entity\Section $left
     * @param \App\Model\Entity\Section $right
     * @return int
     */
    private function compareSections(Section $left, Section $right): int
    {
        $leftGroupSort = (int)($left->group->sort_order ?? PHP_INT_MAX);
        $rightGroupSort = (int)($right->group->sort_order ?? PHP_INT_MAX);
        if ($leftGroupSort !== $rightGroupSort) {
            return $leftGroupSort <=> $rightGroupSort;
        }

        $leftParticipantTypeSort = (int)($left->participant_type->sort_order ?? PHP_INT_MAX);
        $rightParticipantTypeSort = (int)($right->participant_type->sort_order ?? PHP_INT_MAX);
        if ($leftParticipantTypeSort !== $rightParticipantTypeSort) {
            return $leftParticipantTypeSort <=> $rightParticipantTypeSort;
        }

        return strcasecmp((string)$left->section_name, (string)$right->section_name);
    }
}
