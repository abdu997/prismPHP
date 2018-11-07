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
if($_GET['REQUEST_TYPE'] === "view"){
  Prism\Router::enable();
} else if($_GET['REQUEST_TYPE'] === "api"){
  print Prism\Router::enable();
}

// echo file_get_contents("Views/hello_world.php");
