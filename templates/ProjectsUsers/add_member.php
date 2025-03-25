<?php
/**
 * @var \App\View\AppView $this
 */

?>
<div class="row content">
    <div class="column-responsive column-80" style="max-width:800px">
        <div class="documents form content">
            <?= $this->Form->create($userID) ?>
            <fieldset>
                <legend><?= __("Please Select your associated member from the list below:") ?></legend>
                <?php
                echo $this->Form->control('user_name', [
                    'options' => $userNames,
                    'label' => 'User Name',
                    'empty'=> '-- Select One Please --',
                    'required'=> true
                ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Add Member'),['style' => 'background-color: #3c8dbc; color: white; width: 200px']) ?>
            <?= $this->Form->end()?>
            <br>
            <?= $this->Html->link(__('Return to Project List'),['controller'=> 'Projects','action'=>'_index'])?>
        </div>
    </div>
</div>
