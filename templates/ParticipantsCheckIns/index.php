<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ParticipantsCheckIn[]|\Cake\Collection\CollectionInterface $participantsCheckIns
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('New Participants Check In'), ['action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Check Ins'), ['controller' => 'CheckIns', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Check In'), ['controller' => 'CheckIns', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('check_in_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('participant_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('created') ?></th>
        <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
        <th scope="col"><?= $this->Paginator->sort('deleted') ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($participantsCheckIns as $participantsCheckIn) : ?>
        <tr>
            <td><?= h($participantsCheckIn->id) ?></td>
            <td><?= $participantsCheckIn->hasValue('check_in') ? $this->Html->link($participantsCheckIn->check_in->id, ['controller' => 'CheckIns', 'action' => 'view', $participantsCheckIn->check_in->id]) : '' ?></td>
            <td><?= $participantsCheckIn->hasValue('participant') ? $this->Html->link($participantsCheckIn->participant->first_name, ['controller' => 'Participants', 'action' => 'view', $participantsCheckIn->participant->id]) : '' ?></td>
            <td><?= h($participantsCheckIn->created) ?></td>
            <td><?= h($participantsCheckIn->modified) ?></td>
            <td><?= h($participantsCheckIn->deleted) ?></td>
            <td class="actions">
                <?= $this->Actions->buttons($participantsCheckIn) ?>
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
