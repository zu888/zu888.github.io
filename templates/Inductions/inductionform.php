<?php

$type = '';
$id =  '';



$agreementStatus = false
?>

<div class="row content">
    <div class="column-responsive column-80">
        <div class="documents view content">
            <legend><?= __('Poject Document  -  <strong>' . $project->name . '</strong>') ?></legend>
            <p1><strong style="color: red; font-style: italic;"><?= __('You need to reach an agreement on ' . $number . ' documents.') ?></strong></p1>
            <br>
            <br>

            <?php foreach ($documents as $document): ?>
            <table class="table table-bordered" style="background-color:ghostwhite; max-width:800px">
                <tr>
                    <th><?= __('Document Name') ?></th>
                    <td><?= h($document->document_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Document Type') ?></th>
                    <td><?= h($document->document_type) ?></td>
                </tr>
            </table>
            <div id="content-desktop">

               <!-- <embed src="<?php /*= $this->Url->build(DS . 'uploads' . DS . $type . DS . $id . DS . $document->id . '.' . $document->extension) */?>" width="100%" height="550px" />-->
                <iframe src="<?= $this->Url->build(DS . 'uploads' . DS . 'Induction' . DS . $projectID . DS . $document->document_id . '.' . $document->extension) ?>" width="100%" height="550px" frameborder="0"></iframe>
            </div>
            <a class="btn btn-block btn-primary" style="width: 150px" href="<?= $this->Url->build(
                ['controller' => 'Documents', 'action' => 'download', $document->document_id]
            ) ?>">Download PDF</a>
            <br>
            <br>
            <?php endforeach; ?>
            <form method="POST">
                <label>
                    <input type="checkbox" name="agreement" <?= $agreementStatus ? 'checked disabled' : '' ?> required>
                    I have read and agree to the terms and conditions of this document.
                </label>
                <br>
                <button type="submit" class="btn btn-primary mt-3" style="width: 100px;" <?= $agreementStatus ? 'disabled' : '' ?>>Submit</button>
            </form>
        </div>
    </div>
</div>
