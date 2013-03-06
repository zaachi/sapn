SAPN
============

SIMPLE CLASS TO SEND PUSH IOS NOTIFICATION

HOW TO USE
--------------

*  create new instance

    ``$apns = new SAPN( SAPN::DEVELOPMENT );``

* add certificate

    ``$apns	->setCertificateFile('/PATH/TO/CERT/CERT.pem');``

* add device token

    ``$apns	->addDeviceToken('bf447b59acd20718565b763e2de11382a0db1dc17caec4899fefd9790d0422a4');``

* set custom variables

    ``$apns ->setCustomVariable('referenceId', 382);`` 


* set badge

    ``$apns->setBadge(3);``

* set sound

    ``$apns->setSound('default');``

* set Passphrase

    ``$apns->setCertificatePassphrase('password');``

* set puhs notificatoin message

    ``$apns	->setMessage('this is my push message');``
    
* send message 
    
    ``$apns	->send();``