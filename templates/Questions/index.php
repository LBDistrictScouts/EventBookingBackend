<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Question[]|\Cake\Collection\CollectionInterface $questions
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('New Question'), ['action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('event_id') ?></th>
        <th scope="col"><?= $this->Paginator->sort('created') ?></th>
        <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($questions as $question) : ?>
        <tr>
            <td><?= h($question->id) ?></td>
            <td><?= $question->hasValue('event') ? $this->Html->link($question->event->event_name, ['controller' => 'Events', 'action' => 'view', $question->event->id]) : '' ?></td>
            <td><?= h($question->created) ?></td>
            <td><?= h($question->modified) ?></td>
            <td class="actions">
                <?= $this->Html->link($this->Html->icon('eye'), ['action' => 'view', $question->id], ['title' => __('View'), 'class' => 'btn btn-sm btn-secondary', 'escape' => false]) ?>
                <?= $this->Html->link($this->Html->icon('pencil-square'), ['action' => 'edit', $question->id], ['title' => __('Edit'), 'class' => 'btn btn-sm btn-secondary', 'escape' => false]) ?>
                <?= $this->Form->postLink($this->Html->icon('trash3'), ['action' => 'delete', $question->id], ['confirm' => __('Are you sure you want to delete # {0}?', $question->id), 'title' => __('Delete'), 'class' => 'btn btn-sm btn-danger', 'escape' => false]) ?>
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
