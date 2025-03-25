<?php
declare(strict_types=1);


namespace App\Controller;
use Cake\Datasource\FactoryLocator;
use Cake\Mailer\Mailer;
use Cake\I18n\FrozenTime;
use Cake\ORM\Locator\TableLocator;

/**
 * Inductions Controller
 *
 * @property \App\Model\Table\InductionsTable $Inductions
 * @method \App\Model\Entity\Induction[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InductionsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $induction = $this->Inductions->newEmptyEntity();
        $this->Authorization->authorize($induction);

        $this->paginate = [
            'contain' => ['Projects', 'Users'],
        ];
        $inductions = $this->paginate($this->Inductions);

        $this->set(compact('inductions'));
    }

    /**
     * View method
     *
     * @param string|null $id Induction id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $induction = $this->Inductions->get($id, [
            'contain' => ['Projects', 'Users'],
        ]);
        return $this->redirect(['controller' => 'projects', 'action' => 'index']);
        $this->Authorization->authorize($induction);

        $this->set(compact('induction'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $induction = $this->Inductions->newEmptyEntity();
        $this->Authorization->authorize($induction);
        $currentUser = $this->request->getAttribute('identity');

        $projectid = $this->request->getQuery('project');
        $company = FactoryLocator::get('Table')->get('Companies')->find()->where(['admin_id' => $currentUser->id])->first();

        if ($this->request->is('post')) {
            $induction = $this->Inductions->patchEntity($induction, $this->request->getData());
            $project_id = $this->request->getData('Project');
            if ($this->request->getData('Worker')){
                $user_id = $this->request->getData('Worker');
                $induction->project_id = $project_id;
                $induction->user_id = $user_id;
                $induction->company_id = $company->id;

                if ($this->Inductions->save($induction)) {
                    $inductee = FactoryLocator::get('Table')->get('Users')->get($user_id);
                    $project = FactoryLocator::get('Table')->get('Projects')->get($project_id);

                    $inductionDocuments = FactoryLocator::get('Table')->get('Documents')->find()->where([
                        'related_project_id' => $project_id,
                        'requires_signature' => TRUE]);

                    if ($inductionDocuments->count() != 0){
                        $signaturesTable = FactoryLocator::get('Table')->get('Signatures')->find();
                        foreach ($inductionDocuments as $inductionDocument){
                            $currentDocument = $signaturesTable->where([
                                'document_id' => $inductionDocument->id,
                                'user_id' => $user_id
                            ]);
                            if ($currentDocument->count() == 0){
                                $addSignature = $signaturesTable->insert(['document_id', 'user_id'])->values([
                                    'document_id' => $inductionDocument->id,
                                    'user_id' => $user_id
                                ]);
                            }
                        }
                        $addSignature->execute();
                    }

                    $mailer = new Mailer('mailgun');
                    $mailer
                        ->setEmailFormat('html')
                        ->setFrom(['sitex@cosmicproperty.com.au' => 'SiteX'])
                        ->setTo($inductee->email)
                        ->setSubject('SiteX: You have been assigned a project.')
                        ->viewBuilder()
                        ->setTemplate('assignedtoproject');

                    $mailer ->setViewVars([
                        'id' => $inductee->id,
                        'email' => $this->request->getData('email'),
                        'name' => $inductee->first_name,
                        'project' => $project->name,
                        'assigner' => $currentUser->first_name.' '.$currentUser->last_name
                    ]);

                    // Deliver mail
                    if ($mailer->deliver()){
                        //$this->Flash->success(__('Assignment email sent.'));
                    } else {
                        $this->Flash->error(__('Failed to send assignment email.'));
                    }
                    $this->Flash->success(__('Worker assigned successfully.'));
                    return $this->redirect(['controller' => 'projects', 'action' => 'index']);
                }
                $this->Flash->error(__('Worker could not be added. Please try again.'));
            } elseif ($this->request->getData('Contractor')){
                $contractor = $this->request->getData('Contractor');
                $assignment = FactoryLocator::get('Table')->get('CompaniesProjects')->find();
                $assignment->insert(['company_id', 'project_id',])
                    ->values([
                        'company_id' => $contractor,
                        'project_id' => $project_id
                    ])->execute();
                $this->Flash->success(__('Contractor assigned successfully.'));
                return $this->redirect(['controller' => 'projects', 'action' => 'index']);
            }
        }
        $projects = $this->Inductions->Projects->find('list', ['limit' => 200])->all();

        $projectsTable = FactoryLocator::get('Table')->get('Projects');
        $project = $projectsTable->get($projectid);

        $unconfirmedIds = FactoryLocator::get('Table')->get('CompaniesUsers')->find();
        $unconfirmedIds->select(['user_id'])->where([
            'company_id' => $company->id,
            'confirmed' => 0
        ]);
        $uninductedIds = FactoryLocator::get('Table')->get('CompaniesUsers')->find();
        $uninductedIds->select(['user_id'])->where([
            'company_id' => $company->id,
            'inducted is' => NULL
        ]);

        $usersAlreadyAdded = $this->Inductions->find()->select(['user_id'])->where(['project_id' => $projectid]);
        $users = $this->Inductions->Users->find()->select(['id', 'first_name', 'last_name'])->where([
            'cu.company_id' => $company->id,
            'role' => 'On-site Worker',
            'Users.id NOT IN' => $usersAlreadyAdded,
        ])->join([
            "table" => "companies_users cu",
            "type" => "LEFT",
            "conditions" => "Users.id = cu.user_id"
        ]);
        $users->where([
            'Users.id NOT IN' => $unconfirmedIds,
            'Users.id NOT IN' => $uninductedIds,
        ]);

        $contractorsAlreadyAdded = FactoryLocator::get('Table')->get('CompaniesProjects')->find()->select('company_id')->where([
            'project_id' => $projectid
        ]);

        $contractors = FactoryLocator::get('Table')->get('Companies')->find()->where([
            'company_type' => 'Contractor',
            'id NOT IN' => $contractorsAlreadyAdded
        ]);


        $this->set(compact('induction', 'projects', 'project', 'users', 'contractors'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Induction id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $induction = $this->Inductions->get($id, [
            'contain' => [],
        ]);
        return $this->redirect(['controller' => 'projects', 'action' => 'index']);
        $this->Authorization->authorize($induction);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $induction = $this->Inductions->patchEntity($induction, $this->request->getData());
            if ($this->Inductions->save($induction)) {
                $this->Flash->success(__('Induction complete.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Could not finalize induction. Please try again.'));
        }
        $projects = $this->Inductions->Projects->find('list', ['limit' => 200])->all();
        $users = $this->Inductions->Users->find('list', ['limit' => 200])->all();
        $this->set(compact('induction', 'projects', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Induction id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
  /*  public function delete($id = null)
    {
        return $this->redirect(['controller' => 'projects', 'action' => 'index']);
        $this->request->allowMethod(['post', 'delete']);
        $induction = $this->Inductions->get($id);
        $this->Authorization->authorize($induction);
        if ($this->Inductions->delete($induction)) {
            $inductionDocuments = FactoryLocator::get('Table')->get('Documents')->find()->where([
                'related_project_id' => $induction->project_id
            ]);

            if ($inductionDocuments->count() != 0){
                $signaturesTable = FactoryLocator::get('Table')->get('Signatures');
                foreach ($inductionDocuments as $inductionDocument){
                    $removeSignature = $signaturesTable->deleteAll([
                        'document_id' => $inductionDocument->id,
                        'user_id' => $induction->user_id
                    ]);
                }
            }
            $this->Flash->success('Worker successfully removed from project.');
        } else {
            $this->Flash->error(__('Worker could not be removed. Please, try again.'));
        }

        return $this->redirect(['controller' => 'projects', 'action' => 'index']);
    }*/

    public function selectcompany($projectID)
    {
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $companyID = Null;
        $noCompany = 0;


        $existingrecord = $this->Inductions->find()
            ->where([
                'project_id'=>$projectID,
                'user_id'=>$currentUser->id
            ])->first();
        if($existingrecord){
            $this->Flash->error(__('You have joined this project and completed the project induction process'));
            return $this->redirect(['controller' => 'Projects', 'action' => 'view',$projectID]);
        }
        $this->set('companyID', $companyID);
        if ($currentUser->role == 'On-site Worker') {
            $projectCompanyList = $this->fetchTable('CompaniesProjects')->find()
                ->select([
                    'company_id' => 'company_id',
                    'project_id' => 'project_id',
                    'company_name' => 'name',

                ])->join([
                    "table" => "companies",
                    "type" => "LEFT",
                    "conditions" => "CompaniesProjects.company_id = companies.id"
                ])->where([
                    'project_id' => $projectID
                ])->distinct()->toArray();


            $companyNames = array_column($projectCompanyList, 'company_name');

            $this->set('companyNames', $companyNames);

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                $worker_company_index = $data['company_name'];

                $worker_company_data = $projectCompanyList[$worker_company_index];

                $worker_company_id = $worker_company_data->company_id;


                $worker_joined_company =$this->fetchTable('CompaniesUsers')->find()
                    ->where([
                        'company_id' => $worker_company_id,
                        'user_id' => $currentUser->id
                    ])->first();

                    $companydoc = $this->fetchTable('Documents')->find()
                        ->select([
                            'document_id' =>'Documents.id',
                            'document_name' => 'Documents.name',
                            'document_type' => 'Documents.document_type',
                            'status1'=> 'Documents.status',
                            'status2'=> 'projects_documents.status',
                            'project_id'=> 'projects_documents.project_id',
                            'company_id'=> 'projects_documents.company_id',
                            'user_id' =>'projects_documents.user_id'
                        ])
                        ->join([
                        "table" => "projects_documents",
                        "type" => "LEFT",
                        "conditions" => "Documents.id = projects_documents.document_id"])
                        ->where([
                            'projects_documents.project_id' => $projectID,
                            'projects_documents.company_id' => $worker_company_id
                        ])->first();


                if (empty($worker_joined_company)){
                  $noCompany = 1;
                }
                    if($companydoc == null){
                        $this->Flash->error(__('There are no any safety document associated with this company, please contact the company admin for further action.'));
                    }else{
                        if($companydoc->status1 == 'Approved'|| $companydoc->status1 == 'Reviewed' || $companydoc->status2 == 'Approved' || $companydoc->status2 == 'Reviewed'){
                            return $this->redirect(['controller' => 'Inductions', 'action' => 'companysafetydocument',$projectID,$worker_company_id, $noCompany]);
                        }else{
                            $this->Flash->error(__('The company safety document is not approved by builder. Please contact company admin for further action'));
                        }
                    }
            }
        } else {
            $this->Flash->error(__('You do not have the permission to view this page'));
            return $this->redirect($this->referer());
        }
    }
    public function checkcompany($projectID,$companyID){
        $this->Authorization->skipAuthorization();
        $noCompany = 2;
        $companydoc = $this->fetchTable('Documents')->find()
            ->select([
                'document_id' =>'Documents.id',
                'document_name' => 'Documents.name',
                'document_type' => 'Documents.document_type',
                'status1'=> 'Documents.status',
                'status2'=> 'projects_documents.status',
                'project_id'=> 'projects_documents.project_id',
                'company_id'=> 'projects_documents.company_id',
                'user_id' =>'projects_documents.user_id'
            ])
            ->join([
                "table" => "projects_documents",
                "type" => "LEFT",
                "conditions" => "Documents.id = projects_documents.document_id"])
            ->where([
                'projects_documents.project_id' => $projectID,
                'projects_documents.company_id' => $companyID
            ])->first();

        if($companydoc == null){
            $this->Flash->error(__('There are no any safety document associated with this company, please contact the company admin for further action.'));
            return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
        }else{
            if($companydoc->status1 == 'Approved'|| $companydoc->status1 == 'Reviewed' || $companydoc->status2 == 'Approved' || $companydoc->status2 == 'Reviewed'){
                return $this->redirect(['controller' => 'Inductions', 'action' => 'companysafetydocument',$projectID,$companyID, $noCompany]);
            }else{
                $this->Flash->error(__('The company safety document is not approved by builder. Please contact company admin for further action'));
                return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
            }
        }


    }
    public function companysafetydocument($projectID,$companyID,$noCompany){
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');

        $company =$this->fetchTable('Companies')->find()
            ->where(['id'=> $companyID])->first();
        $documents = $this->fetchTable('Documents')->find()
            ->select([
                'document_id' => 'Documents.id',
                'document_name' => 'Documents.name',
                'document_type' => 'Documents.document_type',
                'status1' => 'Documents.status',
                'status2' => 'projects_documents.status',
                'project_id' => 'projects_documents.project_id',
                'company_id' => 'projects_documents.company_id',
                'user_id' => 'projects_documents.user_id',
                'extension' => 'Documents.extension',
                'auth_value' => 'projects_documents.auth_value'
            ])
            ->join([
                "table" => "projects_documents",
                "type" => "LEFT",
                "conditions" => "Documents.id = projects_documents.document_id"
            ])
            ->where([
                'AND' => [
                    'projects_documents.company_id' => $companyID,
                    'projects_documents.project_id' => $projectID,
                    'OR' => [
                        'projects_documents.status' => 'Reviewed',
                        'Documents.status' => 'Approved'
                    ]
                ],
                'NOT' => [
                    'projects_documents.auth_value' => 'Worker Acknowledgement',

                ]
            ])
            ->all();


        $number = count($documents->toArray());

        $this->set('documents', $documents);
        $this->set('company', $company);
        $this->set('number', $number);
        $this->set('projectID', $projectID);
        if ($this->request->is('post')) {
            foreach ($documents as $document) {
                $projectdoc = $this->loadModel('ProjectsDocuments');
                $userAgreement = $projectdoc->newEmptyEntity();
                $userAgreement->project_id = $projectID;
                $userAgreement->document_id = $document->document_id;
                $userAgreement->company_id = $companyID;
                $userAgreement->user_id = $currentUser->id;
                $userAgreement->auth_type = 1;
                $userAgreement->auth_value = 'Worker Acknowledgement';
                $userAgreement->status = 'Reviewed';
                if (!$this->fetchTable('ProjectsDocuments')->save($userAgreement)) {
                    $this->Flash->error(__('Could not proceed to the next step. Please, try again.'));
                }
                $documents = $this->fetchTable('Documents')->find()
                    ->select([
                        'document_id' => 'Documents.id',
                        'document_name' => 'Documents.name',
                        'document_type' => 'Documents.document_type',
                        'status1' => 'Documents.status',
                        'status2' => 'projects_documents.status',
                        'project_id' => 'projects_documents.project_id',
                        'company_id' => 'projects_documents.company_id',
                        'user_id' => 'projects_documents.user_id',
                        'extension' => 'Documents.extension'
                    ])
                    ->join([
                        "table" => "projects_documents",
                        "type" => "LEFT",
                        "conditions" => "Documents.id = projects_documents.document_id"])
                    ->where([
                        'AND' => [
                            'projects_documents.project_id' => $projectID,
                            'projects_documents.auth_value' => 'Induction Document',
                            'projects_documents.status' => 'Pending',
                            'projects_documents.auth_value !=' => 'Worker Acknowledgement'


                        ]
                    ])->first();


            }
            if (!$documents) {
                $this->Flash->error(__('There is no induction document associated with this project, please contact the builder for further action.'));
            } else {
                return $this->redirect(['controller' => 'Inductions', 'action' => 'inductionform', $projectID, $companyID, $noCompany]);
            }
        }
    }

    public function inductionform ($projectID,$companyID,$noCompany)
    {
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');

        $documents = $this->fetchTable('Documents')->find()
            ->select([
                'document_id' =>'Documents.id',
                'document_name' => 'Documents.name',
                'document_type' => 'Documents.document_type',
                'status1'=> 'Documents.status',
                'status2'=> 'projects_documents.status',
                'project_id'=> 'projects_documents.project_id',
                'company_id'=> 'projects_documents.company_id',
                'user_id' =>'projects_documents.user_id',
                'extension'=>'Documents.extension'
            ])
            ->join([
                "table" => "projects_documents",
                "type" => "LEFT",
                "conditions" => "Documents.id = projects_documents.document_id"])
            ->where([
                'AND'=>[
                    'projects_documents.project_id' => $projectID,
                    'projects_documents.auth_value' => 'Induction Document',
                    'projects_documents.status' =>'Pending',
                    'projects_documents.auth_value !=' => 'Worker Acknowledgement'


                ]
            ])->distinct();

        $number = count($documents->toArray());

        if ($documents) {
            $this->set('documents', $documents);
            $this->set('number',$number);
            $project = $this->fetchTable('Projects')->find()
                ->where(['id' => $projectID,
                ])
                ->first();
            $this->set('project', $project);
            $this->set('projectID', $projectID);
            if ($this->request->is('post')) {
                foreach($documents as $document) {
                    $projectdoc = $this->loadModel('ProjectsDocuments');
                    $userAgreement = $projectdoc->newEmptyEntity();
                    $userAgreement->project_id = $projectID;
                    $userAgreement->document_id = $document->document_id;
                    $userAgreement->user_id = $currentUser->id;
                    $userAgreement->auth_type = 1;
                    $userAgreement->auth_value = 'Worker Acknowledgement';
                    $userAgreement->status = 'Reviewed';

                    if (!$this->fetchTable('ProjectsDocuments')->save($userAgreement)){
                        $this->Flash->error(__('Could not join the project. Please, try again.'));
                    }
                }
                if($noCompany==2){
                    $projectuser = $this->loadModel('ProjectsUsers');
                    $user = $this->fetchTable('ProjectsUsers')
                        ->find()
                        ->where([
                            'project_id'=>$projectID,
                            'user_id'=>$currentUser->id,
                            'status' => 'Engaged',
                            'company_id'=> $companyID,
                        ])->first();
                    $user->inducted_date = FrozenTime::now();
                    if ($projectuser->save($user)) {
                        $this->Flash->success(__('You have completed the project induction.'));
                        return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
                    } else{
                        $this->Flash->error(__('Could not complete the induction process. Please, try again.'));
                    }
                }

                    $projectuser = $this->loadModel('ProjectsUsers');
                    $worker = $projectuser->newEmptyEntity();
                    $worker->project_id = $projectID;
                    $worker->user_id = $currentUser->id;
                    $worker->status = 'Engaged';
                    $worker->company_id = $companyID;
                    $worker->inducted_date = FrozenTime::now();
                    if ($projectuser->save($worker)) {
                        $Induction = $this->Inductions;
                        $inducted = $Induction->newEmptyEntity();
                        $inducted->project_id = $projectID;
                        $inducted->user_id = $currentUser->id;
                        $inducted->company_id = $companyID;
                        $inducted->inducted_date = FrozenTime::now();
                        if ($Induction->save($inducted)) {
                            if($noCompany==1) {
                                $company = $this->loadModel('CompaniesUsers');
                                $workercompany = $company->newEmptyEntity();
                                $workercompany->company_id = $companyID;
                                $workercompany->user_id = $currentUser->id;
                                $workercompany->is_company_admin = 0;
                                $workercompany->confirmed = 1;
                                $workercompany->status = 'Engaged';
                                $company->save($workercompany);
                            }
                            $this->Flash->success(__('You joined the project and completed the project induction.'));
                            return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
                        } else {
                            $this->Flash->error(__('Could not join the project. Please, try again.'));
                        }
                    } else {
                        $this->Flash->error(__('Could not join the project. Please, try again.'));
                    }
                }
        }else{
            $this->Flash->error(__('There is no induction document associated with this project, please contact the builder for further action.'));
        }
        }

}
