<?php
/**
 * @var \App\View\AppView $this
 * @var bool $enhanced
 * @var iterable<\App\Model\Entity\Participant> $participants
 */
?>
<div class="participants index content">
    <?= $this->Html->link(__('New Participant'), ['action' => 'add'], ['class' => 'button float-right']) ?>

    <h3><?= __('Participants') ?></h3>
    <?= $this->Html->link(__($enhanced ? 'Simple View' : 'Enhanced View'), [
        'action' => 'index',
        '?' => ['enhanced' => !$enhanced],
    ], ['class' => 'button float-right']) ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('entry_id') ?></th>
                    <th><?= $this->Paginator->sort('first_name') ?></th>
                    <th><?= $this->Paginator->sort('last_name') ?></th>
                    <th><?= $this->Paginator->sort('participant_type_id') ?></th>
                    <th><?= $this->Paginator->sort('section_id') ?></th>
                    <?php if ($enhanced) : ?>
                        <th><?= $this->Paginator->sort('checked_in') ?></th>
                        <th><?= $this->Paginator->sort('checked_out') ?></th>
                        <th><?= $this->Paginator->sort('created') ?></th>
                        <th><?= $this->Paginator->sort('modified') ?></th>
                        <th><?= $this->Paginator->sort('deleted') ?></th>
                        <th><?= $this->Paginator->sort('highest_check_in_sequence') ?></th>
                    <?php endif; ?>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $participant): ?>
                <tr>
                    <td><?= $participant->hasValue('entry') ? $this->Html->link($participant->entry->entry_name, ['controller' => 'Entries', 'action' => 'view', $participant->entry->id]) : '' ?></td>

                    <td><?= h($participant->first_name) ?></td>
                    <td><?= h($participant->last_name) ?></td>
                    <td><?= $participant->hasValue('participant_type') ? $this->Html->link($participant->participant_type->participant_type, ['controller' => 'ParticipantTypes', 'action' => 'view', $participant->participant_type->id]) : '' ?></td>
                    <td><?= $participant->hasValue('section') ? $this->Html->link($participant->section->section_name, ['controller' => 'Sections', 'action' => 'view', $participant->section->id]) : '' ?></td>
                    <?php if ($enhanced) : ?>
                        <td><?= h($participant->checked_in) ?></td>
                        <td><?= h($participant->checked_out) ?></td>
                        <td><?= h($participant->created) ?></td>
                        <td><?= h($participant->modified) ?></td>
                        <td><?= h($participant->deleted) ?></td>
                        <td><?= $this->Number->format($participant->highest_check_in_sequence) ?></td>
                    <?php endif; ?>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $participant->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $participant->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $participant->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participant->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
