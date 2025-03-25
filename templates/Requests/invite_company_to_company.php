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
            <?= $this->Form->create($passcode) ?>
            <fieldset>
                <legend><?= __("Invite a Company") ?></legend>
                <p>Please enter a passcode that corresponds to a specific company.</p>
                <table class="table table-bordered" style="background-color:ghostwhite;">
                    <div class="row" style="margin: auto">
                        <?php
                        echo $this->Form->control('passcode', ['label' => 'Passcode', 'required'=> true]);?>
                    </div>
                </table>
            </fieldset>
            <?= $this->Form->button(__('Invite Company'),['name' => 'join','value' => 'company'])?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

