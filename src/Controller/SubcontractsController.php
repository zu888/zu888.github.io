<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;

/**
 * Subcontracts Controller
 *
 * @property \App\Model\Table\SubcontractsTable $Subcontracts
 * @method \App\Model\Entity\Subcontract[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SubcontractsController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
        $this->Authentication->addUnauthenticatedActions(['add', 'edit', 'delete', 'index']);
    }

    /**
     * Index method
     *
     * @param int|null $projectId Project ID
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index($projectId = null)
    {
        $this->Authorization->skipAuthorization();



        $currentUser = $this->request->getAttribute('identity');
        $projects = $this->fetchTable('Projects');

        $request = $this->request;

        $project = $this->fetchTable('Projects')->get($projectId);

        $builderId = $projects->get($projectId)->builder_id;



        // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {
            // Find the user's status for the project
            $userStatus = $this->fetchTable('ProjectsUsers')->find()
                ->select(['status'])
                ->where([
                    'project_id' => $projectId,
                    'user_id' => $currentUser->id,
                ])
                ->first();

            // Check if the user's status is not "Co-Manager"
            if ($userStatus->status != 'Co-Manager') {
                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
            }
        }
        $this->paginate = [
            'contain' => ['Projects', 'Companies', 'Users'],
        ];

        // Build a custom query to filter by project_id
        $query = $this->Subcontracts->find()
            ->where(['project_id' => $projectId]);

        $subcontracts = $this->paginate($query);

        $this->set(compact('subcontracts', 'project'));
    }

    /**
     * View method
     *
     * @param string|null $id Subcontract id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->Authorization->skipAuthorization();

        $subcontract = $this->Subcontracts->get($id, [
            'contain' => ['Projects', 'Companies', 'Users'],
        ]);



        $currentUser = $this->request->getAttribute('identity');
        $projects = $this->fetchTable('Projects');

        $request = $this->request;

        $project = $request->getQuery('project_id');

        $builderId = $projects->get($request->getQuery('project_id'))->builder_id;



        // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {
            // Find the user's status for the project
            $userStatus = $this->fetchTable('ProjectsUsers')->find()
                ->select(['status'])
                ->where([
                    'project_id' => $request->getQuery('project_id'),
                    'user_id' => $currentUser->id,
                ])
                ->first();

            // Check if the user's status is not "Co-Manager"
            if ($userStatus->status != 'Co-Manager') {
                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
            }
        }

        $this->set(compact('subcontract', 'project'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($id = null)
    {
        $this->Authorization->skipAuthorization();

        // Check if the user's ID matches the builder_id for the project associated with the subcontract
        $currentUser = $this->Authentication->getIdentity();
        $projectId = $this->request->getQuery('project_id');

        if ($projectId) {
            $project = TableRegistry::getTableLocator()->get('Projects')->get($projectId);
        }

        $projects = $this->fetchTable('Projects');

        $request = $this->request;

        $project = $request->getQuery('project');

        $builderId = $projects->get($projectId)->builder_id;

        $project = $projects->get($projectId);

        // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {
            // Find the user's status for the project
            $userStatus = $this->fetchTable('ProjectsUsers')->find()
                ->select(['status'])
                ->where([
                    'project_id' => $request->getQuery('project_id'),
                    'user_id' => $currentUser->id,
                ])
                ->first();

            // Check if the user's status is not "Co-Manager"
            if ($userStatus->status != 'Co-Manager') {
                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
            }
        }
        $subcontract = $this->Subcontracts->newEmptyEntity();
        if ($this->request->is('post')) {

            $data = $this->request->getData();

            $subcontract = $this->Subcontracts->patchEntity($subcontract, $data);
            $subcontract->project_id = $project->id;
            $subcontract->child_worker_id = $data['ultimately_subcontracted_to'];
            $subcontract->parent_company_id = $data['initially_contracted_to'];
            $subcontract->description = $data['description'];


            if ($this->Subcontracts->save($subcontract)) {
                $this->Flash->success(__('The subcontract has been saved.'));
                // Check and create entry in companies_projects table
                $companiesProjectsTable = TableRegistry::getTableLocator()->get('CompaniesProjects');
                $companyProjectExists = $companiesProjectsTable->exists([
                    'company_id' => $subcontract->parent_company_id,
                    'project_id' => $subcontract->project_id,
                ]);
                if (!$companyProjectExists) {
                    $companyProject = $companiesProjectsTable->newEntity([
                        'company_id' => $subcontract->parent_company_id,
                        'project_id' => $subcontract->project_id,
                    ]);
                    $companiesProjectsTable->save($companyProject);
                }

                // Check and create entry in projects_users table
                $projectsUsersTable = TableRegistry::getTableLocator()->get('ProjectsUsers');
                $projectUserExists = $projectsUsersTable->exists([
                    'user_id' => $subcontract->child_worker_id,
                    'project_id' => $subcontract->project_id,
                ]);
                if (!$projectUserExists) {
                    $projectUser = $projectsUsersTable->newEntity([
                        'user_id' => $subcontract->child_worker_id,
                        'project_id' => $subcontract->project_id,
                    ]);
                    $projectsUsersTable->save($projectUser);
                }
                return $this->redirect(['action' => 'index', $project->id]);
            }
            $this->Flash->error(__('The subcontract could not be saved. Please, try again.'));
        }
        $projects = $this->Subcontracts->Projects->find('list', ['limit' => 200])->all();
        $projectsUsersTable = TableRegistry::getTableLocator()->get('ProjectsUsers');
        $companiesProjectsTable= TableRegistry::getTableLocator()->get('CompaniesProjects');

        $userIds = $projectsUsersTable->find()
            ->select(['user_id'])
            ->where(['project_id' => $projectId])
            ->distinct()
            ->extract('user_id')
            ->toArray();


        $companyIds = $companiesProjectsTable->find()
            ->select(['company_id'])
            ->where(['project_id' => $projectId])
            ->distinct()
            ->extract('company_id')
            ->toArray();



        if (!empty($userIds)) {
            $users = $this->Subcontracts->Users->find('list', [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                }
            ])
                ->where(['id IN' => $userIds])
                ->toArray();
        } else {
            $users = ['no companies/workers associated with this project'];
        }


        if (!empty($companyIds)) {
            $companies = $this->Subcontracts->Companies->find('list')
                ->where(['id IN' => $companyIds])
                ->toArray();
        } else {
            $companies = ['no companies/workers associated with this project'];
        }



        $this->set(compact('subcontract', 'projects', 'companies', 'users', 'project'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Subcontract id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {

        $this->Authorization->skipAuthorization();

        $subcontract = $this->Subcontracts->get($id, [
            'contain' => ['Projects', 'Companies', 'Users'],
        ]);


        $currentUser = $this->request->getAttribute('identity');
        $projects = $this->fetchTable('Projects');

        $request = $this->request;

        $project = $request->getQuery('projectId');

        $builderId = $projects->get($request->getQuery('projectId'))->builder_id;



        // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {
            // Find the user's status for the project
            $userStatus = $this->fetchTable('ProjectsUsers')->find()
                ->select(['status'])
                ->where([
                    'project_id' => $request->getQuery('projectId'),
                    'user_id' => $currentUser->id,
                ])
                ->first();

            // Check if the user's status is not "Co-Manager"
            if ($userStatus->status != 'Co-Manager') {
                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
            }
        }
        $subcontract = $this->Subcontracts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $subcontract = $this->Subcontracts->patchEntity($subcontract, $this->request->getData());
            if ($this->Subcontracts->save($subcontract)) {
                $this->Flash->success(__('The subcontract has been saved.'));

                return $this->redirect(['action' => 'index',$project->id]);
            }
            $this->Flash->error(__('The subcontract could not be saved. Please, try again.'));
        }
        $projects = $this->Subcontracts->Projects->find('list', ['limit' => 200])->all();
        $projectsUsersTable = TableRegistry::getTableLocator()->get('ProjectsUsers');
        $companiesProjectsTable= TableRegistry::getTableLocator()->get('CompaniesProjects');

        $userIds = $projectsUsersTable->find()
            ->select(['user_id'])
            ->where(['project_id' => $request->getQuery('projectId')])
            ->distinct()
            ->extract('user_id')
            ->toArray();


        $companyIds = $companiesProjectsTable->find()
            ->select(['company_id'])
            ->where(['project_id' => $request->getQuery('projectId')])
            ->distinct()
            ->extract('company_id')
            ->toArray();



        if (!empty($userIds)) {
            $users = $this->Subcontracts->Users->find('list', [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                }
            ])
                ->where(['id IN' => $userIds])
                ->toArray();
        } else {
            $users = ['no companies/workers associated with this project'];
        }


        if (!empty($companyIds)) {
            $companies = $this->Subcontracts->Companies->find('list')
                ->where(['id IN' => $companyIds])
                ->toArray();
        } else {
            $companies = ['no companies/workers associated with this project'];
        }

        $this->set(compact('subcontract', 'projects', 'companies', 'users', 'project'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Subcontract id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
/*
        $this->request->allowMethod(['post', 'delete']);
        $subcontract = $this->Subcontracts->get($id);
        if ($this->Subcontracts->delete($subcontract)) {
            $this->Flash->success(__('The subcontract has been deleted.'));
        } else {
            $this->Flash->error(__('The subcontract could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);*/
    }
}
