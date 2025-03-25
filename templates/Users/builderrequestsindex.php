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
        <h4> Page Guide: </h4>
        <br>
        <h5> As an Admin on this page you will see all the requests from users requesting to have a 'Builder' account. </h5>
        <h5> You can approve the requests and when satisfied manually remove them from the screen </h5>
        <h5> You cannot see project requests as they are delivered to the assigned 'Builder' of each project </h5>
        <br>
    <?php } ?>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color: ghostwhite; max-width: 1000px">
            <thead class="thead-dark">
            <tr>
                <th><?= $this->Paginator->sort('user_id', 'Requesting User') ?></th>
                <?php if ($currentUser->role == 'Builder') { ?>
                    <th><?= $this->Paginator->sort('project_id', 'Project Requested') ?></th>
                <?php } ?>
                <th><?= $this->Paginator->sort('phone_mobile', 'Mobile Phone') ?></th>
                <th><?= $this->Paginator->sort('phone_office', 'Office Phone') ?></th>
                <th><?= $this->Paginator->sort('email', 'Email') ?></th>
                <th>Approval Status</th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td>
                        <?= $this->Html->link(
                            h($request->first_name . ' ' . $request->last_name),
                            ['controller' => 'Users', 'action' => 'view', $request->user_id]
                        ) ?>
                    </td>
                    <?php if ($currentUser->role == 'Builder') { ?>
                        <td>
                            <?= $request->has('project_id')
                                ? $this->Html->link(
                                    h($request->project_name),
                                    ['controller' => 'Projects', 'action' => 'view', $request->project_id]
                                )
                                : 'N/A' ?>
                        </td>
                    <?php } ?>
                    <td><?= h($request->phone_mobile) ?></td>
                    <td><?= h($request->phone_office) ?></td>
                    <td><?= h($request->email) ?></td>

                    <td>
                        <?= $this->Form->postLink(
                            __('Approve Request'),
                            ['action' => 'approveRequest', $request->id],
                            ['confirm' => __('Are you sure you want to approve this request?')]
                        ) ?>
                    </td>

                    <td class="actions" style="color: darkred">
                        <?= $this->Form->postLink(
                            __("Ignore"),
                            ['action' => 'ignore', $request->id],
                            ['confirm' => __('Are you sure you want to ignore this request?')]
                        ) ?>
                    </td>



                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>


    <br>

    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        </div>
</div>


