<?php
?>
<div class="row content">
    <div class="column-responsive column-80" style="max-width:800px; margin: auto">
        <div class="documents form content">
            <?= $this->Form->create($companyID) ?>
            <fieldset>
                <legend><?= __("Please Select your company from the list below:") ?></legend>
                <?php

                echo $this->Form->control('company_name', [
                    'options' => $companyNames,
                    'label' => 'Company Name',
                    'empty'=> '-- Select One Please --',
                    'id' => 'company_name',

                ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Send Request'),['style' => 'background-color: #3c8dbc; color: white; width: 100px', 'id' => 'submit', 'disabled'=> true]) ?>
            <?= $this->Form->end()?>

        </div>
    </div>
</div>

<script>
    // Function to enable or disable the submit button based on the selected company
    const enableSubmit = function () {
        const selectedCompany = $('#company_name').val();
        const submitButton = $('#submit');

        if (selectedCompany !== '') {
            submitButton.prop('disabled', false); // Enable the button if a company is selected
        } else {
            submitButton.prop('disabled', true); // Disable the button if no company is selected
        }
    }

    const selectedCompany = $('#company_name'); // Select the company dropdown
    const submitButton = $('#submit');

    selectedCompany.on('change', enableSubmit);

    // Call the function initially to set the initial state of the button
    enableSubmit();


</script>
