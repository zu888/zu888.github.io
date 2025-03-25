<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\UsersAgreement $usersAgreement
 * @var string[]|\Cake\Collection\CollectionInterface $users
 * @var string[]|\Cake\Collection\CollectionInterface $projects
 * @var string[]|\Cake\Collection\CollectionInterface $documents
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $usersAgreement->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $usersAgreement->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Users Agreements'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="usersAgreements form content">
            <?= $this->Form->create($usersAgreement) ?>
            <fieldset>
                <legend><?= __('Edit Users Agreement') ?></legend>
                <?php
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('project_id', ['options' => $projects]);
                    echo $this->Form->control('document_id', ['options' => $documents]);
                    echo $this->Form->control('agreed_at');
                    echo $this->Form->control('agreement_status');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
