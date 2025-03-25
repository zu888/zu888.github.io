<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
$currentUser = $this->request->getAttribute('identity');
$state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];
?>

<?php if ($currentUser->role == 'Admin'){ ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="dashboard-box text-center">
                <h2 class="display-4">Pending Builder Requests</h2>
                <p class="huge-number" id="builderRequestsValue"><?= $builderRequests ?></p>
                <?= $this->Html->link('View Requests', ['controller' => 'Requests', 'action' => 'index']) ?>
            </div>
        </div>
        <script>
            // Get the builderRequests value from the HTML element
            const builderRequestsValue = parseFloat(document.getElementById('builderRequestsValue').textContent);

            // Set the text color based on the builderRequests value
            if (builderRequestsValue == 0) {
                document.getElementById('builderRequestsValue').style.color = 'green';
            } else if (builderRequestsValue == 1) {
                document.getElementById('builderRequestsValue').style.color = 'teal';
            } else if (builderRequestsValue == 2) {
                document.getElementById('builderRequestsValue').style.color = '#FFC270';
            } else if (builderRequestsValue == 3) {
                document.getElementById('builderRequestsValue').style.color = 'orange';
            } else if (builderRequestsValue == 4) {
                document.getElementById('builderRequestsValue').style.color = '#FF5349';
            } else {
                document.getElementById('builderRequestsValue').style.color = 'red';
            }
        </script>
        <div class="col-lg-4 col-md-6">
            <div class="dashboard-box text-center">
                <h2 class="display-4">Companies</h2>
                <p class="huge-number"><?= $companyCount ?></p>
                <?= $this->Html->link('View Companies', ['controller' => 'Companies', 'action' => 'index']) ?>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="dashboard-box text-center">
                <h2 class="display-4">Users</h2>
                <p class="huge-number"><?= $userCount ?></p>
                <?= $this->Html->link('View Users', ['controller' => 'Users', 'action' => 'index']) ?>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="dashboard-box text-center">
                <h2 class="display-4">Total Projects</h2>
                <p class="huge-number"><?= $projectCount ?></p>
                <?= $this->Html->link('View Projects', ['controller' => 'Projects', 'action' => 'index']) ?>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="dashboard-box text-center">
                <h2 class="display-4">Active Projects</h2>
                <p class="huge-number"><?= $activeProjectCount ?></p>
               </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="dashboard-box text-center">
                <h2 class="display-4">Pieces of Equipment</h2>
                <p class="huge-number"><?= $equipmentCount ?></p>
            </div>
        </div>
    </div>
</div>

<style>
    .huge-number {
        font-size: 48px; /* Adjust the font size as needed */
        font-weight: bold;
    }
</style>
<?php } ?>

<?php if ($currentUser->role == 'Builder'): ?>

    <style>
        .list-container {
            border: 1px solid #ccc;
            padding: 10px;
        }

        .list-container table {
            width: 100%;
        }

        .list-container th, .list-container td {
            padding: 5px;
            border-bottom: 1px solid #ccc;
        }

        .project-list {
            padding-left: 10px;
        }

        .user-list {
            padding-right: 10px;
        }

        .search-bar {
            margin-bottom: 10px;
            padding: 5px;
            border: 1px solid #ccc;
            width: 100%;
        }
    </style>

    <script>
        // Function to filter list items using regex
        function filterList(inputId, listContainerId) {
            var input = document.getElementById(inputId);
            var filter = new RegExp(input.value, 'i');
            var listContainer = document.getElementById(listContainerId);
            var items = listContainer.querySelectorAll('li');

            items.forEach(function(item) {
                if (filter.test(item.textContent)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>

    <div class="row" style="margin-left: -10px; margin-right: -10px;">
        <div class="col-md-3">
            <h3>My Projects</h3>
            <input type="text" id="projectFilter" class="search-bar" placeholder="Search Projects" onkeyup="filterList('projectFilter', 'projectList')">
            <div class="list-container project-list" id="projectList" style="height: auto;">
                <ul>
                    <?php foreach ($projects as $project): ?>
                        <li>
                            <?= $this->Html->link(html_entity_decode($project->name), ['controller' => 'Projects', 'action' => 'view', $project->id]) ?>
                            <div>
                                Address: <?= h($project->address_no . ' ' . $project->address_street . ', ' . $project->address_suburb . ', ' . $state[$project->address_state]) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>

                </ul>
            </div>
        </div>



        <div class="col-md-3">
            <h3>My Associated Companies</h3>
            <input type="text" id="companyFilter" class="search-bar" placeholder="Search Companies" onkeyup="filterList('companyFilter', 'companyList')">
            <div class="list-container" id="companyList">
                <ul>
                    <?php foreach ($companies as $companyId => $companyName): ?>
                        <li>
                            <?= $this->Html->link(html_entity_decode($companyName), ['controller' => 'Companies', 'action' => 'view', $companyId]) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="col-md-3">
            <h3>My Associated Workers</h3>
            <input type="text" id="userFilter" class="search-bar" placeholder="Search Users" onkeyup="filterList('userFilter', 'userList')">
            <div class="list-container user-list" id="userList">
                <ul>
                    <?php foreach ($users as $userId => $userName): ?>
                        <li>

                            <?= $this->Html->link(html_entity_decode($userName), ['controller' => 'Users', 'action' => 'view', $userId]) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="col-md-3">
            <h3>My Documents</h3>
            <input type="text" id="documentFilter" class="search-bar" placeholder="Search Documents" onkeyup="filterList('documentFilter', 'documentList')">
            <div class="list-container document-list" id="documentList">
                <ul>
                    <?php foreach ($documents as $document): ?>
                        <li>
                            <?= $this->Html->link(h($document->name), ['controller' => 'Documents', 'action' => 'view', $document->id]) ?>
                            <div>
                                Associated Project: <?= h($associatedProjects[$document->related_project_id]) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>


<?php endif; ?>








