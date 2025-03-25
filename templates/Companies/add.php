<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company $company
 * @var \Cake\Collection\CollectionInterface|string[] $projects
 * @var \Cake\Collection\CollectionInterface|string[] $users
 */
$currentUser = $this->request->getAttribute('identity');
$role = $currentUser->role;
$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];
?>
<div class="row content">
    <div class="column-responsive column-80">
        <div class="companies form content">
            <?= $this->Form->create($project);
            ?>
            <fieldset>
                <legend><?= __('Add Your Company Details') ?></legend>
                <p>Please enter your business details before continuing to use SiteX.</p>
                <p><span style="color: red">*</span> Required fields</p>
                <br />
                <table class="table table-bordered" style="background-color:ghostwhite;">
                    <div class="row" style="padding-left: 2.5%; padding-right: 2.5%; max-width: 100%">
                    <?php
                    echo $this->Form->hidden('company_type', ['value'=>$role]);
                    echo $this->Form->control('abn', ['label' => 'ABN * (11 Characters Required)', 'required'=> true, 'placeholder'=>'e.g. 123 45 678 912','minlength'=>11, 'maxlength'=>11]);
                    echo $this->Form->control('name',['label' => 'Company Name *','required'=> true,'placeholder'=>'', 'maxlength'=> 100]);
                    echo $this->Form->control('address_no',['label' => 'Street Number *','required'=> true,'placeholder'=>'e.g. 144 or Unit 2/3','maxlength'=>10]);
                    echo $this->Form->control('address_street',['label' => 'Street Name *','required'=> true,'placeholder'=>'e.g. West Road', 'maxlength'=>50]);
                    echo $this->Form->control('address_suburb',['label' => 'Suburb *','required'=> true,'placeholder'=>'e.g. Clayton', 'maxlength'=>50]);
                    echo $this->Form->control('address_state',['label' => 'State *','required'=> true,'options'=>$state, 'empty'=>' '] );
                    echo $this->Form->control('address_postcode',['label' => 'Postcode *','required'=> true,'placeholder'=>'e.g. 3168','minlength'=>4, 'maxlength'=>4]);
                    echo $this->Form->hidden('address_country',['required'=> true,'placeholder'=>'country name [i.e. Australia]', 'default'=> 'Australia']);
                    echo $this->Form->control('contact_name',['label' => 'Company Contact Name *','required'=> true,'placeholder'=>'e.g. Alice Johnson', 'maxlength'=>50]);
                    echo $this->Form->control('contact_email',['label' => 'Company Contact Email *','required'=> true, 'type' => 'email', 'placeholder'=>'e.g. example@gmail.com','maxlength'=>320]);
                    echo $this->Form->control('contact_phone',['label' => 'Company Contact Phone No. *','required'=> true, 'type' => 'tel', 'minlength'=>10, 'placeholder'=>'e.g. 0412 345 678','maxlength'=>10]);
                    ?>
                    <br>
                    <?= $this->Form->button(__('Add Company'), ['style' => 'float: right;']) ?>

                    </div>
                </table>
            </fieldset>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>

    // Get all form fields
    const formFields = document.querySelectorAll("form input, form select");
    formFields.forEach((field) => {
        field.addEventListener("blur", () => {
            // Get field value and validation message
            const value = field.value;
            let validationMessage = "";

            // Validate field value
            switch (field.name) {
                case "abn":
                    if (!/^[0-9]{11}$/.test(value)) {
                        validationMessage = "Please enter a valid 11 digits abn";
                    }
                    break;
                case "address_postcode":
                    if (!/^[0-9]{4}$/.test(value)) {
                        validationMessage = "Please enter a valid 4 digits post code";
                    }
                    break;
                case "contact_email":
                    if (!/\S+@\S+\.\S+/.test(value)) {
                        validationMessage = "Please enter a valid email address";
                    }
                    break;
                case "contact_phone":
                    if (!/^[0-9]{10}$/.test(value)) {
                        validationMessage = "Please enter a valid 10 digits phone number";
                    }
                    break;
                default:
                    break;
            }

            // Remove any existing validation messages and red borders
            const existingMessage = field.parentNode.querySelector(".validation-message");
            if (existingMessage) {
                existingMessage.remove();
            }
            field.classList.remove("invalid");

            // Show validation message if there is one
            if (validationMessage) {
                // Field value is invalid, add red validation message
                const messageElement = document.createElement("div");
                messageElement.classList.add("validation-message");
                messageElement.style.color = "red";
                messageElement.textContent = validationMessage;
                field.parentNode.appendChild(messageElement);
                field.classList.add("invalid");
            }
        });
    });

</script>
