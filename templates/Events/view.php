<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Event $event
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Event'), ['action' => 'edit', $event->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Event'), ['action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Events'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Event'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="events view content">
            <h3><?= h($event->event_name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Event Name') ?></th>
                    <td><?= h($event->event_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Event Description') ?></th>
                    <td><?= h($event->event_description) ?></td>
                </tr>
                <tr>
                    <th><?= __('Booking Code') ?></th>
                    <td><?= h($event->booking_code) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($event->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Entry Count') ?></th>
                    <td><?= $this->Number->format($event->entry_count) ?></td>
                </tr>
                <tr>
                    <th><?= __('Participant Count') ?></th>
                    <td><?= $this->Number->format($event->participant_count) ?></td>
                </tr>
                <tr>
                    <th><?= __('Checked In Count') ?></th>
                    <td><?= $this->Number->format($event->checked_in_count) ?></td>
                </tr>
                <tr>
                    <th><?= __('Start Time') ?></th>
                    <td><?= h($event->start_time) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($event->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($event->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deleted') ?></th>
                    <td><?= h($event->deleted) ?></td>
                </tr>
                <tr>
                    <th><?= __('Bookable') ?></th>
                    <td><?= $event->bookable ? __('Yes') : __('No'); ?></td>
                </tr>
                <tr>
                    <th><?= __('Finished') ?></th>
                    <td><?= $event->finished ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Sections') ?></h4>
                <?php if (!empty($event->sections)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Section Name') ?></th>
                            <th><?= __('Participant Type Id') ?></th>
                            <th><?= __('Group Id') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Deleted') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($event->sections as $section) : ?>
                        <tr>
                            <td><?= h($section->id) ?></td>
                            <td><?= h($section->section_name) ?></td>
                            <td><?= h($section->participant_type_id) ?></td>
                            <td><?= h($section->group_id) ?></td>
                            <td><?= h($section->created) ?></td>
                            <td><?= h($section->modified) ?></td>
                            <td><?= h($section->deleted) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Sections', 'action' => 'view', $section->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Sections', 'action' => 'edit', $section->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Sections', 'action' => 'delete', $section->id], ['confirm' => __('Are you sure you want to delete # {0}?', $section->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Checkpoints') ?></h4>
                <?php if (!empty($event->checkpoints)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Checkpoint Sequence') ?></th>
                            <th><?= __('Checkpoint Name') ?></th>
                            <th><?= __('Event Id') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Deleted') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($event->checkpoints as $checkpoint) : ?>
                        <tr>
                            <td><?= h($checkpoint->id) ?></td>
                            <td><?= h($checkpoint->checkpoint_sequence) ?></td>
                            <td><?= h($checkpoint->checkpoint_name) ?></td>
                            <td><?= h($checkpoint->event_id) ?></td>
                            <td><?= h($checkpoint->created) ?></td>
                            <td><?= h($checkpoint->modified) ?></td>
                            <td><?= h($checkpoint->deleted) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Checkpoints', 'action' => 'view', $checkpoint->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Checkpoints', 'action' => 'edit', $checkpoint->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Checkpoints', 'action' => 'delete', $checkpoint->id], ['confirm' => __('Are you sure you want to delete # {0}?', $checkpoint->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Entries') ?></h4>
                <?php if (!empty($event->entries)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Event Id') ?></th>
                            <th><?= __('Entry Name') ?></th>
                            <th><?= __('Active') ?></th>
                            <th><?= __('Participant Count') ?></th>
                            <th><?= __('Checked In Count') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Deleted') ?></th>
                            <th><?= __('Entry Email') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($event->entries as $entry) : ?>
                        <tr>
                            <td><?= h($entry->id) ?></td>
                            <td><?= h($entry->event_id) ?></td>
                            <td><?= h($entry->entry_name) ?></td>
                            <td><?= h($entry->active) ?></td>
                            <td><?= h($entry->participant_count) ?></td>
                            <td><?= h($entry->checked_in_count) ?></td>
                            <td><?= h($entry->created) ?></td>
                            <td><?= h($entry->modified) ?></td>
                            <td><?= h($entry->deleted) ?></td>
                            <td><?= h($entry->entry_email) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Entries', 'action' => 'view', $entry->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Entries', 'action' => 'edit', $entry->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Entries', 'action' => 'delete', $entry->id], ['confirm' => __('Are you sure you want to delete # {0}?', $entry->id)]) ?>
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