<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ProjectsDocument> $projectsDocuments
 */
?>
<div class="projectsDocuments index content">
    <?= $this->Html->link(__('New Projects Document'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Projects Documents') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('project_id') ?></th>
                    <th><?= $this->Paginator->sort('document_id') ?></th>
                    <th><?= $this->Paginator->sort('company_id') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projectsDocuments as $projectsDocument): ?>
                <tr>
                    <td><?= $this->Number->format($projectsDocument->id) ?></td>
                    <td><?= $projectsDocument->has('project') ? $this->Html->link($projectsDocument->project->name, ['controller' => 'Projects', 'action' => 'view', $projectsDocument->project->id]) : '' ?></td>
                    <td><?= $projectsDocument->has('document') ? $this->Html->link($projectsDocument->document->id, ['controller' => 'Documents', 'action' => 'view', $projectsDocument->document->id]) : '' ?></td>
                    <td><?= $projectsDocument->company_id === null ? '' : $this->Number->format($projectsDocument->company_id) ?></td>
                    <td><?= $projectsDocument->has('user') ? $this->Html->link($projectsDocument->user->id, ['controller' => 'Users', 'action' => 'view', $projectsDocument->user->id]) : '' ?></td>
                    <td><?= h($projectsDocument->status) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $projectsDocument->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $projectsDocument->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $projectsDocument->id], ['confirm' => __('Are you sure you want to delete # {0}?', $projectsDocument->id)]) ?>
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
