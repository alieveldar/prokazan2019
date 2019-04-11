<?
#error_reporting(E_ALL | E_STRICT) ; ini_set('display_errors', 'On');
if ($GLOBAL["sitekey"]!=1) { $ROOT = $_SERVER['DOCUMENT_ROOT']; $GLOBAL["sitekey"] = 1; $now=time(); require_once($ROOT."/modules/standart/DataBase.php"); } $text="";
// === === === === === === === === === === === === === === === === === === === === === === === === === === === === === ===
$l=$_GET["link"];
$n=$_GET["name"];
$t=$_GET["text"];
$i=(int)$_GET["id"];

$l=preg_replace('/[^a-zA-Z0-9_\.\-]+/i', '', $l);
$t=str_replace("'", "&#039;", $t); $t=trim(strip_tags($t, "<b><i><u><q><p>")); $t=mysql_escape_string($t);
$n = trim(strip_tags($n)); $n=preg_replace('/[\.\(\)\;\:\-]+/i','',$n); echo $n;

DB("INSERT INTO `_comments` (`link`,`pid`,`uid`,`uname`,`from`,`data`,`toid`,`text`,`ip`,`referer`,`vkid`) VALUES ('".$l."', '".$i."', '0', '".$n."', 'mobile', '".time()."', '0', '".$t."', '".$_SERVER['REMOTE_ADDR']."', 'mobileapp', '');"); $last=DBL();
DB("UPDATE `".$l."_lenta` set `comcount`=`comcount`+1 WHERE (`id`='".$i."') LIMIT 1"); file_put_contents("appcomment.txt", $t);
?>