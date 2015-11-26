<?php
require_once '../api.php';
$host = 'http://127.0.0.1:8080';
$file = $_GET['file'];
$acc = $_GET['acc'];
$pas = $_GET['pas'];
$API = new API($host,$file,$acc,$pas);

function PERFORM( $API, $DB, $script, $param = null ){
  $_QUERYS = [
    '-db' => $DB,
    '-lay' => 'Sys',
    '-script.prefind' => $script,
    '-script.prefind.param' => $param,
    '-findany' => TRUE
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

<?=PERFORM( $API, $_GET['file'], $_GET['script'], $_GET['param'] ) ?>

<script>
var rets = document.getElementsByTagName('data');
for(var i = 0; i < rets.length; i++){rets[i].style.display = 'block';}
 </script>
</body>
</html>