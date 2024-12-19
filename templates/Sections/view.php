<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Section $section
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Section'), ['action' => 'edit', $section->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Section'), ['action' => 'delete', $section->id], ['confirm' => __('Are you sure you want to delete # {0}?', $section->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Sections'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Section'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="sections view content">
            <h3><?= h($section->section_name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Section Name') ?></th>
                    <td><?= h($section->section_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Participant Type') ?></th>
                    <td><?= $section->hasValue('participant_type') ? $this->Html->link($section->participant_type->participant_type, ['controller' => 'ParticipantTypes', 'action' => 'view', $section->participant_type->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Group') ?></th>
                    <td><?= $section->hasValue('group') ? $this->Html->link($section->group->group_name, ['controller' => 'Groups', 'action' => 'view', $section->group->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($section->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Osm Section Id') ?></th>
                    <td><?= $section->osm_section_id === null ? '' : $this->Number->format($section->osm_section_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($section->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($section->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deleted') ?></th>
                    <td><?= h($section->deleted) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Events') ?></h4>
                <?php if (!empty($section->events)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Event Name') ?></th>
                            <th><?= __('Event Description') ?></th>
                            <th><?= __('Booking Code') ?></th>
                            <th><?= __('Start Time') ?></th>
                            <th><?= __('Bookable') ?></th>
                            <th><?= __('Finished') ?></th>
                            <th><?= __('Entry Count') ?></th>
                            <th><?= __('Participant Count') ?></th>
                            <th><?= __('Checked In Count') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Deleted') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($section->events as $event) : ?>
                        <tr>
                            <td><?= h($event->id) ?></td>
                            <td><?= h($event->event_name) ?></td>
                            <td><?= h($event->event_description) ?></td>
                            <td><?= h($event->booking_code) ?></td>
                            <td><?= h($event->start_time) ?></td>
                            <td><?= h($event->bookable) ?></td>
                            <td><?= h($event->finished) ?></td>
                            <td><?= h($event->entry_count) ?></td>
                            <td><?= h($event->participant_count) ?></td>
                            <td><?= h($event->checked_in_count) ?></td>
                            <td><?= h($event->created) ?></td>
                            <td><?= h($event->modified) ?></td>
                            <td><?= h($event->deleted) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Events', 'action' => 'view', $event->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Events', 'action' => 'edit', $event->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Events', 'action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Participants') ?></h4>
                <?php if (!empty($section->participants)) : ?>
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
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($section->participants as $participant) : ?>
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