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
                <td><?= $section->hasValue('participant_type') ? $this->Html->link(
                    title: $section->participant_type->participant_type,
                    url: ['controller' => 'ParticipantTypes', 'action' => 'view', $section->participant_type->id],
                )
                                : '' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Group') ?></th>
                <td><?= $section->hasValue('group') ? $this->Html->link(
                    title: $section->group->group_name,
                    url: ['controller' => 'Groups', 'action' => 'view', $section->group->id],
                )
                                : '' ?></td>
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
        </table>
    </div>
    <div class="related">
        <h4><?= __('Related Events') ?></h4>
        <?php if (!empty($section->events)) : ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('Event Name') ?></th>
                    <th scope="col"><?= __('Booking Code') ?></th>
                    <th scope="col"><?= __('Start Time') ?></th>
                    <th scope="col"><?= __('Entry Count') ?></th>
                    <th scope="col"><?= __('Participant Count') ?></th>
                    <th scope="col"><?= __('Checked In Count') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($section->events as $events) : ?>
                <tr>
                    <td><?= h($events->event_name) ?></td>
                    <td><?= h($events->booking_code) ?></td>
                    <td><?= h($events->start_time) ?></td>
                    <td><?= h($events->entry_count) ?></td>
                    <td><?= h($events->participant_count) ?></td>
                    <td><?= h($events->checked_in_count) ?></td>
                    <td class="actions">
                        <?= $this->Actions->buttons($events, ['outline' => true]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Participants') ?></h4>
        <?php if (!empty($section->participants)) : ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th scope="col"><?= __('Name') ?></th>
                    <th scope="col"><?= __('Entry') ?></th>
                    <th scope="col"><?= __('Participant Type') ?></th>
                    <th scope="col"><?= __('In') ?></th>
                    <th scope="col"><?= __('Out') ?></th>
                    <th scope="col"><?= __('#') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($section->participants as $participants) : ?>
                <tr>
                    <td><?= h($participants->full_name) ?></td>
                    <td><?= $participants->hasValue('entry') ?
                            $this->Html->link(
                                title: $participants->entry->entry_name,
                                url: ['controller' => 'Entries', 'action' => 'view', $participants->entry->id],
                            )
                            : '' ?></td>
                    <td><?= $participants->hasValue('participant_type') ?
                            $participants->participant_type->participant_type : '' ?></td>
                    <td><?= $participants->checked_in ? $this->Html->icon('check-circle') : '' ?></td>
                    <td><?= $participants->checked_out ? $this->Html->icon('check-circle') : '' ?></td>
                    <td><?= h($participants->highest_check_in_sequence) ?></td>
                    <td class="actions">
                        <?= $this->Actions->buttons($participants, ['outline' => true]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
