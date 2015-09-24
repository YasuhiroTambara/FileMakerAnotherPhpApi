<?php
require_once '../launch.php';
$API = new API($_GET['host'],null,null,null);

function DBS($API){
  $_QUERYS = [
   '-dbnames' => true
  ];
  return $XML = $API->_execute($_QUERYS);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <style>body{font: 100% Meiryo UI,Meiryo,sans-serif;}</style>
</head>
<body>
<?=DBS($API) ?>
<script type="text/javascript">
var rets = document.getElementsByTagName('data');
for(var i = 0; i < rets.length; i++){rets[i].style.display = 'block';}
 </script>
</body>
</html>