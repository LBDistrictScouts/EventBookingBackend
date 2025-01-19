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
                    <th><?= __('Id') ?></th>
                    <td><?= h($group->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Group Name') ?></th>
                    <td><?= h($group->group_name) ?></td>
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
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Section Name') ?></th>
                            <th><?= __('Participant Type Id') ?></th>
                            <th><?= __('Group Id') ?></th>
                            <th><?= __('Osm Section Id') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Deleted') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($group->sections as $section) : ?>
                        <tr>
                            <td><?= h($section->id) ?></td>
                            <td><?= h($section->section_name) ?></td>
                            <td><?= h($section->participant_type_id) ?></td>
                            <td><?= h($section->group_id) ?></td>
                            <td><?= h($section->osm_section_id) ?></td>
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