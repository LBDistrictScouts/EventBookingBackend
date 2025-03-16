<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CheckIn $checkIn
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Check In'), ['action' => 'edit', $checkIn->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Check In'), ['action' => 'delete', $checkIn->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checkIn->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Check Ins'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Check In'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="checkIns view content">
            <h3><?= h($checkIn->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($checkIn->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Checkpoint') ?></th>
                    <td><?= $checkIn->hasValue('checkpoint') ? $this->Html->link($checkIn->checkpoint->checkpoint_name, ['controller' => 'Checkpoints', 'action' => 'view', $checkIn->checkpoint->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Entry') ?></th>
                    <td><?= $checkIn->hasValue('entry') ? $this->Html->link($checkIn->entry->entry_name, ['controller' => 'Entries', 'action' => 'view', $checkIn->entry->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Participant Count') ?></th>
                    <td><?= $this->Number->format($checkIn->participant_count) ?></td>
                </tr>
                <tr>
                    <th><?= __('Check In Time') ?></th>
                    <td><?= h($checkIn->check_in_time) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($checkIn->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($checkIn->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deleted') ?></th>
                    <td><?= h($checkIn->deleted) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Participants') ?></h4>
                <?php if (!empty($checkIn->participants)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('First Name') ?></th>
                            <th><?= __('Last Name') ?></th>
                            <th><?= __('Entry Id') ?></th>
                            <th><?= __('Participant Type Id') ?></th>
                            <th><?= __('Section Id') ?></th>
                            <th><?= __('Checked In') ?></th>
                            <th><?= __('Checked Out') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Deleted') ?></th>
                            <th><?= __('Highest Check In Sequence') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($checkIn->participants as $participant) : ?>
                        <tr>
                            <td><?= h($participant->id) ?></td>
                            <td><?= h($participant->first_name) ?></td>
                            <td><?= h($participant->last_name) ?></td>
                            <td><?= h($participant->entry_id) ?></td>
                            <td><?= h($participant->participant_type_id) ?></td>
                            <td><?= h($participant->section_id) ?></td>
                            <td><?= h($participant->checked_in) ?></td>
                            <td><?= h($participant->checked_out) ?></td>
                            <td><?= h($participant->created) ?></td>
                            <td><?= h($participant->modified) ?></td>
                            <td><?= h($participant->deleted) ?></td>
                            <td><?= h($participant->highest_check_in_sequence) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Participants', 'action' => 'view', $participant->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Participants', 'action' => 'edit', $participant->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Participants', 'action' => 'delete', $participant->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participant->id)]) ?>
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