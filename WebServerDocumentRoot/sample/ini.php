<?php
ini_set('log_errors',0);
ini_set('session_use_only_cookies',1);
ini_set('display_errors',1);////// 運用環境では 0
error_reporting(-1);

mb_language('uni');
mb_internal_encoding('UTF-8');
mb_http_input('auto');
mb_http_output('UTF-8');

function h($str){
  return htmlspecialchars($str, ENT_QUOTES,'utf-8');
}

function sanitize($arr){
  return
  is_array($arr) ?
  array_map('sanitize',$arr) :
  str_replace("\0",'',$arr);
}

//NULLバイト除去
if(isset($_GET)) $_GET = sanitize($_GET);
if(isset($_POST)) $_POST = sanitize($_POST);