<?php
$index = fopen("../../index.php", "c") or die("Unable to open file!");
if(!ftruncate($index, 0)){
  die("\033[31mERR: Truncate Failed\033[0m\n");
}
$app = "<?
\$folders = [
  'Providers',
  'Controllers',
  'Prism'
];
foreach(\$folders as \$folder){
  foreach(glob(\$folder.'/*.php') as \$file){
    include \$file;
  }
}
include 'config.php';
\$response = Prism\Router::enable();
if(\$_GET['REQUEST_TYPE'] === 'view'){
  \$response;
} else if(\$_GET['REQUEST_TYPE'] === 'api'){
  print \$response;
}";

fwrite($index, $app);
fclose($index);
exit(" \nPHP Compiled Successfully. New index is ready!\n\n");
