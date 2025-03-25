<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\ProjectsDocument;
use Cake\Datasource\FactoryLocator;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\View\JsonView;

/**
 * Companies Controller
 *
 * @property \App\Model\Table\CompaniesTable $Companies
 * @property \App\Model\Table\CompaniesUsersTable $CompaniesUsers
 * @property \App\Model\Table\CompaniesProjectsTable $CompaniesProjects
 * @method \App\Model\Entity\Company[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CompaniesController extends AppController
{



    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions(['listCompaniesAjax', 'add']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $company = $this->Companies->newEmptyEntity();
        $this->Authorization->authorize($company);
        $currentUser = $this->request->getAttribute('identity');

        if ($currentUser->role != 'Admin'){
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
        }
        $allCompanies = $this->Companies->find();

        $requestedCompanies = $allCompanies->select(['id' => 'Companies.id'])
            ->join([
                "table" => "requests",
                "type" => "LEFT",
                "conditions" => "Companies.id = requests.company_id"
            ])->where(['requests.user_id' => $currentUser->id, 'requests.approved_at IS NULL']);

        $allCompanies = $this->Companies->find();

        $yesRequestedCompanies = $allCompanies->where(['id IN' => $requestedCompanies]);
        $allCompanies = $this->Companies->find();

        $joinedCompaniesSelect = $allCompanies->select(['id' => 'Companies.id'
        ])
            ->join([
                "table" => "companies_users",
                "type" => "LEFT",
                "conditions" => "Companies.id = companies_users.company_id"
            ])->where(['companies_users.user_id' => $currentUser->id]);

        $allCompanies = $this->Companies->find();

        $joinedCompanies = $allCompanies->where(['id IN' => $joinedCompaniesSelect]);

        $allCompanies = $this->Companies->find();

        $unRequestedCompanies = $allCompanies->where(['NOT' => ['id IN' => $requestedCompanies]]);
        $unRequestedCompanies = $unRequestedCompanies->where(['NOT' => ['id IN' => $joinedCompaniesSelect]]);

//        $rejectedCompanies = $this->Companies->find();
//        $rejectedCompanies = $rejectedCompanies->select(['id' => 'Companies.id', 'name' => 'Companies.name',
//            'company_type' => 'Companies.company_type', 'abn' => 'Companies.abn', 'address_no' => 'Companies.address_no', 'address_street' => 'Companies.address_street', 'address_suburb' => 'Companies.address_suburb',
//            'address_state' => 'Companies.address_state', 'address_postcode' => 'Companies.address_postcode', 'contact_name' => 'Companies.contact_name', 'contact_email' => 'Companies.contact_email', 'contact_phone' => 'Companies.contact_phone'
//            ,'removal_status' => 'requests.removal_status'
//        ])
//            ->join([
//                "table" => "requests",
//                "type" => "LEFT",
//                "conditions" => "Companies.id = requests.company_id"
//            ])->where([
//                'requests.removal_status' => 1
//            ]);

        $companies = $this->paginate($yesRequestedCompanies);


        $this->set(compact('companies','unRequestedCompanies','joinedCompanies'));
    }


    public function projectPartner($project_id){  //Associated companies list
        $company = $this->Companies->newEmptyEntity();
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $projects = $this->fetchTable('Projects');

        $request = $this->request;

        $project = $request->getQuery('project');

        $builderId = $projects->get($project_id)->builder_id;



        // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {
            // Find the user's status for the project
            $userStatus = $this->fetchTable('ProjectsUsers')->find()
                ->select(['status'])
                ->where([
                    'project_id' => $project_id,
                    'user_id' => $currentUser->id,
                ])
                ->first();

            // Check if the user's status is not "Co-Manager"
            if ($userStatus->status != 'Co-Manager') {
                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
            }
        }
        $partner_company = $this->fetchTable('CompaniesProjects')->find()->select('company_id')->where(['project_id' => $project_id]);
        $assignedCompanies = $this->Companies->find();
        $assignedCompanies = $assignedCompanies->select(['id' => 'Companies.id','partnerid'=>'companies_projects.id', 'name' => 'Companies.name','project_id' => 'companies_projects.project_id',
            'company_type' => 'Companies.company_type', 'abn' => 'Companies.abn', 'address_no' => 'Companies.address_no', 'address_street' => 'Companies.address_street', 'address_suburb' => 'Companies.address_suburb',
            'address_state' => 'Companies.address_state', 'address_postcode' => 'Companies.address_postcode', 'contact_name' => 'Companies.contact_name', 'contact_email' => 'Companies.contact_email', 'contact_phone' => 'Companies.contact_phone'
        ])
            ->join([
                "table" => "companies_projects",
                "type" => "LEFT",
                "conditions" => "Companies.id = companies_projects.company_id"
            ])->where([
                'Companies.id IN' => $partner_company,
                'companies_projects.project_id' => $project_id
            ]);
        $companies = $this->paginate($assignedCompanies);

        $this->set(compact('companies','partner_company'));

    }

    public function addPartner($projectID)
    {
        $this->Authorization->skipauthorization();
        $currentUser = $this->request->getAttribute('identity');

        $companiesProjectsTable = $this->loadModel('CompaniesProjects');
        $companiesProject = $companiesProjectsTable->newEmptyEntity();
        $companyID = null;
        $assignedCompanies = $this->Companies->find();
        $assignedCompanies = $assignedCompanies->select(['name'=> 'Companies.name'])
            ->innerJoin('companies_projects',[
                'Companies.id = companies_projects.company_id',
            ])
            ->innerJoin('projects', [
                'projects.id = companies_projects.project_id',
            ])
            ->where([
                'builder_id'=>$currentUser->id
            ])->distinct();
        $companyNames = [];
        foreach ($assignedCompanies as $company) {
            $companyNames[] = $company->name;
        }

        $this->set('companyNames', $companyNames);
        $this->set('companyID', $companyID);
        $this->set('projectID', $projectID);

        if ($this->request->is('post')) {

            $data = $this->request->getData();
            $selectedCompanyName = $data['company_name'];
            $selectedName = $companyNames[$selectedCompanyName];
            $selectedCompanyID = $this->Companies->find()
                ->select(['id'])
                ->where(['name' => $selectedName])
                ->first();
            if ($selectedCompanyID) {
                $companyID = $selectedCompanyID['id'];
                $existingRecord = $this->CompaniesProjects->find()
                    ->where(['company_id' => $companyID, 'project_id' => $projectID])
                    ->first();
                if (!$existingRecord) {
                    $companyProjectData = [
                        'company_id' => $companyID,
                        'project_id' => $projectID
                    ];
                    $this->CompaniesProjects->patchEntity($companiesProject, $companyProjectData);
                    $this->CompaniesProjects->save($companiesProject);
                    $this->Flash->success(__('Your associated company has been assigned to the project.'));
                } else {
                    $this->Flash->error(__('This company is already associated with this project. Please, try again.'));
                }
            } else {
                $this->Flash->error(__('The company could not be added. Please, try again.'));
            }
        }
    }



    public function removePartner($partnerId, $projectId){ //Remove associated companies from list
        $this->Authorization->skipAuthorization();
        $this->request->allowMethod(['post', 'delete']);

        $table = $this->fetchTable('CompaniesProjects');
        $partner = $table->get($partnerId);

        if ($table->delete($partner)) {
            $this->Flash->success(__('Associated company has been removed from this project'));
        } else {
            $this->Flash->error(__('Associated company could not be removed from this project. Please, try again'));
        }

        return $this->redirect(['action' => 'projectPartner', $projectId]);
    }
    public function myindex()
    {
        $company = $this->Companies->newEmptyEntity();
        $this->Authorization->authorize($company);
        $currentUser = $this->request->getAttribute('identity');

        $company_user = $this->fetchTable('CompaniesUsers')->find()->select('company_id')->where(['user_id' => $currentUser->id]);
        $assignedCompanies = $this->Companies->find();
        $assignedCompanies = $assignedCompanies->select(['id' => 'Companies.id', 'name' => 'Companies.name', 'user_id' => 'companies_users.user_id',
            'company_type' => 'Companies.company_type', 'abn' => 'Companies.abn', 'address_no' => 'Companies.address_no', 'address_street' => 'Companies.address_street', 'address_suburb' => 'Companies.address_suburb',
            'address_state' => 'Companies.address_state', 'address_postcode' => 'Companies.address_postcode', 'contact_name' => 'Companies.contact_name', 'contact_email' => 'Companies.contact_email', 'contact_phone' => 'Companies.contact_phone',
            'status' => 'companies_users.status', 'is_company_admin' => 'is_company_admin'
        ])
            ->join([
                "table" => "companies_users",
                "type" => "LEFT",
                "conditions" => "Companies.id = companies_users.company_id"
            ])->where([
                'Companies.id IN' => $company_user,
                'companies_users.user_id' => $currentUser->id
            ]);
        $existingCompany = $this->Companies->find()->where(['admin_id' => $currentUser->id])->first();


        $companies = $this->paginate($assignedCompanies);

        $this->set(compact('companies','company_user', 'existingCompany'));
    }

    public function mysubcontractor(){
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $company = $this->Companies->find()->where(['admin_id'=>$currentUser->id])->first();

        if(!$company){
            $this->Flash->error(__('Please add your company before access this page. Please try again.'));
            return $this->redirect(['action' => 'add']);
        }

        $assignedCompanies = $this->Companies->find();
        $assignedCompanies = $assignedCompanies->select(['company_userID'=>'companies_users.id','id' => 'Companies.id', 'name' => 'Companies.name', 'user_id' => 'companies_users.user_id',
            'company_type' => 'Companies.company_type', 'abn' => 'Companies.abn', 'address_no' => 'Companies.address_no', 'address_street' => 'Companies.address_street', 'address_suburb' => 'Companies.address_suburb',
            'address_state' => 'Companies.address_state', 'address_postcode' => 'Companies.address_postcode', 'contact_name' => 'Companies.contact_name', 'contact_email' => 'Companies.contact_email', 'contact_phone' => 'Companies.contact_phone',
            'status' => 'companies_users.status', 'is_company_admin' => 'is_company_admin'
        ])
            ->join([
                "table" => "companies_users",
                "type" => "LEFT",
                "conditions" => "Companies.admin_id = companies_users.user_id"
            ])->where([
                'companies_users.company_id' => $company->id,
                'companies_users.is_company_admin' => 1,
                'companies_users.status !=' => 'Owner'
            ]);
        $companies = $this->paginate($assignedCompanies);

        $this->set(compact('companies', 'company'));
    }

    public function myclient(){
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $company = $this->Companies->find()->where(['admin_id'=>$currentUser->id])->first();
        if(!$company){
            $this->Flash->error(__('Please add your company before access this page. Please try again.'));
            return $this->redirect(['action' => 'add']);
        }
        $assignedCompanies = $this->Companies->find();
        $assignedCompanies = $assignedCompanies->select(['company_userID'=>'companies_users.id','id' => 'Companies.id', 'name' => 'Companies.name', 'user_id' => 'companies_users.user_id',
            'company_type' => 'Companies.company_type', 'abn' => 'Companies.abn', 'address_no' => 'Companies.address_no', 'address_street' => 'Companies.address_street', 'address_suburb' => 'Companies.address_suburb',
            'address_state' => 'Companies.address_state', 'address_postcode' => 'Companies.address_postcode', 'contact_name' => 'Companies.contact_name', 'contact_email' => 'Companies.contact_email', 'contact_phone' => 'Companies.contact_phone',
            'status' => 'companies_users.status', 'is_company_admin' => 'is_company_admin'
        ])
            ->join([
                "table" => "companies_users",
                "type" => "LEFT",
                "conditions" => "Companies.id = companies_users.company_id"
            ])->where([
                'companies_users.user_id' => $currentUser->id,
                'companies_users.is_company_admin' => 1,
                'companies_users.status !=' => 'Owner'
            ]);
        $companies = $this->paginate($assignedCompanies);

        $this->set(compact('companies', 'company'));


    }

    /**
     * View method
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $company = $this->Companies->get($id, [
            'contain' => ['Projects', 'Users'],
        ]);
        $currentUser = $this->request->getAttribute('identity');
        $this->Authorization->skipAuthorization();


        // Get company owner's ID
        $ownerId = $this->Companies->get($id)->admin_id;


        // Get associated user IDs
        $associatedUserIds = $this->fetchTable('CompaniesUsers')->find()
            ->where(['company_id' => $id])
            ->extract('user_id')
            ->toArray();

        // Get builder IDs with projects associated to the company
        $projectIds = $this->fetchTable('CompaniesProjects')->find()
            ->where(['company_id' => $id])
            ->extract('project_id')
            ->toArray();

        $associatedBuilderIds = [];
        if (!empty($projectIds)) {
            $associatedBuilderIds = $this->fetchTable('Projects')->find()
                ->select(['builder_id'])
                ->whereInList('id', $projectIds)
                ->extract('builder_id')
                ->toArray();
        }
        $referer = $this->referer();
        // Check if the user is admin, owner, associated user, or associated builder
        if ($currentUser->role != 'Admin' && $currentUser->id != $ownerId && !in_array($currentUser->id, $associatedUserIds) && !in_array($currentUser->id, $associatedBuilderIds) && $referer != '/requests' && $referer != '/requests/companyrequestindex' && $referer != '/requests/invitation' && $referer != '/requests/company-invitation' && $referer != '/requests/builder-project-invitation' && $referer != '/companies/mysubcontractor' && $referer != '/companies/myclient') {
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
        }
        $documents = FactoryLocator::get('Table')->get('Documents')->find()->where([
            'related_company_id' => $id,
        ]);


        $company_user = $this->fetchTable('CompaniesUsers')->find()
            ->select([
                'user_id',
                'id',
                'first_name' => 'users.first_name',
                'last_name' => 'users.last_name',
                'role' => 'users.role',
                'phone_mobile' => 'users.phone_mobile',
                'status'])
            ->join([
                "table" => "users",
                "type" => "LEFT",
                "conditions" => "user_id = users.id"
            ])
            ->order([
                "FIELD(CompaniesUsers.status, 'Engaged', 'Disengaged')"
            ])
            ->where(['company_id' => $id,'is_company_admin'=>0]);

        $company_requests = $this->fetchTable('Requests')->find()
            ->select([
                'id' => 'Requests.id',
                'user_id' => 'Requests.user_id',
                'user_name' => 'CONCAT(u1.first_name, " ", u1.last_name)',
                'user_role' => 'u1.role',
                'user_phone' => 'u1.phone_mobile',
                'removal_status'=> 'Requests.removal_status'// Adjust the field name based on your actual schema
            ])
            ->leftJoin(['u1' => 'users'], ['Requests.user_id = u1.id'])
            ->where(['Requests.company_id' => $id, 'Requests.approved_at IS' => null])
            ->toList();

//        $employment = FactoryLocator::get('Table')->get('CompaniesUsers')->find();
//        $employment->select(['user_id'])
//            ->where([
//                'company_id' => $id,
//                'is_company_admin' => TRUE
//            ])->first();
//
//        foreach($employment as $e){
//            $admin_id = $e->user_id;
//        }
//
//        $isEmployee = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->where([
//            'company_id' => $id,
//            'user_id' => $currentUser->id,
//            'confirmed' => 1,
//        ])->first();
//        if ($isEmployee){
//            $documentsVisible = TRUE;
//        } else {
//            $documentsVisible = FALSE;
//        }
//        $projectIds = FactoryLocator::get('Table')->get('CompaniesProjects')->find()->select('project_id')->where([
//            'company_id' => $id
//        ]);
        $associatedCompany = $this->fetchTable('companiesusers')->find()
            ->select([
                'user_id' => 'companiesusers.user_id',
                'is_company_admin' => 'companiesusers.is_company_admin',
                'company_name' => 'companies.name',
                'company_type' => 'companies.company_type',
                'company_phone' => 'companies.contact_phone',
                'company_email'=> 'companies.contact_email',
                'company_ABN' => 'companies.abn',
                'company_id' => 'companiesusers.company_id',
            ])
            ->join([
                "table" => "companies",
                "type" => "LEFT",
                "conditions" => "companiesusers.user_id = companies.admin_id"
            ])
            ->where(['company_id' => $id, 'is_company_admin' =>1,'user_id !='=>$currentUser->id]);
        $this->set('associatedCompany', $associatedCompany);

        $companyAdmin = $this->Companies->find()
            ->select(['admin_id'])
            ->where(['id' =>$id])
        ->first();
        $companyAdmin =  $companyAdmin->get('admin_id');
        $this->set('companyAdmin', $companyAdmin);

//debug($associatedCompany->toList());
//debug(gettype($companyAdmin));

        //get project id through url
        $pj_id = $this->request->getQuery('pj_id');


        // get project document object
        if(!empty($pj_id)) {
            $projectDocs = $this->fetchTable('ProjectsDocuments')->find()
                ->where(['company_id ' => $id, 'project_id' => $pj_id])
                ->toArray();

            // get document ids
            $docIds = array_column($projectDocs, 'document_id');

            // debug($docIds);
            // debug($projectDocs);


            if ($docIds) {
                //get all documents related to this project
                $company_project_doc = $this->fetchTable('Documents')->find()
                    ->where(['id IN' => $docIds, 'archived' => 0])
                    ->toArray();

            // debug($company_project_doc);

              /*  $company_project_doc = $this->fetchTable('ProjectsDocuments')->find()
                    ->join([
                        "table" => "Documents",
                        "type" => "LEFT",
                        "conditions" => "Documents.id = ProjectsDocuments.document_id"
                    ])
                    ->where(['ProjectsDocuments.document_id IN'=> $docIds,'ProjectsDocuments.auth_value !='=>'Worker Acknowledgement','ProjectsDocuments.project_id'=>$pj_id,'Documents.achieved' => 0])
                  ->toArray();*/

                $pj_documentInfos = [];
                if (!empty($company_project_doc)) {
                    foreach ($docIds as $docId) {
                        $pj_documentInfo = $this->fetchTable('ProjectsDocuments')->find()
                            ->select(['status', 'auth_type', 'auth_value', 'project_id','document_id','comment'])
                            ->where([
                                'document_id' => $docId,
                                'project_id' => $pj_id
                            ])
                            ->toArray();

                        $pj_documentInfos[$docId] = $pj_documentInfo;

                    }
                }
                // debug($pj_documentInfos);
                $this->set(compact('company_project_doc','projectDocs', 'pj_documentInfos'));
            }
        }

        $this->set(compact('company',  'documents', 'company_user','company_requests'));
    }


//    /**
//     * Add method
//     *
//     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
//     */
//    public function add()
//    {
//        $company = $this->Companies->newEmptyEntity();
//        $this->Authorization->authorize($company);
//        $currentUser = $this->request->getAttribute('identity');
//
//        $companyPresent = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->select('company_id')->where([
//            'user_id' => $currentUser->id
//        ])->first();
//        if ($companyPresent){
//            $this->Flash->error(__('You have already added your company.'));
//            return $this->redirect(['controller' => 'projects', 'action' => 'index']);
//        }
//
//        if ($this->request->is('post')) {
//            $company = $this->Companies->patchEntity($company, $this->request->getData());
//            $company->admin_id = $currentUser->id;
//            if ($this->Companies->save($company)) {
//                $this->Flash->success(__('The company has been saved.'));
//                $employment = FactoryLocator::get('Table')->get('CompaniesUsers')->find();
//                $employment->insert(['company_id', 'user_id', 'is_company_admin', 'confirmed', 'inducted'])
//                    ->values([
//                        'company_id' => $company->id,
//                        'user_id' => $currentUser->id,
//                        'is_company_admin' => TRUE,
//                        'confirmed' => TRUE,
//                        'inducted' => date('Y-m-d H:i:s'),
//                    ])->execute();
//                return $this->redirect(['controller' => 'projects', 'action' => 'index']);
//            }
//            $this->Flash->error(__('The company could not be saved. Please, try again.'));
//        }
//        $this->set(compact('company'));
//    }

    public function approveNewWorker($companyId = null, $requestId = null)
    {
        $this->request->allowMethod(['post']);
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $company = $this->fetchTable('Companies')->get($companyId);
        if ($currentUser->id != $company->id ) {
            $request = $this->fetchTable('Requests')->get($requestId);
            $request->approved_at = FrozenTime::now();

            if ($this->fetchTable('Requests')->save($request)) {
                // Create entry in companies_users table
                $companyUser = $this->fetchTable('CompaniesUsers')->newEmptyEntity();
                $companyUser->company_id = $companyId;
                $companyUser->user_id = $request->user_id;
                $companyUser->confirmed = 1;

                if ($this->fetchTable('CompaniesUsers')->save($companyUser)) {
                    $this->Flash->success(__('Worker has been approved and added to the company.'));
                } else {
                    $this->Flash->error(__('Error adding worker to company. Please try again.'));
                }
            } else {
                $this->Flash->error(__('Error approving worker. Please try again.'));
            }
        }
        else {$this->Flash->error(__("You do not have permission to do this."));}

        return $this->redirect(['action' => 'view',$companyId]);
    }

    public function rejectNewWorker($companyId = null, $requestId = null){
        $this->Authorization->skipAuthorization();
        $this->request->allowMethod(['post','delete']);
        $RequestsTable = $this->loadModel('Requests');
        $request = $this->fetchTable('Requests')->get($requestId);
            if ($RequestsTable ->delete($request)) {
                $this->Flash->success(__('Worker has been rejected.'));
            } else {
                $this->Flash->error(__('Error rejecting worker to company. Please try again.'));
            }
        return $this->redirect(['action' => 'view',$companyId]);

    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $project = $this->Companies->newEmptyEntity();
        $this->Authorization->skipAuthorization();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $project = $this->Companies->patchEntity($project, $this->request->getData());

            $currentUser = $this->request->getAttribute('identity');

            $project->admin_id = $currentUser->id;
            $project->company_type = $currentUser->role;

            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $randomString= '';
            $characterCount = strlen($characters);
            for ($i = 0; $i < 10; $i++) {
                $randomString .= $characters[rand(0, $characterCount - 1)];
            }
            $existingPasscode = $this->Companies->find('list', [
                'keyField' => 'id', // Assuming your passcodes are stored in the 'id' field
                'valueField' => 'passcode' // Change this to the actual field name storing the passcodes
            ]);
            $existingPasscode = $existingPasscode->toArray();
            while (in_array($randomString,$existingPasscode)){
                $randomString= '';
                for ($i = 0; $i < 10; $i++) {
                    $randomString .= $characters[rand(0, $characterCount - 1)];
                }
            }
            $project->passcode = $randomString;

            if ($this->Companies->save($project)) {

                $this->Flash->success(__('Your company has been created.'));
                $company = FactoryLocator::get('Table')->get('CompaniesUsers')->find()
                    ->where([
                        'user_id' => $currentUser->id,
                        'company_id' =>$project->id,
                        'is_company_admin' => TRUE
                    ])->first();
                if (empty($company)) {
                    $companyUsersTable = FactoryLocator::get('Table')->get('CompaniesUsers');
                    $companyUser = $companyUsersTable->newEntity([
                        'user_id' => $currentUser->id,
                        'company_id' => $project->id, // Replace with the actual company ID
                        'is_company_admin' => 1,
                        'confirmed' => 1,
                        'status' =>'Owner'
                    ]);
                    $companyUsersTable->save($companyUser);
                }

                $assignment = FactoryLocator::get('Table')->get('CompaniesProjects')->find();
                if (empty($assignment)) {
                    $assignment->insert(['company_id', 'project_id'])
                        ->values([
                            'company_id' => $company->id,
                            'project_id' => $project->id
                        ])
                        ->execute();
                }

                return $this->redirect(['action' => 'view', $project->id]);
            }
            $this->Flash->error(__('The project could not be saved. Please, try again.'));
        }

        $users = $this->Companies->Users->find('list', ['limit' => 200])->all();

        $companies = $this->Companies->find('list', ['limit' => 200])->all();

        $companys = $this->Companies->newEmptyEntity();


        $companies1 = $this->paginate($this->Companies);
//        echo '<pre>';
//        print_r($companies1);
        $this->set(compact('companies1'));

        $this->set(compact('project', 'users', 'companies'));


    }

    /**
     * Approve request method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function join($companyid = null){
        $companyUser = $this->fetchTable('CompaniesUsers')->newEmptyEntity();
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');


        $company = TableRegistry::getTableLocator()->get('Companies')->find()->where(['id' => $companyid])->first();

        $companyUser->company_id = $company->id;
        $companyUser->user_id = $currentUser->id;
        $companyUser->is_company_admin = 0;
        $companyUser->confirmed = 1;
        $companyUser->inducted = FrozenTime::now();

        if ($this->fetchTable('CompaniesUsers')->save($companyUser)) {
            $this->Flash->success(__('You have successfully joined this company.'));

            return $this->redirect(['controller' => 'companies', 'action' => 'index']);
        }
        $this->Flash->error(__('The join for this company was unsuccessful. Please, try again.'));


    }

    public function leave($compnay_id = null){
        $this->Authorization->skipAuthorization();
        $companiesUsersTable = $this->loadModel('CompaniesUsers');
        $currentUser = $this->request->getAttribute('identity');
        $companyUser = $this->fetchTable('CompaniesUsers')->find()->where(['company_id' => $compnay_id, 'user_id' => $currentUser->id])->first();
        if ($companyUser->status == 'Engaged'){
            $companyUser->status = 'Disengaged';
            if ($companiesUsersTable ->save($companyUser)){
                $this->Flash->success(__('You have left the company'));
                return $this->redirect(['action' => 'myindex']);
            }else {
                $this->Flash->error(__('You could not leave the company. Please, try again.'));
                return $this->redirect(['action' => 'myindex']);
            }
        }elseif ($companyUser->status == 'Disengaged'){
            if ($this->fetchTable('CompaniesUsers')->delete($companyUser)) {
                $this->Flash->success(__('This company record has been removed from your account.'));
                return $this->redirect(['action' => 'myindex']);
            }else {
                $this->Flash->error(__('This company record could not be removed from your account. Please, try again.'));
                return $this->redirect(['action' => 'myindex']);
            }
        } else {
            if ($this->fetchTable('CompaniesUsers')->delete($companyUser)) {
                $this->Flash->success(__('User has been removed from company'));
         }else {
                $this->Flash->error(__('User could not be removed from Company. Please, try again.'));
            }
        }
        return $this->redirect(['action' => 'index']);
    }
    public function disengage($company_userID){
        $this->Authorization->skipAuthorization();
        $companiesUsersTable = $this->loadModel('CompaniesUsers');
        $currentUser = $this->request->getAttribute('identity');
        $referer = $this->referer();
        $companyUser = $this->fetchTable('CompaniesUsers')->find()->where(['id' => $company_userID])->first();
        if ($companyUser->status == 'Engaged'){
            $companyUser->status = 'Disengaged';
            if ($companiesUsersTable ->save($companyUser)){
                $this->Flash->success(__('You have disengaged with this company.'));
            }else {
                $this->Flash->error(__('You could not disengaged this company. Please, try again.'));
            }
            if ($referer == '/companies/mysubcontractor'){
                return $this->redirect(['action' => 'mysubcontractor']);
            } elseif($referer == '/companies/myclient'){
                return $this->redirect(['action' => 'myclient']);
            }
        }elseif ($companyUser->status == 'Disengaged') {

            if ($this->fetchTable('CompaniesUsers')->delete($companyUser)) {
                $this->Flash->success(__('You have archived this record.'));
            } else {
                $this->Flash->error(__('You could not archive this record. Please, try again.'));
            }
            if ($referer == '/companies/mysubcontractor'){
                return $this->redirect(['action' => 'mysubcontractor']);
            } elseif($referer == '/companies/myclient'){
                return $this->redirect(['action' => 'myclient']);
            }
        }
    }

    public function deleteCompanyUser($company_id = null,$id = null){
        $this->Authorization->skipAuthorization();
        $companyUser = $this->fetchTable('CompaniesUsers')->get($id);

        if ($companyUser->status === 'Engaged') {
            // If the status is 'Engaged', update it to 'Disengaged'
            $companyUser->status = 'Disengaged';
            if ($this->fetchTable('CompaniesUsers')->save($companyUser)) {
                $this->Flash->success(__('User status has been changed to Disengaged.'));
            } else {
                $this->Flash->error(__('User status could not be changed. Please, try again.'));
            }
        } elseif ($companyUser->status === 'Disengaged') {
            // If the status is 'Disengaged', delete the record
            if ($this->fetchTable('CompaniesUsers')->delete($companyUser)) {
                $this->Flash->success(__('User has been removed from company'));
            } else {
                $this->Flash->error(__('User could not be removed from Company. Please, try again.'));
            }
        } else {
            // Handle unexpected status value
            $this->Flash->error(__('Unexpected status value. Please, try again.'));
        }

        return $this->redirect(['action' => 'view', $company_id]);


    }

    /**
     * Edit method
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $company = $this->Companies->get($id, [
            'contain' => ['Projects', 'Users'],
        ]);
        $currentUser = $this->request->getAttribute('identity');
        $this->Authorization->skipAuthorization();
        if ( $currentUser->role != 'Admin' && $currentUser->id != $this->Companies->get($id)->admin_id ){
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
        }
        //$this->Authorization->authorize($company);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $company = $this->Companies->patchEntity($company, $this->request->getData());
            if ($this->Companies->save($company)) {
                $this->Flash->success(__('The company has been saved.'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The company could not be saved. Please, try again.'));
        }
        $projects = $this->Companies->Projects->find('list', ['limit' => 200])->all();
        $users = $this->Companies->Users->find('list', ['limit' => 200])->all();
        $chosenState = $company->address_state;
        $this->set(compact('company', 'projects', 'users', 'chosenState'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $company = $this->Companies->get($id);
        $this->Authorization->authorize($company);
        if ($this->Companies->delete($company)) {
            $this->Flash->success(__('The company has been deleted.'));
        } else {
            $this->Flash->error(__('The company could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function staff($id = null)
    {
        $company = $this->Companies->get($id, [
            'contain' => ['Projects', 'Users'],
        ]);
        $this->Authorization->authorize($company);

        if ($this->request->is("get") && $this->request->getQuery('deleteUser')){
            $deleteUser = $this->request->getQuery('deleteUser');
            $employee = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->where([
                'company_id' => $id,
                'user_id' => $deleteUser
            ])->first();
            if (FactoryLocator::get('Table')->get('CompaniesUsers')->delete($employee)){
                $this->Flash->success(__('Employee removed from company.'));
            } else {
                $this->Flash->error(__('The employee could not be removed. Please, try again.'));
            }
            return $this->redirect(['action' => 'staff', $id]);
        }

        $employeeIds = FactoryLocator::get('Table')->get('CompaniesUsers')->find();
        $employeeIds->select(['user_id'])->where([
            'company_id' => $id,
            'confirmed' => '1'
        ]);

        $employees = FactoryLocator::get('Table')->get('Users')->find()->where([
            'id IN' => $employeeIds,
        ]);

        $employment = FactoryLocator::get('Table')->get('CompaniesUsers')->find();
        $employment->select(['user_id'])
            ->where([
                'company_id' => $id,
                'is_company_admin' => TRUE
            ])->first();

        foreach($employment as $e){
            $admin_id = $e->user_id;
        }

        $this->set(compact('company', 'admin_id', 'employees'));
    }

    public function change($id = null)
    {
        $company = $this->Companies->newEmptyEntity();
        $this->Authorization->authorize($company);
        $currentUser = $this->request->getAttribute('identity');

        $companyPresent = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->select('company_id')->where([
            'user_id' => $currentUser->id
        ])->first();
        if ($companyPresent){
            $this->Flash->error(__('You are already assigned to a company.'));
            return $this->redirect(['controller' => 'projects', 'action' => 'index']);
        }

        if ($this->request->is('post') && $this->request->getData('company_name')) {
            $company = explode('[', $this->request->getData('company_name'));
            $company = explode(']', $company[1]);
            $company_id = $company[0];
            $employment = FactoryLocator::get('Table')->get('CompaniesUsers')->find();
            $employment->insert(['company_id', 'user_id', 'is_company_admin'])
                ->values([
                    'company_id' => $company_id,
                    'user_id' => $currentUser->id,
                    'is_company_admin' => 0
                ])->execute();
            $this->Flash->success(__('Registered with selected company.'));
            return $this->redirect(['controller' => 'projects', 'action' => 'index']);
            }
            //$this->Flash->error(__('The company could not be saved. Please, try again.'));
        $companies = FactoryLocator::get('Table')->get('Companies')->find();
        $this->set(compact('company', 'companies'));
    }

    public function pending($id = null)
    {
        $company = $this->Companies->get($id, [
            'contain' => ['Projects', 'Users'],
        ]);
        $this->Authorization->authorize($company);

        if ($this->request->is("get") && $this->request->getQuery('rejectUser')){
            $deleteUser = $this->request->getQuery('rejectUser');
            $employee = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->where([
                'company_id' => $id,
                'user_id' => $deleteUser
            ])->first();
            if (FactoryLocator::get('Table')->get('CompaniesUsers')->delete($employee)){
                $this->Flash->success(__('Employee rejected from your company.'));
            } else {
                $this->Flash->error(__('The employee could not be rejected. Please, try again.'));
            }
            return $this->redirect(['action' => 'pending', $id]);
        } else if ($this->request->is("get") && $this->request->getQuery('acceptUser')){
            $acceptUser = $this->request->getQuery('acceptUser');
            $employee = FactoryLocator::get('Table')->get('CompaniesUsers')->find()->where([
                'company_id' => $id,
                'user_id' => $acceptUser
            ])->first();
            $employee->confirmed = 1;
            if (FactoryLocator::get('Table')->get('CompaniesUsers')->save($employee)){
                $this->Flash->success(__('Employee added to your company.'));
            } else {
                $this->Flash->error(__('The employee could not be added. Please, try again.'));
            }
            return $this->redirect(['action' => 'pending', $id]);
        } else if ($this->request->is("get") && $this->request->getQuery('inductUser')){
            $inductUser = $this->request->getQuery('inductUser');
            FactoryLocator::get('Table')->get('CompaniesUsers')->query()->update()
                ->set(['inducted' => date('Y-m-d H:i:s')])
                ->where(['company_id' => $id, 'user_id' => $inductUser])
                ->execute();
            $this->Flash->success(__('Employee inducted into your company.'));
            //return $this->redirect(['action' => 'pending', $id]);
        }

        $unconfirmedIds = FactoryLocator::get('Table')->get('CompaniesUsers')->find();
        $unconfirmedIds->select(['user_id'])->where([
            'company_id' => $id,
            'confirmed' => 0
        ]);
        $employees = FactoryLocator::get('Table')->get('Users')->find()->where([
            'id IN' => $unconfirmedIds,
        ]);

        $uninductedIds = FactoryLocator::get('Table')->get('CompaniesUsers')->find();
        $uninductedIds->select(['user_id'])->where([
            'company_id' => $id,
            'confirmed' => 1,
            'inducted is' => NULL
        ]);
        $uninducted = FactoryLocator::get('Table')->get('Users')->find()->where([
            'id IN' => $uninductedIds,
        ]);

        $this->set(compact('company', 'employees', 'uninducted'));
    }

    public function addworker($id = null)
    {
        $currentUser = $this->request->getAttribute('identity');
        $company = $this->Companies->get($id, [
            'contain' => ['Projects', 'Users'],
        ]);
        $this->Authorization->skipAuthorization();

        $company = TableRegistry::getTableLocator()->get('Companies')->find()->where(['id' => $id])->first();

        // Check if the current user is an admin
        if ($currentUser->role != 'Admin') {
            if ($currentUser->id != $company->admin_id) {
                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'index']);
            }
        }  {
            // Get the list of workers not associated with the company
            $companiesUsersTable = TableRegistry::getTableLocator()->get('CompaniesUsers');

            // Get the list of workers not associated with the company
            $associatedUserIds = $companiesUsersTable
                ->find()
                ->where(['company_id' => $id])
                ->extract('user_id')
                ->toArray();

            $availableWorkers = $this->Companies->Users->find()
                ->where(['Users.id NOT IN' => $associatedUserIds])
                ->combine('id', function ($user) {
                    return $user->first_name . ' ' . $user->last_name;
                })
                ->toArray();

            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $newCompaniesUser = $companiesUsersTable->newEntity([
                    'company_id' => $id,
                    'user_id' => $data['worker_id'],
                ]);
                if ($companiesUsersTable->save($newCompaniesUser)) {
                    $this->Flash->success(__('Worker added successfully.'));
                    return $this->redirect(['controller' => 'Companies', 'action' => 'view', $id]);
                } else {
                    $this->Flash->error(__('Failed to add worker. Please try again.'));
                }
            }

            $this->set(compact('availableWorkers', 'company'));
        }
    }

    public function generatepasscode ($id){
        $company = $this->Companies->get($id);
        $this->Authorization->skipauthorization();
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString= '';
        $characterCount = strlen($characters);
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $characterCount - 1)];
        }
        $existingPasscode = $this->Companies->find('list', [
            'keyField' => 'id',
            'valueField' => 'passcode'
        ]);
        $existingPasscode = $existingPasscode->toArray();
        while (in_array($randomString,$existingPasscode)){
            $randomString= '';
            for ($i = 0; $i < 10; $i++) {
                $randomString .= $characters[rand(0, $characterCount - 1)];
            }
        }
        $company->passcode = $randomString;
        if ($this->Companies->save($company)) {
            $this->Flash->success(__('The passcode of this company has been regenerated'));
            return $this->redirect(['action' => 'view', $id]);
        } else {
            $this->Flash->error(__('The passcode of this company could not be regenerate. Please, try again.'));
        }
    }




}
