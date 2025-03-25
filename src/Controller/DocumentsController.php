<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\UsersAgreement;
use App\Model\Table\UsersAgreementsTable;
use App\Model\Entity\ProjectsDocument;
use App\Model\Table\ProjectsDocumentsTable;
use Cake\Datasource\FactoryLocator;
use Cake\I18n\FrozenTime;

/**
 * Documents Controller
 *
 * @property \App\Model\Table\DocumentsTable $Documents
 * @property \App\Model\Table\CompaniesTable $Companies
 * @property \App\Model\Table\CompaniesProjectsTable $CompaniesProjects
 * @method \App\Model\Entity\Document[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DocumentsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $document = $this->Documents->newEmptyEntity();
        $this->Authorization->authorize($document);
        $documents = $this->paginate($this->Documents);

        $this->set(compact('documents'));
    }

    /**
     * View method
     *
     * @param string|null $id Document id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $currentUserAgreementStatus = [];

        $document = $this->Documents->get($id, [
            'contain' => ['Projects', 'Users'],
        ]);
        $this->Authorization->authorize($document);




        $this->set(compact('document'));

        //retrieve current user
        $currentUser = $this->request->getAttribute('identity');
        $this->ProjectsDocuments = $this->getTableLocator()->get('ProjectsDocuments');

        $projectId = $this->request->getQuery('pj_id');


        // Check if the current user has already agreed
        if($projectId) {
            $currentUserAgreementStatus = $this->ProjectsDocuments->find()
                ->where(['document_id' => $id, 'project_id' => $projectId])
                ->select(['status'])
                ->first();


        }
        $this->set('currentUserAgreementStatus', $currentUserAgreementStatus);
        // debug($id);
        // debug($projectId);
        // debug($currentUserAgreementStatus->status);


        if ($this->request->is('post')) {
            // debug($currentUserAgreementStatus->status);
            //Builder reviewing/rejecting company/personal doc
            if ($currentUser->role === 'Builder') {
                if ($currentUserAgreementStatus !== 'Reviewed') {
                    $this->processReviewOrRejection($document, $currentUser, $projectId);
                } else {
                    $this->Flash->error('You have already agreed to the terms.');
                }
            }

            //Worker agreeing to induction doc
            if ($currentUser->role != 'Builder') {
                // Check if the approval action is triggered
                $reviewAction = $this->request->getData('review_action');
                if ($reviewAction === 'review') {
                    $this->processAgreement($document, $currentUser);
                } else {
                    $this->Flash->error('Please select a review action.');
                }
            }
        }
    }




    public function processAgreement($document, $currentUser)
    {
        //Find current agreement status
        $currentUserAgreementStatus = $this->ProjectsDocuments->find()
            ->where(['user_id' => $currentUser->id, 'document_id' => $document->id])
            ->select(['status'])
            ->first();

        if ($this->request->is('post')) {
            if (!$currentUserAgreementStatus) {
                $reviewAction = $this->request->getData('review_action');
                if ($reviewAction === 'review' && $currentUser->role != 'Builder') {
                    // User has agreed to the document's terms. Save agreement record in ProjectsDocuments

                    //Get project id
                    $projectID = $this->ProjectsDocuments->find()
                        ->where(['user_id' => $document->uploaded_user_id, 'document_id' => $document->id])
                        ->select(['project_id'])
                        ->first();

                    if ($projectID) {
                        $project_id = $projectID->project_id;
                    } else {
                        //no matching record is found
                    }

                    $this->ProjectsDocuments = $this->getTableLocator()->get('ProjectsDocuments');
                    $projectDocument = $this->ProjectsDocuments->newEmptyEntity();
                    $projectDocument->project_id = $project_id;
                    $projectDocument->document_id = $document->id;
                    $projectDocument->company_id = $document->related_company_id;
                    $projectDocument->user_id = $currentUser->id;
                    $projectDocument->status = 'Reviewed';
                    $projectDocument->auth_type = '3';
                    $projectDocument->auth_value = "Induction Document";
                    $this->ProjectsDocuments->save($projectDocument);
                    $this->Flash->success('Agreement submitted successfully.');
                }
            } else {
                $this->Flash->error('You have already agreed to the terms and conditions of the document.');
            }
        }
    }


    protected function processReviewOrRejection($document, $currentUser, $projectId)
    {

        //Builder action only --> reviewing/rejected document
        if ($currentUser->role === 'Builder') {
            $reviewAction = $this->request->getData('review_action');
            if ($reviewAction === 'approve') {
                $this->processApproval($document, $currentUser, $projectId);
            } elseif ($reviewAction === 'reject') {
                $this->processRejection($document, $currentUser, $projectId);
            } else {
                $this->Flash->error('Please select a review action.');
            }
        }
    }


    //Approved
    protected function processApproval($document, $currentUser, $projectId)
    {
        $this->ProjectsDocuments = $this->getTableLocator()->get('ProjectsDocuments');

        // Find the record for this document in projects_documents
        $projectDocument = $this->ProjectsDocuments->find()
            ->where(['document_id' => $document->id, 'project_id' => $projectId])
            ->first();

        if ($projectDocument) {
            // Update the 'status' to 'Reviewed'
            $projectDocument->status = 'Reviewed';

            if ($this->ProjectsDocuments->save($projectDocument)) {
                // Update the document status to "Approved"
                $document->status = "Approved";
                $this->Documents->save($document);

                $this->Flash->success('The document has been reviewed.');

                // Redirect or perform further actions
            } else {
                $this->Flash->error('An error occurred while saving the project document.');
            }
        } else {
            $this->Flash->error('No record found for this document in projects_documents.');
        }

        //redirect back to company view
        if($projectDocument->company_id){
            return $this->redirect([
                'controller' => 'companies',
                'action' => 'view',
                $projectDocument->company_id,
                '?' => ['pj_id' => $projectId]
            ]);
           } else {
            return $this->redirect([
                'controller' => 'users',
                'action' => 'view',
                $projectDocument->user_id,
                '?' => ['pj_id' => $projectId]
            ]);

           }
    }

    //Rejected
    protected function processRejection($document, $currentUser, $projectId)
    {
        $this->ProjectsDocuments = $this->getTableLocator()->get('ProjectsDocuments');
        $comments = $this->request->getData('reject_comments');

        // Find the record for this document in projects_documents
        $projectDocument = $this->ProjectsDocuments->find()
            ->where(['document_id' => $document->id, 'project_id' => $projectId])
            ->first();

        if ($projectDocument) {
            // Update the 'status' to 'Rejected'
            $projectDocument->status = 'Rejected';
            $projectDocument->comment = $comments;

            if ($this->ProjectsDocuments->save($projectDocument)) {
                // Update the document status to "Rejected" and store comments
                $document->status = 'Rejected';
                $document->comment = $comments;
                $this->Documents->save($document);

                $this->Flash->success('The document has been rejected.');

                // Redirect or perform further actions
            } else {
                $this->Flash->error('An error occurred while updating the project document.');
            }
        } else {
            $this->Flash->error('No record found for this document in projects_documents.');
        }
       //redirect back to company view
       if($projectDocument->company_id){
        return $this->redirect([
            'controller' => 'companies',
            'action' => 'view',
            $projectDocument->company_id,
            '?' => ['pj_id' => $projectId]
        ]);
       } else {
        return $this->redirect([
            'controller' => 'users',
            'action' => 'view',
            $projectDocument->user_id,
            '?' => ['pj_id' => $projectId]
        ]);

       }

    }



    // update project document table
    public function updateProjectDoc($document, $projectsId, $auth_type, $auth_value): void
    {
        // TODO:

        $projectsDocumentsTable = $this->getTableLocator()->get('ProjectsDocuments');

        // Find the existing project document
        $projectDocument = $projectsDocumentsTable->find()
            ->where(['project_id' => $projectsId, 'document_id' => $document->id])
            ->first();

        if ($projectDocument) {
            // Update the existing project document
            $projectDocument->auth_type = $auth_type;
            $projectDocument->auth_value = $auth_value;
        } else {
            // Create a new project document if it doesn't exist
            $projectDocument = $projectsDocumentsTable->newEmptyEntity();
            $projectDocument->project_id = $projectsId;
            $projectDocument->document_id = $document->id;
            $projectDocument->company_id = $document->related_company_id;
            $projectDocument->user_id = $document->uploaded_user_id;
            $projectDocument->status = 'Pending';
            $projectDocument->auth_type = $auth_type;
            $projectDocument->auth_value = $auth_value;
        }

        // Save the project document
        $projectsDocumentsTable->save($projectDocument);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $document = $this->Documents->newEmptyEntity();
        $this->Authorization->authorize($document);
        $projectid = $this->request->getQuery('project');

        $fileExtension = $this->request->getData('extension');
        // $currentUser = $this->request->getAttribute('identity');

        $identity = $this->Authentication->getIdentity();
        $currentUser = $identity->get('id');


        // update document access or add document access
        if ($this->request->getQuery('company')) {

            $relation_id = $this->request->getQuery('company');
            // get all related projects for this company
            $companyProjects = $this->fetchTable('CompaniesProjects')->find()
                ->where(['company_id' => $relation_id, 'status' => 'Engaged'])
                ->toArray();

            $related_projects = [];
            if ($companyProjects) {
                foreach ($companyProjects as $companyProject) {
                    $projectInfo = $this->fetchTable('Projects')->find()
                        ->where(['id' => $companyProject->project_id])
                        ->toArray();

                    $related_projects[$companyProject->project_id] = $projectInfo;
                }
            }

            $this->set(compact('related_projects', 'companyProjects'));
        } elseif ($this->request->getQuery('user')) {
            $relation_id = $this->request->getQuery('user');

            // get all related projects for this user
            $userProjects = $this->fetchTable('ProjectsUsers')->find()
                ->where(['user_id' => $relation_id, 'status' => 'Engaged'])
                ->toArray();

            $related_projects = [];
            if ($userProjects) {
                foreach ($userProjects as $userProject) {
                    $projectInfo = $this->fetchTable('Projects')->find()
                        ->where(['id' => $userProject->project_id])
                        ->toArray();

                    $related_projects[$userProject->project_id] = $projectInfo;
                }
            }
            $this->set(compact('related_projects'));
        }

        if ($this->request->is('post')) {
            // Set the 'uploaded_user_id' field with the current user's ID
            $document->uploaded_user_id = $currentUser;
            $auth_type = $this->request->getData('auth_type');

            $file = $this->request->getData('file_upload');
            $document = $this->Documents->patchEntity($document, $this->request->getData());

            $relation_id = $this->request->getData('relation_id');
            $document_relation = $this->request->getData('document_relation');
            $fileDestination = "";
            $signaturerequired = $this->request->getData('requires_signature');

            if ($document_relation == 'user') {
                //Upload to user's personal document folder Personal/user->id
                $directory = WWW_ROOT . 'uploads' . DS . 'Personal' . DS . $relation_id . DS;
                $document->related_user_id = $relation_id;
                is_dir($directory) || mkdir($directory);
            } elseif ($document_relation == 'project') {

                $currentUser = $this->request->getAttribute('identity');
                $projects = $this->fetchTable('Projects');

                $request = $this->request;

                $project = $request->getQuery('project');

                $builderId = $projects->get($projectid)->builder_id;


                // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
                if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {
                    // Find the user's status for the project
                    $userStatus = $this->fetchTable('ProjectsUsers')->find()
                        ->select(['status'])
                        ->where([
                            'project_id' => $projectid,
                            'user_id' => $currentUser->id,
                        ])
                        ->first();

                    // Check if the user's status is not "Co-Manager"
                    if ($userStatus->status != 'Co-Manager') {
                        $this->Flash->error(__('You do not have permission to access this page.'));
                        return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
                    }
                }
                //Upload documents to Induction/project->id
                $directory = WWW_ROOT . 'uploads' . DS . 'Induction' . DS . $relation_id . DS;
                $document->related_project_id = $relation_id;
                is_dir($directory) || mkdir($directory);
                if ($signaturerequired) {
                    $document->requires_signature = TRUE;
                }
            } elseif ($document_relation == 'company') {
                //Upload documents to company folder
                $directory = WWW_ROOT . 'uploads' . DS . 'Company' . DS . $relation_id . DS;
                $document->related_company_id = $relation_id;
                is_dir($directory) || mkdir($directory);

                if ($this->request->getData('worker_accessible')) {
                    $document->worker_accessible = 1;
                } else {
                    $document->worker_accessible = 0;
                }
            } else {
                $this->Flash->error(__('Error getting document relationship. Please try again.'));
                return $this->redirect(['action' => 'add']);
            }

            if ($this->Documents->save($document)) {

                if ($document_relation == 'project') {
                    $auth_type = 5; // induction document type number
                    $auth_value = 'Induction Document';
                    $document->auth_type = $auth_type;
                    $document->auth_value = $auth_value;
                    $this->Documents->save($document);
                    $this->updateProjectDoc($document, $relation_id, $auth_type, $auth_value);
                }

                if ($document_relation == 'company') {
                    $doc_project_ids = $this->request->getData('doc_project_id');

                    // must be company documents or user documents
                    if ($doc_project_ids) {
                        foreach ($doc_project_ids as $doc_project_id) {
                            $auth_type = $this->request->getData('auth_type_' . $doc_project_id);
                            $auth_value = $this->request->getData('auth_value_' . $doc_project_id);

                            if ($auth_type == '3') {
                                if (!$auth_value) {
                                    $auth_value = 'Admin';
                                } else {
                                    $auth_value[] = 'Admin';
                                    $auth_value = implode(',', $auth_value);
                                }
                            } elseif ($auth_type == '4') {
                                $auth_value = 'Private';
                            }

                            if ($auth_type && $auth_value) {
                                $this->updateProjectDoc($document, $doc_project_id, $auth_type, $auth_value);
                            }
                        }
                    }
                }elseif ($document_relation == 'user'){
                    // get all related projects for this user
                    $userProjects = $this->fetchTable('ProjectsUsers')->find()
                        ->where(['user_id' => $relation_id, 'status' => 'Engaged'])
                        ->toArray();

                    if ($userProjects) {
                        foreach ($userProjects as $userProject) {
                            $auth_type = 3;
                            $auth_value = 'Admin,Builder';

                            $this->updateProjectDoc($document, $userProject->project_id, $auth_type, $auth_value);
                        }
                    }
                }

                $fileDestination = $directory . h($document->id) . '.' . $fileExtension;
                if (file_exists($fileDestination)) {
                    $date = date('Y-m-d H-i-s');
                    $backupName = basename($fileDestination, '.' . $fileExtension);
                    is_dir($directory . 'backups' . DS) || mkdir($directory . 'backups' . DS);
                    $backupDestination = $directory . 'backups' . DS . '[' . $date . ']' . $backupName . '.' . $fileExtension;
                    rename($fileDestination, $backupDestination);
                }
                $file->moveTo($fileDestination);
                if ($signaturerequired == 'y') {
                    $signaturesTable = FactoryLocator::get('Table')->get('Signatures')->find();
                    $isInducted = FactoryLocator::get('Table')->get('Inductions')->find()->where([
                        'project_id' => $projectid
                    ]);
                    if ($isInducted->count() != 0) {
                        foreach ($isInducted as $inductee) {
                            $addSignatures = $signaturesTable->insert(['Document_id', 'User_id'])->values([
                                'Document_id' => $document->id,
                                'User_id' => $inductee->user_id
                            ]);
                        }
                        $addSignatures->execute();
                    }
                }
                $this->Flash->success(__('Document Saved'));
                if ($document_relation == 'user') {
                    return $this->redirect(['controller' => 'users', 'action' => 'view', $relation_id]);
                } elseif ($document_relation == 'project') {
                    return $this->redirect(['controller' => 'projects', 'action' => 'index']);
                } elseif ($document_relation == 'company') {
                    return $this->redirect(['controller' => 'companies', 'action' => 'view', $relation_id]);
                }
            }
            $this->Flash->error(__('The document could not be saved. Please try again.'));
        }

        $this->set(compact('document'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Document id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $document = $this->Documents->get($id, [
            'contain' => ['Projects', 'Users'],
        ]);
        $this->Authorization->authorize($document);

        // update document access or add document access
        $document_relation = $document->document_relation;
        if ($document_relation == 'company') {
            $relation_id = $document->related_company_id;

            // get all related projects for this company
            $companyProjects = $this->fetchTable('CompaniesProjects')->find()
                ->where(['company_id' => $relation_id, 'status' => 'Engaged'])
                ->toArray();

            $related_projects = [];
            $project_documents = [];
            if ($companyProjects) {
                foreach ($companyProjects as $companyProject) {
                    $projectInfo = $this->fetchTable('Projects')->find()
                        ->where(['id' => $companyProject->project_id])
                        ->toArray();

                    $projectDocument = $this->fetchTable('ProjectsDocuments')->find()
                        ->where(['project_id' => $companyProject->project_id, 'document_id' => $document->id])
                        ->toArray();

                    $related_projects[$companyProject->project_id] = $projectInfo;
                    $project_documents[$companyProject->project_id] = $projectDocument;
                }
            }
            $this->set(compact('related_projects', 'project_documents'));
        } elseif ($document_relation == 'user') {
            $relation_id = $document->uploaded_user_id;

            // get all related projects for this user
            $userProjects = $this->fetchTable('ProjectsUsers')->find()
                ->where(['user_id' => $relation_id, 'status' => 'Engaged'])
                ->toArray();

            $related_projects = [];
            $project_documents = [];
            if ($userProjects) {
                foreach ($userProjects as $userProject) {
                    $projectInfo = $this->fetchTable('Projects')->find()
                        ->where(['id' => $userProject->project_id])
                        ->toArray();

                    $projectDocument = $this->fetchTable('ProjectsDocuments')->find()
                        ->where(['project_id' => $userProject->project_id, 'document_id' => $document->id])
                        ->toArray();

                    $related_projects[$userProject->project_id] = $projectInfo;
                    $project_documents[$userProject->project_id] = $projectDocument;
                }
            }
            $this->set(compact('related_projects', 'project_documents'));
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $document = $this->Documents->patchEntity($document, $this->request->getData());

            $doc_project_ids = $this->request->getData('doc_project_id');

            // must be company documents or user documents
            if ($doc_project_ids) {
                foreach ($doc_project_ids as $doc_project_id) {
                    $auth_type = $this->request->getData('auth_type_' . $doc_project_id);
                    $auth_value = $this->request->getData('auth_value_' . $doc_project_id);

                    if ($auth_type == '3') {
                        if (!$auth_value) {
                            $auth_value = 'Admin';
                        } else {
                            $auth_value[] = 'Admin';
                            $auth_value = implode(',', $auth_value);
                        }
                    } elseif ($auth_type == '4') {
                        $auth_value = 'Private';
                    } elseif ($auth_type == '1') {
                        $auth_value = 'Builder';
                    }

                    $this->updateProjectDoc($document, $doc_project_id, $auth_type, $auth_value);
                }
            }

            if (!$this->request->getData('worker_accessible')) {
                $document->worker_accessible = 0;
            } else {
                $document->worker_accessible = 1;
            }

            // Update issue date
            if ($this->request->getData('issue_date')) {
                $document->issue_date = $this->request->getData('issue_date');
            }
            // Update expiry date
            if ($this->request->getData('expiry_date')) {
                $document->expiry_date = $this->request->getData('expiry_date');
            }

            if ($this->Documents->save($document)) {
                $this->Flash->success(__('The document has been saved.'));
                if ($document->related_project_id) {
                    return $this->redirect(['controller' => 'projects', 'action' => 'view', $document->related_project_id]);
                } else if ($document->related_company_id) {
                    return $this->redirect(['controller' => 'companies', 'action' => 'view', $document->related_company_id]);
                } else if ($document->related_user_id) {
                    return $this->redirect(['controller' => 'users', 'action' => 'view', $document->related_user_id]);
                }
            }
            $this->Flash->error(__('The document could not be saved. Please, try again.'));
        }
        $projects = $this->Documents->Projects->find('list', ['limit' => 200])->all();
        $users = $this->Documents->Users->find('list', ['limit' => 200])->all();
        $this->set(compact('document', 'projects', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Document id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $document = $this->Documents->get($id);
        $this->Authorization->authorize($document);

        $document->archived = 1;

        if ($this->Documents->save($document)) {
            $this->Flash->success(__('The document has been archived.'));
        } else {
            $this->Flash->error(__('The document could not be archived. Please, try again.'));
        }

        if ($document->related_project_id) {
            return $this->redirect(['controller' => 'projects', 'action' => 'view', $document->related_project_id]);
        } else if ($document->related_company_id) {
            return $this->redirect(['controller' => 'companies', 'action' => 'view', $document->related_company_id]);
        } else if ($document->related_user_id) {
            return $this->redirect(['controller' => 'users', 'action' => 'view', $document->related_user_id]);
        }
    }

    public function unarchived($id = null)
    {

        $document = $this->Documents->get($id);
        $this->Authorization->authorize($document);

        $document->archived = 0;

        if ($this->Documents->save($document)) {
            $this->Flash->success(__('The document has been unarchived.'));
        } else {
            $this->Flash->error(__('The document could not be unarchived. Please, try again.'));
        }

        if ($document->related_project_id) {
            return $this->redirect(['controller' => 'projects', 'action' => 'view', $document->related_project_id]);
        } else if ($document->related_company_id) {
            return $this->redirect(['controller' => 'companies', 'action' => 'view', $document->related_company_id]);
        } else if ($document->related_user_id) {
            return $this->redirect(['controller' => 'users', 'action' => 'view', $document->related_user_id]);
        }
    }

    public function review($id = null)
    {
        $document = $this->Documents->get($id, [
            'contain' => ['Projects'],
        ]);
        $this->Authorization->authorize($document);
        $this->set(compact('document'));

        if ($this->request->is("post") && $this->request->getData('signed') == NULL && $this->request->getData('signature') != 'Y') {
            $this->Flash->error("A signature is required.");
        } elseif ($this->request->is("post") && $this->request->getData('signature') == 'Y') {
            $currentUser = $this->request->getAttribute('identity');
            $record_id = $this->request->getQuery('rid');

            $documentsUser = FactoryLocator::get('Table')->get('Signatures');
            $record = $documentsUser->get($record_id);
            $record->signed_datetime = date('Y-m-d H:i:s');

            if ($documentsUser->save($record)) {
                $projects = FactoryLocator::get('Table')->get('Projects')->find();
                $project = $projects->where(['id' => $document->related_project_id])->first();

                $signatures = FactoryLocator::get('Table')->get('Signatures')->find();
                $signatures->select(['project_name' => 'projects.name'])->join([
                    "table" => "documents",
                    "type" => "LEFT",
                    "conditions" => "Signatures.document_id = documents.id"
                ])->join([
                    "table" => "projects",
                    "type" => "LEFT",
                    "conditions" => "documents.related_project_id = projects.id"
                ])->where([
                    'user_id' => $currentUser->id,
                    'documents.related_project_id' => $project->id,
                    'signed_datetime is' => NULL
                ])->enableAutoFields();

                if ($signatures->count() == 0) {
                    $currentDate = FrozenTime::now();
                    $currentDate->i18nFormat('y-MM-dd');
                    $inductions = FactoryLocator::get('Table')->get('Inductions')->find();
                    $inductions->update()
                        ->set(['inducted_date' => $currentDate])
                        ->where([
                            'project_id' => $project->id,
                            'user_id' => $currentUser->id
                        ])
                        ->execute();
                    $this->Flash->success("Signature saved. Induction complete for " . $project->name);
                } else {
                    $this->Flash->success("Signature saved successfully");
                }
            } else {
                $this->Flash->error(__('The signing activity could not be recorded.'));
            }

            return $this->redirect(['controller' => 'signatures', 'action' => 'pending']);
        } elseif ($this->request->is("post")) {
            // To save a signature
            $file_string = $this->request->getData('signed');
            $image = explode(";base64,", $file_string);
            $image_type = explode("image/", $image[0]);
            $image_type_png = $image_type[1];
            $image_base64 = 'base64_decode'($image[1]);
            $folderPath = WWW_ROOT . 'uploads/Signature/';
            $file = $folderPath . $document->id . '.' . $image_type_png;
            file_put_contents($file, $image_base64);

            $currentUser = $this->request->getAttribute('identity');
            $record_id = $this->request->getQuery('rid');

            $documentsUser = FactoryLocator::get('Table')->get('Signatures');
            $record = $documentsUser->get($record_id);
            $record->signed_datetime = date('Y-m-d H:i:s');

            if ($documentsUser->save($record)) {
                $projects = FactoryLocator::get('Table')->get('Projects')->find();
                $project = $projects->where(['id' => $document->related_project_id])->first();

                $signatures = FactoryLocator::get('Table')->get('Signatures')->find();
                $signatures->select(['project_name' => 'projects.name'])->join([
                    "table" => "documents",
                    "type" => "LEFT",
                    "conditions" => "Signatures.document_id = documents.id"
                ])->join([
                    "table" => "projects",
                    "type" => "LEFT",
                    "conditions" => "documents.related_project_id = projects.id"
                ])->where([
                    'user_id' => $currentUser->id,
                    'documents.related_project_id' => $project->id,
                    'signed_datetime is' => NULL
                ])->enableAutoFields();

                if ($signatures->count() == 0) {
                    $currentDate = FrozenTime::now();
                    $currentDate->i18nFormat('y-MM-dd');
                    $inductions = FactoryLocator::get('Table')->get('Inductions')->find();
                    $inductions->update()
                        ->set(['inducted_date' => $currentDate])
                        ->where([
                            'project_id' => $project->id,
                            'user_id' => $currentUser->id
                        ])
                        ->execute();
                    $this->Flash->success("Signature saved. Induction complete for " . $project->name);
                } else {
                    $this->Flash->success("Signature saved successfully");
                }
            } else {
                $this->Flash->error(__('The signing activity could not be recorded.'));
            }
            return $this->redirect(['controller' => 'signatures', 'action' => 'pending']);
        }
    }

    public function download($id = null)
    {
        $this->Authorization->skipAuthorization();
        $document = $this->Documents->get($id);
        if ($document->related_project_id) {
            $document_type = 'Induction';
            $related_id = $document->related_project_id;
        } elseif ($document->related_company_id) {
            $document_type = 'Company';
            $related_id = $document->related_company_id;
        } elseif ($document->related_user_id) {
            $document_type = 'Personal';
            $related_id = $document->related_user_id;
        } else {
            $this->Flash->error(__('Error, no valid file found.'));
        }
        $fileDestination = WWW_ROOT . 'uploads' . DS . $document_type . DS . $related_id . DS . $document->id . '.' . $document->extension;
        // create an if statement
        if (file_exists($fileDestination)) {
            header('Content-Type: application/' . $document->extension);
            header('Content-Disposition: attachment; filename="' . $document->name . '.' . $document->extension);
            //download
            header('Content-Length: ' . filesize($fileDestination));
            debug(readfile($fileDestination));
        } else {
            $this->Flash->error(__('Error. Could not download file.'));
        }
    }

    //    public function approve($id = null){
    //        $this->Authorization->skipAuthorization();
    //
    //    }

}
