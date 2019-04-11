<?php
### Запрашиваемый файл должен определять переменную $rsstext

$rsstext='<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0"
xmlns:content="http://purl.org/rss/1.0/modules/content/"
xmlns:dc="http://purl.org/dc/elements/1.1/"
xmlns:media="http://search.yahoo.com/mrss/"
xmlns:atom="http://www.w3.org/2005/Atom"
xmlns:georss="http://www.georss.org/georss">
<channel>
<title>'.htmlspecialchars($VARS["sitename"]).'</title>
<link>http://'.$GLOBAL["host"].'</link>
<description>'.htmlspecialchars($VARS["sitename"]).'</description>
<lastBuildDate>'.date("r").'</lastBuildDate>
<language>ru</language>';
// <yandex:logo>http://'.$GLOBAL["host"].'/template/yandexrsslogo.png</yandex:logo>
// <yandex:logo type="square">http://'.$GLOBAL["host"].'/template/yandexrsslogo.png</yandex:logo>
// <image>
//   <url>http://'.$GLOBAL["host"].'/template/yandexrsslogo.png</url>
//   <title>'.htmlspecialchars($VARS["sitename"]).'</title>
//   <link>http://'.$GLOBAL["host"].'</link>
// </image>

$q="";
foreach($tables as $table) {
    $tmp=explode("_", $table); $link=$tmp[0];
    if ($link!="ls" && $link!="afisha") {
        $q.="(SELECT `$table`.`id`, `$table`.`name`, `$table`.`text`, `$table`.`data`, `$table`.`pic`, `_pages`.`link` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link' WHERE (`$table`.`stat`='1' && `$table`.`promo`!='1' && `$table`.`zenyandex`='1') GROUP BY 1) UNION ";
    }
}
$datat=DB(trim($q, "UNIO ")." ORDER BY `data` DESC LIMIT 20");
$remove_tags = ['script'];
for($it=0; $it<$datat["total"]; $it++) {
    @mysql_data_seek($datat["result"], $it);
    $at=@mysql_fetch_array($datat["result"]);
    $printed_text = preg_replace('@<('. implode('|', $remove_tags) .')\b.*?>.*?</\1>|[!--[a-zA-Z0-9\-]--]@si', '', $at['text']);
    $printed_text = html_entity_decode(strip_tags($printed_text));
    $printed_text = preg_replace(['@(&nbsp;|\s)+@', '@ +@'], ' ', $printed_text);
    $filetype = end(explode('.', $at['pic']));
    if( ! in_array($filetype, ['png', 'gif']) ) {
        $filetype = 'jpeg';
    }
    $rsstext.='
<item>
	<title>'.htmlspecialchars($at["name"]).'</title>
	<link>http://'.$GLOBAL["host"].'/'.$at["link"].'/view/'.$at["id"].'</link>
	<guid>http://'.$GLOBAL["host"].'/'.$at["link"].'/view/'.$at["id"].'</guid>
	<pubDate>'.date("r", $at["data"]).'</pubDate>';
    if ($at["pic"]!="") {
        $rsstext .= '
    <enclosure url="http://'.$GLOBAL["host"].'/userfiles/picoriginal/'.$at["pic"].'" type="image/'.$filetype.'" />';
    }
    $rsstext.='
    <content:encoded><![CDATA[
    ' . $printed_text . '
    ]]></content:encoded>
</item>';
}

$rsstext.='
</channel>
</rss>';
?>
