<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project[]|\Cake\Collection\CollectionInterface $projects
 * @var $selected
 * @var $builder
 * @var \App\Model\Entity\Projects[]|CollectionInterface $Sarray
 * @var $requests
 * @var $unRequestedProjects
 * @var $joinedProjects
 */
$currentUser = $this->request->getAttribute('identity');
$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];
?>
<div class="projects index content">
    <h3><?= __('All Projects') ?></h3>
    <?php if($currentUser->role == 'Builder') { ?>
        <br>
        <h4> Page Guide: </h4>
        <h5> As a builder on this page you will see all the projects within the system. </h5>
        <h5> Details are hidden and as a builder you cannot join a project. </h5>
        <h5> Either contact the admin to get assigned to an existing project, or create a project (self-assigned as 'Head Builder') </h5>
        <br>
    <?php } ?>
    <?php if($currentUser->role != 'Admin' && $currentUser->role != 'Builder') { ?>
        <br>
        <h4> Page Guide: </h4>
        <h5> On this page you will see all the project within the system. </h5>
        <h5> You may request to join a project and the request will be sent to head builder to be authenticated. </h5>
        <br>
    <?php } ?>
    <?php if($currentUser->role == 'Builder'){ //TODO: Remove ability for admin to add project for now?>
        <a class="btn btn-block btn-primary" style="width: 200px" href="<?= $this->Url->build(
            ['controller' => 'Projects', 'action' => 'add']) ?>">Add New Project</a>
    <?php } ?>
    <br/>
    <form method="get" accept-charset="utf-8" action=
        <?= $this->Url->build(['controller' => 'projects', 'action' => 'index'])?>>
    </form>
    <br/>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite; max-width:1200px">
            <thead>
            <tr>

                <th><?= $this->Paginator->sort('name','Project Name') ?></th>
                <?php if ($currentUser->role == 'Admin'): ?>
                    <th><?= $this->Paginator->sort('project_type') ?></th>
                    <?php if($currentUser->role == 'Admin'){ echo '<th>Client</th>';} ?>
                <?php endif; ?>
                <th>Address</th>
                <?php if ($currentUser->role == 'Admin'): ?>
                    <th><?= $this->Paginator->sort('start_date') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                <?php endif; ?>
<!--                --><?php //if($currentUser->role == 'On-site Worker'){
//                    echo '<th>Induction</th>';
//                }?>
                <?php if($currentUser->role != 'Admin'){ ?>
                    <th class="actions"><?= __('Actions') ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($unRequestedProjects as $project): ?>
                <tr> <!--onclick="window.location='<?= $this->Url->build(['action' => 'view', $project->id]) ?>';"-->

                    <td><?= h($project->name) ?></td>
                    <?php if ($currentUser->role == 'Admin'): ?>
                        <td><?= h($project->project_type) ?></td>
                        <?php if($currentUser->role == 'Admin'){ echo '<td>'.h($project->client_name).'</td>';} ?>
                    <?php endif; ?>

                    <td><?= h($project->address_no.' '.$project->address_street) ?>
                        <br/>
                        <?= h($project->address_suburb) ?>
                        <br/>
                        <?= h($state[$project->address_state].' '.$project->address_postcode) ?>
                    </td>
                    <?php if ($currentUser->role == 'Admin'): ?>
                        <td><?= h($project->start_date) ?></td>
                        <td><?= h($project->status) ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            <?php foreach ($projects as $project): ?>
                <tr> <!--onclick="window.location='<?= $this->Url->build(['action' => 'view', $project->id]) ?>';"-->
                    <td><?= h($project->name) ?></td>
                    <?php if ($currentUser->role == 'Admin'): ?>
                        <td><?= h($project->project_type) ?></td>
                        <?php if($currentUser->role == 'Admin'){ echo '<td>'.h($project->client_name).'</td>';} ?>
                    <?php endif; ?>

                    <td><?= h($project->address_no.' '.$project->address_street) ?>
                        <br/>
                        <?= h($project->address_suburb) ?>
                        <br/>
                        <?= h($state[$project->address_state].' '.$project->address_postcode) ?>
                    </td>
                    <?php if ($currentUser->role == 'Admin'): ?>
                        <td><?= h($project->start_date) ?></td>
                        <td><?= h($project->status) ?></td>
                    <?php endif; ?>
                    <td class="actions">
                        Requested
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php foreach ($joinedProjects as $project): ?>
                <tr> <!--onclick="window.location='<?= $this->Url->build(['action' => 'view', $project->id]) ?>';"-->
                    <td><?= h($project->name) ?></td>
                    <?php if ($currentUser->role == 'Admin'): ?>
                        <td><?= h($project->project_type) ?></td>
                        <?php if($currentUser->role == 'Admin'){ echo '<td>'.h($project->client_name).'</td>';} ?>
                    <?php endif; ?>

                    <td><?= h($project->address_no.' '.$project->address_street) ?>
                        <br/>
                        <?= h($project->address_suburb) ?>
                        <br/>
                        <?= h($state[$project->address_state].' '.$project->address_postcode) ?>
                    </td>
                    <?php if ($currentUser->role == 'Admin'): ?>
                        <td><?= h($project->start_date) ?></td>
                        <td><?= h($project->status) ?></td>
                    <?php endif; ?>
                    <td class="actions">
                        Joined
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

<script>
    function add() {
        window.location.href = 'http://localhost/team122-app_fit3048/projects/add';
    }
</script>
