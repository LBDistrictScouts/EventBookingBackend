<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Group> $groups
 */
?>
<div class="groups index content">
    <?= $this->Html->link(__('New Group'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Groups') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('group_name') ?></th>
                    <th><?= $this->Paginator->sort('visible') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= $this->Paginator->sort('sort_order') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groups as $group): ?>
                <tr>
                    <td><?= h($group->id) ?></td>
                    <td><?= h($group->group_name) ?></td>
                    <td><?= h($group->visible) ?></td>
                    <td><?= h($group->created) ?></td>
                    <td><?= h($group->modified) ?></td>
                    <td><?= $this->Number->format($group->sort_order) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $group->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $group->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $group->id], ['confirm' => __('Are you sure you want to delete # {0}?', $group->id)]) ?>
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
