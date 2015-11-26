<?php
require_once './ini.php';
require_once './connect.php';

if(strlen($_GET['keyv'])===0){
  exit();
}

$param =
  // ターゲットのテーブルオカレンス
  '&occ=Eat' .
  // フィルタ条件のフィールド名と値の組み合わせ
  '&crit='  . 'Eat::kp|' . $_GET['keyv'] .
  // 取得するフィールド名と値の組み合わせ
  '&get=' . implode('\n', ['kp', 'iTime']);

$rtn =$API->Perform('Del',$param);
$ret =$rtn['gtRet'];
$cov =$rtn['gnCov'];

echo $ret;
//echo $cov;