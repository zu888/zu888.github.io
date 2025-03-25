<?php
/**
 * CHECK-IN SCRIPT
 *
 * @property \App\Model\Table\CheckinsTable $Checkins
 */

use Cake\Datasource\FactoryLocator;
use Cake\I18n\FrozenTime;
use Cale\Mailer\Email;

$checkInsList = FactoryLocator::get('Table')->get('Checkins')->find()->where(['checkout_datetime' => NULL]);
//$checkIns = $this->Checkins->find()->where(['checkout_datetime' => NULL]);
//$this->set('checkins', $checkIns);

$currentDateTime = FrozenTime::now();
$currentDateTime->i18nFormat('y-MM-dd H:i:s');
$maxHours = 8;
$maxSeconds = 15;
//$checkOutLimitTime = $checkIns->checkin_datetime->add(new DateInterval("PT{$maxHours}H"));
$checkOutLimitTime = $checkInsList->checkin_datetime->add(new DateInterval("PT{$maxSeconds}s"));

foreach($checkInsList as $checkIn){
    if($currentDateTime >=  $checkOutLimitTime){
        //mail("clintonbao@gmail.com", "Please Sign out", "You have not checked out, please do so now" );
        $email = new Email('default');
        $email->setFrom(['sbao0004@student.monash.edu'=> 'Cosmic Properties'])
            -> setTo('sbao0004@student.monash.edu')
            -> setSubject('Please Sign Out')
            -> send('You have not signed out. Please do so immediately');
    }
}
