<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ProjectsDocument $projectsDocument
 * @var string[]|\Cake\Collection\CollectionInterface $projects
 * @var string[]|\Cake\Collection\CollectionInterface $users
 * @var string[]|\Cake\Collection\CollectionInterface $documents
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $projectsDocument->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $projectsDocument->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Projects Documents'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="projectsDocuments form content">
            <?= $this->Form->create($projectsDocument) ?>
            <fieldset>
                <legend><?= __('Edit Projects Document') ?></legend>
                <?php
                    echo $this->Form->control('project_id', ['options' => $projects]);
                    echo $this->Form->control('document_id', ['options' => $documents]);
                    echo $this->Form->control('company_id');
                    echo $this->Form->control('user_id', ['options' => $users, 'empty' => true]);
                    echo $this->Form->control('status');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
