<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CheckIn $checkIn
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Check In'), ['action' => 'edit', $checkIn->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Check In'), ['action' => 'delete', $checkIn->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checkIn->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Check In'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Checkpoints'), ['controller' => 'Checkpoints', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Checkpoint'), ['controller' => 'Checkpoints', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Entries'), ['controller' => 'Entries', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Entry'), ['controller' => 'Entries', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="checkIns view large-9 medium-8 columns content">
    <h3><?= h($checkIn->id) ?></h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('Id') ?></th>
                <td><?= h($checkIn->id) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Checkpoint') ?></th>
                <td><?= $checkIn->hasValue('checkpoint') ? $this->Html->link($checkIn->checkpoint->checkpoint_name, ['controller' => 'Checkpoints', 'action' => 'view', $checkIn->checkpoint->id]) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Entry') ?></th>
                <td><?= $checkIn->hasValue('entry') ? $this->Html->link($checkIn->entry->entry_name, ['controller' => 'Entries', 'action' => 'view', $checkIn->entry->id]) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Participant Count') ?></th>
                <td><?= $this->Number->format($checkIn->participant_count) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Check In Time') ?></th>
                <td><?= h($checkIn->check_in_time) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td><?= h($checkIn->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Modified') ?></th>
                <td><?= h($checkIn->modified) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Deleted') ?></th>
                <td><?= h($checkIn->deleted) ?></td>
            </tr>
        </table>
    </div>
    <div class="related">
        <h4><?= __('Related Participants') ?></h4>
        <?php if (!empty($checkIn->participants)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('First Name') ?></th>
                    <th scope="col"><?= __('Last Name') ?></th>
                    <th scope="col"><?= __('Participant Type') ?></th>
                    <th scope="col"><?= __('Section') ?></th>
                    <th scope="col"><?= __('Checked In') ?></th>
                    <th scope="col"><?= __('Checked Out') ?></th>
                    <th scope="col"><?= __('Checkpoint') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($checkIn->participants as $participants): ?>
                <tr>
                    <td><?= h($participants->first_name) ?></td>
                    <td><?= h($participants->last_name) ?></td>
                    <td><?= $participants->hasValue('participant_type') ?
                            h($participants->participant_type->participant_type) : '' ?></td>
                    <td><?= $participants->hasValue('section') ? $this->Html->link(
                        title: $participants->section->section_name,
                        url: ['controller' => 'Sections', 'action' => 'view', $participants->section->id],
                        ) : ''?>
                    </td>
                    <td><?= $participants->checked_in ? $this->Html->icon('check-circle') : '' ?></td>
                    <td><?= $participants->checked_out ? $this->Html->icon('check-circle') : '' ?></td>
                    <td><?= h($participants->highest_check_in_sequence) ?></td>
                    <td><?= h($participants->created) ?></td>
                    <td><?= h($participants->modified) ?></td>
                    <td class="actions">
                        <?= $this->Actions->buttons($participants, ['outline' => true]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
