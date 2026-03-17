<?php

/** Email Template
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entry $entry
 * @var string|null $notificationType
 * @var \App\Model\Entity\Entry|null $mergedEntry
 */

?>
<?php
$notificationType = $notificationType ?? 'created';
$mergedEntry = $mergedEntry ?? null;

$heading = match ($notificationType) {
    'updated' => __('Registration Updated'),
    'merged' => __('Booking Updated After Merge'),
    'reminder' => __('Event Reminder'),
    default => __('Registration Confirmed'),
};

$summary = match ($notificationType) {
    'updated' => __('Your booking details have been updated.'),
    'merged' => $mergedEntry !== null
        ? __('Booking details were merged from "{0}" into this booking.', $mergedEntry->entry_name)
        : __('Booking details were merged into this booking.'),
    'reminder' => __('This is your reminder that the event starts in around 12 hours.'),
    default => __('Your booking has been received and confirmed.'),
};
?>

<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 5px; padding: 20px;">
    <tr>
        <td align="center" style="padding-bottom: 20px;">
            <h1 style="margin: 0; color: #333;"><?= h($entry->event->event_name) ?> Booking</h1>
            <h2 style="padding-top: 0px; color: #6c757d;"><?=
                $this->Time->i18nFormat($entry->event->start_time, 'dd-MMM-yy')
            ?></h2>
        </td>
    </tr>
</table>

<br />

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background: #f4f4f4; padding: 20px;">
    <tr>
        <td align="center">
            <table
                role="presentation"
                width="600"
                cellpadding="0"
                cellspacing="0"
                style="background: #d4edda; border-radius: 6px; padding: 20px; border: 1px solid #c3e6cb;"
            >
                <tr>
                    <td style="font-family: 'Nunito Sans', Arial, sans-serif;">
                        <h2 style="margin: 0 0 10px 0; color: #155724;"><?= h($heading) ?></h2>
                        <p style="margin: 0 0 16px 0; color: #155724;"><?= h($summary) ?></p>
                        <p style="margin: 10px 0;">Walking Group Name: <strong>"<?=
                                $entry->entry_name
                        ?>"</strong></p>
                        <p style="margin: 10px 0;">Contact Email: <strong>"<?=
                                $entry->entry_email
                        ?>"</strong></p>
                        <p style="margin: 10px 0 20px 0;">Contact Mobile: <strong>"<?=
                                $entry->entry_mobile
                        ?>"</strong></p>

                        <table
                            role="presentation"
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            style="background: #ffffff; border-radius: 6px; padding: 20px;
                                border: 1px solid #e0e0e0; margin-bottom: 20px;"
                        >
                            <tr>
                                <td style="width: 50%; text-align: center; padding: 10px;">
                                    <p style="font-size: 24px; margin: 0; font-weight: bold;">
                                        <?= $entry->event->booking_code ?>-<?= $entry->reference_number ?></p>
                                    <p style="margin: 5px 0 0 0; color: #6c757d;">Booking Reference</p>
                                </td>
                                <td style="width: 50%; text-align: center; padding: 10px;">
                                    <p style="font-size: 24px; margin: 0; font-weight: bold;">
                                        <?= $entry->security_code ?>
                                    </p>
                                    <p style="margin: 5px 0 0 0; color: #6c757d;">Security Code</p>
                                </td>
                            </tr>
                        </table>

                        <p style="color: #333; font-size: 14px;">You will need the booking reference and security code to register on the day of the walk.</p>

                        <?php if (!empty($entry->participants)) : ?>
                            <table
                                role="presentation"
                                width="100%"
                                cellpadding="0"
                                cellspacing="0"
                                style="background: #ffffff; border-radius: 6px; padding: 20px;
                                    border: 1px solid #e0e0e0; margin-top: 20px;"
                            >
                                <tr>
                                    <td style="font-family: 'Nunito Sans', Arial, sans-serif;">
                                        <h3 style="margin: 0 0 12px 0; color: #333;"><?= __('Participants') ?></h3>
                                        <table
                                            role="presentation"
                                            width="100%"
                                            cellpadding="0"
                                            cellspacing="0"
                                            style="border-collapse: collapse; font-size: 14px;"
                                        >
                                            <thead>
                                                <tr>
                                                    <th align="left" style="padding: 10px 8px; border-bottom: 2px solid #dee2e6; color: #495057;">
                                                        <?= __('Name') ?>
                                                    </th>
                                                    <th align="left" style="padding: 10px 8px; border-bottom: 2px solid #dee2e6; color: #495057;">
                                                        <?= __('Type') ?>
                                                    </th>
                                                    <th align="left" style="padding: 10px 8px; border-bottom: 2px solid #dee2e6; color: #495057;">
                                                        <?= __('Section') ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($entry->participants as $participant) : ?>
                                                    <?php $participantName = preg_replace('/\s+/', ' ', trim($participant->full_name)); ?>
                                                    <tr>
                                                        <td style="padding: 10px 8px; border-bottom: 1px solid #edf0f2; color: #212529;">
                                                            <?= h($participantName ?? trim($participant->full_name)) ?>
                                                        </td>
                                                        <td style="padding: 10px 8px; border-bottom: 1px solid #edf0f2; color: #495057;">
                                                            <?= $participant->has('participant_type') ? h($participant->participant_type->participant_type) : '' ?>
                                                        </td>
                                                        <td style="padding: 10px 8px; border-bottom: 1px solid #edf0f2; color: #495057;">
                                                            <?= $participant->has('section') && $participant->section !== null ? h($participant->section->section_name) : '' ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<br />

<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 5px; padding: 20px;">
    <tr>
        <td style="color: #555;">
            <p>
                <?php if ($notificationType === 'updated' || $notificationType === 'merged') : ?>
                    <?= __('You are receiving this email because a reservation in your name was updated.') ?>
                <?php elseif ($notificationType === 'reminder') : ?>
                    <?= __('You are receiving this email because your event is coming up soon.') ?>
                <?php else : ?>
                    <?= __('You are receiving this email because a reservation was added in your name.') ?>
                <?php endif; ?>
            </p>
            <p>
                <?php if ($notificationType === 'updated' || $notificationType === 'merged') : ?>
                    <?= __('Your booking was last updated at {0} on {1}.',
                        $this->Time->i18nFormat($entry->modified, 'HH:mm', 'Europe/London'),
                        $this->Time->i18nFormat($entry->modified, 'dd-MMM-yy', 'Europe/London'),
                    ) ?>
                <?php elseif ($notificationType === 'reminder') : ?>
                    <?= __('The event starts at {0} on {1}.',
                        $this->Time->i18nFormat($entry->event->start_time, 'HH:mm', 'Europe/London'),
                        $this->Time->i18nFormat($entry->event->start_time, 'dd-MMM-yy', 'Europe/London'),
                    ) ?>
                <?php else : ?>
                    <?= __('Your booking was created at {0} on {1}.',
                        $this->Time->i18nFormat($entry->created, 'HH:mm', 'Europe/London'),
                        $this->Time->i18nFormat($entry->created, 'dd-MMM-yy', 'Europe/London'),
                    ) ?>
                <?php endif; ?>
            </p>
            <p>If this wasn't you, please email <a href="mailto:greenway@lbdscouts.org.uk" style="color: #28a745;">greenway@lbdscouts.org.uk</a>.</p>
        </td>
    </tr>
</table>
