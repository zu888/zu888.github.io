<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company $company
 * @var string[]|\Cake\Collection\CollectionInterface $projects
 * @var string[]|\Cake\Collection\CollectionInterface $users
 * @var string[]|\Cake\Collection\CollectionInterface $companies
 *
 */
$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];
$currentUser = $this->request->getAttribute('identity');

?>
<div class="row content">
    <div class="column-responsive column-80">
        <?php if($currentUser->role=="Admin"){?>
        <div class="companies edit content">
            <h3>
                <?= __('Actions') ?>
            </h3>
            <div style="display: flex;">
            <a class="btn btn-block btn-primary" style="width: 130px ;margin-right: 5px" href="<?= $this->Url->build(
                ['controller' => 'Companies', 'action' => 'index']) ?>">List Companies</a>

                    <a class="btn btn-block btn-primary" style="width: 130px" href="<?= $this->Url->build(
                ['controller' => 'Companies', 'action' => 'delete',$company->id],['confirm' => __('Are you sure you want to delete # {0}?', $company->id) ]) ?>">Delete</a>
                <?php }  ?>
            </div>


    <div class="column-responsive column-80">
        <div class="companies form content">
            <?= $this->Form->create($company) ?>
            <fieldset>
                <legend><?= __('Edit Company') ?></legend>
                <table class="table table-bordered" style="background-color:ghostwhite; padding-left: 2.5%; padding-right: 2.5%; max-width: 100%">
                <?php
                echo $this->Form->control('name',['required'=> true,'placeholder'=>'the company name [i.e. aCompany]', 'maxlength'=> 100]);
                echo $this->Form->control('address_no',['required'=> true,'placeholder'=>'address line [i.e. 145 clayton rd]','maxlength'=>10]);
                echo $this->Form->control('address_street',['required'=> true,'placeholder'=>'street name [i.e. clayton road]', 'maxlength'=>50]);
                echo $this->Form->control('address_suburb',['required'=> true,'placeholder'=>'suburb [i.e. Clayton]', 'maxlength'=>50]);
                echo $this->Form->control('address_state',['required'=> true,'options'=>$state, 'empty'=>' '] );
                echo $this->Form->control('address_postcode',['required'=> true,'placeholder'=>'post code no. [i.e. 3168]','minlength'=>4, 'maxlength'=>4]);
                echo $this->Form->hidden('address_country',['required'=> true,'placeholder'=>'country name [i.e. Australia]', 'default'=> 'Australia']);
                echo $this->Form->control('contact_name',['required'=> true,'placeholder'=>'full name [i.e. Alice]', 'maxlength'=>50]);
                echo $this->Form->control('contact_email',['required'=> true, 'type' => 'email', 'placeholder'=>'your email address i.e. ykuu0005@gmail.com','maxlength'=>320]);
                echo $this->Form->control('contact_phone',['required'=> true, 'type' => 'tel', 'minlength'=>10, 'placeholder'=>'10 digits Australian phone number [i.e. 0452611111]','maxlength'=>10]);
                ?>
                    </table>
                    </fieldset>
                        <?= $this->Form->button(__('Submit'), ['style' => 'float: right;']) ?>
                        <?= $this->Form->end() ?>
                </div>
            </div>
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
