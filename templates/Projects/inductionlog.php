<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Projects $user
 * @var $employerAccess
 * @var $assignedProjects
 * @var $documents
 * @var $requests
 * @var $assignedCompanies
 * @var $builderRequests
 */

$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];

$currentUser = $this->request->getAttribute('identity');
?>
<div class="row content">
    <div class="column-responsive column-80">
        <div class="users view">

        <table class="table table-bordered" style="background-color:ghostwhite; max-width:800px">
                 <h3>Worker: </h3>
                <br/>
                <tr>
                    <th><?= __('Name') ?></th>
                </tr>
                <tr>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Company Name') ?></th>
                    <td>

                    </td>
                </tr>
                <tr>
                    <th>Date of Induction</th>
                    <td>
                    </td>
                </tr>
                <tr>
                    <th>Induction Status</th>
                    <td>

                    </td>
                </tr>
                
            </table>
 

        <div class="related">
                <h4><?= __('Worker Documents') ?></h4>
                    <div class="table-responsive">
                    <table class="table table-bordered" style="background-color:ghostwhite; max-width:800px">
                        <tr>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Document Type') ?></th>
                            <th><?= __('Issue Date') ?></th>
                            <th><?= __('Expiry Date') ?></th>
                            <!-- <th><?= __('Status') ?></th> -->
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($documents as $document) : ?>
                            <tr>
                                <td><?= h($document->name) ?></td>
                                <td><?= h($document->document_type) ?></td>
                                <td><?= h($document->issue_date) ?></td>
                                <td><?= h($document->expiry_date) ?></td>
                                <!-- <td><?= h($document->status) ?></td> -->
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['controller' => 'Documents', 'action' => 'view', $document->id]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                        </tr>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>



