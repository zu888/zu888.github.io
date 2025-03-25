<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project[]|\Cake\Collection\CollectionInterface $projects
 * @var $selected
 * @var $builder
 */
$currentUser = $this->request->getAttribute('identity');
$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];
?>
<div class="projects index content">
    <h3><?= __('My Projects') ?></h3><br/>
    <?php if($currentUser->role == 'Builder'){ ?>
        <a class="btn btn-block btn-primary" style="width: 200px" href="<?= $this->Url->build(
            ['controller' => 'Projects', 'action' => 'add']) ?>">Add New Project</a>
    <?php } ?>
    <br/>
    <form method="get" accept-charset="utf-8" action=
        <?= $this->Url->build(['controller' => 'projects', 'action' => 'index'])?>>
        <div class="row align-items-stretch">
            <div class="col-md-2">
                <label class="pb-1" for="sort_by">Filter by Status:</label>
                <select onchange="this.form.submit()" class="form-select" name="status" id="status" >
                    <option  value="All" <?= $selected == 'All' ? 'selected' : '' ?>>All</option>
                    <option  value="Active">Active</option>
                    <option  value="Complete">Complete</option>
                    <option  value="Cancelled">Cancelled</option>
                </select>
            </div>
        </div>
    </form>
    <br/>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite; max-width:800px">
            <thead>
                <tr>



<!--                    --><?php //if($currentUser->role == 'Admin'){ echo '<th>Client</th>';} ?>
<!--                    --><?php //if($currentUser->role != 'Admin'){ echo '<th>Builder</th>';} ?>


                    <th><?= $this->Paginator->sort('name','Porject Name') ?></th>
                    <th><?= $this->Paginator->sort('project_type') ?></th>
                    <?php if($currentUser->role == 'Builder'){ echo '<th>Client</th>';} ?>
                    <?php if($currentUser->role != 'Builder'){ echo '<th>Builder</th>';} ?>

                    <th>Address</th>
                    <th>Builder</th>
                    <th><?= $this->Paginator->sort('start_date') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <?php if($currentUser->role == 'On-site Worker'){
                        echo '<th>Induction</th>';
                    }?>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                <tr> <!--onclick="window.location='<?= $this->Url->build(['action' => 'view', $project->id]) ?>';"-->
                    <td><?= h($project->name) ?></td>
                    <?php if($currentUser->role == 'Admin'){ echo '<td>'.h($project->client_name).'</td>';} ?>
                    <?php



//
//                    if($currentUser->role != 'Admin'){
//                        echo '<td>'.$this->Html->link(($project->builder_fname.' '.$project->builder_lname),
//                                ['controller' => 'Users', 'action' => 'view', $project->builder_id]).'</td>';


                    if($currentUser->role != 'Builder'){
                        // echo '<td>'.$this->Html->link(($project->builder_fname.' '.$project->builder_lname),
                        //         ['controller' => 'Users', 'action' => 'view', $project->builder_id]).'</td>';
                        echo '<td>'.$project->builder_fname.' '.$project->builder_lname.'</td>';
                    } ?>

                    <td><?= h($project->address_no.' '.$project->address_street) ?>
                        <br/>
                        <?= h($project->address_suburb) ?>
                        <br/>
                        <?= h($state[$project->address_state].' '.$project->address_postcode) ?>
                    </td>
                    <td><?= h($project->user->first_name.' '.$project->user->last_name); ?></td>
                    <td><?= h($project->start_date) ?></td>
                    <td><?= h($project->status) ?></td>
                    <?php if($currentUser->role == 'On-site Worker'){
                        if ($project->inducted_date){
                            echo '<td>Complete</td>';
                        } else {
                            echo '<td>Incomplete</td>';
                        }
                    }?>
                    <td class="actions">

<!--                        --><?php //if($currentUser->role == 'Admin'){ ?>
                        <?php if($currentUser->role == 'Builder'){ ?>
                            <?= $this->Html->link(__('Working Lists'), ['controller' => 'checkins', 'action' => 'checkin', '?' => ['project' => $project->id]]) ?><br/>
                            <?= $this->Html->link(__('View Details'), ['action' => 'view', $project->id]) ?><br/>
                            <?= $this->Html->link(__('List Check-ins'), ['controller' => 'checkins', 'action' => 'index', '?' => ['project' => $project->id]]) ?><br/>
                            <?= $this->Html->link(__('List Staff'), ['controller' => 'Projects', 'action' => 'staff', $project->id]) ?><br/>
                            <?= $this->Html->link(__('Edit Details'), ['action' => 'edit', $project->id]) ?><br/>
                            <?= $this->Html->link(__('Generate QR Codes'), ['action' => 'generateqr', $project->id]) ?><br/>
                            <?= $this->Html->link(__('Assign Staff'), ['controller' => 'inductions', 'action' => 'add', '?' => ['project' => $project->id]]) ?><br/>
                            <?= $this->Html->link(__('Add Induction Documents'), ['controller' => 'documents', 'action' => 'add', '?' => ['project' => $project->id]]) ?><br/>
                        <?php } elseif($currentUser->role == 'Contractor'){ ?>
                            <?= $this->Html->link(__('View Details'), ['action' => 'view', $project->project_id]) ?><br/>
                            <?= $this->Html->link(__('List Staff'), ['controller' => 'Projects', 'action' => 'staff', $project->id]) ?><br/>
                            <?= $this->Html->link(__('Assign Staff'), ['controller' => 'inductions', 'action' => 'add', '?' => ['project' => $project->id]]) ?><br/>

                        <?php } elseif( $currentUser->role != 'Builder'){ ?>
                            <?= $this->Html->link(__('View Details'), ['action' => 'view', $project->project_id]) ?><br/>
                        <?php } ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($projects) == 0){
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
