<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ProjectsUser $projectsUser
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Projects User'), ['action' => 'edit', $projectsUser->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Projects User'), ['action' => 'delete', $projectsUser->id], ['confirm' => __('Are you sure you want to delete # {0}?', $projectsUser->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Projects Users'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Projects User'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="projectsUsers view content">
            <h3><?= h($projectsUser->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Project') ?></th>
                    <td><?= $projectsUser->has('project') ? $this->Html->link($projectsUser->project->name, ['controller' => 'Projects', 'action' => 'view', $projectsUser->project->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $projectsUser->has('user') ? $this->Html->link($projectsUser->user->id, ['controller' => 'Users', 'action' => 'view', $projectsUser->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($projectsUser->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
