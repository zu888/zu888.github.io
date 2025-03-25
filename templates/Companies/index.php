<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company[]|\Cake\Collection\CollectionInterface $companies
 * @var $unRequestedCompanies
 * @var $joinedCompanies
 * @var $rejectedCompanies
 */
$currentUser = $this->request->getAttribute('identity');

$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];
?>

<div class="companies index content">
    <h3><?= __('All Companies') ?></h3>
    <?php if($currentUser->role == 'Admin') { ?>
        <br>
        <h4> Page Guide: </h4>
        <h5> As an Admin on this page you will see all the companies within the system. </h5>
        <h5> You can create companies and details are visible including edit/delete functions are available only to you. </h5>
        <h5> You cannot join companies as an Admin. </h5>
        <br>
    <?php } ?>
    <?php if($currentUser->role == 'Builder') { ?>
        <br>
        <h4> Page Guide: </h4>
        <h5> As a Builder on this page you will see all the companies within the system. </h5>
        <h5> As a Builder you can create companies but must contact Admin if you need to edit company details in the future. </h5>
        <h5> You can join and leave companies at any point. </h5>
        <br>
    <?php } ?>
    <?php if($currentUser->role != 'Admin' && $currentUser->role != 'Builder') { ?>
        <br>
        <h4> Page Guide: </h4>
        <h5> On this page you will see all the companies within the system. </h5>
        <h5> You may only view company details. </h5>
        <h5> You can join and leave companies at any point. </h5>
        <?php if($currentUser->role != 'On-site Worker'): ?>
        <h5> <strong>If you are a builder, please add your own company first.</strong> </h5>
        <h5> <strong>After that, please navigate to "My Account" and request to be a builder.</strong></h5>
        <?php endif;?>
        <br>

    <?php } ?>
    <?php if($currentUser->role == 'Builder' || $currentUser->role == 'Contractor'){ //TODO: Remove ability for admin to add project for now?>
        <a class="btn btn-block btn-primary" style="width: 200px" href="<?= $this->Url->build(
            ['controller' => 'Companies', 'action' => 'add']) ?>">Add New Company</a>
    <?php } ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite;">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th><?= $this->Paginator->sort('company_type') ?></th>
                    <th>ABN</th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th>Address</th>
                    <th>Contact Name</th>
                    <th>Contact Email</th>
                    <th>Contact Phone</th>
                    <th class="actions"><?= __('Employment Status') ?></th>
                    <?php if($currentUser->role != 'Admin'){ ?>
                    <th class="actions"><?= __('Actions') ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($unRequestedCompanies as $ncompany): ?>
                <tr>
                    <td><?= $this->Number->format($ncompany->id) ?></td>
                    <td><?= h($ncompany->company_type) ?></td>
                    <td><?= h($ncompany->abn) ?></td>
                    <td><?= h($ncompany->name) ?></td>
                    <td><?= h($ncompany->address_no.' '.$ncompany->address_street) ?>
                        <br/>
                        <?= h($ncompany->address_suburb) ?>
                        <br/>
                        <?= h($state[$ncompany->address_state].' '.$ncompany->address_postcode) ?>
                    </td>
                    <td><?= h($ncompany->contact_name) ?></td>
                    <td><?= h($ncompany->contact_email) ?></td>
                    <td><?= h($ncompany->contact_phone) ?></td>
                    <td class="actions">
                        <?php if($currentUser->role == 'Admin'){ ?>
                            <?= h("Not Required")?>
                        <?php }?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php foreach ($companies as $company): ?>
                <tr>
                    <td><?= $this->Number->format($company->id) ?></td>
                    <td><?= h($company->company_type) ?></td>
                    <td><?= h($company->abn) ?></td>
                    <td><?= h($company->name) ?></td>
                    <td><?= h($company->address_no.' '.$company->address_street) ?>
                        <br/>
                        <?= h($company->address_suburb) ?>
                        <br/>
                        <?= h($state[$company->address_state].' '.$company->address_postcode) ?>
                    </td>
                    <td><?= h($company->contact_name) ?></td>
                    <td><?= h($company->contact_email) ?></td>
                    <td><?= h($company->contact_phone) ?></td>
                    <td class="actions">
                        <?php if($currentUser->role == 'Admin'){ ?>
                            <?= $this->Html->link(__('View'), ['action' => 'view', $company->id]) ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $company->id]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $company->id], ['confirm' => __('Are you sure you want to delete # {0}?', $company->name)]) ?>
                        <?php } ?>
                    </td>
                    <td class="actions">
                        <?php if($currentUser->role == 'Admin'){ ?>
                            <?= h("Not Required")?>
                        <?php } elseif($currentUser->role != 'Admin'){ ?>
                            <?= h("Requested")?>
                        <?php } ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php foreach ($joinedCompanies as $ncompany): ?>
                <tr>
                    <td><?= $this->Number->format($ncompany->id) ?></td>
                    <td><?= h($ncompany->company_type) ?></td>
                    <td><?= h($ncompany->abn) ?></td>
                    <td><?= h($ncompany->name) ?></td>
                    <td><?= h($ncompany->address_no.' '.$ncompany->address_street) ?><br/>
                        <?= h($ncompany->address_suburb) ?><br/>
                        <?= h($state[$ncompany->address_state].' '.$ncompany->address_postcode) ?>
                    </td>
                    <td><?= h($ncompany->contact_name) ?></td>
                    <td><?= h($ncompany->contact_email) ?></td>
                    <td><?= h($ncompany->contact_phone) ?></td>
                    <td class="actions">
                        <?php if($currentUser->role == 'Admin'){ ?>
                            <?= $this->Html->link(__('View'), ['action' => 'view', $ncompany->id]) ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $ncompany->id]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $ncompany->id], ['confirm' => __('Are you sure you want to delete # {0}?', $ncompany->name)]) ?>
                        <?php } elseif($currentUser->role == 'Builder'){ ?>
                            <?= $this->Html->link(__('View'), ['action' => 'view', $ncompany->id]) ?>
                        <?php } elseif ($currentUser->id == $ncompany->admin_id) { ?>
                            <?= $this->Html->link(__('View'), ['action' => 'view', $ncompany->id]) ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $ncompany->id]) ?>
                        <?php } else { ?>
                            <?= $this->Html->link(__('View'), ['action' => 'view', $ncompany->id]) ?>
                        <?php } ?>
                    </td>

                    <?php if($currentUser->role != 'Admin'){ ?>
                    <td class="actions">
                        <?php if($currentUser->role == 'Admin'){ ?>
                            <?= h("Not Required")?>
                        <?php }elseif($currentUser->role != 'Admin'){ ?>
                            <?= $this->Html->link(__('Leave Company'), ['action' => 'leave', $ncompany->id]) ?>
                        <?php } ?>
                    </td>
                    <?php } ?>
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
