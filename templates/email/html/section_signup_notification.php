<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entry $entry
 * @var list<\App\Model\Entity\Section> $sections
 * @var string $sectionList
 * @var list<array{section_name: string, login_url: string}> $sectionLoginLinks
 */

$participantCount = count((array)$entry->participants);
$sectionCounts = [];
foreach ($sections as $section) {
    $sectionCounts[] = __(
        '{0} from {1}',
        count(array_filter(
            (array)$entry->participants,
            static fn($participant): bool => $participant->section_id === $section->id,
        )),
        $section->section_name,
    );
}
?>

<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 5px; padding: 20px;">
    <tr>
        <td>
            <h1 style="margin: 0 0 12px 0; color: #333;"><?= __('New Signup Received') ?></h1>
            <p style="margin: 0 0 12px 0; color: #555;">
                <?= __('A new booking has been received for {0}.', $sectionList) ?>
            </p>
            <p style="margin: 0; color: #555;">
                <?= __('Team {0} signed up for {1}.', $entry->entry_name, $entry->event->event_name) ?>
            </p>
        </td>
    </tr>
</table>

<br />

<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background: #f8f9fa; border-radius: 5px; padding: 20px;">
    <tr>
        <td style="color: #333;">
            <p style="margin: 0 0 10px 0;"><strong><?= __('Team') ?>:</strong> <?= h($entry->entry_name) ?></p>
            <p style="margin: 0 0 10px 0;"><strong><?= __('Event') ?>:</strong> <?= h($entry->event->event_name) ?></p>
            <p style="margin: 0 0 10px 0;"><strong><?= __('Contact Email') ?>:</strong> <?= h($entry->entry_email) ?></p>
            <p style="margin: 0 0 10px 0;"><strong><?= __('Contact Mobile') ?>:</strong> <?= h($entry->entry_mobile) ?></p>
            <p style="margin: 0;">
                <strong><?= __('Participants') ?>:</strong>
                <?= __('{0} total, {1}.', $participantCount, implode(', ', $sectionCounts)) ?>
            </p>
        </td>
    </tr>
</table>

<br />

<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background: #eef4ff; border-radius: 5px; padding: 20px;">
    <tr>
        <td style="color: #1f3b73;">
            <p style="margin: 0 0 12px 0; font-weight: bold;"><?= __('Log In To View Section Details') ?></p>
            <?php foreach ($sectionLoginLinks as $sectionLoginLink) : ?>
                <p style="margin: 0 0 10px 0;">
                    <a href="<?= h($sectionLoginLink['login_url']) ?>" style="color: #1f5fbf; text-decoration: underline;">
                        <?= __('Log in to view {0}', $sectionLoginLink['section_name']) ?>
                    </a>
                </p>
            <?php endforeach; ?>
        </td>
    </tr>
</table>

<br />

<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 5px; padding: 20px; border: 1px solid #e0e0e0;">
    <tr>
        <td>
            <h2 style="margin: 0 0 12px 0; color: #333;"><?= __('Signed Up Participants') ?></h2>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr>
                        <th align="left" style="padding: 10px 8px; border-bottom: 2px solid #dee2e6; color: #495057;"><?= __('Name') ?></th>
                        <th align="left" style="padding: 10px 8px; border-bottom: 2px solid #dee2e6; color: #495057;"><?= __('Section') ?></th>
                        <th align="left" style="padding: 10px 8px; border-bottom: 2px solid #dee2e6; color: #495057;"><?= __('Type') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ((array)$entry->participants as $participant) : ?>
                        <?php $participantName = preg_replace('/\s+/', ' ', trim($participant->full_name)); ?>
                        <tr>
                            <td style="padding: 10px 8px; border-bottom: 1px solid #edf0f2; color: #212529;"><?= h($participantName ?? trim($participant->full_name)) ?></td>
                            <td style="padding: 10px 8px; border-bottom: 1px solid #edf0f2; color: #495057;"><?= h($participant->section?->section_name ?? __('No section')) ?></td>
                            <td style="padding: 10px 8px; border-bottom: 1px solid #edf0f2; color: #495057;"><?= h($participant->participant_type?->participant_type ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </td>
    </tr>
</table>
