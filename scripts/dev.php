<?php
$index = fopen("../../index.php", "c") or die("Unable to open file!");
if(!ftruncate($index, 0)){
  die("\033[31mERR: Truncate Failed\033[0m\n");
}
$app = "<?
// DEV INDEX
require_once 'config.php';
array_push(\$GLOBALS['folders'], 'Prism');
foreach(\$GLOBALS['folders'] as \$folder){
  foreach(glob(\$folder.'/*.php') as \$file){
    require_once \$file;
  }
}
print Prism\Router::enable();

";

fwrite($index, $app);
fclose($index);
exit(" \nPHP Compiled Successfully. New index is ready!\n\n");
