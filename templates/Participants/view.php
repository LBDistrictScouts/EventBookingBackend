<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Participant $participant
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Participant'), ['action' => 'edit', $participant->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Participant'), ['action' => 'delete', $participant->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participant->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Participants'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Participant'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="participants view content">
            <h3><?= h($participant->first_name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($participant->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('First Name') ?></th>
                    <td><?= h($participant->first_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Last Name') ?></th>
                    <td><?= h($participant->last_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Entry') ?></th>
                    <td><?= $participant->hasValue('entry') ? $this->Html->link($participant->entry->entry_name, ['controller' => 'Entries', 'action' => 'view', $participant->entry->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Participant Type') ?></th>
                    <td><?= $participant->hasValue('participant_type') ? $this->Html->link($participant->participant_type->participant_type, ['controller' => 'ParticipantTypes', 'action' => 'view', $participant->participant_type->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Section') ?></th>
                    <td><?= $participant->hasValue('section') ? $this->Html->link($participant->section->section_name, ['controller' => 'Sections', 'action' => 'view', $participant->section->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Highest Check In Sequence') ?></th>
                    <td><?= $this->Number->format($participant->highest_check_in_sequence) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($participant->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($participant->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deleted') ?></th>
                    <td><?= h($participant->deleted) ?></td>
                </tr>
                <tr>
                    <th><?= __('Checked In') ?></th>
                    <td><?= $participant->checked_in ? __('Yes') : __('No'); ?></td>
                </tr>
                <tr>
                    <th><?= __('Checked Out') ?></th>
                    <td><?= $participant->checked_out ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Check Ins') ?></h4>
                <?php if (!empty($participant->check_ins)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Checkpoint Id') ?></th>
                            <th><?= __('Entry Id') ?></th>
                            <th><?= __('Check In Time') ?></th>
                            <th><?= __('Participant Count') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Deleted') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($participant->check_ins as $checkIn) : ?>
                        <tr>
                            <td><?= h($checkIn->id) ?></td>
                            <td><?= h($checkIn->checkpoint_id) ?></td>
                            <td><?= h($checkIn->entry_id) ?></td>
                            <td><?= h($checkIn->check_in_time) ?></td>
                            <td><?= h($checkIn->participant_count) ?></td>
                            <td><?= h($checkIn->created) ?></td>
                            <td><?= h($checkIn->modified) ?></td>
                            <td><?= h($checkIn->deleted) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'CheckIns', 'action' => 'view', $checkIn->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'CheckIns', 'action' => 'edit', $checkIn->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'CheckIns', 'action' => 'delete', $checkIn->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checkIn->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>