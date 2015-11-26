<?php
require_once './api.php';

$host = 'http://127.0.0.1:8080';
$file = 'FOODWEB';
$acc = 'Admin';
$pas = 'Pass';

$API = new API($host,$file,$acc,$pas);