<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company $company
 * @var \App\Model\Entity\CompaniesProject $companiesProject
 */

?>
<div class="row content">
    <div class="column-responsive column-80" style="max-width:800px">
        <div class="documents form content">
            <?= $this->Form->create($companyID) ?>
            <fieldset>
                <legend><?= __("Please Select your associated company from the list below:") ?></legend>
                <?php

                echo $this->Form->control('company_name', [
                    'options' => $companyNames,
                    'label' => 'Company Name',
                    'empty'=> '-- Select One Please --'
                ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Add Associated Company'),['style' => 'background-color: #3c8dbc; color: white; width: 200px']) ?>
            <?= $this->Form->end()?>
            <br>
            <?= $this->Html->link(__('Return to Project List'),['controller'=> 'Projects','action'=>'_index'])?>

        </div>
    </div>
</div>


