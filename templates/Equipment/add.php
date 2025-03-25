<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Document $document
 * @var \Cake\Collection\CollectionInterface|string[] $projects
 * @var \Cake\Collection\CollectionInterface|string[] $users
 */

$currentUser = $this->request->getAttribute('identity');
$documentRelation = "user"; //Tells whether the upload is for personal or project-specific induction document.
$id = $currentUser->id;
$currentId = $currentUser->id;
$type = "Other";
$heading = "Add Personal Equipment";
if ($this->request->getQuery('project')){
    $documentRelation = "project";
    $id = $this->request->getQuery('project');
    $type = "Induction";
    $heading = "Add Site Induction Equipment";
} elseif ($this->request->getQuery('company')){
    $documentRelation = "company";
    $id = $this->request->getQuery('company');
    $heading = "Add Equipment for your Company";
}

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.js" integrity="sha512-+UiyfI4KyV1uypmEqz9cOIJNwye+u+S58/hSwKEAeUMViTTqM9/L4lqu8UxJzhmzGpms8PzFJDzEqXL9niHyjA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>
    .document_auth_box{
       margin-bottom:40px;
    }
    .document_auth_box_top{
        display:flex;
    }
    .document_auth_box_top div{
        margin-right:20px;

    }
    .document_auth_box_top div input{
    }
    .document_auth_box_select label {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 5px;
        font-weight: 700;
    }
    .document_auth_box_input{

    }
    .document_auth_box_select{
    }
    .checkbox-box{
        display:flex
    }
    .checkbox-box div{
        margin-right:12px;
    }
</style>

<div class="row content">
    <div class="column-responsive column-80" style="padding-left: 2.5%; padding-right: 2.5%; max-width: 100%">
        <div class="documents form content">
            <?= $this->Form->create($equipment, ['type' => 'file', 'id' => 'document-form']) ?>
            <fieldset>
                <legend><?= __($heading) ?></legend>
                <p><span style="color: red">*</span> Required fields</p>

                <?php
                    echo $this->Form->control('name', ['label' => 'Name *','required'=> true, 'placeholder'=>'e.g. Ryobi PowerDrill 350 Max','maxlength'=>100]);
                echo $this->Form->label('Description *');
echo '<br>';
                echo $this->Form->textarea('description', [
                    'placeholder' => 'Serial Number:' . PHP_EOL . 'Make:' . PHP_EOL . 'Model:' . PHP_EOL . 'Risk Assessment:',
                    'default' => 'Serial Number:' . PHP_EOL . 'Make:' . PHP_EOL . 'Model:' . PHP_EOL . 'Risk Assessment:'. PHP_EOL . 'Logbook Entries:',
                    'style' => 'width: 100%',
                ]);
                    echo $this->Form->hidden('is_licensed', ['label' => 'Licensed', 'default'=> true]);

                    echo $this->Form->control('hired_from_date', ['type' => 'text', 'label' => 'Hired From Date *', 'autocomplete' => 'off', 'empty' => true]);
                    echo $this->Form->control('hired_until_date', ['type' => 'text', 'label' => 'Hired Until Date *', 'autocomplete' => 'off','empty' => true]);
                    echo $this->Form->control('file_upload', ['label' => 'Upload Equipment Documents (PDF)', 'type' => 'file', 'accept' => '.pdf']);
                echo $this->Form->label('imagelabel', 'Image (Only JPEG, PNG and GIF image types are accepted)');
                echo '<br>';
                echo $this->Form->control('combined_file', [
                    'type' => 'file', // Use a single file input for both the file and the image
                    'label' => false,
                    'accept' => 'image/jpeg, image/png, image/gif, image/jpg',
                ]);
                // Add a hidden input field to store the image name
                echo $this->Form->hidden('image', [
                    'id' => 'image-hidden',
                    'value' => '', // This will be populated using JavaScript
                ]);
                    if ($documentRelation == 'project'){
                        echo '<div class="document_auth_box">';
                        echo    '<div class="document_auth_box_top">';
                        echo        '<div>';
                        echo            '<input type="radio" id="document_Auth1" name="auth_type" value="1" checked="true">';
                        echo            '<label for="document_Auth1">Only Builder (select this if unsure)</label>';
                        echo        '</div>';
//                        echo        '<div>';
//                        echo            '<input type="radio" id="document_Auth2" name="auth_type" value="2">';
//                        echo            '<label for="document_Auth2">Specific people using their email (for multiple emails, use a semicolon to separate)</label>';
//                        echo        '</div>';
                        echo        '<div>';
                        echo            '<input type="radio" id="document_Auth3" name="auth_type" value="3">';
                        echo            '<label for="document_Auth3">Specific roles</label>';
                        echo        '</div>';
                        echo    '</div>';
                        echo '<div class="document_auth_box_input"></div></div>';
                    }
                    if ($documentRelation == 'company'){
                        echo '<input type="checkbox" id="worker_accessible" name="worker_accessible" value="y" checked="true">
                                <label for="worker_accessible">Make this equipment visible to on-site workers?</label><br/><br/>';
                    }
                    echo $this->Form->hidden('equipment_type', ['value' => $type]);
                    echo $this->Form->hidden('equipment_relation', ['value' => $documentRelation]);
                    echo $this->Form->hidden('relation_id', ['value' => $id]);
                    echo $this->Form->hidden('builder_auth', ['value' => $currentId]);
                ?>
            </fieldset>
            <?= $this->Form->button('Submit', ['id' => 'submit', 'style' => 'float: right;']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<script>
    jQuery(function () {
        jQuery('#hired-from-date').datetimepicker({
            onShow: function (ct) {
                this.setOptions({
                    maxDate: jQuery('#hired-until-date').val() ? jQuery('#hired-until-date').val() : false,
                    format:'Y-m-d'
                })
            },
            timepicker: false
        });
        jQuery('#hired-until-date').datetimepicker({
            onShow: function (ct) {
                this.setOptions({
                    minDate: jQuery('#hired-from-date').val() ? jQuery('#hired-from-date').val() : new Date(),
                    format:'Y-m-d'
                })
            },
            timepicker: false
        });
    });
    $("#document-form").submit(function(event) {

        $('#submit').prop('disabled', true);
    });
    $(function(){
        // var document_auth_box_input =  `<div class="form-group input text required">
        //     <label class="control-label" for="auth_value">Anybody</label>
        //     <input
        //         type="text"
        //         name="auth_value"
        //         maxlength="50"
        //         class="form-control"
        //         id="auth_value"
        //         required="required"
        //         data-validity-message="This field cannot be left empty"
        //     >
        // </div> `;
        var document_auth_box_select = `<div class="input select required">
            <div class="checkbox-box">
                <div>
                    <label for="Admin">Admin</label>
                    <input type="checkbox" id="Admin" checked disabled name="auth_value[]" value="Admin">
                </div>
                <div>
                    <label for="Builder">Builder</label>
                    <input type="checkbox" id="Builder" name="auth_value[]" value="Builder">
                </div>
                <div>
                    <label for="Client">Client</label>
                    <input type="checkbox" id="Client" name="auth_value[]" value="Client">
                </div>
                <div>
                    <label for="On-siteWorker">On-site Worker</label>
                    <input type="checkbox" id="On-siteWorker" name="auth_value[]" value="On-site Worker">
                </div>
                <div>
                    <label for="Contractor">Contractor</label>
                    <input type="checkbox" id="Contractor" name="auth_value[]" value="Contractor">
                </div>
                <div>
                    <label for="Subcontractor">Subcontractor</label>
                    <input type="checkbox" id="Subcontractor" name="auth_value[]" value="Subcontractor">
                </div>
                <div>
                    <label for="Consultant">Consultant</label>
                    <input type="checkbox" id="Consultant" name="auth_value[]" value="Consultant">
                </div>
                <div>
                    <label for="Visitor">Visitor</label>
                    <input type="checkbox" id="Visitor" name="auth_value[]" value="Visitor">
                </div>
            </div>
        </div>`;

        $('input[type=radio][name=auth_type]').change(function(){
            var val = $(this).val();
            console.log(val);
            if(val==1){
                $(".document_auth_box_input").html("")
            }else if(val==2){
                // $(".document_auth_box_input").html(document_auth_box_input)
            }else if(val==3){
                $(".document_auth_box_input").html(document_auth_box_select)
            }
        })
    })
</script>
