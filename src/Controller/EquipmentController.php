<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Equipment Controller
 *
 * @property \App\Model\Table\EquipmentTable $Equipment
 * @method \App\Model\Entity\Equipment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EquipmentController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->skipAuthorization();
        // Check if the current user's role is admin
        $currentUser = $this->Authentication->getIdentity();
        if ($currentUser && $currentUser->role !== 'Admin') {
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['controller' => 'SomeController', 'action' => 'someAction']); // Redirect to an appropriate action
        }
        $equipment = $this->paginate($this->Equipment);

        $this->set(compact('equipment'));
    }

    /**
     * View method
     *
     * @param string|null $id Equipment id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $equipment = $this->Equipment->get($id, [
            'contain' => [],
        ]);

        $this->Authorization->skipAuthorization();

        // Fetch the current user
        $currentUser = $this->Authentication->getIdentity();
        $this->set(compact('currentUser'));
        // Fetch the project
        $projectsTable = $this->fetchTable('Projects');
        if(!$equipment->related_project_id){
            $this->Flash->error(__('This equipment has been archived.'));
            return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
        }
        $project = $projectsTable->get($equipment->related_project_id);
        $this->set(compact('project'));

        // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
        if ($currentUser->role != 'Admin' && $currentUser->id != $project->builder_id) {
            $projectsUsersTable = $this->fetchTable('ProjectsUsers');

            $userPermission = $projectsUsersTable->exists([
                'project_id' => $project->id,
                'user_id' => $currentUser->id,
                'status' => 'Co-Manager', // Check for "Co-Manager" status
            ]);

            if (!$userPermission && $equipment->related_user_id != $currentUser->id) {
                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
            }
        }


        if ($equipment->related_project_id && $equipment->related_user_id) {


            // Fetch the status
            $projectsUsersTable = $this->fetchTable('ProjectsUsers');
            $statusEntity = $projectsUsersTable->find()
                ->select(['status'])
                ->where([
                    'user_id' => $equipment->related_user_id,
                    'project_id' => $equipment->related_project_id,
                ])
                ->first();

            $status = $statusEntity ? $statusEntity->status : null;
            $this->set(compact('status'));
        }
        else {

        }




        $this->set(compact('equipment'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $equipment = $this->Equipment->newEmptyEntity();
        $this->Authorization->authorize($equipment);
        $projectid = $this->request->getQuery('project');
        $currentUser = $this->request->getAttribute('identity');
        $myequipments = [];
        if ($this->request->is('post')) {

            $auth_type = $this->request->getData('auth_type');
            $file = $this->request->getData('file_upload');
            $equipment = $this->Equipment->newEmptyEntity();

            $equipment = $this->Equipment->patchEntity($equipment, $this->request->getData());
            $equipment->related_user_id = $currentUser->id;
            $attachment = $this->request->getData('combined_file');



            if ($attachment && $attachment->getSize() > 0) {
                if ($attachment->getError()) {
                    $this->Flash->error($attachment->getError());
                }

                $dir = WWW_ROOT . 'uploads' . DS . 'userimage';

                if (!is_dir($dir)) {
                    if (!mkdir($dir, 0755, true)) {
                        $this->Flash->error(__('The directory ' . $dir . ' does not exist and could not be created. Please try again.'));
                        return $this->redirect(['controller' => 'projects', 'action' => 'index']);
                    }
                }

                // Generate a random 32-character name for the image
                $imagename = bin2hex(random_bytes(16)) . '.jpg'; // You can adjust the file extension if needed

                $targetPath = $dir . DS . $imagename;
                $newPath = '/uploads/userimage/' . $imagename;

                $attachment->moveTo($targetPath);
                $equipment->image = $newPath;
                $equipment->image_date = date('Y-m-d');

            } else {
                $this->Flash->error(__('You may want to upload an image.'));
            }





            if($auth_type == '3'){
                $auth_value = $this->request->getData('auth_value');
                $auth_value = implode(',', $auth_value);
                $equipment->auth_value = $auth_value;
            }
            $relation_id = $this->request->getData('relation_id');
            $equipment_relation = $this->request->getData('equipment_relation');
            $fileDestination = "";
            //$signaturerequired = $this->request->getData('requires_signature');

            if ($equipment_relation == 'user'){
                //Upload to user's personal equipment folder Personal/user->id
                $directory = WWW_ROOT.'uploads'.DS.'Personal'.DS.$relation_id.DS;
                $equipment->related_user_id = $relation_id;
                is_dir($directory) || mkdir($directory);
            } elseif ($equipment_relation == 'project'){
                //Upload equipments to Induction/project->id


                $projects = $this->fetchTable('Projects');

                $request = $this->request;

                $project = $request->getQuery('project');

                $builderId = $projects->get($projectid)->builder_id;



                // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
                if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {
                    $projectsUsersTable = $this->fetchTable('ProjectsUsers');

                    $userPermission = $projectsUsersTable->exists([
                        'project_id' => $projectid,
                        'user_id' => $currentUser->id,
                    ]);

                    if (!$userPermission) {
                        $this->Flash->error(__('You do not have permission to access this page.'));
                        return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
                    }
                }

                $directory = WWW_ROOT.'uploads'.DS.'Induction'.DS.$relation_id.DS;
                $equipment->related_project_id = $relation_id;
                is_dir($directory) || mkdir($directory);
                // if ($signaturerequired){
                //     $equipment->requires_signature = TRUE;
                // }
            } elseif ($equipment_relation == 'company'){
                //Upload equipments to company folder
                $directory = WWW_ROOT.'uploads'.DS.'Company'.DS.$relation_id.DS;
                $equipment->related_company_id = $relation_id;
                is_dir($directory) || mkdir($directory);
                if ($this->request->getData('worker_accessible')){
                    $equipment->worker_accessible = 1;
                } else {
                    $equipment->worker_accessible = 0;
                }
            } else {
                $this->Flash->error(__('Error getting equipment relationship. Please try again.'));
                return $this->redirect(['action' => 'add']);
            }
            // debug($this->request->getData());
            // debug($equipment);
            // debug($this->Equipment->save($equipment));
            // exit;
            $equipment->review_status = 'Pending';
            if ($this->Equipment->save($equipment)) {
                $fileDestination = $directory.h($equipment->id).'.pdf';
                if (file_exists($fileDestination)){
                    $date = date('Y-m-d H-i-s');
                    $backupName = basename($fileDestination, '.pdf');
                    is_dir($directory.'backups'.DS) || mkdir($directory.'backups'.DS);
                    $backupDestination = $directory.'backups'.DS.'['.$date.']'.$backupName.'.pdf';
                    rename($fileDestination, $backupDestination);
                }
                $file->moveTo($fileDestination);
                // if($signaturerequired == 'y'){
                //     $signaturesTable = FactoryLocator::get('Table')->get('Signatures')->find();
                //     $isInducted = FactoryLocator::get('Table')->get('Inductions')->find()->where([
                //         'project_id' => $projectid
                //     ]);
                //     if ($isInducted->count() != 0){
                //         foreach($isInducted as $inductee){
                //             $addSignatures = $signaturesTable->insert(['Document_id', 'User_id'])->values([
                //                 'Document_id' => $document->id,
                //                 'User_id' => $inductee->user_id
                //             ]);
                //         }
                //         $addSignatures->execute();
                //     }
                // }
                $this->Flash->success(__('Equipment Saved, see list at the bottom of the page.'));
                if ($equipment_relation == 'user'){
                    return $this->redirect(['controller' => 'users', 'action' => 'view', $relation_id]);
                } elseif ($equipment_relation == 'project'){
                    return $this->redirect(['controller' => 'projects', 'action' => 'view', $equipment->related_project_id]);
                } elseif ($equipment_relation == 'company'){
                    return $this->redirect(['controller' => 'companies', 'action' => 'view', $relation_id]);
                }
            }
            $this->Flash->error(__('The equipment could not be saved. Please try again.'));
        }

        $this->set(compact('equipment', 'myequipments'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Equipment id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $equipment = $this->Equipment->get($id, [
            'contain' => [],
        ]);
        $this->Authorization->skipAuthorization();
        $currentUser = $this->request->getAttribute('identity');
        $authType = $equipment->auth_type;
        $oldImage = $equipment->image;

        if ($authType == 1) {
            $projects = $this->fetchTable('Projects');
            $project = $projects->get($equipment->related_project_id);

            if (
                $currentUser->id != $equipment->related_user_id &&
                $currentUser->id != $project->builder_id &&
                !$this->fetchTable('ProjectsUsers')->exists([
                    'project_id' => $project->id,
                    'user_id' => $currentUser->id,
                    'status' => 'Co-Manager'
                ])
            ) {
                $this->Flash->error(__('You do not have access to this page.'));
                return $this->redirect(['controller' => 'projects', 'action' => 'index']);
            }
        }


        if ($authType == 2) {
            // Check if the user's role is in the 'auth_value' field
            $allowedRoles = explode(',', $equipment->auth_value);

            if ($currentUser->id != $equipment->related_user_id && !in_array($currentUser->role, $allowedRoles)) {
                $this->Flash->error(__('You do not have access to this page.'));
                return $this->redirect(['controller' => 'projects', 'action' => 'index']);
            }
        }

        if ($authType == 3) {
            // Check if the user's email is in the 'auth_value' field
            $allowedEmails = explode(';', $equipment->auth_value);

            if ($currentUser->id != $equipment->related_user_id && !in_array($currentUser->email, $allowedEmails)) {
                $this->Flash->error(__('You do not have access to this page.'));
                return $this->redirect(['controller' => 'projects', 'action' => 'index']);
            }
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $equipment = $this->Equipment->patchEntity($equipment, $this->request->getData());
            $attachment = $this->request->getData('combined_file');

            if ($attachment && $attachment->getSize() > 0) {
                if ($attachment->getError()) {
                    $this->Flash->error($attachment->getError());
                }

                $dir = WWW_ROOT . 'uploads' . DS . 'userimage';

                if (!is_dir($dir)) {
                    if (!mkdir($dir, 0755, true)) {
                        $this->Flash->error(__('The directory ' . $dir . ' does not exist and could not be created. Please try again.'));
                        return $this->redirect(['controller' => 'projects', 'action' => 'index']);
                    }
                }

                // Generate a random 32-character name for the image
                $imagename = bin2hex(random_bytes(16)) . '.jpg'; // You can adjust the file extension if needed

                $targetPath = $dir . DS . $imagename;
                $newPath = '/uploads/userimage/' . $imagename;

                $attachment->moveTo($targetPath);
                $equipment->image = $newPath;
                $equipment->image_date = date('Y-m-d');
            } else {
                $equipment->image = $oldImage;
            }

            if ($this->Equipment->save($equipment)) {
                $this->Flash->success(__('The equipment has been saved.'));
                return $this->redirect(['controller' => 'equipment', 'action' => 'view', $equipment->id]);
            }

            $this->Flash->error(__('The equipment could not be saved. Please, try again.'));
        }

        $this->set(compact('equipment'));
    }


    /**
     * Disassociates the equipment from the project
     *
     * @param string|null $id Equipment id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function disassociatefromproject($id = null)
    {
        $equipment = $this->Equipment->get($id);
        $this->Authorization->skipAuthorization();
        $projects = $this->fetchTable('Projects');
        $builderId = $projects->get($equipment->related_project_id)->builder_id;
        $currentUser = $this->request->getAttribute('identity');


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
        // Check if the equipment is associated with a project
        if ($equipment->related_project_id) {
            // Set the related_project_id to null
            $equipment->related_project_id = null;

            if ($this->Equipment->save($equipment)) {
                $this->Flash->success(__('The equipment has been disassociated from the project.'));
            } else {
                $this->Flash->error(__('The equipment could not be disassociated from the project. Please, try again.'));
            }
        } else {
            $this->Flash->error(__('The equipment is not associated with a project.'));
        }



        return $this->redirect(['controller' => 'projects', 'action' => 'index']);
    }
    public function approve($id = null)
    {
        $equipment = $this->Equipment->get($id);
        $this->Authorization->skipAuthorization();
        $projects = $this->fetchTable('Projects');
        $builderId = $projects->get($equipment->related_project_id)->builder_id;
        $currentUser = $this->request->getAttribute('identity');


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
        // Check if the equipment review status is not already 'Accepted'
        if ($equipment->review_status !== 'Accepted') {
            $equipment->review_status = 'Accepted';
            $equipment->review_reason = '';
            if ($this->Equipment->save($equipment)) {
                $this->Flash->success(__('Equipment has been approved.'));
            } else {
                $this->Flash->error(__('Unable to approve equipment. Please, try again.'));
            }
        } else {
            $this->Flash->warning(__('Equipment is already approved.'));
        }

        // Redirect to the equipment details page or wherever you want
        return $this->redirect(['action' => 'view', $id]);
    }

    public function reject($id = null, $rejectionReason = null)
    {
        $equipment = $this->Equipment->get($id);
        $this->Authorization->skipAuthorization();
        $projects = $this->fetchTable('Projects');
        $builderId = $projects->get($equipment->related_project_id)->builder_id;
        $currentUser = $this->request->getAttribute('identity');


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

        // Update the equipment's review status to 'Rejected'
        $equipment->review_status = 'Rejected';

        // Set the rejection reason
        $equipment->review_reason = $rejectionReason;

        // Save the updated equipment data
        if ($this->Equipment->save($equipment)) {
            $this->Flash->success(__('Equipment has been rejected.'));
        } else {
            $this->Flash->error(__('Failed to reject equipment.'));
        }

        // Redirect to the equipment's details page or any other desired location
        return $this->redirect(['action' => 'view', $id]);
    }

}
