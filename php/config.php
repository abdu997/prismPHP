<?php

namespace Prism;

class Config
{
  // PHP backend code folders
  public static $folders = [
    'Providers',
    'Controllers'
  ];

  //Dependencies
  public static $dep = [

  ];

  //DB
  public static $db = [
    [
      'condition' => true,
      'servername' => 'localhost',
      'username' => 'root',
      'password' => '',
      'db' => ''
    ],
  ];

  public static $timezone = 'America/New_York';

  // Mail
  public static $logo_url = "";
  public static $email = "";
  public static $password = "";
  public static $from_name = "";
  public static $host = "";
  public static $website_url = "";

  // Twilio
  public static $sid = "";
  public static $token = "";
  public static $number = "";

  // Router
  public static $allowed_hostnames = [
    "http://example.com:4000",
  ];
  public static $Access_Control_Allow_Credentials = true;
  public static $auth_groups = [
    [
      'auth_ref' => 'public',
      'condition' => true,
    ],
  ];
  public static $api = [
    [
      'route' => 'public/test',
      'callback' => 'ExampleController::example',
      'auth' => ['public'],
      'REQUEST_METHOD' => 'GET'
    ],
  ];
  public static $views = [
    [
      'route' => 'public/hello',
      'filename' => 'hello_world.html',
      'auth' => ['public'],
    ]
  ];
}
