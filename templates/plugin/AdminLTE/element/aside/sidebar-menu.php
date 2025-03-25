<?php
use Cake\ORM\TableRegistry;

$currentUser = $this->request->getAttribute('identity');
$company_id = $this->request->getSession()->read('company_id');
$role = NULL;
if($currentUser){
    $role = $currentUser->role;
}
$companiesTable = TableRegistry::getTableLocator()->get('Companies');

// Query the Companies table to find the company where admin_id matches the user's ID
if($currentUser){
$myCompany = $companiesTable->find()
    ->where(['admin_id' => $currentUser->id])
    ->first();
}
?>
<script src="https://kit.fontawesome.com/134a1e4612.js" crossorigin="anonymous"></script>
<ul class="sidebar-menu" data-widget="tree">
    <?php
        if($role == 'Admin'){ ?>
            <!-- <li class="header">Admin Navigation</li> -->

            <!-- Dashboard Section -->
            <li><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'Dashboard']) ?>"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a></li>

            <!-- Site Management Section -->
            <li class="header">Site Management</li>
            <li><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'index']) ?>"><i class="fa fa-users"></i> <span>All Users</span></a></li>
            <li><a href="<?= $this->Url->build(['controller' => 'Projects', 'action' => 'allprojects']) ?>"><i class="fa fa-bars-progress"></i> <span>All Projects</span></a></li>
            <li><a href="<?= $this->Url->build(['controller' => 'Companies', 'action' => 'index']) ?>"><i class="fa fa-city"></i> <span>All Companies</span></a></li>
            <li><a href="<?= $this->Url->build(['controller' => 'requests', 'action' => 'index']) ?>"><i class="fa fa-user-clock"></i> <span>Pending Requests</span></a></li>

        <?php } elseif($role == 'Builder'){ ?>
            <!-- <li class="header">Builder Navigation</li> -->

            <!-- Dashboard Section -->
            <li><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'Dashboard']) ?>"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a></li>

            <!-- Projects Section -->
            <li class="header">Projects</li>
            <li><a href="<?= $this->Url->build(['controller' => 'Projects', 'action' => 'index']) ?>"><i class="fa fa-bars-progress"></i> <span>My Projects</span></a></li>
            <li><a href="<?= $this->Url->build(['controller' => 'requests', 'action' => 'builderProjectInvitation']) ?>"><i class="fa fa-pen-to-square"></i> <span>My Project Invitation</span></a></li>

            <!-- Company Management Section -->
            <li class="header">Company Management</li>
            <?php if ($myCompany) : ?>
                <li>
                    <a href="<?= $this->Url->build(['controller' => 'Companies', 'action' => 'view', $myCompany->id]) ?>">
                        <i class="fa fa-building"></i> <span>My Company</span>
                    </a>
                </li>
            <?php else : ?>
                <li>
                    <a href="<?= $this->Url->build(['controller' => 'Companies', 'action' => 'add']) ?>">
                        <i class="fa fa-building"></i> <span>Create Company</span>
                    </a>
                </li>
            <?php endif; ?>
            <li><a href="<?= $this->Url->build(['controller' => 'Companies', 'action' => 'mysubcontractor']) ?>"><i class="fa fa-arrow-right-to-city"></i> <span>My Subcontractors</span></a></li>

            <!-- Requests Section -->
            <li class="header">Requests</li>
            <li><a href="<?= $this->Url->build(['controller' => 'requests', 'action' => 'CompanyInvitation']) ?>"><i class="fa fa-envelope-open"></i> <span>My Company Invitation</span></a></li>
            <li><a href="<?= $this->Url->build(['controller' => 'Requests', 'action' => 'index']) ?>"><i class="fa fa-user-clock"></i> <span>Pending Requests</span></a></li>

        <?php } elseif($role == 'Contractor' || $role == 'Subcontractor'){?>
            <li class="header">Builder Application</li>
            <li>
                <a href="<?= $this->Url->build(['controller' => 'Requests', 'action' => 'builderrequest']) ?>">
                    <i class="fa fa-hammer"></i> <span>Become a Builder</span>
                </a>
            </li>
            <!-- Company Management Section -->
            <li class="header">Company Management</li>
            <?php if ($myCompany) : ?>
                <li>
                    <a href="<?= $this->Url->build(['controller' => 'Companies', 'action' => 'view', $myCompany->id]) ?>">
                        <i class="fa fa-building"></i> <span>My Company</span>
                    </a>
                </li>
            <?php else : ?>
                <li>
                    <a href="<?= $this->Url->build(['controller' => 'Companies', 'action' => 'add']) ?>">
                        <i class="fa fa-building"></i> <span>Create Company</span>
                    </a>
                </li>
            <?php endif; ?>
            <li><a href="<?= $this->Url->build(['controller' => 'Companies', 'action' => 'myclient']) ?>"><i class="fa fa-users"></i> <span>My Clients</span></a></li>
            <li><a href="<?= $this->Url->build(['controller' => 'Companies', 'action' => 'mysubcontractor']) ?>"><i class="fa fa-arrow-right-to-city"></i> <span>My Subcontractors</span></a></li>
<!--            <li><a href="--><?php //= $this->Url->build(['controller' => 'Requests', 'action' => 'joincompany']) ?><!--"><i class="fa fa-building-o"></i> <span>Join New Company</span></a></li>-->

            <!-- Projects Section -->
            <li class="header">Projects</li>
            <li><a href="<?= $this->Url->build(['controller' => 'Projects', 'action' => 'index']) ?>"><i class="fa fa-bars-progress"></i> <span>My Projects</span></a></li>
            <li><a href="<?= $this->Url->build(['controller' => 'Requests', 'action' => 'joinproject']) ?>"><i class="fa fa-briefcase"></i> <span>Join New Project</span></a></li>


            <!-- Requests Section -->
            <li class="header">Requests</li>
            <li><a href="<?= $this->Url->build(['controller' => 'requests', 'action' => 'index']) ?>"><i class="fa fa-user-plus"></i> <span>My Requests</span></a></li>
            <li><a href="<?= $this->Url->build(['controller' => 'Requests', 'action' => 'companyrequestindex']) ?>"><i class="fa fa-user-clock"></i> <span>Pending Requests</span></a></li>

            <!-- Invitations Section -->
            <li class="header">Invitations</li>
            <li><a href="<?= $this->Url->build(['controller' => 'requests', 'action' => 'CompanyInvitation']) ?>"><i class="fa fa-envelope-circle-check"></i> <span>My Company Invitation</span></a></li>
            <li><a href="<?= $this->Url->build(['controller' => 'requests', 'action' => 'invitation']) ?>"><i class="fa fa-envelope-open"></i> <span>Pending Invitation</span></a></li>


        <?php } elseif($role == 'On-site Worker'){ ?>
                <!-- <li class="header"><?= h($role) ?> Navigation</li> -->

            <!-- Current Engagements Section -->
                <li class="header">Current Engagements</li>
                <li><a href="<?= $this->Url->build(['controller' => 'Companies', 'action' => 'myindex']) ?>"><i class="fa fa-building-user"></i> <span>My Companies</span></a></li>
                <li><a href="<?= $this->Url->build(['controller' => 'Projects', 'action' => 'index']) ?>"><i class="fa fa-bars-progress"></i> <span>My Projects</span></a></li>

            <!-- Onboarding Section -->
                <li class="header">Onboarding</li>
                <li><a href="<?= $this->Url->build(['controller' => 'Requests', 'action' => 'joincompany']) ?>"><i class="fa fa-building-circle-arrow-right"></i> <span>Join New Company</span></a></li>
                <li><a href="<?= $this->Url->build(['controller' => 'Requests', 'action' => 'joinproject']) ?>"><i class="fa fa-pen-to-square"></i> <span>Join New Project</span></a></li>

                <!-- Requests Section -->
                <li class="header">Requests</li>
                <li><a href="<?= $this->Url->build(['controller' => 'requests', 'action' => 'index']) ?>"><i class="fa fa-user-plus"></i> <span>My Requests</span></a></li>
                <li><a href="<?= $this->Url->build(['controller' => 'requests', 'action' => 'invitation']) ?>"><i class="fa fa-user-clock"></i> <span>Pending Invitation</span></a></li>

        <?php }elseif($role == 'Visitor' || $role == 'Client' || $role == 'Consultant'){?>
            <li class="header"><?= h($role) ?> Navigation</li>
            <li><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'view', $currentUser->id]) ?>"><i class="fa fa-user"></i> <span>My Account</span></a></li>
        <?php } ?>
</ul>
