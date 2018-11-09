<?php
// PHP backend code folders
$GLOBALS['folders'] =  [
  'Providers',
  'Controllers'
];

//Dependencies
$GLOBALS['dep'] =  [

];

//DB
$GLOBALS['DB'] =  [
  [
    'condition' => $_SERVER['SERVER_NAME'] === "localhost",
    'servername' => 'localhost',
    'username' => 'root',
    'password' => '',
    'db' => ''
  ],
];

$GLOBALS['timezone'] =  'America/New_York';

// Mail
$GLOBALS['email'] =  "";
$GLOBALS['host'] =  "";
$GLOBALS['password'] =  "";
$GLOBALS['from_name'] =  "";

$GLOBALS['logo_url'] =  "";
$GLOBALS['primary_colour'] = "";
$GLOBALS['secondary_colour'] = "";



// Twilio
$GLOBALS['sid'] =  "";
$GLOBALS['token'] =  "";
$GLOBALS['number'] =  "";

// Router
$GLOBALS['allowed_hostnames'] =  [
  "http://example.com:4000",
];
$GLOBALS['Access_Control_Allow_Credentials'] =  true;
$GLOBALS['auth_groups'] =  [
  [
    'auth_ref' => 'public',
    'condition' => true,
  ],
];
$GLOBALS['api'] =  [
  [
    'route' => 'public/test',
    'callback' => 'ExampleController::example',
    'auth' => ['public'],
    'REQUEST_METHOD' => 'GET'
  ],
];
$GLOBALS['views'] =  [
  [
    'route' => 'public/hello',
    'filename' => 'hello_world.html',
    'auth' => ['public'],
  ]
];
