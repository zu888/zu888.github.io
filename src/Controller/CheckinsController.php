<?php
declare(strict_types=1);

namespace App\Controller;
use App\Model\Entity\Checkin;
use Cake\Datasource\FactoryLocator;
use Cake\I18n\FrozenTime;
use DateInterval;
use Cake\Mailer\Mailer;

/**
 * Checkins Controller
 *
 * @property \App\Model\Table\CheckinsTable $Checkins
 * @method \App\Model\Entity\Checkin[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */

class CheckinsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index($project = null)
    {

        $currentUser = $this->request->getAttribute('identity');
        $this->Authorization->skipAuthorization();
        $builderId = $this->Projects->get($project)->builder_id;

        $associatedUserIds = $this->fetchTable('ProjectsUsers')->find()
            ->where(['project_id' => $project])
            ->extract('user_id')
            ->toArray();

        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId && !in_array($currentUser->id, $associatedUserIds)) {
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
        }

        $key = $this->request->getQuery('key');
        $type = $this->request->getQuery('type');
        $target_date_time = $this->request->getQuery('date');


        $maxHours = 8;

        $this->paginate = [
            'contain' => ['Projects', 'Users'],
        ];

        $project = FactoryLocator::get('Table')->get('Projects')->get($this->request->getQuery('project'));
        $checkins = FactoryLocator::get('Table')->get('Checkins')->find();
        $checkins->select([
            'id',
            'checkin_datetime',
            'checkout_datetime',
            'user_id',
            'fname' => 'u2.first_name',
            'lname' => 'u2.last_name',
            'full_name' => 'CONCAT(u2.first_name, u2.last_name)',
            'role' => 'u2.role',
        ])->join([
            "table" => "users u2",
            "type" => "LEFT",
            "conditions" => "Checkins.user_id = u2.id"
        ])->where([
            'Checkins.project_id' => $project->id
        ]);


        $currentDateTime = FrozenTime::now();
        $currentDateTime->i18nFormat('y-MM-dd H:i:s');

        $search_words = 0;
        if ($key){$search_words = $key;}

        if ($type == 0){
            if ($key) {$checkins->find('all')->where(['companies.name like' => '%' . $key . '%' ] );}
        } elseif ($type == 1){
            $key = str_replace(' ', '', $key);
            if ($key) {$checkins->find('all')->where(['concat(u2.first_name, u2.last_name) like' => '%' . $key . '%'] );}
        }

        if ($this->request->getQuery('type') && $search_words == 0){
            $this->Flash->error(__('Please enter search terms.'));
        }

        if($target_date_time){
            $target_date_time = str_replace('/','-', $target_date_time);
            $checkins->find('all')->where(['checkin_datetime like' => $target_date_time . '%'] );
        }

        $checkins = $this->paginate($checkins);
        $this->set(compact('checkins', 'currentDateTime', 'maxHours', 'project','search_words','type', 'target_date_time'));
    }
    public function checkin($project = null)
    {

        $currentUser = $this->request->getAttribute('identity');
        $this->Authorization->skipAuthorization();
        $projects = $this->fetchTable('Projects');

        $request = $this->request;

        $project = $request->getQuery('project');

        $builderId = $projects->get($project)->builder_id;

        $associatedUserIds = $this->fetchTable('ProjectsUsers')->find()
            ->where(['project_id' => $project])
            ->extract('user_id')
            ->toArray();

        // Check if the user is not an admin and not the builder, and their status for the project is not "Co-Manager"
        if ($currentUser->role != 'Admin' && $currentUser->id != $builderId) {
            // Find the user's status for the project
            $userStatus = $this->fetchTable('ProjectsUsers')->find()
                ->select(['status'])
                ->where([
                    'project_id' => $project,
                    'user_id' => $currentUser->id,
                ])
                ->first();

            // Check if the user's status is not "Co-Manager"
            if ($userStatus->status != 'Co-Manager') {
                $this->Flash->error(__('You do not have permission to access this page.'));
                return $this->redirect(['controller' => 'Companies', 'action' => 'myindex']);
            }
        }


        $key = $this->request->getQuery('key');
        $type = $this->request->getQuery('type');
        $target_date_time = $this->request->getQuery('date');


        $maxHours = 8;

        $this->paginate = [
            'contain' => ['Projects', 'Users'],
        ];

        $project = FactoryLocator::get('Table')->get('Projects')->get($this->request->getQuery('project'));
        $checkins = FactoryLocator::get('Table')->get('Checkins')->find();
        $checkins->select([
            'id',
            'checkin_datetime',
            'checkout_datetime',
            'user_id',
            'fname' => 'u2.first_name',
            'lname' => 'u2.last_name',
            'full_name' => 'CONCAT(u2.first_name, u2.last_name)',
            'role' => 'u2.role',
        ])->join([
            "table" => "users u2",
            "type" => "LEFT",
            "conditions" => "Checkins.user_id = u2.id"
        ])->where([
            'Checkins.project_id' => $project->id
        ]);



        $currentDateTime = FrozenTime::now();
        $currentDateTime->i18nFormat('y-MM-dd H:i:s');

        $search_words = 0;
        if ($key){$search_words = $key;}
        if ($key){
            $key = str_replace(' ', '', $key);
            if ($key) {$checkins->find('all')->where(['concat(u2.first_name, u2.last_name) like' => '%' . $key . '%'] );}
        }

        if ($this->request->getQuery('type') && $search_words == 0){
            $this->Flash->error(__('Please enter search terms.'));
        }

        if (isset($_GET['date']) && $_GET['date'] !== '') {
            $target_date_time = $_GET['date'];
            $target_date_time = str_replace('/', '-', $target_date_time);
            $date = date('Y-m-d', strtotime($target_date_time));
            $checkins->find('all')->where(['checkin_datetime like' => $date . '%']);
        } else {
            $today = date('Y-m-d');
            $checkins->find('all')->where(['checkin_datetime like' => $today . '%']);
        }




        $checkins = $this->paginate($checkins);
        $this->set(compact('checkins', 'currentDateTime', 'maxHours', 'project','search_words','type', 'target_date_time'));
    }

    /**
     * View method
     *
     * @param string|null $id Checkin id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $checkin = $this->Checkins->get($id, [
            'contain' => ['Projects', 'Users'],
        ]);

        $this->Authorization->authorize($checkin);

        $this->set(compact('checkin'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // $this->Authorization->skipAuthorization();
        $checkin = $this->Checkins->newEmptyEntity();
        $this->Authorization->authorize($checkin);

        $currentUser = $this->request->getAttribute('identity');
        $projectsTable = $this->fetchTable('Projects');
        $project = $projectsTable->get($this->request->getQuery('project'));

        $associatedUserIds = $this->fetchTable('ProjectsUsers')->find()
            ->where(['project_id' => $project->id])
            ->extract('user_id')
            ->toArray();

        $existingCheckin = $this->Checkins->find()
            ->where([
                'user_id' => $currentUser->id,
                'DATE(checkin_datetime)' => date('Y-m-d'),
                'checkout_datetime IS NULL'
            ])
            ->first();

        if ($existingCheckin) {
            // Get the name of the project the user is checked into
            $checkedInProject = $projectsTable->find()
                ->select(['name'])
                ->where(['id' => $existingCheckin->project_id])
                ->first();

            if ($project->id != $existingCheckin->project_id) {
                $projectName = $checkedInProject ? $checkedInProject->name : 'Unknown Project';

                $this->Flash->error("You are already checked into the project: $projectName");
                return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
            }
        }

        if ($currentUser->role != 'Admin' && !in_array($currentUser->id, $associatedUserIds)) {
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
        }

        // $user_id = $this->request->getQuery('user_id');
        $inducted = FactoryLocator::get('Table')->get('Inductions')->find()->where([
            'user_id' => $currentUser->id, //  $user_id, //
            'project_id' => $project->id
        ])->first();
        // debug($project);
        // debug($inducted);
        // if ($inducted->inducted_date == NULL && $inducted->user_id == NULL){
        //     $this->Flash->error(__('You are not assigned to this project.'));
        //     return $this->redirect(['controller' => 'projects', 'action' => 'index']);
        // } elseif ($inducted->inducted_date == NULL){
        //     $this->Flash->error(__('Please complete your induction before checking in.'));
        //     return $this->redirect(['controller' => 'signatures', 'action' => 'pending']);
        // }

        $checkout = FALSE;
        $maxHours = 8;
        $currentDateTime = FrozenTime::now();
        $currentDateTime->i18nFormat('y-MM-dd H:i:s');

        $checkoutsMissing = $this->Checkins->find()->where([
            'project_id' => $project->id, // 5
            'user_id' => $currentUser->id, // 28 //$user_id, //
            'checkout_datetime is' => NULL,
        ]);

        foreach ($checkoutsMissing as $checkoutMissing){
            //debug($checkoutMissing);
            $checkOutLimit = $checkoutMissing->checkin_datetime->add(new DateInterval("PT{$maxHours}H"));
            if($checkOutLimit > $currentDateTime) {
                $selectedCheckout = $checkoutMissing;
                $checkout = TRUE;
            }
        }

        if ($checkout){
            $selectedCheckout->checkout_datetime = date('Y-m-d H:i:s');
            // debug($selectedCheckout);
            // exit;
            if ($this->Checkins->save($selectedCheckout)) {
                // $this->Flash->success(__('Checkout successful.'));
                // return $this->redirect(['controller' => 'projects', 'action' => 'index']);
            }
        }

        if (!$checkout) {
            $checkin = $this->Checkins->patchEntity($checkin, $this->request->getData());
            $checkin->project_id = $project->id;
            $checkin->user_id = $currentUser->id; // $user_id;//
            $checkin->checkin_datetime = date('Y-m-d H:i:s');

            if ($this->Checkins->save($checkin)) {
                // $this->Flash->success(__('Checkin successful.'));

                // return $this->redirect(['controller' => 'projects', 'action' => 'index']);
            }else{
                $this->Flash->error(__('The checkin could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('checkin', 'project', 'checkout'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Checkin id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $checkin = $this->Checkins->get($id, [
            'contain' => [],
        ]);
        $this->Authorization->authorize($checkin);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $checkin = $this->Checkins->patchEntity($checkin, $this->request->getData());
            $checkin->checkout_datetime = date('Y-m-d H:i:s');
            if ($this->Checkins->save($checkin)) {
                $this->Flash->success(__('The checkin has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The checkin could not be saved. Please, try again.'));
        }
        $projects = $this->Checkins->Projects->find('list', ['limit' => 200])->all();
        $users = $this->Checkins->Users->find('list', ['limit' => 200])->all();
        $this->set(compact('checkin', 'projects', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Checkin id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $checkin = $this->Checkins->get($id);
        $this->Authorization->authorize($checkin);
        if ($this->Checkins->delete($checkin)) {
            $this->Flash->success(__('The checkin has been deleted.'));
        } else {
            $this->Flash->error(__('The checkin could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    public function reminders($id = null)
    {
        $this->Authorization->skipAuthorization();

        $checkInList = FactoryLocator::get('Table')->get('Checkins')->find();
        $checkInList->select(['id' => 'Checkins.id', 'projectid' => 'Checkins.project_id', 'user_id' => 'users.id', 'user_email' => 'users.email', 'user_firstName' => 'users.first_name', 'checkin_time' => 'Checkins.checkin_datetime', 'checkin_sent' => 'Checkins.email_sent'])
            ->join([
                "table" => "users",
                "type" => "LEFT",
                "conditions" => "user_id = users.id"
            ])->where([
                'checkout_datetime IS' => NULL
            ]);
        $currentDateTime = FrozenTime::now();
        $currentDateTime->i18nFormat('y-MM-dd H:i:s');
        $maxHours = 8;

            foreach($checkInList as $recipient) {
                $site = FactoryLocator::get('Table')->get('Projects')->get($recipient->projectid);
                $checkoutLimit = $recipient->checkin_time->add(new DateInterval("PT{$maxHours}H"));
                if($currentDateTime > $checkoutLimit && $recipient -> checkin_sent != 1) {

                $mailer = new Mailer('default');
                $mailer
                    ->setEmailFormat('html')
                    ->setFrom(['sitex_noreply@u22s1010.monash-ie.me' => 'SiteX [No Reply]'])
                    ->setTo($recipient->user_email)
                    ->setSubject('SiteX: Reminder to check out of site')
                    ->viewBuilder()
                    ->setTemplate('checkoutreminder');

                $mailer->setViewVars([
                    'email' => $this->request->getData('email'),
                    'name' => $recipient->first_name,
                    'site' => $site->name,
                ]);

                // Deliver mail
                if ($mailer->deliver()) {
                    $this->Flash->success(__('Reminder has been sent.'));
                    $recipient -> email_sent = 1;
                    $this->Checkins->save($recipient);

                } else {
                    $this->Flash->error(__('Failed to send reminder email.'));
                }
            }
        }
        $this->redirect(['controller' => 'users', 'action' => 'login']);
    }
}
