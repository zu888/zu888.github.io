<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Equipment $equipment
 */
$type = '';
$id =  '';
if($equipment -> related_project_id){
    $type = 'Induction';
    $id = $equipment -> related_project_id;
}
elseif($equipment -> related_company_id){
    $type = 'Company';
    $id = $equipment -> related_company_id;
}
elseif($equipment -> related_user_id){
    $type = 'Personal';
    $id = $equipment -> related_user_id;
}
?>


<div class="row content">

    <div class="column-responsive column-80">
        <div class="documents view content">
            <a class="btn btn-block btn-primary" style="width: 200px" href="<?= $this->Url->build(
                ['controller' => 'equipment', 'action' => 'edit', $equipment->id]) ?>">Edit Equipment</a>
<br>
            <table class="table table-bordered" style="background-color:ghostwhite; max-width:800px">
                <tr>
                    <th><?= __('Equipment') ?></th>
                    <td><?= h($equipment->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Description') ?></th>
                    <td><?= nl2br(h($equipment->description)) ?></td>
                </tr>
                <tr>
                    <th><?= __('Document Type') ?></th>
                    <td><?= h($equipment->equipment_type) ?></td>
                </tr>
                <tr>
                    <th><?= __('Licensed') ?></th>
                    <td><?= $equipment->is_licensed ? __('Yes') : __('No') ?></td>
                </tr>
                <tr>
                    <th><?= __('Hired From Date') ?></th>
                    <td><?= h($equipment->hired_from_date) ?></td>
                </tr>
                <tr>
                    <th><?= __('Hired Until Date') ?></th>
                    <td><?= h($equipment->hired_until_date) ?></td>
                </tr>
                <tr>
                    <th><?= __('Review Status') ?></th>
                    <td style="color: <?= ($equipment->review_status == 'Rejected') ? 'red' : (($equipment->review_status == 'Accepted') ? 'green' : 'orange') ?>; font-weight: bold;">
                        <?= h($equipment->review_status) ?>
                    </td>
                </tr>
                <?php if (!empty($equipment->review_reason)): ?>
                    <tr>
                        <th><?= __('Rejection Reason') ?></th>
                        <td><?= h($equipment->review_reason) ?></td>
                    </tr>
                <?php endif; ?>

                <tr>
                <tr>
                    <th><?= __('Photo as at: ') . h($equipment->image_date) ?></th>
                <td>
                        <?php
                        if (!empty($equipment->image)): ?>
                            <img src="<?= $this->Url->image($equipment->image) ?>" alt="Equipment Photo" style="max-width: 500px; max-height: 400px;" />
                        <?php else: ?>
                            No Photo Available
                        <?php endif; ?>
                    </td>
                </tr>


            </table>
            <div id ="content-desktop">
                <embed src="<?= $this->Url->build(DS.'uploads'.DS.$type.DS.$id.DS.$equipment->id.'.pdf') ?>" width="60%" height="1200px"/>
            </div>
            <div class="button-container">
                <a class="btn btn-primary" href="<?= $this->Url->build(['controller' => 'Documents', 'action' => 'download', $equipment->id]) ?>">Download PDF</a>
                <?php if ($equipment->review_status !== 'Accepted' && ($currentUser->id == $project->builder_id || $status == 'Co-Manager')): ?>
                <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#rejectionReasonModal">Reject</a>
                    <a class="btn btn-primary" href="<?= $this->Url->build(['controller' => 'equipment', 'action' => 'approve', $equipment->id]) ?>" onclick="return confirm('Are you sure you want to approve this equipment?');">Approve</a>
                <?php endif; ?>
            </div>
            <!-- Rejection Reason Modal -->
            <div class="modal fade" id="rejectionReasonModal" tabindex="-1" role="dialog" aria-labelledby="rejectionReasonModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectionReasonModalLabel">Rejection Reason</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Please enter the rejection reason:</p>
                            <input type="text" id="rejectionReasonInput" class="form-control">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="submitRejectionButton">Submit Rejection</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function () {
                    $('#submitRejectionButton').click(function () {
                        var rejectionReason = $('#rejectionReasonInput').val();
                        if (rejectionReason.trim() !== "") {
                            var equipmentId = <?= $equipment->id ?>; // Get the equipment ID
                            var url = '<?= $this->Url->build(['controller' => 'Equipment', 'action' => 'reject']) ?>';
                            url += '/' + equipmentId + '/' + encodeURIComponent(rejectionReason);
                            window.location.href = url;
                        }
                    });
                });
            </script>

        </div>
    </div>
</div>
