<?
// DEV INDEX
include 'config.php';
array_push($GLOBALS['folders'], 'Prism');
foreach($GLOBALS['folders'] as $folder){
  foreach(glob($folder.'/*.php') as $file){
    include $file;
  }
}
print Prism\Router::enable();
