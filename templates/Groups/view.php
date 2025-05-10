<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Group $group
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Group'), ['action' => 'edit', $group->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Group'), ['action' => 'delete', $group->id], ['confirm' => __('Are you sure you want to delete # {0}?', $group->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Groups'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Group'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="groups view content">
            <h3><?= h($group->group_name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Group Name') ?></th>
                    <td><?= h($group->group_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Sort Order') ?></th>
                    <td><?= $this->Number->format($group->sort_order) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($group->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($group->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deleted') ?></th>
                    <td><?= h($group->deleted) ?></td>
                </tr>
                <tr>
                    <th><?= __('Visible') ?></th>
                    <td><?= $group->visible ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Sections') ?></h4>
                <?php if (!empty($group->sections)) : ?>
                    <?php foreach ($group->sections as $section) : ?>
                        <div class="card">
                            <div class="card-header"><?= h($section->section_name) ?></div>
                            <div class="card-body">
                                <table>
                                    <tr>
                                        <th><?= __('Section Name') ?></th>
                                        <th><?= __('Section Type') ?></th>
                                        <th><?= __('OSM Section ID') ?></th>
                                        <th class="actions"><?= __('Actions') ?></th>
                                    </tr>
                                    <tr>
                                        <td><?= h($section->section_name) ?></td>
                                        <td><?= $section->has('participant_type') ? h($section->participant_type->participant_type) : '' ?></td>
                                        <td><?= h($section->osm_section_id) ?></td>
                                        <td class="actions">
                                            <?= $this->Html->link(__('View'), ['controller' => 'Sections', 'action' => 'view', $section->id]) ?>
                                            <?= $this->Html->link(__('Edit'), ['controller' => 'Sections', 'action' => 'edit', $section->id]) ?>
                                            <?= $this->Form->postLink(__('Delete'), ['controller' => 'Sections', 'action' => 'delete', $section->id], ['confirm' => __('Are you sure you want to delete # {0}?', $section->id)]) ?>
                                        </td>
                                    </tr>
                                </table>
                                <?php if (!empty($section->participants)) : ?>
                                    <div class="card-body">
                                        <table  class="table-responsive table-striped">
                                            <tr>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Team Name</th>
                                                <th>Number in Team</th>
                                            </tr>
                                            <?php foreach ($section->participants as $participant) : ?>
                                                <tr>
                                                    <td><?= h($participant->first_name) ?></td>
                                                    <td><?= h($participant->last_name) ?></td>
                                                    <td><?= h($participant->entry->entry_name) ?></td>
                                                    <td><?= h($participant->entry->participant_count) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <hr/>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
