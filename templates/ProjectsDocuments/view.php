<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ProjectsDocument $projectsDocument
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Projects Document'), ['action' => 'edit', $projectsDocument->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Projects Document'), ['action' => 'delete', $projectsDocument->id], ['confirm' => __('Are you sure you want to delete # {0}?', $projectsDocument->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Projects Documents'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Projects Document'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="projectsDocuments view content">
            <h3><?= h($projectsDocument->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Project') ?></th>
                    <td><?= $projectsDocument->has('project') ? $this->Html->link($projectsDocument->project->name, ['controller' => 'Projects', 'action' => 'view', $projectsDocument->project->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Document') ?></th>
                    <td><?= $projectsDocument->has('document') ? $this->Html->link($projectsDocument->document->id, ['controller' => 'Documents', 'action' => 'view', $projectsDocument->document->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $projectsDocument->has('user') ? $this->Html->link($projectsDocument->user->id, ['controller' => 'Users', 'action' => 'view', $projectsDocument->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($projectsDocument->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($projectsDocument->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Company Id') ?></th>
                    <td><?= $projectsDocument->company_id === null ? '' : $this->Number->format($projectsDocument->company_id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
