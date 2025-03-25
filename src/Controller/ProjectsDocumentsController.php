<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * ProjectsDocuments Controller
 *
 * @property \App\Model\Table\ProjectsDocumentsTable $ProjectsDocuments
 * @method \App\Model\Entity\ProjectsDocument[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProjectsDocumentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Projects', 'Users', 'Documents'],
        ];
        $projectsDocuments = $this->paginate($this->ProjectsDocuments);

        $this->set(compact('projectsDocuments'));
    }

    /**
     * View method
     *
     * @param string|null $id Projects Document id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $projectsDocument = $this->ProjectsDocuments->get($id, [
            'contain' => ['Projects', 'Users', 'Documents'],
        ]);

        $this->set(compact('projectsDocument'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $projectsDocument = $this->ProjectsDocuments->newEmptyEntity();
        if ($this->request->is('post')) {
            $projectsDocument = $this->ProjectsDocuments->patchEntity($projectsDocument, $this->request->getData());
            if ($this->ProjectsDocuments->save($projectsDocument)) {
                $this->Flash->success(__('The projects document has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The projects document could not be saved. Please, try again.'));
        }
        $projects = $this->ProjectsDocuments->Projects->find('list', ['limit' => 200])->all();
        $users = $this->ProjectsDocuments->Users->find('list', ['limit' => 200])->all();
        $documents = $this->ProjectsDocuments->Documents->find('list', ['limit' => 200])->all();
        $this->set(compact('projectsDocument', 'projects', 'users', 'documents'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Projects Document id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $projectsDocument = $this->ProjectsDocuments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $projectsDocument = $this->ProjectsDocuments->patchEntity($projectsDocument, $this->request->getData());
            if ($this->ProjectsDocuments->save($projectsDocument)) {
                $this->Flash->success(__('The projects document has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The projects document could not be saved. Please, try again.'));
        }
        $projects = $this->ProjectsDocuments->Projects->find('list', ['limit' => 200])->all();
        $users = $this->ProjectsDocuments->Users->find('list', ['limit' => 200])->all();
        $documents = $this->ProjectsDocuments->Documents->find('list', ['limit' => 200])->all();
        $this->set(compact('projectsDocument', 'projects', 'users', 'documents'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Projects Document id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $projectsDocument = $this->ProjectsDocuments->get($id);
        if ($this->ProjectsDocuments->delete($projectsDocument)) {
            $this->Flash->success(__('The projects document has been deleted.'));
        } else {
            $this->Flash->error(__('The projects document could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
