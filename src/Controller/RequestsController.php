<?php
declare(strict_types=1);

namespace App\Controller;
use App\Model\Entity\CompaniesProject;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use function PHPUnit\Framework\isEmpty;

/**
 * Requests Controller
 *
 * @property \App\Model\Table\RequestsTable $Requests
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\ProjectsTable $projects
 * @method \App\Model\Entity\Request[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RequestsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $request = $this->Requests->newEmptyEntity();
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');

        if ($currentUser->role == "Admin") {

            $activeRequests = $this->fetchTable('Requests')->find()
                ->select(['id' => 'Requests.id', 'user_id' => 'Requests.user_id', 'company_id' => 'Requests.company_id', 'first_name' => 'u1.first_name', 'last_name' => 'u1.last_name',
                    'request_type' => 'Requests.request_type', 'request_text' => 'Requests.request_text', 'created_at' => 'Requests.created_at', 'approved_at' => 'Requests.approved_at', 'removal_status' => 'Requests.removal_status',])
                ->join([
                    "table" => "users u1",
                    "type" => "LEFT",
                    "conditions" => "Requests.user_id = u1.id"])
                ->where(['removal_status IN' => [0, 2], 'request_type' => 'Builder']);


            $companyRequests = $this->fetchTable('Requests')->find()
                ->select(['id' => 'Requests.id', 'first_name' => 'u1.first_name', 'last_name' => 'u1.last_name', 'user_role' => 'u1.role', 'companies_company_id' => 'c1.id', 'company_name' => 'c1.name', 'user_id' => 'Requests.user_id', 'project_id' => 'Requests.project_id', 'company_id' => 'Requests.company_id',
                    'request_type' => 'Requests.request_type', 'request_text' => 'Requests.request_text', 'created_at' => 'Requests.created_at', 'approved_at' => 'Requests.approved_at', 'removal_status' => 'Requests.removal_status',])
                ->join([
                    "table" => "companies c1",
                    "type" => "LEFT",
                    "conditions" => "Requests.company_id = c1.id"
                ])
                ->join([
                    "table" => "users u1",
                    "type" => "LEFT",
                    "conditions" => "Requests.user_id = u1.id"])
                ->where([
                    'removal_status IN' => [0, 2],
                    'u1.role' => 'Builder']);


            $this->paginate = [
                'contain' => ['Users'],
            ];
            $requests = $activeRequests->toList();

            $this->set(compact('requests', 'companyRequests'));
        } elseif ($currentUser->role == "Builder") {

            $project_builder = $this->fetchTable('Requests')
                ->find()
                ->select(['id' => 'Requests.id', 'first_name' => 'u1.first_name', 'last_name' => 'u1.last_name', 'projects_project_id' => 'p1.id', 'project_name' => 'p1.name', 'builder_id' => 'p1.builder_id', 'user_id' => 'Requests.user_id', 'project_id' => 'Requests.project_id', 'company_id' => 'Requests.company_id',
                    'request_type' => 'Requests.request_type', 'request_text' => 'Requests.request_text', 'created_at' => 'Requests.created_at', 'approved_at' => 'Requests.approved_at', 'removal_status' => 'Requests.removal_status', 'company_name' => 'c1.name', 'worker_company' => 'Requests.company_id_worker'])
                ->join([
                    "table" => "projects p1",
                    "type" => "LEFT",
                    "conditions" => "Requests.project_id = p1.id"
                ])
                ->join([
                    "table" => "users u1",
                    "type" => "LEFT",
                    "conditions" => "Requests.user_id = u1.id"])
                ->join([
                    "table" => "companies c1",
                    "type" => "LEFT",
                    "conditions" => "Requests.company_id = c1.id"
                ])->where([
                    'p1.builder_id' => $currentUser->id,
                    'removal_status IN' => [0, 2],
                ]);
            $companiesTable = TableRegistry::getTableLocator()->get('Companies');
            $workerCompanyID = [];
            foreach ($project_builder  as $request) {
                $workerCompanyID[] = $request['worker_company'];
            }
            if (!empty($workerCompanyID)) {
                $workerCompanyName = $companiesTable->find()
                    ->select(['id' => 'admin_id', 'name' => 'name', 'companyID' => 'id'])
                    ->where(['id IN' => $workerCompanyID])->toArray();

                $workercompanyNamesById = []; // Create an associative array for easier lookup

                foreach ($workerCompanyName as $company) {
                    $workercompanyNamesById[$company['companyID']] = [
                        'name' => $company['name'],
                    ];
                }

                foreach ($project_builder->toArray() as &$request) {
                    $workerID = $request['worker_company'];
                    if (isset($workercompanyNamesById[$workerID])) {
                        $request['workerCompanyName'] = $workercompanyNamesById[$workerID]['name'];
                    }
                }
                unset($request);
            }

            $company_user = $this->fetchTable('CompaniesUsers')->find()->select('company_id')->where(['user_id' => $currentUser->id]);

            if (!empty($company_user->toArray())) {

                $companyRequests = $this->fetchTable('Requests')->find()
                    ->select(['Request_id' => 'Requests.id', 'first_name' => 'u1.first_name', 'last_name' => 'u1.last_name', 'user_role' => 'u1.role', 'companies_company_id' => 'c1.id', 'company_name' => 'c1.name', 'user_id' => 'Requests.user_id', 'project_id' => 'Requests.project_id', 'company_id' => 'Requests.company_id',
                        'request_type' => 'Requests.request_type', 'request_text' => 'Requests.request_text', 'created_at' => 'Requests.created_at', 'approved_at' => 'Requests.approved_at', 'removal_status' => 'Requests.removal_status', 'admin_id' => 'c1.admin_id'])
                    ->join([
                        "table" => "companies c1",
                        "type" => "LEFT",
                        "conditions" => "Requests.company_id = c1.id"
                    ])
                    ->join([
                        "table" => "users u1",
                        "type" => "LEFT",
                        "conditions" => "Requests.user_id = u1.id"])->where([
                        'company_id IN' => $company_user,
                        'removal_status IN' => [0, 2],
                        'u1.role !=' => 'Builder'
                    ]);

                $companyRequests = $companyRequests->toArray();

                // debug($companyRequests);

                $companiesTable = TableRegistry::getTableLocator()->get('Companies');

                $AdminIds = [];
                foreach ($companyRequests as $request) {
                    $AdminIds[] = $request['user_id'];
                }
                //debug($AdminIds);

                if (!empty($AdminIds)) {
                    $company_name = $companiesTable->find()
                        ->select(['id' => 'admin_id', 'name' => 'name', 'companyID' => 'id'])
                        ->where(['admin_id IN' => $AdminIds])->toArray();
                    // debug($company_name);

                    $companyNamesById = []; // Create an associative array for easier lookup

                    foreach ($company_name as $company) {
                        $companyNamesById[$company['id']] = [
                            'name' => $company['name'],
                            'id' => $company['companyID']
                        ];
                    }

                    foreach ($companyRequests as &$request) {
                        $adminId = $request['user_id'];

                        if (isset($companyNamesById[$adminId])) {
                            $request['companyname'] = $companyNamesById[$adminId]['name'];
                        }
                        if (isset($companyNamesById[$adminId])) {
                            $request['requestcompanyID'] = $companyNamesById[$adminId]['id'];
                        }

                    }

                    unset($request);
                }
                // debug($companyRequests);
                $this->set('companyRequests', $companyRequests);
                // debug($companyRequests);
            }

            $this->set('requests', $project_builder);
//            $this->paginate = [
//                'contain' => ['Users'],
//            ];
//            $requests = $this->paginate($project_builder);
//
//            $this->set(compact('requests'));


        } else {
            $userRequests = $this->Requests->find()
                ->select([
                    "Request_id"=>"Requests.id",
                    "user_id" => "Requests.user_id",
                    "company_id" => "Requests.company_id",
                    "company_name" => "c1.name",
                    "project_id" =>"Requests.project_id",
                    "project_name"=> "p1.name",
                    "request_type"=> "Requests.request_type",
                    "request_text"=>"Requests.request_text",
                    "created_at" => "Requests.created_at",
                    "removal_status" => "Requests.removal_status",
                    "approved_at" =>"Requests.approved_at",
                    "comment" => "Requests.comment",
                    "company_id_worker" =>"Requests.company_id_worker"
                ])
                ->join([
                    "table" => "companies c1",
                    "type" => "LEFT",
                    "conditions" => "Requests.company_id = c1.id"
                ])
                ->join([
                    "table" => "projects p1",
                    "type" => "LEFT",
                    "conditions" => "Requests.project_id = p1.id"
                ])
                ->where([
                    'user_id' => $currentUser->id,
                    'request_type !=' => 'Builder']);

            //debug($userRequests->toArray());

            $builderRequests = $this->Requests->find()->where(['user_id' => $currentUser->id, 'request_type' => 'Builder']);

            $requests = $this->paginate($userRequests);

            $this->set(compact('requests',  'builderRequests'));


        }

    }

    public function companyrequestindex()
    {
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');

        $company_user = $this->fetchTable('CompaniesUsers')->find()->select('company_id')->where(['user_id' => $currentUser->id]);


        $companyRequests = $this->fetchTable('Requests')->find()
            ->select([
                'id' => 'Requests.id',
                'first_name' => 'u1.first_name',
                'last_name' => 'u1.last_name',
                'user_role' => 'u1.role',
                'companies_company_id' => 'c1.id',
                'company_name' => 'c1.name',
                'user_id' => 'Requests.user_id',
                'project_id' => 'Requests.project_id',
                'company_id' => 'Requests.company_id',
                'request_type' => 'Requests.request_type',
                'request_text' => 'Requests.request_text',
                'created_at' => 'Requests.created_at',
                'approved_at' => 'Requests.approved_at',
                'removal_status' => 'Requests.removal_status',
                'admin_id' => 'c1.admin_id'
            ])
            ->join([
                "table" => "companies c1",
                "type" => "LEFT",
                "conditions" => "Requests.company_id = c1.id"
            ])
            ->join([
                "table" => "users u1",
                "type" => "LEFT",
                "conditions" => "Requests.user_id = u1.id"
            ])->where([
                'company_id IN' => $company_user,
                'removal_status IN' => [0, 2]
            ]);


        //debug($companyRequests->toArray());

        $companyRequests = $companyRequests->toArray();
        $companiesTable = TableRegistry::getTableLocator()->get('Companies');

        $AdminIds = [];
        foreach ($companyRequests as $request) {
            $AdminIds[] = $request['user_id'];
        }
        //debug($AdminIds);

        if (!empty($AdminIds)) {
            $company_name = $companiesTable->find()
                ->select(['id' => 'admin_id', 'name' => 'name', 'company_id' => 'id'])
                ->where(['admin_id IN' => $AdminIds])->toArray();
            // debug($company_name);

            $companyNamesById = []; // Create an associative array for easier lookup

            foreach ($company_name as $company) {
                $companyNamesById[$company['id']] = [
                    'name' => $company['name'],
                    'company_id' => $company['company_id'],
                ];
            }
            // debug($companyNamesById);

            foreach ($companyRequests as &$request) {
                $adminId = $request['user_id'];
                // debug($adminId);
                if (isset($companyNamesById[$adminId])) {
                    $request['companyname'] = $companyNamesById[$adminId]['name'];
                    $request['requestedCompanyID'] = $companyNamesById[$adminId]['company_id'];
                }
            }
            //debug($request);
            unset($request);
        }
        // debug($companyRequests);
        $this->set('companyRequests', $companyRequests);


    }

    /**
     * View method
     *
     * @param string|null $id Request id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $request = $this->Requests->get($id, [
            'contain' => ['Users'],
        ]);

        $this->set(compact('request'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $request = $this->Requests->newEmptyEntity();
        $this->Authorization->authorize($request);

        if ($this->request->is('post')) {
            $request = $this->Requests->patchEntity($request, $this->request->getData());
            if ($this->Requests->save($request)) {
                $this->Flash->success(__('The request has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The request could not be saved. Please, try again.'));
        }
        $users = $this->Requests->Users->find('list', ['limit' => 200])->all();
        $this->set(compact('request', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Request id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $request = $this->Requests->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $request = $this->Requests->patchEntity($request, $this->request->getData());
            if ($this->Requests->save($request)) {
                $this->Flash->success(__('The request has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The request could not be saved. Please, try again.'));
        }
        $users = $this->Requests->Users->find('list', ['limit' => 200])->all();
        $this->set(compact('request', 'users'));
    }

    /**
     * Removal method
     *
     * @param string|null $id Request id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function removal($id)
    {

        $currentUser = $this->request->getAttribute('identity');
        $this->request->allowMethod(['post', 'delete', 'get']);
        $request = $this->Requests->get($id);
//        debug($request);
        $this->Authorization->skipauthorization();
        $request->removal_status = 1;
        if ($this->Requests->save($request)) {
            $this->Flash->success(__('The request has been removed.'));
        } else {
            $this->Flash->error(__('The request could not be removed. Please, try again.'));
        }
        $referer = $this->referer();
        if ($referer == '/requests/companyrequestindex') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'companyrequestindex']);
        } elseif ($referer == '/requests/invitation') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'invitation']);
        } elseif ($referer == '/requests/company-invitation') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'companyInvitation']);
        } elseif ($referer == '/requests/builder-project-invitation') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'builderProjectInvitation']);
        } else {
            return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
        }
    }


    /**
     * Delete method
     *
     * @param string|null $id Request id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete', 'get']);
        $currentUser = $this->request->getAttribute('identity');
        $this->Authorization->skipauthorization();
        $request = $this->Requests->get($id);

        if ($this->Requests->delete($request)) {
            $this->Flash->success(__('The request has been deleted.'));
        } else {
            $this->Flash->error(__('The request could not be deleted. Please, try again.'));
        }

        $referer = $this->referer();
        if ($referer == '/requests/companyrequestindex') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'companyrequestindex']);
        } elseif ($referer == '/requests/invitation') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'invitation']);
        } elseif ($referer == '/requests/company-invitation') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'companyInvitation']);
        } elseif ($referer == '/requests/builder-project-invitation') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'builderProjectInvitation']);
        } else {
            return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
        }
    }


    /**
     * Add builder request method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addBuilderRequest($userid = null, $requestText = null)
    {
        $request = $this->Requests->newEmptyEntity();
        $this->Authorization->authorize($request);

        $user = TableRegistry::getTableLocator()->get('Users')->find()->where(['id' => $userid])->first();

        $request->user_id = $user->id;
        $request->request_type = "Builder";
        $request->created_at = FrozenTime::now();
        $request->request_text = $requestText;
        $request->removal_status = 0;

        if ($this->Requests->save($request)) {
            $this->Flash->success(__('The request has been sent to the admin for approval .'));

            return $this->redirect(['controller' => 'users', 'action' => 'view', $userid]);
        }
        $this->Flash->error(__('The request could not be sent. Please, try again.'));
    }

    public function builderrequest(){
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $license = null;
        $this->set('license', $license);
        if ($this->request->is('post')) {
            $license = $this->request->getData('License_numbers');
            return $this->redirect(['controller' => 'Requests', 'action' => 'addBuilderRequest', $currentUser->id,$license]);
        }
    }

    /**
     * Add project request method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addProjectRequest($projectid = null, $userid = null)
    {
        $this->Authorization->skipAuthorization();

        $request = $this->Requests->newEmptyEntity();

        $project = TableRegistry::getTableLocator()->get('Projects')->find()->where(['id' => $projectid])->first();
        $user = TableRegistry::getTableLocator()->get('Users')->find()->where(['id' => $userid])->first();


        $request->user_id = $user->id;
        $request->request_type = "Project";
        $request->project_id = $project->id;
        $request->created_at = FrozenTime::now();
        $request->request_text = "User is requesting to join a Project ";
        $request->removal_status = 0;

        if ($this->Requests->save($request)) {
            $this->Flash->success(__('The request has been sent to the builder of the project to be approved.'));

            return $this->redirect(['controller' => 'projects', 'action' => 'allprojects']);
        }
        $this->Flash->error(__('The request could not be sent. Please, try again.'));
    }


    /**
     * Add company request method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addCompanyRequest($companyid = null, $userid = null)
    {

        $this->Authorization->skipAuthorization();
        $request = $this->Requests->newEmptyEntity();

        $company = TableRegistry::getTableLocator()->get('Companies')->find()->where(['id' => $companyid])->first();
        $user = TableRegistry::getTableLocator()->get('Users')->find()->where(['id' => $userid])->first();
        $request->user_id = $user->id;
        $request->request_type = "Company";
        $request->company_id = $company->id;
        $request->created_at = FrozenTime::now();
        $request->request_text = "User is requesting to join a Company ";
        $request->removal_status = 0;

        if ($this->Requests->save($request)) {
            $this->Flash->success(__('The request has been sent.'));
        } else {
            $this->Flash->error(__('The request could not be sent. Please, try again.'));
        }
    }

    /**
     * Approve request method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function approveRequest($requestid = null)
    {
        $request = $this->Requests->newEmptyEntity();
        $this->Authorization->skipauthorization();
        $currentUser = $this->request->getAttribute('identity');

        $request = TableRegistry::getTableLocator()->get('Requests')->find()->where(['id' => $requestid])->first();

        if ($request->request_type == "Builder") {
            $request->approved_at = FrozenTime::now();

            $userTable = TableRegistry::getTableLocator()->get('Users');
            $companyTable = TableRegistry::getTableLocator()->get('Companies');

            $user = $userTable->find()->where(['id' => $request->user_id])->first();
            $user->role = 'Builder';

            $company = $companyTable->find()->where(['admin_id' => $request->user_id])->first();
            $company->company_type = 'Builder';

            if ($this->Requests->save($request) && $userTable->save($user) && $companyTable->save($company)) {
                $this->Flash->success(__('The request has been approved.'));
            } else {
                $this->Flash->error(__('The request could not be approved. Please, try again.'));
            }

            $referer = $this->referer();
            //debug($referer);
            if ($referer == '/requests/companyrequestindex') {
                return $this->redirect(['controller' => 'Requests', 'action' => 'companyrequestindex']);
            } else {
                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
            }
        }
        if ($request->request_type == "Project_Member") {

            $request->approved_at = FrozenTime::now();

            $project_users = $this->fetchTable('ProjectsUsers');

            $project_user = $project_users->newEmptyEntity();

            $project_user->user_id = $request->user_id;
            $project_user->project_id = $request->project_id;
            $project_user->company_id = $request->company_id_worker;


            if ($this->Requests->save($request) && $project_users->save($project_user)) {
                $this->Flash->success(__('The request has been approved.'));

            } else {
                $this->Flash->error(__('The request could not be approved. Please, try again.'));
            }

            $referer = $this->referer();
            //debug($referer);
            if ($referer == '/requests/companyrequestindex') {
                return $this->redirect(['controller' => 'Requests', 'action' => 'companyrequestindex']);
            } else {
                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
            }

        }
        if ($request->request_type == "Company_Member") {

            $request->approved_at = FrozenTime::now();

            $company_users = $this->fetchTable('CompaniesUsers');

            $company_user = $company_users->newEmptyEntity();

            $company_user->user_id = $request->user_id;
            $company_user->company_id = $request->company_id;
            $company_user->confirmed = 1;

            if ($this->Requests->save($request) && $company_users->save($company_user)) {
                $this->Flash->success(__('The request has been approved.'));
            } else {
                $this->Flash->error(__('The request could not be approved. Please, try again.'));
            }

            $referer = $this->referer();
            //debug($referer);
            if ($referer == '/requests/companyrequestindex') {
                return $this->redirect(['controller' => 'Requests', 'action' => 'companyrequestindex']);
            } else {
                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
            }
        }

        if ($request->request_type == "Company_Company") {

            $request->approved_at = FrozenTime::now();

            $company_users = $this->fetchTable('CompaniesUsers');

            $company_user = $company_users->newEmptyEntity();

            $company_user->user_id = $request->user_id;
            $company_user->company_id = $request->company_id;
            $company_user->is_company_admin = 1;
            $company_user->confirmed = 1;

            if ($this->Requests->save($request) && $company_users->save($company_user)) {
                $this->Flash->success(__('The request has been approved.'));
            } else {
                $this->Flash->error(__('The request could not be approved. Please, try again.'));
            }

            $referer = $this->referer();
            //debug($referer);
            if ($referer == '/requests/companyrequestindex') {
                return $this->redirect(['controller' => 'Requests', 'action' => 'companyrequestindex']);
            } else {
                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
            }
        }


        if ($request->request_type == "Project_Company") {

            $request->approved_at = FrozenTime::now();
            $company_projects = $this->fetchTable('CompaniesProjects');
            $company_project = $company_projects->newEmptyEntity();

            $company_project->company_id = $request->company_id;
            $company_project->project_id = $request->project_id;

            if ($this->Requests->save($request) && $company_projects->save($company_project)) {
                $this->Flash->success(__('The request has been approved.'));
            } else {
                $this->Flash->error(__('The request could not be approved. Please, try again.'));
            }
            $referer = $this->referer();
            //debug($referer);
            if ($referer == '/requests/companyrequestindex') {
                return $this->redirect(['controller' => 'Requests', 'action' => 'companyrequestindex']);
            } else {
                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
            }
        }

        if ($request->request_type == "Project_Member_Invitation") {
            $request->approved_at = FrozenTime::now();

            $projectID = $request->project_id;
            $userID = $currentUser->id;

            $company_users_table = $this->fetchTable('ProjectsUsers');
            $projectsCompany = $company_users_table->newEmptyEntity();
            $existingRecord = $company_users_table->find()
                ->where(['user_id' => $userID, 'project_id' => $projectID])
                ->first();
            if (!$existingRecord) {
                $companyUserdata = [
                    'user_id' => $userID,
                    'project_id' => $projectID
                ];
                $company_users_table->patchEntity($projectsCompany, $companyUserdata);
                if ($this->Requests->save($request) && $company_users_table->save($projectsCompany)) {

                    $this->Flash->success(__('You have been assigned to the project.'));
                } else {
                    $this->Flash->error(__('You could not join the project. Please, try again.'));
                }
            } else {
                $this->Flash->error(__('You are already associated with this project. Please, try again.'));
            }
            $referer = $this->referer();
            //debug($referer);
            if ($referer == '/requests/invitation') {
                return $this->redirect(['controller' => 'Requests', 'action' => 'invitation']);
            } else {
                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
            }
        }

        if ($request->request_type == "Project_Company") {

            $request->approved_at = FrozenTime::now();
            $company_projects = $this->fetchTable('CompaniesProjects');
            $company_project = $company_projects->newEmptyEntity();

            $company_project->company_id = $request->company_id;
            $company_project->project_id = $request->project_id;

            if ($this->Requests->save($request) && $company_projects->save($company_project)) {
                $this->Flash->success(__('The request has been approved.'));
            } else {
                $this->Flash->error(__('The request could not be approved. Please, try again.'));
            }
            $referer = $this->referer();
            //debug($referer);
            if ($referer == '/requests/companyrequestindex') {
                return $this->redirect(['controller' => 'Requests', 'action' => 'companyrequestindex']);
            } else {
                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
            }
        }

        if ($request->request_type == "Project_Company_Invitation") {
            $request->approved_at = FrozenTime::now();

            $projectID = $request->project_id;
            $companyID = $request->company_id;

            $company_users_table = $this->fetchTable('CompaniesProjects');
            $projectsCompany = $company_users_table->newEmptyEntity();
            $existingRecord = $company_users_table->find()
                ->where(['company_id' => $companyID, 'project_id' => $projectID])
                ->first();
            if (!$existingRecord) {
                $projectsCompanydata = [
                    'company_id' => $companyID,
                    'project_id' => $projectID
                ];
                $company_users_table->patchEntity($projectsCompany, $projectsCompanydata);
                if ($this->Requests->save($request) && $company_users_table->save($projectsCompany)) {

                    $this->Flash->success(__('Your company have been assigned to the project.'));
                } else {
                    $this->Flash->error(__('Your company could not join the project. Please, try again.'));
                }
            } else {
                $this->Flash->error(__('Your company has already associated with this project. Please, try again.'));
            }
            $referer = $this->referer();
            //debug($referer);
            if ($referer == '/requests/invitation') {
                return $this->redirect(['controller' => 'Requests', 'action' => 'invitation']);
            } else {
                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
            }
        }

        if ($request->request_type == "Company_Member_Invitation") {
            $request->approved_at = FrozenTime::now();

            $companyID = $request->company_id;
            $userID = $currentUser->id;

            $company_users_table = $this->fetchTable('CompaniesUsers');

            $companyuser = $company_users_table->newEmptyEntity();

            $existingRecord = $company_users_table->find()
                ->where(['user_id' => $userID, 'company_id' => $companyID])
                ->first();
            if (!$existingRecord) {
                $companyUserdata = [
                    'company_id' => $companyID,
                    'user_id' => $userID,
                    'is_company_admin' => 0,
                    'confirmed' => 1,
                    'status' => 'Engaged'
                ];
                $company_users_table->patchEntity($companyuser, $companyUserdata);
                if ($this->Requests->save($request) && $company_users_table->save($companyuser)) {

                    $this->Flash->success(__('You have been engaged with this company'));
                } else {
                    $this->Flash->error(__('You could not join this company. Please, try again.'));
                }
            } else {
                $this->Flash->error(__('You are already associated with this company. Please, try again.'));
            }
            $referer = $this->referer();
            //debug($referer);
            if ($referer == '/requests/invitation') {
                return $this->redirect(['controller' => 'Requests', 'action' => 'invitation']);
            } else {
                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
            }
        }

        if ($request->request_type == "Company_Company_Invitation") {
            $request->approved_at = FrozenTime::now();

            $companyID = $request->company_id;
            $userID = $currentUser->id;

            $company_users_table = $this->fetchTable('CompaniesUsers');

            $companyuser = $company_users_table->newEmptyEntity();

            $existingRecord = $company_users_table->find()
                ->where(['user_id' => $userID, 'company_id' => $companyID])
                ->first();
            if (!$existingRecord) {
                $companyUserdata = [
                    'company_id' => $companyID,
                    'user_id' => $userID,
                    'is_company_admin' => 1,
                    'confirmed' => 1,
                    'status' => 'Engaged'
                ];
                $company_users_table->patchEntity($companyuser, $companyUserdata);
                if ($this->Requests->save($request) && $company_users_table->save($companyuser)) {

                    $this->Flash->success(__('Your company have been engaged with this company'));
                } else {
                    $this->Flash->error(__('Your company could not join this company. Please, try again.'));
                }
            } else {
                $this->Flash->error(__('You are already associated with this company. Please, try again.'));
            }
            $referer = $this->referer();
            //debug($referer);
            if ($referer == '/requests/invitation') {
                return $this->redirect(['controller' => 'Requests', 'action' => 'invitation']);
            } else {
                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
            }
        }


    }

    public function rejectrequest($id)
    {
        $this->request->allowMethod(['post', 'delete', 'get']);
        $request = $this->Requests->get($id);
//        debug($request);
        $this->Authorization->skipauthorization();
        $request->removal_status = 2;
        if ($this->Requests->save($request)) {
            $this->Flash->success(__('The request has been rejected.'));
        } else {
            $this->Flash->error(__('The request could not be rejected. Please, try again.'));
        }
        $referer = $this->referer();
        if ($referer == '/requests/companyrequestindex') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'companyrequestindex']);
        } elseif ($referer == '/requests/invitation') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'invitation']);
        } else {
            return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
        }
    }

    public function joinproject()
    {
        $this->Authorization->skipauthorization();
        $currentUser = $this->request->getAttribute('identity');
        $passcode = null;
        $ownacompany = false;
        $this->set('passcode', $passcode);
        $companyowner = TableRegistry::getTableLocator()->get('Companies')->find();
        $companyowner = $companyowner->select(['admin_id'])->toArray();
        $adminIds = [];
        foreach ($companyowner as $company) {
            $adminIds[] = (int)$company->admin_id;
        }

        if (in_array($currentUser->id, $adminIds)) {
            $ownacompany = true;
        }
        if (!$ownacompany && $currentUser->role == 'Contractor') {
            $this->Flash->error(__('As a contractor, you need to register your company first in order to join a project.'));
            return $this->redirect(['controller' => 'companies', 'action' => 'add']);
        }
        $this->set('ownacompany', $ownacompany);

        if ($this->request->is('post')) {
            $joinType = $this->request->getData('join');

            if ($joinType === 'member') { // Handle joining as a member
                $passcode = $this->request->getData();
                $allproject = $this->fetchTable('Projects')->find();
                $existingPasscode = $allproject->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'passcode'
                ]);
                $passcode = $passcode['passcode'];
                $existingPasscode = $existingPasscode->toArray();
                if (in_array($passcode, $existingPasscode)) {
                    $request = $this->Requests->newEmptyEntity();
                    $project = TableRegistry::getTableLocator()->get('Projects')->find()->where(['passcode' => $passcode])->first();
                    $projectID = $project->id;
                    $userID = $currentUser->id;

                    $existingmember = $this->fetchTable('ProjectsUsers')->find()->where([
                        'user_id' => $userID,
                        'project_id' => $projectID,
                        'status' => 'Engaged'
                    ])
                        ->first();
                    if (!$existingmember) {
                        $existingrequest = $this->Requests->find()
                            ->where([
                                'user_id' => $userID,
                                'project_id' => $projectID,
                                'request_type' => 'Project_Member',
                                'removal_status' => 0
                            ])
                            ->first();
                        if (!$existingrequest) {
                            return $this->redirect(['controller' => 'Requests', 'action' => 'selectcompany', $projectID]);
//                            $request->user_id = $userID;
//                            $request->request_type = "Project_Member";
//                            $request->project_id = $projectID;
//                            $request->created_at = FrozenTime::now();
//                            if($currentUser->role =='On-site Worker'){
//                                $request->request_text = "Worker is requesting to join a Project ";
//                            }else{
//                                $request->request_text = "User is requesting to join a Project ";
//                            }
//                            $request->removal_status = 0;
//
//                            if ($this->Requests->save($request)) {
//                                $this->Flash->success(__('The request has been sent to the Builder for approval. You will be joined once the builder approves your request'));
//
//                                return $this->redirect(['controller' => 'projects', 'action' => 'index', $userID]);
//                            }
//                            $this->Flash->error(__('The request could not be sent. Please, try again.'));
                        } else {
                            $this->Flash->error(__('There is an on going request for this project already, please wait for approval or remove the current request and try again.'));
                        }
                    } else {
                        $this->Flash->error(__('You have joined this project already. Please refer to "My Project" for project detail.'));
                    }
                } else {
                    $this->Flash->error(__('There is no project associated with this passcode. Please, try again.'));
                }

            } elseif ($joinType === 'company') {  // Handle joining as a company
                $passcode = $this->request->getData();
                $allproject = $this->fetchTable('Projects')->find();
                $existingPasscode = $allproject->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'passcode'
                ]);
                $passcode = $passcode['passcode'];
                $existingPasscode = $existingPasscode->toArray();
                if (in_array($passcode, $existingPasscode)) {
                    $request = $this->Requests->newEmptyEntity();
                    $project = TableRegistry::getTableLocator()->get('Projects')->find()->where(['passcode' => $passcode])->first();
                    $projectID = $project->id;
                    $userID = $currentUser->id;
                    $company = TableRegistry::getTableLocator()->get('Companies')->find()->where(['admin_id' => $userID])->first();
                    $companyID = $company->id;
                    $existingmember = $this->fetchTable('CompaniesProjects')->find()->where([
                        'company_id' => $companyID,
                        'project_id' => $projectID,
                        'status' => 'Engaged'
                    ])
                        ->first();
                    if (!$existingmember) {
                        $existingrequest = $this->Requests->find()
                            ->where([
                                'user_id' => $userID,
                                'project_id' => $projectID,
                                'request_type' => 'Project_Company',
                                'removal_status' => 0
                            ])
                            ->first();
                        if (!$existingrequest) {
                            $request->user_id = $userID;
                            $request->request_type = "Project_Company";
                            $request->project_id = $projectID;
                            $request->created_at = FrozenTime::now();
                            $request->company_id = $companyID;
                            $request->request_text = "Company/Contractor is requesting to join a Project";
                            $request->removal_status = 0;

                            if ($this->Requests->save($request)) {
                                $this->Flash->success(__('The request has been sent to the Builder for approval. Your company will be joined once the builder approves your request'));
                                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
                            } else {
                                $this->Flash->error(__('The request could not be sent. Please, try again.'));
                            }
                        } else {
                            $this->Flash->error(__('There is an on going request for this project already, please wait for approval or remove the current request and try again. You can refer to "My Requests" section to view request status'));
                        }
                    } else {
                        $this->Flash->error(__('Your company have associated with this project already. Please refer to "My Project" for project detail.'));
                    }
                } else {
                    $this->Flash->error(__('There is no project associated with this passcode. Please, try again.'));
                }
            }
        }
    }

    public function inviteCompanyToCompany($companyID)
    {
        $this->Authorization->skipauthorization();
        $currentUser = $this->request->getAttribute('identity');
        $passcode = null;
        $this->set('passcode', $passcode);

        if ($this->request->is('post')) {
            $passcode = $this->request->getData();
            $allcompany = $this->fetchTable('Companies')->find();

            $existingPasscode = $allcompany->find('list', [
                'keyField' => 'id',
                'valueField' => 'passcode'
            ]);
            $passcode = $passcode['passcode'];
            $existingPasscode = $existingPasscode->toArray();

            if (in_array($passcode, $existingPasscode)) {
                $request = $this->Requests->newEmptyEntity();
                $company = TableRegistry::getTableLocator()->get('Companies')->find()->where(['passcode' => $passcode])->first();
                // $companyID = $company->id;
                $userID = $company->admin_id;

                $existingCompany = $this->fetchTable('CompaniesUsers')->find()
                    ->where([
                        'user_id' => $userID,
                        'company_id' => $companyID,
                        'is_company_admin' => 1,
                        'status' => 'Engaged'
                    ])->first();
                if ($existingCompany) {
                    $this->Flash->error(__('The company is already subcontracted to this company'));
                    return $this->redirect(['controller' => 'Requests', 'action' => 'inviteWorkerToCompany', $companyID]);
                }
                $existingInvitation = $this->Requests->find()
                    ->where([
                        'user_id' => $userID,
                        'company_id' => $companyID,
                        'request_type' => 'Company_Company_Invitation',
                        'removal_status' => 0
                    ])->first();
                if ($existingInvitation) {
                    $this->Flash->error(__('There is an on going invitation for this company already, please wait for acceptance or remove the current request and try again.'));
                    return $this->redirect(['controller' => 'Requests', 'action' => 'inviteWorkerToCompany', $companyID]);
                }


                $request->user_id = $userID;
                $request->company_id = $companyID;
                $request->request_type = "Company_Company_Invitation";
                $request->created_at = FrozenTime::now();
                $request->request_text = "A company owner is inviting your company to join their company";
                $request->removal_status = 0;

                if ($this->Requests->save($request)) {
                    $this->Flash->success(__('The invitation has been sent to the company for approval. The company will join your company once they accept your invitation'));

                    return $this->redirect(['controller' => 'Requests', 'action' => 'company-invitation']);
                }
                $this->Flash->error(__('The invitation could not be sent. Please, try again.'));
            } else {
                $this->Flash->error(__('There is no company associated with this passcode. Please, try again.'));
            }
        }

    }

    public function inviteWorkerToCompany($companyID)
    {
        $this->Authorization->skipauthorization();
        $currentUser = $this->request->getAttribute('identity');
        $email = null;
        $this->set('email', $email);
        if ($this->request->is('post')) {
            $email = $this->request->getData();
            $alluser = $this->fetchTable('Users')->find();

            $existingEmail = $alluser->find('list', [
                'keyField' => 'id',
                'valueField' => 'email'
            ]);
            $email = $email['email'];
            $existingEmail = $existingEmail->toArray();
            if (in_array($email, $existingEmail)) {
                $request = $this->Requests->newEmptyEntity();
                $user = TableRegistry::getTableLocator()->get('Users')->find()->where(['email' => $email])->first();
                $userID = $user->id;
                $existingMember = $this->fetchTable('CompaniesUsers')->find()
                    ->where([
                        'user_id' => $userID,
                        'company_id' => $companyID,
                        'is_company_admin' => 0,
                        'status' => 'Engaged'
                    ])->first();
                if ($existingMember) {
                    $this->Flash->error(__('The worker is already belong to this company'));
                    return $this->redirect(['controller' => 'Requests', 'action' => 'inviteWorkerToCompany', $companyID]);
                }
                $existingInvitation = $this->Requests->find()
                    ->where([
                        'user_id' => $userID,
                        'company_id' => $companyID,
                        'request_type' => 'Company_Member_Invitation',
                        'removal_status' => 0
                    ])->first();
                if ($existingInvitation) {
                    $this->Flash->error(__('There is an on going invitation for this worker already, please wait for acceptance or remove the current request and try again.'));
                    return $this->redirect(['controller' => 'Requests', 'action' => 'inviteWorkerToCompany', $companyID]);
                }


                $request->user_id = $userID;
                $request->company_id = $companyID;
                $request->request_type = "Company_Member_Invitation";
                $request->created_at = FrozenTime::now();
                $request->request_text = "A company owner is inviting you to join their company";
                $request->removal_status = 0;

                if ($this->Requests->save($request)) {
                    $this->Flash->success(__('The invitation has been sent to the user for approval. The user will join your company once they approves your request'));

                    return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
                }
                $this->Flash->error(__('The invitation could not be sent. Please, try again.'));
            } else {
                $this->Flash->error(__('There is no user associated with this email. Please, try again.'));
            }
        }
    }

    public function joincompany()
    {
        $this->Authorization->skipauthorization();
        $currentUser = $this->request->getAttribute('identity');
        $ownacompany = false;
        $passcode = null;
        $this->set('passcode', $passcode);

        $companyowner = TableRegistry::getTableLocator()->get('Companies')->find();
        $companyowner = $companyowner->select(['admin_id'])->toArray();
        $adminIds = [];
        foreach ($companyowner as $company) {
            $adminIds[] = (int)$company->admin_id;
        }

        if (in_array($currentUser->id, $adminIds)) {
            $ownacompany = true;
        }
        if (!$ownacompany && $currentUser->role == 'Contractor') {
            $this->Flash->error(__('As a contractor, you need to register your company first in order to be subcontracted to other company.'));
            return $this->redirect(['controller' => 'companies', 'action' => 'add']);
        }
        $this->set('ownacompany', $ownacompany);


        if ($this->request->is('post')) {
            $joinType = $this->request->getData('join');

            if ($joinType === 'member') { // Handle joining as a member
                $passcode = $this->request->getData();
                $allcompany = $this->fetchTable('Companies')->find();
                $existingPasscode = $allcompany->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'passcode'
                ]);
                $passcode = $passcode['passcode'];
                $existingPasscode = $existingPasscode->toArray();

                if (in_array($passcode, $existingPasscode)) {
                    $request = $this->Requests->newEmptyEntity();
                    $company = TableRegistry::getTableLocator()->get('Companies')->find()->where(['passcode' => $passcode])->first();
                    $companyID = $company->id;
                    $userID = $currentUser->id;

                    $existingmember = $this->fetchTable('CompaniesUsers')->find()->where([
                        'user_id' => $userID,
                        'company_id'=>$companyID,
                        'is_company_admin' => 0,
                        'status' => 'Engaged'
                    ])
                        ->first();
                    if (!$existingmember) {
                        $existingrequest = $this->Requests->find()
                            ->where([
                                'user_id' => $userID,
                                'company_id' => $companyID,
                                'request_type' => 'Company_Member',
                                'removal_status' => 0
                            ])
                            ->first();
                        if (!$existingrequest) {
                            $request->user_id = $userID;
                            $request->request_type = "Company_Member";
                            $request->company_id = $companyID;
                            $request->created_at = FrozenTime::now();

                            if ($currentUser->role == 'On-site Worker') {
                                $request->request_text = "Worker is requesting to join a company ";
                            } else {
                                $request->request_text = "User is requesting to join a company ";
                            }
                            $request->removal_status = 0;

                            if ($this->Requests->save($request)) {
                                $this->Flash->success(__('The request has been sent to the company admin for approval. You will be joined once the admin approves your request'));

                                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
                            }
                            $this->Flash->error(__('The request could not be sent. Please, try again.'));
                        } else {
                            $this->Flash->error(__('There is an on going request for this company already, please wait for approval or remove the current request and try again.'));
                        }
                    } else {
                        $this->Flash->error(__('You have joined this company already.'));
                    }
                } else {
                    $this->Flash->error(__('There is no company associated with this passcode. Please, try again.'));
                }
            } elseif ($joinType === 'company') {// Handle joining as a company
                $passcode = $this->request->getData();

                $myowncompany = $this->fetchTable('Companies')->find()
                    ->where(['passcode'=>$passcode['passcode'], 'admin_id'=>$currentUser->id])
                    ->first();

                if($myowncompany){
                    $this->Flash->error(__('You cannot join your own company. Please, try again.'));
                    return $this->redirect(['controller' => 'Requests', 'action' =>'joincompany']);
                }
                $allcompany = $this->fetchTable('Companies')->find();
                $existingPasscode = $allcompany->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'passcode'
                ]);
                $passcode = $passcode['passcode'];
                $existingPasscode = $existingPasscode->toArray();

                if (in_array($passcode, $existingPasscode)) {
                    $request = $this->Requests->newEmptyEntity();
                    $company = TableRegistry::getTableLocator()->get('Companies')->find()->where(['passcode' => $passcode])->first();
                    $companyID = $company->id;
                    $userID = $currentUser->id;

                    $existingmember = $this->fetchTable('CompaniesUsers')->find()->where([
                        'user_id' => $userID,
                        'is_company_admin' => 1,
                        'status' => 'Engaged'
                    ])->first();
                    if (!$existingmember) {
                        $existingrequest = $this->Requests->find()
                            ->where([
                                'user_id' => $userID,
                                'company_id' => $companyID,
                                'request_type' => 'Company_Company',
                                'removal_status' => 0
                            ])
                            ->first();
                        if (!$existingrequest) {
                            $request->user_id = $userID;
                            $request->request_type = "Company_Company";
                            $request->company_id = $companyID;
                            $request->created_at = FrozenTime::now();
                            $request->request_text = "A company is requesting to subcontracted to your company ";
                            $request->removal_status = 0;
                            if ($this->Requests->save($request)) {
                                $this->Flash->success(__('The request has been sent to the company admin for approval. You will be joined once the admin approves your request'));

                                return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
                            }
                            $this->Flash->error(__('The request could not be sent. Please, try again.'));
                        } else {
                            $this->Flash->error(__('There is an on going request for this project already, please wait for approval or remove the current request and try again. You can refer to "My Requests" section to view request status'));
                        }
                    } else {
                        $this->Flash->error(__('Your company have subcontracted to this company already.'));
                    }
                } else {
                    $this->Flash->error(__('There is no company associated with this passcode. Please, try again.'));
                }
            }
        }
    }


    public function invitation()
    {
        $this->Authorization->skipauthorization();
        $currentUser = $this->request->getAttribute('identity');
        $ownacompany = false;
        $companyowner = TableRegistry::getTableLocator()->get('Companies')->find();
        $companyowner = $companyowner->select(['admin_id'])->toArray();
        $adminIds = [];
        foreach ($companyowner as $company) {
            $adminIds[] = (int)$company->admin_id;
        }

        if (in_array($currentUser->id, $adminIds)) {
            $ownacompany = true;
        }
        $this->set('ownacompany', $ownacompany);


        $project_invitation = $this->fetchTable('Requests')
            ->find()
            ->select(['id' => 'Requests.id', 'first_name' => 'u1.first_name', 'last_name' => 'u1.last_name', 'projects_project_id' => 'p1.id', 'project_name' => 'p1.name', 'builder_id' => 'p1.builder_id', 'user_id' => 'Requests.user_id', 'project_id' => 'Requests.project_id', 'company_id' => 'Requests.company_id',
                'request_type' => 'Requests.request_type', 'request_text' => 'Requests.request_text', 'created_at' => 'Requests.created_at', 'approved_at' => 'Requests.approved_at', 'removal_status' => 'Requests.removal_status', 'company_id' => 'Requests.company_id', 'worker_company' => 'Requests.company_id_worker'])
            ->join([
                "table" => "projects p1",
                "type" => "LEFT",
                "conditions" => "Requests.project_id = p1.id"
            ])
            ->join([
                "table" => "users u1",
                "type" => "LEFT",
                "conditions" => "Requests.user_id = u1.id"])
            ->where([
                'u1.id' => $currentUser->id,
                'removal_status IN' => [0, 2],
            ]);

        $workerCompanyID = array_column($project_invitation->toArray(), 'worker_company');


        if(!empty($workerCompanyID)){
            $companyName = $this->fetchTable('Companies')->find()
                ->select([
                    'company_id'=>'id',
                    'company_name'=>'name'
                ])->where([
                    'id IN'=>$workerCompanyID
                ])->toArray();
            $companyMapping = [];
            foreach ($companyName as $company) {
                $companyMapping[$company['company_id']] = $company['company_name'];
            }

            foreach ($project_invitation->toArray() as &$invitation) {
                if (isset($invitation['worker_company']) && isset($companyMapping[$invitation['worker_company']])) {
                    $invitation['worker_company_name'] = $companyMapping[$invitation['worker_company']];
                }
            }
        }




        $project_invitation = $project_invitation->toArray();
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $builderIds = [];
        foreach ($project_invitation as $invitation) {
            $builderIds[] = $invitation['builder_id'];
        }

        if (!empty($builderIds)) {
            $builder_name = $usersTable->find()
                ->select(['id' => 'id', 'first_name' => 'first_name', 'last_name' => 'last_name'])
                ->where(['id IN' => $builderIds])->toArray();

            $adminNamesById = []; // Create an associative array for easier lookup

            foreach ($builder_name as $builder) {
                $adminNamesById[$builder['id']] = [
                    'first_name' => $builder['first_name'],
                    'last_name' => $builder['last_name']
                ];
            }

            foreach ($project_invitation as &$invitation) {
                $adminId = $invitation['builder_id'];

                if (isset($adminNamesById[$adminId])) {
                    $invitation['builder_first_name'] = $adminNamesById[$adminId]['first_name'];
                    $invitation['builder_last_name'] = $adminNamesById[$adminId]['last_name'];
                }
            }
            unset($invitation);
            //debug($project_invitation);
        }
        $this->set('project_invitation', $project_invitation);


        $company_invitation = $this->fetchTable('Requests')
            ->find()
            ->select(['id' => 'Requests.id', 'first_name' => 'u1.first_name', 'last_name' => 'u1.last_name', 'company_id' => 'c1.id', 'company_name' => 'c1.name', 'admin_id' => 'c1.admin_id', 'user_id' => 'Requests.user_id',
                'request_type' => 'Requests.request_type', 'request_text' => 'Requests.request_text', 'created_at' => 'Requests.created_at', 'approved_at' => 'Requests.approved_at', 'removal_status' => 'Requests.removal_status'])
            ->join([
                "table" => "companies c1",
                "type" => "LEFT",
                "conditions" => "Requests.company_id = c1.id"
            ])
            ->join([
                "table" => "users u1",
                "type" => "LEFT",
                "conditions" => "Requests.user_id = u1.id"])
            ->where([
                'u1.id' => $currentUser->id,
                'removal_status IN' => [0, 2],
            ]);


        //debug($company_invitation->toarray());

        $company_invitation = $company_invitation->toArray();
        $usersTable = TableRegistry::getTableLocator()->get('Users');

        $AdminIds = [];
        foreach ($company_invitation as $invitation) {
            $AdminIds[] = $invitation['admin_id'];
        }
        if (!empty($AdminIds)) {

            $admin_name = $usersTable->find()
                ->select(['id' => 'id', 'first_name' => 'first_name', 'last_name' => 'last_name'])
                ->where(['id IN' => $AdminIds])->toArray();
            //debug($admin_name);

            $adminNamesById = []; // Create an associative array for easier lookup

            foreach ($admin_name as $admin) {
                $adminNamesById[$admin['id']] = [
                    'first_name' => $admin['first_name'],
                    'last_name' => $admin['last_name']
                ];
            }
            foreach ($company_invitation as &$invitation) {
                $adminId = $invitation['admin_id'];

                if (isset($adminNamesById[$adminId])) {
                    $invitation['admin_first_name'] = $adminNamesById[$adminId]['first_name'];
                    $invitation['admin_last_name'] = $adminNamesById[$adminId]['last_name'];
                }
            }
            unset($invitation);
            //debug($project_invitation);
        }
        $this->set('company_invitation', $company_invitation);

        //debug($company_invitation);
    }

    public function builderProjectInvitation()
    {
        $this->Authorization->skipauthorization();
        $currentUser = $this->request->getAttribute('identity');

        $userproject = $this->fetchTable('Projects')->find()
            ->select('id')
            ->where(['builder_id' => $currentUser->id])->toArray();
        $projectIds = [];
        foreach ($userproject as $project) {
            $projectIds[] = $project['id'];
        }

        if (!empty($projectIds)) {
            $project_invitation = $this->fetchTable('Requests')
                ->find()
                ->select(['id' => 'Requests.id', 'first_name' => 'u1.first_name', 'last_name' => 'u1.last_name', 'projects_project_id' => 'p1.id', 'project_name' => 'p1.name', 'builder_id' => 'p1.builder_id', 'user_id' => 'Requests.user_id', 'project_id' => 'Requests.project_id', 'company_id' => 'Requests.company_id',
                    'request_type' => 'Requests.request_type', 'worker_company' => 'Requests.company_id_worker', 'request_text' => 'Requests.request_text', 'created_at' => 'Requests.created_at', 'approved_at' => 'Requests.approved_at', 'removal_status' => 'Requests.removal_status', 'comment' => 'Requests.comment', 'company_id' => 'Requests.company_id', 'company_name' => 'c1.name'])
                ->join([
                    "table" => "projects p1",
                    "type" => "LEFT",
                    "conditions" => "Requests.project_id = p1.id"
                ])
                ->join([
                    "table" => "users u1",
                    "type" => "LEFT",
                    "conditions" => "Requests.user_id = u1.id"])
                ->join([
                    "table" => "companies c1",
                    "type" => "LEFT",
                    "conditions" => "Requests.company_id = c1.id"])
                ->where([
                    'p1.id In' => $projectIds,
                    'removal_status IN' => [0, 2],
                ])->toArray();


            $workerCompanyId = array_column($project_invitation, 'worker_company');
            if(!empty($workerCompanyId)) {
                $workerCompany = $this->fetchTable('Companies')->find()
                    ->select([
                        'company_id' => 'id',
                        'company_name' => 'name'])
                    ->where(['id IN' => $workerCompanyId])
                    ->toArray();
                $companyMapping = [];
                foreach ($workerCompany as $company) {
                    $companyMapping[$company['company_id']] = $company['company_name'];
                }


                foreach ($project_invitation as &$invitation) {
                    if (isset($invitation['worker_company']) && isset($companyMapping[$invitation['worker_company']])) {
                        $invitation['worker_company_name'] = $companyMapping[$invitation['worker_company']];
                    }
                }
            }

            $this->set('project_invitation', $project_invitation);
        }

//debug($project_invitation->toArray());

    }

    public function companyInvitation()
    {
        $this->Authorization->skipauthorization();
        $currentUser = $this->request->getAttribute('identity');

        $usercompany = $this->fetchTable('Companies')->find()
            ->select('id')
            ->where(['admin_id' => $currentUser->id])->toArray();
        $companyIds = [];
        foreach ($usercompany as $company) {
            $companyIds[] = $company['id'];
        }

//        debug($companyIds);
        if (!empty($companyIds)) {
            $company_invitation = $this->fetchTable('Requests')
                ->find()
                ->select(['id' => 'Requests.id', 'first_name' => 'u1.first_name', 'last_name' => 'u1.last_name', 'user_id' => 'Requests.user_id', 'company_id' => 'Requests.company_id',
                    'request_type' => 'Requests.request_type', 'request_text' => 'Requests.request_text', 'created_at' => 'Requests.created_at', 'approved_at' => 'Requests.approved_at', 'comment' => 'Requests.comment', 'removal_status' => 'Requests.removal_status', 'company_id' => 'Requests.company_id', 'company_name' => 'c1.name'])
                ->join([
                    "table" => "users u1",
                    "type" => "LEFT",
                    "conditions" => "Requests.user_id = u1.id"])
                ->join([
                    "table" => "companies c1",
                    "type" => "LEFT",
                    "conditions" => "Requests.company_id = c1.id"])
                ->where([
                    'c1.id In' => $companyIds,
                    'removal_status IN' => [0, 2],
                ]);


            $company_invitation = $company_invitation->toArray();

            $adminID = [];
            $companiesTable = TableRegistry::getTableLocator()->get('Companies');
            foreach ($company_invitation as $invitation) {
                $adminID[] = $invitation['user_id'];
            }
//        debug($adminID);

            if (!empty($adminID)) {
                $company_name = $companiesTable->find()
                    ->select(['admin_id' => 'admin_id', 'company_name' => 'name', 'company_id' => 'id'])
                    ->where(['admin_id IN' => $adminID])->toArray();

//        debug($company_name);

                $CompanyNamesById = []; // Create an associative array for easier lookup

                foreach ($company_name as $company) {
                    $CompanyNamesById[$company['admin_id']] = [
                        'company_name' => $company['company_name'],
                        'invited_company_id' => $company['company_id'],
                    ];
                }
                //debug($CompanyNamesById);

                foreach ($company_invitation as &$invitation) {
                    $adminID = $invitation['user_id'];
//debug($adminID);
//debug($CompanyNamesById[$adminID]);
                    if (isset($CompanyNamesById[$adminID])) {
                        $invitation['invited_company_name'] = $CompanyNamesById[$adminID]['company_name'];
                    }
                    if (isset($CompanyNamesById[$adminID])) {
                        $invitation['invited_company_id'] = $CompanyNamesById[$adminID]['invited_company_id'];
                    }
                }
                unset($invitation);
            }
            // debug($company_invitation);
            $this->set('company_invitation', $company_invitation);
        }


    }

    public function addprojectinvitation($projectID)
    {
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $projects = $this->fetchTable('Projects');

        $request = $this->request;

        $project = $request->getQuery('project');

        $builderId = $projects->get($projectID)->builder_id;


        // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {
            // Find the user's status for the project
            $userStatus = $this->fetchTable('ProjectsUsers')->find()
                ->select(['status'])
                ->where([
                    'project_id' => $projectID,
                    'user_id' => $currentUser->id,
                ])
                ->first();

            // Check if the user's status is not "Co-Manager"
            if ($userStatus->status != 'Co-Manager') {
                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
            }
        }
        $assignedUsers = $this->fetchTable('Users')->find();
        $currentUser = $this->request->getAttribute('identity');
        $userID = null;
        $project_user_table = $this->fetchTable('ProjectsUsers');
        $assignedUsers = $assignedUsers->select(['first_name' => 'first_name', 'last_name' => 'last_name', 'email' => 'email'])
            ->innerJoin('projects_users', [
                'Users.id = projects_users.user_id',
            ])
            ->innerJoin('projects', [
                'projects.id = projects_users.project_id',
            ])
            ->where([
                'builder_id' => $currentUser->id
            ])->distinct();

        $userNames = [];
        $emailaddress = [];
        foreach ($assignedUsers as $user) {
            $fullName = $user->first_name . ' ' . $user->last_name;
            $userNames[] = $fullName;
        }
        foreach ($assignedUsers as $user) {
            $emailaddress[] = $user->email;
        }

        $this->set('userNames', $userNames);
        $this->set('userID', $userID);
        $this->set('projectID', $projectID);
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $selectedMemberName = $data['user_name'];
            //debug($selectedMemberName);
            $selectedEmail = $emailaddress[$selectedMemberName];
//            debug($selectedEmail);
            $selectedUserID = $this->fetchTable('Users')->find()
                ->select(['id'])
                ->where(['email' => $selectedEmail])
                ->first();
//            debug($selectedUserID);
            if ($selectedUserID) {
                $userID = $selectedUserID['id'];
//                debug($userID);
//                debug($projectID);
                $existingRecord = $project_user_table->find()
                    ->where(['user_id' => $userID, 'project_id' => $projectID])
                    ->first();
                if ($existingRecord) {
                    $this->Flash->error(__('This member is already associated with this project. Please, try again.'));
                    return $this->redirect(['action' => 'addprojectinvitation', $projectID]);
                }
                $existinginvitation = $this->Requests->find()
                    ->where([
                        'user_id' => $userID,
                        'project_id' => $projectID,
                        'request_type' => 'Project_Member_Invitation',
                        'removal_status' => 0])->first();
                if ($existinginvitation) {
                    $this->Flash->error(__('There is an on going invitation already. Please wait for acceptance or remove the current invitation and try again.'));
                    return $this->redirect(['action' => 'addprojectinvitation', $projectID]);
                }


                return $this->redirect(['controller' => 'Requests', 'action' => 'companyforproject',$projectID, $userID]);

            }
        }
    }

    public function inviteProjectCompany($projectID)
    {
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $projects = $this->fetchTable('Projects');

        $request = $this->request;

        $project = $request->getQuery('project');

        $builderId = $projects->get($projectID)->builder_id;


        // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {
            // Find the user's status for the project
            $userStatus = $this->fetchTable('ProjectsUsers')->find()
                ->select(['status'])
                ->where([
                    'project_id' => $projectID,
                    'user_id' => $currentUser->id,
                ])
                ->first();

            // Check if the user's status is not "Co-Manager"
            if ($userStatus->status != 'Co-Manager') {
                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
            }
        }
        $companies_project_table = $this->fetchTable('CompaniesProjects');
        $CompaniesTable = $this->fetchTable('Companies')->find();
        $currentUser = $this->request->getAttribute('identity');
        $companyID = null;
        $project_user_table = $this->fetchTable('ProjectsUsers');
        $CompaniesTable = $CompaniesTable->select(['name' => 'Companies.name'])
            ->innerJoin('companies_projects', [
                'Companies.id = companies_projects.company_id',
            ])
            ->innerJoin('projects', [
                'projects.id = companies_projects.project_id',
            ])
            ->where([
                'builder_id' => $currentUser->id
            ])->distinct();

        $companyNames = [];

        foreach ($CompaniesTable as $company) {
            $companyNames[] = $company->name;
        }

        $this->set('companyNames', $companyNames);
        $this->set('companyID', $companyID);
        $this->set('projectID', $projectID);

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $selectedCompanyName = $data['company_name'];
            $selectedName = $companyNames[$selectedCompanyName];
            $selectedCompanyID = $this->fetchTable('Companies')->find()
                ->select(['id', 'admin_id'])
                ->where(['name' => $selectedName])
                ->first();

            if ($selectedCompanyID) {
                $companyID = $selectedCompanyID['id'];
                $userID = $selectedCompanyID['admin_id'];
                $existingRecord = $companies_project_table->find()
                    ->where(['company_id' => $companyID, 'project_id' => $projectID])
                    ->first();

                if ($existingRecord) {
                    $this->Flash->error(__('This company is already associated with this project. Please, try again.'));
                    return $this->redirect(['action' => 'inviteProjectCompany', $projectID]);
                }
                $existinginvitation = $this->Requests->find()
                    ->where([
                        'user_id' => $userID,
                        'project_id' => $projectID,
                        'request_type' => 'Project_Company_Invitation',
                        'removal_status' => 0])->first();


                if ($existinginvitation) {
                    $this->Flash->error(__('There is an on going invitation already. Please wait for acceptance or remove the current invitation and try again.'));
                    return $this->redirect(['action' => 'inviteProjectCompany', $projectID]);
                }
                $projectInvitation = $this->Requests->newEmptyEntity();
                $projectInvitation->user_id = $userID;
                $projectInvitation->company_id = $companyID;
                $projectInvitation->project_id = $projectID;
                $projectInvitation->request_type = "Project_Company_Invitation";
                $projectInvitation->created_at = FrozenTime::now();
                $projectInvitation->request_text = "A builder is inviting your company to join a project";
                $projectInvitation->removal_status = 0;
                if ($this->Requests->save($projectInvitation)) {
                    $this->Flash->success(__('The invitation has been sent to the company. The company will be joined once they accept your invitation'));
                    return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
                } else {
                    $this->Flash->error(__('The invitation could not be sent. Please, try again.'));
                }
            }
        }
    }

    public function reason($requestID)
    {
        $this->Authorization->skipAuthorization();
        $reason = null;
        $this->set('reason', $reason);
        $reason = $this->request->getData('Comment');

        $request = $this->Requests->get($requestID, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $request->comment = $reason;
            $request->removal_status = 2;
            if ($this->Requests->save($request)) {
                $this->Flash->success(__('The request has been rejected.'));
                $referer = $this->referer();
                //debug($referer);
                if ($referer == '/requests/companyrequestindex') {
                    return $this->redirect(['controller' => 'Requests', 'action' => 'companyrequestindex']);
                } elseif ($referer == '/requests/invitation') {
                    return $this->redirect(['controller' => 'Requests', 'action' => 'invitation']);
                } elseif ('/requests') {
                    return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
                }
            } else {
                $this->Flash->error(__('The request could not be rejected. Please, try again.'));
                $referer = $this->referer();
                if ($referer == '/requests/companyrequestindex') {
                    return $this->redirect(['controller' => 'Requests', 'action' => 'companyrequestindex']);
                } elseif ($referer == '/requests/invitation') {
                    return $this->redirect(['controller' => 'Requests', 'action' => 'invitation']);
                } elseif ('/requests') {
                    return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
                }
            }

        }
    }

    public function viewreason($requestID)
    {
        $this->Authorization->skipAuthorization();
        $reason = $this->Requests->get($requestID);
        $reason = $reason->get('comment');
        $this->set('reason', $reason);
        $referer = $this->referer();
        if ($referer == '/requests') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'index']);
        } elseif ($referer == '/requests/company-invitation') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'companyInvitation']);
        } elseif ($referer == '/requests/builder-project-invitation') {
            return $this->redirect(['controller' => 'Requests', 'action' => 'builderProjectInvitation']);
        }
    }

    public function selectcompany($project_id)
    {
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $companyID = null;
        $this->set('companyID', $companyID);
        $myCompanyID = $this->fetchTable('CompaniesUsers')->find()
            ->select('company_id')
            ->where(['user_id' => $currentUser->id,
                'is_company_admin' => 0,
                'status' => 'Engaged'])
            ->distinct()->toArray();


        if (!empty($myCompanyID)) {
            $companyIDs = [];
            foreach ($myCompanyID as $entity) {
                $companyIDs[] = $entity->company_id;
            }

            $myCompanyNames = $this->fetchTable('Companies')->find()
                ->select('name')
                ->where(['id IN' => $companyIDs])
                ->toArray();

            $companyNames = [];
            foreach ($myCompanyNames as $company) {
                $companyNames[] = $company->name;
            }
            $this->set('companyNames', $companyNames);
            if ($this->request->is('post')) {
                $data = $this->request->getData();

                $selectedCompanyID = $data['company_name'];
                $selectedCompanyID = $companyIDs[$selectedCompanyID];


                $projectAssociatedCompany = $this->fetchTable('CompaniesProjects')->find()->where(['company_id' => $selectedCompanyID, 'project_id' => $project_id])->first();

                if (empty($projectAssociatedCompany)) {
                    $this->Flash->error(__('This company is not associated with the project. Please contact the company admin or join as another company employee.'));
                } else {
                    $request = $this->Requests->newEmptyEntity();
                    $request->user_id = $currentUser->id;
                    $request->request_type = "Project_Member";
                    $request->project_id = $project_id;
                    $request->created_at = FrozenTime::now();
                    $request->company_id_worker = $selectedCompanyID;
                    if ($currentUser->role == 'On-site Worker') {
                        $request->request_text = "Worker is requesting to join a Project ";
                    } else {
                        $request->request_text = "User is requesting to join a Project ";
                    }
                    $request->removal_status = 0;

                    if ($this->Requests->save($request)) {
                        $this->Flash->success(__('The request has been sent to the Builder for approval. You will be joined once the builder approves your request'));
                        return $this->redirect(['controller' => 'projects', 'action' => 'index', $currentUser->id]);
                    } else {
                        $this->Flash->error(__('The request could not be sent. Please, try again.'));
                    }
                }
            }
        } else {
            $this->Flash->error(__('You need to be an employee of at least one company first.'));
            return $this->redirect(['controller' => 'Requests', 'action' => 'joincompany']);
        }
    }
    public function companyforproject($project_id,$user_id )
    {
        $this->Authorization->skipAuthorization();


        $currentUser = $this->request->getAttribute('identity');
        $companyID = null;
        $this->set('companyID', $companyID);

        $assignedCompanies = $this->fetchTable('CompaniesProjects')->find()
            ->select([
                'project_id' => 'project_id',
                'company_id' => 'CompaniesProjects.company_id',
                'company_name' => 'companies.name'
            ])
            ->join([
                "table" => "companies",
                "type" => "LEFT",
                "conditions" => "CompaniesProjects.company_id = companies.id"])
            ->where([
                'project_id' => $project_id,

            ])
            ->distinct()
        ->toArray();

        $projectcompany = array_column($assignedCompanies,'company_id');


        if (!empty($projectcompany)) {
            $userJoinedCompanies = $this->fetchTable('CompaniesUsers')->find()
                ->select(['company_id' => 'CompaniesUsers.company_id',
                    'company_name' => 'companies.name'])
                ->join([
                    "table" => "companies",
                    "type" => "LEFT",
                    "conditions" => "CompaniesUsers.company_id = companies.id"])
                ->where([
                    'user_id' => $user_id,
                    'company_id IN' => $projectcompany
                ])->distinct()
                ->toArray();


            if(!empty($userJoinedCompanies)) {
                $companyNames = [];
                foreach ($userJoinedCompanies as $company) {
                    $companyNames[] = $company->company_name;
                }
                $this->set('companyNames', $companyNames);

                if ($this->request->is('post')) {
                    $data = $this->request->getData();

                    $data = $data['company_name'];

                    $selectedCompanyID = $assignedCompanies[$data]['company_id'];
                    $selectedCompanyName = $companyNames[$data];

                    $projectInvitation = $this->Requests->newEmptyEntity();

                    $projectInvitation->user_id = $user_id;
                    $projectInvitation->project_id = $project_id;
                    $projectInvitation->request_type = "Project_Member_Invitation";
                    $projectInvitation->created_at = FrozenTime::now();
                    $projectInvitation->request_text = "A builder is inviting you to join a project";
                    $projectInvitation->removal_status = 0;
                    $projectInvitation->company_id_worker = $selectedCompanyID;
                    if ($this->Requests->save($projectInvitation)) {
                        $this->Flash->success(__('The invitation has been sent to the member. The member will be joined once they accept your invitation'));

                        return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
                    } else {
                        $this->Flash->error(__('The invitation could not be sent. Please,try again.'));
                    }
                    }
                } else {
                    $this->Flash->error(__('This user does not have any connection with the project associated company.'));
                }
        }else{
            $this->Flash->error(__('You need to have at least one company associated with this project before you invite worker.'));
            return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
        }
    }
}

