<?
session_start();  header("Content-Type: text/html; charset=windows-1251");
$DataBaseName="admin_dozor";  $DataBaseLogin="admin_prokazan";  $DataBasePass="gnsQXSJgXNou";@mysql_connect("mysql.local", $DataBaseLogin, $DataBasePass); 
@mysql_select_db($DataBaseName); mysql_query("set character_set_client='cp1251'"); mysql_query("set character_set_results='cp1251'");  mysql_query("set collation_connection='cp1251_general_ci'"); 
if ($_SESSION['userlogin']!="") { @mysql_query("update dozor_user set gps='".$_GET["gps"]."' where (id='".$_SESSION['userlogin']."')"); }
?>