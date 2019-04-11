<?
#error_reporting(E_ALL | E_STRICT) ; ini_set('display_errors', 'On');
if ($GLOBAL["sitekey"]!=1) { $ROOT = $_SERVER['DOCUMENT_ROOT']; $GLOBAL["sitekey"] = 1; $now=time(); require_once($ROOT."/modules/standart/DataBase.php");	 } //require_once("pclzip.lib.php"); $text="";

$lentas=array("news"=>"Новости Казани","auto"=>"Авто","sport"=>"Спорт","business"=>"Бизнес","afisha"=>"Афиша","ls"=>"Семья");
$bignewsdata=$_GET["bignewsdata"]; $bignewsdata=preg_replace('/[^0-9]+/i', '', $bignewsdata);
// === === === === === === === === === === === === === === === === === === === === === === === === === === === === === ===
## ВЫБОРКА ВСЕХ НОВОСТЕЙ
$q=""; $news=array(); foreach ($lentas as $t=>$n) { $tab=$t."_lenta"; $q.="(SELECT `".$tab."`.`id`,'".$t."' as `link`,`".$tab."`.`name`,`".$tab."`.`lid`,`".$tab."`.`text`,`".$tab."`.`data`,`".$tab."`.`pic`,`".$tab."`.`tavto`,`".$tab."`.`likes`,`".$tab."`.`dislikes`,`".$tab."`.`seens`,`".$tab."`.`picauth`,`".$tab."`.`comcount` , `_users`.`nick`, `_users`.`avatar`, `_widget_video`.`text` as `video` FROM `".$tab."` LEFT JOIN `_users` ON `_users`.`id`=`".$tab."`.`uid` LEFT JOIN `_widget_video` ON `_widget_video`.`pid`=`".$tab."`.`id` AND `_widget_video`.`link`=`link` WHERE (`".$tab."`.`stat`=1 && `".$tab."`.`promo`=0 && `".$tab."`.`data`>'".$bignewsdata."')) UNION "; } $q=trim($q, "UNION ")." ORDER BY `data` DESC LIMIT 30"; $d=DB($q); for($i=0; $i<$d["total"]; $i++) { @mysql_data_seek($d["result"], $i); $ar=@mysql_fetch_array($d["result"]); $news[]=$ar;} if ($d["total"]==0) { echo 0; exit(); }

### ФОРМИРОВАНИЕ XML
$text.="<news>";
foreach($news as $ar) { 
	$d=DB("SELECT `name`,`pic`,`text` FROM `_widget_pics` WHERE (`stat`=1 && `pid`='".$ar["id"]."' && `link`='".$ar["link"]."') ORDER BY `rate` ASC");
	if ($d["total"]>0) { for($i=0; $i<$d["total"]; $i++): @mysql_data_seek($d["result"], $i); $ap=@mysql_fetch_array($d["result"]);
	if ($ap["name"]!="") { $ar["text"].="<h3>".$ap["name"]."</h3>"; } $ar["text"].="<img src=\"http://prokazan.ru/userfiles/picintv/".$ap["pic"]."\">"; if ($ap["text"]!="") { $ar["text"].=$ap["text"]; } endfor; }
	$rdata=ToRusData($ar["data"]);
$text.="<item>
<id>".$ar["id"]."</id>
<title><![CDATA[ ".nlbr(str_replace('"',"&quot;",$ar["name"]))." ]]></title>
<pic>$ar[pic]</pic>
<picshow>$ar[tavto]</picshow>
<picauth><![CDATA[ $ar[picauth] ]]></picauth>
<author><![CDATA[ $ar[nick] ]]></author>
<avatar>http://prokazan.ru/".$ar["avatar"]."</avatar>
<data>$ar[data]</data>
<rdata>$rdata</rdata>
<link>".$ar["link"]."</link>
<lid><![CDATA[ ".nlbr($ar["lid"])." ]]></lid>		
<text><![CDATA[ ".nlbr($ar["text"])." ]]></text>
<video><![CDATA[ ".GetMobileVideo($ar["video"])." ]]></video>
<seens>$ar[seens]</seens>
<like>$ar[likes]</like>
<dislike>$ar[dislikes]</dislike>
<com>$ar[comcount]</com>
</item>"."\r\n"; }
$text.="</news>";

$text="<items>\r\n".$text."\r\n</items>"; echo $text;

// === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === 
function nlbr($text) { $text=str_replace(array("\t","\r","\n","&nbsp;", 'src="/userfiles'), array("","",""," ", 'src="http://prokazan.ru/userfiles'), $text); /*$text=htmlspecialchars($text, ENT_QUOTES);*/ return $text; }
function GetMobileVideo($text) { $text=preg_replace('~width="\d+"~', 'width="100%"', $text); $text=preg_replace('~height="\d+"~', 'height="300px"', $text); return $text; }
function ToRusData($var) { $var = date("Y.m.d.H.i.s", $var); list($y, $m, $d, $h, $i, $s)=explode(".", $var); $data=$h.":".$i.", ".$d.".".$m.".".$y; return($data); }
?>