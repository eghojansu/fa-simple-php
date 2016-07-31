<?php

// configuration file

return [
    // application name
    'name' => 'Application Name',

    // application alias name
    'alias' => 'AN',

    // application description
    'desc' => 'Application description',

    // owner
    'owner' => 'Somebody',

    // below is system variables -----------------------------------------------

    // debug
    'debug'=>true,
    'continueOnDBError'=>false,

    // template
    'template' => 'default',

    // session name
    'session' => 'Fa-SP',

    // image types
    'imageTypes' => [
        'image/jpeg','image/jpg','image/png'
    ],

    // user level
    'userLevel' => [
        'User'=>'user',
        'Admin'=>'admin',
    ],
];