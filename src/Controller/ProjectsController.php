<?php

declare(strict_types=1);

namespace App\Controller;

use App\Test\Fixture\InductionsFixture;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\FactoryLocator;
use Cake\I18n\Time;
use Cake\I18n\FrozenTime;
use Cake\http\Client;
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;



/**
 * Projects Controller
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 * @property \App\Model\Table\CompaniesTable $Companies
 * @method \App\Model\Entity\Project[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProjectsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $project = $this->Projects->newEmptyEntity();
        $this->Authorization->authorize($project);
        $currentUser = $this->request->getAttribute('identity');
        $assignedProjects = 0;

        //        $company = $this->Companies->newEmptyEntity();
        //        $this->Authorization->authorize($company);


        //        $companies = $this->paginate($this->Companies);

        //        $this->set(compact('companies'));

        $projectsUsersTable = $this->getTableLocator()->get('ProjectsUsers');
        $projectUserStatus = $projectsUsersTable->find()
            ->select(['status', 'project_id'])
            ->where(['user_id' => $currentUser->id])
            ->toArray();


        $this->set(compact('projectUserStatus'));

        if ($currentUser->role == 'Admin') {
            $assignedProjects = $this->Projects->find();
        } elseif ($currentUser->role == 'Builder') {
            $assignedProjects = $this->Projects->find()->where(['builder_id' => $currentUser->id]);
        }elseif ($currentUser->role == 'Contractor') {
            $userCompany = $this->fetchTable('Companies')->find()
                ->select(['company_id'=>'id'])
                ->where(['admin_id'=> $currentUser->id])
                ->first();
            if($userCompany == null){
                $this->Flash->error(__('You need to add your company before you check the project detail.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'add']);
            }

            $userCompanyProject = $this->fetchTable('CompaniesProjects')->find()
                ->select('project_id')
                ->where(['company_id'=>$userCompany->company_id, 'status'=>'Engaged']);


            if (!empty($userCompanyProject)) {
                $assignedProjects = $this->Projects->find()
                    ->select([
                        'id' => 'Projects.id', 'name' => 'Projects.name', 'project_type' => 'Projects.project_type', 'builder_id' => 'Projects.builder_id',
                        'address_no' => 'Projects.address_no', 'address_street' => 'Projects.address_street', 'address_suburb' => 'Projects.address_suburb',
                        'address_postcode' => 'Projects.address_postcode', 'address_state' => 'Projects.address_state', 'start_date' => 'Projects.start_date',
                        'status' => 'Projects.status', 'project_id' => 'Projects.id', 'builder_fname' => 'u1.first_name', 'builder_lname' => 'u1.last_name', 'user_status' => 'u1.status', 'user_id' => 'u1.id'
                    ])
                    ->join([
                        "table" => "users u1",
                        "type" => "LEFT",
                        "conditions" => "Projects.builder_id = u1.id"])
                    ->where(['Projects.id IN' => $userCompanyProject]);

            }else{
                $assignedProjects = null;
            }




        } elseif ($currentUser->role != 'Admin' && $currentUser->role != 'Builder') {
            $project_user = $this->fetchTable('ProjectsUsers')->find()->select('project_id')->where([
                'user_id' => $currentUser->id,
            ]);


            $assignedProjects = $this->Projects->find()->select([
                'id' => 'Projects.id', 'name' => 'Projects.name', 'project_type' => 'Projects.project_type', 'builder_id' => 'Projects.builder_id',
                'address_no' => 'Projects.address_no', 'address_street' => 'Projects.address_street', 'address_suburb' => 'Projects.address_suburb',
                'address_postcode' => 'Projects.address_postcode', 'address_state' => 'Projects.address_state', 'start_date' => 'Projects.start_date',
                'status' => 'Projects.status', 'project_id' => 'Projects.id', 'builder_fname' => 'u1.first_name', 'builder_lname' => 'u1.last_name', 'user_status' => 'u1.status', 'user_id' => 'u1.id', 'inducted_date' =>'projects_users.inducted_date','usercompany' => 'projects_users.company_id'
            ])
                ->join([
                    "table" => "users u1",
                    "type" => "LEFT",
                    "conditions" => "builder_id = u1.id"])
                ->join([
                    "table" => "projects_users",
                    "type" => "LEFT",
                    "conditions" => "projects_users.project_id = Projects.id"])
                ->where([
                    'Projects.id IN' => $project_user,
                    'projects_users.user_id' => $currentUser->id
                ]);
            /*$project_data = array_column($project_user->toArray(),'project_id');
            if(!empty($project_data)) {
                $induction = $this->fetchTable('inductions')
                    ->find()
                    ->where(['project_id IN' => $project_data,
                        'user_id' => $currentUser->id]);

                $inductionMapping = [];
                foreach ($induction as $inducted) {
                    $inductionMapping[$inducted['project_id']] = $inducted['inducted_date'];
                }


                foreach ($assignedProjects->toArray() as &$inductedworker) {


                    if (isset($inductedworker->id) && isset($inductionMapping[$inductedworker['project_id']])) {
                        $inductedworker['inducted_date'] =  $inductionMapping[$inductedworker['project_id']];

                    }
                */
        }




        //LEGACY CODE PRIOR 4/04/2023
        //        elseif ($currentUser->role == 'Contractor'){
        //            $company = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->select('company_id')->where([
        //                'user_id' => $currentUser->id,
        //                'is_company_admin' => 1
        //            ])->first();
        //            if (!$company){
        //                return $this->redirect(['controller' => 'companies', 'action' => 'add']);
        //            }
        //            $company_id = $company->company_id;
        //
        //            $assignedProjectsIds = FactoryLocator::get('Table')->get('CompaniesProjects')->find()->select('project_id')->where([
        //                'company_id' => $company_id
        //            ]);
        //            $assignedProjects = $this->Projects->find();
        //            $assignedProjects->select(['id' => 'Projects.id', 'name' => 'Projects.name', 'project_type' => 'Projects.project_type', 'builder_id' => 'Projects.builder_id',
        //                'address_no' => 'Projects.address_no', 'address_street' => 'Projects.address_street', 'address_suburb' => 'Projects.address_suburb',
        //                'address_postcode' => 'Projects.address_postcode', 'address_state' => 'Projects.address_state', 'start_date' => 'Projects.start_date',
        //                'status' => 'Projects.status', 'project_id' => 'Projects.id', 'builder_fname' => 'u2.first_name', 'builder_lname' => 'u2.last_name', 'user_status' => 'u2.status'])
        //                ->join([
        //                "table" => "users u2",
        //                "type" => "LEFT",
        //                "conditions" => "builder_id = u2.id"
        //                ])->where([
        //                'Projects.id IN' => $assignedProjectsIds
        //            ]);
        //        } else {
        //            $assignedProjects = FactoryLocator::get('Table')->get('Inductions')->find();
        //            $assignedProjects->select(['inducted_date' => 'Inductions.inducted_date', 'name' => 'projects.name', 'project_type' => 'projects.project_type', 'builder_id' => 'projects.builder_id',
        //                'address_no' => 'projects.address_no', 'address_street' => 'projects.address_street', 'address_suburb' => 'projects.address_suburb',
        //                'address_postcode' => 'projects.address_postcode', 'address_state' => 'projects.address_state', 'start_date' => 'projects.start_date',
        //                'status' => 'projects.status', 'project_id' => 'projects.id', 'builder_fname' => 'u2.first_name', 'builder_lname' => 'u2.last_name', 'user_status' => 'u2.status'])->join([
        //                "table" => "projects",
        //                "type" => "LEFT",
        //                "conditions" => "Inductions.project_id = projects.id"])->join([
        //                "table" => "users u2",
        //                "type" => "LEFT",
        //                "conditions" => "builder_id = u2.id"
        //            ])->where(['user_id' => $currentUser->id])->enableAutoFields();
        //        }
        //        $selected = 0;
        //        if($this->request->is('get') && $this->request->getQuery('status')){
        //            $status = $this->request->getQuery('status');
        //            if ($status != 'All'){
        //                if ($currentUser->role == 'Builder'){
        //                    $assignedProjects->where(['Projects.status' => $status]);
        //                } else {
        //                    $assignedProjects->where(['projects.status' => $status]);
        //                }
        //                foreach($assignedProjects as $assignedProject){
        //                    $assignedProject->status = $status;
        //                }
        //            }
        //            $selected = $status;
        //        }

        //Status

        $key = $this->request->getQuery('key');

        $projectTable = $this->fetchTable('Projects');
        $companyTable = $this->fetchTable('Companies');

        if ($key) {
            $projects = $assignedProjects->where(['Projects.status' => $key]);
        } else {
            $projects = $assignedProjects;
        }

        $this->set('projects', $projects);
        //        $this->set('companies', $companies);

        $Squery = $projectTable->find();
        $statuses = $Squery->select(['status' => 'projects.status'])->from('projects')->distinct();

        $Sarray = array();

        foreach ($statuses as $statuss) :
            $Sarray[$statuss->status] = $statuss->status;
        endforeach;

        $this->set(compact('Sarray'));
        //ENDSTATUS

        $this->paginate = [
            'contain' => ['Users'],
        ];
       $projects = $this->paginate($assignedProjects);
       $this->set(compact('projects', 'Sarray', 'project'));
    }

    /**
     * Active Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function activeindex()
    {
        $project = $this->Projects->newEmptyEntity();
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $assignedProjects = 0;


        if ($currentUser->role == 'Admin') {
            $assignedProjects = $this->Projects->find()->where(['Projects.status' => 'Active']);
        } else {
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['controller' => 'Projects', 'action' => 'index']); // Redirect to an appropriate action
        }

        $key = $this->request->getQuery('key');
        if ($key) {
            $projects = $assignedProjects->where(['Projects.status' => $key]);
        } else {
            $projects = $assignedProjects;
        }

        $this->set('projects', $projects);

        $projectTable = $this->fetchTable('Projects');
        $statuses = $projectTable->find()
            ->select(['status' => 'projects.status'])
            ->distinct()
            ->where(['projects.status !=' => '']) // Specify the table name
            ->order(['projects.status' => 'ASC']); // Specify the table name

        $statusArray = [];
        foreach ($statuses as $status) {
            $statusArray[$status->status] = $status->status;
        }
        $this->set(compact('statusArray'));

        $this->paginate = [
            'contain' => ['Users'],
        ];
        $this->set('projects', $this->paginate($projects));
    }


    /**
     * AllProjects method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function allprojects()
    {
        $project = $this->Projects->newEmptyEntity();
        $this->Authorization->authorize($project);

        $currentUser = $this->request->getAttribute('identity');

        $allProjects = $this->Projects->find();

        $requestedProjects = $allProjects->select(['id' => 'Projects.id'])
            ->join([
                "table" => "requests",
                "type" => "LEFT",
                "conditions" => "Projects.id = requests.project_id"
            ])->where(['requests.user_id' => $currentUser->id, 'requests.approved_at IS NULL']);

        $allProjects = $this->Projects->find();

        $yesRequestedProjects = $allProjects->where(['id IN' => $requestedProjects]);

        $allProjects = $this->Projects->find();

        $joinedProjectsSelect = $allProjects->select([
            'id' => 'Projects.id'
        ])
            ->join([
                "table" => "projects_users",
                "type" => "LEFT",
                "conditions" => "Projects.id = projects_users.project_id"
            ])->where(['projects_users.user_id' => $currentUser->id]);

        $allProjects = $this->Projects->find();

        $joinedProjects = $allProjects->where(['id IN' => $joinedProjectsSelect]);

        $allProjects = $this->Projects->find();

        $unRequestedProjects = $allProjects->where(['NOT' => ['id IN' => $requestedProjects]]);
        $unRequestedProjects = $unRequestedProjects->where(['NOT' => ['id IN' => $joinedProjectsSelect]]);

        $projects = $this->paginate($yesRequestedProjects);


        $this->set(compact('projects', 'unRequestedProjects', 'joinedProjects'));


        // LEGACY CODE 04/04/2023
        //        if ($currentUser->role == 'Admin') {
        //            $assignedProjects = $this->Projects->find();
        //        } elseif($currentUser->role == 'Builder'){
        //            $company = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->select('company_id')->where([
        //                'user_id' => $currentUser->id,
        //                'is_company_admin' => 1
        //            ])->first();
        //            if (!$company){
        //                return $this->redirect(['controller' => 'companies', 'action' => 'add']);
        //            }
        //            $assignedProjects = $this->Projects->find()->where(['builder_id' => $currentUser->id]);
        //        } elseif ($currentUser->role == 'Contractor'){
        //            $company = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->select('company_id')->where([
        //                'user_id' => $currentUser->id,
        //                'is_company_admin' => 1
        //            ])->first();
        //            if (!$company){
        //                return $this->redirect(['controller' => 'companies', 'action' => 'add']);
        //            }
        //            $company_id = $company->company_id;
        //
        //            $assignedProjectsIds = FactoryLocator::get('Table')->get('CompaniesProjects')->find()->select('project_id')->where([
        //                'company_id' => $company_id
        //            ]);
        //            $assignedProjects = $this->Projects->find();
        //            $assignedProjects->select(['id' => 'Projects.id', 'name' => 'Projects.name', 'project_type' => 'Projects.project_type', 'builder_id' => 'Projects.builder_id',
        //                'address_no' => 'Projects.address_no', 'address_street' => 'Projects.address_street', 'address_suburb' => 'Projects.address_suburb',
        //                'address_postcode' => 'Projects.address_postcode', 'address_state' => 'Projects.address_state', 'start_date' => 'Projects.start_date',
        //                'status' => 'Projects.status', 'project_id' => 'Projects.id', 'builder_fname' => 'u2.first_name', 'builder_lname' => 'u2.last_name', 'user_status' => 'u2.status'])
        //                ->join([
        //                    "table" => "users u2",
        //                    "type" => "LEFT",
        //                    "conditions" => "builder_id = u2.id"
        //                ])->where([
        //                    'Projects.id IN' => $assignedProjectsIds
        //                ]);
        //        } else {
        //            $assignedProjects = FactoryLocator::get('Table')->get('Inductions')->find();
        //            $assignedProjects->select(['inducted_date' => 'Inductions.inducted_date', 'name' => 'projects.name', 'project_type' => 'projects.project_type', 'builder_id' => 'projects.builder_id',
        //                'address_no' => 'projects.address_no', 'address_street' => 'projects.address_street', 'address_suburb' => 'projects.address_suburb',
        //                'address_postcode' => 'projects.address_postcode', 'address_state' => 'projects.address_state', 'start_date' => 'projects.start_date',
        //                'status' => 'projects.status', 'project_id' => 'projects.id', 'builder_fname' => 'u2.first_name', 'builder_lname' => 'u2.last_name', 'user_status' => 'u2.status'])->join([
        //                "table" => "projects",
        //                "type" => "LEFT",
        //                "conditions" => "Inductions.project_id = projects.id"])->join([
        //                "table" => "users u2",
        //                "type" => "LEFT",
        //                "conditions" => "builder_id = u2.id"
        //            ])->where(['user_id' => $currentUser->id])->enableAutoFields();
        //        }
        //        $selected = 0;
        //        if($this->request->is('get') && $this->request->getQuery('status')){
        //            $status = $this->request->getQuery('status');
        //            if ($status != 'All'){
        //                if ($currentUser->role == 'Builder'){
        //                    $assignedProjects->where(['Projects.status' => $status]);
        //                } else {
        //                    $assignedProjects->where(['projects.status' => $status]);
        //                }
        //                foreach($assignedProjects as $assignedProject){
        //                    $assignedProject->status = $status;
        //                }
        //            }
        //            $selected = $status;
        //        }

        //        //Status
        //
        //        $key = $this->request->getQuery('key');
        //        $projectTable = $this->fetchTable('projects');
        //
        //        if ($key) {
        //            $projects = $assignedProjects->where(['Projects.status' => $key]);
        //        } else {
        //            $projects = $assignedProjects;
        //        }
        //
        //        $this->set('projects', $projects);
        //
        //        $Squery = $projectTable->find();
        //        $statuses = $Squery->select(['status'])->from('projects')->distinct();
        //
        //        $Sarray = array();
        //
        //        foreach ($statuses as $statuss):
        //            $Sarray[$statuss->status] = $statuss->status;
        //        endforeach;
        //
        //        $this->set(compact('Sarray'));
        //        //ENDSTATUS
        //
        //        $this->paginate = [
        //            'contain' => ['Users'],
        //        ];
        //        $projects = $this->paginate($assignedProjects);


    }

    /**
     * View method
     *
     * @param string|null $id Project id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $project = $this->Projects->get($id, ['contain' => ['Users', 'Companies', 'Checkins', 'Inductions']]);

        $currentUser = $this->request->getAttribute('identity');
        $this->Authorization->skipAuthorization();
        $builderId = $this->Projects->get($id)->builder_id;

        // Retrieve the user's status for the specified project
        $userStatus = $this->fetchTable('ProjectsUsers')->find()
            ->select(['status'])
            ->where([
                'project_id' => $id,
                'user_id' => $currentUser->id,
            ])
            ->first();

        // Check if the user's status is "disengaged"
        if ($userStatus && $userStatus->status === 'Disengaged') {
            $this->Flash->error(__('You are disengaged from this project.'));
            return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
        }

        // Check permission based on roles and associations
        $associatedUserIds = $this->fetchTable('ProjectsUsers')->find()
            ->where(['project_id' => $id])
            ->extract('user_id')
            ->toArray();
        $referer = $this->referer();

        $associatedAdminIds = $this->fetchTable('CompaniesProjects')->find()
            ->select(['admin_id'=>'companies.admin_id'])
            ->join([
                "table" => "companies",
                "type" => "LEFT",
                "conditions" => "CompaniesProjects.company_id = companies.id"])
            ->where([
                'project_id' => $id
            ])->extract('admin_id')
            ->toArray();



        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId && !in_array($currentUser->id, $associatedUserIds) && !in_array($currentUser->id, $associatedAdminIds)&& $referer != '/requests/invitation') {
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
        }

        $_documents = FactoryLocator::get('Table')->get('Documents')->find()->where(['related_project_id' => $project->id])->all();
        $_equipment = FactoryLocator::get('Table')->get('Equipment')->find()->where('related_project_id =' . $project->id)->all();
        $documents = array();
        $equipments = array();

        $myequipments = $this->fetchTable('Equipment')->find('all', [
            'conditions' => [
                'related_user_id' => $currentUser->id,
                'related_project_id' => $id
            ]
        ])->toArray();

        $currentUser = $this->request->getAttribute('identity');
        if ($currentUser->role === 'Admin') {
            $documents = $_documents;
            $equipments = $_equipment;
        } else {
            foreach ($_documents as $document) {
                $auth_type = $document->auth_type; // 1 2 3
//                if ($auth_type == 1) {
//                    if ($currentUser->role === 'Builder') {
//                        array_push($documents, $document);
//                    };
//                } else if ($auth_type == 2) {
//                    $auth_value = explode(';', $document->auth_value);
//                    if (in_array($currentUser->email, $auth_value)) {
//                        array_push($documents, $document);
//                    }
//                } else if ($auth_type == 3) {
//                    $auth_value = explode(',', $document->auth_value);
//                    if (in_array($currentUser->role, $auth_value)) {
//                        array_push($documents, $document);
//                    }
//                }
                if ($auth_type == 5){
                    $documents[] = $document;
                }
            }
            foreach ($_equipment as $equi) {
                $auth_type = $equi->auth_type; // 1 2 3
                if ($auth_type == 1) {
                    if ($currentUser->role === 'Builder') {
                        array_push($equipments, $equi);
                    };
                } else if ($auth_type == 2) {
                    $auth_value = explode(';', $equi->auth_value);
                    if (in_array($currentUser->email, $auth_value)) {
                        array_push($equipments, $equi);
                    }
                } else if ($auth_type == 3) {
                    $auth_value = explode(',', $equi->auth_value);
                    if (in_array($currentUser->role, $auth_value)) {
                        array_push($equipments, $equi);
                    }
                }
            }
        }

        // Find the user's status for the project with the given ID
        $userStatus = $this->fetchTable('ProjectsUsers')->find()
            ->select(['status'])
            ->where([
                'project_id' => $id,
                'user_id' => $currentUser->id,
            ])
            ->first();


        // Pass the userStatus variable to the view
        if ($userStatus) {
            $this->set('status', $userStatus->status);
        } else {
            $this->set('status', null);
        }
        $checkins = FactoryLocator::get('Table')->get('Checkins')->find();
        $checkins->select(['fname' => 'users.first_name', 'lname' => 'users.last_name', 'role' => 'users.role', 'checkin_datetime', 'checkout_datetime'])->join([
            "table" => "users",
            "type" => "LEFT",
            "conditions" => "Checkins.user_id = users.id"
        ])->where(['Checkins.project_id' => $project->id])->enableAutoFields();

        $workers = FactoryLocator::get('Table')->get('Inductions')->find();
        $workers->select(['id', 'user_id', 'fname' => 'users.first_name', 'lname' => 'users.last_name', 'role' => 'users.role', 'inducted_date'])->join([
            "table" => "users",
            "type" => "LEFT",
            "conditions" => "Inductions.user_id = users.id"
        ])->enableAutoFields();

        $maxHours = 8;
        $currentDateTime = FrozenTime::now();
        $currentDateTime->i18nFormat('y-MM-dd H:i:s');

        $builder = FactoryLocator::get('Table')->get('Users')->get($project->builder_id);

        $allCompanies = $this->fetchTable('Companies')->find();

        $associatedCompaniesSelect = $this->fetchTable('ProjectsUsers')->find()
            ->select([
                'id' => 'companies_users.company_id'
            ])
            ->join([
                "table" => "companies_users",
                "type" => "LEFT",
                "conditions" => "ProjectsUsers.user_id = companies_users.user_id"
            ])->where(['ProjectsUsers.project_id' => $id]);


        $associatedCompanies = $allCompanies->where(['id IN' => $associatedCompaniesSelect]);

        $this->set(compact('project', 'documents', 'checkins', 'workers', 'currentDateTime', 'maxHours', 'builder', 'equipments', 'associatedCompanies'));

        //AGREEMENT STATUS
        $this->Documents = $this->getTableLocator()->get('Documents');
        $this->ProjectsDocuments = $this->getTableLocator()->get('ProjectsDocuments');
        $documents = $this->Documents->find()->all();

        $agreementStatus = [];
        foreach ($documents as $document) {
            $agreementRecord = $this->ProjectsDocuments->find()
                ->where(['user_id' => $currentUser->id, 'document_id' => $document->id])
                ->select(['status'])
                ->first();

            if ($agreementRecord) {
                $agreementStatus[$document->id] = 'Reviewed';
            } else {
                $agreementStatus[$document->id] = 'Pending';
            }
        }
        $this->set(compact('agreementStatus', 'myequipments'));

        //INDUCTION REGISTER
        $projectsUsersTable = TableRegistry::getTableLocator()->get('ProjectsUsers');
        $companiesUsersTable = TableRegistry::getTableLocator()->get('CompaniesUsers');
        $companiesTable = TableRegistry::getTableLocator()->get('Companies');
        $usersTable = TableRegistry::getTableLocator()->get('Users');

        // Perform the initial query - Fetch related workers for a project
        $query = $projectsUsersTable
            ->find()
            ->select([
                'first_name' => 'Users.first_name',
                'last_name' => 'Users.last_name',
                'company_name' => 'companies.name',
                'company_id' => 'companies.id',
                'document_id' => 'ProjectsDocuments.id',
                'document_status' => 'ProjectsDocuments.status',
                'user_id' => 'Users.id',
                'inducted_date' => 'ProjectsUsers.inducted_date',
            ])
            ->innerJoinWith('Users')
           ->join([
               "table" => "companies",
               "type" => "LEFT",
               "conditions" => "companies.id = ProjectsUsers.company_id"
           ])
            ->leftJoinWith('ProjectsDocuments')
            ->where(['ProjectsUsers.project_id' => $id]);


        $projectsUsers = $query->toArray();

        // Get the user IDs from $projectsUsers
        $userIds = array_column($projectsUsers, 'user_id');

        if (!empty($userIds)) {
            // Fetch Document Status for Each User
            $documentStatuses = [];
            foreach ($userIds as $userId) {
                $documentStatus = $this->fetchTable('ProjectsDocuments')->find()
                    ->select(['document_id', 'status'])
                    ->where([
                        'project_id' => $id,
                        'user_id' => $userId
                    ])
                    ->toArray();

                $documentStatuses[$userId] = $documentStatus;
            }

            $this->set(compact('projectsUsers', 'documentStatuses'));
        } else {
            // If no users are found, set an empty array for document statuses
            $documentStatuses = [];
            $this->set(compact('projectsUsers', 'documentStatuses'));
        }
        $this->set('projectsUsers', $projectsUsers);


        // // Check if the initial query returned any results
        // if ($query->isEmpty()) {
        //     // If the initial query is empty, create a new query to select from Companies
        //     $companyQuery = $this->Companies->find()
        //         ->select([
        //             'company_name' => 'Companies.name',
        //             'company_id' => 'Companies.id',
        //         ])
        //         ->where(['admin_id' => $id]);

        //     // Execute the new query to select from Companies
        //     $result = $companyQuery->first(); // You can use first() if you expect only one result

        //     // Now you can check if the result from the Companies table is not null
        //     if ($result) {
        //         // Handle the case when a company with admin_id = $id is found in Companies table
        //         $companyName = $result->company_name;
        //         $companyId = $result->company_id;
        //         // Do something with the company data
        //     } else {
        //         // Handle the case when no data is found in either table
        //         // Both the initial query and the Companies query returned null
        //     }
        // } else {
        //     // Handle the case when data is found in the initial query
        //     // Convert the result set to an array
        //     $projectsUsers = $query->toArray();
        //     $this->set('projectsUsers', $projectsUsers);
        // }
        // $projectsUsers = $query->toArray();
        // $this->set('projectsUsers', $projectsUsers);

        // related companies for this project
        $partner_companies = $this->fetchTable('CompaniesProjects')->find()
            ->where(['project_id' => $id])
            ->toArray();

        // get the related company ids
        $partner_companies_ids = array_column($partner_companies, 'company_id');

        //            debug($partner_companies_ids);
        if (!empty($partner_companies_ids)) {

            $companies = $this->fetchTable('Companies')->find()
                ->where(['id IN' => $partner_companies_ids])
                ->toArray();
            //            debug($companies);
            $this->set(compact('partner_companies', 'companies'));
        }

        // Fetch Related Companies for a Project
        $partner_companies = $this->fetchTable('CompaniesProjects')->find()
            ->where(['project_id' => $id])
            ->toArray();

        // Get the related company ids
        $partner_companies_ids = array_column($partner_companies, 'company_id');

        if (!empty($partner_companies_ids)) {
            // Fetch Company Details
            $companies = $this->fetchTable('Companies')->find()
                ->where(['id IN' => $partner_companies_ids])
                ->toArray();

            // Fetch Document Status for Each Company
            $document_statuses = [];
            foreach ($partner_companies_ids as $companyId) {
                $documentStatus = $this->fetchTable('ProjectsDocuments')->find()
                    ->select(['ProjectsDocuments.document_id', 'ProjectsDocuments.status'])
                    ->innerJoinWith('Documents')
                    ->where([
                        'ProjectsDocuments.project_id' => $id,
                        'ProjectsDocuments.company_id' => $companyId,
                        'Documents.archived' => 0,
                        'ProjectsDocuments.auth_type' => 3,
                        'ProjectsDocuments.auth_value LIKE' => '%Builder%'

                    ])
                    ->toArray();

                $document_statuses[$companyId] = $documentStatus;
            }

            $this->set(compact('partner_companies', 'companies', 'document_statuses'));
        } else {
            $companies = []; // Set an empty array if no companies are found
            $this->set(compact('partner_companies', 'companies'));
        }

        $projectsDocumentsTable = TableRegistry::getTableLocator()->get('ProjectsDocuments');
        $documents = $this->getTableLocator()->get('Documents');
        //PERSONAL DOCUMENTS
        $query = $projectsDocumentsTable->find()
            ->select([
                'id' => 'ProjectsDocuments.id',
                'project_id' => 'ProjectsDocuments.project_id',
                'document_id' => 'ProjectsDocuments.document_id',
                'status' => 'ProjectsDocuments.status',
                'comment' => 'ProjectsDocuments.comment',
                'name' => 'Documents.name',
                'document_type' => 'Documents.document_type',
                'issue_date' => 'Documents.issue_date',
                'expiry_date' => 'Documents.expiry_date',
            ])
            ->innerJoinWith('Documents')
            ->where([
                'ProjectsDocuments.user_id' => $currentUser->id,
                'project_id' => $id,
                'ProjectsDocuments.company_id IS' => null,
                'Documents.archived' => 0,
                'ProjectsDocuments.auth_value LIKE' => '%Builder%', // TODO
            ]);

        $personalDocument = $query->toArray();
        $this->set('personalDocument', $personalDocument);

        //COMPANY DOCUMENTS
        $companyDoc = $projectsDocumentsTable->find()
        ->select([
            'id' => 'ProjectsDocuments.id',
            'project_id' => 'ProjectsDocuments.project_id',
            'document_id' => 'ProjectsDocuments.document_id',
            'status' => 'ProjectsDocuments.status',
            'comment' => 'ProjectsDocuments.comment',
            'name' => 'Documents.name',
            'document_type' => 'Documents.document_type',
            'issue_date' => 'Documents.issue_date',
            'expiry_date' => 'Documents.expiry_date',
        ])
        ->innerJoinWith('Documents')
        ->where([
            'ProjectsDocuments.user_id' => $currentUser->id,
            'project_id' => $id,
            'Documents.archived' => 0,
            'ProjectsDocuments.auth_value LIKE' => '%Builder%', // TODO
        ]);

    $companyDocument = $companyDoc->toArray();
    $this->set('companyDocument', $companyDocument);







    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $project = $this->Projects->newEmptyEntity();
        $this->Authorization->authorize($project);

        if ($this->request->is('post')) {
            $project = $this->Projects->patchEntity($project, $this->request->getData());
            $currentUser = $this->request->getAttribute('identity');
            $project->builder_id = $currentUser->id;
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $randomString = '';
            $characterCount = strlen($characters);
            for ($i = 0; $i < 10; $i++) {
                $randomString .= $characters[rand(0, $characterCount - 1)];
            }
            $existingPasscode = $this->Projects->find('list', [
                'keyField' => 'id', // Assuming your passcodes are stored in the 'id' field
                'valueField' => 'passcode' // Change this to the actual field name storing the passcodes
            ]);
            $existingPasscode = $existingPasscode->toArray();
            while (in_array($randomString, $existingPasscode)) {
                $randomString = '';
                for ($i = 0; $i < 10; $i++) {
                    $randomString .= $characters[rand(0, $characterCount - 1)];
                }
            }
            $project->passcode = $randomString;

            if ($this->Projects->save($project)) {
                $this->Flash->success(__('The project has been saved.'));
                //                $company = FactoryLocator::get('Table')->get('CompaniesUsers')->find()
                //                    ->where([
                //                        'user_id' => $currentUser->id,
                //                        'is_company_admin' => TRUE
                //                    ])->first();
                //                $assignment = FactoryLocator::get('Table')->get('CompaniesProjects')->find();
                //                $assignment->insert(['company_id', 'project_id'])
                //                    ->values([
                //                        'company_id' => $company->company_id,
                //                        'project_id' => $project->id])
                //                    ->execute();
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The project could not be saved. Please, try again.'));
        }
        $users = $this->Projects->Users->find('list', ['limit' => 200])->all();
        $companies = $this->Projects->Companies->find('list', ['limit' => 200])->all();
        $state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];

        $this->set(compact('project', 'users', 'companies', 'state'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Project id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $project = $this->Projects->get($id, [
            'contain' => ['Companies'],
        ]);

        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $projects = $this->fetchTable('Projects');

        $request = $this->request;

        $project = $request->getQuery('project');
        $project = $this->Projects->get($id);
        $builderId = $projects->get($id)->builder_id;



        // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {
            // Find the user's status for the project
            $userStatus = $this->fetchTable('ProjectsUsers')->find()
                ->select(['status'])
                ->where([
                    'project_id' => $id,
                    'user_id' => $currentUser->id,
                ])
                ->first();

            // Check if the user's status is not "Co-Manager"
            if ($userStatus->status != 'Co-Manager') {
                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
            }
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $project = $this->Projects->patchEntity($project, $this->request->getData());
            if ($this->Projects->save($project)) {
                $this->Flash->success(__('The project has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The project could not be saved. Please, try again.'));
        }
        $users = $this->Projects->Users->find('list', ['limit' => 200])->all();
        $companies = $this->Projects->Companies->find('list', ['limit' => 200])->all();
        $this->set(compact('project', 'users', 'companies'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Project id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $project = $this->Projects->get($id);
        $this->Authorization->authorize($project);
        if ($this->Projects->delete($project)) {
            $this->Flash->success(__('The project has been deleted.'));
        } else {
            $this->Flash->error(__('The project could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    // public function generateqr($id = null)
    // {
    //     $project = $this->Projects->get($id, [
    //         'contain' => ['Users', 'Companies', 'Checkins', 'Inductions'],
    //     ]);
    //     $this->Authorization->authorize($project);

    //     $this->set(compact('project'));
    // }
    public function generateqr($id = null)
    {
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');



        if ($currentUser->role == 'On-site Worker') {
            $InductionsItem = FactoryLocator::get('Table')->get('Inductions')->get($id);

            // debug($InductionsItem);
            // //$project =  $project->project;
            // debug($this->Projects);
            $user = FactoryLocator::get('Table')->get('Users')->get($InductionsItem->user_id);
            $project = $this->Projects->get($InductionsItem->project_id, [
                'contain' => ['Users', 'Companies', 'Checkins', 'Inductions'],
            ]);
            $project->user = $user;
            // debug($project);
            // exit;
        } else {
            $project = $this->Projects->get($id, [
                'contain' => ['Users', 'Companies', 'Checkins', 'Inductions'],
            ]);
            // debug($project);
            // exit;
        }

        //$this->Authorization->authorize($project);

        $this->set(compact('project'));
    }

    public function pdf($id = null)
    {
        $state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];
        $project = $this->Projects->get($id);
        $this->Authorization->authorize($project);
        $currentUser = $this->request->getAttribute('identity');
        require_once('lib/phpqrcode/qrlib.php');

        $this->viewBuilder()->enableAutoLayout(false);
        $this->viewBuilder()->setClassName('CakePdf.Pdf');
        $CakePdf = new \CakePdf\Pdf\CakePdf();
        $address = $project->address_no . ' ' . $project->address_street . ', ' . $project->address_suburb . ', ' . $state[$project->address_state] . ' ' . $project->address_postcode;

        if ($this->request->getQuery('type') == 'checkin') {
            $this->viewBuilder()->setOption(
                'pdfConfig',
                [
                    'orientation' => 'portrait',
                    'download' => true, // This can be omitted if "filename" is specified.
                    'filename' => 'checkin_' . $id . '.pdf' //// This can be omitted if you want file name based on URL.
                ]
            );
            $CakePdf->template('checkin', 'checkin');
            $title = $project->name . ' Checkin Poster';
            $CakePdf->viewVars([
                'id' => $project->id,
                'title' => $title,
                'name' => $project->name,
                'address' => $address,
                'builderName' => $currentUser->first_name . " " . $currentUser->last_name,
                'builderMobilePhone' => $currentUser->phone_mobile,
                'builderOfficePhone' => $currentUser->phone_office,
                'builderEmail' => $currentUser->email
            ]);
            $fileDestination = WWW_ROOT . 'uploads' . DS . 'qr_checkin' . DS . $project->id . DS . $title . '.pdf';
            $CakePdf->write($fileDestination);

            if (file_exists($fileDestination)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($fileDestination) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($fileDestination));
                readfile($fileDestination);
            } else {
                $this->Flash->error(__('Error locating checkin poster PDF.'));
            }
        } elseif ($this->request->getQuery('type') == 'induction') {
            $this->viewBuilder()->setOption(
                'pdfConfig',
                [
                    'orientation' => 'portrait',
                    'download' => true, // This can be omitted if "filename" is specified.
                    'filename' => 'induction_' . $id . '.pdf' //// This can be omitted if you want file name based on URL.
                ]
            );

            $CakePdf->template('induction', 'induction');
            $title = $project->name . ' Induction Poster';
            $CakePdf->viewVars([
                'id' => $project->id,
                'title' => $title,
                'name' => $project->name,
                'address' => $address,
                'builderName' => $currentUser->first_name . " " . $currentUser->last_name,
                'builderMobilePhone' => $currentUser->phone_mobile,
                'builderOfficePhone' => $currentUser->phone_office,
                'builderEmail' => $currentUser->email
            ]);
            $fileDestination = WWW_ROOT . 'uploads/qr_induction/' . $project->id . '/' . $title . '.pdf';
            $CakePdf->write($fileDestination);

            if (file_exists($fileDestination)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($fileDestination) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($fileDestination));
                debug(readfile($fileDestination));
            } else {
                $this->Flash->error(__('Error locating induction poster PDF.'));
            }
        }

        //return $this->redirect(['action' => 'generateqr', $project->id]);
    }

    public function staff($id = null)
    {
        $project = $this->Projects->get($id);
        $this->Authorization->authorize($project);
        $currentUser = $this->request->getAttribute('identity');
        $key = $this->request->getQuery('key');
        $type = $this->request->getQuery('type');

        $workers = FactoryLocator::get('Table')->get('Inductions')->find();
        $workers->select([
            'id', 'user_id', 'inducted_date', 'company_id', 'company_name' => 'companies.name',
            'fname' => 'users.first_name', 'lname' => 'users.last_name', 'full_name' => 'concat(users.first_name, users.last_name)', 'role' => 'users.role', 'inducted_date'
        ])->join([
            "table" => "users",
            "type" => "LEFT",
            "conditions" => "Inductions.user_id = users.id"
        ])->join([
            "table" => "companies",
            "type" => "LEFT",
            "conditions" => "Inductions.company_id = companies.id"
        ])->where([
            'project_id' => $project->id
        ])->enableAutoFields();

        if ($project->builder_id != $currentUser->id) {
            $myCompany = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->where([
                'user_id' => $currentUser->id
            ])->first();
            $workers->where(['company_id' => $myCompany->company_id]);
        }

        $search_words = 0;
        if ($key) {
            $search_words = $key;
        }

        if ($type == 0) {
            if ($key) {
                $workers->find('all')->where(['companies.name like' => '%' . $key . '%']);
            }
        } elseif ($type == 1) {
            $key = str_replace(' ', '', $key);
            if ($key) {
                $workers->find('all')->where(['concat(users.first_name, users.last_name) like' => '%' . $key . '%']);
            }
        }

        if ($this->request->getQuery('type') && $search_words == 0) {
            $this->Flash->error(__('Please enter search terms.'));
        }
        $workers = $this->paginate($workers);
        $this->set(compact('project', 'workers', 'search_words', 'type'));
    }

    public function removeContractor($id = null)
    {
        $project = $this->Projects->get($id);
        $this->Authorization->authorize($project);
        if ($this->request->is('get')) {
            $company_id = $this->request->getQuery('company');
            $assignment = FactoryLocator::get('Table')->get('CompaniesProjects')->find()->where([
                'company_id' => $company_id,
                'project_id' => $id
            ])->first();
            if (FactoryLocator::get('Table')->get('CompaniesProjects')->delete($assignment)) {
                $contractors = FactoryLocator::get('Table')->get('Inductions')->find()->where([
                    'company_id' => $company_id,
                    'project_id' => $id
                ]);

                if ($contractors != NULL) {
                    foreach ($contractors as $c) {
                        FactoryLocator::get('Table')->get('Inductions')->delete($c);
                    }
                }
                $this->Flash->success(__('The contractor has been removed.'));
            } else {
                $this->Flash->error(__('The contractor could not be removed. Please, try again.'));
            }
        } else {
            $this->Flash->error(__('No contractor selected.'));
        }
        return $this->redirect(['controller' => 'projects', 'action' => 'view', $id]);
    }

    public function cancelProject($id = null)
    {
        $project = $this->Projects->get($id);
        $this->Authorization->authorize($project);

        $project->status = 'Cancelled';

        if ($this->Projects->save($project)) {
            $this->Flash->success(__('The project has been cancelled'));
            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__('The project could not be cancelled. Please, try again.'));
        }
    }

    public function generatepasscode($id)
    {
        $project = $this->Projects->get($id);
        $this->Authorization->skipauthorization();
        $currentUser = $this->request->getAttribute('identity');
        $projects = $this->fetchTable('Projects');

        $request = $this->request;



        $builderId = $projects->get($id)->builder_id;



        // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {
            // Find the user's status for the project
            $userStatus = $this->fetchTable('ProjectsUsers')->find()
                ->select(['status'])
                ->where([
                    'project_id' => $id,
                    'user_id' => $currentUser->id,
                ])
                ->first();

            // Check if the user's status is not "Co-Manager"
            if (!$userStatus || $userStatus->status != 'Co-Manager') {
                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
            }
        }
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';
        $characterCount = strlen($characters);
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $characterCount - 1)];
        }
        $existingPasscode = $this->Projects->find('list', [
            'keyField' => 'id',
            'valueField' => 'passcode'
        ]);
        $existingPasscode = $existingPasscode->toArray();
        while (in_array($randomString, $existingPasscode)) {
            $randomString = '';
            for ($i = 0; $i < 10; $i++) {
                $randomString .= $characters[rand(0, $characterCount - 1)];
            }
        }
        $project->passcode = $randomString;
        if ($this->Projects->save($project)) {
            $this->Flash->success(__('The passcode of this project has been regenerated'));
            return $this->redirect(['action' => 'view', $id]);
        } else {
            $this->Flash->error(__('The passcode of this project could not be regenerate. Please, try again.'));
        }
    }


    public function leave($projectId)
    {
        $currentUser = $this->request->getAttribute('identity');
        $this->Authorization->skipAuthorization();
        try {
            // Find the ProjectsUsers record for the current user and project
            $projectsUsersTable = $this->getTableLocator()->get('ProjectsUsers');
            $projectUser = $projectsUsersTable->find()
                ->where([
                    'project_id' => $projectId,
                    'user_id' => $currentUser->id,
                ])
                ->firstOrFail(); // Throws an exception if the record is not found

            // Update the status field to 'Disengaged'
            $projectUser->status = 'Disengaged';

            if ($projectsUsersTable->save($projectUser)) {
                $this->Flash->success(__('You have left the project.'));
            } else {
                $this->Flash->error(__('Unable to leave the project. Please try again.'));
            }
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Project not found.'));
        }

        return $this->redirect(['action' => 'index']); // Redirect to the project list or appropriate location
    }

    public function companyleave($projectId)
    {
        $currentUser = $this->request->getAttribute('identity');
        $this->Authorization->skipAuthorization();
        $company = $this->fetchTable('companies')->find()
            ->where([
            'admin_id'=>  $currentUser->id
            ])->first();
        $company = $company->id;

        try {
            // Find the ProjectsUsers record for the current user and project
            $projectsUsersTable = $this->getTableLocator()->get('CompaniesProjects');
            $projectUser = $projectsUsersTable->find()
                ->where([
                    'project_id' => $projectId,
                    'company_id' => $company,
                ])
                ->firstOrFail(); // Throws an exception if the record is not found
            // Update the status field to 'Disengaged'
            $projectUser->status = 'Disengaged';

            if ($projectsUsersTable->save($projectUser)) {
                $this->Flash->success(__('You have left the project.'));
            } else {
                $this->Flash->error(__('Unable to leave the project. Please try again.'));
            }
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Project not found.'));
        }

        return $this->redirect(['action' => 'index']); // Redirect to the project list or appropriate location
    }

    public function completeProject($id = null)
    {
        $project = $this->Projects->get($id);
        $this->Authorization->authorize($project);

        $project->status = 'Complete';
        $project->completion_date = Time::now()->format('Y-m-d');

        if ($this->Projects->save($project)) {
            $this->Flash->success(__('The project has been marked as completed.'));
            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__('The project could not be marked as completed. Please, try again.'));
        }
    }


}



