<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company[]|\Cake\Collection\CollectionInterface $companies
 * @var \App\Model\Entity\User $user
 * @var \Cake\Collection\CollectionInterface|string[] $state
 */



$role = $this->request->getQuery('role');
if (!$role) {
    $role = 'Employee';
}
if (!$role) { ?>

    <body class="hold-transition register-page">
        <div class="register-body content">
            <h3>Please select the type of account to create:</h3>
            <p>If you plan to use SiteX as the owner or administrator of a business, please choose your business type:</p>
            <a class="btn btn-block btn-primary" style="width: 200px" href="?role=Builder">Builder</a><br />
            <a class="btn btn-block btn-primary" style="width: 200px" href="?role=Contractor">Contractor</a>
            <br /><br />
            <p>If you are an employee or subcontractor, choose this option:</p>
            <a class="btn btn-block btn-primary" style="width: 200px" href="?role=Employee">Employee / Subcontractor</a>
        </div>

    <?php } else { ?>
        <link rel="stylesheet" type="text/css" href="<?= $this->Url->build(DS . 'css' . DS . 'amsify.suggestags.css') ?>">
        <script type="text/javascript" src="<?= $this->Url->build(DS . 'js' . DS . 'jquery.amsify.suggestags.js') ?>"></script>
        <style>
            .column {
                float: left;
                width: 50%;
                max-width: 400px;
            }
        </style>
        <div>
            <div class="users form content">
                <h3>Register a New Account</h3>

                <?= $this->fetch('content'); ?>
                <?= $this->Form->create($user, ['onSubmit' => 'disableButton()', 'enctype' => "multipart/form-data"]) ?>
                <fieldset>
                    <legend></legend>
                    <p><span style="color: red">*</span> Required fields</p>

                    <div class="row" style="padding-left: 2.5%; padding-right: 2.5%; max-width: 100%">
                        <input type="hidden" name="role" id="role" value="<?= $role ?>">
                        <?php
                        echo $this->Form->control('email', ['label' => 'Email Address *', 'type' => 'email', 'required' => true, 'placeholder' => 'e.g. contractor@example.com', 'maxlength' => 320]);
                        echo $this->Form->control('password', ['label' => 'Password (Minimum 8 characters) *', 'required' => true, 'placeholder' => 'Please enter a password with more than 8 characters or numbers ', 'minlength' => 8]);
                        echo $this->Form->control('first_name', ['label' => 'First Name *', 'required' => true, 'placeholder' => 'e.g. Michael']);
                        echo $this->Form->control('last_name', ['label' => 'Last Name *', 'required' => true, 'placeholder' => 'e.g. Kay']);
                        echo $this->Form->control('phone_mobile', ['label' => 'Mobile Phone Number *', 'type' => 'tel', 'required' => true, 'placeholder' => 'e.g. 0412 345 678', 'minlength' => 10, 'maxlength' => 10]);
                        echo $this->Form->control('phone_office', ['label' => 'Office Phone Number', 'type' => 'tel', 'required' => false, 'placeholder' => 'e.g. 03 1111 8888', 'minlength' => 10, 'maxlength' => 10]);
                        echo $this->Form->control('address_no', ['label' => 'Street Number *', 'placeholder' => 'e.g 144 or Unit 2/3', 'required' => true, 'maxlength' => 10]);
                        echo $this->Form->control('address_street', ['label' => 'Street Name *', 'required' => true, 'placeholder' => 'e.g. West Rd']);
                        echo $this->Form->control('address_suburb', ['label' => 'Suburb *', 'required' => true, 'placeholder' => 'e.g. Clayton']);
                        echo $this->Form->control('address_state', ['label' => 'State *', 'required' => true, 'options' => $state, 'empty' => ' ']);
                        echo $this->Form->control('address_postcode', ['label' => 'Postcode *', 'required' => true, 'placeholder' => 'e.g. 3000', 'maxlength' => 4, 'minlength' => 4]);
                        echo $this->Form->hidden('address_country', ['required' => true, 'placeholder' => 'e.g Australia', 'default' => 'Australia']);
                        echo $this->Form->control('emergency_name', ['label' => 'Emergency Contact Name *', 'required' => true, 'placeholder' => 'e.g. Kate']);
                        echo $this->Form->control('emergency_relationship', ['label' => 'Emergency Contact Relationship *', 'required' => true, 'placeholder' => 'e.g. Parent']);
                        echo $this->Form->control('emergency_phone', ['label' => 'Emergency Contact Phone *', 'required' => true, 'type' => 'tel', 'placeholder' => 'e.g. 0412 345 678', 'minlength' => 10, 'maxlength' => 10]);
                        echo $this->Form->label('image', 'Profile Picture (Only JPEG, PNG and GIF image types are accepted)');
                        echo '<br>';
                        echo $this->Form->control('img', [
                            'type' => 'file',
                            'label' => false,
                            'accept' => 'image/jpeg, image/png, image/gif, image/jpg',
                        ]);
                        echo $this->Form->label('role', 'Select your role: (If you are a Builder, please select contractor first) *');
                        echo $this->Form->select('role', [
                            'Contractor' => 'Contractor',
//                            'Subcontractor' => 'Subcontractor',
                            'On-site Worker' => 'On-site Worker',
                            'Consultant' => 'Consultant',
                            'Visitor' => 'Visitor',
                            'Client' => 'Client',
                        ], [
                            'default' => 'Visitor',
                            'class' => 'form-control',
                            'required' => true,
                        ]);
                        ?>
                        <?= $this->Form->button('Sign Up', ['id' => 'btn', 'style' => 'float: right;']) ?>


                    </div>
                </fieldset>
                <br />
                <?= $this->Form->end() ?>


                <a href="<?= $this->Url->build(['action' => 'login']) ?> " style="padding-left: 2%;">Return to Login</a><br>
                <br />
            </div>
        </div>

        <?php echo $this->Html->script('AdminLTE./bower_components/bootstrap/dist/js/bootstrap.min'); ?>
        <?php echo $this->Html->script('AdminLTE./plugins/iCheck/icheck.min'); ?>
        <?php echo $this->fetch('script'); ?>
        <?php echo $this->fetch('scriptBottom'); ?>

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
                        case "email":
                            if (!/\S+@\S+\.\S+/.test(value)) {
                                validationMessage = "Please enter a valid email address";
                            }
                            break;
                        case "address_postcode":
                            if (!/^[0-9]{4}$/.test(value)) {
                                validationMessage = "Please enter a valid 4 digits post code";
                            }
                            break;
                        case "phone_mobile":
                            if (!/^[0-9]{10}$/.test(value)) {
                                validationMessage = "Please enter a valid 10 digits phone number";
                            }
                            break;
                        case "phone_office":
                            if (!/^[0-9]{10}$/.test(value)) {
                                validationMessage = "Please enter a valid 10 digits phone number";
                            }
                            break;
                        case "emergency_phone":
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


            $(function() {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%' /* optional */
                });
            });

            function disableButton() {
                let btn = document.getElementById('btn');
                btn.disabled = true;
            }
        </script>
    <?php } ?>
