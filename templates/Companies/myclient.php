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

    <h3><?= __('My Clients') ?></h3>


    <br>
    <h4> Page Guide: </h4>
    <h5> On this page you will see all companies that you are subcontracted to. </h5>
    <h5> You can view the company detail and disengage with them by anytime. </h5>
    <br>

    <?= $this->Html->link(__('Join Company/Contractor'), ['controller' => 'Requests', 'action' => 'joincompany', $company->id,], ['class' => 'btn btn-primary', 'style' => 'width: 200px']) ?>

    <br>
    <br>
    <br>

    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite;x">
            <thead class="thead-dark">
            <tr>
                <th><?= $this->Paginator->sort('name') ?></th>
                <th><?= $this->Paginator->sort('ABN') ?></th>
                <th><?= $this->Paginator->sort('contact_name') ?></th>
                <th><?= $this->Paginator->sort('contact_email') ?></th>
                <th>Status</th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($companies as $company): ?>
                <tr>
                    <td><?= $this->Html->link(__($company->name), ['action' => 'view', $company->id]) ?></td>
                    <td><?= h($company->abn) ?></td>
                    <td><?= h($company->contact_name) ?></td>
                    <td><?= h($company->contact_email) ?></td>
                    <td style="color: <?= $company->status == 'Engaged' ? 'limegreen' : 'darkred' ?>"><?= h($company->status) ?></td>
                    <td class="actions">
                        <?php if ($company->status == 'Engaged'|| $company->status == 'Owner'): ?>
                            <?= $this->Html->link('<i class="fa fa-folder"></i> ' . __('View'), ['action' => 'view', $company->id], ['class' => 'btn btn-primary', 'escape' => false,]) ?>
                        <?php endif; ?>
                        <?php if($company->status == 'Engaged' && $company->status != 'Owner'): ?>
                            <?= $this->Html->link('<i class="fa fa-sign-out"></i> ' . __('Disengage'),
                                ['action' => 'disengage', $company->company_userID],
                                [
                                    'confirm' => __('Are you sure you want to disengage the company?'),
                                    'class' => 'btn btn-danger',
                                    'escape' => false
                                ]
                            ) ?>
                        <?php endif; ?>
                        <?php if($company->status == 'Disengaged'): ?>
                            <?= $this->Html->link(__('Archive'), ['action' => 'disengage', $company->company_userID],  ['confirm' => 'Are you sure you want to archive this record? This action cannot be restore.','class' => 'btn btn-danger',
                                'escape' => false])?>
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

