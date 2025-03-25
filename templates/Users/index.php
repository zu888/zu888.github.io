<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];
?>
<div class="users index content">


    <h3><?= __('Users') ?></h3>
    <div class="table-responsive">
        <table class="table table-bordered" style="background-color:ghostwhite; max-width:1200px">
            <thead class="thead-dark">
            <tr>
                <th><?= $this->Paginator->sort('role') ?></th>
                <th><?= $this->Paginator->sort('full_name', ['label' => 'Name']) ?></th>
                <th><?= $this->Paginator->sort('full_address', ['label' => 'Address']) ?></th>
                <th><?= $this->Paginator->sort('email') ?></th>
                <th><?= $this->Paginator->sort('phone_mobile') ?></th>
                <th><?= $this->Paginator->sort('phone_office') ?></th>
                <th><?= $this->Paginator->sort('emergency_name') ?></th>
                <th><?= $this->Paginator->sort('emergency_relationship') ?></th>
                <th><?= $this->Paginator->sort('emergency_phone') ?></th>

            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= h($user->role) ?></td>
                    <td><?= h($user->first_name . ' ' . $user->last_name) ?></td>
                    <td><?= h($user->address_no . ' ' . $user->address_street . ', ' . $user->address_suburb . ', ' . $state[$user->address_state] . ' ' . $user->address_postcode . ', ' . $user->address_country) ?></td>
                    <td><?= h($user->email) ?></td>
                    <td><?= h($user->phone_mobile) ?></td>
                    <td><?= h($user->phone_office) ?></td>
                    <td><?= h($user->emergency_name) ?></td>
                    <td><?= h($user->emergency_relationship) ?></td>
                    <td><?= h($user->emergency_phone) ?></td>
                    <td class="actions">
                        <?php // $this->Html->link(__('View'), ['action' => 'view', $user->id]) ?>
                        <?php //$this->Html->link(__('Edit'), ['action' => 'edit', $user->id]) ?>
<!--                        --><?php //= $this->Form->postLink(__('Delete'), ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete # {0}?', $user->id)]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
      </div>
</div>
