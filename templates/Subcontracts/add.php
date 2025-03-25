<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Subcontract $subcontract
 * @var \Cake\Collection\CollectionInterface|string[] $projects
 * @var \Cake\Collection\CollectionInterface|string[] $companies
 * @var \Cake\Collection\CollectionInterface|string[] $users
 */
?>
<div class="row" style="margin-left: 25px">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Subcontracts'), ['action' => 'index', $project->id], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="subcontracts form content">
            <?= $this->Form->create($subcontract) ?>
            <fieldset>
                <legend><?= __('Add Subcontract') ?></legend>
                <?php
                    echo $this->Form->hidden('project_id', ['options' => $projects]);
                    echo $this->Form->control('initially_contracted_to', ['options' => $companies]);
                    echo $this->Form->control('ultimately_subcontracted_to', ['options' => $users]);
                    echo $this->Form->control('description');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
