<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ParticipantType[]|\Cake\Collection\CollectionInterface $participantTypes
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('New Participant Type'), ['action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('participant_type') ?></th>
        <th scope="col"><?= $this->Paginator->sort('adult') ?></th>
        <th scope="col"><?= $this->Paginator->sort('uniformed') ?></th>
        <th scope="col"><?= $this->Paginator->sort('out_of_district') ?></th>
        <th scope="col"><?= $this->Paginator->sort('created') ?></th>
        <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
        <th scope="col"><?= $this->Paginator->sort('deleted') ?></th>
        <th scope="col"><?= $this->Paginator->sort('category') ?></th>
        <th scope="col"><?= $this->Paginator->sort('sort_order') ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($participantTypes as $participantType) : ?>
        <tr>
            <td><?= h($participantType->id) ?></td>
            <td><?= h($participantType->participant_type) ?></td>
            <td><?= h($participantType->adult) ?></td>
            <td><?= h($participantType->uniformed) ?></td>
            <td><?= h($participantType->out_of_district) ?></td>
            <td><?= h($participantType->created) ?></td>
            <td><?= h($participantType->modified) ?></td>
            <td><?= h($participantType->deleted) ?></td>
            <td><?= h($participantType->category) ?></td>
            <td><?= $this->Number->format($participantType->sort_order) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $participantType->id], ['title' => __('View'), 'class' => 'btn btn-secondary']) ?>
                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $participantType->id], ['title' => __('Edit'), 'class' => 'btn btn-secondary']) ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $participantType->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participantType->id), 'title' => __('Delete'), 'class' => 'btn btn-danger']) ?>
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
