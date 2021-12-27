<?php

function s($variable) {
$s = Array();
$s['dbhost'] = "localhost"; // MySQL host
$s['dbname'] = "c1890273_cbaf"; // Database name
$s['dbuname'] = "c1890273_cbaf"; // Database Username
$s['dbpass'] = "49konuseZE";// Database password
return $s[$variable];
}

include_once "admin/ezsql/ez_sql_core.php";
include_once "admin/ezsql/ez_sql_mysqli.php";

$db = new ezSQL_mysqli(s('dbuname'),s('dbpass'),s('dbname'),s('dbhost'));
$db->query("SET NAMES 'utf8'");

function getCode(){
  return 'MugeG5FmVLGp4v';
}

function get_cuenta_mail_index($length){
  global $db;
  $cuenta=$db->get_var("SELECT cuenta_mail from enlaces order by id desc limit 1");
  $cuenta++;
  if($cuenta>=$length){
    $cuenta=0;
  }
  return $cuenta;
}
 ?>
