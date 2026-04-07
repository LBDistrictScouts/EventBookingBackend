<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \Cake\View\View $this
 * @var \App\Model\Entity\Entry $entry
 * @var string|null $notificationType
 * @var \App\Model\Entity\Entry|null $mergedEntry
 * @var string $editUrl
 */

?>
<?php
$notificationType = $notificationType ?? 'created';
$mergedEntry = $mergedEntry ?? null;

$heading = match ($notificationType) {
    'updated' => 'Registration Updated',
    'merged' => 'Booking Updated After Merge',
    'reminder' => 'Event Reminder',
    default => 'Registration Confirmed',
};
?>

<?= $entry->event->event_name ?> - <?= $heading ?>
Event Date: <?= $this->Time->i18nFormat($entry->event->start_time, 'dd-MMM-yy') ?>


===========================================================

<?php if ($notificationType === 'updated') : ?>
Your booking details have been updated.
<?php elseif ($notificationType === 'merged') : ?>
Your booking was updated after a merge<?php if ($mergedEntry !== null) : ?> from "<?= $mergedEntry->entry_name ?>"<?php endif; ?>.
<?php elseif ($notificationType === 'reminder') : ?>
This is your reminder that the event starts in around 12 hours.
<?php else : ?>
Your booking has been received and confirmed.
<?php endif; ?>

Walking Group Name: "<?= $entry->entry_name ?>"
Contact Email: "<?= $entry->entry_email ?>"
Contact Mobile: "<?= $entry->entry_mobile ?>"

-------------------------------------------

Booking Reference: <?= $entry->event->booking_code ?>-<?= $entry->reference_number ?>

Security Code: <?= $entry->security_code ?>

<?php if ($notificationType === 'reminder') : ?>
Event Start Time: <?= $this->Time->i18nFormat($entry->event->start_time, 'HH:mm', 'Europe/London') ?> on <?= $this->Time->i18nFormat($entry->event->start_time, 'dd-MMM-yy', 'Europe/London') ?>
<?php endif; ?>

<?php if (!empty($entry->participants)) : ?>

Participants
<?php foreach ($entry->participants as $participant) : ?>
<?php $participantName = preg_replace('/\s+/', ' ', trim($participant->full_name)); ?>
- <?= $participantName ?? trim($participant->full_name) ?>
  Type: <?= $participant->has('participant_type') ? $participant->participant_type->participant_type : 'N/A' ?>
  Section: <?=
    $participant->has('section') && $participant->section !== null
        ? $participant->section->section_name
        : 'N/A'
?>
<?php endforeach; ?>
<?php endif; ?>

-------------------------------------------

You will receive an email confirming the above information.
You will need the booking reference and security code to register on the day of the walk.
Edit Booking: <?= $editUrl ?>

The Greenway Team
