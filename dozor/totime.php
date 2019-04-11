<? session_start();  header("Content-Type: text/html; charset=windows-1251"); date_default_timezone_set('Europe/Minsk');
	if (!isset($_SESSION['userlogin'])){$_SESSION['userlogin']="";} $dir=explode("/",$_SERVER["REQUEST_URI"]); 
	$DataBaseName="admin_dozor";  $DataBaseLogin="admin_prokazan";  $DataBasePass="gnsQXSJgXNou";@mysql_connect("mysql.local", $DataBaseLogin, $DataBasePass); 
	@mysql_select_db($DataBaseName); mysql_query("set character_set_client='cp1251'"); mysql_query("set character_set_results='cp1251'");  mysql_query("set collation_connection='cp1251_general_ci'"); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><head><title>DozoR RNTI</title><meta http-equiv="Content-Type" content="text/html; 
charset=Windows-1251" /><link rel="stylesheet" type="text/css" href="style.css" media="all" /></head><body>
<? @mysql_query("update dozor_user set  done='0', gps='', timeto='".time()."', timefrom='".time()."', usedtime='0'"); echo "Время установлено..."; ?></body></html>