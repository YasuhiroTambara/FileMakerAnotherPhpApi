<?php
require_once '../api.php';
$host = 'http://127.0.0.1:8080';
$API = new API($host);
//$API = new API($host,null,null,null);

function DBS($API){
  $_QUERYS = [
    '-dbnames' => true
  ];
  return $XML = $API->_execute($_QUERYS);
}
?><!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <style>body{font: 100% Meiryo UI,Meiryo,sans-serif;}</style>
</head>
<body>
<?=DBS($API) ?>
<script>
var rets = document.getElementsByTagName('data');
for(var i = 0; i < rets.length; i++){rets[i].style.display = 'block';}
 </script>
</body>
</html>