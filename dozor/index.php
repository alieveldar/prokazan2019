<?
session_start();  header("Content-Type: text/html; charset=windows-1251");
date_default_timezone_set('Europe/Minsk');
$vsegozadaniy=7; ### ������� �� ������� 
$totalzadan=7; ### ������� ������ ������ � ����� ���������
$timeafter1=30; $timeafter2=45;

if (!isset($_SESSION['userlogin'])){$_SESSION['userlogin']="";} $dir=explode("/",$_SERVER["REQUEST_URI"]); $DataBaseName="admin_dozor";  $DataBaseLogin="admin_prokazan";  $DataBasePass="gnsQXSJgXNou";
@mysql_connect("mysql.local", $DataBaseLogin, $DataBasePass); @mysql_select_db($DataBaseName); mysql_query("set character_set_client='cp1251'"); mysql_query("set character_set_results='cp1251'");  mysql_query("set collation_connection='cp1251_general_ci'"); 

if (isset($_POST["login"])) { unset($_POST['login']); $sql=@mysql_query("SELECT id, name from dozor_user where (id='".$_POST["ekipage"]."' && pass='".$_POST["pass"]."')");  $tot=@mysql_num_rows($sql); if ($tot!=1) { $msg="<div class='msr'>������ �������!</div>"; } else { mysql_data_seek($sql, 0); $ar=mysql_fetch_array($sql); $_SESSION['userlogin']=$ar["id"]; @header("location: /"); exit(); }}


if (isset($_POST["enter"])) { unset($_POST['enter']); 
	$c=$_POST['code']; $c=str_replace('um', '', $c);  $c=str_replace('UM', '', $c); $c=str_replace('MU', '', $c); $c=str_replace('mu', '', $c); $u=$_SESSION['userlogin']; 
	$sqm=@mysql_query("SELECT * from dozor_user where (id='".$_SESSION['userlogin']."')"); mysql_data_seek($sqm, 0); $am=mysql_fetch_array($sqm); $o=$am["numb"]; $dn=$am["done"];
	$sqo=@mysql_query("SELECT * from dozor_order where (id='$o') LIMIT 1");  mysql_data_seek($sqo, 0); $or=mysql_fetch_array($sqo); $rc=$or["code"];
	if ($rc!=$c) { $_SESSION["mes"] ="<div class='msr'>�������� ���!</div>"; header("location: /"); exit(); } else { 
		$nz=$or["id"]+1; $ndone=$dn+1; if ($nz>$vsegozadaniy && $ndone<$vsegozadaniy) { $nz=1; } if ($ndone==$vsegozadaniy) { $nz=$vsegozadaniy+1; }
		$_SESSION["mes"] ="<div class='ksg'>��� ������. ����� �������! $add (done=$ndone)</div>";
		@mysql_query("update dozor_user set done='$ndone', numb='$nz', timeto='".time()."' where (id='".$_SESSION['userlogin']."')");
		if ($ndone==$totalzadan) { @header("location: /done.php"); exit(); } else { @header("location: /"); exit(); }
	}
}





if ($_SESSION['userlogin']=="") {
	$Page="<div style='text-align:center;'><img src='/dozor.gif' style='width:100%; height:auto;'>
	<h1>����� �������</h1>$msg<form action='/' method='post'><div class='AuthBtn'><input name='ekipage' type='text' placeholder='����� �������'></div>
	<div class='AuthBtn'><input name='pass' type='text' placeholder='������ �������'></div><div class='AuthSub'><input name='login' type='submit' value='�����'></div></form></div>";
} else { 
	$msg=$_SESSION["mes"];
	$sql=@mysql_query("SELECT * from dozor_user where (id='".$_SESSION['userlogin']."')"); @mysql_data_seek($sql, 0); $ar=@mysql_fetch_array($sql);
	$raz=time()-$ar["timeto"]; $m=floor($raz/60); $s=$raz-($m*60); $oid=$ar["numb"]; $sqo=@mysql_query("SELECT * from dozor_order where (id='$oid')"); @mysql_data_seek($sqo, 0); $or=@mysql_fetch_array($sqo); if ($ar["done"]==$totalzadan) { @header("location: /done.php"); exit(); }
	$Page.="<div class='Done'>������� #".$ar["id"]." | <b>��������� �������: $ar[done] �� $totalzadan</b></div>";
	$Page.="<h2>�������</h2><div class='Text'>".nl2br($or["text1"])."</div>";
	#��������� �� �������
	if ($m>=$timeafter1) { $Page.="<h2>���������</h2><div class='Text'>".nl2br($or["text2"])."</div>"; };
	if ($m>=$timeafter2) { $Page.="<h2>������������</h2><div class='Text'>".nl2br($or["text3"])."</div>"; };  
	$Page.="<div class='TimeYet'>������� ���������� ".$m." �����(�)</div>";
	######################################################
	$Page.="<div style='color:green; margin:10px 0;'>������ ��������� ����� 30 ����� � ������ �������, ������ ����� 15 ����� ����� ������. ������� ����� 8917-232-56-64!</div>";
	######################################################
 	$Page.="<div style='text-align:center;'><h2>������� ���</h2>$msg<form action='/' method='post'><input type='hidden' name='order' value='$oid'><div class='AuthCod'><input name='code' type='text' placeholder='��� - 4 �����'></div><div class='AuthSub'><input name='enter' type='submit' value='���������'></div></form></div><div id='map' style='width:1px; height:1px; overflow:hidden;'></div>
	<script>setInterval(foo, 55000); function foo () { location.reload(); } if (navigator.geolocation) { navigator.geolocation.getCurrentPosition(function(position) { var lat=position.coords.latitude; var long=position.coords.longitude; var gps=long+','+lat; document.getElementById('map').innerHTML=\"<img src='/map.php?rand=".time()."&gps=\"+gps+\"' />\";}); }</script>";
}
$_SESSION["mes"]="";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head><title>DozoR UMEDIA</title>
<meta name="viewport" content="width=device-width">
<meta http-equiv="Content-Type" content="text/html; charset=Windows-1251" />
<link rel="stylesheet" type="text/css" href="style.css" media="all" />
</head>
<body><? echo "<div class='Main'>".$Page."</div>"; ?></body></html>