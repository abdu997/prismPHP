<?php
$folders = [
  'Providers',
  'Controllers',
  'Prism'
];
foreach($folders as $folder){
  foreach(glob($folder."/*.php") as $file){
    include $file;
  }
}
include "config.php";
$response = Prism\Router::enable();
if($_GET['REQUEST_TYPE'] === "view"){
  $response;
} else if($_GET['REQUEST_TYPE'] === "api"){
  print $response;
}
