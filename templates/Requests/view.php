<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Request $request
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Request'), ['action' => 'edit', $request->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Request'), ['action' => 'delete', $request->id], ['confirm' => __('Are you sure you want to delete # {0}?', $request->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Requests'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Request'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="requests view content">
            <h3><?= h($request->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $request->has('user') ? $this->Html->link($request->user->id, ['controller' => 'Users', 'action' => 'view', $request->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Request Type') ?></th>
                    <td><?= h($request->request_type) ?></td>
                </tr>
                <tr>
                    <th><?= __('Request Text') ?></th>
                    <td><?= h($request->request_text) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($request->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Project Id') ?></th>
                    <td><?= $request->project_id === null ? '' : $this->Number->format($request->project_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created At') ?></th>
                    <td><?= h($request->created_at) ?></td>
                </tr>
                <tr>
                    <th><?= __('Approved At') ?></th>
                    <td><?= h($request->approved_at) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
