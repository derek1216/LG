<?php
ob_start();
session_start();
//設定時區為亞洲時間
date_default_timezone_set('Asia/Taipei');
//資料庫資訊
define("ROOT", dirname(__FILE__));
$ROOT_PATH = ROOT;
require_once(ROOT."/include/db.class.php");
require_once(ROOT."/include/functions.inc.php");
mb_internal_encoding('UTF-8');
if ($_SERVER['HTTP_HOST'] == 'lg-airsolution-marathon.ptt.com.tw'){
	$db_username = "Rlgmarathon";
	$db_password = "MarAla1424";
	$db_database = "R_lgmarathon";
	$db_hostname = "DB.Server";
}else{
	$db_username = "2lgmarathon";
	$db_password = "LoMaRa1423";
	$db_database = "2_lgmarathon";
	$db_hostname = "DB.Server";
}
$db = new dbClass($db_username , $db_password , $db_database , $db_hostname);
$pic_folder = '/pic/';

header('Content-Type: application/json; charset=utf-8');
?>