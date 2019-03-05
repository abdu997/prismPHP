<?
// DEV INDEX
require_once 'config.php';
array_push($GLOBALS['folders'], 'Prism');
foreach($GLOBALS['folders'] as $folder){
  foreach(glob($folder.'/*.php') as $file){
    require_once $file;
  }
}
print Prism\Router::enable();
