<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Question $question
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Question'), ['action' => 'edit', $question->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Question'), ['action' => 'delete', $question->id], ['confirm' => __('Are you sure you want to delete # {0}?', $question->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Questions'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Question'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="questions view large-9 medium-8 columns content">
    <h3><?= h($question->id) ?></h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('Id') ?></th>
                <td><?= h($question->id) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Event') ?></th>
                <td><?= $question->hasValue('event') ? $this->Html->link($question->event->event_name, ['controller' => 'Events', 'action' => 'view', $question->event->id]) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td><?= h($question->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Modified') ?></th>
                <td><?= h($question->modified) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Deleted') ?></th>
                <td><?= h($question->deleted) ?></td>
            </tr>
        </table>
    </div>
    <div class="text">
        <h4><?= __('Question Text') ?></h4>
        <?= $this->Text->autoParagraph(h($question->question_text)); ?>
    </div>
    <div class="text">
        <h4><?= __('Answer Text') ?></h4>
        <?= $this->Text->autoParagraph(h($question->answer_text)); ?>
    </div>
</div>
