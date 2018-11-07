<?php

namespace Prism;

/**
 * All router settings and protocols.
 *
 */
class Router
{
  /**
   * Router config settings. Starts session. Calls santization of post values. Lists allowed external hostnames. If an external request hostname is in the allowed hostnames list, then includes the origin in Access Control Allow Origin. Defines set error handler. Defines timezone.
   *
   */
  public static function config()
  {
    session_start();
    DB::sanitize();
    if(isset(apache_request_headers()['Origin']) && in_array(apache_request_headers()['Origin'], $GLOBALS['allowed_hostnames'])){
      header(
        "Access-Control-Allow-Origin: ".apache_request_headers()['Origin']
      );
    }
    date_default_timezone_set($GLOBALS['timezone']);
    if($_GET['REQUEST_TYPE'] === "api"){
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
   * Calls router config. Check if route exists. Loops through routes, if a match exists auth, group and method. The callback is then called and returned in JSON if it is an array, returned as a string if not.
   * @return mixed
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
      if(in_array($auth_group['auth_ref'], $route['auth']) && $auth_group['condition']){
        if($_GET['REQUEST_TYPE'] === "api"){
          error_log('Controllers\''.$route['callback']);
          $callback = call_user_func("Controllers\\".$route['callback']);
          http_response_code(200);
          if(is_array($callback)){
            return json_encode($callback);
          } else {
            return $callback;
          }
        } else if($_GET['REQUEST_TYPE'] === "view"){
          return require("Views/".$route['filename']);
        }
      }
    }
    return json_encode(['status' => 'error', 'message' => 'Access Denied']);
  }

  /**
   * Checks if current route exists, if not a 404 is returned and the operation is exited.
   *
   */
  public static function checkRouteExists()
  {
    if($_GET['REQUEST_TYPE'] === "api"){
      foreach($GLOBALS['api'] as $route){
        if($route['route'] === $_GET['route']){
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
    } else if($_GET['REQUEST_TYPE'] === "view"){
      foreach($GLOBALS['views'] as $view){
        if($view['route'] === $_GET['route']){
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
   * @param  int $errno
   * @param  string $errstr  message
   * @param  string $errfile file
   * @param  int $errline error line
   * @return string json array of the call status and result
   */
  public static function errorHandler($errno, $errstr, $errfile, $errline)
  {
    http_response_code(500);
    $errmsg = $errstr." Error on line ".$errline." in ".$errfile;
    print json_encode(
      [
        'status' => 'error',
        'message' => $errmsg
      ]
    );
    error_log($errmsg);
    $timestamp = Prism\DB::timestamp();
    $sql = "INSERT INTO php_errors(errno, errstr, errfile, errline, timestamp) VALUES('$errno', '$errstr', '$errfile', '$errline', '$timestamp')";
    mysqli_query(Prism\DB::connect(), $sql);
    exit();
  }
}
