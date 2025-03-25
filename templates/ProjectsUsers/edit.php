<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ProjectsUser $projectsUser
 * @var string[]|\Cake\Collection\CollectionInterface $projects
 * @var string[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $projectsUser->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $projectsUser->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Projects Users'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="projectsUsers form content">
            <?= $this->Form->create($projectsUser) ?>
            <fieldset>
                <legend><?= __('Edit Projects User') ?></legend>
                <?php
                    echo $this->Form->control('project_id', ['options' => $projects]);
                    echo $this->Form->control('user_id', ['options' => $users]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
