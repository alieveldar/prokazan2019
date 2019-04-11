<?
#error_reporting(E_ALL | E_STRICT) ; ini_set('display_errors', 'On');
if ($GLOBAL["sitekey"]!=1) { $ROOT = $_SERVER['DOCUMENT_ROOT']; $GLOBAL["sitekey"] = 1; $now=time(); require_once($ROOT."/modules/standart/DataBase.php");	 } $text="";
// === === === === === === === === === === === === === === === === === === === === === === === === === === === === === ===
$link=$_GET["link"]; $id=(int)$_GET["id"]; $link=preg_replace('/[^a-zA-Z0-9_\.\-]+/i', '', $link);

$data=DB("SELECT `_comments`.`id`,`_comments`.`uid`, `_comments`.`text`, `_comments`.`data`, `_comments`.`uname`, `_users`.`nick`  FROM `_comments` LEFT JOIN `_users` ON `_users`.`id`=`_comments`.`uid` WHERE (`_comments`.`link`='".$link."' && `_comments`.`pid`='".(int)$id."') GROUP BY 1 ORDER BY `_comments`.`data` ASC");

for ($i=0; $i<$data["total"]; $i++) {
	@mysql_data_seek($data["result"],$i); $com=@mysql_fetch_array($data["result"]); $datar=ToRusData($com["data"]);
	if ($com["uid"]==0) { $com["nick"]=$com["uname"] ? $com["uname"] : "Горожанин"; }
	$text.='<div class="username"><b>'.$com["nick"].'</b>, '.$datar.'</div><div class="usertext">'.$com["text"].'</div>';
}

if ($data["total"]==0) { $text='<div class="usertext nocomment">Нет комментариев...</div>'; }

echo nlbr($text);

// === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === 
function nlbr($text) { $text=str_replace(array("\t","\r","\n","&nbsp;", 'src="/userfiles'), array("","",""," ", 'src="http://prokazan.ru/userfiles'), $text); $text=htmlspecialchars($text, ENT_QUOTES); return $text; }
function ToRusData($var) { $var = date("Y.m.d.H.i.s", $var); list($y, $m, $d, $h, $i, $s)=explode(".", $var); $data=$h.":".$i.", ".$d.".".$m.".".$y; return($data); }
?>