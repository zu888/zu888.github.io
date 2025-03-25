<?php
/**
 * @var \App\View\AppView $this
 */

?>

<?php echo $this->Html->css('main'); ?>

<div class="row content">
    <div class="column-responsive column-80 boxed-content" style="margin: auto">
        <div class="documents form content">
            <?= $this->Form->create($userID) ?>
            <fieldset>
                <legend><?= __("Please select your associated worker from the list below:") ?></legend>
                <?php
                echo $this->Form->control('user_name', [
                    'options' => $userNames,
                    'label' => 'User Name',
                    'empty'=> '-- Select One Please --',
                    'required'=> true
                ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Next'),['style' => 'background-color: #3c8dbc; color: white; width: 100px']) ?>
            <?= $this->Form->end()?>
            <br>
            <?= $this->Html->link(__('Return to Project List'),['controller'=> 'Projects','action'=>'_index'])?>
        </div>
    </div>
</div>
