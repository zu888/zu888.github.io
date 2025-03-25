<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string[]|\Cake\Collection\CollectionInterface $companies
 * @var string[]|\Cake\Collection\CollectionInterface $documents
 * @var \Cake\Collection\CollectionInterface|string[] $state
 * @var \Cake\Collection\CollectionInterface|string $chosenState
 */
$currentUser = $this->request->getAttribute('identity');

$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.js" integrity="sha512-+UiyfI4KyV1uypmEqz9cOIJNwye+u+S58/hSwKEAeUMViTTqM9/L4lqu8UxJzhmzGpms8PzFJDzEqXL9niHyjA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="row content">
    <div class="column-responsive column-80" style="background-color:ghostwhite; padding-left: 2.5%; padding-right: 2.5%; max-width: 100%">
        <div class="users form content">
<!--            ['id' => 'user-form']-->
            <?= $this->Form->create($user,['id'=>'user-form','enctype'=>"multipart/form-data"]) ?>
            <fieldset>
                <legend><?= __('Edit Your Profile') ?></legend>
                <?php
                    echo $this->Form->control('address_no', ['required' => true]);
                    echo $this->Form->control('address_street', ['required' => true]);
                    echo $this->Form->control('address_suburb', ['required' => true]);
                    echo $this->Form->control('address_state', ['required' => true, 'options'=>$state, 'default'=>$chosenState]);
                    echo $this->Form->control('address_postcode', ['required' => true]);
                    echo $this->Form->hidden('address_country', ['required' => true, 'type' => 'select']);
                    echo $this->Form->control('phone_mobile', ['required' => true, 'label' => 'Mobile Phone', 'type' => 'tel','minlength' => 10, 'maxlength' => 10]);
                    echo $this->Form->control('phone_office', ['required' => true, 'label' => 'Office Phone', 'type' => 'tel','minlength' => 10, 'maxlength' => 10]);
                    echo $this->Form->control('emergency_name', ['required' => true, 'label' => 'Emergency Contact Name']);
                    echo $this->Form->control('emergency_relationship', ['required' => true, 'label' => 'Emergency Contact Relationship']);
                    echo $this->Form->control('emergency_phone', ['required' => true, 'label' => 'Emergency Contact Phone', 'type' => 'tel','minlength' => 10, 'maxlength' => 10]);

                    echo $this->Form->label('image', 'Profile Picture (Only JPEG, PNG and GIF image types are accepted)');
                    echo '<br>';
                    echo $this->Form->control('img', ['type'=>'file',
                        'label'=>false,
                        'accept' => 'image/jpeg, image/png, image/gif, image/jpg']);

                    if ($currentUser->role != 'Admin'){
                        echo $this->Form->label('role', 'Select a basic Role:');
                        echo $this->Form->select('role', [
                            'Contractor' => 'Contractor',
                            'Subcontractor' => 'Subcontractor',
                            'On-site Worker' => 'On-site Worker',
                            'Consultant' => 'Consultant',
                            'Visitor' => 'Visitor',
                            'Client' => 'Client'
                        ],[
                            'class' => 'form-control'
                        ]);
                    }
                ?>
            </fieldset>
            <h5>Once changes are saved, you will be logged out and required to log in</h5>
            <?= $this->Form->button(__('Confirm'), ['id' => 'submit', 'style' => 'float: right;']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<script>

    // $("#user-form").submit(function(event) {
    //     $('#submit').prop('disabled', true);
    // });
    // let country_select = $('select#address-country');
    // $.getJSON("https://restcountries.com/v3.1/all", function( data ) {
    //     var items = [];
    //     $.each( data, function( key, val ) {
    //         items.push( {id: val.name.common, text: val.name.common} );

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

    // $("#user-form").submit(function(event) {
    //     $('#submit').prop('disabled', true);
    // });
    // let country_select = $('select#address-country');
    // $.getJSON("https://restcountries.com/v3.1/all", function( data ) {
    //     var items = [];
    //     $.each( data, function( key, val ) {
    //         items.push( {id: val.name.common, text: val.name.common} );
    //     });
    //     // country_select.append(items.join(""));
    //     $("select#address-country").select2({
    //         data: items
    //     });
    // });
</script>
