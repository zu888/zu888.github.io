<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project $project
 * @var  $countries
 * @var \Cake\Collection\CollectionInterface|string[] $users
 * @var \Cake\Collection\CollectionInterface|string[] $companies
 * @var \Cake\Collection\CollectionInterface|string[] $documents
 * @var \Cake\Collection\CollectionInterface|string[] $state
 */

use Cake\Http\Client;
$currentUser = $this->request->getAttribute('identity');

?>




<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.js" integrity="sha512-+UiyfI4KyV1uypmEqz9cOIJNwye+u+S58/hSwKEAeUMViTTqM9/L4lqu8UxJzhmzGpms8PzFJDzEqXL9niHyjA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="row content">
    <div class="column-responsive column-80" style="padding-left: 2.5%; padding-right: 2.5%; max-width: 100%">
        <div class="projects form">
            <?= $this->Form->create($project);
            ?>
            <fieldset>
                <legend><?= __('Add New Project') ?></legend>
                <p><span style="color: red">*</span> Required fields</p>
                <?php
                echo $this->Form->control('name', ['label' => 'Project Name *','placeholder'=>'']);
                echo $this->Form->control('permit_no', ['label' => 'Permit Number *','placeholder'=>'e.g. COS123456','maxlength'=>50]);
                echo $this->Form->control('client_name', ['label' => 'Client Name *','required'=> true ,'placeholder'=>'e.g. Alice Johnson', 'maxlength'=>50]);
                echo $this->Form->control('client_email' , ['label' => 'Client Email Address *', 'type' => 'email', 'required'=> true ,'placeholder'=>'e.g. example@gmail.com', 'maxlength'=>320]);
                echo $this->Form->control('client_phone', ['label' => 'Client Phone Number *', 'type' => 'tel', 'required'=> true , 'pattern' => '^[0-9]{10}$', 'placeholder'=>'e.g. 0412 345 678', 'data-validation' => 'Please enter a valid phone number', 'maxlength'=>10]);
                echo $this->Form->control('surveyor_name', ['label' => 'Surveyor Name *', 'required'=> true ,'placeholder'=>'e.g. Will Smith', 'maxlength'=>50]);
                echo $this->Form->control('surveyor_email', ['label' => 'Surveyor Email Address *','type' => 'email', 'required'=> true ,'placeholder'=>'e.g. example@gmail.com', 'maxlength'=>320]);
                echo $this->Form->control('surveyor_phone', ['label' => 'Surveyor Phone Number *', 'type' => 'tel', 'required'=> true , 'pattern' => '^[0-9]{10}$', 'placeholder'=>'e.g. 0412 345 678','lessThanOrEqual'=>9999999999,'maxlength'=>10]);
                echo $this->Form->control('address_no', ['label' => 'Street Number *', 'required'=> true , 'placeholder'=>'e.g. 144 or Unit 2/3','maxlength'=>10]);
                echo $this->Form->control('address_street',['label' => 'Street Name *', 'required'=> true ,'placeholder'=>'e.g. West Road', 'maxlength'=>50]);
                echo $this->Form->control('address_suburb', ['label' => 'Suburb *', 'required'=> true , 'placeholder'=>'e.g. Clayton','maxlength'=>50]);
                echo $this->Form->control('address_state', ['label' => 'State *', 'required'=> true , 'options'=>$state, 'empty'=>' ']);
                echo $this->Form->control('address_postcode' ,['label' => 'Postcode *', 'required'=> true , 'placeholder'=>'e.g. 3168','maxlength'=>4 ,'minlength'=>4]);
                echo $this->Form->hidden('address_country', ['default'=> 'Australia']);
                echo $this->Form->control('start_date', ['type' => 'text','placeholder'=>'Click to select the start date (optional)']);
                echo $this->Form->control('est_completion_date', ['label' => 'Estimated Completion Date', 'type' => 'text','placeholder'=>'Click to select the completion date (optional)']);
                //echo $this->Form->select('Assign Builder', $companies,  ['label' => 'Assign a builder to the project', 'required'=> false, 'empty' => 'Choose a builder']); TODO: Leave this until we can assign builder/company properly
                ?>
                <?php if($currentUser->role == 'Builder'){echo $this->Form->control('Builder', ['type' => 'text', 'default' => 'You are the builder of this project', 'readonly' => true]);}?>
            </fieldset>
            <?= $this->Form->button(__('Submit'), ['onclick' => 'this.form.submit(); this.disabled=true', 'style' => 'float: right;']) ?>

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
                case "address_postcode":
                    if (!/^[0-9]{4}$/.test(value)) {
                        validationMessage = "Please enter a valid 4 digits post code";
                    }
                    break;
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
        jQuery('#start-date').datetimepicker({
            onShow: function (ct) {
                this.setOptions({
                    maxDate: jQuery('#est-completion-date').val() ? jQuery('#est-completion-date').val() : false,
                    format:'Y-m-d'
                })
            },
            timepicker: false
        });
        jQuery('#est-completion-date').datetimepicker({
            onShow: function (ct) {
                this.setOptions({
                    minDate: jQuery('#start-date').val() ? jQuery('#start-date').val() : false,
                    format:'Y-m-d'
                })
            },
            timepicker: false
        });
    });

    let country_select = $('select#address-country');
    $.getJSON("https://restcountries.com/v3.1/all", function( data ) {
        var items = [];
        $.each( data, function( key, val ) {
            items.push( {id: val.name.common, text: val.name.common} );
        });
        // country_select.append(items.join(""));
        $("select#address-country").select2({
            data: items
        });
    });
</script>
