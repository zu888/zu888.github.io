<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project $project
 * @var string[]|\Cake\Collection\CollectionInterface $users
 * @var string[]|\Cake\Collection\CollectionInterface $companies
 * @var string[]|\Cake\Collection\CollectionInterface $documents
 */
$currentUser = $this->request->getAttribute('identity');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.js" integrity="sha512-+UiyfI4KyV1uypmEqz9cOIJNwye+u+S58/hSwKEAeUMViTTqM9/L4lqu8UxJzhmzGpms8PzFJDzEqXL9niHyjA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<div class="row content">
    <div class="column-responsive column-80" style="padding-left: 2.5%; padding-right: 2.5%; max-width: 100%x">
        <?= $this->Form->create($project) ?>
        <fieldset>
            <legend><?= __('Edit Project: '.$project->name) ?></legend>
            <?php
                echo $this->Form->control('name', ['label' => 'Project Name', 'required'=> true , 'maxlength'=>50]);
                echo $this->Form->control('permit_no', ['label' => 'Permit Number', 'required'=> true , 'maxlength'=>50]);
                echo $this->Form->control('client_name', ['required'=> true , 'maxlength'=>50]);
                echo $this->Form->control('client_email', ['required'=> true , 'type' => 'email', 'maxlength'=>320]);
                echo $this->Form->control('client_phone', ['required'=> true , 'type' => 'tel', 'lessThanOrEqual'=>9999999999,'maxlength'=>10]);
                echo $this->Form->control('surveyor_name', ['required'=> true , 'maxlength'=>50]);
                echo $this->Form->control('surveyor_email' , ['required'=> true , 'type' => 'email', 'maxlength'=>320]);
                echo $this->Form->control('surveyor_phone', ['required'=> true , 'type' => 'tel', 'data-validation' => 'Please enter a valid phone number', 'lessThanOrEqual'=>9999999999,'maxlength'=>10]);
                echo $this->Form->control('est_completion_date', ['type' => 'text', 'value' => '', 'autocomplete' => 'off', 'label' => 'Est. Completion Date (Currently '.$project->est_completion_date.')']);
            ?>
            <?php if($currentUser->role == 'Builder'){echo $this->Form->control('Builder', ['type' => 'text', 'default' => 'You are the builder of this project', 'readonly' => true]);}?>

        </fieldset>
        <?= $this->Form->button(__('Submit'), ['style' => 'float: right;']) ?>
        <?= $this->Form->end() ?>
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
                case "client_email":
                    if (!/\S+@\S+\.\S+/.test(value)) {
                        validationMessage = "Please enter a valid email address";
                    }
                    break;
                case "client_phone":
                    if (!/^[0-9]{10}$/.test(value)) {
                        validationMessage = "Please enter a valid 10 digits phone number";
                    }
                    break;
                case "surveyor_email":
                    if (!/\S+@\S+\.\S+/.test(value)) {
                        validationMessage = "Please enter a valid email address";
                    }
                    break;
                case "surveyor_phone":
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

        jQuery(function () {
            jQuery('#est-completion-date').datetimepicker({
                onShow: function (ct) {
                    this.setOptions({
                        startDate:'+1970/01/01',
                        minDate:'+1970/01/01',
                        format:'d-m-Y'
                    })
                },
                timepicker: false
            });
        });
</script>
