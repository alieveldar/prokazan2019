<?
$table = "_tags";
if ($start == "") {
    $start = 0;
    $dir[1] = 0;
}
$file = $table . "-" . $start . "." . $page . "." . $id;

#############################################################################################################################################
### Вывод списка новостей в категории
if ($start === 0) {
    $file = "_tags-cloud";
    if (RetCache($file) == "true") {
        list($tags, $cap) = GetCache($file, 0);
    } else {
        list($tags, $cap) = TagsCloud();
        SetCache($file, $tags, "");
    }
    $cap = "Теги публикаций";
    $Page["Content"] = $tags;
    $Page["Caption"] = $cap;
} ### Вывод списка новостей общий
else {
    $data = DB("SELECT `name` FROM `" . $table . "` WHERE (`id`='" . (int)$dir[1] . "') LIMIT 1");
    if ($data["total"] == 1) {
        @mysql_data_seek($data["result"], 0);
        $tag = @mysql_fetch_array($data["result"]);
        if (RetCache($file) == " true") {
            list($text, $cap) = GetCache($file);
        } else {
            list($text, $cap) = GetLentaList();
            SetCache($file, $text, $cap);
        }
        $Page["Content"] = $text;
        $Page["Caption"] = $cap;
    } else {
        $cap = "Тег не найден";
        $text = @file_get_contents($ROOT . "/template/404.html");
        $Page["Content"] = $text;
        $Page["Caption"] = $cap;
    }
}

#############################################################################################################################################

function GetLentaList()
{
    global $ORDERS, $VARS, $ROOT, $GLOBAL, $dir, $RealHost, $Page, $node, $UserSetsSite, $table, $tag, $C, $C20, $C10, $C25, $C15;
    $query = '';
    $orderby = $ORDERS[$node["orderby"]];
    $tables = array();
    $onpage = 30;
    $pg = $dir[2] ? $dir[2] : 1;
    $from = ($pg - 1) * $onpage;
    $onblock = 4;
		$tagblock = 0;
    $right_sections = getRightSectionsNews();

    $q = "SELECT `[table]`.`id`, `[table]`.`lid`, `[table]`.`name`, `[table]`.`data`, `[table]`.`comcount`, `[table]`.`pic`, '[link]' as `link`
	FROM `[table]` LEFT JOIN `_users` ON `_users`.`id`=`[table]`.`uid` WHERE (`[table]`.`stat`='1' && `[table]`.`tags` LIKE '%," . (int)$dir[1] . ",%')";
    $endq = "ORDER BY `data` DESC LIMIT " . $from . ", " . $onpage;
    $data = getNewsFromLentas($q, $endq);
    $text = '';

    for ($i = 0; $i < $data["total"]; $i++) {
        @mysql_data_seek($data["result"], $i);
        $ar = @mysql_fetch_array($data["result"]);
        $ar['link'] = '/' . $ar['link'] . '/view/' . $ar['id'] . '/';
        $text .= getCenterContent($ar);
        if ($data["total"] > ($i + 1)) {
            if (($i + 1) % $onblock == 0) {
                $text .= "<div class='banner2 hidden-mobile' id='Banner-6-" . (floor($i / $onblock) + 1) . "'></div>";
								$text .= "<div class='banner2 hidden-desktop' id='Banner-28-" . (floor($i / $onblock) + 1) . "'></div>";
								
								while( empty($right_sections[$tagblock]['news']['total']) &&
                   count($right_sections) > $tagblock ) {
										$tagblock++;
								}
								if( isset( $right_sections[$tagblock]['news']['total'] ) &&
										0 < $right_sections[$tagblock]['news']['total'] ) {
										$text .= '<div class="hidden-desktop slider-tags"><h3>' . $right_sections[$tagblock]['title'] . '</h3>';
										$text .= '<div id="sliderTags-'.$tagblock.'" class="owl-carousel">';
										mysql_data_seek($right_sections[ $tagblock ]['news']['result'], 0);
										while( $tagnews = mysql_fetch_assoc($right_sections[ $tagblock ]['news']['result']) ) {
												if ( strpos( $tagnews["link"], "ls" ) !== false ||
														 strpos( $tagnews["link"], "bubr" ) !== false ) {
														$rel = "target='_blank' rel='nofollow'";
												} else {
														$rel = "";
												}
												$time = date('H:i', $tagnews['data']);
												$date = date('d.m', $tagnews['data']);
												$safeTitle = str_replace('"', '&quot;', $tagnews['name']);
												if ( $tagnews["pic"] != "" ) {
														if(file_exists( $_SERVER['DOCUMENT_ROOT'] . "/userfiles/picsmnews/" . $tagnews["pic"] )) {
																$tagnews["pic"] = "/userfiles/picsmnews/" . $tagnews["pic"];
														} else {
																$tagnews["pic"] = "/userfiles/pictavto/" . $tagnews["pic"];
														}
														$tagnews['pic'] = 'http://prokazan.ru' . $tagnews['pic'];
												} else {
														$tagnews["pic"] = "";
												}
												$text .= <<<HTML
												<div class="slide">
														<div class="news-mid">
																<div class="news-mid__media-wrapper">
																		<img class="news-mid__picture" src="{$tagnews['pic']}" alt="{$safeTitle}">
																</div>
																<div class="news-mid__content">
																		<a class="news-mid__header" href="{$tagnews['link']}" {$rel} title="{$safeTitle}">{$tagnews['name']}</a>
																		<p class="news-mid__text">{$tagnews['lid']}</p>
																		<div class="news-mid__info">
																				<div class="news-mid__date">
																						<p class="news-mid__day">{$date}</p>
																						<p class="news-mid__time">{$time}</p>
																				</div>
																		</div>
																</div>
														</div>
												</div>
HTML;
										}
										$text .= '</div></div>';
								}
								$tagblock++;
            }
        }
    }


    $q = "SELECT `[table]`.`id` FROM `[table]` WHERE (`[table]`.`stat`='1' && `[table]`.`tags` LIKE '%," . (int)$dir[1] . ",%')";
    $endq = "";
    $data = getNewsFromLentas($q, $endq);
    $total = $data["total"];
    $text .= Pager2($pg, $onpage, ceil($total / $onpage), $dir[0] . "/" . $dir[1] . "/[page]");
    return (array($text, $tag['name']));
}

#############################################################################################################################################
