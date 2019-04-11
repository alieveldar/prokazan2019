<? session_start(); header("Content-Type: text/html; charset=windows-1251"); date_default_timezone_set('Europe/Minsk');
	if (!isset($_SESSION['userlogin'])){$_SESSION['userlogin']="";} $dir=explode("/",$_SERVER["REQUEST_URI"]); 
	$DataBaseName="admin_dozor";  $DataBaseLogin="admin_prokazan";  $DataBasePass="gnsQXSJgXNou";@mysql_connect("mysql.local", $DataBaseLogin, $DataBasePass); 
	@mysql_select_db($DataBaseName); mysql_query("set character_set_client='cp1251'"); mysql_query("set character_set_results='cp1251'");  mysql_query("set collation_connection='cp1251_general_ci'"); 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><head><title>DozoR RNTI</title><meta http-equiv="Content-Type" content="text/html; 
charset=Windows-1251" /><meta name="viewport" content="width=device-width"><link rel="stylesheet" type="text/css" href="style.css" media="all" /></head><body><?

$sqo=@mysql_query("SELECT * from dozor_order"); $total=@mysql_num_rows($sqo); $total1=$total; $zad=array(); for ($i=0 ; $i<$total; $i++): @mysql_data_seek($sqo,$i); $ar=@mysql_fetch_array($sqo); $id=$ar["id"]; $zadc[$id]=$ar["code"]; $zad[$id]=$ar["name"]; endfor;


$sqo=@mysql_query("SELECT * from dozor_user"); $total=@mysql_num_rows($sqo);
for ($i=0 ; $i<$total; $i++): @mysql_data_seek($sqo,$i); $ar=@mysql_fetch_array($sqo); $id=$ar["numb"]; $zadan=$zad[$id]; $code=$zadc[$id]; 

if ($ar["done"]==$total1) {
	$Page.="<h3>Ёкпипаж #".$ar["id"]." (пароль: $ar[pass]) / <b style='color:red;'>выполнил все задани€</b> <hr>";
} else {
	$Page.="<h2>Ёкпипаж #".$ar["id"]." (пароль: $ar[pass])</h2>¬ыполнено: <b style='color:green;'>".($ar["done"])."/".$total1."</b><br>";
	$Page.="«адание <b>#".$id." - Ђ".$zadan."ї</b> [код: $code] => ";
	$Page.="выполн€ют <b>".round((time()-$ar["timeto"])/60)." минут</b>, начали в <u>".date("H:i", $ar["timeto"])."</u><br><br>";
	if ($ar["gps"]!="" && $ar["gps"]!="49.105556,55.800556") { $Page.="<img src=\"http://static.maps.api.2gis.ru/1.0?zoom=15&size=500,250&markers=$ar[gps]\" style='width:100%; height:auto;'><br>"; }
	$Page.="<hr><br>";
}
endfor;

echo "<div class='Main' style='width:99%; overflow:hidden;'>".$Page."</div>";
echo "<script>setInterval(foo, 180000); function foo () { location.reload(); }</script>";  
?></body></html>
