<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Request> $requests
 * @var $companyRequests
 */

$currentUser = $this->request->getAttribute('identity');
?>
<div class="requests index content">
    <?php if($currentUser->role == 'On-site Worker'){ ?>
    <h3><?= __('Project Invitation For Member') ?></h3>
    <h4> Section Guide: </h4>
    <h5> On this page, you can see all the project invitation sent from the associated builder. </h5>
    <h5> You can approve the requests and when satisfied manually remove them from the screen </h5>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite;">
            <thead class="thead-dark">
            <tr>
                <th><?= $this->Paginator->sort('user_id', 'Project') ?></th>
                <th><?= $this->Paginator->sort('company_id', 'Builder') ?></th>
                <th><?= $this->Paginator->sort('company_name', 'Company I Working For') ?></th>
                <th><?= $this->Paginator->sort('created_at') ?></th>
                <th><?= $this->Paginator->sort('approved_at') ?></th>
                <th>Approval Status</th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($project_invitation as $invitation): ?>

                    <?php if($invitation->request_type == 'Project_Member_Invitation'){?>

                    <tr>
                        <!--                    <td>--><?php //= isset($request->user_id) ? $this->Html->link(__(h($request->first_name.' '.$request->last_name)), "/users/view/".$request->user_id) : ''?><!--</td>-->
                        <!--                    <td>--><?php //= isset($request->company_id) ? $this->Html->link(__(h($request->company_name)), "/companies/view/".$request->company_id) : ''?><!--</td>-->
                        <td><?=h($invitation['project_name'])?></td>
                        <td><?= $this->Html->link(html_entity_decode($invitation['builder_first_name'] . ' ' . $invitation['builder_last_name']), ['controller' => 'Users', 'action' => 'view', ($invitation['builder_id'])])?></td>
                        <td><?= $this->Html->link(html_entity_decode($invitation['worker_company_name']), ['controller' => 'Companies', 'action' => 'view', ($invitation['worker_company'])])?></td>
                        <td><?= h($invitation['created_at']) ?></td>
                        <td><?= h($invitation['approved_at']) ?></td>
                        <?php if ($invitation['approved_at'] != NULL) {
                            echo '<td style="color: limegreen">Request Approved</td>';}?>
                        <?php if ($invitation['approved_at'] == NULL && $invitation['removal_status'] == 2) {
                            echo '<td style="color: darkred">Request Rejected</td>';}?>
                        <?php if ($invitation['approved_at'] == NULL && $invitation['removal_status'] == 0 ) {
                            echo '<td style="color: darkblue">Await for Action</td>';}?>

                        <td>
                            <?php
                            if ($invitation->approved_at == NULL && $invitation->removal_status == 0) {
                                echo $this->Html->link('<i class="fa fa-check"></i> ' . __('Approve'), [
                                    'action' => 'approveRequest',
                                    $invitation->id
                                ], [
                                    'escape' => false,
                                    'class' => 'btn btn-success',
                                    'confirm' => __('Are you sure you want to approve this request?')
                                ]);
                                echo ' ';
                                echo $this->Html->link('<i class="fa fa-times"></i> ' . __('Reject'), [
                                    'controller' => 'Requests',
                                    'action' => 'reason',
                                    $invitation->id
                                ], [
                                    'escape' => false,
                                    'class' => 'btn btn-danger'
                                ]);
                            }elseif ($invitation->approved_at != NULL|| $invitation->removal_status == 2) {
                                echo $this->Html->link(__('Delete Record'), ['confirm' => __('Are you sure you want to approve this request?'), 'action' => 'delete', $invitation->id], ['class' => 'btn btn-danger']);
                            }
                            ?>
                        </td>




                    </tr>
                <?php }?>
            <?php endforeach; ?>

            </tbody>

        </table>
    </div>
    <?php }?>
    <?php if($ownacompany){ ?>
    <h3><?= __('Project Invitation For Company') ?></h3>
    <h4> Section Guide: </h4>
    <h5> On this page, you can see all the project invitation sent from the associated builder. </h5>
    <h5> You can approve the requests and when satisfied manually remove them from the screen </h5>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite;">
            <thead class="thead-dark">
            <tr>
                <th><?= $this->Paginator->sort('user_id', 'Project') ?></th>
                <th><?= $this->Paginator->sort('company_id', 'Builder') ?></th>
                <th><?= $this->Paginator->sort('created_at') ?></th>
                <th><?= $this->Paginator->sort('approved_at') ?></th>
                <th>Approval Status</th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($project_invitation as $invitation): ?>

                <?php if($invitation->request_type == 'Project_Company_Invitation'){?>

                    <tr>
                        <!--                    <td>--><?php //= isset($request->user_id) ? $this->Html->link(__(h($request->first_name.' '.$request->last_name)), "/users/view/".$request->user_id) : ''?><!--</td>-->
                        <!--                    <td>--><?php //= isset($request->company_id) ? $this->Html->link(__(h($request->company_name)), "/companies/view/".$request->company_id) : ''?><!--</td>-->
                        <td><?=h($invitation['project_name'])?></td>

                        <td><?= $this->Html->link(html_entity_decode($invitation['builder_first_name'] . ' ' . $invitation['builder_last_name']), ['controller' => 'Users', 'action' => 'view', ($invitation['builder_id'])])?></td>
                        <td><?= h($invitation['created_at']) ?></td>
                        <td><?= h($invitation['approved_at']) ?></td>
                        <?php if ($invitation['approved_at'] != NULL) {
                            echo '<td style="color: limegreen">Request Approved</td>';}?>
                        <?php if ($invitation['approved_at'] == NULL && $invitation['removal_status'] == 2) {
                            echo '<td style="color: darkred">Request Rejected</td>';}?>
                        <?php if ($invitation['approved_at'] == NULL && $invitation['removal_status'] == 0 ) {
                            echo '<td style="color: darkblue">Await for Action</td>';}?>

                        <td>
                            <?php
                            if ($invitation->approved_at == NULL && $invitation->removal_status == 0) {
                                echo $this->Html->link('<i class="fa fa-check"></i> ' . __('Approve'), [
                                    'action' => 'approveRequest',
                                    $invitation->id
                                ], [
                                    'escape' => false,
                                    'class' => 'btn btn-success',
                                    'confirm' => __('Are you sure you want to approve this request?')
                                ]);
                                echo ' ';
                                echo $this->Html->link('<i class="fa fa-times"></i> ' . __('Reject'), [
                                    'controller' => 'Requests',
                                    'action' => 'reason',
                                    $invitation->id
                                ], [
                                    'escape' => false,
                                    'class' => 'btn btn-danger'
                                ]);
                            }elseif ($invitation->approved_at != NULL|| $invitation->removal_status == 2) {
                                echo $this->Html->link(__('Delete Record'), ['confirm' => __('Are you sure you want to approve this request?'), 'action' => 'delete', $invitation->id], ['class' => 'btn btn-danger']);
                            }
                            ?>
                        </td>




                    </tr>
                <?php }?>
            <?php endforeach; ?>

            </tbody>

        </table>
    </div>
    <?php }?>
    <?php if($currentUser->role == 'On-site Worker'){ ?>
        <h3><?= __('Company Invitation For Member') ?></h3>
        <h4> Section Guide: </h4>
        <h5> On this page, you can see all the member invitation sent from companies. </h5>
        <h5> You can approve the requests and when satisfied manually remove them from the screen </h5>
        <br>
        <div class="table-responsive">
            <table class="table table-bordered" style="background-color:ghostwhite;">
                <thead class="thead-dark">
                <tr>
                    <th><?= $this->Paginator->sort('company_id', 'Company') ?></th>
                    <th><?= $this->Paginator->sort('admin_id', 'Company Admin') ?></th>
                    <th><?= $this->Paginator->sort('created_at') ?></th>
                    <th><?= $this->Paginator->sort('approved_at') ?></th>
                    <th>Approval Status</th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($company_invitation as $invitation): ?>

                    <?php if($invitation->request_type == 'Company_Member_Invitation'){?>

                        <tr>
                            <!--                    <td>--><?php //= isset($request->user_id) ? $this->Html->link(__(h($request->first_name.' '.$request->last_name)), "/users/view/".$request->user_id) : ''?><!--</td>-->
                            <!--                    <td>--><?php //= isset($request->company_id) ? $this->Html->link(__(h($request->company_name)), "/companies/view/".$request->company_id) : ''?><!--</td>-->
                            <td><?= $this->Html->link(html_entity_decode($invitation['company_name']), ['controller' => 'Companies', 'action' => 'view', ($invitation['company_id'])])?></td>

                            <td><?= $this->Html->link(html_entity_decode($invitation['admin_first_name'] . ' ' . $invitation['admin_last_name']), ['controller' => 'Users', 'action' => 'view', ($invitation['admin_id'])])?></td>
                            <td><?= h($invitation['created_at']) ?></td>
                            <td><?= h($invitation['approved_at']) ?></td>
                            <?php if ($invitation['approved_at'] != NULL) {
                                echo '<td style="color: limegreen">Request Approved</td>';}?>
                            <?php if ($invitation['approved_at'] == NULL && $invitation['removal_status'] == 2) {
                                echo '<td style="color: darkred">Request Rejected</td>';}?>
                            <?php if ($invitation['approved_at'] == NULL && $invitation['removal_status'] == 0 ) {
                                echo '<td style="color: darkblue">Await for Action</td>';}?>

                            <td>
                                <?php
                                if ($invitation->approved_at == NULL && $invitation->removal_status == 0) {
                                    echo $this->Html->link('<i class="fa fa-check"></i> ' . __('Approve'), [
                                        'action' => 'approveRequest',
                                        $invitation->id
                                    ], [
                                        'escape' => false,
                                        'class' => 'btn btn-success',
                                        'confirm' => __('Are you sure you want to approve this request?')
                                    ]);
                                    echo ' ';
                                    echo $this->Html->link('<i class="fa fa-times"></i> ' . __('Reject'), [
                                        'controller' => 'Requests',
                                        'action' => 'reason',
                                        $invitation->id
                                    ], [
                                        'escape' => false,
                                        'class' => 'btn btn-danger'
                                    ]);
                                }elseif ($invitation->approved_at != NULL|| $invitation->removal_status == 2) {
                                    echo $this->Html->link(__('Delete Record'), ['confirm' => __('Are you sure you want to delete this request?'), 'action' => 'delete', $invitation->id], ['class' => 'btn btn-danger']);
                                }
                                ?>
                            </td>
                        </tr>
                    <?php }?>
                <?php endforeach; ?>

                </tbody>

            </table>
        </div>
    <?php }?>
    <?php if($ownacompany){ ?>
        <h3><?= __('Company Invitation For Company') ?></h3>
        <h4> Section Guide: </h4>
        <h5> On this page, you can see all the company invitation sent from companies. </h5>
        <h5> You can approve the requests and when satisfied manually remove them from the screen </h5>
        <br>
        <div class="table-responsive">
            <table class="table table-bordered" style="background-color:ghostwhite;">
                <thead class="thead-dark">
                <tr>
                    <th><?= $this->Paginator->sort('company_id', 'Company') ?></th>
                    <th><?= $this->Paginator->sort('admin_id', 'Company Admin') ?></th>
                    <th><?= $this->Paginator->sort('created_at') ?></th>
                    <th><?= $this->Paginator->sort('approved_at') ?></th>
                    <th>Approval Status</th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($company_invitation as $invitation): ?>

                    <?php if($invitation->request_type == 'Company_Company_Invitation'){?>

                        <tr>
                            <!--                    <td>--><?php //= isset($request->user_id) ? $this->Html->link(__(h($request->first_name.' '.$request->last_name)), "/users/view/".$request->user_id) : ''?><!--</td>-->
                            <!--                    <td>--><?php //= isset($request->company_id) ? $this->Html->link(__(h($request->company_name)), "/companies/view/".$request->company_id) : ''?><!--</td>-->

                            <td><?= $this->Html->link(html_entity_decode($invitation['company_name']), ['controller' => 'Companies', 'action' => 'view', ($invitation['company_id'])])?></td>

                            <td><?= $this->Html->link(html_entity_decode($invitation['admin_first_name'] . ' ' . $invitation['admin_last_name']), ['controller' => 'Users', 'action' => 'view', ($invitation['admin_id'])])?></td>
                            <td><?= h($invitation['created_at']) ?></td>
                            <td><?= h($invitation['approved_at']) ?></td>
                            <?php if ($invitation['approved_at'] != NULL) {
                                echo '<td style="color: limegreen">Request Approved</td>';}?>
                            <?php if ($invitation['approved_at'] == NULL && $invitation['removal_status'] == 2) {
                                echo '<td style="color: darkred">Request Rejected</td>';}?>
                            <?php if ($invitation['approved_at'] == NULL && $invitation['removal_status'] == 0 ) {
                                echo '<td style="color: darkblue">Await for Action</td>';}?>

                            <td>
                                <?php
                                if ($invitation->approved_at == NULL && $invitation->removal_status == 0) {
                                    echo $this->Html->link('<i class="fa fa-check"></i> ' . __('Approve'), [
                                        'action' => 'approveRequest',
                                        $invitation->id
                                    ], [
                                        'escape' => false,
                                        'class' => 'btn btn-success',
                                        'confirm' => __('Are you sure you want to approve this request?')
                                    ]);
                                    echo ' ';
                                    echo $this->Html->link('<i class="fa fa-times"></i> ' . __('Reject'), [
                                        'controller' => 'Requests',
                                        'action' => 'reason',
                                        $invitation->id
                                    ], [
                                        'escape' => false,
                                        'class' => 'btn btn-danger'
                                    ]);
                                }elseif ($invitation->approved_at != NULL|| $invitation->removal_status == 2) {
                                    echo $this->Html->link(__('Delete Record'), ['confirm' => __('Are you sure you want to approve this request?'), 'action' => 'delete', $invitation->id], ['class' => 'btn btn-danger']);
                                }
                                ?>
                            </td>
                        </tr>
                    <?php }?>
                <?php endforeach; ?>

                </tbody>

            </table>
        </div>

    <?php }?>

</div>
