<?php

namespace Prism;

use MySQLi;

/**
 * Invloves all Database assets.
 *
 */
class DB
{
  private static $db;
  private $connection;

  /**
   * Adjusts the value of $connection based on the environment, as different environments have their own database credentials.
   *
   */
  private function __construct()
  {
    foreach($GLOBALS['DB'] as $connection){
      if($connection['condition']){
        $this->connection = new MySQLi($connection['servername'], $connection['username'], $connection['password'], $connection['db']);
        break;
      }
    }
  }

  public static function createErrorLog()
  {
    $sql = "CREATE TABLE IF NOT EXISTS `php_errors` (
      `record_id` int(11) NOT NULL AUTO_INCREMENT,
      `errno` int(11) NOT NULL,
      `errstr` varchar(100) NOT NULL,
      `errfile` varchar(100) NOT NULL,
      `errline` varchar(100) NOT NULL,
      `timestamp` varchar(30) NOT NULL,
      PRIMARY KEY (record_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
    mysqli_query(self::connect(), $sql);
  }

  public static function query($sql)
  {
    if(mysqli_query(self::connect(), $sql)){
      return ['status'=>'success'];
    } else {
      trigger_error(mysqli_error(self::connect()));
      return ['status'=>'error', 'result'=> mysqli_error(self::connect())];
    }
  }

  public static function insert($sql)
  {
    if(mysqli_query(self::connect(), $sql)){
      return mysqli_insert_id(self::connect());
    } else {
      trigger_error(mysqli_error(self::connect()));
      return ['status'=>'error', 'result'=> mysqli_error(self::connect())];
    }
  }

  public static function select($sql)
  {
    $result = mysqli_query(self::connect(), $sql);
    if($result){
      $output = [];
      while($row = mysqli_fetch_array($result)){
        $output[] = $row;
      }
      return $output;
    } else {
      trigger_error(mysqli_error(self::connect()));
      return ['status'=>'error', 'result'=> mysqli_error(self::connect())];
    }
  }

  public static function update($sql)
  {
    if(mysqli_query(self::connect(), $sql)){
      return ['status'=>'success'];
    } else {
      trigger_error(mysqli_error(self::connect()));
      return ['status'=>'error', 'result'=> mysqli_error(self::connect())];
    }
  }

  /**
   * Insantiates the databse function.
   *
   */
  public static function connect()
  {
    if(self::$db == null){
      self::$db = new DB();
    }
    return self::$db->connection;
  }

  /**
   * Sanitizes first and second dimension values of the post array.
   *
   */
  public static function sanitize()
  {
    foreach($_POST as $key => $value){
      if(is_array($value)){
        foreach($value as $sub_key => $sub_value){
          $value[$sub_key] = mysqli_real_escape_string(self::connect(), $value[$sub_key]);
        }
      } else {
        $_POST[$key] = mysqli_real_escape_string(self::connect(), $_POST[$key]);
      }
    }
  }

  public static function timestamp()
  {
    return date("Y-m-d H:i:s", strtotime('now'));
  }
}
