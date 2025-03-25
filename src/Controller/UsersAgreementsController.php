<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * UsersAgreements Controller
 *
 * @property \App\Model\Table\UsersAgreementsTable $UsersAgreements
 * @method \App\Model\Entity\UsersAgreement[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersAgreementsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Projects', 'Documents'],
        ];
        $usersAgreements = $this->paginate($this->UsersAgreements);

        $this->set(compact('usersAgreements'));
    }

    /**
     * View method
     *
     * @param string|null $id Users Agreement id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $usersAgreement = $this->UsersAgreements->get($id, [
            'contain' => ['Users', 'Projects', 'Documents'],
        ]);

        $this->set(compact('usersAgreement'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $usersAgreement = $this->UsersAgreements->newEmptyEntity();
        if ($this->request->is('post')) {
            $usersAgreement = $this->UsersAgreements->patchEntity($usersAgreement, $this->request->getData());
            if ($this->UsersAgreements->save($usersAgreement)) {
                $this->Flash->success(__('The users agreement has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The users agreement could not be saved. Please, try again.'));
        }
        $users = $this->UsersAgreements->Users->find('list', ['limit' => 200])->all();
        $projects = $this->UsersAgreements->Projects->find('list', ['limit' => 200])->all();
        $documents = $this->UsersAgreements->Documents->find('list', ['limit' => 200])->all();
        $this->set(compact('usersAgreement', 'users', 'projects', 'documents'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Users Agreement id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $usersAgreement = $this->UsersAgreements->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $usersAgreement = $this->UsersAgreements->patchEntity($usersAgreement, $this->request->getData());
            if ($this->UsersAgreements->save($usersAgreement)) {
                $this->Flash->success(__('The users agreement has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The users agreement could not be saved. Please, try again.'));
        }
        $users = $this->UsersAgreements->Users->find('list', ['limit' => 200])->all();
        $projects = $this->UsersAgreements->Projects->find('list', ['limit' => 200])->all();
        $documents = $this->UsersAgreements->Documents->find('list', ['limit' => 200])->all();
        $this->set(compact('usersAgreement', 'users', 'projects', 'documents'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Users Agreement id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $usersAgreement = $this->UsersAgreements->get($id);
        if ($this->UsersAgreements->delete($usersAgreement)) {
            $this->Flash->success(__('The users agreement has been deleted.'));
        } else {
            $this->Flash->error(__('The users agreement could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
