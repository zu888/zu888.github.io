<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project $project
 * @var $documents
 * @var $checkins
 * @var $workers
 * @var $currentDateTime
 * @var $maxHours
 * @var $builder
 * @var $associatedCompanies
 * @var \Cake\Collection\CollectionInterface|string[] $partner_companies
 * @var \Cake\Collection\CollectionInterface|string[] $companies
 * @var \Cake\Collection\CollectionInterface|string[] $personalDocument
 */
$currentUser = $this->request->getAttribute('identity');

//debug($personalDocument);


$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];
?>
<div class="row content">
    <?php echo $this->Html->css('main'); ?>

    <div class="column-responsive column-80">
    <a  class="btn btn-secondary" href="javascript:history.go(-1)" style="text-decoration: underline;">Back</a>

        <div class="projects view" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;"><?= h($project->name) ?></h3>
            <?php if ($currentUser->role == 'Builder' && $project->status == 'Active') : ?>
                <td><?= $this->Html->link('Mark this project as completed', ['controller' => 'Projects', 'action' => 'completeProject', $project->id], ['class' => 'btn btn-success', 'confirm' => __('Are you sure you want to mark {0} as completed?', $project->name)]) ?></td>
            <?php endif; ?>
        </div>

        <br>

        <table class="table table-bordered" style="background-color:ghostwhite; max-width: 100%">
            <tr>
                <th><?= __('Project Name') ?></th>
                <td><?= h($project->name) ?></td>
            </tr>
            <?php if ($currentUser->id == $project->builder_id || $status == 'Co-Manager') { ?>
                <tr>
                    <th>
                        <?= __('Project Passcode') ?><br>
                        <span style="color: red;">(Share only with known users)</span>
                    </th>
                    <td style="color: red; font-weight: bold;"><?= h($project->passcode) ?></td>
                </tr>

                <tr>
                    <th><?= __('Permit Number') ?></th>
                    <td>
                        <?= h($project->permit_no) ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($project->has('user')) { ?>
                <tr>
                    <th><?= __('Builder') ?></th>
                    <?php if ($currentUser->role === 'On-site Worker') {  ?>
                        <td><?= $project->has('user') ? $project->user->first_name . ' ' . $project->user->last_name : '' ?></td>
                    <?php } else { ?>
                        <td><?= $project->has('user') ? $this->Html->link("View Builder Information", ['controller' => 'Users', 'action' => 'view', $project->builder_id]) : '' ?></td>
                    <?php } ?>
                </tr> <?php } ?>
            <tr>
                <th><?= __('Site Address') ?></th>
                <td>
                    <?= h($project->address_no . ' ' . $project->address_street) ?><br />
                    <?= h($project->address_suburb) ?><br />
                    <?= h($state[$project->address_state] . ' ' . $project->address_postcode) ?>
                </td>
            </tr>
            <?php if ($project->has('user')) {
                if ($currentUser->id == $project->builder_id || $status == 'Co-Manager') { ?>
                    <tr>
                        <th><?= __('Client Details') ?></th>
                        <td>
                            <?= h($project->client_name) ?><br />
                            <?= h($project->client_email) ?><br />
                            <?= h($project->client_phone) ?>
                        </td>
                    </tr>
                <?php } else { ?>

                <?php } ?>
                <tr>
                    <th><?= __('Surveyor Details') ?></th>
                    <td>
                        <?= h($project->surveyor_name) ?><br />
                        <?= h($project->surveyor_email) ?><br />
                        <?= h($project->surveyor_phone) ?>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Start Date') ?></th>
                    <td><?= h($project->start_date) ?></td>
                </tr>
                <tr>
                    <th><?= __('Estimated Completion Date') ?></th>
                    <td><?= h($project->est_completion_date) ?></td>
                </tr>
                <tr>
                <?php } ?>
                <th><?= __('Status') ?></th>
                    <td style="color: <?= $project->status === 'Active' ? 'limegreen' : ($project->status === 'Cancelled' ? 'darkred' : 'black') ?>;">
                        <?= h($project->status) ?>
                    </td>
                </tr>
                <?php if ($project->status == 'Complete') { ?>
                    <tr>
                        <th>Completion Date</th>
                        <td><?= h($project->completion_date) ?></td>
                    </tr>
                <?php } ?>
                <?php if ($currentUser->role == 'Builder' && $project->status == 'Active') : ?>
                    <tr>
                        <th><?= __('Cancel Project') ?></th>
                        <td><?= $this->Html->link('Mark this project as cancelled', ['controller' => 'Projects', 'action' => 'cancelProject', $project->id], ['class' => 'btn btn-danger', 'confirm' => __('Are you sure you want to cancel {0}?', $project->name)]) ?></td>
                    </tr>
                <?php endif; ?>
        </table>

        <table class="table table-bordered" style="background-color:ghostwhite; max-width: 100%;">
            <tr>
                <th>Site View</th>
                <td>
                    <?= $this->Html->link(__('Site Live View and Check-in Record'), ['controller' => 'checkins', 'action' => 'checkin', '?' => ['project' => $project->id]], ['class' => 'btn btn-warning']) ?>
                    <?= $this->Html->link(__('QR Codes'), ['action' => 'generateqr', $project->id], ['class' => 'btn btn-warning']) ?>
                </td>
            </tr>
            <tr>
                <th>Project View</th>
                <td>
                    <?= $this->Html->link(__('Workers and Co-Managers'), ['controller' => 'ProjectsUsers', 'action' => 'index', $project->id], ['class' => 'btn btn-info']) ?>
                    <?= $this->Html->link(__('Associated Companies'), ['controller'=>'Companies','action'=> 'projectPartner',$project->id], ['class' => 'btn btn-info']) ?>
                    <?= $this->Html->link(__('Subcontracts'), ['controller' => 'subcontracts', 'action' => 'index', $project->id], ['class' => 'btn btn-info']) ?>
                </td>
            </tr>
            <tr>
                <th>Add to Project</th>
                <td>
                    <?= $this->Html->link(__('Worker'), ['controller' => 'Requests', 'action' => 'addprojectinvitation', $project->id], ['class' => 'btn btn-success']) ?>
                    <?= $this->Html->link(__('Company'), ['controller' => 'Requests', 'action' => 'inviteProjectCompany', $project->id], ['class' => 'btn btn-success']) ?>
                    <?= $this->Html->link(__('Documents'), ['controller' => 'documents', 'action' => 'add', '?' => ['project' => $project->id]], ['class' => 'btn btn-success']) ?>
                    <?= $this->Html->link(__('Equipment'), ['controller' => 'Equipment', 'action' => 'add', '?' => ['project' => $project->id]], ['class' => 'btn btn-success']) ?>
                </td>
            </tr>
        </table>

        <!-- INDUCTION DOCUMENTS -->
        <div class="related">
            <h4><?= __('Induction Documents') ?></h4>
            <?php if ($currentUser->id == $project->builder_id || $status == 'Co-Manager') : ?>
                <a class="btn btn-block btn-primary" style="width: 200px" href="<?= $this->Url->build(
                                                                                    ['controller' => 'documents', 'action' => 'add', '?' => ['project' => $project->id]]
                                                                                ) ?>">Add Induction Documents</a>
            <?php endif; ?>
            <div class="table-responsive">
                <br>
                <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                    <tr>
                        <th><?= __('Name') ?></th>
                        <th><?= __('Document Type') ?></th>
                        <th><?= __('Issue Date') ?></th>
                        <th><?= __('Expiry Date') ?></th>
                        <?php if ($currentUser->role != 'Builder' || $status == 'Co-Manager') : ?>
                            <th><?= __('Status') ?></th>
                        <?php endif; ?>
                        <th class="actions"><?= __('Actions') ?></th>
                    </tr>
                    <?php foreach ($documents as $document) : ?>
                        <tr>
                            <td>
                                <?= $this->Html->link(__($document->name), ['controller' => 'Documents', 'action' => 'view', $document->id]) ?>
                            </td>
                            <td><?= h($document->document_type) ?></td>
                            <td><?= h($document->issue_date) ?></td>
                            <td><?= h($document->expiry_date) ?></td>
                            <?php if ($currentUser->role != 'Builder' || $status == 'Co-Manager') : ?>
                                <td style="color: <?= $agreementStatus[$document->id] === 'Reviewed' ? 'green' : 'orange' ?>">
                                    <?= h($agreementStatus[$document->id]) ?>
                                </td>
                            <?php endif; ?>
                            <?php if ($document->archived == 0){ ?>
                            <td class="actions">
                                <?= $this->Html->link('<i class="fa fa-folder"></i> ' . __('View'), ['controller' => 'Documents', 'action' => 'view', $document->id], ['class' => 'btn btn-primary', 'escape' => false]) ?>
                                <?php if ($currentUser->id == $project->builder_id || $status == 'Co-Manager') : ?>
                                    <?= $this->Html->link('<i class="fa fa-pencil"></i> ' . __('Edit Details'), ['controller' => 'Documents', 'action' => 'edit', $document->id], ['class' => 'btn btn-warning', 'escape' => false]) ?>
                                    <?= $this->Html->link('<i class="fa fa-archive"></i> ' . __('Archive Document'), ['controller' => 'Documents', 'action' => 'delete', $document->id], ['confirm' => __('Are you sure you want to archive ' .$document->name) .'?', 'class' => 'btn btn-danger', 'escape' => false]) ?>
                                <?php endif; ?>
                            </td>
                            <?php } else{ ?>
                                <td class="actions">
                                    <?php if ($currentUser->id == $project->builder_id || $status == 'Co-Manager') { ?>
                                        <?= $this->Html->link('<i class="fa fa-archive"></i> ' . __('Unarchive Document'), ['controller' => 'Documents', 'action' => 'unarchived', $document->id], [ 'class' => 'btn btn-warning', 'escape' => false]) ?>
                                        <br />
                                    <?php } else{ echo 'Document has been archived'; }?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($documents) == 0) {
                        echo '<tr><td>No induction documents have been added.</td></tr>';
                    } ?>
                </table>
            </div>
        </div>

        <!-- COMPANY DOCUMENTS-->
        <?php if ($currentUser->id == $project->builder_id || $status == 'Co-Manager') : ?>
            <div class="related">
                <h4><?= __('Company Documents') ?></h4>
                <div class="table-responsive">
                    <table class="table table-bordered" style="background-color: ghostwhite; max-width: 100%">
                        <tr>
                            <th><?= __('Company Name') ?></th>
                            <th><?= __('Status') ?></th>
                        </tr>
                        <?php foreach ($companies as $company) : ?>
                            <td>
<!--                                <td>--><?php //= $this->Html->link(h($company->name), "/companies/view/" . $company->id . '?pj_id=' . $project->id) ?><!--</td>-->
                                <?= $this->Html->link(html_entity_decode($company->name), "/companies/view/" . $company->id . '?pj_id=' . $project->id) ?>
                                <td>
                                    <?php
                                    $allApproved = true;
                                    // Retrieve document statuses for the specified company, if available
                                    $userDocumentStatuses = $document_statuses[$company->id] ?? [];
                                    // Check if there are no document statuses
                                    if (empty($userDocumentStatuses)) {
                                        echo "No documents found.";
                                    } else {
                                        foreach ($userDocumentStatuses as $documentStatus) {
                                            // Check if the document status is not 'Reviewed' or 'Rejected'
                                            if ($documentStatus->status !== 'Reviewed' && $documentStatus->status !== 'Rejected' ) {
                                                $allApproved = false; // Set the flag to false if any document is pending
                                                break;
                                            }
                                        }
                                        if ($allApproved) {
                                            echo "All documents have been reviewed";
                                        } else {
                                            echo "Documents pending review";
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <!--
                            <?php if (count($documents) == 0) {
                                echo '<tr><td>No documents have been added.</td></tr>';
                            } ?> -->
                    </table>
                </div>
            </div>
        <?php endif; ?>


        <!-- INDUCTION REGISTER -->
        <?php if ($currentUser->id == $project->builder_id || $status == 'Co-Manager') : ?>
            <div class="related">

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h4><?= __('Induction Register') ?></h4>
                    <div class="custom-col text-right">
                        <div class="search-container">
                            <label for="user-search"></label>
                            <input type="text" id="user-search" placeholder="Search by name">
                            <i class="fa fa-search search-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" style="background-color: ghostwhite; max-width: 100%">
                        <tr>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Company') ?></th>
                            <th><?= __('Induction Date') ?></th>
                            <th><?= __('Status') ?></th>
                        </tr>
                        <?php
                        $prevUserId = null;
                        $allReviewed = true; // Assume all documents are reviewed initially

                        foreach ($projectsUsers as $projectsUser) :
                            if ($prevUserId !== $projectsUser->user_id) {
                                // Display the user's name and company name only once for each user
                        ?>
                                <tr class="user-row">
                                    <td><?= $this->Html->link(h($projectsUser->first_name . ' ' . $projectsUser->last_name), "/users/view/" . $projectsUser->user_id . '?pj_id=' . $project->id) ?></td>
                                    <td>
                                        <?php if (!empty($projectsUser->company_name)) : ?>
                                            <!--<a><?php /*= $this->Html->link(h($projectsUser->company_name), "/companies/view/" . $projectsUser->company_id . '?pj_id=' . $project->id) */?></a>-->
                                            <a><?= $this->Html->link(html_entity_decode($projectsUser->company_name), "/companies/view/" . $projectsUser->company_id . '?pj_id=' . $project->id) ?></a>
                                            <!--                                                <a href="/companies/view/--><?php //= $projectsUser->company_id
                                                                                                                            ?><!--">--><?php //= h($projectsUser->company_name)
                                                                                                                                        ?><!--</a>-->
                                        <?php else : ?>
                                            Worker is a sole contractor/NA
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($projectsUser->inducted_date != null) { ?>
                                        <td><?= h($projectsUser->inducted_date) ?></td>
                                    <?php } else {
                                        echo '<td>N/A</td>';
                                    }; ?>
                                    <td>
                                        <?php
                                        $allReviewed = true;
                                        $userDocumentStatuses = $documentStatuses[$projectsUser->user_id] ?? [];

                                        if (empty($userDocumentStatuses)) {
                                            echo "No documents found"; // Display when there are no document statuses
                                        } else {
                                            foreach ($userDocumentStatuses as $documentStatus) {
                                                if ($documentStatus['status'] !== 'Reviewed' && $documentStatus['status'] !== 'Rejected' ) {
                                                    $allReviewed = false;
                                                    break; // Exit the loop as soon as a pending document is found
                                                }
                                            }

                                            if ($allReviewed) {
                                                echo "All documents have been reviewed";
                                            } else {
                                                echo "Documents pending review";
                                            }
                                        }
                                        ?>
                                    </td>
                                </tr>
                        <?php
                                // Reset the flag for the next user
                                $allReviewed = true;
                            }

                            $prevUserId = $projectsUser->user_id;
                        endforeach;
                        ?>
                    </table>
                </div>
            </div>
        <?php endif; ?>


        <!-- CONTRACTOR COMPANY DOCUMENTS-->
        <div class="related">
            <?php if ($currentUser->role == 'Contractor') { ?>
                <h4><?= __('My Company Documents') ?></h4>
                <div class="table-responsive">

                    <table class="table table-bordered" style="background-color: ghostwhite; max-width: 100%">
                        <tr>
                            <th>Name</th>
                            <th>Document Type</th>
                            <th>Issue Date</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Comment</th>
                        </tr>
                        <?php if (!empty($companyDocument)) : ?>
                            <?php foreach ($companyDocument as $companyDocument) : ?>
                                <tr>
                                    <td>
                                        <?= $this->Html->link(
                                            h($companyDocument->name),
                                            ['controller' => 'Documents', 'action' => 'view', $companyDocument->document_id, '?' => ['pj_id' => $project->id]]
                                        ) ?>
                                    </td>
                                    <td><?= h($companyDocument->document_type) ?></td>
                                    <td><?= h($companyDocument->issue_date) ?></td>
                                    <td><?= h($companyDocument->expiry_date) ?></td>
                                    <td style="color: <?= $companyDocument->status === 'Reviewed' ? 'green' : ($companyDocument->status === 'Rejected' ? 'red' : 'orange') ?>">
                                        <?= h($companyDocument->status) ?>
                                    </td>
                                    <td><?= h($companyDocument->comment) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6">You have added no company documents to this project yet.</td>
                            </tr>
                        <?php endif; ?>

                    </table>
                </div>
            <?php } ?>
        </div>




        <!-- PERSONAL DOCUMENTS-->
        <div class="related">
            <?php if ($currentUser->role == 'On-site Worker') { ?>
                <h4><?= __('My Personal Documents') ?></h4>
                <div class="table-responsive">

                    <table class="table table-bordered" style="background-color: ghostwhite; max-width: 100%">
                        <tr>
                            <th>Name</th>
                            <th>Document Type</th>
                            <th>Issue Date</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Comment</th>
                        </tr>
                        <?php if (!empty($personalDocument)) : ?>
                            <?php foreach ($personalDocument as $personalDocument) : ?>
                                <tr>
                                    <td>
                                        <?= $this->Html->link(
                                            h($personalDocument->name),
                                            ['controller' => 'Documents', 'action' => 'view', $personalDocument->document_id, '?' => ['pj_id' => $project->id]]
                                            ) ?>
                                    </td>
                                    <td><?= h($personalDocument->document_type) ?></td>
                                    <td><?= h($personalDocument->issue_date) ?></td>
                                    <td><?= h($personalDocument->expiry_date) ?></td>
                                    <td style="color: <?= $personalDocument->status === 'Reviewed' ? 'green' : ($personalDocument->status === 'Rejected' ? 'red' : 'orange') ?>">
                                        <?= h($personalDocument->status) ?>
                                    </td>

                                    <td><?= h($personalDocument->comment) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6">You have added no personal documents to this project yet.</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            <?php } ?>
        </div>






        <!-- PLANT REGISTER (EQUIPMENT) -->
        <?php if ($currentUser->id == $project->builder_id || $status == 'Co-Manager') : ?>
            <div class="related">
                <h4><?= __('Plant Register') ?></h4>
                <div class="table-responsive">
                    <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                        <tr>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Hired From Date') ?></th>
                            <th><?= __('Hired Until Date') ?></th>
                            <th><?= __('Review Status') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($equipments as $equipment) : ?>
                            <tr>
                                <td>
                                    <?= $this->Html->link(__(h($equipment->name)), ['controller' => 'Equipment', 'action' => 'view', $equipment->id]) ?>
                                </td>
                                <td><?= h($equipment->hired_from_date) ?></td>
                                <td><?= h($equipment->hired_until_date) ?></td>
                                <td style="color: <?= ($equipment->review_status == 'Rejected') ? 'red' : (($equipment->review_status == 'Accepted') ? 'green' : 'orange'); ?>; font-weight: bold;">
                                    <?= h($equipment->review_status) ?>
                                </td>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['controller' => 'Equipment', 'action' => 'view', $equipment->id]) ?>
                                    <?php if ($currentUser->id == $project->builder_id || $status == 'Co-Manager') {
                                        // echo '<br/>' . $this->Html->link(__('Edit Details'), ['controller' => 'Equipment', 'action' => 'edit', $equipment->id]);
                                        echo '<br/>' . $this->Html->link(__('Disassociate from project'), ['controller' => 'Equipment', 'action' => 'disassociatefromproject', $equipment->id], [
                                            'confirm' => __('Are you sure you want to disassociate this equipment from the project?'),
                                        ]);
                                    } ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($equipments) == 0) {
                            echo '<tr><td>No induction equipments have been added.</td></tr>';
                        } ?>
                    </table>
                </div>
            </div>
            <!-- WORKER PLANT REGISTER (EQUIPMENT) -->
        <?php else : ?>
            <div class="related">
                <h4><?= __('My Equipment') ?></h4><a class="btn btn-block btn-primary" style="width: 200px" href="<?= $this->Url->build(
                                                                                                                        ['controller' => 'equipment', 'action' => 'add', '?' => ['project' => $project->id]]
                                                                                                                    ) ?>">Add Equipment</a>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered" style="background-color:ghostwhite; padding-left: 2.5%; padding-right: 2.5%; max-width: 100% ">
                        <tr>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Hired From Date') ?></th>
                            <th><?= __('Hired Until Date') ?></th>
                            <th><?= __('Review Status') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($myequipments as $equipment) : ?>
                            <tr>
                                <td><?= h($equipment->name) ?></td>
                                <td><?= h($equipment->hired_from_date) ?></td>
                                <td><?= h($equipment->hired_until_date) ?></td>
                                <td style="color: <?= ($equipment->review_status == 'Rejected') ? 'red' : (($equipment->review_status == 'Accepted') ? 'green' : 'orange'); ?>; font-weight: bold;">
                                    <?= h($equipment->review_status) ?>
                                </td>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['controller' => 'Equipment', 'action' => 'view', $equipment->id]) ?>
                                    <?= $this->Html->link(__('Edit'), ['controller' => 'Equipment', 'action' => 'edit', $equipment->id]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($equipments) == 0) {
                            echo '<tr><td>You have added no equipment to this project yet.</td></tr>';
                        } ?>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>

<style>
    .reviewed {
        color: green;
    }

    .rejected {
        color: red;
    }

    .button-link {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        /* Change this to your desired button color */
        color: #fff;
        /* Text color */
        text-decoration: none;
        /* Remove underline */
        border: none;
        border-radius: 5px;
        /* Rounded corners */
        cursor: pointer;
        transition: background-color 0.3s ease;
        /* Smooth hover effect */
    }

    .button-link:hover {
        background-color: #0056b3;
        /* Change the color on hover */
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById("user-search");
        const rows = document.querySelectorAll(".user-row");

        input.addEventListener("input", function() {
            const searchText = input.value.trim().toLowerCase();

            rows.forEach(function(row) {
                const username = row.querySelector("td:first-child").textContent.trim().toLowerCase();
                if (username.includes(searchText)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    });
</script>
