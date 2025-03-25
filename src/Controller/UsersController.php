<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\FactoryLocator;
use Cake\I18n\FrozenTime;
use Cake\Mailer\TransportFactory;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Cake\Mailer\Mailer;
use Cake\Filesystem\File;
use Cake\Routing\Router;

define('PRIVATE_CODE', 'shining_glass');
/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
        $this->Authentication->addUnauthenticatedActions(['login', 'signup', 'forgot', 'requestpassword', 'verify']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Skip authorization checks for the entire action
        $this->Authorization->skipAuthorization();

        // Check if the current user's role is admin
        $currentUser = $this->Authentication->getIdentity();
        if ($currentUser && $currentUser->role !== 'Admin') {
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['controller' => 'Projects', 'action' => 'index']); // Redirect to an appropriate action
        }

        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }


    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {

        $user = $this->Users->get($id, [
            'contain' => ['Companies', 'Signatures', 'Checkins', 'Inductions'],
        ]);

        $currentUser = $this->request->getAttribute('identity');
        $this->Authorization->skipAuthorization();

        $companyIds = $this->fetchTable('CompaniesUsers')->find()
            ->select(['company_id'])
            ->where(['user_id' => $id])
            ->extract('company_id') // Extract the company IDs from the records
            ->toArray();

        $projectIds = $this->fetchTable('ProjectsUsers')->find()
            ->select(['project_id'])
            ->where(['user_id' => $id])
            ->extract('project_id') // Extract the project IDs from the records
            ->toArray();

        $companyOwnerIds = [];
        if (!empty($companyIds)) {
            $companyOwnerIds = $this->fetchTable('Companies')->find()
                ->select(['admin_id'])
                ->whereInList('id', $companyIds)
                ->extract('admin_id')
                ->toArray();
        }

        $projectBuilderIds = [];
        if (!empty($projectIds)) {
            $projectBuilderIds = $this->fetchTable('Projects')->find()
                ->select(['builder_id'])
                ->whereInList('id', $projectIds)
                ->extract('builder_id')
                ->toArray();
        }
        $builderUserIds = [];
        $associatedAdminIds = [];
        if ($this->Users->get($id)->role == 'Builder') {
            $projectIds2 = $this->fetchTable('Projects')->find()
                ->select(['id'])
                ->where(['builder_id' => $id])
                ->extract('id')
                ->toArray();


            if (!empty($projectIds2)) {
                $builderUserIds = $this->fetchTable('ProjectsUsers')->find()
                    ->select(['user_id'])
                    ->whereInList('project_id', $projectIds2)
                    ->extract('user_id')
                    ->toArray();

                $associatedAdminIds = $this->fetchTable('CompaniesProjects')->find()
                    ->select(['admin_id'=>'companies.admin_id'])
                    ->join([
                        "table" => "companies",
                        "type" => "LEFT",
                        "conditions" => "CompaniesProjects.company_id = companies.id"])
                    ->whereInList('project_id', $projectIds2)
                    ->extract('admin_id')
                    ->toArray();


            }
        }






        $allowedIds = array_merge($companyOwnerIds, $projectBuilderIds, $builderUserIds, $associatedAdminIds);

        $referer = $this->referer();

        if ($currentUser->role != 'Admin' && $currentUser->id != $id && !in_array($currentUser->id, $allowedIds) && $referer != '/requests' && $referer != '/requests/companyrequestindex' && $referer != '/requests/invitation' && $referer != '/requests/company-invitation' && $referer != '/requests/builder-project-invitation') {
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
        }


        $documents = FactoryLocator::get('Table')->get('Documents')->find()->where([
            'related_user_id' => $id,
        ]);

        $company_user = $this->fetchTable('CompaniesUsers')->find()->select('company_id')->where(['user_id' => $currentUser->id]);
        $assignedCompanies = $this->fetchTable('companies')->find();
        $ownedCompanies = $this->fetchTable('companies')->find()
            ->where(['admin_id' => $user->id])
            ->count();
        $assignedCompanies = $assignedCompanies->select([
            'id' => 'companies.id', 'name' => 'companies.name', 'user_id' => 'companies_users.user_id',
            'company_type' => 'companies.company_type', 'abn' => 'companies.abn', 'address_no' => 'companies.address_no', 'address_street' => 'companies.address_street', 'address_suburb' => 'companies.address_suburb',
            'address_state' => 'companies.address_state', 'address_postcode' => 'companies.address_postcode', 'contact_name' => 'companies.contact_name', 'contact_email' => 'companies.contact_email', 'contact_phone' => 'companies.contact_phone'
        ])
            ->join([
                "table" => "companies_users",
                "type" => "LEFT",
                "conditions" => "companies.id = companies_users.company_id"
            ])->where([
                'companies.id IN' => $company_user,
                'companies_users.user_id' => $currentUser->id
            ]);

        if ($currentUser->role == 'Builder' || $currentUser->role == 'Admin') {
            $assignedProjects = $this->fetchTable('Projects')->find()->where(['builder_id' => $currentUser->id]);
        } elseif ($currentUser->role != 'Admin' && $currentUser->role != 'Builder') {
            $project_user = $this->fetchTable('ProjectsUsers')->find()->select('project_id')->where(['user_id' => $currentUser->id]);
            $assignedProjects = $this->fetchTable('Projects')->find();
            $assignedProjects->select([
                'id' => 'Projects.id', 'name' => 'Projects.name', 'project_type' => 'Projects.project_type', 'builder_id' => 'Projects.builder_id',
                'address_no' => 'Projects.address_no', 'address_street' => 'Projects.address_street', 'address_suburb' => 'Projects.address_suburb',
                'address_postcode' => 'Projects.address_postcode', 'address_state' => 'Projects.address_state', 'start_date' => 'Projects.start_date',
                'status' => 'Projects.status', 'project_id' => 'Projects.id', 'builder_fname' => 'users.first_name', 'builder_lname' => 'users.last_name', 'user_status' => 'users.status'
            ])
                ->join([
                    "table" => "users",
                    "type" => "LEFT",
                    "conditions" => "builder_id = users.id"
                ])->where([
                    'Projects.id IN' => $project_user
                ]);
        }


        $requests = $this->fetchTable('Requests')->find()->where(['user_id' => $currentUser->id, 'approved_at IS NULL']);

        $builderRequests = $this->fetchTable('Requests')->find()->where(['user_id' => $currentUser->id, 'request_type' => 'Builder', 'removal_status' => 0])->first();

        //get project id through url
        $pj_id = $this->request->getQuery('pj_id');

        $project_documents = [];
        //get personal document of worker
        if (!empty($pj_id)) {
            $projectDocs = $this->fetchTable('ProjectsDocuments')->find()
                ->where(['user_id  ' => $id, 'project_id' => $pj_id, 'auth_value !='=>'Worker Acknowledgement'])
                ->toArray();

            // get document ids
            $docIds = array_column($projectDocs, 'document_id');

            if ($docIds) {
                //get all documents related to this project
                $user_project_doc = $this->fetchTable('Documents')->find()
                    ->where(['id IN' => $docIds, 'archived' => 0])
                    ->toArray();

                // reorder project docs so that it has an document id where we can match them in the view file
                foreach ($user_project_doc as $user_project_document){
                    foreach ($projectDocs as $projectDoc){
                        if ($projectDoc->document_id == $user_project_document->id){
                            $project_documents[$user_project_document->id] = $projectDoc;
                        }
                    }
                }
                $this->set(compact('user_project_doc', 'project_documents', 'projectDocs'));
            }
        }

        $this->set(compact('user', 'ownedCompanies', 'assignedProjects', 'documents', 'assignedCompanies', 'requests', 'builderRequests'));

        // if ($pj_id) {
        //     //get company name the user is working for in a project
        //     $projectsUsersTable = TableRegistry::getTableLocator()->get('ProjectsUsers');
        //     $query = $projectsUsersTable
        //         ->find()
        //         ->select([
        //             'company_name' => 'Companies.name',
        //             'company_id' => 'Companies.id',
        //         ])
        //         ->innerJoinWith('Users.Companies')
        //         ->where([
        //             'ProjectsUsers.project_id' => $pj_id,
        //             'Users.id' => $id,
        //         ]);

        //     $userCompanyName = $query->toArray();
        //     $this->set('companyName', $userCompanyName);
        // }

        $projectsUsersTable = TableRegistry::getTableLocator()->get('ProjectsUsers');
        $query = $projectsUsersTable
            ->find()
            ->select([
                'company_name' => 'Companies.name',
                'company_id' => 'Companies.id',
            ])
            ->innerJoinWith('Users.Companies')
            ->where([
                'Users.id' => $id,
            ]);

        if (isset($pj_id)) {
            // Only add the project_id condition when pj_id is set
            $query->where(['ProjectsUsers.project_id' => $pj_id]);
            $userCompanyName = $query->toArray();
            $this->set('companyName', $userCompanyName);
        } else {
            // If $pj_id is not set, return an empty array
            $this->set('companyName', []);
        }
    }

    /**
     * Builder Request Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function builderrequestsindex()
    {

        $currentUser = $this->request->getAttribute('identity');
        $this->Authorization->skipAuthorization();



        if ($currentUser->role == "Admin") {
            $activeRequests = $this->Users->find()
                ->select(['id', 'first_name', 'last_name', 'phone_mobile', 'phone_office', 'email'])
                ->where(['status' => 'Pending', 'role' => 'Builder']);




            $this->set('requests', $activeRequests);


            $requests = $this->paginate($activeRequests);
            $this->set(compact('requests'));
        }
    }



    // ...

    /**
     * Ignore Builder Application
     *
     * @param $id
     * @return \Cake\Http\Response|null
     */
    public function ignore($id = null)
    {
        $this->Authorization->skipAuthorization();
        $query = $this->Users->query();
        $query->update()
            ->set(['status' => 'Deactivated'])
            ->where(['id' => $id]);

        if ($query->execute()) {
            $this->Flash->success(__('Applicant status has been set to "Deactivated".'));
        } else {
            $this->Flash->error(__('The user could not be updated. Please, try again.'));
        }


        return $this->redirect(['controller' => 'Users', 'action' => 'builderrequestsindex']);
    }

    /**
     * Approve request method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function approveRequest($id = null)
    {
        $this->Authorization->skipAuthorization();
        $query = $this->Users->query();
        $query->update()
            ->set(['status' => 'Verified'])
            ->where(['id' => $id]);

        if ($query->execute()) {
            $this->Flash->success(__('Applicant status has been set to "Verified".'));
        } else {
            $this->Flash->error(__('The user could not be updated. Please, try again.'));
        }

        return $this->redirect(['controller' => 'Users', 'action' => 'builderrequestsindex']);
    }








    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id);
        $this->Authorization->authorize($user);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());

            $attachment =  $this->request->getData('img');
            if ($attachment && $attachment->getSize() > 0) {
                $imagename = $attachment->getClientFilename();
                if ($attachment->getError()) {
                    $this->Flash->error($attachment->getError());
                }
                $dir = WWW_ROOT . 'uploads' . DS . 'userimage';
                $targetPath = $dir . DS . $imagename;
                if (!file_exists($dir)) {
                    if (mkdir($dir)) {
                        $this->Flash->error(__('The' . $dir . ' dir does not exist. Please, try again.'));
                    }
                }
                $newPath = '/uploads/userimage/' . $imagename;
                $attachment->moveTo($targetPath);
                $user->image = $newPath;
            }

            if ($this->Users->save($user)) {
                $this->Flash->success(__('Changes saved.'));
                return $this->redirect(['action' => 'logout']);
            }
            $this->Flash->error(__('The changes could not be saved. Please, try again.'));
        }

        $state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];

        $chosenState = $user->address_state;

        $this->set(compact('user', 'state', 'chosenState'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        /*
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
        */
    }

    public function login()
    {
        $this->Authorization->skipAuthorization();
        $redirect = FALSE;
        $redirectstatus = false;
        if ($this->request->getQuery('redirect')) {
            $redirect = $this->request->getQuery('redirect');
        }
        $this->set(compact('redirect'));

        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();

        if ($result && $result->isValid()) {
            if ($redirect) {
                $currentUser = $this->request->getAttribute('identity');
                if ($currentUser->role == "Admin") {
                    return $this->redirect(['controller' => 'users', 'action' => 'dashboard']);
                } else return $this->redirect($redirect);
            } else {
                $currentUser = $this->request->getAttribute('identity');
                if ($currentUser->role == "Visitor" || $currentUser->role == "Client" || $currentUser->role == "Consultant") {
                    return $this->redirect(['controller' => 'users', 'action' => 'view', $currentUser->id]);
                }
                if ($currentUser->role == "Admin" || $currentUser->role == "Builder") {
                    return $this->redirect(['controller' => 'users', 'action' => 'dashboard']);
                } else{
                    return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
                }
            }
        }
        $currentUrl = Router::url(null, true);
        if (parse_url($currentUrl, PHP_URL_QUERY)) {
            $redirectParam = isset($_GET['redirect']) ? $_GET['redirect'] : '';
            $redirectstatus = true;
            $urlWithQuery = Router::url([
                'controller' => 'Users',  // Replace with your controller name
                'action' => 'signup',         // Replace with your action name
                '?' => ['redirect' => $redirectParam],
            ], true);

            $this->set(compact('urlWithQuery'));
        }
        $this->set(compact('redirectstatus'));

        // display error if user submitted and authentication failed
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__('Invalid username or password'));
        }
    }

    public function logout()
    {
        $this->Authorization->skipAuthorization();
        $result = $this->Authentication->getResult();
        // regardless of POST or GET, redirect if user is logged in
        if ($result && $result->isValid()) {
            $this->Authentication->logout();
            $this->Flash->success(__('Logout Successful.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
    }

    public function signup()
    {
        $this->Authorization->skipAuthorization();

        $hashing = new DefaultPasswordHasher();
        $password = $this->request->getData('password');
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {

            $user = $this->Users->patchEntity($user, $this->request->getData());
            // debug($user);
            // exit;
            $hashed_password = $hashing->hash($password);
            $user->password = $hashed_password;
            $user->status = "Verified";


            $role = $this->request->getData('role');
            // debug($this->request->getData());
            // exit;
            if ($role == "Admin" || $role == "Builder"){
                $this->Flash->error(__('Error during account creation'));
                return $this->redirect('/');
            }

            $user->role = $role;




            //debug($this->request->getData('company_name'));

            $user->role = $role;
            // debug($user);
            // exit;
            // debug($user->company_name);
            // exit;

            if ($user->role === 'Builder') {
                $user->status = 'Pending';
            }

            $attachment =  $this->request->getData('img');
            if ($attachment && $attachment->getSize() > 0) {
                $imagename = $attachment->getClientFilename();
                if ($attachment->getError()) {
                    $this->Flash->error($attachment->getError());
                }
                $dir = WWW_ROOT . 'uploads' . DS . 'userimage';
                $targetPath = $dir . DS . $imagename;
                if (!file_exists($dir)) {
                    if (mkdir($dir)) {
                        $this->Flash->error(__('The' . $dir . ' dir does not exist. Please, try again.'));
                    }
                }
                $newPath = '/uploads/userimage/' . $imagename;
                $attachment->moveTo($targetPath);
                $user->image = $newPath;
            }

            if ($this->Users->save($user)) {
                //if($this->request->getData('company_name')){
                //                $company = explode('[', $user->company_name); // $this->request->getData('company_name')
                //                $company = explode(']', $company[1]);
                //                $company_id = $company[0];
                //                $companies = FactoryLocator::get('Table')->get('CompaniesUsers')->find();
                //                $companies->insert(['company_id', 'user_id', 'confirmed', 'is_company_admin'])
                //                    ->values([
                //                        'company_id' => $company_id,
                //                        'user_id' => $user->id,
                //                        'confirmed'=>1,
                //                        'is_company_admin'=>1
                //                    ])
                //                    ->execute();
                //}

                // email verification
                // $code = md5($user->password . PRIVATE_CODE);
                // $mailer = new Mailer('default');
                // $mailer
                //     ->setEmailFormat('html')
                //     ->setFrom(['sitex@u22s1010.monash-ie.me' => 'SiteX'])
                //     ->setTo($user->email)
                //     ->setSubject('SiteX Account verification')
                //     ->viewBuilder()
                //     ->setTemplate('verification');

                // $mailer ->setViewVars([
                //     'id'=>$user->id,
                //     'email' => $this->request->getData('email'),
                //     'name' => $user->first_name,
                //     'code' => $code,
                // ]);

                // // Deliver mail
                // if ($mailer->deliver()){
                //     //$this->Flash->success(__('Verification email sent.'));
                // } else {
                //     $this->Flash->error(__('Failed to send verification email.'));
                // }



                // Check if the selected role is "Builder"
                if ($user->role === 'Builder') {
                    // Display a message for Builder
                    $this->Flash->success(__('Your account has been created. You will need to wait for account approval.'));
                    return $this->redirect(['controller' => 'projects', 'action' => 'index']);
                } else {
                    // Display a message for other roles

                    $currentUrl = Router::url(null, true);
                    if (parse_url($currentUrl, PHP_URL_QUERY)) {
                        $redirectParam = isset($_GET['redirect']) ? $_GET['redirect'] : '';

                        $urlWithQuery = Router::url([
                            'controller' => 'Users',  // Replace with your controller name
                            'action' => 'login',         // Replace with your action name
                            '?' => ['redirect' => $redirectParam],
                        ], true);

                        $this->Flash->success(__('Account successfully created. You can now log in.'));
                        return $this->redirect($urlWithQuery);

                    }
                    $this->Flash->success(__('Account successfully created. You can now log in.'));
                    return $this->redirect(['controller' => 'projects', 'action' => 'index']);
                }
            }
        }
        $companies = FactoryLocator::get('Table')->get('Companies')->find();

        $state = ['NSW', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'ACT', 'NT'];



        $this->set(compact('user', 'companies', 'state'));
    }

    public function forgot()
    {
        $this->Authorization->skipAuthorization();

        $hashing = new DefaultPasswordHasher();

        if ($this->request->is('post')) {
            $user = $this->Users->find('all', array(
                'conditions' => array('email' => $this->request->getData('email'))
            ))->first();
            if (!$user) {
                //$this->Flash->error(__('This email is not registered.'));
            } else {
                $correctCode = md5($user->password . PRIVATE_CODE);
                if ($this->request->getData('code') == $correctCode) {
                    $password = $this->request->getData('password');
                    $hashed_password = $hashing->hash($password);
                    $user->password = $hashed_password;
                    if ($this->Users->save($user)) {

                        $this->Flash->success(__('Password updated, please log in.'));

                        return $this->redirect([
                            'controller' => 'Users',
                            'action' => 'login',
                            '?' => array('result' => 'reset')
                        ]);
                    }
                    $this->Flash->error(__('The password could not be updated. Please, try again.'));
                } else {
                    $this->Flash->error(__('This link has expired. Please request a new password reset email.'));
                }
            }
        }
    }

    public function requestpassword()
    {
        $this->Authorization->skipAuthorization();

        if ($this->request->is('post')) {

            $recipient = $this->getTableLocator()->get('Users')
                ->find()
                ->where(['email =' => $this->request->getData('email')])
                ->first();
            if (!$recipient) {
                $this->Flash->error(__('This email is not registered.'));
            } else {
                $code = md5($recipient->password . PRIVATE_CODE);

                $mailer = new Mailer('default');
                $mailer
                    ->setEmailFormat('html')
                    ->setFrom(['sitex_noreply@u22s1010.monash-ie.me' => 'SiteX [No Reply]'])
                    ->setTo($recipient->email)
                    ->setSubject('SiteX password reset request')
                    ->viewBuilder()
                    ->setTemplate('resetpassword');

                $mailer->setViewVars([
                    'email' => $this->request->getData('email'),
                    'name' => $recipient->first_name,
                    'code' => $code,
                ]);

                // Deliver mail
                if ($mailer->deliver()) {
                    $this->Flash->success(__('Password reset email sent.'));
                } else {
                    $this->Flash->error(__('Failed to send password reset email.'));
                }
            }

            return $this->redirect(['action' => 'requestpassword']);
        }
    }

    public function dashboard()
    {
        $currentUser = $this->request->getAttribute('identity');
        $this->Authorization->skipAuthorization();

        if ($currentUser->role == 'Admin') {
            // Get count of Companies
            $companyCount = $this->fetchTable('Companies')->find()->count();

            // Get count of Users
            $userCount = $this->fetchTable('Users')->find()->count();

            // Get count of Projects
            $projectCount = $this->fetchTable('Projects')->find()->count();

            // Get count of active Projects
            $activeProjectCount = $this->fetchTable('Projects')->find()->where(['status' => 'Active'])->count();

            // Get count of Equipment
            $equipmentCount = $this->fetchTable('Equipment')->find()->count();

            // Get count of Checkins within the last 24 hours
            $checkinCount = $this->fetchTable('Checkins')->find()
                ->where(['checkin_datetime >=' => new \DateTime('-24 hours')])
                ->count();

            // Get Builder Requests with null approved_at
            $builderRequests = $this->fetchTable('Requests')->find()
                ->where(['request_type' => 'Builder', 'approved_at IS' => null])->count();

            // Pass data to the view
            $this->set(compact('companyCount', 'userCount', 'projectCount', 'activeProjectCount', 'equipmentCount', 'checkinCount', 'builderRequests'));
        } elseif ($currentUser->role == 'Builder') {
            $projectsTable = TableRegistry::getTableLocator()->get('Projects');
            $projectsQuery = $projectsTable->find()
                ->select(['id', 'name', 'address_no', 'address_street', 'address_suburb', 'address_state'])
                ->where(['builder_id' => $currentUser->id])
                ->orderDesc('id'); // Order projects by ID in descending order (most recent first)

            $projects = $projectsQuery->toArray();

            $projectIds = $projectsQuery->combine('id', 'name')->toArray();

            $companies = [];
            $users = [];
            $associatedProjects = [];
            $documents = [];

            if (!empty($projectIds)) {
                $companies = TableRegistry::getTableLocator()->get('Companies')->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'name'
                ])
                    ->join([
                        'table' => 'companies_projects',
                        'type' => 'INNER',
                        'conditions' => ['Companies.id = companies_projects.company_id'],
                    ])
                    ->where(['companies_projects.project_id IN' => array_keys($projectIds)])
                    ->toArray();

                $users = TableRegistry::getTableLocator()->get('Users')->find('list', [
                    'keyField' => 'id',
                    'valueField' => function ($row) {
                        return $row->first_name . ' ' . $row->last_name;
                    }
                ])
                    ->join([
                        'table' => 'projects_users',
                        'type' => 'INNER',
                        'conditions' => ['Users.id = projects_users.user_id'],
                    ])
                    ->where(['projects_users.project_id IN' => array_keys($projectIds)])
                    ->toArray();

                if (!empty($companies) && !empty($users)) {
                    $associatedProjects = $projectsTable->find()
                        ->combine('id', 'name')
                        ->toArray();
                    $documents = TableRegistry::getTableLocator()->get('Documents')->find()
                        ->where(['related_project_id IN' => array_keys($projectIds)])
                        ->orderDesc('id') // Order documents by ID in descending order (most recent first)
                        ->toArray();
                }
            }

            $this->set(compact('documents', 'projects', 'companies', 'users', 'associatedProjects', 'projectIds'));
        }

    }

    public function verify()
    {
        $this->Authorization->skipAuthorization();

        $id = $this->request->getQuery('id');
        $user = $this->Users->get($id);

        $correctCode = md5($user->password . PRIVATE_CODE);
        $code = $this->request->getQuery('code');

        if ($code == $correctCode) {
            $user->status = 'Verified';
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Your account has been verified'));
                return $this->redirect(['controller' => 'projects', 'action' => 'index']);
            }
            $this->Flash->error(__('account could not be verified'));
        }
    }
}
