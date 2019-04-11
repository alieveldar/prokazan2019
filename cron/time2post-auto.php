<?
### Запрашиваемый файл должен определять переменную $rsstext 

$rsstext='<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:yandex="http://news.yandex.ru">
<channel>
<title>'.htmlspecialchars($VARS["sitename"]).'</title>
<link>http://'.$GLOBAL["host"].'</link>
<lastBuildDate>'.date("r").'</lastBuildDate>
';

$table="auto_lenta"; $tmp=explode("_", $table); $link=$tmp[0];
$q="SELECT `$table`.`id`, `$table`.`name`, `$table`.`soctext` as `text`, `$table`.`data`, `$table`.`pic`, `_pages`.`link` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link' WHERE (`$table`.`stat`='1' && `$table`.`tavto`='1' && `$table`.`promo`!='1' && `$table`.`soctext`!='') GROUP BY 1 ORDER BY `data` DESC LIMIT 20";

$datat=DB($q); for($it=0; $it<$datat["total"]; $it++) { @mysql_data_seek($datat["result"], $it); $at=@mysql_fetch_array($datat["result"]);
$rsstext.='
<item>
	<title>'.htmlspecialchars($at["name"]).'</title>
	<link>http://'.$GLOBAL["host"].'/'.$at["link"].'/view/'.$at["id"].'</link>   
	<description>'.htmlspecialchars($at["text"]).'
	
	Подробнее: http://'.$GLOBAL["host"].'/'.$at["link"].'/view/'.$at["id"].'</description>   
	<pubDate>'.date("r", $at["data"]).'</pubDate>';
if ($at["pic"]!="") { $rsstext.='
	<enclosure url="http://'.$GLOBAL["host"].'/userfiles/picintv/'.$at["pic"].'" type="image/jpeg" />'; }
$rsstext.='
</item>';
}

$rsstext.='
</channel>
</rss>';

?>