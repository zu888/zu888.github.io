<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Request> $requests
 * @var $companyRequests
 */

$currentUser = $this->request->getAttribute('identity');
?>
<div class="requests index content">
    <h3><?= __('Project Member Invitation') ?></h3>
    <h4> Section Guide: </h4>
    <h5> On this page, you can see all the project invitation you sent to the specific member. </h5>
    <h5> You can view the invitation status and when satisfied manually remove them from the screen </h5>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite;">
            <thead class="thead-dark">
            <tr>
                <th><?= $this->Paginator->sort('project_id', 'Project') ?></th>
                <th><?= $this->Paginator->sort('user_id', 'Member') ?></th>
                <th><?= $this->Paginator->sort('company_id', 'Company Working For') ?></th>
                <th><?= $this->Paginator->sort('created_at') ?></th>
                <th><?= $this->Paginator->sort('approved_at') ?></th>
                <th>Approval Status</th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($project_invitation)):?>
            <?php foreach ($project_invitation as $invitation): ?>

                <?php if($invitation->request_type == 'Project_Member_Invitation' ){?>

                    <tr>
                        <td>
                            <?php if ($invitation->projects_project_id === null) {
                                echo 'N/A';
                            } else {
                                $projectLink = $this->Html->link(__(h($invitation->project_name)), "/projects/view/" . $invitation->projects_project_id);
                                echo $projectLink;
                            } ?>
                        </td>

                      <td><?= $this->Html->link(html_entity_decode($invitation->first_name . ' ' . $invitation->last_name), ['controller' => 'Users', 'action' => 'view', ($invitation->user_id)])?></td>
                        <td><?= $this->Html->link(html_entity_decode($invitation->worker_company_name), ['controller' => 'Companies', 'action' => 'view', ($invitation->worker_company)])?></td>
                        <td><?= h($invitation->created_at) ?></td>
                        <td><?= h($invitation->approved_at) ?></td>
                        <?php if ($invitation->approved_at != NULL) {
                            echo '<td style="color: limegreen">Request Approved</td>';}?>
                        <?php if ($invitation->approved_at == NULL && $invitation->removal_status == 2) {
                            echo '<td style="color: darkred">Request Rejected</td>';}?>
                        <?php if ($invitation->approved_at == NULL && $invitation->removal_status == 0 ) {
                            echo '<td style="color: darkblue">Request Pending</td>';}?>

                        <?php if($invitation->removal_status == 0 && $invitation->approved_at == NULL): //Pending request?>
                        <td >
                            <?php
                            echo $this->Html->link(__("Remove"), ['controller' => 'Requests', 'action' => 'removal', $invitation->id], ['style' => 'color: darkred']);
                            ?>
                        </td >
                        <?php elseif($invitation->removal_status == 2):?>
                            <td >
                                <?php
                                echo $this->Form->postLink(__('View Comment'), ['controller' => 'Requests', 'action' => 'viewreason', $invitation->id],['confirm' => __($invitation->comment, $invitation->id)]);
                                echo '  |  ';
                                echo $this->Html->link(__("Delete"), ['controller' => 'Requests', 'action' => 'delete', $invitation->id], ['style' => 'color: darkred']);
                                ?>
                            </td >
                    <?php else: // approved request, removed request?>
                        <td >
                            <?php
                            echo $this->Html->link(__("Delete"), ['controller' => 'Requests', 'action' => 'delete', $invitation->id], ['style' => 'color: darkred']);
                            ?>
                        </td >
                    <?php endif ?>
                        </td>
                    </tr>
                <?php }?>
            <?php endforeach; ?>
            <?php endif ?>
            </tbody>
        </table>
    </div>

    <h3><?= __('Project Associated Company Invitation') ?></h3>
    <h4> Section Guide: </h4>
    <h5> On this page, you can see all the project invitation you sent to an associated company. </h5>
    <h5> You can view the invitation status and when satisfied manually remove them from the screen </h5>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite;">
            <thead class="thead-dark">
            <tr>
                <th><?= $this->Paginator->sort('project_id', 'Project') ?></th>
                <th><?= $this->Paginator->sort('company_id', 'Company') ?></th>
                <th><?= $this->Paginator->sort('created_at') ?></th>
                <th><?= $this->Paginator->sort('approved_at') ?></th>
                <th>Approval Status</th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($project_invitation)):?>
            <?php foreach ($project_invitation as $invitation): ?>

                <?php if($invitation->request_type == 'Project_Company_Invitation'){?>

                    <tr>
                        <!--                    <td>--><?php //= isset($request->user_id) ? $this->Html->link(__(h($request->first_name.' '.$request->last_name)), "/users/view/".$request->user_id) : ''?><!--</td>-->
                        <!--                    <td>--><?php //= isset($request->company_id) ? $this->Html->link(__(h($request->company_name)), "/companies/view/".$request->company_id) : ''?><!--</td>-->
                        <tr>
                            <td>
                                <?php if ($invitation->projects_project_id === null) {
                                    echo 'N/A';
                                } else {
                                    $projectLink = $this->Html->link(__(html_entity_decode($invitation->project_name)), "/projects/view/" . $invitation->projects_project_id);
                                    echo $projectLink;
                                } ?>
                            </td>
                            <td><?= $this->Html->link(html_entity_decode($invitation->company_name), ['controller' => 'Companies', 'action' => 'view', ($invitation->company_id)])?></td>
                        <td><?= h($invitation->created_at) ?></td>
                        <td><?= h($invitation->approved_at) ?></td>
                        <?php if ($invitation->approved_at != NULL) {
                            echo '<td style="color: limegreen">Request Approved</td>';}?>
                        <?php if ($invitation->approved_at == NULL && $invitation->removal_status == 2) {
                            echo '<td style="color: darkred">Request Rejected</td>';}?>
                        <?php if ($invitation->approved_at == NULL && $invitation->removal_status == 0 ) {
                            echo '<td style="color: darkblue">Request Pending</td>';}?>

                        <?php if($invitation->removal_status == 0 && $invitation->approved_at == NULL): //Pending request?>
                            <td >
                                <?php
                                echo $this->Html->link(__("Remove"), ['controller' => 'Requests', 'action' => 'removal', $invitation->id], ['style' => 'color: darkred']);
                                ?>
                            </td >
                        <?php elseif($invitation->removal_status == 2):?>
                            <td >
                                <?php
                                echo $this->Form->postLink(__('View Comment'), ['controller' => 'Requests', 'action' => 'viewreason', $invitation->id],['confirm' => __($invitation->comment, $invitation->id)]);
                                echo '  |  ';
                                echo $this->Html->link(__("Delete"), ['controller' => 'Requests', 'action' => 'delete', $invitation->id], ['style' => 'color: darkred']);
                                ?>
                            </td >
                        <?php else: // approved request, removed request?>
                            <td >
                                <?php
                                echo $this->Html->link(__("Delete Records"), ['controller' => 'Requests', 'action' => 'delete', $invitation->id], ['class' => 'btn btn-danger']);
                                ?>
                            </td >
                        <?php endif ?>

                        </td>




                    </tr>
                <?php }?>
            <?php endforeach; ?>
            <?php endif ?>
            </tbody>

        </table>
    </div>



</div>
