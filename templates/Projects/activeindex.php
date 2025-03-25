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
    <h3><?= __('My Projects') ?></h3>
    <?php if($currentUser->role == 'Admin') { ?>
        <br>
        <h4> Page Guide: </h4>
        <h5> This is a list of all the active projects. </h5>
        <br>
    <?php } ?>


    <form method="get" accept-charset="utf-8" action=
        <?= $this->Url->build(['controller' => 'projects', 'action' => 'index'])?>>
        <div class="row align-items-stretch">
            <div class="col-md-2">
                <label class="pb-1" for="sort_by">Filter by Status:</label>
                <?= $this->Form->create(null, ['type' => 'get'])?>
              </div>
        </div>
    </form>
    <br/>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite; max-width:1200px">
            <thead>
            <tr>
                <th><?= $this->Paginator->sort('name','Project Name') ?></th>
                <th><?= $this->Paginator->sort('project_type') ?></th>
                <?php if($currentUser->role == 'Admin'){ echo '<th>Client</th>';} ?>
                <!--                    --><?php //if($currentUser->role != 'Builder'){ echo '<th>Builder</th>';} ?>
                <th>Address</th>
                <th>Builder</th>
                <th><?= $this->Paginator->sort('start_date') ?></th>
                <th><?= $this->Paginator->sort('status') ?></th>



            </tr>
            </thead>
            <tbody>
            <?php foreach ($projects as $project): ?>
                <tr onclick="window.location='<?= $this->Url->build(['controller' => 'Projects', 'action' => 'view', $project->id]) ?>';">
                    <td><a href="<?= $this->Url->build(['controller' => 'Projects', 'action' => 'view', $project->id]) ?>"><?= h($project->name) ?></a></td>

                <td><?= h($project->project_type) ?></td>
                    <?php if($currentUser->role == 'Admin'){ echo '<td>'.h($project->client_name).'</td>';} ?>
                    <td><?= h($project->address_no.' '.$project->address_street) ?>
                        <br/>
                        <?= h($project->address_suburb) ?>
                        <br/>
                        <?= h($state[$project->address_state].' '.$project->address_postcode) ?>

                    </td>
                    <?php if($currentUser->role == 'Admin' || $currentUser->role == 'Builder'){ echo '<td>'.h($project->user->first_name.' '.$project->user->last_name).'</td>';}?>
                    <?php
                    if($currentUser->role != 'Builder' && $currentUser->role != 'Admin'){
                        //  echo '<td>'.$this->Html->link(($project->builder_fname.' '.$project->builder_lname),
                        //          ['controller' => 'Users', 'action' => 'view', $project->builder_id]).'</td>';
                        echo '<td>'.$project->builder_fname.' '.$project->builder_lname.'</td>';
                    } ?>
                    <td><?= h($project->start_date) ?></td>
                    <td><?= h($project->status) ?></td>
                    <?php if($currentUser->role == 'On-site Worker'){
                        if ($project->inducted_date){
                            echo '<td>Complete</td>';
                        } else {
                            echo '<td>Incomplete</td>';
                        }
                    }?>

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
