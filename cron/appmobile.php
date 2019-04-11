<?
#error_reporting(E_ALL | E_STRICT) ; ini_set('display_errors', 'On');
if ($GLOBAL["sitekey"]!=1) { $ROOT = $_SERVER['DOCUMENT_ROOT']; $GLOBAL["sitekey"] = 1; $now=time(); require_once($ROOT."/modules/standart/DataBase.php");	 } require_once("pclzip.lib.php"); $text="";

$lentas=array("news"=>"Новости Казани","auto"=>"Авто","sport"=>"Спорт","business"=>"Бизнес","afisha"=>"Афиша","ls"=>"Семья");
// === === === === === === === === === === === === === === === === === === === === === === === === === === === === === ===
### ПОГОДА
	$xml=simplexml_load_file("http://export.yandex.ru/bar/reginfo.xml?region=43"); $lvl=$xml->traffic->level; $icnt=$xml->traffic->icon; $txtt=$xml->traffic->hint; $temp=$xml->weather->day->day_part->temperature;
	$icnpa=$xml->weather->day->day_part->xpath('//image-v3'); $txtp=$xml->weather->day->day_part->weather_type; $icnp=$icnpa[0];
	$xml=simplexml_load_file("http://kovalut.ru/webmaster/xml-table.php?kod=1601"); $data=$xml->Central_Bank_RF; $day=$xml->Central_Bank_RF->USD->New->Digital_Date;
	$usd=$xml->Central_Bank_RF->USD->New->Exch_Rate; $euro=$xml->Central_Bank_RF->EUR->New->Exch_Rate;
	$text.="<weather><item><text><![CDATA[ Погода в Казани: <b>".$temp."</b>°C     Пробки: <b>".$lvl."</b>     <b>$</b>=<b>".round($usd,2)."</b> руб.     <b>€</b>=<b>".round($euro,2)."</b> руб. ]]></text></item></weather>\r\n";
	
	
### МЕНЮ
$text.="<menu>";
	$lentas=array("news"=>"Новости Казани","auto"=>"Авто","sport"=>"Спорт","business"=>"Бизнес","afisha"=>"Афиша","ls"=>"Семья");
	foreach ($lentas as $t=>$n) { $text.="<item><title>".$n."</title><link>newslist</link><id>".$t."</id></item>";  }
$text.="</menu>"."\r\n";

### ВЫБОРКА ВСЕХ НОВОСТЕЙ
$q=""; $news=array();

foreach ($lentas as $t=>$n) { $tab=$t."_lenta"; $q.="(SELECT `".$tab."`.`id`,'".$t."' as `link`,`".$tab."`.`name`,`".$tab."`.`lid`,`".$tab."`.`text`,`".$tab."`.`data`,`".$tab."`.`pic`, '1' as `tavto`,`".$tab."`.`likes`,`".$tab."`.`dislikes`,`".$tab."`.`seens`,`".$tab."`.`picauth`,`".$tab."`.`comcount` , `_users`.`nick` , `_users`.`avatar`, `_widget_video`.`text` as `video` FROM `".$tab."` LEFT JOIN `_users` ON `_users`.`id`=`".$tab."`.`uid` LEFT JOIN `_widget_video` ON `_widget_video`.`pid`=`".$tab."`.`id` AND `_widget_video`.`link`=`link` WHERE (`".$tab."`.`stat`=1 && `".$tab."`.`onind`=1 && `".$tab."`.`promo`=0)) UNION "; } $q=trim($q, "UNION ")." ORDER BY `data` DESC LIMIT 1"; $d=DB($q); for($i=0; $i<$d["total"]; $i++) { @mysql_data_seek($d["result"], $i); $ar=@mysql_fetch_array($d["result"]); $news[]=$ar; $ids=$ar["id"]; }

$q=""; foreach ($lentas as $t=>$n) { $tab=$t."_lenta"; $q.="(SELECT `".$tab."`.`id`,'".$t."' as `link`,`".$tab."`.`name`,`".$tab."`.`lid`,`".$tab."`.`text`,`".$tab."`.`data`,`".$tab."`.`pic`,`".$tab."`.`tavto`,`".$tab."`.`likes`,`".$tab."`.`dislikes`,`".$tab."`.`seens`,`".$tab."`.`picauth`,`".$tab."`.`comcount` , `_users`.`nick`, `_users`.`avatar`, `_widget_video`.`text` as `video` FROM `".$tab."` LEFT JOIN `_users` ON `_users`.`id`=`".$tab."`.`uid` LEFT JOIN `_widget_video` ON `_widget_video`.`pid`=`".$tab."`.`id` AND `_widget_video`.`link`=`link` WHERE (`".$tab."`.`stat`=1 && `".$tab."`.`promo`=0)) UNION "; } $q=trim($q, "UNION ")." ORDER BY `data` DESC LIMIT 300"; $d=DB($q); for($i=0; $i<$d["total"]; $i++) { @mysql_data_seek($d["result"], $i); $ar=@mysql_fetch_array($d["result"]); if ($ar["id"]!=$ids) { $news[]=$ar; }}

### ФОРМИРОВАНИЕ XML
$text.="<news>";
foreach($news as $ar) { 

$d=DB("SELECT `name`,`pic`,`text` FROM `_widget_pics` WHERE (`stat`=1 && `pid`='".$ar["id"]."' && `link`='".$ar["link"]."') ORDER BY `rate` ASC");
if ($d["total"]>0) { for($i=0; $i<$d["total"]; $i++): @mysql_data_seek($d["result"], $i); $ap=@mysql_fetch_array($d["result"]);
if ($ap["name"]!="") { $ar["text"].="<h3>".$ap["name"]."</h3>"; } $ar["text"].="<img src=\"http://prokazan.ru/userfiles/picintv/".$ap["pic"]."\">"; if ($ap["text"]!="") { $ar["text"].=$ap["text"]; } endfor; }

$rdata=ToRusData($ar["data"]);
$text.="<item>
<id>".$ar["id"]."</id>
<title><![CDATA[ ".nlbr($ar["name"])." ]]></title>
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

/* Добавление в ZIP */
$text="<items>\r\n".$text."\r\n</items>";
chmod('../appmobile.zip', 0777); $zip = new ZipArchive; $zip->open('../appmobile.zip'); $zip->addFromString('content.lvv', $text); chmod('../appmobile.zip', 0777); echo $text;


// === === === === === === === === === === === === === === === === === === === === === === === === === === === === === === 
function nlbr($text) { $text=str_replace(array("\t","\r","\n","&nbsp;", 'src="/userfiles'), array("","",""," ", 'src="http://prokazan.ru/userfiles'), $text); /*$text=htmlspecialchars($text, ENT_QUOTES);*/ return $text; }
function GetMobileVideo($text) { $text=preg_replace('~width="\d+"~', 'width="320"', $text); $text=preg_replace('~height="\d+"~', 'height="300"', $text);  $text=str_replace("<iframe ", '<iframe allowCrossDomainXHR="true" sandboxRoot="http://www.youtube.com/" documentRoot="app:/sandbox/" ', $text); return $text; }
function ToRusData($var) { $var = date("Y.m.d.H.i.s", $var); list($y, $m, $d, $h, $i, $s)=explode(".", $var); $data=$h.":".$i.", ".$d.".".$m.".".$y; return($data); }


?>