<?php
$currentUser = $this->request->getAttribute('identity');

?>
<div class="row content">
    <div class="column-responsive column-80">
        <div class="companies view">
            <h3><?= __('Add Worker to Company:') ?> <?= h($company->name) ?></h3>
            <table class="table table-bordered" style="background-color: ghostwhite; max-width: 800px">
                <!-- Company details table... -->
            </table>

            <?php if ($currentUser->role == 'Admin' || $currentUser->role == 'Builder'): ?>
                <!-- Assigned Members to this Company table... -->
            <?php endif; ?>


            <?= $this->Form->create(null, ['url' => ['action' => 'addworker', $company->id]]) ?>
            <fieldset>
                <?php
                echo $this->Form->control('worker_id', [
                    'label' => __('Select Worker'),
                    'type' => 'select',
                    'options' => $availableWorkers,
                    'empty' => __('Select a worker'),
                    'class' => 'form-control',
                ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Add Worker'), ['class' => 'btn btn-primary']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
