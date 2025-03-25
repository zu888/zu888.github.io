<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\UsersAgreement $usersAgreement
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Users Agreement'), ['action' => 'edit', $usersAgreement->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Users Agreement'), ['action' => 'delete', $usersAgreement->id], ['confirm' => __('Are you sure you want to delete # {0}?', $usersAgreement->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Users Agreements'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Users Agreement'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="usersAgreements view content">
            <h3><?= h($usersAgreement->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $usersAgreement->has('user') ? $this->Html->link($usersAgreement->user->id, ['controller' => 'Users', 'action' => 'view', $usersAgreement->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Project') ?></th>
                    <td><?= $usersAgreement->has('project') ? $this->Html->link($usersAgreement->project->name, ['controller' => 'Projects', 'action' => 'view', $usersAgreement->project->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Document') ?></th>
                    <td><?= $usersAgreement->has('document') ? $this->Html->link($usersAgreement->document->id, ['controller' => 'Documents', 'action' => 'view', $usersAgreement->document->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($usersAgreement->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Agreed At') ?></th>
                    <td><?= h($usersAgreement->agreed_at) ?></td>
                </tr>
                <tr>
                    <th><?= __('Agreement Status') ?></th>
                    <td><?= $usersAgreement->agreement_status ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
