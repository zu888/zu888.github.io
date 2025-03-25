<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Request $request
 * @var \Cake\Collection\CollectionInterface|string[] $users
 */
?>
<div class="row content">
    <div class="column-responsive column-80">
        <div class="companies form content">
            <?= $this->Form->create($reason);
            ?>
            <fieldset>
                <legend><?= __('Add Comment of Rejection') ?></legend>
                <p><span style="color: red">*</span> Required fields</p>

                <table class="table table-bordered" style="background-color:ghostwhite;">
                    <div class="row" style="padding-left: 2.5%; padding-right: 2.5%; max-width: 100%">
                        <?php
                        echo $this->Form->control('Comment',['label' => 'Comment *','required'=> true, 'type' => 'textarea', 'placeholder'=>'State the reason here','maxlength'=>500]);
                        ?>
                    </div>
                </table>
            </fieldset>
            <?= $this->Form->button(__('Submit'), ['style' => 'float: right; margin-right: 5%;']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
