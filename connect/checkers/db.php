<?php
require_once '../launch.php';
$API = new API($_GET['host'],$_GET['db'],$_GET['acc'],$_GET['pas']);

function LAYS($API,$DB){
  //$API->_props['username'] = $ACC;
  //$API->_props['password'] = $PAS;
  $_QUERYS = [
    '-db' => $DB,
    '-layoutnames' => true
  ];
  return $XML = $API->_execute($_QUERYS);
}

function SCRIPTS($API,$DB){
  //$API->_props['username'] = $ACC;
  //$API->_props['password'] = $PAS;
  $_QUERYS = [
    '-db' => $DB,
    '-scriptnames' => true
  ];
  
  return $XML = $API->_execute($_QUERYS);
  //var_dump($XML);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <style>
  body{font: 100% Meiryo UI,Meiryo,sans-serif;}
  </style>
</head>
<body>
<p>__layouts__</p>
<?=LAYS($API,$_GET['db']) ?>
<hr>
<p>__scripts__</p>
<?=SCRIPTS($API,$_GET['db']) ?>
<script type="text/javascript">
var rets = document.getElementsByTagName('data');
for(var i = 0; i < rets.length; i++){rets[i].style.display = 'block';}
 </script>
</body>
</html>