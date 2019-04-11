<?
### Запрашиваемый файл должен определять переменную $rsstext 

$rsstext='<?xml version="1.0" encoding="utf-8" ?><rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
<channel>
<atom:link href="http://'.$GLOBAL["host"].'/rss.xml" rel="self" type="application/rss+xml"/>
<title>'.$VARS["sitename"].'</title>
<link>http://'.$GLOBAL["host"].'</link>
<description>'.$VARS["sitename"].'</description>
<lastBuildDate>'.date("r").'</lastBuildDate>
<image>
  <url>http://'.$GLOBAL["host"].'/template/index/logo.png</url>
  <title>'.$VARS["sitename"].'</title>
  <link>http://'.$GLOBAL["host"].'</link>
</image>';


$table="economy_lenta"; $link="economy"; 
$q="SELECT * FROM `$table` WHERE (`stat`='1') ORDER BY `data` DESC LIMIT 10"; $datat=DB($q); 


for($it=0; $it<$datat["total"]; $it++) { @mysql_data_seek($datat["result"], $it); $at=@mysql_fetch_array($datat["result"]);
if ($at["pic"]!="") { $rsstexti='<enclosure url="http://'.$GLOBAL["host"].'/userfiles/picintv/'.$at["pic"].'" type="image/jpeg" />'; }
$text=str_replace(array("<li>","</p>","</div>","</li>","</ul>"), array("• ","<br />","<br />","<br />","<br />"), $at["lid"]);
$rsstext.='
<item>
	<title>'.$at["name"].'</title>
	<author>'.$GLOBAL["host"].'</author>
	<pubDate>'.date("r", $at["data"]).'</pubDate>
	<link>http://'.$GLOBAL["host"].'/'.$link.'/'.$at["cat"].'</link>
	'.$rsstexti.'
	<guid isPermaLink="true">http://'.$GLOBAL["host"].'/'.$link.'/'.$at["cat"].'/'.$at["id"].'</guid>
	<description><![CDATA['.$text.']]></description>';	
$rsstext.='
</item>';
}

$rsstext.='
</channel>
</rss>';
?>