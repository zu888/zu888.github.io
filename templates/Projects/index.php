<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project[]|\Cake\Collection\CollectionInterface $projects
 * @var $selected
 * @var $builder
 * @var \App\Model\Entity\Projects[]|CollectionInterface $Sarray
 * @var $requests
 */


$currentUser = $this->request->getAttribute('identity');
$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];
?>
<div class="projects index content">

    <?php echo $this->Html->css('main'); ?>

    <h3><?= __('My Projects') ?></h3>
    <?php if($currentUser->role == 'Admin') { ?>
        <br>
        <h4> Page Guide: </h4>
        <h5> As the Admin of the site you have full access to all the projects. </h5>
        <h5> There is no requirement to join a project on a different screen to make any changes/edits. </h5>
        <h5> Full access includes all the actions which the builders can perform. </h5>
        <h5> All project information including detailed documents and equipment is located in 'Project Details'. </h5>
        <br>
    <?php } ?>
    <?php if($currentUser->role == 'Builder') { ?>
        <br>
        <h4> Page Guide: </h4>
        <h5> As a builder on this page you will see the projects which you are the assigned 'Builder'. </h5>
        <h5> Actions can be made for each of the projects that you are in charge. </h5>
        <h5> All project information including <strong>detailed documents, equipment and passcode</strong> is located in 'Project Details'. </h5>
        <br>
    <?php } ?>
    <?php if($currentUser->role != 'Admin' && $currentUser->role != 'Builder') { ?>
        <br>
        <h4> Page Guide: </h4>
        <h5> On this page you will see the projects which you have successfully been authenticated to join. </h5>
        <h5> You may view the details of the project including documents that may pertain to you. </h5>
        <br>
    <?php } ?>
    <?php if($currentUser->role == 'Builder'){  ?>
        <a class="btn btn-block btn-primary" style="width: 200px" href="<?= $this->Url->build(
            ['controller' => 'Projects', 'action' => 'add']) ?>">Add New Project</a>
    <?php } ?>
    <br/>
    <form method="get" accept-charset="utf-8" action=
        <?= $this->Url->build(['controller' => 'projects', 'action' => 'index'])?>>
        <div class="row align-items-stretch">
            <div class="col-md-2">
                <label class="pb-1" for="sort_by">Filter by Status:</label>
                <?= $this->Form->create(null, ['type' => 'get'])?>
                <?= $this->Form->select('key', $Sarray, ['onchange'=>'this.form.submit()', 'empty' => 'All', 'value' => $this->request->getQuery('key'), 'style' => 'color: #323232']); ?>
            </div>
        </div>
    </form>
    <br/>
    <div class="table-responsive-sm" style="overflow: visible ">
        <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%; ">
            <thead>
                <tr>
                    <th>Project Name</th>
<!--                    <th>Project Type</th>-->
                    <?php if($currentUser->role == 'Admin'){ echo '<th>Client</th>';} ?>
<!--                    --><?php //if($currentUser->role != 'Builder'){ echo '<th>Builder</th>';} ?>
                    <th>Address</th>
                    <?php if ($currentUser->role != 'Builder'){
                        echo '<th>Builder</th>'; }?>
                    <th>Start Date</th>
                    <th>Project Status</th>
                    <?php if($currentUser->role == 'On-site Worker'){
                        echo '<th>Induction</th>';
                    }?>
                    <?php $userStatus = null;
foreach ($projects as $project);
                    // Iterate through the projectUserStatus array of objects
                    foreach ($projectUserStatus as $statusRecord) {

                        if ($statusRecord->project_id == $project->id) {
                            $userStatus = $statusRecord->status;
                            break; // Exit the loop once the status is found
                        }
                    } ?>
                    <?php if($currentUser->role == 'Builder'){
                        echo '<th>Passcode  <span style="color: red;">(Share only with known user)</span></th>';
                    }?>
                    <th class="actions"><?= __('Actions') ?></th>



                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                <tr> <!--onclick="window.location='<?= $this->Url->build(['action' => 'view', $project->id]) ?>';"-->
                    <td><?= $this->Html->link(h($project->name), ['action' => 'view', $project->id]) ?></td>
<!--                    <td>--><?php //= h($project->project_type) ?><!--</td>-->
                    <?php if($currentUser->role == 'Admin'){ echo '<td>'.h($project->client_name).'</td>';} ?>
                    <td><?= h($project->address_no.' '.$project->address_street) ?>
                        <br/>
                        <?= h($project->address_suburb) ?>
                        <br/>
                        <?= h($state[$project->address_state].' '.$project->address_postcode) ?>

                    </td>
                    <?php if($currentUser->role == 'Admin' ){ echo '<td>'.h($project->user->first_name.' '.$project->user->last_name).'</td>';}?>
                    <?php
                    if($currentUser->role != 'Builder' && $currentUser->role != 'Admin'){
                        //  echo '<td>'.$this->Html->link(($project->builder_fname.' '.$project->builder_lname),
                        //          ['controller' => 'Users', 'action' => 'view', $project->builder_id]).'</td>';
                        echo '<td>'.$project->builder_fname.' '.$project->builder_lname.'</td>';
                    } ?>
                    <td><?= h($project->start_date) ?></td>
                    <td style="color: <?= $project->status === 'Active' ? 'limegreen' : ($project->status === 'Cancelled' ? 'darkred' : 'black') ?>;">
                        <?= h($project->status) ?>
                    </td>
                    <?php
                    if ($currentUser->role == 'On-site Worker') {
                        if ($project->inducted_date) {
                            echo '<td style="color: limegreen;">Complete</td>';
                        } else {
                            echo '<td style="color: red;">Incomplete</td>';
                        }
                    }
                    ?>
                    <?php
                    if($currentUser->role == 'Builder' ){ echo '<td>'.h($project->passcode).'</td>';}?>

                    <td class="actions">
                        <?php
                        $userStatus = null;

                        // Iterate through the projectUserStatus array of objects
                        foreach ($projectUserStatus as $statusRecord) {

                            if ($statusRecord->project_id == $project->id) {
                                $userStatus = $statusRecord->status;
                                break; // Exit the loop once the status is found
                            }
                        }


                        ?>
                        <?php if ($currentUser->role == 'Builder' || $currentUser->role == 'Admin'|| $userStatus == 'Co-Manager'): ?>
                            <!-- View Dropdown Button -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-folder"></i> View <span class="caret" style="margin-left: 5px;"></span>
                                </button>
                                <div class="dropdown-menu">
                                    <!-- View Dropdown Menu Items -->
                                    <?= $this->Html->link(__('Project View'), ['action' => 'view', $project->id], ['class' => 'dropdown-item']) ?>
                                    <?= $this->Html->link(__('Site Live View and Check-in Record'), ['controller' => 'checkins', 'action' => 'checkin', '?' => ['project' => $project->id]], ['class' => 'dropdown-item']) ?>
                                    <?= $this->Html->link(__('QR Codes'), ['action' => 'generateqr', $project->id], ['class' => 'dropdown-item']) ?>
<!--                                    --><?php //= $this->Html->link(__('Project Workers and Co-Managers'), ['controller' => 'ProjectsUsers', 'action' => 'index', $project->id], ['class' => 'dropdown-item']) ?>
<!--                                    --><?php //= $this->Html->link(__('Associated Companies'), ['controller'=>'Companies','action'=> 'projectPartner',$project->id], ['class' => 'dropdown-item']) ?>
<!--                                    --><?php //= $this->Html->link(__('Subcontracting'), ['controller' => 'subcontracts', 'action' => 'index', $project->id], ['class' => 'dropdown-item']) ?>
                                </div>
                            </div>
                            <!-- Edit Dropdown Button -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-pencil"></i>  Edit <span class="caret" style="margin-left: 5px;"></span>
                                </button>
                                <div class="dropdown-menu">
                                    <!-- Edit Dropdown Menu Items -->
                                    <?= $this->Html->link(__('Edit Project Details'), ['action' => 'edit', $project->id], ['class' => 'dropdown-item']) ?>
                                    <?= $this->Html->link(__('Regenerate Passcode'), ['action' => 'generatepasscode', $project->id], ['class' => 'dropdown-item']) ?>
                                </div>
                            </div>
                            <!-- Add Dropdown Button -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-plus-circle"></i> Add <span class="caret" style="margin-left: 5px;"></span>
                                </button>
                                <div class="dropdown-menu">
                                    <!-- Add Dropdown Menu Items -->
                                    <?= $this->Html->link(__('Worker'), ['controller' => 'Requests', 'action' => 'addprojectinvitation', $project->id], ['class' => 'dropdown-item']) ?>
                                    <?= $this->Html->link(__('Company'), ['controller' => 'Requests', 'action' => 'inviteProjectCompany', $project->id], ['class' => 'dropdown-item']) ?>
                                    <?= $this->Html->link(__('Documents'), ['controller' => 'documents', 'action' => 'add', '?' => ['project' => $project->id]], ['class' => 'dropdown-item']) ?>
                                    <?= $this->Html->link(__('Equipment'), ['controller' => 'Equipment', 'action' => 'add', '?' => ['project' => $project->id]], ['class' => 'dropdown-item']) ?>
                                </div>
                            </div>


                        <?php elseif ($currentUser->role != 'Admin' && $currentUser->role != 'Builder'): ?>
                            <?php if ($userStatus == 'Disengaged'): ?>
                                <p>Disengaged</p>
                            <?php else: ?>
                                <?php if ($currentUser->role != 'Contractor'){ ?>
                                     <?= $this->Html->link('<i class="fa fa-folder"></i> ' . __('View Project Details'), ['action' => 'view', $project->id], ['class' => 'btn btn-primary', 'escape' => false,]) ?>

                                    <?php if (!($project->inducted_date)){ ?>
                                        <?= $this->Html->link(
                                            '<i class="fa fa-check"></i> ' . __('Complete Induction'),
                                            ['controller' => 'Inductions', 'action' => 'checkcompany', $project->id,$project->usercompany],
                                            ['class' => 'btn btn-primary', 'style' => 'background-color: green; color: white;', 'escape' => false]
                                        ) ?>

                                    <?php } ?>

                                    <?= $this->Html->link('<i class="fa fa-sign-out"></i> ' . __('Leave Project'),
                                        ['action' => 'leave', $project->id],
                                        [
                                            'confirm' => __('Are you sure you want to leave this project?'),
                                            'class' => 'btn btn-danger',
                                            'escape' => false
                                        ]
                                    ) ?>

                            <?php }else{ ?>

                                    <?= $this->Html->link('<i class="fa fa-folder"></i> ' . __('View Project Details'), ['action' => 'view', $project->id], ['class' => 'btn btn-primary', 'escape' => false,]) ?>
                                    <?= $this->Html->link('<i class="fa fa-sign-out"></i> ' . __('Leave Project'),
                                        ['action' => 'companyleave', $project->id],
                                        [
                                            'confirm' => __('Are you sure you want to leave this project?'),
                                            'class' => 'btn btn-danger',
                                            'escape' => false
                                        ]
                                    ) ?>
                                <?php } ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>


                </tr>
                <?php endforeach; ?>
                <?php if($projects->count() == 0){
                    echo '<tr><td>You have no assigned projects.</td></tr>';
                } ?>
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
