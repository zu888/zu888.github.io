<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company[]|\Cake\Collection\CollectionInterface $companies
 * @var iterable<\App\Model\Entity\CompaniesProject> $projectsCompanies
 * @var $partner_company
 */

$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];
?>

<div class="projectsUsers index content">
    <h3><?= __('Associated Company to this Project')?></h3>
    <div>
        <div class="table-responsive">
            <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                <thead class="thead-dark">
            <tr>
                <th><?= $this->Paginator->sort('name','Name') ?> </th>
                <th><?= $this->Paginator->sort('company_type','Type') ?> </th>
                <th><?= $this->Paginator->sort('abn', 'ABN') ?> </th>
                <th><?= $this->Paginator->sort('contact_number','Contact Number')?> </th>
                <th><?= $this->Paginator->sort('contact_email','Email')?></th>
                <th >Address</th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($companies as $company): ?>
                <tr>
                    <td><?= h($company->name) ?></td>
                    <td><?= h($company->company_type) ?></td>
                    <td><?= h($company->abn) ?></td>
                    <td><?= h($company->contact_phone) ?></>
                    <td><?= h($company->contact_email) ?></td>
                    <td><?= h($company->address_no.' '.$company->address_street.', ') ?>
                        <br/>
                        <?= h($company->address_suburb.', ') ?>
                        <br/>
                        <?= h($state[$company->address_state].' '.$company->address_postcode) ?>
                    </td>
                    <td class="actions">
                        <?= $this->Form->postLink(__('Remove from project'), ['action' => 'removePartner', $company->partnerid, $company->project_id],
                            ['class' => 'btn btn-danger', 'confirm' => __('Are you sure you want to remove'. h($company->name) . ' from the project?'),]) ?>


                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
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
