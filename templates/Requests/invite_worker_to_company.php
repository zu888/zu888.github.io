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
            <?= $this->Form->create($email) ?>
            <fieldset>
                <legend><?= __("Invite an Employee") ?></legend>
                <p>Please enter an email that corresponds to a specific user.</p>
                <table class="table table-bordered" style="background-color:ghostwhite;">
                    <div class="row" style="margin: auto">
                        <?php
                        echo $this->Form->control('email', ['label' => 'Email', 'required'=> true]);?>
                    </div>
                </table>
            </fieldset>
            <?= $this->Form->button(__('Invite Member'),['name' => 'join','value' => 'member'])?>


            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
