<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\UsersAgreement> $usersAgreements
 */
?>
<div class="usersAgreements index content">
    <?= $this->Html->link(__('New Users Agreement'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Users Agreements') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('project_id') ?></th>
                    <th><?= $this->Paginator->sort('document_id') ?></th>
                    <th><?= $this->Paginator->sort('agreed_at') ?></th>
                    <th><?= $this->Paginator->sort('agreement_status') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usersAgreements as $usersAgreement): ?>
                <tr>
                    <td><?= $this->Number->format($usersAgreement->id) ?></td>
                    <td><?= $usersAgreement->has('user') ? $this->Html->link($usersAgreement->user->id, ['controller' => 'Users', 'action' => 'view', $usersAgreement->user->id]) : '' ?></td>
                    <td><?= $usersAgreement->has('project') ? $this->Html->link($usersAgreement->project->name, ['controller' => 'Projects', 'action' => 'view', $usersAgreement->project->id]) : '' ?></td>
                    <td><?= $usersAgreement->has('document') ? $this->Html->link($usersAgreement->document->id, ['controller' => 'Documents', 'action' => 'view', $usersAgreement->document->id]) : '' ?></td>
                    <td><?= h($usersAgreement->agreed_at) ?></td>
                    <td><?= h($usersAgreement->agreement_status) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $usersAgreement->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $usersAgreement->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $usersAgreement->id], ['confirm' => __('Are you sure you want to delete # {0}?', $usersAgreement->id)]) ?>
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
