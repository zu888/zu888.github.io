<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ProjectsUser> $projectsUsers
 * @var \App\Model\Entity\ProjectsUser $projectsUser
 */
$currentUser = $this->request->getAttribute('identity');
?>
<div class="projectsUsers index content">
    <h3><?= __('Workers for this Project')?></h3>
    <div class="search-bar">
        <input type="text" id="name-filter" placeholder="Search by Name">
    </div>
    <br>
    <div>
        <table class="table table-bordered" style="background-color:ghostwhite; max-width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Phone Mobile</th>
                    <th>Company Working For</th>
                    <th>Status</th>
                    <th>Induction</th>
                    <th>Inducted Date</th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($projectsUsers as $projectsUser): ?>
                <tr>
                    <td><?= $this->Html->link(h($projectsUser->first_name.' '.$projectsUser->last_name), "/users/view/".$projectsUser->user_id) ?></td>
                    <td><?= h($projectsUser->role) ?></td>
                    <td><?= h($projectsUser->phone_mobile) ?></td>
                    <!-- <td><?= $this->Html->link(h($projectsUser->company_name), "/companies/view/".$projectsUser->company_id) ?></td> -->
                    <td><?= $this->Html->link(html_entity_decode($projectsUser->company_name), "/companies/view/" .$projectsUser->company_id) ?></td>

                    <td style="color: <?= $projectsUser->status == 'Engaged' ? 'limegreen' : ($projectsUser->status == 'Co-Manager' ? 'blue' : 'darkred') ?>">
                        <?= h($projectsUser->status) ?>
                    </td>

                        <?php if ($projectsUser->inducted_date!=null){
                            echo '<td>Completed</td>';
                        }else{
                            echo '<td>Incomplete</td>';
                        };?>
                    <?php if ($projectsUser->inducted_date!=null){ ?>
                        <td><?= h($projectsUser->inducted_date)?></td>
                    <?php }else{
                        echo '<td>N/A</td>';
                    };?>

                    <td class="actions">
                        <?php
                        $actionText = in_array($projectsUser->status, ['Engaged', 'Co-Manager']) ? __('Remove from Project') : __('Delete Record');
                        $confirmMessage = in_array($projectsUser->status, ['Engaged', 'Co-Manager'])
                            ? __('Are you sure you want to remove {0} from the project?', h($projectsUser->first_name.' '.$projectsUser->last_name))
                            : __('Are you sure you want to delete the record of {0}?', h($projectsUser->first_name.' '.$projectsUser->last_name));
                        ?>
                        <?= $this->Form->postLink($actionText, ['action' => 'deleteProjectUser', $projectsUser->project_id, $projectsUser->id], ['confirm' => $confirmMessage]) ?>
                        <br>
                        <?php
                        if ($currentUser->id == $project->builder_id && $builderCompanyId == $projectsUser->company_id) {
                            if ($projectsUser->status != 'Disengaged') {
                                // Determine the text and confirmation message for promoting or demoting
                                $promotionText = $projectsUser->status == 'Co-Manager' ? __('Demote to Regular Worker') : __('Promote to Co-Manager');
                                $promotionConfirmMessage = $projectsUser->status == 'Co-Manager'
                                    ? __('Are you sure you want to demote {0} to a regular worker?', h($projectsUser->first_name.' '.$projectsUser->last_name))
                                    : __('Are you sure you want to promote {0} to a Co-Manager? This will give them access to all aspects of the project.', h($projectsUser->first_name.' '.$projectsUser->last_name));
                                ?>

                                <?= $this->Form->postLink($promotionText, ['action' => 'promoteToCoManager', $projectsUser->project_id, $projectsUser->user_id], ['confirm' => $promotionConfirmMessage]) ?>
                            <?php }
                        }
                        ?>

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
    $(document).ready(function() {
        $("#name-filter").on("input", function() {
            var searchText = $(this).val().toLowerCase();

            // Loop through each row in the table
            $("table tbody tr").each(function() {
                var name = $(this).find("td:first").text().toLowerCase();

                // Check if the name contains the search text
                if (name.indexOf(searchText) === -1) {
                    $(this).hide(); // Hide rows that don't match
                } else {
                    $(this).show(); // Show rows that match
                }
            });
        });
    });
</script>

