<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Equipment> $equipment
 */
?>
<div class="equipment index content">
    <?= $this->Html->link(__('New Equipment'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Equipment') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('equipment_type') ?></th>
                    <th><?= $this->Paginator->sort('is_licensed') ?></th>
                    <th><?= $this->Paginator->sort('hired_from_date') ?></th>
                    <th><?= $this->Paginator->sort('hired_until_date') ?></th>
                    <th><?= $this->Paginator->sort('worker_accessible') ?></th>
                    <th><?= $this->Paginator->sort('related_project_id') ?></th>
                    <th><?= $this->Paginator->sort('related_company_id') ?></th>
                    <th><?= $this->Paginator->sort('related_user_id') ?></th>
                    <th><?= $this->Paginator->sort('auth_type') ?></th>
                    <th><?= $this->Paginator->sort('auth_value') ?></th>
                    <th><?= $this->Paginator->sort('image') ?></th>
                    <th><?= $this->Paginator->sort('image_date') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipment as $equipment): ?>
                <tr>
                    <td><?= $this->Number->format($equipment->id) ?></td>
                    <td><?= h($equipment->name) ?></td>
                    <td><?= h($equipment->equipment_type) ?></td>
                    <td><?= h($equipment->is_licensed) ?></td>
                    <td><?= h($equipment->hired_from_date) ?></td>
                    <td><?= h($equipment->hired_until_date) ?></td>
                    <td><?= $equipment->worker_accessible === null ? '' : $this->Number->format($equipment->worker_accessible) ?></td>
                    <td><?= $equipment->has('project') ? $this->Html->link($equipment->project->name, ['controller' => 'Projects', 'action' => 'view', $equipment->project->id]) : '' ?></td>
                    <td><?= $equipment->has('company') ? $this->Html->link($equipment->company->name, ['controller' => 'Companies', 'action' => 'view', $equipment->company->id]) : '' ?></td>
                    <td><?= $equipment->has('user') ? $this->Html->link($equipment->user->id, ['controller' => 'Users', 'action' => 'view', $equipment->user->id]) : '' ?></td>
                    <td><?= $this->Number->format($equipment->auth_type) ?></td>
                    <td><?= h($equipment->auth_value) ?></td>
                    <td><?= h($equipment->image) ?></td>
                    <td><?= h($equipment->image_date) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $equipment->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $equipment->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $equipment->id], ['confirm' => __('Are you sure you want to delete # {0}?', $equipment->id)]) ?>
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
