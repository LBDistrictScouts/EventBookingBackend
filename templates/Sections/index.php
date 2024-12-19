<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Section> $sections
 */
?>
<div class="sections index content">
    <?= $this->Html->link(__('New Section'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Sections') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('section_name') ?></th>
                    <th><?= $this->Paginator->sort('participant_type_id') ?></th>
                    <th><?= $this->Paginator->sort('group_id') ?></th>
                    <th><?= $this->Paginator->sort('osm_section_id') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= $this->Paginator->sort('deleted') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sections as $section): ?>
                <tr>
                    <td><?= $this->Number->format($section->id) ?></td>
                    <td><?= h($section->section_name) ?></td>
                    <td><?= $section->hasValue('participant_type') ? $this->Html->link($section->participant_type->participant_type, ['controller' => 'ParticipantTypes', 'action' => 'view', $section->participant_type->id]) : '' ?></td>
                    <td><?= $section->hasValue('group') ? $this->Html->link($section->group->group_name, ['controller' => 'Groups', 'action' => 'view', $section->group->id]) : '' ?></td>
                    <td><?= $section->osm_section_id === null ? '' : $this->Number->format($section->osm_section_id) ?></td>
                    <td><?= h($section->created) ?></td>
                    <td><?= h($section->modified) ?></td>
                    <td><?= h($section->deleted) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $section->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $section->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $section->id], ['confirm' => __('Are you sure you want to delete # {0}?', $section->id)]) ?>
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