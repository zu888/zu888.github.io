<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Request> $requests
 * @var $companyRequests
 */

$currentUser = $this->request->getAttribute('identity');
?>

<div class="requests index content">
    <?php if($currentUser->role == 'Admin') { ?>
        <h3><?= __('Builder Requests') ?></h3>
        <br>
        <h4> Section Guide: </h4>
        <br>
        <h5> As an Admin on this page you will see all the requests from users requesting to have a 'Builder' account. </h5>
        <h5> You can approve the requests and when satisfied manually remove them from the screen </h5>
        <h5> You cannot see project requests as they are delivered to the assigned 'Builder' of each project </h5>
        <br>
    <?php } ?>
    <?php if($currentUser->role == 'Builder') { ?>
        <h3><?= __('Project Requests') ?></h3>
        <br>
        <h4> Section Guide: </h4>
        <br>
        <h5> As a Builder on this page you will see all the requests from users requesting to join projects which you are the assigned Builder. </h5>
        <h5> You can approve the requests and when satisfied manually remove them from the screen </h5>
        <h5> You cannot see Builder requests as they are delivered to the Admin of the system</h5>
        <br>
    <?php } ?>
    <?php if($currentUser->role != 'Builder' && $currentUser->role != 'Admin') { ?>
        <h3><?= __('Requested Projects') ?></h3>
        <br>
        <h4> Section Guide: </h4>
        <h5> As a User on this page you will see all the requests you made for joining a project </h5>
        <h5> You can view the status of your request and remove the request you made </h5>
        <h5> <strong>Note: If you cancel a pending request, you will need to request again for approval</strong></h5>
        <br>
    <?php } ?>
    <?php if($currentUser->role == 'Builder'||$currentUser->role === 'Admin') { ?>


        <div class="table-responsive">
            <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                <thead class="thead-dark">
                <tr>
                    <th><?= __('Requesting Party') ?></th>
                    <?php if ($currentUser->role == 'Builder') { ?>
                        <th><?= __('Project Requested') ?></th>
                    <?php } ?>
                    <?php if ($currentUser->role == 'Admin'){ ?>
                        <th><?= __('Building License') ?></th>
                    <?php }else{?>
                        <th><?= __('Company Working For') ?></th>
                        <th><?= __('Request Text') ?></th>
                    <?php } ?>
                    <th><?= __('Created At') ?></th>
                    <th><?= __('Approved At') ?></th>
                    <th>Approval Status</th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($requests as $request): ?>
                <?php if($request->request_type == 'Builder'){ ?>

                    <tr>
                        <td>
                            <?php
                            $userName = html_entity_decode($request->first_name . ' ' . $request->last_name);
                            $userLink = $this->Html->link($userName, ['controller' => 'Users', 'action' => 'view', $request->user_id]);
                            echo $userLink;
                            ?>
                        </td>

                        <td><?= h($request->request_text) ?></td>
                        <td><?= h($request->created_at) ?></td>
                        <td><?= h($request->approved_at) ?></td>
                        <td >
                            <?php
                            if ($request->approved_at != NULL) {
                                echo '<span style="color: limegreen">Request Approved</span>';
                            }elseif($request-> removal_status == 1 && $request->approved_at == NULL){
                                echo '<span style="color: darkred">Request Cancelled</span>';
                            }elseif($request-> removal_status == 2 && $request->approved_at == NULL){
                                echo '<span style="color: darkred">Request Rejected</span>';
                            }else {
                                echo '<span style="color: darkblue">Request Pending</span>';
                            }
                            ?>
                        </td>
                        <td class="actions">
                            <?php
                            if ($request->approved_at == NULL && $request->removal_status == 0) {
                                echo $this->Html->link(__('Approve'), [
                                    'action' => 'approveRequest',
                                    $request->id
                                ], [
                                    'class' => 'confirm-link',
                                    'data-action' => 'approve',
                                    'data-id' => $request->id
                                ]);
                                echo ' | ';
                                echo $this->Html->link(__("Reject"), [
                                    'controller' => 'Requests',
                                    'action' => 'reason',
                                    $request->id
                                ], [
                                    'class' => 'confirm-link',
                                    'data-action' => 'reject',
                                    'data-id' => $request->id
                                ]);
                            } elseif ($request->approved_at != NULL || $request->removal_status == 2) {
                                echo $this->Html->link(__('Delete'), [
                                    'action' => 'delete',
                                    $request->id
                                ], [
                                    'class' => 'confirm-link',
                                    'data-action' => 'delete',
                                    'data-id' => $request->id
                                ]);
                            }
                            ?>
                        </td>

                    </tr>


                    <?php } ?>
                <?php if($request->request_type == 'Project_Member' || $request->request_type == 'Project_Company'):?>
                    <tr>
                        <?php if($request->project_id && $request->company_id && $currentUser->role == 'Builder'):?>
                        <td>
                            <?php
                            $companyName = $request->company_name;
                            echo $this->Html->link(html_entity_decode($companyName), ['controller' => 'Companies', 'action' => 'view', ($request->company_id)]) ;
//                            $companyLink = $this->Html->link($comapanyName,"/companies/view/" . $request->company_id);
                           // echo $comapanyName;
                            ?>
                        </td>
                        <?php else:?>
                        <td>
                            <?php

                            $userName = h($request->first_name . ' ' . $request->last_name);
                            echo $this->Html->link(html_entity_decode($userName), ['controller' => 'Users', 'action' => 'view', ($request->user_id)]);
//                            $userLink = $this->Html->link($userName, "/users/view/" . $request->user_id);
                           // echo $userName ;
                            ?>
                        </td>
                        <?php endif;?>

                        <?php if ($currentUser->role == 'Builder') { ?>
                            <td>
                                <?php if ($request->project_id === null) {
                                    echo 'N/A';
                                } else {
                                    $projectLink = $this->Html->link(__(h($request->project_name)), "/projects/view/" . $request->project_id);
                                    echo $projectLink;
                                } ?>
                            </td>
                        <?php } ?>
                        <?php if ($request->request_type == 'Project_Member') { ?>
                            <td><?= $this->Html->link(h($request['workerCompanyName']), ['controller' => 'Companies', 'action' => 'view', ($request->worker_company)]) ;?></td>
                        <?php }else{ ?>
                            <td>  NA </td>
                        <?php } ?>
                        <td><?= h($request->request_text) ?></td>
                        <td><?= h($request->created_at) ?></td>
                        <td><?= h($request->approved_at) ?></td>

                        <td >
                            <?php
                            if ($request->approved_at != NULL) {
                                echo '<span style="color: limegreen">Request Approved</span>';
                            }elseif($request-> removal_status == 1 && $request->approved_at == NULL){
                                echo '<span style="color: darkred">Request Cancelled</span>';
                            }elseif($request-> removal_status == 2 && $request->approved_at == NULL){
                                echo '<span style="color: darkred">Request Rejected</span>';
                            }else {
                                echo '<span style="color: darkblue">Request Pending</span>';
                            }
                            ?>

                        </td>
                        <td class="actions">
                            <?php
                            if ($request->approved_at == NULL && $request->removal_status == 0) {
                                echo $this->Html->link(__('Approve'), [
                                    'confirm' => __('Are you sure you want to approve this request?'),
                                    'action' => 'approveRequest',
                                    $request->id
                                ]);
                                echo ' | ';
                                echo $this->Html->link(__("Reject"), [
                                    'controller' => 'Requests',
                                    'action' => 'reason',
                                    $request->id
                                ]);
                            }elseif ($request->approved_at != NULL|| $request->removal_status == 2) {
                                echo $this->Html->link(__('Delete Record'), ['confirm' => __('Are you sure you want to approve this request?'), 'action' => 'delete', $request->id], ['class' => 'btn btn-danger']);
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endif;?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <br>
    <?php } ?>

    <?php if($currentUser->role != 'Builder'&&$currentUser->role != 'Admin') { ?>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
            <thead class="thead-dark">
            <tr>
                <th><?= $this->Paginator->sort('project_id', 'Project Name') ?></th>
                <th><?= $this->Paginator->sort('created_at') ?></th>
                <th><?= $this->Paginator->sort('approved_at', 'Status') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>

            <tbody>
            <?php if(!empty($requests)){?>
            <?php foreach ($requests as $request): ?>

                <?php if($request->request_type == 'Project_Member' || $request->request_type == 'Project_Company'){?>
                <tr>
                    <td><?= h($request->project_name)?></td>
                    <td><?= h($request->created_at) ?></td>
                    <td >
                        <?php
                        if ($request->approved_at != NULL ) {
                            echo '<span style="color: limegreen">Request Approved</span>';
                        }elseif($request-> removal_status == 1 && $request->approved_at == NULL){
                            echo '<span style="color: darkred">Request Cancelled</span>';
                        }elseif($request-> removal_status == 2 && $request->approved_at == NULL){
                            echo '<span style="color: darkred">Request Rejected</span>';
                        }else {
                            echo '<span style="color: darkblue">Request Pending</span>';
                        }
                        ?>
                    </td>
                    <?php if($request->removal_status == 0&& $request->approved_at == NULL):?>
                        <td >
                            <?php
                            echo $this->Html->link(__("Cancel Request"), ['controller' => 'Requests', 'action' => 'removal', $request->Request_id], ['class' => 'btn btn-danger']);
                            ?>
                        </td >
                        <?php elseif($request->removal_status == 2):?>
                        <td >
                            <?php
                            echo $this->Form->postLink(__('View Comment'), ['controller' => 'Requests', 'action' => 'viewreason', $request->Request_id], [
                                'confirm' => __($request->comment, $request->id),
                                'class' => 'btn btn-primary'
                            ]);
                            echo ' ';                           ;
                            echo $this->Html->link(__("Delete"), ['controller' => 'Requests', 'action' => 'delete', $request->Request_id], [
                                'class' => 'btn btn-danger',
                                'style' => 'color: white'
                            ]);                            ?>
                        </td >
                    <?php elseif($request->removal_status == 0 && $request->approved_at !=null || $request->removal_status == 1): // approved request, removed request?>
                        <td >
                            <?php
                            echo $this->Html->link(__("Delete Record"), ['controller' => 'Requests', 'action' => 'delete', $request->Request_id], ['class' => 'btn btn-danger']);
                            ?>
                        </td >
                    <?php endif ?>
                    <?php } ?>

            <?php endforeach; ?>
                    <?php } ?>
            </tbody>
        </table>
    </div>
        <br>
        <?php } ?>




    <?php /*if($currentUser->role == 'Builder'||$currentUser->role === 'Admin') { ?>


        <div class="table-responsive">
            <table class="table table-bordered" style="background-color:ghostwhite; max-width:1000px">
                <thead class="thead-dark">
                <tr>
                    <th><?= $this->Paginator->sort('user_id', 'Requesting User') ?></th>
                    <?php if ($currentUser->role == 'Builder') { ?>
                        <th><?= $this->Paginator->sort('project_id', 'Project Requested') ?></th>
                    <?php } ?>
                    <th><?= $this->Paginator->sort('created_at') ?></th>
                    <th><?= $this->Paginator->sort('approved_at') ?></th>
                    <th>Approval Status</th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?= isset($request->user_id) ? $this->Html->link(__(h($request->first_name.' '.$request->last_name)), "/users/view/".$request->user_id) : ''?></td>
                        <?php if ($currentUser->role == 'Builder') { ?>
                            <?php if ($request->project_id == NULL) { ?>
                                <td>N/A</td>
                            <?php } ?>
                            <?php if ($request->project_id != NULL) { ?>
                                <td><?= isset($request->project_id) ? $this->Html->link(__(h($request->project_name)), "/projects/view/".$request->project_id) : ''?></td>
                            <?php } ?>
                        <?php } ?>
                        <td><?= h($request->created_at) ?></td>
                        <td><?= h($request->approved_at) ?></td>
                        <?php if ($request->approved_at != NULL) {
                            echo '<td style="color: limegreen">Request Approved</td>';
                        }?>
                        <?php if ($request->approved_at == NULL) {
                            echo '<td>'.$this->Html->link(__('Approve Request'), ['confirm' => __('Are you sure you want to approve this request?'),'action' => 'approveRequest', $request->id]).'</td>';}?>
                        <td class="actions" style="color: darkred"> <?= $this->Html->link(__("Remove"), ['controller' => 'Requests', 'action' => 'removal', $request->id]) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <br>
    <?php }*/ ?>
    <?php if($currentUser->role == 'Builder') { ?>
        <h3><?= __('Company Requests') ?></h3>
        <?php if($currentUser->role == 'Admin') { ?>
            <br>
            <h5> As an Admin on this page you will see all the company requests from 'Builders' requesting to join a specific company. </h5>
            <h5> You can approve the requests and when satisfied manually remove them from the screen </h5>
            <h5> You cannot see company requests from 'Contractors', 'On-Site Personnel' etc as they are delivered to the 'Builder' request page for them to approve </h5>
            <br>
        <?php } ?>
        <?php if($currentUser->role == 'Builder') { ?>
            <br>
            <h5> As a Builder on this page you will see all the company requests from users requesting to join companies which you are a part of. </h5>
            <h5> You can approve the requests and when satisfied manually remove them from the screen </h5>
            <h5> You cannot see a Builder requesting to join a company as they are delivered to the Admin of the system</h5>
            <br>
        <?php } ?>


        <div class="table-responsive">
            <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                <thead class="thead-dark">
                <tr>
                    <th><?= $this->Paginator->sort('user_id', 'Requesting Party') ?></th>
                    <th><?= $this->Paginator->sort('company_id', 'Company Requested') ?></th>
                    <th><?= __('Request Text') ?></th>
                    <th><?= $this->Paginator->sort('created_at') ?></th>
                    <th><?= $this->Paginator->sort('approved_at') ?></th>
                    <th>Approval Status</th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($companyRequests)):?>
                <?php foreach ($companyRequests as $request): ?>
                <?php if($request->request_type ==  'Company_Company'|| $request->request_type == 'Company_Member'):?>
                    <tr>
                        <?php if($request->request_type == 'Company_Company' ):?>

                            <td> <?= $this->Html->link(html_entity_decode($request['companyname']), ['controller' => 'Companies', 'action' => 'view', ($request['requestcompanyID'])]) ?></td>
                        <?php else:?>


                            <td> <?= $this->Html->link(html_entity_decode($request['first_name'] . ' ' . $request['last_name']), ['controller' => 'Users', 'action' => 'view', ($request->user_id)]) ?></td>
                        <?php endif;?>
                        <td> <?= $this->Html->link(h($request['company_name']), ['controller' => 'Companies', 'action' => 'view', ($request->company_id)]) ?></td>
                        <td><?=h($request['request_text'])?></td>
                        <td><?=h($request['created_at'])?></td>
                        <td><?=h($request['approved_at'])?></td>
                        <td >
                            <?php
                            if ($request->approved_at != NULL) {
                                echo '<span style="color: limegreen">Request Approved</span>';
                            }elseif($request['removal_status'] == 1 && $request['approved_at'] == NULL){
                                echo '<span style="color: darkred">Request Cancelled</span>';
                            }elseif($request['removal_status'] == 2 && $request['approved_at'] == NULL){
                                echo '<span style="color: darkred">Request Rejected</span>';
                            }else {
                                echo '<span style="color: darkblue">Request Pending</span>';
                            }
                            ?>
                        </td>
                        <td class="actions">
                            <?php
                            if ($request->approved_at == NULL && $request->removal_status == 0) {
                                echo $this->Html->link('<i class="fa fa-thumbs-up"></i> ' . __('Approve'), [
                                    'action' => 'approveRequest',
                                    $request->Request_id
                                ], [
                                    'escape' => false,
                                    'class' => 'btn btn-success',
                                    'confirm' => __('Are you sure you want to approve this request?')
                                ]);
                                echo ' ';
                                echo $this->Html->link('<i class="fa fa-thumbs-down"></i> ' . __('Reject'), [
                                    'controller' => 'Requests',
                                    'action' => 'reason',
                                    $request->Request_id
                                ], [
                                    'escape' => false,
                                    'class' => 'btn btn-danger'
                                ]);
                            }elseif ($request->approved_at != NULL|| $request->removal_status == 2) {
                                echo $this->Html->link(__('Delete Record'), ['confirm' => __('Are you sure you want to remove this record of this request?'), 'action' => 'delete', $request->Request_id], ['class' => 'btn btn-danger']);
                            }
                            ?>
                        </td>
                    </tr>
                <?php endif ?>
                <?php endforeach; ?>
                <?php endif ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

    <?php if($currentUser->role != 'Builder' && $currentUser->role != 'Admin') { ?>
        <h3><?= __('Requested Companies') ?></h3>
        <br>
        <h4> Section Guide: </h4>
        <h5> As a User on this section you will see all the requests you made for joining a company </h5>
        <h5> You can view the status of your request and remove the request you made </h5>
        <h5> <strong>Note: Removing the pending request, you will need to request again for approval</strong></h5>


    <?php } ?>

    <?php if($currentUser->role != 'Builder'&&$currentUser->role != 'Admin') { ?>

        <div class="table-responsive">
            <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                <thead class="thead-dark">
                <tr>
                    <th><?= $this->Paginator->sort('company_id', 'Company Requested') ?></th>
                    <th><?= $this->Paginator->sort('created_at') ?></th>
                    <th><?= $this->Paginator->sort('approved_at', 'Status') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($requests)){?>
                <?php foreach ($requests as $request): ?>
                     <?php if($request->request_type == 'Company_Member'|| $request->request_type=='Company_Company'){?>
                    <tr>
                        <td><?= h($request->company_name)?></td>
                        <td><?= h($request->created_at) ?></td>
                        <td >
                            <?php
                            if ($request->approved_at != NULL) {
                                echo '<span style="color: limegreen">Request Approved</span>';
                            }elseif($request-> removal_status == 1 && $request->approved_at == NULL){
                                echo '<span style="color: darkred">Request Cancelled</span>';
                            }elseif($request-> removal_status == 2 && $request->approved_at == NULL){
                                echo '<span style="color: darkred">Request Rejected</span>';
                            }else {
                                echo '<span style="color: darkblue">Request Pending</span>';
                            }
                            ?>
                        </td>
                        <?php if($request->removal_status == 0 && $request->approved_at == NULL): //Pending request?>
                            <td >
                                <?php
                                echo $this->Html->link(__("Cancel Request"), ['controller' => 'Requests', 'action' => 'removal', $request->Request_id], ['class' => 'btn btn-danger']);
                                ?>
                            </td >
                        <?php elseif($request->removal_status == 2):?>
                            <td >
                                <?php
                                echo $this->Form->postLink(__('View Comment'), ['controller' => 'Requests', 'action' => 'viewreason', $request->Request_id], [
                                    'confirm' => __($request->comment, $request->id),
                                    'class' => 'btn btn-primary'
                                ]);
                                echo ' ';
                                echo $this->Html->link(__("Delete"), ['controller' => 'Requests', 'action' => 'delete', $request->Request_id], [
                                    'class' => 'btn btn-danger',
                                    'style' => 'color: white'
                                ]);                                ?>
                            </td >
                        <?php else: // approved request, removed request?>
                        <td >
                            <?php
                            echo $this->Html->link(__("Delete Record"), ['controller' => 'Requests', 'action' => 'delete', $request->Request_id], ['class' => 'btn btn-danger']);
                            ?>
                        </td >
                        <?php endif ?>
                    </tr>
                    <?php } ?>

                <?php endforeach; ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

    <?php if($currentUser->role == 'Contractor') { ?>
    <h3><?= __('Builder Request') ?></h3>
        <br>
        <h4> Section Guide: </h4>
        <h5> As a Contractor on this section you will see the request you made for becoming a builder </h5>
        <h5> You can view the status of your request and remove the request you made </h5>
        <h5> <strong>Note: Removing the pending request, you will need to request again for approval</strong></h5>

        <div class="table-responsive">
            <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                <thead class="thead-dark">
                <tr>
                    <th><?= $this->Paginator->sort('created_at') ?></th>
                    <th><?= $this->Paginator->sort('approved_at', 'Status') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($builderRequests as $request): ?>
                    <?php if($request->request_type == 'Builder'){?>
                        <tr>
                            <td><?= h($request->created_at) ?></td>
                            <td >
                                <?php
                                if ($request->approved_at != NULL) {
                                    echo '<span style="color: limegreen">Request Approved</span>';
                                }elseif($request-> removal_status == 1 && $request->approved_at == NULL){
                                    echo '<span style="color: darkred">Request Cancelled</span>';
                                }elseif($request-> removal_status == 2 && $request->approved_at == NULL){
                                    echo '<span style="color: darkred">Request Rejected</span>';
                                }else {
                                    echo '<span style="color: darkblue">Request Pending</span>';
                                }
                                ?>
                            </td>
                            <?php if($request->removal_status == 0 && $request->approved_at == NULL): //Pending request?>
                                <td >
                                    <?php
                                    echo $this->Html->link(__("Cancel Request"), ['controller' => 'Requests', 'action' => 'removal', $request->id], ['class' => 'btn btn-danger']);
                                    ?>
                                </td >
                            <?php elseif($request->removal_status == 2):?>
                                <td >
                                    <?php
                                    echo $this->Form->postLink(__('View Comment'), ['controller' => 'Requests', 'action' => 'viewreason', $request->id], [
                                        'confirm' => __($request->comment, $request->id),
                                        'class' => 'btn btn-primary'
                                    ]);
                                    echo ' ';
                                    echo $this->Html->link(__("Delete"), ['controller' => 'Requests', 'action' => 'delete', $request->id], [
                                        'class' => 'btn btn-danger',
                                        'style' => 'color: white'
                                    ]);                                    ?>
                                </td >
                            <?php else: // approved request, removed request?>
                                <td >
                                    <?php
                                    echo $this->Html->link(__("Delete Record"), ['controller' => 'Requests', 'action' => 'delete', $request->id], ['class' => 'btn btn-danger']);
                                    ?>
                                </td >
                            <?php endif ?>
                        </tr>
                    <?php } ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmLinks = document.querySelectorAll('.confirm-link');
        confirmLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const action = link.getAttribute('data-action');
                const requestId = link.getAttribute('data-id');
                const confirmation = confirm('Are you sure you want to ' + action + ' this request? This action cannot be undone.');
                if (confirmation) {
                    // Handle the action based on user's choice
                    if (action === 'approve') {
                        window.location.href = '<?= $this->Url->build([
                            'action' => 'approveRequest'
                        ]) ?>/' + requestId;
                    } else if (action === 'reject') {
                        window.location.href = '<?= $this->Url->build([
                            'controller' => 'Requests',
                            'action' => 'reason'
                        ]) ?>/' + requestId;
                    } else if (action === 'delete') {
                        window.location.href = '<?= $this->Url->build([
                            'action' => 'delete'
                        ]) ?>/' + requestId;
                    }
                }
            });
        });
    });
</script>
