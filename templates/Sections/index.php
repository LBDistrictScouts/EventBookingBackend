<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Section[]|\Cake\Collection\CollectionInterface $sections
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('New Section'), ['action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participant Types'), ['controller' => 'ParticipantTypes', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant Type'), ['controller' => 'ParticipantTypes', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Groups'), ['controller' => 'Groups', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Group'), ['controller' => 'Groups', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('section_name') ?></th>
        <th scope="col"><?= $this->Paginator->sort('participant_type_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('group_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('osm_section_id', 'OSM Section ID') ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($sections as $section) : ?>
        <tr>
            <td><?= h($section->section_name) ?></td>
            <td><?= $section->hasValue('participant_type') ? $this->Html->link($section->participant_type->participant_type, ['controller' => 'ParticipantTypes', 'action' => 'view', $section->participant_type->id]) : '' ?></td>
            <td><?= $section->hasValue('group') ? $this->Html->link($section->group->group_name, ['controller' => 'Groups', 'action' => 'view', $section->group->id]) : '' ?></td>
            <td><?= $section->osm_section_id === null ? '' : h($section->osm_section_id) ?></td>
            <td class="actions">
                <?= $this->Actions->buttons($section) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->first('«', ['label' => __('First')]) ?>
        <?= $this->Paginator->prev('‹', ['label' => __('Previous')]) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next('›', ['label' => __('Next')]) ?>
        <?= $this->Paginator->last('»', ['label' => __('Last')]) ?>
    </ul>
    <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
</div>
