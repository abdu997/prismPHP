<?php
//Dependencies
$dep = [
  'vendor/twilio-php-master/Twilio/autoload.php',
  'vendor/PHPMailer/src/Exception.php',
  'vendor/PHPMailer/src/PHPMailer.php',
  'vendor/PHPMailer/src/SMTP.php'
];

//DB
$db = [
  [
    'condition' => $_SERVER['SERVER_NAME'] === "localhost",
    'servername' => 'localhost',
    'username' => 'root',
    'password' => '',
    'db' => ''
  ],
];

$timezone = 'America/New_York';

// Mail
$logo_url = "";
$email = "";
$password = "";
$from_name = "";
$host = "";
$website_url = "";

// Twilio
$sid = "";
$token = "";
$number = "";

// Router
$allowed_hostnames = [
  "http://example.com:4000",
];
$Access_Control_Allow_Credentials = true;
$auth_groups = [
  [
    'auth_ref' => 'public',
    'condition' => true,
  ],
];
$api = [
  [
    'route' => 'public/test',
    'callback' => 'ExampleController::example',
    'auth' => ['public'],
    'REQUEST_METHOD' => 'GET'
  ],
];
$views = [
  [
    'route' => 'public/hello',
    'filename' => 'hello_world.html',
    'auth' => ['public'],
  ]
];
