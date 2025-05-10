<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Event $event
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Edit Event'), ['action' => 'edit', $event->id], ['class' => 'nav-link']) ?></li>
<li><?= $this->Form->postLink(__('Delete Event'), ['action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id), 'class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Events'), ['action' => 'index'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('New Event'), ['action' => 'add'], ['class' => 'nav-link']) ?> </li>
<li><?= $this->Html->link(__('List Checkpoints'), ['controller' => 'Checkpoints', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Checkpoint'), ['controller' => 'Checkpoints', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Entries'), ['controller' => 'Entries', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Entry'), ['controller' => 'Entries', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Questions'), ['controller' => 'Questions', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Question'), ['controller' => 'Questions', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<div class="events view large-9 medium-8 columns content">
    <h3><?= h($event->event_name) ?></h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('Event Name') ?></th>
                <td><?= h($event->event_name) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Event Description') ?></th>
                <td><?= h($event->event_description) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Booking Code') ?></th>
                <td><?= h($event->booking_code) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Entry Count') ?></th>
                <td><?= $this->Number->format($event->entry_count) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Participant Count') ?></th>
                <td><?= $this->Number->format($event->participant_count) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Checked In Count') ?></th>
                <td><?= $this->Number->format($event->checked_in_count) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Start Time') ?></th>
                <td><?= h($event->start_time) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td><?= h($event->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Modified') ?></th>
                <td><?= h($event->modified) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Deleted') ?></th>
                <td><?= h($event->deleted) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Bookable') ?></th>
                <td><?= $event->bookable ? __('Yes') : __('No'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Finished') ?></th>
                <td><?= $event->finished ? __('Yes') : __('No'); ?></td>
            </tr>
        </table>
    </div>
    <div class="accordion" id="accordionRelated">
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseSections" aria-expanded="false" aria-controls="collapseSections">
                    <?= __('Related Sections') ?>
                </button>
            </h4>
            <div id="collapseSections" class="accordion-collapse collapse" data-bs-parent="#accordionRelated">
            <?php if (!empty($event->sections)) : ?>
                <div class="accordion-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th><?= __('Section Name') ?></th>
                                <th><?= __('Participant Type') ?></th>
                                <th><?= __('Group') ?></th>
                                <th scope="col" class="actions"><?= __('Actions') ?></th>
                            </tr>
                            <?php foreach ($event->sections as $section) : ?>
                            <tr>
                                <td><?= h($section->section_name) ?></td>
                                <td><?= $section->has('participant_type') ?
                                        $section->participant_type->participant_type : '' ?></td>
                                <td><?= $section->has('group') ? $section->group->group_name : '' ?></td>
                                <td class="actions">
                                    <?= $this->Actions->buttons($section, ['outline' => true]) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            </div>
        </div>
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseCheckpoints" aria-expanded="false" aria-controls="collapseCheckpoints">
                    <?= __('Related Checkpoints') ?>
                </button>
            </h4>
            <div id="collapseCheckpoints" class="accordion-collapse collapse" data-bs-parent="#accordionRelated">
            <?php if (!empty($event->checkpoints)) : ?>
                <div class="accordion-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th scope="col"><?= __('Checkpoint Sequence') ?></th>
                                <th scope="col"><?= __('Checkpoint Name') ?></th>
                                <th scope="col" class="actions"><?= __('Actions') ?></th>
                            </tr>
                            <?php foreach ($event->checkpoints as $checkpoint) : ?>
                            <tr>
                                <td><?= h($checkpoint->checkpoint_sequence) ?></td>
                                <td><?= h($checkpoint->checkpoint_name) ?></td>
                                <td class="actions">
                                    <?= $this->Actions->buttons($checkpoint, ['outline' => true]) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            </div>
        </div>
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseEntries" aria-expanded="true" aria-controls="collapseEntries">
                    <?= __('Related Entries') ?>
                </button>
            </h4>
            <div id="collapseEntries" class="accordion-collapse collapse show" data-bs-parent="#accordionRelated">
            <?php if (!empty($event->entries)) : ?>
                <div class="accordion-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th scope="col"><?= __('Reference Number') ?></th>
                                <th scope="col"><?= __('Entry Name') ?></th>
                                <th scope="col"><?= __('Active') ?></th>
                                <th scope="col"><?= __('Participant Count') ?></th>
                                <th scope="col"><?= __('Checked In Count') ?></th>
                                <th scope="col"><?= __('Created') ?></th>
                                <th scope="col"><?= __('Entry Email') ?></th>
                                <th scope="col" class="actions"><?= __('Actions') ?></th>
                            </tr>
                            <?php foreach ($event->entries as $entries) : ?>
                            <tr>
                                <td><?= h($entries->reference_number) ?></td>
                                <td><?= h($entries->entry_name) ?></td>
                                <td><?= h($entries->active) ?></td>
                                <td><?= h($entries->participant_count) ?></td>
                                <td><?= h($entries->checked_in_count) ?></td>
                                <td><?= h($entries->created) ?></td>
                                <td><?= h($entries->entry_email) ?></td>
                                <td class="actions">
                                    <?= $this->Actions->buttons($entries, ['outline' => true]) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            </div>
        </div>
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseQuestions" aria-expanded="false" aria-controls="collapseQuestions">
                    <?= __('Related Questions') ?>
                </button>
            </h4>
            <div id="collapseQuestions" class="accordion-collapse collapse" data-bs-parent="#accordionRelated">
            <?php if (!empty($event->questions)) : ?>
                <div class="accordion-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th scope="col"><?= __('Question Text') ?></th>
                                <th scope="col"><?= __('Answer Text') ?></th>
                                <th scope="col"><?= __('Created') ?></th>
                                <th scope="col"><?= __('Modified') ?></th>
                                <th scope="col" class="actions"><?= __('Actions') ?></th>
                            </tr>
                            <?php foreach ($event->questions as $questions) : ?>
                            <tr>
                                <td><?= $this->Text->truncate($questions->question_text, 35) ?></td>
                                <td><?= $this->Text->truncate($questions->answer_text, 35) ?></td>
                                <td><?= h($questions->created) ?></td>
                                <td><?= h($questions->modified) ?></td>
                                <td class="actions">
                                    <?= $this->Actions->buttons($questions, ['outline' => true]) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>
