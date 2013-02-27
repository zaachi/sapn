<?php
include 'class/sapn.php';

//create new instance
$apns = new SAPN( SAPN::PRODUCTION );
//set certificate
$apns	->setCertificateFile('/PATH/TO/CERT/CERT.pem');

//add devices
$apns	->addDeviceToken('bf447b59acd20718565c763e2de11382a0db1dc15caec4899fefd9790d0422a4')
		->addDeviceToken('bf447b59acd20718565b753e2df11382a0db1dc17caec4899fefd9790d0422a4')
		->addDeviceToken('bac47b59acd20718565b763e2de11382a0db1dc17caec4899fefd9790d0422a5');
//set options
$apns	->setBadge(3)
		->setSound('default');
//set message
$apns	->setMessage('this is my push message 3');
//send message
$apns	->send();
?>