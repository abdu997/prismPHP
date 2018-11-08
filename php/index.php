<?
// DEV INDEX
include 'config.php';
array_push(Prism\Config::$folders, 'Prism');
foreach(Prism\Config::$folders as $folder){
  foreach(glob($folder.'/*.php') as $file){
    include $file;
  }
}
print Prism\Router::enable();

