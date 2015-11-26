<?php
require_once './ini.php';
require_once './connect.php';

if(strlen($_GET['date'])===0){
  exit();
}

$param =
  // ターゲットのテーブルオカレンス
  '&occ=Eat' .
  // セットするフィールド名と値の組み合わせ
  '&set='  . implode('\n', [
    'Eat::dDate|' . $_GET['date'],
    'Eat::iTime|' . $_GET['time']
  ]) .
  // 取得するフィールド名と値の組み合わせ
  '&get=' . implode('\n', ['kp', 'iTime']);

$rtn =$API->Perform('New',$param);
$ret =$rtn['gtRet'];
$cov =$rtn['gnCov'];

echo $ret;
//echo $cov;