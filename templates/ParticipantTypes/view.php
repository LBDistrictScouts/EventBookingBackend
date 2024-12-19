<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ParticipantType $participantType
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Participant Type'), ['action' => 'edit', $participantType->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Participant Type'), ['action' => 'delete', $participantType->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participantType->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Participant Types'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Participant Type'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="participantTypes view content">
            <h3><?= h($participantType->participant_type) ?></h3>
            <table>
                <tr>
                    <th><?= __('Participant Type') ?></th>
                    <td><?= h($participantType->participant_type) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($participantType->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($participantType->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($participantType->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deleted') ?></th>
                    <td><?= h($participantType->deleted) ?></td>
                </tr>
                <tr>
                    <th><?= __('Adult') ?></th>
                    <td><?= $participantType->adult ? __('Yes') : __('No'); ?></td>
                </tr>
                <tr>
                    <th><?= __('Uniformed') ?></th>
                    <td><?= $participantType->uniformed ? __('Yes') : __('No'); ?></td>
                </tr>
                <tr>
                    <th><?= __('Out Of District') ?></th>
                    <td><?= $participantType->out_of_district ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Participants') ?></h4>
                <?php if (!empty($participantType->participants)) : ?>
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
                        <?php foreach ($participantType->participants as $participant) : ?>
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
            <div class="related">
                <h4><?= __('Related Sections') ?></h4>
                <?php if (!empty($participantType->sections)) : ?>
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
                        <?php foreach ($participantType->sections as $section) : ?>
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
        </div>
    </div>
</div>