<?php

namespace Prism;

/**
 * All router settings and protocols.
 *
 */
class Router
{
  /**
   * Router config settings. Starts session. Calls santization of post values.
   * Loads Dependencies. If an external request hostname is in the allowed
   * hostnames list from the prism config.php, then includes the origin in
   * Access Control Allow Origin. Defines set error handler. Defines timezone.
   * Changes response content type based on the request type.
   *
   */
  public static function config()
  {
    session_start();
    DB::sanitize();
    DB::createErrorLog();
    self::loadDep();
    if(isset(apache_request_headers()['Origin']) && in_array(apache_request_headers()['Origin'], $GLOBALS['allowed_hostnames'])){
      header(
        "Access-Control-Allow-Origin: ".apache_request_headers()['Origin']
      );
    }
    date_default_timezone_set($GLOBALS['timezone']);
    if(preg_match("/^api\/(.*)$/", $_GET['route'])){
      header("Content-type: application/json");
    } else {
      header("Content-type: text/html");
    }
    header("Access-Control-Allow-Credentials: ".$GLOBALS['Access_Control_Allow_Credentials']);
    header_remove("X-Powered-By");
    error_reporting(E_ALL);
    set_error_handler("Prism\Router::errorHandler");
  }

  /**
   * Adds prism core depenedancies to dep list from the prism config file.
   * Then requires all depenedancy files.
   *
   */
  private static function loadDep()
  {
    array_push(
      $GLOBALS['dep'],
      'Prism/vendor/twilio-php-master/Twilio/autoload.php',
      'Prism/vendor/PHPMailer/src/Exception.php',
      'Prism/vendor/PHPMailer/src/PHPMailer.php',
      'Prism/vendor/PHPMailer/src/SMTP.php'
    );
    foreach($GLOBALS['dep'] as $dep){
      require $dep;
    }
  }

  /**
   * Runs the router config and fetches the current route. If a router error
   * code exists, it is then set as the http response code and exits the
   * operation. Loops through the auth groups defined in the config. If the auth
   * group condition is not met and does not corresponde to the route auth group,
   * and access denied response is retuned. If the request type is api, the
   * callback from the controller is returned in a success json result array. If the request type is
   * view, the file contents from the Views folder is returned.
   *
   * @return string View or api response.
   */
  public static function enable()
  {
    self::config();
    $route = self::checkRouteExists();
    if(isset($route['error_code'])){
      http_response_code($route['error_code']);
      exit();
    }
    foreach($GLOBALS['auth_groups'] as $auth_group){
      if(in_array($auth_group['auth_ref'], $route['auth'])){
        if($GLOBALS['auth_groups']){
          if(preg_match("/^api\/(.*)$/", $_GET['route'])){
            $callback = call_user_func("Controllers\\".$route['callback']);
            http_response_code(200);
            return json_encode(
              [
                'status' => 'success',
                'result' => $callback
              ]
            );
          } else if(preg_match("/^view\/(.*)$/", $_GET['route'])){
            return file_get_contents("Views/".$route['filename']);
          }
        }
      }
    }
    return json_encode(['status' => 'error', 'message' => 'Access Denied']);
  }

  /**
   * If it is an api request, it loops through api routes to check if
   * the request method matches the route, if not, returns a 405. If so, the
   * route is returned. If it is a view request, it loops throught the views
   * routes, if so, the route is returned. If the route is not found, an error
   * 404 is returned.
   *
   * @return array error code or route info.
   */
  public static function checkRouteExists()
  {
    if(preg_match("/^api\/(.*)$/", $_GET['route'], $match)){
      foreach($GLOBALS['api'] as $route){
        if($route['route'] === $match[1]){
          if(
            $_SERVER['REQUEST_METHOD'] !== $route['REQUEST_METHOD']
            && $_SERVER['REQUEST_METHOD'] !== "OPTIONS"
          ){
            return [
              'error_code' => 405
            ];
          }
          return $route;
        }
      }
    } else if(preg_match("/^view\/(.*)$/", $_GET['route'], $match)){
      foreach($GLOBALS['views'] as $view){
        if($view['route'] === $match[1]){
          return $view;
        }
      }
    }
    return [
      'error_code' => 404
    ];
  }

  /**
   * The function thats called when an error occurs. In localhost, Internal Server Error 500 is returned with the callback and message. Message error logged. Else, error details recorded in database. Operation exited.
   *
   * @param  int    $errno
   * @param  string $errstr  message
   * @param  string $errfile file
   * @param  int    $errline error line
   * @return string          json array of the call status and result
   */
  public static function errorHandler($errno, $errstr, $errfile, $errline)
  {
    if($errno === 1024){
      $errmsg = $errstr;
    } else {
      http_response_code(500);
      $errmsg = $errstr." Error on line ".$errline." in ".$errfile;
      error_log($errmsg);
    }
    DB::query("INSERT INTO php_errors(errno, errstr, errfile, errline, timestamp) VALUES('?', '?', '?', '?', '?')", ['$errno', '$errstr', '$errfile', '$errline', '$timestamp']);
    print json_encode(
      [
        'status' => 'error',
        'message' => $errmsg
      ]
    );
    exit();
  }
}
