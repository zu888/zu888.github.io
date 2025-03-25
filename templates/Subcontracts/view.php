<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Subcontract $subcontract
 */
?>
<div class="row content">
    <div class="column-responsive column-80">
        <div class="subcontracts view">
            <?= $this->Html->link(__('Back to Subcontracts'), ['action' => 'index', $project], ['class' => 'side-nav-item']) ?>

            <h3 class="subcontract-id"><?= h("Subcontract for: ".$subcontract->project->name) ?></h3>
            <table class="table table-bordered subcontract-table" style="background-color: ghostwhite; max-width: 800px">
                <tr>
                    <th><?= __('Project') ?></th>
                    <td><?= $subcontract->has('project') ? $this->Html->link($subcontract->project->name, ['controller' => 'Projects', 'action' => 'view', $subcontract->project->id], ['class' => 'subcontract-link']) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Initially contracted to') ?></th>
                    <td><?= $subcontract->has('company') ? $this->Html->link($subcontract->company->name, ['controller' => 'Companies', 'action' => 'view', $subcontract->company->id], ['class' => 'subcontract-link']) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Ultimately subcontracted to') ?></th>
                    <td><?= $subcontract->has('user') ? $this->Html->link($subcontract->user->first_name." ".$subcontract->user->last_name, ['controller' => 'Users', 'action' => 'view', $subcontract->user->id], ['class' => 'subcontract-link']) : '' ?></td>
                </tr>

            </table>
            <div class="text subcontract-description">
                <strong><?= __('Description') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($subcontract->description)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
