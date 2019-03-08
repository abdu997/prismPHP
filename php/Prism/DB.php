<?php

namespace Prism;

use MySQLi;

/**
 * Invloves all Database operations.
 *
 */
class DB
{
  private static $db;
  private $connection;

  /**
   * Adjusts the value of $connection based on which connection condition is met
   * from the DB prism config.php.
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

  /**
   * Creates a php errors table if it does not exist in the database.
   *
   */
  public static function createErrorLog()
  {
    $sql = "CREATE TABLE IF NOT EXISTS `prism_php_errors` (
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
   * Retrieves timestamp in UTC format.
   *
   * @return string Current timestamp
   */
  public static function timestamp()
  {
    return date("Y-m-d H:i:s", strtotime('now'));
  }

  /**
   * Added a second paramter option for sql query functions. The second paramter is an array that contains the query values. If the second paramter exists, it replaces each question mark in the query string with the corresponding value from the values array. Orindally replaces question marks in query string with sanitized or serialized values from the values array.
   *
   * @param  string $sql    SQL query string
   * @param  array  $values Array of values to be replaced into the query string
   * @return string         SQL query string
   */
  private static function queryBuilder($sql, $values)
  {
    if(!is_array($values)){
      return "Values must be an array";
    }
    $value_count = substr_count($sql, "?");
    for($x = 0; $x < $value_count; $x++){
      $value = $values[$x];
      if(is_array($value)){
        $value = serialize($value);
      }
      $value = mysqli_real_escape_string(self::connect(), $value);
      $sql = preg_replace("/\?/", $value, $sql, 1);
    }
    return $sql;
  }

  /**
   * Runs any sql query. If successful, a success response is returned. If the
   * query was unsuccessful, the mysqli_error is returned.
   *
   * @param  string $sql Sql query
   * @return array       Request status and message.
   */
  public static function query($sql, $values = null)
  {
    if($values){
      $sql = self::queryBuilder($sql, $values);
    }
    if(mysqli_query(self::connect(), $sql)){
      return true;
    } else {
      return false;
      trigger_error(mysqli_error(self::connect()), E_USER_ERROR);
    }
  }

  /**
   * Runs an insert sql query. If successful, the mysqli insert id is returned.
   * If the query was unsuccessful, the mysqli_error is returned.
   *
   * @param  string $sql Sql query
   * @return array       Request status and message.
   */
  public static function insert($sql, $values = null)
  {
    if($values){
      $sql = self::queryBuilder($sql, $values);
    }
    if(mysqli_query(self::connect(), $sql)){
      return mysqli_insert_id(self::connect());
    } else {
      return false;
      trigger_error(mysqli_error(self::connect()), E_USER_ERROR);
    }
  }

  /**
   * Runs a select sql query, preferably a select query. If successful,
   * result rows are then returned in an array schema. If the query was
   * unsuccessful, the mysqli_error is returned.
   *
   * @param  string $sql Sql query
   * @return array       Request status and message.
   */
  public static function select($sql, $values = null)
  {
    if($values){
      $sql = self::queryBuilder($sql, $values);
    }
    $result = mysqli_query(self::connect(), $sql);
    if($result){
      $output = [];
      while($row = mysqli_fetch_assoc($result)){
        $output[] = $row;
      }
      return $output;
    } else {
      return false;
      trigger_error(mysqli_error(self::connect()), E_USER_ERROR);
    }
  }

  /**
   * Runs a select sql query, preferably a select query. If successful,
   * a result row is then returned in an array schema. If the query was
   * unsuccessful, the mysqli_error is returned. Temporary loop variables are
   * unset as recommended
   *
   * @param  string $sql Sql query
   * @return array       Request status and message.
   */
  public static function selectOne($sql, $values = null)
  {
    if($values){
      $sql = self::queryBuilder($sql, $values);
    }
    $result = mysqli_query(self::connect(), $sql);
    if($result){
      $row = mysqli_fetch_assoc($result);
      foreach($row as $key => $value){
        if(preg_match("/a:(.*):/", $value)){
          $row[$key] = unserialize($value);
        }
      }
      unset($key, $value);
      return $row;
    } else {
      return false;
      trigger_error(mysqli_error(self::connect()));
    }
  }

  /**
   * Runs any sql query, preferably a update query. If successful, a success
   * response is returned. If the query was unsuccessful, the mysqli_error is
   * returned.
   *
   * @param  string $sql Sql query
   * @return array       Request status and message.
   */
  public static function update($sql)
  {
    return self::query($sql);
  }

  /**
   * Runs any sql query, preferably a delete query. If successful, a success
   * response is returned. If the query was unsuccessful, the mysqli_error is
   * returned.
   *
   * @param  string $sql Sql query
   * @return array       Request status and message.
   */
  public static function delete($sql)
  {
    return self::query($sql);
  }
}
