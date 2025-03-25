<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Document $document
 * @var string[]|\Cake\Collection\CollectionInterface $projects
 * @var string[]|\Cake\Collection\CollectionInterface $users
 * @var \Cake\Collection\CollectionInterface|string[] $related_projects
 * @var \Cake\Collection\CollectionInterface|string[] $project_documents
 */

//debug($project_documents);

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

<div class="row content">
    <div class="column-responsive column-80" style="max-width:800px" <div class="documents form content">
        <?= $this->Form->create($document, ['id' => 'document-form']) ?>
        <fieldset>
            <legend><?= 'Edit Document Details: ' . $document->name ?></legend>
            <?= $this->Form->control('name', [
                'label' => 'Document Name',
                'required' => true,
                'placeholder' => 'Enter the new document name',
                'maxlength' => 50,
            ]) ?>


            <?php
            echo $this->Form->control('details', ['label' => 'Document Details', 'required' => true, 'maxlength' => 240]);
            if ($document->related_company_id) {
                $accessible = '';
                if ($document->worker_accessible == 1) {
                    $accessible = 'checked';
                }
                echo '<input type="checkbox" id="worker_accessible" name="worker_accessible" value="y" ' . $accessible . '>
                                    <label for="worker_accessible">Make this document visible to on-site workers?</label><br/><br/>';
            }
            ?>

            <?= $this->Form->control('issue_date', [
                'type' => 'text',
                'label' => 'Issue Date (If Applicable)',
                'autocomplete' => 'off',
                'placeholder' => 'Please select the issue date',
                'empty' => true,
                'style' => 'width: 30%;',
            ]) ?>

            <?= $this->Form->control('expiry_date', [
                'type' => 'text',
                'id' => 'expiry-date',
                'label' => 'Expiry Date (If Applicable)',
                'autocomplete' => 'off',
                'placeholder' => 'Please select the expiry date',
                'empty' => true,
                'style' => 'width: 30%;',
            ]) ?>

<fieldset>


<!-- <?= $this->Form->control('new_file_upload', [
    'label' => 'Upload New Document (PDF, JPEG)',
    'type' => 'file',
    'accept' => '.pdf,.jpeg,.jpg',
]) ?> -->

            <?php if ($document->document_relation == 'company') { ?>
                <div class="related">
                    <h4><?= __('Project Access') ?></h4>
                    <div class="table-responsive">
                        <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                            <tr>
                                <th><?= __('Project Name') ?></th>
                                <th><?= __('Address') ?></th>
                                <th class="actions"><?= __('Actions (Select document visibility for this project)') ?></th>
                            </tr>
                            <?php if ($related_projects) { ?>
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
                                                    <?php
                                                    if (isset($project_documents[$related_project[0]->id][0]) && $project_documents) {

                                                        $projectDocument = $project_documents[$related_project[0]->id][0];

                                                        // Fetch existing auth_value for this project document
                                                        $authValue = $projectDocument->auth_value;
                                                        $selectedRoles = explode(',', $authValue);
                                                    ?>
                                                    <script>
                                                        // Pre-select checkboxes based on existing auth_value
                                                        $(document).ready(function() {
                                                            var projectId = <?= $related_project[0]->id ?>;
                                                            var selectedRoles = <?= json_encode($selectedRoles) ?>;
                                                            selectedRoles.forEach(function(role) {
                                                                $("#" + role + "_" + projectId).prop('checked', true);
                                                            });

                                                            // Handle radio button change event for this specific project
                                                            $('input[name="auth_type_<?= $related_project[0]->id ?>"]').change(function() {
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
                                                        <input type="radio" id="document_Auth4_<?= $related_project[0]->id ?>" name="auth_type_<?= $related_project[0]->id ?>" value="4" <?php echo ($projectDocument->auth_type == 4) ? 'checked' : ''; ?>>
                                                        <label for="document_Auth4_<?= $related_project[0]->id ?>">Private</label>
                                                    </div>
                                                    <div>
                                                        <input type="radio" id="document_Auth3_<?= $related_project[0]->id ?>" name="auth_type_<?= $related_project[0]->id ?>" value="3" <?php echo ($projectDocument->auth_type == 3) ? 'checked' : ''; ?>>
                                                        <label for="document_Auth3_<?= $related_project[0]->id ?>">Any Role</label>
                                                    </div>


                                                    <div class="checkbox-box" id="checkboxes_<?= $related_project[0]->id ?>" <?php echo ($projectDocument->auth_type != 3) ? 'style="display: none;"' : ''; ?>>
                                                        <div>
                                                            <input type="checkbox" id="Admin_<?= $related_project[0]->id ?>" name="auth_value_<?= $related_project[0]->id ?>[]" value="Admin" disabled checked >
                                                            <label for="Admin_<?= $related_project[0]->id ?>">Admin</label>
                                                        </div>
                                                        <div>
                                                            <input type="checkbox" id="Builder_<?= $related_project[0]->id ?>" name="auth_value_<?= $related_project[0]->id ?>[]" value="Builder" <?php echo (in_array('Builder', $selectedRoles)) ? 'checked' : ''; ?>>
                                                            <label for="Builder_<?= $related_project[0]->id ?>">Builder</label>
                                                        </div>
<!--                                                        <div>-->
<!--                                                            <input type="checkbox" id="Client_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Client" --><?php //echo (in_array('Client', $selectedRoles)) ? 'checked' : ''; ?><!--//>-->
<!---->
<!--                                                            <label for="Client_--><?php //= $related_project[0]->id ?><!--">Client</label></div>-->
<!--                                                        <div>-->
<!--                                                            <input type="checkbox" id="On-siteWorker_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="On-siteWorker" --><?php //echo (in_array('On-siteWorker', $selectedRoles)) ? 'checked' : ''; ?><!--//>-->
<!--                                                            <label for="On-siteWorker_--><?php //= $related_project[0]->id ?><!--">On-site Worker</label>-->
<!--                                                        </div>-->
<!--                                                        <div>-->
<!--                                                            <input type="checkbox" id="Contractor_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Contractor" --><?php //echo (in_array('Contractor', $selectedRoles)) ? 'checked' : ''; ?><!--//>-->
<!--                                                            <label for="Contractor_--><?php //= $related_project[0]->id ?><!--">Contractor</label>-->
<!--                                                        </div>-->
<!--                                                        <div>-->
<!--                                                            <input type="checkbox" id="Subcontractor_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Subcontractor" --><?php //echo (in_array('Subcontractor', $selectedRoles)) ? 'checked' : ''; ?><!--//>-->
<!--                                                            <label for="Subcontractor_--><?php //= $related_project[0]->id ?><!--">Subcontractor</label>-->
<!--                                                        </div>-->
<!--                                                        <div>-->
<!--                                                            <input type="checkbox" id="Consultant_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Consultant" --><?php //echo (in_array('Consultant', $selectedRoles)) ? 'checked' : ''; ?><!--//>-->
<!--                                                            <label for="Consultant_--><?php //= $related_project[0]->id ?><!--">Consultant</label>-->
<!--                                                        </div>-->
<!--                                                        <div>-->
<!--                                                            <input type="checkbox" id="Visitor_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Visitor" --><?php //echo (in_array('Visitor', $selectedRoles)) ? 'checked' : ''; ?><!--//>-->
<!--                                                            <label for="Visitor_--><?php //= $related_project[0]->id ?><!--">Visitor</label>-->
<!--                                                        </div>-->
                                                    </div>

                                                    <?php } else{ //No project document data exists
//                                                        debug($related_project[0]->id);
                                                        ?>

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
<!--                                                                <div>-->
<!--                                                                    <input type="checkbox" id="Client_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Client">-->
<!--                                                                    <label for="Client_--><?php //= $related_project[0]->id ?><!--">Client</label>-->
<!--                                                                </div>-->
<!--                                                                <div>-->
<!--                                                                    <input type="checkbox" id="On-siteWorker_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="On-siteWorker">-->
<!--                                                                    <label for="On-siteWorker_--><?php //= $related_project[0]->id ?><!--">On-site Worker</label>-->
<!--                                                                </div>-->
<!--                                                                <div>-->
<!--                                                                    <input type="checkbox" id="Contractor_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Contractor">-->
<!--                                                                    <label for="Contractor_--><?php //= $related_project[0]->id ?><!--">Contractor</label>-->
<!--                                                                </div>-->
<!--                                                                <div>-->
<!--                                                                    <input type="checkbox" id="Subcontractor_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Subcontractor">-->
<!--                                                                    <label for="Subcontractor_--><?php //= $related_project[0]->id ?><!--">Subcontractor</label>-->
<!--                                                                </div>-->
<!--                                                                <div>-->
<!--                                                                    <input type="checkbox" id="Consultant_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Consultant">-->
<!--                                                                    <label for="Consultant_--><?php //= $related_project[0]->id ?><!--">Consultant</label>-->
<!--                                                                </div>-->
<!--                                                                <div>-->
<!--                                                                    <input type="checkbox" id="Visitor_--><?php //= $related_project[0]->id ?><!--" name="auth_value_--><?php //= $related_project[0]->id ?><!--[]" value="Visitor">-->
<!--                                                                    <label for="Visitor_--><?php //= $related_project[0]->id ?><!--">Visitor</label>-->
<!--                                                                </div>-->
                                                            </div>

                                                        </div>

                                                    <?php } ?>


                                                </div>
                                                <div class="document_auth_box_input" data-project-id="<?= $related_project[0]->id ?>"></div>
                                            </div>
                                        </td>
                                        <!-- Save the project id for this document so that we can use it in the controller -->
                                        <?php echo $this->Form->hidden('doc_project_id[]', ['value' => $related_project[0]->id]); ?>
                                    </tr>


                                <?php endforeach; ?>
                            <?php } ?>
                            <?php
                            if (!$related_projects && $document->document_relation == 'company') {
                                echo '<tr><td>Your company do not have any related projects.</td></tr>';
                            } elseif (!$related_projects && $document->document_relation == 'user') {
                                echo '<tr><td>You do not have any related projects.</td></tr>';
                            }
                            ?>
                        </table>
                    </div>
                </div>
            <?php } ?>

        </fieldset>
        <?= $this->Form->button('Submit', ['id' => 'submit']) ?>
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

</script>
