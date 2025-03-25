<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Subcontract $subcontract
 * @var string[]|\Cake\Collection\CollectionInterface $projects
 * @var string[]|\Cake\Collection\CollectionInterface $companies
 * @var string[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div class="row" style="margin-left: 30px;margin-top: 20px;">
    <aside class="column">
        <div class="side-nav">
            <?= $this->Html->link(__('Back to Subcontracts'), ['action' => 'index', $project], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="subcontracts form content">
            <?= $this->Form->create($subcontract, ['class' => 'subcontract-form']) ?>
            <fieldset>
                <legend class="subcontract-legend"><?= __('Edit Subcontract') ?></legend>
                <?php


                echo $this->Form->control('parent_company_id', [
                    'options' => $companies,
                    'class' => 'subcontract-control',
                    'label' => 'Initially Contracted to',
                    'style' => 'margin-left: 10px;'
                ]);

                echo $this->Form->control('child_worker_id', [
                    'options' => $users,
                    'class' => 'subcontract-control',
                    'label' => 'Ultimately Subcontracted To',
                    'style' => 'margin-left: 10px;'
                ]);


                echo $this->Form->control('description', [
                    'class' => 'subcontract-control',
                    'style' => 'width: 95%; height: 150px;'
                ]);
                ?>

            </fieldset>
            <?= $this->Form->button(__('Submit'), ['class' => 'subcontract-button']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.js" integrity="sha512-+UiyfI4KyV1uypmEqz9cOIJNwye+u+S58/hSwKEAeUMViTTqM9/L4lqu8UxJzhmzGpms8PzFJDzEqXL9niHyjA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    jQuery(function () {
        jQuery('.subcontract-form #est-completion-date').datetimepicker({
            onShow: function (ct) {
                this.setOptions({
                    startDate:'+1970/01/01',
                    minDate:'+1970/01/01',
                    format:'d-m-Y'
                })
            },
            timepicker: false
        });
    });
</script>
