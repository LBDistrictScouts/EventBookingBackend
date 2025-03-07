<?php

use Cake\Utility\Inflector;

/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ParticipantType> $participantTypes
 */
?>
<div class="participantTypes index content">
    <?= $this->Html->link(__('New Participant Type'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Participant Types') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('participant_type') ?></th>
                    <th><?= $this->Paginator->sort('sort_order') ?></th>
                    <th><?= $this->Paginator->sort('adult') ?></th>
                    <th><?= $this->Paginator->sort('uniformed') ?></th>
                    <th><?= $this->Paginator->sort('out_of_district') ?></th>
                    <th><?= $this->Paginator->sort('category') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participantTypes as $participantType) : ?>
                <tr>
                    <td><?= h($participantType->participant_type) ?></td>
                    <td><?= $this->Number->ordinal($participantType->sort_order) ?></td>
                    <td><?= h($participantType->adult) ? 'Yes' : 'No' ?></td>
                    <td><?= h($participantType->uniformed) ? 'Yes' : 'No' ?></td>
                    <td><?= h($participantType->out_of_district) ? 'Yes' : 'No' ?></td>
                    <td><?= $participantType->category === null ? '' :
                            Inflector::humanize(Inflector::underscore($participantType->category->name)) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $participantType->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $participantType->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $participantType->id],
                            ['confirm' => __('Are you sure you want to delete # {0}?', $participantType->id)]
                        ) ?>
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
        <p><?= $this->Paginator->counter(
            __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')
        ) ?></p>
    </div>
</div>
