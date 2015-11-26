<?php
require_once './ini.php';
require_once './connect.php';

if(strlen($_GET['keyvEat'])===0 || strlen($_GET['col'])===0){
  exit();
}

if($_GET['col']==='tName'){
  $col = 'Eat::tName';
}else if($_GET['col']==='iTime'){
  $col = 'Eat::iTime';
}

$param =
  // 
  '&occ=Eat' .
  // 
  '&crit=' . 'Eat::kp|' . $_GET['keyvEat'] .
  // 
  '&set='  . $col . '|' . $_GET['value'];
//'&get='.implode('\n', array('tFoo','nBar'));//値を取得するフィールド名

$rtn =$API->Perform('Setter',$param);
//$ret =$rtn['gtRet'];
$cov =$rtn['gnCov'];

echo $cov;