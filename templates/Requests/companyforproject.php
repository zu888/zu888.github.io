<?php
?>
<div class="row content">
    <div class="column-responsive column-80" style="max-width:800px; margin: auto">
        <div class="documents form content">
            <?= $this->Form->create($companyID) ?>
            <fieldset>
                <legend><?= __("Please select the engaged company for this worker from the list below:") ?></legend>
                <?php

                echo $this->Form->control('company_name', [
                    'options' => $companyNames,
                    'label' => 'Company Name',
                    'empty'=> '-- Select One Please --',
                    'required'=> true


                ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Send Invitation'),['style' => 'background-color: #3c8dbc; color: white; width: 150px']) ?>
            <?= $this->Form->end()?>

        </div>
    </div>
</div>
