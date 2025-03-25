<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Company $company
 * @var $admin_id
 * @var $documents
 * @var $isEmployee
 * @var $company_user
 * @var \Cake\Collection\CollectionInterface|string[] $company_project_doc
 * @var \Cake\Collection\CollectionInterface|string[] $projectDocs
 * @var \Cake\Collection\CollectionInterface|string[] $pj_documentInfos
 */
$currentUser = $this->request->getAttribute('identity');
$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];

?>
<div class="row content">

    <?php echo $this->Html->css('main'); ?>

    <div class="column-responsive column-80">
        <div class="companies view">
        <a  class="btn btn-secondary" href="javascript:history.go(-1)" style="text-decoration: underline;">Back</a>
            <h3><?= h($company->name) ?></h3>
            <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                <tr>
                    <th><?= __('Company/Contractor Name') ?></th>
                    <td><?= h($company->name) ?></td>
                </tr>
                <?php if ($currentUser->id == $company->admin_id) { ?>
                    <tr>
                        <th>
                            <?= __('Company Passcode') ?><br>
                            <span style="color: red; ">(Share only with known users)</span>
                        </th>
                        <td style="color: red; font-weight: bold;"><?= h($company->passcode) ?></td>
                    </tr>
                <?php } ?>

                <tr>
                    <th><?= __('Address') ?></th>
                    <td>
                        <?= h($company->address_no) . ' ' . h($company->address_street) ?><br />
                        <?= h($company->address_suburb) ?><br />
                        <?= h($state[$company->address_state]) . ' ' . h($company->address_postcode) ?>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Contact Name') ?></th>
                    <td><?= h($company->contact_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Contact Email') ?></th>
                    <td><?= h($company->contact_email) ?></td>
                </tr>
                <tr>
                    <th><?= __('Contact Phone') ?></th>
                    <td><?= h($company->contact_phone) ?></td>
                </tr>
                <tr>
                    <th><?= __('ABN') ?></th>
                    <td><?= h($company->abn) ?></td>
                </tr>
                <?php if ($currentUser->id == $company->admin_id) { ?>
                <tr>

                    <th><?= __('Actions') ?></th>

                    <td>
                        <?php if ($currentUser->id == $company->admin_id) { ?>
                            <?= $this->Html->link(__('Regenerate Passcode'),
                                ['action' => 'generatepasscode', $company->id],
                                ['class' => 'btn btn-success'])
                            ?>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <?php if ($currentUser->role == 'Admin' ||  $currentUser->id == $company->admin_id) { ?>
                <h3><?= __('Employees') ?></h3>
                <div class="custom-row">
                    <div class="custom-col">
                        <a class="btn btn-primary" style="width: 200px" href="<?= $this->Url->build(
                            ['controller' => 'Requests', 'action' => 'inviteWorkerToCompany', $company->id]
                        ) ?>">Invite New Employee</a>
                    </div>
                    <div class="custom-col text-right">
                        <div class="search-container">
                            <input type="text" id="searchInput" placeholder="Search by name">
                            <i class="fa fa-search search-icon"></i>
                        </div>
                    </div>
                </div>
                <br>
                <!--                --><?php //if ($currentUser->role == 'Admin' || $currentUser->id == $company->admin_id):
                                        ?>
                <!--                    --><?php //= $this->Html->link(__('Add Worker'), ['action' => 'addworker', $company->id], ['class' => 'button float-right'])
                                            ?>
                <!--                --><?php //endif;
                                        ?>
                <table class="table table-bordered" style="background-color: ghostwhite; 100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Phone Number</th>
                            <th>Status</th>
                            <?php if ($currentUser->role == 'Admin' || $currentUser->id == $company->admin_id) : ?>
                                <th class="actions"><?= __('Actions') ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($company_user as $user) : ?>
                            <tr id="userRow<?= $user->id ?>"> <!-- Add the id attribute here -->
                                <td>
                                    <?= $this->Html->link(h($user->first_name . ' ' . $user->last_name), ['controller' => 'Users', 'action' => 'view', $user->user_id]) ?>
                                </td>
                                <?php if ($currentUser->id == $user->user_id) : ?>
                                    <td>Admin</td>
                                <?php else : ?>
                                    <td><?= h($user->role) ?></td>
                                <?php endif; ?>
                                <td><?= h($user->phone_mobile) ?></td>
                                <td style="color: <?= $user->status == 'Engaged' ? 'limegreen' : 'darkred' ?>"><?= h($user->status) ?></td>
                                <?php if ($currentUser->role != 'On-site Worker') : ?>
                                    <td class="actions">
                                        <?php
                                        $userName = h($user->first_name . ' ' . $user->last_name);
                                        $confirmMessage = $user->status == 'Engaged'
                                            ? __("Are you sure you want to remove {0} from company?", $userName)
                                            : __("Are you sure you want to delete the record of {0}?", $userName);
                                        ?>
                                        <?= $this->Form->postLink($user->status == 'Engaged' ? __('Remove from Company') : __('Delete Record'), ['action' => 'deleteCompanyUser', $company->id, $user->id], ['confirm' => $confirmMessage]) ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>


            <?php } ?>

            <div class="related">
                <?php if ($currentUser->id == $company->admin_id) { ?>
                    <h3><?= __('Company Documents') ?></h3>
                    <a class="btn btn-block btn-primary" style="width: 200px" href="<?= $this->Url->build(
                                                                                        ['controller' => 'Documents', 'action' => 'add', '?' => ['company' => $company->id]]
                                                                                    ) ?>">Add Company Documents</a><br>

                    <div class="table-responsive-md">
                        <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                            <tr>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Document Type') ?></th>
                                <th><?= __('Issue Date') ?></th>
                                <th><?= __('Expiry Date') ?></th>
                                <!--                                <th>--><?php //= __('Status')
                                                                            ?><!--</th>-->
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                            <?php foreach ($documents as $document) : ?>
                                <tr>
                                    <td>
                                        <?= $this->Html->link(__($document->name), ['controller' => 'Documents', 'action' => 'view', $document->id]) ?>
                                    </td>
                                    <td><?= h($document->document_type) ?></td>
                                    <td><?= h($document->issue_date) ?></td>
                                    <td><?= h($document->expiry_date) ?></td>
                                    <!--                                    <td>--><?php //= h($document->status)
                                                                                    ?><!--</td>-->
                                    <?php if ($document->archived == 0){ ?>
                                    <td class="actions">
                                        <?= $this->Html->link('<i class="fa fa-folder"></i> ' . __('View'), ['controller' => 'Documents', 'action' => 'view', $document->id], ['class' => 'btn btn-primary', 'escape' => false]) ?>
                                        <?php if ($currentUser->id == $company->admin_id): ?>
                                            <?= $this->Html->link('<i class="fa fa-pencil"></i> ' . __('Edit Details'), ['controller' => 'Documents', 'action' => 'edit', $document->id], ['class' => 'btn btn-warning', 'escape' => false]) ?>
                                            <?= $this->Html->link('<i class="fa fa-archive"></i> ' . __('Archive Document'), ['controller' => 'Documents', 'action' => 'delete', $document->id], ['confirm' => __('Are you sure you want to archive '. $document->name . '?'), 'class' => 'btn btn-danger', 'escape' => false]) ?>
                                        <?php endif; ?>
                                    </td>
                                    <?php } else{ ?>
                                        <td class="actions">
                                            <?php if ($currentUser->id == $company->admin_id) : ?>
                                                <?= $this->Html->link('<i class="fa fa-archive"></i> ' . __('Unarchive Document'), ['controller' => 'Documents', 'action' => 'unarchived', $document->id], [ 'class' => 'btn btn-warning', 'escape' => false]) ?>
                                                <br />
                                            <?php endif; ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($documents->count() == 0) {
                                echo '<tr><td>No company documents have been added.</td></tr>';
                            } ?>
                            <tr>

                            </tr>
                        </table>
                    </div>
                <?php } ?>
            </div>

            <div class="related">
                <?php if (!empty($company_project_doc)) : ?>
                    <?php if ($currentUser->role == 'Builder') { ?>
                        <h3><?= __('Company Documents') ?></h3>
                        <div class="table-responsive">
                            <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
                                <tr>
                                    <th><?= __('Name') ?></th>
                                    <th><?= __('Document Type') ?></th>
                                    <th><?= __('Issue Date') ?></th>
                                    <th><?= __('Expiry Date') ?></th>
                                    <th><?= __('Status') ?></th>
                                    <th><?= __('Comments') ?></th>
                                    <th class="actions"><?= __('Actions') ?></th>
                                </tr>

                                <?php foreach ($company_project_doc as $document) : ?>
                            <?php
                                    $auth_values = ($pj_documentInfos[$document->id][0]->auth_value);
                                    $auth_values_array = explode(',', $auth_values);
                                    $pj_id = ($pj_documentInfos[$document->id][0]->project_id);
                            ?>
                                <?php if (in_array('Builder', $auth_values_array)) { ?>
                                    <tr>
                                        <td><?= h($document->name) ?></td>
                                        <td><?= h($document->document_type) ?></td>
                                        <td><?= h($document->issue_date) ?></td>
                                        <td><?= h($document->expiry_date) ?></td>
                                        <td style="color:
                                                <?php if ($pj_documentInfos[$document->id][0]->status === 'Reviewed') : ?>
                                                green
                                            <?php elseif ($pj_documentInfos[$document->id][0]->status === 'Rejected') : ?>
                                                red
                                            <?php else : ?>
                                                orange
                                            <?php endif; ?>">
                                            <?= h($pj_documentInfos[$document->id][0]->status) ?>
                                        </td>
                                        <td><?= h($pj_documentInfos[$document->id][0]->comment) ?></td>
                                        <td class="actions">
                                            <?= $this->Html->link(__('View'), ['controller' => 'Documents', 'action' => 'view', $pj_documentInfos[$document->id][0]->document_id,'?' => ['pj_id' => $pj_id]]) ?>
                                            <?php if ($currentUser->id == $company->admin_id) {
                                                echo '<br/>' . $this->Html->link(__('Edit Details'), ['controller' => 'Documents', 'action' => 'edit', $document->id]);
                                                echo '<br/>' . $this->Html->link(
                                                    __('Delete Document'),
                                                    ['controller' => 'Documents', 'action' => 'delete', $document->id],
                                                    ['confirm' => 'Are you sure you want to delete this document?']
                                                );
                                            } ?>
                                        </td>
                                    </tr>
                                    <?php }; ?>
                                <?php endforeach; ?>

                                <?php if ($documents->count() == 0) {
                                    echo '<tr><td>No company documents have been added.</td></tr>';
                                } ?>
                                <tr>
                                    <td>
                                        <?php if ($currentUser->id == $company->admin_id) {
                                            echo $this->Html->link(__('Add Company Documents'), ['controller' => 'Documents', 'action' => 'add', '?' => ['company' => $company->id]]);
                                        } ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <?php } ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('.table tbody tr');

        searchInput.addEventListener('keyup', function() {
            const searchTerm = searchInput.value.toLowerCase();

            tableRows.forEach(function(row) {
                const rowId = row.id; // Get the row's id attribute

                if (rowId.startsWith('userRow')) {
                    // Extract the user's ID from the row ID
                    const userId = rowId.replace('userRow', '');

                    // Get the user's name cell in the row
                    const nameCell = row.querySelector('td:first-child'); // Assuming the name is in the first cell

                    // Get the user's name from the name cell
                    const userName = nameCell.textContent.trim().toLowerCase();

                    if (userName.includes(searchTerm)) {
                        row.style.display = ''; // Show the row
                    } else {
                        row.style.display = 'none'; // Hide the row
                    }
                }
            });
        });
    });
</script>
