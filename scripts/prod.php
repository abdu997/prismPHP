<?php
$index = fopen("../../index.php", "c") or die("Unable to open file!");
if(!ftruncate($index, 0)){
  die("\033[31mERR: Truncate Failed\033[0m\n");
}
require_once '../../config.php';
$folders = $GLOBALS['folders'];
array_push($folders, 'Prism');
$app = "<?// PROD INDEX";
foreach($folders as $folder){
  foreach(glob("../../".$folder."/*.php") as $file){
    $app .= str_replace(["<?php", "<?", "?>"], "", file_get_contents($file));
  }
}
$app .= str_replace(["<?php", "<?", "?>"], "", file_get_contents("../../config.php"));
$app .= "namespace Prism{print Router::enable();}?>";
fwrite($index, $app);
fclose($index);
exit(" \nPHP Compiled Successfully. New index is ready!\n\n");
