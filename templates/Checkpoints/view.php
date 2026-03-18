<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Checkpoint $checkpoint
 * @var \App\Model\Entity\Checkpoint|null $previousCheckpoint
 * @var \Cake\Collection\CollectionInterface<int, \App\Model\Entity\Participant> $betweenParticipants
 * @var int $betweenParticipantCount
 */
?>
<?php $this->extend('/layout/TwitterBootstrap/dashboard'); ?>

<?php $this->start('tb_actions'); ?>
<li><?= $this->Html->link(__('Check In Walkers'), ['action' => 'view', $checkpoint->id, '#' => 'checkpoint-checkin-panel'], ['class' => 'nav-link']) ?></li>
<li><?= $this->Html->link(__('Open Event'), ['controller' => 'Events', 'action' => 'view', $checkpoint->event_id, '#' => 'checkpoints'], ['class' => 'nav-link']) ?></li>
<?php $this->end(); ?>
<?php $this->assign('tb_sidebar', '<ul class="nav flex-column">' . $this->fetch('tb_actions') . '</ul>'); ?>

<section class="checkpoint-dashboard pb-4">
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="text-uppercase small fw-semibold text-secondary mb-2"><?= __('Checkpoint Dashboard') ?></div>
                    <h2 class="display-6 fw-bold mb-2"><?= h($checkpoint->checkpoint_name) ?></h2>
                    <div class="text-secondary mb-3">
                        <?= h($checkpoint->event->event_name) ?>
                        <span class="mx-2">/</span>
                        <?= __('Sequence {0}', $this->Number->format($checkpoint->checkpoint_sequence)) ?>
                        <?php if ($previousCheckpoint !== null) : ?>
                            <span class="mx-2">/</span>
                            <?= __('Previous: {0}', h($previousCheckpoint->checkpoint_name)) ?>
                        <?php else : ?>
                            <span class="mx-2">/</span>
                            <?= __('First checkpoint in event') ?>
                        <?php endif; ?>
                    </div>
                    <p class="lead mb-0">
                        <?php if ($previousCheckpoint !== null) : ?>
                            <?= __('Participants shown here have progressed beyond {0} but have not yet reached this checkpoint.', h($previousCheckpoint->checkpoint_name)) ?>
                        <?php else : ?>
                            <?= __('Participants shown here have not yet reached this checkpoint.') ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <?= $this->element(
                'Checkpoints/checkin_panel',
                [
                    'checkIn' => $checkIn,
                    'checkpoint' => $checkpoint,
                    'participants' => $checkpointParticipants,
                    'selectedEntryId' => $selectedEntryId,
                    'selectedEntryReference' => $selectedEntryReference,
                    'selectedEntryLabel' => $selectedEntryLabel,
                ],
            ) ?>
        </div>
    </div>

    <div class="row g-4 mb-4 align-items-stretch">
        <div class="col-12 col-xl-10">
            <?= $this->element(
                'Checkpoints/count_fragment',
                compact(
                    'checkpoint',
                    'beforeParticipantCount',
                    'betweenParticipantCount',
                    'checkedInHereParticipantCount',
                    'stillWalkingParticipantCount',
                    'checkedOutParticipantCount',
                ),
            ) ?>
        </div>
        <div class="col-12 col-xl-2">
            <div class="d-grid h-100">
                <?= $this->Html->link(
                    __('Refresh Transit Data'),
                    ['action' => 'view', $checkpoint->id],
                    [
                        'class' => 'btn btn-outline-primary h-100 d-flex align-items-center justify-content-center',
                        'data-checkpoint-refresh' => $this->Url->build(['action' => 'view', $checkpoint->id]),
                    ],
                ) ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <?= $this->element(
                'Checkpoints/table_fragment',
                compact('checkpoint', 'previousCheckpoint', 'betweenParticipants', 'betweenParticipantCount'),
            ) ?>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header"><?= __('Checkpoint Details') ?></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5"><?= __('Event') ?></dt>
                        <dd class="col-sm-7"><?= $this->Html->link($checkpoint->event->event_name, ['controller' => 'Events', 'action' => 'view', $checkpoint->event->id]) ?></dd>

                        <dt class="col-sm-5"><?= __('Sequence') ?></dt>
                        <dd class="col-sm-7"><?= $this->Number->format($checkpoint->checkpoint_sequence) ?></dd>

                        <dt class="col-sm-5"><?= __('Previous') ?></dt>
                        <dd class="col-sm-7"><?= h($previousCheckpoint?->checkpoint_name ?? __('None')) ?></dd>

                        <dt class="col-sm-5"><?= __('Created') ?></dt>
                        <dd class="col-sm-7"><?= h($checkpoint->created) ?></dd>

                        <dt class="col-sm-5"><?= __('Modified') ?></dt>
                        <dd class="col-sm-7"><?= h($checkpoint->modified) ?></dd>
                    </dl>
                </div>
            </div>

            <?= $this->element('Checkpoints/recent_fragment', compact('checkpoint')) ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const refreshButton = document.querySelector('[data-checkpoint-refresh]');
    if (!(refreshButton instanceof HTMLAnchorElement)) {
        return;
    }

    const refreshFragments = async function () {
        const baseUrl = refreshButton.dataset.checkpointRefresh;
        if (!baseUrl) {
            return;
        }

        refreshButton.classList.add('disabled');

        const fragments = [
            {name: 'count', id: 'checkpoint-between-count'},
            {name: 'table', id: 'checkpoint-between-table'},
            {name: 'recent', id: 'checkpoint-recent-checkins'},
        ];

        try {
            await Promise.all(fragments.map(async function (fragment) {
                const url = new URL(baseUrl, window.location.origin);
                url.searchParams.set('fragment', fragment.name);

                const response = await fetch(url.toString(), {
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                });
                if (!response.ok) {
                    throw new Error('Refresh failed');
                }

                const html = await response.text();
                const documentFragment = new DOMParser().parseFromString(html, 'text/html');
                const replacement = documentFragment.getElementById(fragment.id);
                const current = document.getElementById(fragment.id);

                if (!replacement || !current) {
                    throw new Error('Replacement fragment missing');
                }

                current.replaceWith(replacement);
            }));
        } catch (error) {
            window.location.assign(baseUrl);
        } finally {
            refreshButton.classList.remove('disabled');
        }
    };

    const refreshCheckInPanel = async function (entryReference = '') {
        const panel = document.getElementById('checkpoint-checkin-panel');
        if (!panel) {
            return;
        }

        const form = panel.querySelector('[data-checkpoint-checkin-form]');
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const panelUrl = form.dataset.panelUrl;
        if (!panelUrl) {
            return;
        }

        const url = new URL(panelUrl, window.location.origin);
        url.searchParams.set('fragment', 'panel');
        if (entryReference) {
            url.searchParams.set('entry_reference', entryReference);
        }

        const response = await fetch(url.toString(), {
            headers: {'X-Requested-With': 'XMLHttpRequest'},
        });
        if (!response.ok) {
            throw new Error('Panel refresh failed');
        }

        const html = await response.text();
        const documentFragment = new DOMParser().parseFromString(html, 'text/html');
        const replacement = documentFragment.getElementById('checkpoint-checkin-panel');
        if (!replacement) {
            throw new Error('Replacement panel missing');
        }

        panel.replaceWith(replacement);
    };

    refreshButton.addEventListener('click', function (event) {
        event.preventDefault();
        void refreshFragments();
    });

    document.addEventListener('change', function (event) {
        const input = event.target.closest('[data-checkpoint-entry-reference]');
        if (!(input instanceof HTMLInputElement)) {
            return;
        }

        void refreshCheckInPanel(input.value);
    });

    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-checkpoint-entry-load]');
        if (!(button instanceof HTMLButtonElement)) {
            return;
        }

        const panel = button.closest('#checkpoint-checkin-panel');
        if (!panel) {
            return;
        }

        const input = panel.querySelector('[data-checkpoint-entry-reference]');
        if (!(input instanceof HTMLInputElement)) {
            return;
        }

        void refreshCheckInPanel(input.value);
    });

    document.addEventListener('keydown', function (event) {
        const input = event.target.closest('[data-checkpoint-entry-reference]');
        if (!(input instanceof HTMLInputElement) || event.key !== 'Enter') {
            return;
        }

        event.preventDefault();
        void refreshCheckInPanel(input.value);
    });

    document.addEventListener('submit', function (event) {
        const form = event.target.closest('[data-checkpoint-checkin-form]');
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        event.preventDefault();
        const formData = new FormData(form);
        const selectedEntryReference = String(formData.get('entry_reference') ?? '');

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {'X-Requested-With': 'XMLHttpRequest'},
        }).then(async function (response) {
            if (!response.ok) {
                throw new Error('Save failed');
            }

            await Promise.all([
                refreshFragments(),
                refreshCheckInPanel(selectedEntryReference),
            ]);
        }).catch(function () {
            window.location.assign(form.action.replace(/\.json$/, ''));
        });
    });
});
</script>
