<?php
require_once '../api.php';
$host = 'http://127.0.0.1:8080';
/*
$file = $_GET['file'];
$acc = $_GET['acc'];
$pas = $_GET['pas'];
*/
$API = new API($host);
//$API = new API($host,null,null,null);
//$API = new API($host,$file,$acc,$pas);

function LAYS($API,$DB,$ACC,$PAS){
  $API->_props['acc'] = $ACC;
  $API->_props['pas'] = $PAS;
  $_QUERYS = [
    '-db' => $DB,
    '-layoutnames' => true
  ];
  return $XML = $API->_execute($_QUERYS);
  //var_dump($XML);
}

function SCRIPTS($API,$DB,$ACC,$PAS){
  $API->_props['acc'] = $ACC;
  $API->_props['pas'] = $PAS;
  $_QUERYS = [
    '-db' => $DB,
    '-scriptnames' => true
  ];
  return $XML = $API->_execute($_QUERYS);
  //var_dump($XML);
}
?><!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <style>
  body{font: 100% Meiryo UI,Meiryo,sans-serif;}
  </style>
</head>
<body>
<p>↓↓↓ layout names ↓↓↓</p>
<?=LAYS($API,$_GET['file'],$_GET['acc'],$_GET['pas']) ?>
<br>
<p>↓↓↓ script names ↓↓↓</p>
<?=SCRIPTS($API,$_GET['file'],$_GET['acc'],$_GET['pas']) ?>
<script>
var rets = document.getElementsByTagName('data');
for(var i = 0; i < rets.length; i++){rets[i].style.display = 'block';}
 </script>
</body>
</html>