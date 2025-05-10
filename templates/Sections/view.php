<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Section $section
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Section'), ['action' => 'edit', $section->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Section'), ['action' => 'delete', $section->id], ['confirm' => __('Are you sure you want to delete # {0}?', $section->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Sections'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Section'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Participant Types'), ['controller' => 'ParticipantTypes', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant Type'), ['controller' => 'ParticipantTypes', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Groups'), ['controller' => 'Groups', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Group'), ['controller' => 'Groups', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Participants'), ['controller' => 'Participants', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Participant'), ['controller' => 'Participants', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Event'), ['controller' => 'Events', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="sections view large-9 medium-8 columns content">
    <h3><?= h($section->section_name) ?></h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('Id') ?></th>
                <td><?= h($section->id) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Section Name') ?></th>
                <td><?= h($section->section_name) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Participant Type') ?></th>
                <td><?= $section->hasValue('participant_type') ? $this->Html->link($section->participant_type->participant_type, ['controller' => 'ParticipantTypes', 'action' => 'view', $section->participant_type->id]) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Group') ?></th>
                <td><?= $section->hasValue('group') ? $this->Html->link($section->group->group_name, ['controller' => 'Groups', 'action' => 'view', $section->group->id]) : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Osm Section Id') ?></th>
                <td><?= $section->osm_section_id === null ? '' : $this->Number->format($section->osm_section_id) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td><?= h($section->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Modified') ?></th>
                <td><?= h($section->modified) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Deleted') ?></th>
                <td><?= h($section->deleted) ?></td>
            </tr>
        </table>
    </div>
    <div class="related">
        <h4><?= __('Related Events') ?></h4>
        <?php if (!empty($section->events)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Event Name') ?></th>
                    <th scope="col"><?= __('Event Description') ?></th>
                    <th scope="col"><?= __('Booking Code') ?></th>
                    <th scope="col"><?= __('Start Time') ?></th>
                    <th scope="col"><?= __('Bookable') ?></th>
                    <th scope="col"><?= __('Finished') ?></th>
                    <th scope="col"><?= __('Entry Count') ?></th>
                    <th scope="col"><?= __('Participant Count') ?></th>
                    <th scope="col"><?= __('Checked In Count') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col"><?= __('Deleted') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($section->events as $events): ?>
                <tr>
                    <td><?= h($events->id) ?></td>
                    <td><?= h($events->event_name) ?></td>
                    <td><?= h($events->event_description) ?></td>
                    <td><?= h($events->booking_code) ?></td>
                    <td><?= h($events->start_time) ?></td>
                    <td><?= h($events->bookable) ?></td>
                    <td><?= h($events->finished) ?></td>
                    <td><?= h($events->entry_count) ?></td>
                    <td><?= h($events->participant_count) ?></td>
                    <td><?= h($events->checked_in_count) ?></td>
                    <td><?= h($events->created) ?></td>
                    <td><?= h($events->modified) ?></td>
                    <td><?= h($events->deleted) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['controller' => 'Events', 'action' => 'view', $events->id], ['class' => 'btn btn-secondary']) ?>
                        <?= $this->Html->link(__('Edit'), ['controller' => 'Events', 'action' => 'edit', $events->id], ['class' => 'btn btn-secondary']) ?>
                        <?= $this->Form->postLink( __('Delete'), ['controller' => 'Events', 'action' => 'delete', $events->id], ['confirm' => __('Are you sure you want to delete # {0}?', $events->id), 'class' => 'btn btn-danger']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Participants') ?></h4>
        <?php if (!empty($section->participants)): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('First Name') ?></th>
                    <th scope="col"><?= __('Last Name') ?></th>
                    <th scope="col"><?= __('Entry Id') ?></th>
                    <th scope="col"><?= __('Participant Type Id') ?></th>
                    <th scope="col"><?= __('Section Id') ?></th>
                    <th scope="col"><?= __('Checked In') ?></th>
                    <th scope="col"><?= __('Checked Out') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col"><?= __('Deleted') ?></th>
                    <th scope="col"><?= __('Highest Check In Sequence') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($section->participants as $participants): ?>
                <tr>
                    <td><?= h($participants->id) ?></td>
                    <td><?= h($participants->first_name) ?></td>
                    <td><?= h($participants->last_name) ?></td>
                    <td><?= h($participants->entry_id) ?></td>
                    <td><?= h($participants->participant_type_id) ?></td>
                    <td><?= h($participants->section_id) ?></td>
                    <td><?= h($participants->checked_in) ?></td>
                    <td><?= h($participants->checked_out) ?></td>
                    <td><?= h($participants->created) ?></td>
                    <td><?= h($participants->modified) ?></td>
                    <td><?= h($participants->deleted) ?></td>
                    <td><?= h($participants->highest_check_in_sequence) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['controller' => 'Participants', 'action' => 'view', $participants->id], ['class' => 'btn btn-secondary']) ?>
                        <?= $this->Html->link(__('Edit'), ['controller' => 'Participants', 'action' => 'edit', $participants->id], ['class' => 'btn btn-secondary']) ?>
                        <?= $this->Form->postLink( __('Delete'), ['controller' => 'Participants', 'action' => 'delete', $participants->id], ['confirm' => __('Are you sure you want to delete # {0}?', $participants->id), 'class' => 'btn btn-danger']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
