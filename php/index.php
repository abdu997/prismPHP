<?
$folders = [
  'Providers',
  'Controllers',
  'Prism'
];
foreach($folders as $folder){
  foreach(glob($folder.'/*.php') as $file){
    include $file;
  }
}
include 'config.php';
print Prism\Router::enable();
