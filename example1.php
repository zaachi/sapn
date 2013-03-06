<?php
include 'class/sapn.php';

//create instance 
$apns = new SAPN( SAPN::DEVELOPMENT );
//add certificate
$apns	->setCertificateFile('/PATH/TO/CERT/CERT.pem');

//add devices
$apns	->addDeviceToken('bf447b59acd20718565b763e2de11382a0db1dc17caec4899fefd9790d0422a4')
		->addDeviceToken('bf447b59acd20718565b763e2de11382a0db1dc17caec4899fefd9790d0422a4')
		->addDeviceToken('bf447b59acd20718565b763e2de11382a0db1dc17caec4899fefd9790d0422a5');

//set options
$apns	->setBadge(3)
		->setSound('default')
		->setCertificatePassphrase('password');

//set message
$apns	->setMessage('this is my push message');

//set custom variables
$apns ->setCustomVariable('notificationId', 1);
      ->setCustomVariable('referenceId', 382);

//send message
$apns	->send();
?>