<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var $employerAccess
 * @var $assignedProjects
 * @var $documents
 * @var $requests
 * @var $assignedCompanies
 * @var $builderRequests
 * @var \Cake\Collection\CollectionInterface|string[] $user_project_doc
 * @var \Cake\Collection\CollectionInterface|string[] $project_documents
 */

//debug($user_project_doc);
//debug($project_documents);

$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];

$currentUser = $this->request->getAttribute('identity');
?>
<div class="row content">
    <div class="column-responsive column-80">
        <div class="users view">
        <a  class="btn btn-secondary" href="javascript:history.go(-1)" style="text-decoration: underline;">Back</a>

            <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                <h3>User Overview: <?= h($user->first_name . ' ' . $user->last_name)  ?></h3>
                <br />
                <?php if ($currentUser->id == $user->id) { ?>
                    <a class="btn btn-block btn-primary" style="width: 200px" href="<?= $this->Url->build(['action' => 'edit', $user->id]) ?>">Edit Profile
                    </a>
                <?php } ?>
                <br />
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($user->first_name . ' ' . $user->last_name) ?></td>
                </tr>
                <?php foreach ($companyName as $result) : ?>
                    <tr>
                        <th><?= __('Company Name') ?></th>
                        <td><?= h($result['company_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th><?= __('Role') ?></th>
                    <td><?= h($user->role) ?>
                        <?php if (
                            $currentUser->role != "Builder" &&
                            $currentUser->role != "Admin" &&
                            !isset($builderRequests) &&
                            $currentUser->id == $user->id &&
                            $ownedCompanies > 0
                        ) : ?>
                        <br>
                                <a href="#" data-toggle="modal" data-target="#builderRequestModal" class="btn btn-success"><?= __('Request to be a builder') ?></a>

                            <div class="modal fade" id="builderRequestModal" tabindex="-1" role="dialog" aria-labelledby="builderRequestModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="builderRequestModalLabel">Request to be a Builder</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Please enter your building license numbers (Individual and Company):</p>
                                            <input type="text" id="licenseNumberInput" class="form-control">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="submitRequestButton">Submit Request</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    $('#submitRequestButton').click(function() {
                                        var licenseNumber = $('#licenseNumberInput').val();
                                        if (licenseNumber.trim() !== "") {
                                            var url = '<?= $this->Url->build(['controller' => 'Requests', 'action' => 'addBuilderRequest', $currentUser->id]) ?>';
                                            url += '/' + encodeURIComponent(licenseNumber);
                                            window.location.href = url;
                                        }
                                    });
                                });
                            </script>

                        <?php endif; ?>

                    </td>
                </tr>
                <?php if ($currentUser->id == $user->id) : ?>
                    <tr>
                        <th><?= __('Address') ?></th>
                        <td>
                            <?= h($user->address_no) . ' ' . h($user->address_street) ?><br />
                            <?= h($user->address_suburb) ?><br />
                            <?= h($state[$user->address_state]) . ' ' . h($user->address_postcode) ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= h($user->email) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mobile Phone') ?></th>
                    <td><?= h($user->phone_mobile) ?></td>
                </tr>
                <tr>
                    <th><?= __('Office Phone') ?></th>
                    <td><?= h($user->phone_office) ?></td>
                </tr>
                <?php if ($currentUser->id == $user->id) : ?>
                    <tr>
                        <th><?= __('Emergency Contact') ?></th>
                        <td>
                            <?= h($user->emergency_name) ?> (<?= h($user->emergency_relationship) ?>)<br />
                            Phone: <?= h($user->emergency_phone) ?><br />
                        </td>
                    </tr>
                <?php endif; ?>
            </table>

            <?php if ($user->role != "Admin") { ?>
                <div class="related">
                    <?php if ($currentUser->id == $user->id) { ?>
                        <h4><?= __('Users Created/Pending Requests') ?></h4> <!-- TODO: Need to fix this to display requests for user -->
                        <div class="table-responsive-md">
                            <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                                <tr>
                                    <th><?= __('Request Type') ?></th>
                                    <th><?= __('Project ID') ?></th>
                                    <th><?= __('Request Text') ?></th>
                                    <th><?= __('Created At') ?></th>
                                </tr>
                                <?php foreach ($requests as $request) : ?>
                                    <tr>
                                        <td><?= h($request->request_type) ?></td>
                                        <?php if ($request->project_id == NULL) { ?>
                                            <td>N/A</td>
                                        <?php } ?>
                                        <?php if ($request->project_id != NULL) { ?>
                                            <td><?= h($request->project_id) ?></td>
                                        <?php } ?>
                                        <td><?= h($request->request_text) ?></td>
                                        <td><?= h($request->created_at) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if ($user->role != "Visitor" && $user->role != "Client" && $user->role != "Consultant" && $user->role != "Admin" && $currentUser->id == $user->id) { ?>
                <div class="related">
                    <h4><?= __('Users Assigned Companies') ?></h4>
                    <div class="table-responsive">
                        <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th><?= $this->Paginator->sort('company_type') ?></th>
                                    <th><?= $this->Paginator->sort('abn') ?></th>
                                    <th><?= $this->Paginator->sort('name') ?></th>
                                    <th>Address</th>
                                    <th><?= $this->Paginator->sort('contact_name') ?></th>
                                    <th><?= $this->Paginator->sort('contact_email') ?></th>
                                    <th><?= $this->Paginator->sort('contact_phone') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignedCompanies as $company) : ?>
                                    <tr>
                                        <td><?= h($company->company_type) ?></td>
                                        <td><?= h($company->abn) ?></td>
                                        <td><?= h($company->name) ?></td>
                                        <td><?= h($company->address_no . ' ' . $company->address_street) ?>
                                            <br />
                                            <?= h($company->address_suburb) ?>
                                            <br />
                                            <?= h($state[$company->address_state] . ' ' . $company->address_postcode) ?>
                                        </td>
                                        <td><?= h($company->contact_name) ?></td>
                                        <td><?= h($company->contact_email) ?></td>
                                        <td><?= h($company->contact_phone) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
            <?php if ($user->role != "Visitor" && $user->role != "Client" && $user->role != "Consultant" && $user->role != "Admin" && $currentUser->id == $user->id) { ?>
                <div class="related">
                    <h4><?= __('Users Assigned Projects') ?></h4>
                    <div class="table-responsive">
                        <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                            <thead>
                                <tr>
                                    <th><?= $this->Paginator->sort('name', 'Project Name') ?></th>
                                    <th><?= $this->Paginator->sort('project_type') ?></th>
                                    <th>Address</th>
                                    <th><?= $this->Paginator->sort('start_date') ?></th>
                                    <th><?= $this->Paginator->sort('status') ?></th>
                                    <th class="actions"><?= __('View Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignedProjects as $project) : ?>
                                    <tr> <!--onclick="window.location='<?= $this->Url->build(['action' => 'view', $project->id]) ?>';"-->
                                        <td><?= h($project->name) ?></td>
                                        <td><?= h($project->project_type) ?></td>
                                        <td><?= h($project->address_no . ' ' . $project->address_street) ?>
                                            <br />
                                            <?= h($project->address_suburb) ?>
                                            <br />
                                            <?= h($state[$project->address_state] . ' ' . $project->address_postcode) ?>
                                        </td>
                                        <td><?= h($project->start_date) ?></td>
                                        <td><?= h($project->status) ?></td>
                                        <td><?= $this->Html->link(__('Project Details'), ['action' => 'view', $project->id]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>


            <div class="related">
                <?php if ($currentUser->id == $user->id && $currentUser->role == 'On-site Worker') { ?>
                    <h4><?= __('Personal Documents') ?></h4>
                    <a class="btn btn-block btn-primary" style="width: 200px" href="<?= $this->Url->build(
                                                                                        ['controller' => 'Documents', 'action' => 'add', '?' => ['user' => $currentUser->id]]
                                                                                    ) ?>">Add Personal Documents</a><br>
                    <div class="table-responsive">
                        <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                            <tr>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Document Type') ?></th>
                                <th><?= __('Issue Date') ?></th>
                                <th><?= __('Expiry Date') ?></th>
                                <!-- <th><?= __('Status') ?></th> -->
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                            <?php foreach ($documents as $document) : ?>
                                <tr>
                                    <td><?= h($document->name) ?></td>
                                    <td><?= h($document->document_type) ?></td>
                                    <!-- <td>Selected Document Type: <?= h($selectedDocumentType) ?></td> -->
                                    <td><?= h($document->issue_date) ?></td>
                                    <td><?= h($document->expiry_date) ?></td>
                                    <!-- <td><?= h($document->status) ?></td> -->
                                    <?php if ($document->archived == 0){ ?>
                                    <td class="actions">
                                        <?= $this->Html->link('<i class="fa fa-folder"></i> ' . __('View'), ['controller' => 'Documents', 'action' => 'view', $document->id], ['class' => 'btn btn-primary', 'escape' => false]) ?>
                                        <?php if ($currentUser->id == $user->id) : ?>
                                            <?= $this->Html->link('<i class="fa fa-pencil"></i> ' . __('Edit Details'), ['controller' => 'Documents', 'action' => 'edit', $document->id], ['class' => 'btn btn-warning', 'escape' => false]) ?>
                                            <?= $this->Html->link('<i class="fa fa-archive"></i> ' . __('Archive Document'), ['controller' => 'Documents', 'action' => 'delete', $document->id], ['confirm' => __('Are you sure you want to archive ' .$document->name . '?'), 'class' => 'btn btn-danger', 'escape' => false]) ?>
                                            <br />
                                        <?php endif; ?>
                                    </td>
                                    <?php } else{ ?>
                                        <td class="actions">
                                            <?php if ($currentUser->id == $user->id) : ?>
                                                <?= $this->Html->link('<i class="fa fa-archive"></i> ' . __('Unarchive Document'), ['controller' => 'Documents', 'action' => 'unarchived', $document->id], [ 'class' => 'btn btn-warning', 'escape' => false]) ?>
                                                <br />
                                            <?php endif; ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($documents->count() == 0) {
                                echo '<tr><td>No personal documents have been added.</td></tr>';
                            } ?>
                            <tr>

                            </tr>
                        </table>
                    </div>
                <?php } ?>
            </div>

            <div class="related">
                <?php if (!empty($user_project_doc)) : ?>
                    <?php if ($currentUser->role == 'Builder') { ?>
                        <h3><?= __('Personal Documents') ?></h3>
                        <div class="table-responsive">
                            <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                                <tr>
                                    <th><?= __('Name') ?></th>
                                    <th><?= __('Document Type') ?></th>
                                    <th><?= __('Issue Date') ?></th>
                                    <th><?= __('Expiry Date') ?></th>
                                    <th><?= __('Status') ?></th>
                                    <th><?= __('Comments') ?></th>
                                    <th class="actions"><?= __('Actions') ?></th>
                                </tr>
                                <?php foreach ($user_project_doc as $document) : ?>
                                    <?php
                                        $auth_value = $project_documents[$document->id]->auth_value;
                                        $auth_value = explode(',', $auth_value);
                                        $pj_id = $project_documents[$document->id]->project_id;
                                        if (in_array('Builder', $auth_value)) {
                                    ?>
                                    <tr>
                                        <td><?= h($document->name) ?></td>
                                        <td><?= h($document->document_type) ?></td>
                                        <td><?= h($document->issue_date) ?></td>
                                        <td><?= h($document->expiry_date) ?></td>
                                        <td style="color:
                                        <?php if ($project_documents[$document->id]->status === 'Reviewed') : ?>
                                            green
                                        <?php elseif ($project_documents[$document->id]->status === 'Rejected') : ?>
                                            red
                                        <?php else : ?>
                                            orange
                                        <?php endif; ?>">
                                            <?= h($project_documents[$document->id]->status) ?>
                                        </td>
                                        <td><?= h($project_documents[$document->id]->comment) ?></td>
                                        <td class="actions">
                                            <?= $this->Html->link(__('View'), ['controller' => 'Documents', 'action' => 'view', $document->id,'?' => ['pj_id' => $pj_id]]) ?>
                                            <?php if ($currentUser->id == $user->id) {
                                                echo '<br/>' . $this->Html->link(__('Edit Details'), ['controller' => 'Documents', 'action' => 'edit', $document->id]);
                                                echo '<br/>' . $this->Html->link(
                                                    __('Delete Document'),
                                                    ['controller' => 'Documents', 'action' => 'delete', $document->id],
                                                    ['confirm' => 'Are you sure you want to delete this document?']
                                                );
                                            } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                <?php endforeach; ?>

                                <?php if ($documents->count() == 0) {
                                    echo '<tr><td>No documents have been added.</td></tr>';
                                } ?>
                                <tr>
                                    <td>
                                        <?php if ($currentUser->id == $user->id) {
                                            echo $this->Html->link(__('Add Documents'), ['controller' => 'Documents', 'action' => 'add', '?' => ['user' => $user->id]]);
                                        } ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <?php } ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
