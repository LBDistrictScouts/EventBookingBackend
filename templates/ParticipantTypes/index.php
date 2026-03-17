<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ParticipantType[]|\Cake\Collection\CollectionInterface $participantTypes
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>
<?php
$explicitSort = $this->request->getQuery('sort');
$sortLink = function (string $field, ?string $label = null) use ($explicitSort): string {
    if ($explicitSort !== null && $explicitSort !== '') {
        return $this->Paginator->sort($field, $label);
    }

    return $this->Html->link(
        $label ?? ucfirst(str_replace('_', ' ', $field)),
        ['action' => 'index', '?' => ['sort' => $field, 'direction' => 'asc']],
        ['class' => 'text-decoration-none text-body'],
    );
};
?>

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
        <th scope="col"><?= $sortLink('participant_type') ?></th>
        <th scope="col"><?= $sortLink('category') ?></th>
        <th scope="col"><?= $sortLink('sort_order') ?></th>
        <th scope="col"><?= $sortLink('adult') ?></th>
        <th scope="col"><?= $sortLink('uniformed') ?></th>
        <th scope="col"><?= $sortLink('out_of_district') ?></th>
        <th scope="col"><?= $sortLink('created') ?></th>
        <th scope="col"><?= $sortLink('modified') ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($participantTypes as $participantType) : ?>
        <tr>
            <td><?= h($participantType->participant_type) ?></td>
            <td><?= h($participantType->category?->label()) ?></td>
            <td><?= $this->Number->format($participantType->sort_order) ?></td>
            <td><?= $this->BooleanIcon->render($participantType->adult) ?></td>
            <td><?= $this->BooleanIcon->render($participantType->uniformed) ?></td>
            <td><?= $this->BooleanIcon->render($participantType->out_of_district) ?></td>
            <td><?= h($participantType->created) ?></td>
            <td><?= h($participantType->modified) ?></td>
            <td class="actions">
                <?= $this->Actions->buttons($participantType) ?>
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
