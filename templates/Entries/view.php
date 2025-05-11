<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entry $entry
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Entry'), ['action' => 'edit', $entry->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Entry'), ['action' => 'delete', $entry->id], ['confirm' => __('Are you sure you want to delete # {0}?', $entry->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Entries'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Entry'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['controller' => 'CheckIns', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Check In'), ['controller' => 'CheckIns', 'action' => 'add', $entry->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="entries view large-9 medium-8 columns content">
    <h3><?= h($entry->entry_name) ?></h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('Event') ?></th>
                <td><?= $entry->hasValue('event') ? $this->Html->link($entry->event->event_name, ['controller' => 'Events', 'action' => 'view', $entry->event->id]) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Entry Name') ?></th>
                <td><?= h($entry->entry_name) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Entry Email') ?></th>
                <td><?= $this->Text->autoLinkEmails($entry->entry_email) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Entry Mobile') ?></th>
                <td><?= h($entry->entry_mobile) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Security Code') ?></th>
                <td><?= h($entry->security_code) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Participant Count') ?></th>
                <td><?= $this->Number->format($entry->participant_count) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Checked In Count') ?></th>
                <td><?= $this->Number->format($entry->checked_in_count) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Reference Number') ?></th>
                <td><?= $entry->event->booking_code ?>-<?= $this->Number->format($entry->reference_number) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td><?= h($entry->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Modified') ?></th>
                <td><?= h($entry->modified) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Active') ?></th>
                <td><?= $entry->active ? __('Yes') : __('No'); ?></td>
            </tr>
        </table>
    </div>
    <div class="related">
        <h4><?= __('Related Check Ins') ?></h4>
        <?php if (!empty($entry->check_ins)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('Checkpoint') ?></th>
                    <th scope="col"><?= __('Sequence') ?></th>
                    <th scope="col"><?= __('Check In Time') ?></th>
                    <th scope="col"><?= __('Participant Count') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($entry->check_ins as $checkIns): ?>
                <tr>
                    <td><?= h($checkIns->checkpoint->checkpoint_name) ?></td>
                    <td><?= h($checkIns->checkpoint->checkpoint_sequence) ?></td>
                    <td><?= h($checkIns->check_in_time) ?></td>
                    <td><?= h($checkIns->participant_count) ?></td>
                    <td><?= h($checkIns->created) ?></td>
                    <td><?= h($checkIns->modified) ?></td>
                    <td class="actions">
                        <?= $this->Actions->buttons($checkIns, ['outline' => true]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Participants') ?></h4>
        <?php if (!empty($entry->participants)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('First Name') ?></th>
                    <th scope="col"><?= __('Last Name') ?></th>
                    <th scope="col"><?= __('Participant Type') ?></th>
                    <th scope="col"><?= __('Section') ?></th>
                    <th scope="col"><?= __('Checked In') ?></th>
                    <th scope="col"><?= __('Checked Out') ?></th>
                    <th scope="col"><?= __('Highest') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($entry->participants as $participant): ?>
                    <tr>
                        <td><?= h($participant->first_name) ?></td>
                        <td><?= h($participant->last_name) ?></td>
                        <td><?= $participant->has('participant_type') ?
                                h($participant->participant_type->participant_type) : '' ?></td>
                        <td><?= $participant->has('section')
                            && !is_null($participant->section)
                            && $participant->section->has('section_name') ?
                                $this->Html->link(
                                    title: $participant->section->section_name,
                                    url: [
                                        'controller' => 'Sections',
                                        'action' => 'view',
                                        $participant->section_id,
                                    ]
                                ) : '' ?></td>
                        <td><?= $participant->checked_in ? $this->Html->icon('check-circle') : '' ?></td>
                        <td><?= $participant->checked_out ? $this->Html->icon('check-circle') : '' ?></td>
                        <td><?= h($participant->highest_check_in_sequence) ?></td>
                        <td><?= h($participant->created) ?></td>
                        <td><?= h($participant->modified) ?></td>
                        <td class="actions">
                            <?= $this->Actions->buttons($participant, ['outline' => true]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
