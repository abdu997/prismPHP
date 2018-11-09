# PrismPHP
PrismPHP is a framework dedicated to be a project setup solution. It includes a view/api router, a MySQL database switchboard, data sanitizer and validators, a PHPMailer shortcut, a dev/prod project bundler and an error log recorder. 

## Requirements 
- PHP 7.2 =<
- Apache 2.4.29 =<
- MySQL

## Config
The config.php file contains the key values that are needed to configure prismPHP to your liking. If you wish not to fill in some values, **Do not delete the key**; instead, assign it a null or an empty value.

### PHP Code Folders
There are a three default code folders you may use to sort your code
- **Controllers:** Code to be directly run from api response.
- **Providers:** Internal code that can be invoked from the Controllers
```php
$GLOBALS['folders'] =  [
  'Providers',
  'Controllers',
  ...
];
```
If you would like to amend or append the folder structure, you may edit the array with the relative path to the folder from the php directory. This will fetch all `.php` files from the directory.

### Add Dependancies
You can add the relative paths to your app dependancies by inserting them into the `dep` array. It is suggested that you would place third party dependencies in a `vendor` folder under `php`
```php
$GLOBALS['dep'] =  [
  "vendor/exlib/autoload.php"
  ...
];
```

### DB Conncetion Switch Board
You can have multiple MySQL connection credentials loaded on prismPHP. In order for prismPHP to be able to identify which MySQL credential should be used, it checks if the credential conditions are met.
The `connection` key can store a dynamic expression, the result of the expression determines whether the credentials corresponding to it will be used.
Although, it is recommended that the expression would result in a boolean, as long as the result is a defined object or a non-zero integer, the condition can be met and accepted.
It is the developers responsibility to ensure the conditions are unique, if they are not unique, it may result with the use of the wrong credentials.
```php
$GLOBALS['DB'] =  [
  [
    'condition' => $_SERVER['SERVER_NAME'] === "localhost",
    'servername' => 'localhost',
    'username' => 'root',
    'password' => '',
    'db' => 'dbname'
  ],
  ...
];
```
### Timezone Setup
The timezone key contains the value of the timezone you wish to use during the runtime of your system. It will be used for bug tracking and for the http response timestamp.
Check which timezone name you want from the PHP docs website [here](http://php.net/manual/en/timezones.php). **This key cannot be null**
```php
$GLOBALS['timezone'] =  'America/New_York';
```

### PHPMailer Config
PHPMailer `6.0.5` is included and used as a core dependancy in prismPHP. Along with it, comes a pre-designed email template. To have this feature working as it should, you will have to fill the required following keys.
```php
$GLOBALS['email'] =  "example@gmail.com"; // Address to send emails from
$GLOBALS['host'] =  "smtp.gmail.com"; // The host client of the sender email address
$GLOBALS['password'] =  "password"; // The email's password
$GLOBALS['from_name'] =  "Company Name"; // Name you wish to use to identify yourself in the sent emails

$GLOBALS['logo_url'] =  "http://example.com/logo"; // Your logo url to included in the header of the email message
$GLOBALS['primary_colour'] = "#000000"; // Header background and text colour
$GLOBALS['secondary_colour'] = "#FFFFFF"; // Message body background colour
```
The SMTP method will increase the protocol chances of success. It will also lower the chances of the message being filtered as spam.

### Twilio Config
prismPHP includes the Twilio PHP package that helps you send automated SMS messages from your Twilio number. The following are the Twilio keys required to have prismPHP's Twilio extension to work.
```php
$GLOBALS['sid'] =  ""; // Twilio account sid
$GLOBALS['token'] =  ""; // Twilio account token
$GLOBALS['number'] =  ""; // Twilio phone number
```

### PrismPHP View/API Router
The prismPHP router is equipped to handle your app's HTTP requests. 

**Cross Origin hostnames**
The first key that is offered by this feature is `allowed_hostnames`.
```php
$GLOBALS['allowed_hostnames'] =  [
  "http://example.com:4000",
  ...
];
```
This array will hold all cross origin address that you wish to exempt for accessing project resources. Cross origin requests from hosts not exempted will recieve a CORS error.

**Access Control Allow Credentials**
The second key would be `Access_Control_Allow_Credentials`. By default, it will be set to `true`. It would need to be activated if you wish to use PHP native session persistence mechanisms.
```php
$GLOBALS['Access_Control_Allow_Credentials'] =  true;
```

**Auth Groups**
The third key is `auth_groups`. Authentication groups are eventually assigned to routes in order to create a layer of restriction. **If this is empty, you will get Access Denied**. 
The `auth_ref` key holds the authentication group's name value. The `condition` key works similarily to the DB switch board condition. It is can be an expression. It is recommended the expression would result in a boolean, yet a defined non-zero output should suffice.

Below, are a few examples of what auth groups can look like. First, is a public group, by its nature it can be accessed without restriction; therefore the condition is always true, as long as the route belongs to this group. 
The second auth group is a native callback that returns a boolean. The third, is a callback to function you've written.

```php
$GLOBALS['auth_groups'] =  [
  [
    'auth_ref' => 'public',
    'condition' => true,
  ],
  [
    'auth_ref' => 'admin',
    'condition' => isset($_SESSION['admin_id]),
  ],
  [
    'auth_ref' => 'partner',
    'condition' => SessionProvider\Session::isPartner(),
  ],
  ...
];
```

**API Routes**
API requests are requests where the address contains `/api/` (i.e `http://example.com/api/routeName`). Everything after `/api/` would be considered the route.
```php
$GLOBALS['api'] =  [
  [
    'route' => 'public/test', // The url here would look like -- http://example.com/api/public/test
    'callback' => 'ExampleController::example', // The callback must be within the Controllers namespace
    'auth' => ['public'], // This an array for a reason, you may have multiple auth groups for a route
    'REQUEST_METHOD' => 'GET' // If the HTTP request method does not match this, you will get an ERR 405
  ],
  ...
];
```

**Views Routes**
API requests are requests where the address contains `/view/` (i.e `http://example.com/view/routeName`). Everything after `/view/` would be considered the route.
```php
$GLOBALS['views'] =  [
  [
    'route' => 'public/hello', // The url here would look like -- http://example.com/view/public/hello
    'filename' => 'hello_world.html', // this can be a path relative to the php/Views folder
    'auth' => ['public'], // This an array for a reason, you may have multiple auth groups for a route
  ],
  ...
];
```








