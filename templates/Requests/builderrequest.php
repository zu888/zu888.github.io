<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Request $request
 */
$currentUser = $this->request->getAttribute('identity');
?>

<?php echo $this->Html->css('main'); ?>

    <div class="row content">
        <div class="column-responsive column-80 boxed-content" style="margin: auto">
            <div class="documents form content">
                <?= $this->Form->create($license) ?>
                <fieldset>
                    <legend><?= __("Apply to be a builder") ?></legend>
                    <p>Please enter your building license numbers.</p>
                    <table class="table table-bordered" style="background-color:ghostwhite;">
                        <div class="row" style="margin: auto">
                            <?php
                            echo $this->Form->control('License numbers', ['label' => 'License Number:', 'required'=> true]);?>
                        </div>
                    </table>
                </fieldset>
                    <?= $this->Form->button(__('Submit'))?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>

<?php
