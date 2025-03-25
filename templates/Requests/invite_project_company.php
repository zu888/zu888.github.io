<?php
/**
 * @var \App\View\AppView $this
 */

?>

<?php echo $this->Html->css('main'); ?>

<div class="row content">
    <div class="column-responsive column-80 boxed-content" style="margin: auto">
        <div class="documents form content">
            <?= $this->Form->create($companyID) ?>
            <fieldset>
                <legend><?= __("Please select your associated company from the list below:") ?></legend>
                <?php
                echo $this->Form->control('company_name', [
                    'options' => $companyNames,
                    'label' => 'Company Name',
                    'empty'=> '-- Select One Please --',
                    'required'=> true
                ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Send invitation'),['style' => 'background-color: #3c8dbc; color: white; width: 200px']) ?>
            <?= $this->Form->end()?>
            <br>
            <?= $this->Html->link(__('Return to Project List'),['controller'=> 'Projects','action'=>'_index'])?>
        </div>
    </div>
</div>

