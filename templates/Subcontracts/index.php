<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Subcontract> $subcontracts
 */
?>
<div class="row content">
    <div class="column-responsive column-80">
        <div class="subcontracts index content">
            <div style="font-size: 18px;">
                <?= $this->Html->link(__('Return to Project Details'), ['controller' => 'Projects', 'action' => 'view', $project->id], ['class' => 'button float-right']) ?>
                <br><br>
                <?= $this->Html->link(__('New Subcontract'), ['action' => 'add', '?' => ['project_id' => $project->id]], ['class' => 'button float-right']) ?>
            </div>
            <h3><?= __('Subcontracts for project: ') . $project->name ?></h3>
            <div class="table-responsive">
                <table class="table table-bordered"style="background: whitesmoke">
                    <thead>
                    <tr>

                        <th><?= $this->Paginator->sort('description') ?></th>
                        <th><?= $this->Paginator->sort('initially_contracted to') ?></th>
                        <th><?= $this->Paginator->sort('ultimately_subcontracted_to') ?></th>
                        <th class="actions"><?= __('Actions') ?></th>
                    </tr>

                    </thead>
                    <tbody>
                    <?php foreach ($subcontracts as $subcontract): ?>
                        <tr>

                            <td><?= h($subcontract->description) ?></td>
                            <td><?= $subcontract->has('company') ? $this->Html->link($subcontract->company->name, ['controller' => 'Companies', 'action' => 'view', $subcontract->company->id]) : '' ?></td>
                            <td><?= $subcontract->has('user') ? $this->Html->link($subcontract->user->first_name ." ". $subcontract->user->last_name, ['controller' => 'Users', 'action' => 'view', $subcontract->user->id]) : '' ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['action' => 'view',$subcontract->id, '?' => ['project_id' => $project->id]]) ?>
                                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $subcontract->id, '?' => ['projectId' => $project->id]]) ?>
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
            </div>
        </div>
    </div>
</div>
