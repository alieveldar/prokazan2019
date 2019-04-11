<?
### Запрашиваемый файл должен определять переменную $rsstext 
$rsstext = '<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0" xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/">
<channel>
<title>' . htmlspecialchars($VARS["sitename"]) . '</title>
<link>http://' . $_SERVER["HTTP_HOST"] . '</link>
<description>' . htmlspecialchars($VARS["sitename"]) . '</description>
<lastBuildDate>' . date("r") . '</lastBuildDate>
<yandex:logo>http://' . $_SERVER["HTTP_HOST"] . '/template/yandexrsslogo.png</yandex:logo>
<yandex:logo type="square">http://' . $_SERVER["HTTP_HOST"] . '/template/yandexrsslogo.png</yandex:logo>
<image>
  <url>http://' . $_SERVER["HTTP_HOST"] . '/template/yandexrsslogo.png</url>
  <title>' . htmlspecialchars($VARS["sitename"]) . '</title>
  <link>http://' . $_SERVER["HTTP_HOST"] . '</link>
</image>
<yandex:analytics id="7655743" type="Yandex"/>
<yandex:analytics id="UA-39062606-1" type="Google"/>
<yandex:analytics id="1684050" type="MailRu"/>
<yandex:analytics type="LiveInternet" params="ProKazan"/>';

$q = "";
foreach ($tables as $table) {
    $tmp = explode("_", $table);
    $link = $tmp[0];
    if ($link != "ls" && $link != "afisha") {
        $q .= "(SELECT `$table`.`id`, `$table`.`name`, `$table`.`lid`, `$table`.`text`, `$table`.`data`, `$table`.`pic`, `_pages`.`link` FROM `$table` LEFT JOIN `_pages` ON `_pages`.`link`='$link' WHERE (`$table`.`stat`='1' && `$table`.`promo`!='1' && `$table`.`yarss`='1') GROUP BY 1) UNION ";
    }
}
$datat = DB(trim($q, "UNION ") . " ORDER BY `data` DESC LIMIT 20");

for ($it = 0; $it < $datat["total"]; $it++) {
    @mysql_data_seek($datat["result"], $it);
    $at = @mysql_fetch_array($datat["result"]);
    $rawContent = $at['lid'] . '. ' . $at['text'];
    $content = preg_replace(['%<img.*?>|<script.*?</script>|<iframe.*?</iframe>%is', '%&nbsp;%', '%\s+%'], ['', ' ', ' '], $rawContent);
    $content = strip_tags($content);
    preg_match_all('%<img(?=.*src="([^"]*)")(?=.*alt="([^"]*)")[^>]*>%', $rawContent, $rawImages);
    $images = [];
    foreach ($rawImages[0] as $k => $i) {
        $tmpImage = ['src' => $rawImages[1][$k], 'alt' => $rawImages[2][$k]];
        $imgType = end(explode('.', $tmpImage['src']));
        if ($imgType === 'jpg') {
            $imgType = 'jpeg';
        } elseif (!in_array($imgType, ['jpeg', 'gif', 'png'])) {
            continue;
        }
        $tmpImage['type'] = 'image/' . $imgType;
        $images[] = $tmpImage;
        $figureImage = '<img src="' . $tmpImage['src'] . '"/>';
        if ($tmpImage['alt']) {
            $figureImage = '<figure>' . $figureImage . '<figcaption>' . $tmpImage['alt'] . '</figcaption></figure>';
        }
    }
    $rsstext .= '
<item turbo="true">
	<title>' . htmlspecialchars($at["name"]) . '</title>
	<link>http://' . $_SERVER["HTTP_HOST"] . '/' . $at["link"] . '/view/' . $at["id"] . '</link>
	<yandex:full-text>' . htmlspecialchars($content) . '</yandex:full-text>
	<pubDate>' . date("r", $at["data"]) . '</pubDate>';
    if ($at["pic"] != "") {
        $rsstext .= '
	<enclosure url="http://' . $_SERVER["HTTP_HOST"] . '/userfiles/picpreview/' . $at["pic"] . '" type="image/jpeg" />';
    }
    foreach ($images as $image) {
        $rsstext .= '
	<enclosure url="http://' . $_SERVER["HTTP_HOST"] . $image['src'] . '" type="' . $image['type'] . '" />';
    }
    $rsstext .= '
</item>';
}

$rsstext .= '
</channel>
</rss>';
?>
