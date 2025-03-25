<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Document $document
 * @var \Cake\Collection\CollectionInterface|string[] $projects
 * @var \Cake\Collection\CollectionInterface|string[] $users
 * @var \Cake\Collection\CollectionInterface|string[] $related_projects
 * @var \Cake\Collection\CollectionInterface|string[] $companyProjects
 */
//debug($related_projects);

$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];

$currentUser = $this->request->getAttribute('identity');
$documentRelation = "user"; //Tells whether the upload is for personal or project-specific induction document.
$id = $currentUser->id;
$type = "Other";
$heading = "Add Personal Document";
if ($this->request->getQuery('project')) {
    $documentRelation = "project";
    $id = $this->request->getQuery('project');
    $type = "Induction";
    $heading = "Add Site Induction Document";
} elseif ($this->request->getQuery('company')) {
    $documentRelation = "company";
    $id = $this->request->getQuery('company');
    $heading = "Add Document for your Company";
}

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.js" integrity="sha512-+UiyfI4KyV1uypmEqz9cOIJNwye+u+S58/hSwKEAeUMViTTqM9/L4lqu8UxJzhmzGpms8PzFJDzEqXL9niHyjA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>
    .document_auth_box {
        margin-bottom: 40px;
    }

    .document_auth_box_top {
        display: flex;
    }

    .document_auth_box_top div {
        margin-right: 20px;

    }

    .document_auth_box_top div input {}

    .document_auth_box_select label {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 5px;
        font-weight: 700;
    }

    .document_auth_box_input {}

    .document_auth_box_select {}

    .checkbox-box {
        display: flex
    }

    .checkbox-box div {
        margin-right: 12px;
    }
</style>

    <div class="row content">
        <div class="column-responsive column-80" style="padding-left: 2.5%; padding-right: 2.5%; max-width: 100%">
            <div class="documents form content">
                <?= $this->Form->create($document, ['type' => 'file', 'id' => 'document-form']) ?>
                <fieldset>
                    <legend><?= __($heading) ?></legend>
                    <?php
                    echo $this->Form->control('name', ['label' => 'Document Name *','required' => true, 'placeholder' => 'e.g Contract-COS123', 'maxlength' => 50]);
                    //If it is a induction document, the default should be 'Induction'
                    if ($heading == "Add Document for your Company"){
                        echo $this->Form->label('document_type', 'Select Document Type:');
                        echo $this->Form->select(
                            'document_type',
                            [
                                'SWMS' => 'SWMS',
                                'Insurance' => 'Insurance',
                                'Other' => 'Other'
                            ],
                            ['required' => true]
                        );
                    }
                    elseif ($documentRelation == 'user'){
                        echo $this->Form->label('document_type', 'Select Document Type:');
                        echo $this->Form->select(
                            'document_type',
                            [
                                'High Risk Work License' => 'High Risk Work License',
                                'Insurance' => 'Insurance',
                                'Card' => 'Card',
                                'Other' => 'Other'
                            ],
                            ['required' => true]
                        );
                    }
                    else {
                      // If $heading is 'Add Site Induction Document', set the default value to 'Induction'
                    echo $this->Form->hidden('document_type', ['value' => 'Induction']);

                }
                    echo $this->Form->control('details', ['label' => 'Details *', 'required' => true, 'placeholder' => 'e.g Contract with contractor xxx', 'maxlength' => 240]);
                    echo $this->Form->control('document_no', ['label' => 'Document Number (e.g Receipt number, License number, etc) *', 'required' => true, 'placeholder' => 'e.g COS123', 'maxlength' => 50]);
                    echo $this->Form->control('issuer', ['label' => 'Issuer Name *','required' => true, 'placeholder' => 'e.g Charlie', 'maxlength' => 50]);
                    echo $this->Form->control('issue_date', ['type' => 'text', 'label' => 'Issue Date (If Applicable)', 'autocomplete' => 'off', 'placeholder' => 'Please select the issue date', 'empty' => true,  'style' => 'width: 30%;']);
                    echo $this->Form->control('expiry_date', ['type' => 'text', 'id' => 'expiry-date', 'label' => 'Expiry Date (If Applicable)', 'autocomplete' => 'off', 'placeholder' => 'Please select the expiry date', 'empty' => true, 'style' => 'width: 30%;']);
                    echo $this->Form->control('file_upload', ['label' => 'Upload Document (PDF, JPEG) *', 'type' => 'file', 'required' => true, 'accept' => '.pdf,.jpeg,.jpg']);

//                    if ($documentRelation == 'company' || $documentRelation == 'user') {
//                        echo 'Please select the roles you want this document to be visible to:';
//                        echo '<div class="document_auth_box">';
//                        echo    '<div class="document_auth_box_top">';
//                        echo        '<div>';
//                        echo            '<input type="radio" id="document_Auth4" name="auth_type" value="4">';
//                        echo            '<label for="document_Auth4">Private</label>';
//                        echo        '</div>';
////                        echo        '<div>';
////                        echo            '<input type="radio" id="document_Auth1" name="auth_type" value="1" checked="true">';
////                        echo            '<label for="document_Auth1">Only Builder</label>';
////                        echo        '</div>';
////                        echo        '<div>';
////                        echo            '<input type="radio" id="document_Auth2" name="auth_type" value="2">';
////                        echo            '<label for="document_Auth2">Any Body(multiple emails use ; separate)</label>';
////                        echo        '</div>';
//                        echo        '<div>';
//                        echo            '<input type="radio" id="document_Auth3" name="auth_type" value="3">';
//                        echo            '<label for="document_Auth3">Any Role</label>';
//                        echo        '</div>';
//                        echo    '</div>';
//                        echo '<div class="document_auth_box_input"></div></div>';
//                        // echo '<input type="checkbox" id="requires_signature" name="requires_signature" value="y">
//                        //     <label for="requires_signature">Requires Signature</label><br/>';
//                        // echo $this->Form->control('declaration_text', ['label' => 'Declaration Text', 'required' => true, 'maxlength' => 500, 'readonly' => true]);
//
//                    }

                    if ($documentRelation == 'company') { ?>
                    <div class="related">
                        <h4><?= __('Project Access') ?></h4>
                        <h5>Give document access to different projects</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                                <tr>
                                    <th><?= __('Project Name') ?></th>
                                    <th><?= __('Address') ?></th>
                                    <th class="actions"><?= __('Actions (Select document visibility for this project)') ?></th>
                                </tr>
                                <?php if (isset($related_projects) && $related_projects) { ?>
                                    <?php foreach ($related_projects as $related_project) : ?>

                                        <tr>
                                            <td><?= h($related_project[0]->name) ?></td>
                                            <td><?= h($related_project[0]->address_no . ' ' . $related_project[0]->address_street) ?><br />
                                                <?= h($related_project[0]->address_suburb) ?><br />
                                                <?= h($state[$related_project[0]->address_state] . ' ' . $related_project[0]->address_postcode) ?>
                                            </td>
                                            <td class="actions">
                                                <div class="document_auth_box">
                                                    <div class="document_auth_box_top">

                                                            <script>

                                                                $(document).ready(function() {

                                                                    $('input[name="auth_type_<?= $related_project[0]->id?>"]').change(function() {
                                                                        var val = $(this).val();
                                                                        var projectId = <?= $related_project[0]->id ?>;

                                                                        // Toggle visibility of the checkbox box based on radio button value
                                                                        var checkboxes = $('#checkboxes_' + projectId);
                                                                        if (val == 3) {
                                                                            checkboxes.show();
                                                                        } else {
                                                                            checkboxes.hide();
                                                                        }
                                                                    });
                                                                });
                                                            </script>

                                                            <div>
                                                                <input type="radio" id="document_Auth4_<?= $related_project[0]->id ?>" name="auth_type_<?= $related_project[0]->id ?>" value="4">
                                                                <label for="document_Auth4_<?= $related_project[0]->id ?>">Private</label>
                                                            </div>
                                                            <div>
                                                                <input type="radio" id="document_Auth3_<?= $related_project[0]->id ?>" name="auth_type_<?= $related_project[0]->id ?>" value="3">
                                                                <label for="document_Auth3_<?= $related_project[0]->id ?>">Any Role</label>
                                                            </div>

                                                        <div class="checkbox-box" id="checkboxes_<?= $related_project[0]->id ?>" style="display: none;">
                                                            <div>
                                                                <input type="checkbox" id="Admin_<?= $related_project[0]->id ?>" name="auth_value_<?= $related_project[0]->id ?>[]" value="Admin" disabled checked>
                                                                <label for="Admin_<?= $related_project[0]->id ?>">Admin</label>
                                                            </div>
                                                            <div>
                                                                <input type="checkbox" id="Builder_<?= $related_project[0]->id ?>" name="auth_value_<?= $related_project[0]->id ?>[]" value="Builder">
                                                                <label for="Builder_<?= $related_project[0]->id ?>">Builder</label>
                                                            </div>
<!--                                                            <div>-->
<!--                                                                <input type="checkbox" id="Client_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Client">-->
<!--                                                                <label for="Client_--><?php //= $related_project[0]->id ?><!--">Client</label>-->
<!--                                                            </div>-->
<!--                                                            <div>-->
<!--                                                                <input type="checkbox" id="On-siteWorker_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="On-siteWorker">-->
<!--                                                                <label for="On-siteWorker_--><?php //= $related_project[0]->id ?><!--">On-site Worker</label>-->
<!--                                                            </div>-->
<!--                                                            <div>-->
<!--                                                                <input type="checkbox" id="Contractor_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Contractor">-->
<!--                                                                <label for="Contractor_--><?php //= $related_project[0]->id ?><!--">Contractor</label>-->
<!--                                                            </div>-->
<!--                                                            <div>-->
<!--                                                                <input type="checkbox" id="Subcontractor_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Subcontractor">-->
<!--                                                                <label for="Subcontractor_--><?php //= $related_project[0]->id ?><!--">Subcontractor</label>-->
<!--                                                            </div>-->
<!--                                                            <div>-->
<!--                                                                <input type="checkbox" id="Consultant_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Consultant">-->
<!--                                                                <label for="Consultant_--><?php //= $related_project[0]->id ?><!--">Consultant</label>-->
<!--                                                            </div>-->
<!--                                                            <div>-->
<!--                                                                <input type="checkbox" id="Visitor_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Visitor">-->
<!--                                                                <label for="Visitor_--><?php //= $related_project[0]->id ?><!--">Visitor</label>-->
<!--                                                            </div>-->
                                                        </div>

                                                    </div>
<!--                                                    <div class="document_auth_box_input" data-project-id="--><?php //= $related_project[0]->id ?><!--"></div>-->
                                                </div>
                                            </td>
                                            <!-- Save the project id for this document so that we can use it in the controller -->
                                            <?php echo $this->Form->hidden('doc_project_id[]', ['value' => $related_project[0]->id]); ?>
                                        </tr>


                                    <?php endforeach; ?>
                                <?php }}elseif ($documentRelation == 'user'){ ?>

                                    <h5>Added document will be available for all associated projects.</h5>

                                <?php } ?>
                                <?php
                                if (!isset($related_projects) && $documentRelation == 'company') {
                                    echo '<tr><td>Your company do not have any related projects.</td></tr>';
                                } elseif (!isset($related_projects) && $documentRelation == 'user') {
                                    echo '<tr><td>You do not have any related projects.</td></tr>';
                                }
                                ?>
                            </table>
                        </div>
                    </div>

                    <?php
//                    if ($documentRelation == 'company') {
//                        echo '<input type="checkbox" id="worker_accessible" name="worker_accessible" value="y" checked="true">
//                                <label for="worker_accessible">Make this document visible to on-site workers?</label><br/><br/>';
//                    }
                    echo $this->Form->hidden('uploaded_user_id', ['value' => $currentUser->id]);
                    echo $this->Form->hidden('document_relation', ['value' => $documentRelation]);
                    echo $this->Form->hidden('relation_id', ['value' => $id]);
                    echo $this->Form->hidden('extension', ['id' => 'extensionField']);
                    echo $this->Form->hidden('archived', ['value' => 0]);
                    ?>


                </fieldset>
                <?= $this->Form->button('Submit', ['id' => 'submit', 'style' => 'float: right;' ]) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>


<script>



    jQuery(function() {
        jQuery('#issue-date').datetimepicker({
            onShow: function(ct) {
                this.setOptions({
                    maxDate: jQuery('#expiry-date').val() ? jQuery('#expiry-date').val() : false,
                    format: 'Y-m-d'
                })
            },
            timepicker: false
        });
        jQuery('#expiry-date').datetimepicker({
            onShow: function(ct) {
                this.setOptions({
                    minDate: jQuery('#issue-date').val() ? jQuery('#issue-date').val() : false,
                    format: 'Y-m-d'
                })
            },
            timepicker: false
        });
    });
    $("#document-form").submit(function(event) {

        $('#submit').prop('disabled', true);
    });
    $(function() {
    //     var document_auth_box_input = `<div class="form-group input text required">
    //         <label class="control-label" for="auth_value">Any Body</label>
    //         <input
    //             type="text"
    //             name="auth_value"
    //             maxlength="50"
    //             class="form-control"
    //             id="auth_value"
    //             required="required"
    //             data-validity-message="This field cannot be left empty"
    //         >
    //     </div> `;
    //     var document_auth_box_select = `<div class="input select required">
    //         <div class="checkbox-box">
    //             <div>
    //                 <label for="Admin">Admin</label>
    //                 <input type="checkbox" id="Admin" checked disabled name="auth_value[]" value="Admin">
    //             </div>
    //             <div>
    //                 <label for="Builder">Builder</label>
    //                 <input type="checkbox" id="Builder" name="auth_value[]" value="Builder">
    //             </div>
    //             <div>
    //                 <label for="Client">Client</label>
    //                 <input type="checkbox" id="Client" name="auth_value[]" value="Client">
    //             </div>
    //             <div>
    //                 <label for="On-siteWorker">On-site Worker</label>
    //                 <input type="checkbox" id="On-siteWorker" name="auth_value[]" value="On-site Worker">
    //             </div>
    //             <div>
    //                 <label for="Contractor">Contractor</label>
    //                 <input type="checkbox" id="Contractor" name="auth_value[]" value="Contractor">
    //             </div>
    //             <div>
    //                 <label for="Subcontractor">Subcontractor</label>
    //                 <input type="checkbox" id="Subcontractor" name="auth_value[]" value="Subcontractor">
    //             </div>
    //             <div>
    //                 <label for="Consultant">Consultant</label>
    //                 <input type="checkbox" id="Consultant" name="auth_value[]" value="Consultant">
    //             </div>
    //             <div>
    //                 <label for="Visitor">Visitor</label>
    //                 <input type="checkbox" id="Visitor" name="auth_value[]" value="Visitor">
    //             </div>
    //         </div>
    //     </div>`;

        $('input#file-upload').change(function (){

            var files = event.target.files;
            var extension = files[0].type;
            extension = extension.replace(/(.*)\//g, '');
            // console.log(extension);
            document.getElementById('extensionField').value = extension;

        });

        // $('input[type=radio][name=auth_type]').change(function() {
        //     var val = $(this).val();
        //     // console.log(val);
        //     if (val == 1) {
        //         $(".document_auth_box_input").html("")
        //     } else if (val == 2) {
        //         $(".document_auth_box_input").html(document_auth_box_input)
        //     } else if (val == 3) {
        //         $(".document_auth_box_input").html(document_auth_box_select)
        //     }else if (val == 4) {
        //         $(".document_auth_box_input").html("Set your document to be private so no other users can see it.")
        //     }
        // })
        //
        // $('select[name=document_type]').change(function() {
        //     selectedDocumentType = $(this).val(); // Update the variable with the selected value
        //
        //     let $label = $('label[for=expiry-date]');
        //
        //     // Check if the selected document type is 'Insurance'
        //     if (selectedDocumentType === 'Insurance') {
        //         $label.text('Expiry Date *');
        //
        //         // Make the expiry date field mandatory
        //         $('#expiry-date').prop('required', true);
        //     } else {
        //         $label.text('Expiry Date (If Applicable)');
        //
        //         // Remove the required attribute from the expiry date field
        //         $('#expiry-date').prop('required', false);
        //     }
        // });

        var selectedDocumentType = ""; // Create a variable to store the selected document type
        $('select[name=document_type]').change(function() {
            selectedDocumentType = $(this).val(); // Update the variable with the selected value
        });

    })
</script>
