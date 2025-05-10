<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Group $group
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Group'), ['action' => 'edit', $group->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Group'), ['action' => 'delete', $group->id], ['confirm' => __('Are you sure you want to delete {0}?', $group->group_name), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Groups'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Group'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="groups view large-9 medium-8 columns content">
    <h3><?= h($group->group_name) ?></h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('Group Name') ?></th>
                <td><?= h($group->group_name) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Sort Order') ?></th>
                <td><?= $this->Number->format($group->sort_order) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td><?= h($group->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Modified') ?></th>
                <td><?= h($group->modified) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Deleted') ?></th>
                <td><?= h($group->deleted) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Visible') ?></th>
                <td><?= $group->visible ? __('Yes') : __('No'); ?></td>
            </tr>
        </table>
    </div>
    <div class="related">
        <h4><?= __('Related Sections') ?></h4>
        <?php if (!empty($group->sections)) : ?>
            <?php foreach ($group->sections as $section) : ?>
                <div class="card">
                    <div class="card-header"><?= h($section->section_name) ?></div>
                    <div class="card-body">
                        <table class="table">
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
                                    <?= $this->Html->link(
                                        title: $this->Html->icon('eye'),
                                        url: ['controller' => 'Sections', 'action' => 'view', $section->id],
                                        options: ['escape' => false, 'class' => 'btn btn-sm btn-success'],
                                    ) ?>
                                    <?= $this->Html->link(
                                        title: $this->Html->icon('pencil-square'),
                                        url: ['controller' => 'Sections', 'action' => 'edit', $section->id],
                                        options: ['escape' => false, 'class' => 'btn btn-sm btn-warning'],
                                    ) ?>
                                    <?= $this->Form->postLink(
                                        title: $this->Html->icon('trash3'),
                                        url: ['controller' => 'Sections', 'action' => 'delete', $section->id],
                                        options: [
                                            'confirm' => __('Are you sure you want to delete # {0}?', $section->id),
                                            'escape' => false,
                                            'class' => 'btn btn-sm btn-danger',
                                        ],
                                    ) ?>
                                </td>
                            </tr>
                        </table>
                        <?php if (!empty($section->participants)) : ?>
                            <div class="card-body">
                                <table  class="table table-responsive table-striped">
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Team Name</th>
                                        <th>Number in Team</th>
                                        <th>Actions</th>
                                    </tr>
                                    <?php foreach ($section->participants as $participant) : ?>
                                        <tr>
                                            <td><?= h($participant->first_name) ?></td>
                                            <td><?= h($participant->last_name) ?></td>
                                            <td><?= $this->Html->link($participant->entry->entry_name, [
                                                    'controller' => 'Entries',
                                                    'action' => 'view',
                                                    $participant->entry->id,
                                                ]) ?></td>
                                            <td><?= h($participant->entry->participant_count) ?></td>
                                            <td class="actions">
                                                <?= $this->Html->link(
                                                    title: $this->Html->icon('eye'),
                                                    url: ['controller' => 'Participants', 'action' => 'view', $participant->id],
                                                    options: ['escape' => false, 'class' => 'btn btn-sm btn-outline-success'],
                                                ) ?>
                                                <?= $this->Html->link(
                                                    title: $this->Html->icon('pencil-square'),
                                                    url: ['controller' => 'Participants', 'action' => 'edit', $participant->id],
                                                    options: ['escape' => false, 'class' => 'btn btn-sm btn-outline-warning'],
                                                ) ?>
                                                <?= $this->Form->postLink(
                                                    title: $this->Html->icon('trash3'),
                                                    url: ['controller' => 'Participants', 'action' => 'delete', $participant->id],
                                                    options: [
                                                        'confirm' => __(
                                                            'Are you sure you want to delete # {0} {1}?',
                                                            $participant->first_name,
                                                            $participant->last_name,
                                                        ),
                                                        'escape' => false,
                                                        'class' => 'btn btn-sm btn-outline-danger',
                                                    ],
                                                ) ?>
                                            </td>
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
