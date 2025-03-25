<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Request> $requests
 * @var $companyRequests
 */

$currentUser = $this->request->getAttribute('identity');
?>
<div class="requests index content">
    <h3><?= __('Company Requests') ?></h3>
    <br>
    <h4> Page Guide: </h4>
    <h5> As a Company Owner on this page you will see all the company requests from users requesting to join companies which you are a part of. </h5>
    <h5> You can approve the requests and when satisfied manually remove them from the screen </h5>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite;">
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
            <?php foreach ($companyRequests as $request): ?>
            <?php if($request->admin_id == $currentUser->id):?>

                <?php if($request->request_type =='Company_Member' ||$request->request_type == 'Company_Company' ):?>
             <?php if($currentUser->id != $request->user_id){?>
                <tr>
<!--                    <td>--><?php //= isset($request->user_id) ? $this->Html->link(__(h($request->first_name.' '.$request->last_name)), "/users/view/".$request->user_id) : ''?><!--</td>-->
<!--                    <td>--><?php //= isset($request->company_id) ? $this->Html->link(__(h($request->company_name)), "/companies/view/".$request->company_id) : ''?><!--</td>-->
                    <?php if($request->request_type == 'Company_Company' ):?>
                        <td><?= $this->Html->link(html_entity_decode($request['companyname']), ['controller' => 'Companies', 'action' => 'view', ($request['requestedCompanyID'])])?></td>

                    <?php else:?>
                        <td><?= $this->Html->link(html_entity_decode($request['first_name'] . ' ' . $request['last_name']), ['controller' => 'Users', 'action' => 'view', ($request->user_id)])?></td>

                    <?php endif;?>
                    <td><?= $this->Html->link(html_entity_decode($request['company_name']), ['controller' => 'Companies', 'action' => 'view', ($request['company_id'])])?></td>
                    <td><?=h($request['request_text'])?></td>
                    <td><?=h($request['created_at'])?></td>
                    <td><?=h($request['approved_at'])?></td>
                    <?php if ($request['approved_at'] != NULL) {
                        echo '<td style="color: limegreen">Request Approved</td>';}?>
                    <?php if ($request['approved_at'] == NULL && $request['removal_status'] == 2) {
                        echo '<td style="color: darkred">Request Rejected</td>';}?>
                    <?php if ($request['approved_at'] == NULL && $request['removal_status'] == 0 ) {
                        echo '<td style="color: darkblue">Await for Action</td>';}?>

                    <td>
                        <?php
                        if ($request->approved_at == NULL && $request->removal_status == 0) {
                            echo $this->Html->link('<i class="fa fa-thumbs-up"></i> ' . __('Approve'), [
                                'action' => 'approveRequest',
                                $request->id
                            ], [
                                'escape' => false,
                                'class' => 'btn btn-success',
                                'confirm' => __('Are you sure you want to approve this request?')
                            ]);
                            echo ' ';
                            echo $this->Html->link('<i class="fa fa-thumbs-down"></i> ' . __('Reject'), [
                                'controller' => 'Requests',
                                'action' => 'reason',
                                $request->id
                            ], [
                                'escape' => false,
                                'class' => 'btn btn-danger'
                            ]);
                        }elseif ($request->approved_at != NULL|| $request->removal_status == 2) {
//                            echo $this->Html->link(__('Delete Record'), ['confirm' => __('Are you sure you want to delete this request?'), 'action' => 'delete', $request->id],[
//                                'style' => 'color: darkred;', // Change 'red' to the desired color
//                            ]);
                            }
                        ?>
                    </td>




                </tr>
           <?php }?>
            <?php endif ?>
                <?php endif ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
