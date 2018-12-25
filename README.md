# PrismPHP
PrismPHP is a framework dedicated to be a project setup solution. It includes a view/api router, a MySQL database switchboard, data sanitizers and validators, a PHPMailer shortcut, a dev/prod project bundler and an error log recorder.

## Requirements
- PHP 7.2 =<
- Apache 2.4.29 =<
- MySQL
- Unix-Like Operating System

## Installation
To create a primsPHP project, clone the master branch into a folder in the LAMP enviroment of your choice. Initally, the dev bundler would be the default bundler.

In order to have prismPHP running smoothly, you'll need to have `php/index.php`. If you have lost your `index.php`, you can reset it by running the dev bundler.

You will also need to have these Apache .htaccess rules set in the root folder of your project. These are the key rules you will need.

```HTACCESS
RewriteEngine On
RewriteRule ^api/(.*) php/?route=api/$1 [NC,L]
```

You will need to use this rule for exemptions.

```HTACCESS
RewriteCond %{ENV:REDIRECT_STATUS} !=200
RewriteRule ^(.*) php/?route=view/$1 [NC,L]
```

## Config
The config.php file contains the key values that are needed to configure prismPHP to your liking. If you wish not to fill in some values, **Do not delete the key**; instead, assign it a null or an empty value.

### PHP Code Folders
There are a three default code folders you may use to sort your code
- **Controllers:** Code to be directly run from an api response.
- **Providers:** Internal code that can be invoked from the Controllers
```php
$GLOBALS['folders'] =  [
  'Providers',
  'Controllers',
  ...
];
```
If you would like to amend or append the folder structure, you may edit the array with the relative path to the folder from the `php` directory. This will fetch all `.php` files from the directory.

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
It is the developer's responsibility to ensure the conditions are unique; if they are not unique, it may result with the use of the wrong credentials.
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
Check which timezone name works for you, from the PHP docs website [here](http://php.net/manual/en/timezones.php). **This key cannot be null**
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
The SMTP method will increase the protocol's chances of success. It will also lower the chances of the message being filtered as spam.

### Twilio Config
prismPHP includes the Twilio PHP package that helps send automated SMS messages from your Twilio number. The following are the Twilio keys required to have prismPHP's Twilio extension working.
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
This array will hold all cross origin addresses that you wish to exempt for accessing project resources. Cross origin requests from hosts not exempted will receive a CORS error.

**Access Control Allow Credentials**
The second key would be `Access_Control_Allow_Credentials`. By default, it will be set to `true`. It must be activated if you wish to use PHP's native session persistence mechanisms.
```php
$GLOBALS['Access_Control_Allow_Credentials'] =  true;
```

**Auth Groups**
The third key is `auth_groups`. Authentication groups are eventually assigned to routes in order to create a layer of restriction. **If this is empty, you will get Access Denied**.
The `auth_ref` key holds the authentication group's name value. The `condition` key works similarily to the DB switch board condition. It is an expression. It is recommended the expression would result in a boolean, yet a defined non-zero output should suffice.

Below, are a few examples of what auth groups can look like. First, is a public group, by its nature it can be accessed without restriction; therefore the condition is always true, as long as the route belongs to this group.
The second auth group is a native callback that returns a boolean. The third, is a callback to a function you've written. **This must be an expression**

```php
$GLOBALS['auth_groups'] =  [
  [
    'auth_ref' => 'public',
    'condition' => true,
  ],
  [
    'auth_ref' => 'admin',
    'condition' => isset($_SESSION['admin_id]) === true,
  ],
  [
    'auth_ref' => 'partner',
    'condition' => SessionProvider\Session::notPartner() === false,
  ],
  ...
];
```

**API Routes**
API requests are requests where the address contains `/api/` (i.e `http://example.com/api/routeName`). Everything after `/api/` would be considered the route. `callback` is the Controller function that returns the API's response. `auth` is an array that contains the `auth_ref`s that are granted privileges to the route resource; you may have multiple `auth_ref`s in this array. `REQUEST_METHOD` must be GET, HEAD, POST, PUT, DELETE, CONNECT, OPTIONS or TRACE; if the HTTP request method header does not match the one defined by the route, the response will be `ERR 405 Method Not Allowed`.

API response `content-type` header is set to `application/json`.
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
Views requests are requests where the address contains `/view/` (i.e `http://example.com/view/viewName`). Everything after `/view/` would be considered the route. `filename` is the path to the UI file relative to `php/Views`. `auth` is an array that contains the `auth_ref`s that are granted privileges to the route resource; you may have multiple `auth_ref`s in this array.

Views response `content-type` header is set to `text/html`.
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
## Project Bundler
Essentially, the bundler is concatenator that places all of the project's code into the `php/index.php` folder. PrismPHP offers two bundlers. **You can only have one running at time**
### DEV Bundler
The dev bundler, concatenates the project's code, as is, into `php/index.php`. The dev bundler is built to help the developer bundle the code automatically on each run time. It preserves the line indexes of the scripts, so as to help the developer track bugs and errors accurately. To have the dev bundler running, run these commands.
```sh
$ cd path/to/php/prism/scripts
$ php dev.php
```
### Prod Bundler
The prod bundler works similar to the dev bundler. However, the prod bundler removes docs, comments and whitespaces from the scripts before concatenating. Generally, it minifies the code; hence, making bug tracking a much more difficult task. In addition, the prod bundler does not bundle automatically; so if you make changes to your scripts, you will have to run the bundler once more to have the changes reflected in future runtimes.

The greatest advantages of running the project using the prod bundler would be an increase in runtime efficiency. Requests fullfilled by the a prod bundled project can cut runtime by up to 50% for each request. In addition, the it can cut data usage by up to 50% as well.

To have the prod bundler running, run these commands.
```sh
$ cd path/to/php/prism/scripts
$ php prod.php
```
