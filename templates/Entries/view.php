<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entry $entry
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Entry'), ['action' => 'edit', $entry->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Entry'), ['action' => 'delete', $entry->id], ['confirm' => __('Are you sure you want to delete # {0}?', $entry->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Entries'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Entry'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="entries view content">
            <h3><?= h($entry->entry_name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= h($entry->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Event') ?></th>
                    <td><?= $entry->hasValue('event') ? $this->Html->link($entry->event->event_name, ['controller' => 'Events', 'action' => 'view', $entry->event->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Entry Name') ?></th>
                    <td><?= h($entry->entry_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Entry Email') ?></th>
                    <td><?= h($entry->entry_email) ?></td>
                </tr>
                <tr>
                    <th><?= __('Participant Count') ?></th>
                    <td><?= $this->Number->format($entry->participant_count) ?></td>
                </tr>
                <tr>
                    <th><?= __('Checked In Count') ?></th>
                    <td><?= $this->Number->format($entry->checked_in_count) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($entry->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($entry->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deleted') ?></th>
                    <td><?= h($entry->deleted) ?></td>
                </tr>
                <tr>
                    <th><?= __('Active') ?></th>
                    <td><?= $entry->active ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Check Ins') ?></h4>
                <?php if (!empty($entry->check_ins)) : ?>
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
                        <?php foreach ($entry->check_ins as $checkIn) : ?>
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
            <div class="related">
                <h4><?= __('Related Participants') ?></h4>
                <?php if (!empty($entry->participants)) : ?>
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
                        <?php foreach ($entry->participants as $participant) : ?>
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