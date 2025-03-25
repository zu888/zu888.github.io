<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Request $request
 * @var \App\Model\Entity\Project $project
 */
$currentUser = $this->request->getAttribute('identity');
?>

<?php echo $this->Html->css('main'); ?>


<div class="row content">
    <div class="column-responsive column-80 boxed-content" style="margin: auto;">
        <div class="documents form content">
            <?= $this->Form->create($passcode) ?>
            <fieldset>
                <legend><?= __("Join a Project") ?></legend>
                <p>Please enter the passcode that corresponds to a specific project.</p>
                <table class="table table-bordered" style="background-color:ghostwhite;">
                    <div class="row" style="margin: auto">
                    <?php
                    echo $this->Form->control('passcode', ['label' => 'Passcode', 'required'=> true]);?>
                    </div>
                </table>
            </fieldset>
            <?php if($currentUser->role == 'On-site Worker'):?>
            <?= $this->Form->button(__('Join as a Worker'),['name' => 'join','value' => 'member'])?>
            <?php endif; ?>
            <?php if($currentUser->role != 'On-site Worker' && $ownacompany): ?>
            <?= $this->Form->button(__('Join as a Company/Contractor'), ['name' => 'join','value' => 'company'])?>
            <?php endif; ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
