<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Document $document
 */
$type = '';
$id =  '';

if ($document->related_project_id) {
    $type = 'Induction';
    $id = $document->related_project_id;
} elseif ($document->related_company_id) {
    $type = 'Company';
    $id = $document->related_company_id;
} elseif ($document->related_user_id) {
    $type = 'Personal';
    $id = $document->related_user_id;
}

$agreementStatus = false;
$currentUser = $this->request->getAttribute('identity');
$doc_status=null;
if ($currentUserAgreementStatus) {
    $doc_status = $currentUserAgreementStatus->status;
}
$uploaderId = $document->uploaded_user_id;
?>
<div class="row content">
    <div class="column-responsive column-80">
        <div class="documents view content">
        <a class="btn btn-secondary" href="javascript:history.go(-1)" style="text-decoration: underline;">Back</a>

        <a class="btn btn-block btn-primary" style="width: 200px" href="<?= $this->Url->build(
                                                                                ['controller' => 'Documents', 'action' => 'download', $document->id]
                                                                            ) ?>">Download Document</a> <br>
            <table class="table table-bordered" style="background-color:ghostwhite; max-width:800px ite; padding-left: 2.5%; padding-right: 2.5%; max-width: 100%">
                <!-- <tr>
                    <th><?= __('Worker Name') ?></th>
                    <td></td>
                </tr>
                <tr>
                    <th><?= __('Company') ?></th>
                    <td></td>
                </tr>
                <tr> -->
                    <th><?= __('Document') ?></th>
                    <td><?= h($document->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Document Type') ?></th>
                    <td><?= h($document->document_type) ?></td>
                </tr>
                <tr>
                    <th><?= __('Details') ?></th>
                    <td><?= h($document->details) ?></td>
                </tr>
                <tr>
                    <th><?= __('Issue Date') ?></th>
                    <td><?= h($document->issue_date) ?></td>
                </tr>
                <tr>
                    <th><?= __('Expiry Date') ?></th>
                    <td><?= h($document->expiry_date) ?></td>
                </tr>
            </table>
            <div id="content-desktop">
                <iframe src="<?= $this->Url->build(DS . 'uploads' . DS . $type . DS . $id . DS . $document->id . '.' . $document->extension) ?>" width="100%" height="550px" frameborder="0"></iframe>
               <!-- <embed src="<?php /*= $this->Url->build(DS . 'uploads' . DS . $type . DS . $id . DS . $document->id . '.' . $document->extension) */?>" width="100%" height="1200px" />-->
            </div>



            <!-- If current user is a worker/contractor, etc, they can agree to the contents of the document -->
            <?php if ($currentUser->role != 'Builder'&& $currentUser->id != $uploaderId) : ?>
                <form method="POST">
                    <label>
                        <input type="checkbox" name="review_action" value="review">
                        I have read and agree to the terms and conditions of this document.
                    </label>
                    <br>
                    <button type="submit" class="btn btn-primary mt-3">Submit</button>
                </form>
            <?php endif; ?>


            <!-- If current user is the Builder, they can review or reject the document -->
            <?php if ($currentUser->role === 'Builder' && $doc_status === 'Pending' && $currentUser->id != $uploaderId) : ?>
                <form method="POST">
                    <label>
                        <input type="radio" name="review_action" id="approve" value="approve" >
                        Approve
                    </label>
                    <label>
                        <input type="radio" name="review_action" id="reject" value="reject">
                        Reject
                    </label>
                    <br>
                    <label id="reject_comments" style="display: none">
                        Comments (if rejected):
                        <textarea name="reject_comments" rows="3" cols="40" maxlength="500"></textarea>
                    </label>
                    <br>
                    <button type="submit" class="btn btn-primary mt-3">Submit</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>

    // Function to show or hide reject comments based on the selected radio button
    const showComment = function () {
        if (reject.is(':checked')) {
            comment.show();
        } else {
            comment.hide();
        }

        if (approve.is(':checked')){
            comment.hide();
        }
    }

    const reject = $('#reject');
    const approve = $('#approve');
    const comment = $('#reject_comments');

    reject.on('change', showComment);
    approve.on('change', showComment)

    showComment();

</script>
