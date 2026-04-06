<?php
/**
 * @var \Cake\View\View $this
 * @var \App\Model\Entity\Entry $entry
 * @var list<\App\Model\Entity\Section> $sections
 * @var string $sectionList
 * @var list<array{section_name: string, login_url: string}> $sectionLoginLinks
 */

$participants = (array)$entry->participants;
$participantCount = count($participants);
$sectionCounts = [];
foreach ($sections as $section) {
    $sectionCounts[] = sprintf(
        '%d from %s',
        count(array_filter(
            $participants,
            static fn($participant): bool => $participant->section_id === $section->id,
        )),
        $section->section_name,
    );
}
?>
New Signup Received

A new booking has been received for <?= $sectionList ?>.
Team <?= $entry->entry_name ?> signed up for <?= $entry->event->event_name ?>.

Contact Email: <?= $entry->entry_email ?>
Contact Mobile: <?= $entry->entry_mobile ?>
Participants: <?= $participantCount ?> total, <?= implode(', ', $sectionCounts) ?>.

Log In To View Section Details
<?php foreach ($sectionLoginLinks as $sectionLoginLink) : ?>
- Log in to view <?= $sectionLoginLink['section_name'] ?>: <?= $sectionLoginLink['login_url'] ?>
<?php endforeach; ?>

Participants
<?php foreach ($participants as $participant) : ?>
<?php $participantName = preg_replace('/\s+/', ' ', trim($participant->full_name)); ?>
- <?= $participantName ?? trim($participant->full_name) ?>
  Section: <?= $participant->section?->section_name ?? 'No section' ?>
  Type: <?= $participant->participant_type?->participant_type ?? '' ?>
<?php endforeach; ?>
