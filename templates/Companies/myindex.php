<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company[]|\Cake\Collection\CollectionInterface $companies
 * @var $company_user
 */
$currentUser = $this->request->getAttribute('identity');

$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];
?>

<div class="companies index content">
<?php echo $this->Html->css('main'); ?>

    <h3><?= __('Your Companies') ?></h3>
    <?php if($currentUser->role == 'Builder') { ?>
        <br>
        <h4> Page Guide: </h4>
        <h5> As a builder on this page you will see the companies which you have joined. </h5>
        <h5> Companies can be viewed for company details and colleague details. </h5>
        <br>
    <?php } ?>
    <?php if($currentUser->role != 'Admin' && $currentUser->role != 'Builder') { ?>
        <br>
        <h4> Page Guide: </h4>
        <h5> On this page you will see the companies which you have joined. </h5>
        <h5> Companies can be viewed for company details and colleague details. </h5>
        <br>
    <?php } ?>
    <?php if($currentUser->role == 'Builder' || $currentUser->role == 'Contractor'){ ?>
        <?php if(!$existingCompany){?>
        <a class="btn btn-block btn-primary" style="width: 200px" href="<?= $this->Url->build(
            ['controller' => 'Companies', 'action' => 'add']) ?>">Add New Company</a>
        <?php } ?>
    <?php } ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite;x">
            <thead class="thead-dark">
            <tr>
                <th><?= $this->Paginator->sort('name') ?></th>
                <th><?= $this->Paginator->sort('company_type') ?></th>
                <th><?= $this->Paginator->sort('abn') ?></th>
                <th>Address</th>
                <th><?= $this->Paginator->sort('contact_name') ?></th>
                <th><?= $this->Paginator->sort('contact_email') ?></th>
                <th><?= $this->Paginator->sort('contact_phone') ?></th>
                <th>Status</th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($companies as $company): ?>
                <tr>
                    <td><?= h($company->name) ?></td>
                    <td><?= h($company->company_type) ?></td>
                    <td><?= h($company->abn) ?></td>

                    <td><?= h($company->address_no.' '.$company->address_street) ?>
                        <br/>
                        <?= h($company->address_suburb) ?>
                        <br/>
                        <?= h($state[$company->address_state].' '.$company->address_postcode) ?>
                    </td>
                    <td><?= h($company->contact_name) ?></td>
                    <td><?= h($company->contact_email) ?></td>
                    <td><?= h($company->contact_phone) ?></td>
                    <td style="color: <?= $company->status == 'Engaged' ? 'limegreen' : 'darkred' ?>"><?= h($company->status) ?></td>
                    <td class="actions">
                        <?php if ($company->status == 'Engaged'|| $company->status == 'Owner'): ?>
                            <?= $this->Html->link('<i class="fa fa-folder"></i> ' . __('View'), ['action' => 'view', $company->id], ['class' => 'btn btn-primary', 'escape' => false,]) ?>
                            <br> <!-- Add a blank line -->
                        <?php endif; ?>
                        <?php if($company->status == 'Engaged' && $company->status != 'Owner'): ?>
                            <br> <!-- Add a blank line -->
                        <?= $this->Html->link('<i class="fa fa-sign-out"></i> ' . __('Leave'),
                                        ['action' => 'leave', $company->id],
                                        [
                                            'confirm' => __('Are you sure you want to leave the company?'),
                                            'class' => 'btn btn-danger',
                                            'escape' => false
                                        ]
                                    ) ?>
                        <?php endif; ?>
                        <?php if($company->status == 'Disengaged'): ?>
                            <?= $this->Html->link(__('Remove'), ['action' => 'leave', $company->id],  ['confirm' => 'Are you sure you want to remove the record?'])?>
                            <br> <!-- Add a blank line -->
                        <?php endif; ?>

                        <?php if( ($company->status == 'Owner')): ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $company->id])?>
                            <br>
                            <?= $this->Html->link(__('Invite Member'), ['controller' => 'Requests', 'action' => 'inviteWorkerToCompany',$company->id])?>
                            <br>
                            <?= $this->Html->link(__('Invite Company'), ['controller' => 'Requests', 'action' => 'inviteCompanyToCompany',$company->id])?>
                            <br>
                            <?= $this->Html->link(__('Regenerate Passcode'), ['action'=> 'generatepasscode', $company->id])?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
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
