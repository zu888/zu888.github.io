<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\CompaniesProject;

/**
 * ProjectsUsers Controller
 *
 * @property \App\Model\Table\ProjectsUsersTable $ProjectsUsers
 * @method \App\Model\Entity\ProjectsUser[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProjectsUsersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index($id = null)
    {
        $projectsUsers = $this->ProjectsUsers->newEmptyEntity();
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $projects = $this->fetchTable('Projects');


        $builderId = $projects->get($id)->builder_id;
        $builderCompanyId = $this->fetchTable('Companies')->find()
            ->select(['id'])
            ->where(['admin_id' => $builderId])
            ->first()->id;



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

        // Get the current user's data. This might vary depending on how your authentication is set up.

        $assignedUsers = $this->ProjectsUsers->find()->select([
            'user_id',
            'id',
            'first_name' => 'users.first_name',
            'last_name' => 'users.last_name',
            'role' => 'users.role',
            'phone_mobile' => 'users.phone_mobile',
            'project_id',
            'status',
            'company_id',
            'company_name'=>'companies.name',
            'inducted_date'=>'inducted_date'
        ])
            ->join([
                "table" => "users",
                "type" => "LEFT",
                "conditions" => "user_id = users.id"
            ])->join([
                "table" => "companies",
                "conditions" => "company_id = companies.id",
            ])
            ->where(['ProjectsUsers.project_id' => $id]);


        $this->set('projectsUsers', $assignedUsers);

        $this->paginate = [
            'contain' => ['Projects', 'Users'],
        ];
        $assignedUsers = $this->paginate($this->ProjectsUsers);
    $project = $projects->get($id);

        $this->set(compact('assignedUsers','project', 'builderCompanyId'));
    }

    /**
     * View method
     *
     * @param string|null $id Projects User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $projectsUser = $this->ProjectsUsers->get($id, [
            'contain' => ['Projects', 'Users'],
        ]);

        $this->set(compact('projectsUser'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $projectsUser = $this->ProjectsUsers->newEmptyEntity();
        if ($this->request->is('post')) {
            $projectsUser = $this->ProjectsUsers->patchEntity($projectsUser, $this->request->getData());
            if ($this->ProjectsUsers->save($projectsUser)) {
                $this->Flash->success(__('The projects user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The projects user could not be saved. Please, try again.'));
        }
        $projects = $this->ProjectsUsers->Projects->find('list', ['limit' => 200])->all();
        $users = $this->ProjectsUsers->Users->find('list', ['limit' => 200])->all();
        $this->set(compact('projectsUser', 'projects', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Projects User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $projectsUser = $this->ProjectsUsers->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $projectsUser = $this->ProjectsUsers->patchEntity($projectsUser, $this->request->getData());
            if ($this->ProjectsUsers->save($projectsUser)) {
                $this->Flash->success(__('The projects user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The projects user could not be saved. Please, try again.'));
        }
        $projects = $this->ProjectsUsers->Projects->find('list', ['limit' => 200])->all();
        $users = $this->ProjectsUsers->Users->find('list', ['limit' => 200])->all();
        $this->set(compact('projectsUser', 'projects', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Projects User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($project_user_id = null, $project_id = null)
    {
        $this->Authorization->skipAuthorization();
        $this->request->allowMethod(['post', 'delete']);
        $projectsUser = $this->ProjectsUsers->get($project_user_id);
        if ($this->ProjectsUsers->delete($projectsUser)) {
            $this->Flash->success(__('The projects user has been deleted.'));
        } else {
            $this->Flash->error(__('The projects user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index', $project_id]);
    }

    public function deleteProjectUser($project_id = null, $id = null) {
        $this->Authorization->skipAuthorization();
        $projectUser = $this->fetchTable('ProjectsUsers')->get($id);

        if ($projectUser->status == 'Engaged') {
            // Update the status to 'Disengaged' if it's 'Engaged'
            $projectUser->status = 'Disengaged';
            if ($this->fetchTable('ProjectsUsers')->save($projectUser)) {
                $this->Flash->success(__('User has been disengaged from project'));
            } else {
                $this->Flash->error(__('User could not be disengaged from Project. Please, try again.'));
            }
        } else {
            // Delete the record if it's 'Disengaged'
            if ($this->fetchTable('ProjectsUsers')->delete($projectUser)) {
                $this->Flash->success(__('User has been removed from project'));
            } else {
                $this->Flash->error(__('User could not be removed from Project. Please, try again.'));
            }
        }

        return $this->redirect(['action' => 'index', $project_id]);
    }


    public function addMember($projectID){
        $this->Authorization->skipauthorization();
        $currentUser = $this->request->getAttribute('identity');

        $projectsUser = $this->ProjectsUsers->newEmptyEntity();
        $userID = null;


        $assignedUsers = $this->fetchTable('Users')->find();

        $assignedUsers = $assignedUsers->select(['first_name'=> 'first_name',  'last_name'=> 'last_name', 'email'=>'email'])
            ->innerJoin('projects_users',[
                'Users.id = projects_users.user_id',
            ])
            ->innerJoin('projects', [
                'projects.id = projects_users.project_id',
            ])
            ->where([
                'builder_id'=>$currentUser->id
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
//            debug($selectedMemberName);
            $selectedEmail = $emailaddress[$selectedMemberName];
//            debug($selectedEmail);

            $selectedUserID = $this->fetchTable('Users')->find()
                ->select(['id'])
                ->where(['email' => $selectedEmail])
                ->first();
            if ($selectedUserID) {
                $userID = $selectedUserID['id'];
                $existingRecord = $this->ProjectsUsers->find()
                    ->where(['user_id' => $userID, 'project_id' => $projectID])
                    ->first();
                if (!$existingRecord) {
                    $projectsUserdata = [
                        'user_id' => $userID,
                        'project_id' => $projectID
                    ];
                    $this->ProjectsUsers->patchEntity($projectsUser, $projectsUserdata);
                    $this->ProjectsUsers->save($projectsUser);
                    $this->Flash->success(__('Your associated member has been assigned to the project.'));
                } else {
                    $this->Flash->error(__('This member is already associated with this project. Please, try again.'));
                }
            } else {
                $this->Flash->error(__('The member could not be added. Please, try again.'));
            }
        }
    }

    public function promoteToCoManager($projectId, $userId)
    {
        $this->request->allowMethod(['post', 'put']);
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $projects = $this->fetchTable('Projects');


        $builderId = $projects->get($projectId)->builder_id;


        // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {

                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);

        }

        // Check if the user has the appropriate role to perform this action
        // You can add additional authorization logic here if needed

        // Load the ProjectsUsers entity for the specified project and user
        $projectUser = $this->ProjectsUsers->find()
            ->where([
                'project_id' => $projectId,
                'user_id' => $userId,
            ])
            ->first();

        if (!$projectUser) {
            $this->Flash->error(__('Project user not found.'));
        } else {
            // Toggle the user's status between 'Engaged' and 'Co-Manager'
            $newStatus = $projectUser->status === 'Engaged' ? 'Co-Manager' : 'Engaged';
            $projectUser->status = $newStatus;

            if ($this->ProjectsUsers->save($projectUser)) {
                $this->Flash->success(__('User status has been updated.'));
            } else {
                $this->Flash->error(__('Unable to update user status. Please, try again.'));
            }
        }

        return $this->redirect($this->referer());
    }


}
