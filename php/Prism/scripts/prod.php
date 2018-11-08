<?php
$index = fopen("../../index.php", "c") or die("Unable to open file!");
if(!ftruncate($index, 0)){
  die("\033[31mERR: Truncate Failed\033[0m\n");
}
$folders = [
  '../../Providers',
  '../../Prism',
  '../../Controllers',
];
$app = "<?";
foreach($folders as $folder){
  foreach(glob($folder."/*.php") as $file){
    $app .= str_replace(["<?php", "<?", "?>"], "", php_strip_whitespace($file));
  }
}
$app .= "namespace Prism;include 'config.php';\$response = Router::enable();if(\$_GET['REQUEST_TYPE'] === 'view'){\$response;}elseif(\$_GET['REQUEST_TYPE'] === 'api'){print \$response;}?>";
fwrite($index, $app);
fclose($index);
exit(" \nPHP Compiled Successfully. New index is ready!\n\n");
