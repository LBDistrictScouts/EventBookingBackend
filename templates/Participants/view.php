<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Participant $participant
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Participant'), ['action' => 'edit', $participant->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Participant'), ['action' => 'delete', $participant->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participant->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Participant'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Entries'), ['controller' => 'Entries', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Entry'), ['controller' => 'Entries', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participant Types'), ['controller' => 'ParticipantTypes', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant Type'), ['controller' => 'ParticipantTypes', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['controller' => 'CheckIns', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Check In'), ['controller' => 'CheckIns', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="participants view large-9 medium-8 columns content">
    <h3><?= h($participant->first_name) ?></h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('First Name') ?></th>
                <td><?= h($participant->first_name) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Last Name') ?></th>
                <td><?= h($participant->last_name) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Entry') ?></th>
                <td><?= $participant->hasValue('entry') ?
                        $this->Html->link(
                            title: $participant->entry->entry_name,
                            url: ['controller' => 'Entries', 'action' => 'view', $participant->entry->id],
                        ) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Participant Type') ?></th>
                <td><?= $participant->hasValue('participant_type') ?
                        $this->Html->link(
                            title: $participant->participant_type->participant_type,
                            url: [
                                'controller' => 'ParticipantTypes',
                                'action' => 'view',
                                $participant->participant_type->id,
                            ],
                        ) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Section') ?></th>
                <td><?= $participant->hasValue('section') ?
                        $this->Html->link(
                            title: $participant->section->section_name,
                            url: ['controller' => 'Sections', 'action' => 'view', $participant->section->id],
                        ) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Highest Check In Sequence') ?></th>
                <td><?= $this->Number->format($participant->highest_check_in_sequence) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td><?= h($participant->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Modified') ?></th>
                <td><?= h($participant->modified) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Checked In') ?></th>
                <td><?= $participant->checked_in ? $this->Html->icon('check-circle') : ''; ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Checked Out') ?></th>
                <td><?= $participant->checked_out ? $this->Html->icon('check-circle') : ''; ?></td>
            </tr>
        </table>
    </div>
    <div class="related">
        <h4><?= __('Related Check Ins') ?></h4>
        <?php if (!empty($participant->check_ins)) : ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('#') ?></th>
                    <th scope="col"><?= __('Checkpoint') ?></th>
                    <th scope="col"><?= __('Check In Time') ?></th>
                    <th scope="col"><?= __('Participant Count') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($participant->check_ins as $checkIns) : ?>
                <tr>
                    <td><?= $checkIns->hasValue('checkpoint') ?
                            h($checkIns->checkpoint->checkpoint_sequence) : '' ?></td>
                    <td><?= $checkIns->hasValue('checkpoint') ?
                            $this->Html->link(
                                title: $checkIns->checkpoint->checkpoint_name,
                                url: ['controller' => 'CheckPoints', 'action' => 'view', $checkIns->checkpoint->id],
                            )
                            : ''?></td>
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
</div>
