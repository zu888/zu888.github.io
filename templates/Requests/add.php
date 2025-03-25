<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Request $request
 * @var \Cake\Collection\CollectionInterface|string[] $users
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Requests'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="requests form content">
            <?= $this->Form->create($request) ?>
            <fieldset>
                <legend><?= __('Add Request') ?></legend>
                <?php
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('project_id');
                    echo $this->Form->control('request_type');
                    echo $this->Form->control('request_text');
                    echo $this->Form->control('created_at', ['empty' => true]);
                    echo $this->Form->control('approved_at', ['empty' => true]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
