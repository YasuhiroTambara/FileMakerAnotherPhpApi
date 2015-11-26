<?php
//共通の環境設定
require_once './ini.php';
//FileMakerサーバーとの通信を利用する準備
require_once './connect.php';

if(strlen($_GET['date'])===0){
  exit();
}

if($_GET['col']==='tMemo'){
  $col = 'Day::tMemo';
}else if($_GET['col']==='tFoo'){
  $col = 'Day::tFoo';
}

$param =
  //&occ : テーブルオカレンスの指定
  '&occ=Day' .
  //&crit : 検索条件を改行リストで指定
  '&crit=' . 'Day::dDate|' . $_GET['date'] .
  //&set : 値を更新（もしくは新規作成して入力）するフィールド名を改行リストで指定
  '&set='  . $col . '|' . $_GET['value'];

$rtn =$API->Perform('Setter',$param);
//対象レコードのJSONテキスト
//$ret =$rtn['gtRet'];
//対象レコード数
$cov =$rtn['gnCov'];

echo $cov;