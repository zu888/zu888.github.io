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
                <legend><?= __("Please select the company that you are working for from the list below:") ?></legend>
                <p3 style="font-weight: bold; color: red;"><?= __("If you cannot find your company, please contact the company admin first and try again.") ?></p3>
                <br>
                <br>
                <?php
                echo $this->Form->control('company_name', [
                    'options' => $companyNames,
                    'label' => 'Company Name',
                    'empty'=> '-- Select One Please --',
                    'required'=> true
                ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Next'),['style' => 'background-color: #3c8dbc; color: white; width: 100px']) ?>
            <?= $this->Form->end()?>



        </div>
    </div>
</div>



