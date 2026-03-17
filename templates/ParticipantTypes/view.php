<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ParticipantType $participantType
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Participant Type'), ['action' => 'edit', $participantType->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Participant Type'), ['action' => 'delete', $participantType->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participantType->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participant Types'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Participant Type'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="participantTypes view large-9 medium-8 columns content">
    <h3><?= h($participantType->participant_type) ?></h3>
    <div class="mb-3">
        <?= $this->Actions->buttons($participantType) ?>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('Participant Type') ?></th>
                <td><?= h($participantType->participant_type) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Category') ?></th>
                <td><?= h($participantType->category?->label()) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Sort Order') ?></th>
                <td><?= $this->Number->format($participantType->sort_order) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td><?= h($participantType->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Modified') ?></th>
                <td><?= h($participantType->modified) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Adult') ?></th>
                <td><?= $this->BooleanIcon->render($participantType->adult) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Uniformed') ?></th>
                <td><?= $this->BooleanIcon->render($participantType->uniformed) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Out Of District') ?></th>
                <td><?= $this->BooleanIcon->render($participantType->out_of_district) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Deleted') ?></th>
                <td><?= $participantType->deleted ? h($participantType->deleted) : '' ?></td>
            </tr>
        </table>
    </div>
    <div class="related">
        <h4><?= __('Related Participants') ?></h4>
        <?php if (!empty($participantType->participants)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Name') ?></th>
                    <th scope="col"><?= __('Entry Id') ?></th>
                    <th scope="col"><?= __('Section Id') ?></th>
                    <th scope="col"><?= __('Checked In') ?></th>
                    <th scope="col"><?= __('Checked Out') ?></th>
                    <th scope="col"><?= __('Highest Check In Sequence') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($participantType->participants as $participants): ?>
                <tr>
                    <td><?= h($participants->id) ?></td>
                    <td><?= h(trim($participants->full_name)) ?></td>
                    <td><?= h($participants->entry_id) ?></td>
                    <td><?= h($participants->section_id) ?></td>
                    <td><?= $this->BooleanIcon->render($participants->checked_in) ?></td>
                    <td><?= $this->BooleanIcon->render($participants->checked_out) ?></td>
                    <td><?= h($participants->highest_check_in_sequence) ?></td>
                    <td class="actions">
                        <?= $this->Actions->buttons($participants) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Sections') ?></h4>
        <?php if (!empty($participantType->sections)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Section Name') ?></th>
                    <th scope="col"><?= __('Group Id') ?></th>
                    <th scope="col"><?= __('Osm Section Id') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($participantType->sections as $sections): ?>
                <tr>
                    <td><?= h($sections->id) ?></td>
                    <td><?= h($sections->section_name) ?></td>
                    <td><?= h($sections->group_id) ?></td>
                    <td><?= $sections->osm_section_id === null ? '' : h($sections->osm_section_id) ?></td>
                    <td class="actions">
                        <?= $this->Actions->buttons($sections) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
