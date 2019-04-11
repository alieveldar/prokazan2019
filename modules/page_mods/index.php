<?php
if($_SESSION['full'] != 1)
	echo '<script type="text/javascript">
  if (screen.width < 800) {
    window.location = "http://prokazan.ru/kazan-news";
  }
</script>';
$yandex1='<!-- Яндекс.Директ --><div id="yandex1ad"></div><script type="text/javascript">(function(w, d, n, s, t) { w[n] = w[n] || []; w[n].push(function() { Ya.Direct.insertInto(125901, "yandex1ad", { ad_format: "direct", font_size: 0.8, type: "vertical", border_type: "block", limit: 3, title_font_size: 1, site_bg_color: "FFFFFF", header_bg_color: "CCCCCC", border_color: "CCCCCC", title_color: "0066CC", url_color: "006600", text_color: "000000", hover_color: "0066FF", no_sitelinks: true}); }); t = d.getElementsByTagName("script")[0]; s = d.createElement("script"); s.src = "//an.yandex.ru/system/context.js"; s.type = "text/javascript"; s.async = true; t.parentNode.insertBefore(s, t);})(window, document, "yandex_context_callbacks");</script>'.$C25;
$yandex2='<!-- Яндекс.Директ --><div id="yandex2ad"></div><script type="text/javascript">(function(w, d, n, s, t) { w[n] = w[n] || []; w[n].push(function() { Ya.Direct.insertInto(125901, "yandex2ad", { ad_format: "direct", font_size: 0.8, type: "vertical", border_type: "block", limit: 3, title_font_size: 1, site_bg_color: "FFFFFF", header_bg_color: "CCCCCC", border_color: "CCCCCC", title_color: "0066CC", url_color: "006600", text_color: "000000", hover_color: "0066FF", no_sitelinks: true}); }); t = d.getElementsByTagName("script")[0]; s = d.createElement("script"); s.src = "//an.yandex.ru/system/context.js"; s.type = "text/javascript"; s.async = true; t.parentNode.insertBefore(s, t);})(window, document, "yandex_context_callbacks");</script>'.$C25;

$src="http://prokazan.ru"; $file="_index-indexpage";
if(RetCache( $file, 'cacheblock' ) == "true") {
	list( $Page['TopContent'] ) = GetCache( $file.'_top', 0 );
	list( $Page['LeftContent'] ) = GetCache( $file.'_left', 0 );
	list( $Page['Content'] ) = GetCache( $file.'_content', 0 );
	list( $Page['RightContent'] ) = GetCache( $file.'_right', 0 );
} else {
	$Page['TopContent'] = getTopBlock();
	$Page['LeftContent'] = getLeftBlock();
	$Page['Content'] = getContentBlock();
	$Page['RightContent'] = getRightBlock();
	SetCache( $file.'_top', $Page['TopContent'], '' );
	SetCache( $file.'_left', $Page['LeftContent'], '' );
	SetCache( $file.'_content', $Page['Content'], '' );
	SetCache( $file.'_right', $Page['RightContent'], '' );
	SetCache( $file, '', '' );
}
$Page["Caption"]="";

function getRightBlock() {
	global $used, $C25, $VARS;
	$adv   = array();
	$list  = array();
	$cnt   = 1;
	$ban10 = 2;

	$social = '<div class="divider divider_margin-top-0 divider_bottom-space">';
	$text = '';
	$social_icons = ['social-vk' => 'vk', 'social-instagram' => 'insta'];
	foreach($social_icons as $social_var => $icon) {
		if( ! empty( $VARS[ $social_var ] )) {
			$social .= '
			<a href="' . $VARS[ $social_var ] . '" target="_blank" rel="nofollow"
			class="divider__icon divider__icon_' . $icon . '"></a>';
		}
	}
	$social .= '<span class="divider__text">подписывайся!</span></div>';
	$text = $social;
	$text .= '<h3 class="news-short-header">Новости <span class="news-short-header_black"> РОССИИ</span></h3>';


	$q = "SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`data`, '[link]' as `link` FROM `[table]` WHERE `[table]`.`stat`='1' && (`[table]`.`promo`=1 || `[table]`.`spromo`=1) && '[link]' !='ls' && `[table]`.`data`<'" . ( time() - 4 * 24 * 60 * 60 ) . "' && `[table]`.`data`>'" . ( time() - 7 * 24 * 60 * 60 ) . "'";
	$endq = "ORDER BY `data` DESC";
	$data = getNewsFromLentas( $q, $endq );
	for ( $i = 0; $i < $data["total"]; $i++ ) {
		@mysql_data_seek( $data["result"], $i );
		$ar = @mysql_fetch_array( $data["result"] );
		$ar["pic"]  = "";
		if ( $ar["link"] != "ls" ) {
			$ar['link'] = "/{$ar['link']}/view/{$ar['id']}";
			$adv[] = $ar;
			$used[$ar['link']][] = $ar['id'];
		}
	}

	$q = "SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`data`, '[link]' as `link` FROM `[table]` WHERE `[table]`.`stat`='1' && '[link]' !='ls' && `[table]`.`tags` LIKE '%244,%'";
	$endq = "ORDER BY `data` DESC LIMIT 120";
	$data = getNewsFromLentas( $q, $endq );

	for ( $i = 0; $i < $data["total"]; $i ++ ) {
		@mysql_data_seek( $data["result"], $i );
		$ar = @mysql_fetch_array( $data["result"] );
		$ar['link'] = "/{$ar['link']}/view/{$ar['id']}";
		$list[] = $ar;
		$used[$ar['link']][] = $ar['id'];
	}


    // $q = "SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`data`, '[link]' as `link` FROM `[table]` WHERE (`[table]`.`stat`='1' && `[table]`.`redak`!=1 && `[table]`.`promo`<>1 && `[table]`.`spromo`<>1 [used])";

    // $q = "SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`data`, '[link]' as `link` FROM `[table]` WHERE `[table]`.`stat`='1' && '[link]' !='ls' && `[table]`.`tag` LIKE '%244,%'";
    // $endq = "ORDER BY `data` DESC";
    // $data = getNewsFromLentas( $q, $endq );
    // for ( $i = 0; $i < $data["total"]; $i++ ) {
    //  @mysql_data_seek( $data["result"], $i );
    //  $ar = @mysql_fetch_array( $data["result"] );
    //  $ar["pic"]  = "";
    //  if ( $ar["link"] != "ls" ) {
    //      $ar['link'] = "/{$ar['link']}/view/{$ar['id']}";
    //      $adv[] = $ar;
    //      $used[$ar['link']][] = $ar['id'];
    //  }
    // }

    // $q = "SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`data`, '[link]' as `link` FROM `[table]` WHERE `[table]`.`stat`='1' && '[link]' !='ls' && `[table]`.`tags` LIKE '%244,%'";
    // $endq = "ORDER BY `data` DESC LIMIT 45";
    // $data = getNewsFromLentas( $q, $endq );

    // for ( $i = 0; $i < $data["total"]; $i ++ ) {
    //  @mysql_data_seek( $data["result"], $i );
    //  $ar = @mysql_fetch_array( $data["result"] );
    //  $ar['link'] = "/{$ar['link']}/view/{$ar['id']}";
    //  $list[] = $ar;
    //  $used[$ar['link']][] = $ar['id'];
    // }

	/* ProGorodChelny */
	$proChelnyJson = getChelnyNews('tags', ['limit' => 70,'tags' => array('125')]); // 55
	$proChelnyList = json_decode($proChelnyJson, true);
	$gorodZelenodolskJson = getZelenodolskNews('tags', ['limit' => 70,'tags' => array('125')]); // 55
	$gorodZelenodolskList = json_decode($gorodZelenodolskJson, true);
	$newshunterblock = '<div class="ad-injection-block" id="ad_target-54j4gjuci63rx"></div>';
    $newshunterblock = "";
	$JsonList = getBlockJsonNews(['progorodchelny' => $proChelnyList, 'gorodzelenodolsk' => $gorodZelenodolskList]);
	usort( $JsonList, 'ArraySort' );

	$tmplist = $JsonList;
	$list = [];
	$advid = 0;
	$cnt2 = 0;
	foreach($tmplist as $i => $tmpar) {
		if (( $cnt2 + 1 ) % 3 == 0 && $adv[$advid]["name"] != "" ) {
			$list[] = $adv[$advid];
			$advid ++;
			if($adv[$advid]["name"] != "") {
				$list[] = $adv[$advid];
				$advid ++;
				$cnt2 ++;
			}
		} 
		$list[] = $tmpar;
		$cnt2 ++;
		if(( $cnt2 + 1 ) % 4 == 0) $cnt2 = 0;
	}
// Если есть ещё коммерческая новость, то она добавляется в список после всех
	if(count($adv) > $advid)
	{
		while($advid != count($adv))
		{
			if($adv[$advid]["name"] != "") 
			{
				$list[] = $adv[$advid];
				$advid ++;
			}
		}
	}

	$cnt = 1;

	foreach ( $list as $ar ) {

		$text .= getBlocksContent($ar);

		if ( $cnt % 4 == 0 )
		{
			if ( $cnt == 4 )
			{
				$text .= "<noindex>";

				$text .= "<div class='banner' id='Banner-9-1' class='123'></div>";

				$longBannerData = [
					'show' => false,
					'items' => [
						[
							'url' => 'http://www.medel.ru/directions/flebologiya/',
							'text' => 'Лечение варикоза ног уникальная лазерная методика',
							'image' => '/template/advert/medel1.jpg',
						],
						[
							'url' => 'http://www.medel.ru/directions/podtyazhka-litsa-smas-extra-lifting/',
							'text' => 'Подтяжка лица без операции и без уколов',
							'image' => '/template/advert/medel2.jpg',
						],
					],
					'afterText' => 'Реклама',
					'bannerId' => '1907',
				];

				if( $longBannerData['show'] ) {
					$text .= '<div class="banner-toggleable" style="margin-bottom:45px;">';
					foreach ($longBannerData['items'] as $item) {
						if (empty($item['url']) && empty($item['text'])) {
							continue;
						}
						$url = '/advert/clickBanner.php?id=' . $longBannerData['bannerId'] . '%26away=' . urlencode($item['url']);
						$text .= '<div class="banner-toggleable__picture-wrapper">
						<a href="' . $url . '" rel="nofollow" target="_blank">
						<img class="banner-toggleable__picture" src="' . $item['image'] . '">
						</a>
						</div>
						<h3 class="banner-toggleable__header">
						<a href="' . $url . '" rel="nofollow" target="_blank">
						' . $item['text'] . '
						</a>
						</h3>';
					}
					$text .= '
					<span class="banner-toggleable__text banner-toggleable__text_stroke1">имеются противопоказания</span>
					<span class="banner-toggleable__text banner-toggleable__text_stroke2">необходима консультация специалиста</span>
					<span class="banner-toggleable__text-small">' . $longBannerData['afterText'] . '</a></span>
					<div class="banner-toggleable__button-wrapper">
					<button class="banner-toggleable__button"></button>
					</div>
					</div>
					<img src="/advert/showBanner.php?ids=' . $longBannerData['bannerId'] . '" style="width:0px;height:0px;">';
				} else {
					$text .= '<div style="height: 25px;"></div>';
				}

				$text .= "</noindex>";
			}

			if ($ban10 <= 8) {
				$text .= "<div class='banner3' id='Banner-10-" . $ban10 . "'></div>";
                if ($ban10 == 4 && $newshunterblock != ""){
                    $text .= $newshunterblock;
                    $ban10 = $ban10 + 2;
                    $text .= "<div class='banner3' id='Banner-10-" . $ban10 . "'></div>";
                }
                    $ban10 = $ban10 + 2;

			}

		}
		$cnt ++;
	}

	return $text;
}

function getContentBlock() {
	global $used;
	$text     = '';
	$list     = array();
	$tmplist  = array();
	$redlist  = array();
	$advid    = 0;
	$advsid   = 0;
	$cnt      = 1;
	$ban6     = 1;
	$lastdata = 0;
	$spromo = array();

	/*Surikat*/
	$q    = "SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`lid`, `[table]`.`tavto`, `[table]`.`pic`,`[table]`.`data`, '[link]' as `link` FROM `[table]` WHERE (`[table]`.`stat`='1' && `[table]`.`data`>'" . ( time() - 2 * 24 * 60 * 60 ) . "' && `[table]`.`spromo`=1 [used])";
	$endq = "ORDER BY `data` DESC LIMIT 1";
	$data = getNewsFromLentas( $q, $endq );
	if ( $data["total"] == 1 ) {
		@mysql_data_seek( $data["result"], 0 );
		$ar = @mysql_fetch_array( $data["result"] );
		if ( $ar["link"] != "ls" ) {
			$user[$ar['link']][] = $ar['id'];
			$ar["style"] = "NorN";
			$cnt ++;
			$ar["link"] = "/" . $ar["link"] . "/view/" . $ar["id"];
			if ( $ar["pic"] != "" && $ar["tavto"] == 1 )
			{
				if(file_exists( $_SERVER['DOCUMENT_ROOT'] . "/userfiles/picsmnews/" . $ar["pic"] )) {
                    // Новый размер
					$ar["pic"] = "/userfiles/picsmnews/" . $ar["pic"];
				} else {
                    // Близжайший по разрешению
					$ar["pic"] = "/userfiles/pictavto/" . $ar["pic"];
				}
			} else {
				$ar["pic"] = "";
			}
			//$list[]   = $ar;
			$spromo[] = $ar; //  вип новости
			$lastdata = $ar["data"];
		}
	}

	/*PodSurikat*/
	$q    = "SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`lid`, `[table]`.`tavto`,`[table]`.`data`, `[table]`.`pic`, '[link]' as `link` FROM `[table]` WHERE (`[table]`.`stat`='1' && `[table]`.`data`>'" . ( time() - 1 * 24 * 60 * 60 ) . "' && `[table]`.`promo`=1 [used])";
	$endq = "ORDER BY `data` DESC";
	$data = getNewsFromLentas( $q, $endq );
	for ( $i = 0; $i < $data["total"]; $i ++ ) {
		@mysql_data_seek( $data["result"], $i );
		$ar = @mysql_fetch_array( $data["result"] );
		if ( $ar["link"] != "ls" ) {
			$used[$ar['link']][] = $ar['id'];
			$ar["style"] = "ReOneOrder";
			$ar["link"]  = "/" . $ar["link"] . "/view/" . $ar["id"];
			if ( $ar["pic"] != "" && $ar["tavto"] == 1 ) {
				if(file_exists( $_SERVER['DOCUMENT_ROOT'] . "/userfiles/picsmnews/" . $ar["pic"] )) {
                    // Новый размер
					$ar["pic"] = "/userfiles/picsmnews/" . $ar["pic"];
				} else {
                    // Близжайший по разрешению
					$ar["pic"] = "/userfiles/pictavto/" . $ar["pic"];
				}
			} else {
				$ar["pic"] = "";
			}
			$avd[] = $ar;
		}
	}

	/*Staruhi*/
	$q    = "SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`lid`, `[table]`.`tavto`,`[table]`.`data`, `[table]`.`pic`, '[link]' as `link` FROM `[table]` WHERE (`[table]`.`stat`='1' && `[table]`.`data`<'" . ( time() - 7 * 24 * 60 * 60 ) . "' && `[table]`.`data`>'" . ( time() - 10 * 24 * 60 * 60 ) . "' && (`[table]`.`promo`=1 || `[table]`.`spromo`=1) [used])";
	$endq = "ORDER BY `data` DESC LIMIT 10";
	$data = getNewsFromLentas( $q, $endq );
	for ( $i = 0; $i < $data["total"]; $i ++ ) {
		@mysql_data_seek( $data["result"], $i );
		$ar = @mysql_fetch_array( $data["result"] );
		if ( $ar["link"] != "ls" ) {
			$used[$ar['link']][] = $ar['id'];
			$ar["style"] = "Oldest";
			$ar["link"]  = "/" . $ar["link"] . "/view/" . $ar["id"];
			if ( $ar["pic"] != "" && $ar["tavto"] == 1 ) {
				if(file_exists( $_SERVER['DOCUMENT_ROOT'] . "/userfiles/picsmnews/" . $ar["pic"] )) {
                    // Новый размер
					$ar["pic"] = "/userfiles/picsmnews/" . $ar["pic"];
				} else {
                    // Близжайший по разрешению
					$ar["pic"] = "/userfiles/pictavto/" . $ar["pic"];
				}
			} else {
				$ar["pic"] = "";
			}
			$avds[] = $ar;
		}
	}

	$q    = "SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`lid`, `[table]`.`tavto`,`[table]`.`data`, `[table]`.`comcount`, `[table]`.`pic`, '[link]' as `link` FROM `[table]` WHERE (`[table]`.`stat`='1' && `[table]`.`redak`=1 [used])";
	$endq = "ORDER BY `data` DESC LIMIT 40";
	$data = getNewsFromLentas( $q, $endq );
	$sc   = 0;
	for ( $i = 0; $i < $data["total"]; $i ++ ) {
		@mysql_data_seek( $data["result"], $i );
		$ar = @mysql_fetch_array( $data["result"] );
		$used[$ar['link']][] = $ar['id'];
		$ar["style"] = "Editors";
		$ar["link"]  = "/" . $ar["link"] . "/view/" . $ar["id"];
		if ( $ar["pic"] != "" && $ar["tavto"] == 1 ) {
			if(file_exists( $_SERVER['DOCUMENT_ROOT'] . "/userfiles/picsmnews/" . $ar["pic"] )) {
                // Новый размер
				$ar["pic"] = "/userfiles/picsmnews/" . $ar["pic"];
			} else {
                // Близжайший по разрешению
				$ar["pic"] = "/userfiles/pictavto/" . $ar["pic"];
			}
		} else {
			$ar["pic"] = "";
		}
		if ( $sc < 4 && $ar["link"] != "ls" ) {
			//$redlist[] = $ar;
			$tmplist[] = $ar;
			$sc ++;
		} else {
			$tmplist[] = $ar;
		}
	}

	$BubrJson = getBubrNews();
	$BubrList = json_decode($BubrJson, true);
	$JsonList = getBlockJsonNews(['bubr' => $BubrList]);

	usort( $tmplist, 'ArraySort' );
	$cnt = 0; //Счётчик полного списка новостей с рекламой и с бабр
	$cnt2 = 0; //Счётчик новостей без дополнительных элементов
	$block = 0; //Счётчик блоков
	while($cnt2 < count($tmplist))
	{

		if($cnt % 3 == 0 || $cnt % 4 == 0 || $cnt == 0) {
			$block++;
		}

		if($block == 1)
		{
			//Новость Казани
			if(!empty($tmplist[$cnt2])) $list[$cnt] = $tmplist[$cnt2];
			$cnt ++;$cnt2 ++;
			//Новость Казани
			if(!empty($tmplist[$cnt2])) $list[$cnt] = $tmplist[$cnt2];
			$cnt ++;$cnt2 ++;
			//Новость Казани
			if(!empty($tmplist[$cnt2])) $list[$cnt] = $tmplist[$cnt2];
			$cnt ++;$cnt2 ++;
		}
		if($block == 2)
		{
			//Если есть, то - БАБР, если нет, то - Казань
			if(is_array($JsList = array_shift($JsonList)))
			{
				$list[$cnt] = $JsList;
				$cnt++;
			} else {
				if(!empty($tmplist[$cnt2])) $list[$cnt] = $tmplist[$cnt2];
				$cnt ++;$cnt2 ++;
			}
			//Новость Казани
			if(!empty($tmplist[$cnt2])) $list[$cnt] = $tmplist[$cnt2];
			$cnt ++;$cnt2 ++;
			//Если есть, то - БАБР, если нет, то - Казань
			if(is_array($JsList = array_shift($JsonList)))
			{
				$list[$cnt] = $JsList;
				$cnt++;
			} else {
				if(!empty($tmplist[$cnt2])) $list[$cnt] = $tmplist[$cnt2];
				$cnt ++;$cnt2 ++;
			}
		}

		if($block > 2)
		{
			//Новость Казани
			if(!empty($tmplist[$cnt2])) $list[$cnt] = $tmplist[$cnt2];
			$cnt ++;$cnt2 ++;
			//Если есть, то - БАБР, если нет, то - Казань
			if(is_array($JsList = array_shift($JsonList)))
			{
				$list[$cnt] = $JsList;
				$cnt++;
			} else {
				if(!empty($tmplist[$cnt2])) $list[$cnt] = $tmplist[$cnt2];
				$cnt ++;$cnt2 ++;
			}
			//Новость Казани
			if(!empty($tmplist[$cnt2])) $list[$cnt] = $tmplist[$cnt2];
			$cnt ++;$cnt2 ++;
		}

			//Коммерческая новость после каждых 3-х новостей
		if ( $avd[$advid]["name"] != "" ) {
			$list[$cnt] = $avd[$advid];
			$advid ++;
			$cnt ++;
		} else {
			if ( $avds[$advsid]["name"] != "" ) {
				$list[$cnt] = $avds[$advsid];
				$advsid ++; 
				$cnt ++;
			}
		}
			//Если блок не заполнен, то заполняем принудительно
		while($cnt % 4 != 0) {
			if(!empty($tmplist[$cnt2])) $list[$cnt] = $tmplist[$cnt2];
			$cnt ++;$cnt2 ++;
		}

	}

	 if (!empty($spromo)) {
        array_unshift($list, $spromo[0]);
    } //ВСТАВЛЯЕМ СУПЕРПРОМО НОВОСТЬ САМОЙ ПЕРВОЙ

	$cnt = 1;
	$tagblock = 0;
	$right_sections = getRightSectionsNews();

	foreach ( $list as $ar ) {
		if ( strpos( $ar["link"], "ls" ) !== false ||
			strpos( $ar["link"], "bubr" ) !== false ||
			strpos( $ar['link'], 'progorodchelny') !== false ) {
			$rel = "target='_blank' rel='nofollow'";
	} else {
		$rel      = "";
		$lastdata = $ar["data"];
	}
	$text .= getCenterContent($ar, "index");


	if ( $cnt % 4 == 0 ) {
		if ( $ban6 < 14 ) {
			$text .= "<div class='banner2 hidden-mobile' id='Banner-6-" . $ban6 . "'></div>";
			$text .= "<div class='banner2 hidden-desktop' id='Banner-28-" . $ban6 . "'></div>";
			$ban6++;
		}
    //     while( empty($right_sections[$tagblock]['news']['total']) &&
    //         count($right_sections) > $tagblock ) {
    //         $tagblock++;
    // }

    // $tagblock++;
	}
	$cnt++;
}

$text .= "<input id='lastdata' value='" . $lastdata . "' type='hidden'><div id='ShowMoreInd'><a href='javascript:void(0);' onclick='ShowMoreInd();'>Показать больше новостей</a></div>";


return $text;
}

function getLeftBlock() {
	global $used, $C25;
	$adv   = array();
	$list  = array();
	$cnt   = 1; 
	$ban10 = 1;

	$q = "SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`data`, '[link]' as `link` FROM `[table]` WHERE (`[table]`.`stat`='1' && `[table]`.`redak`!=1 && (`[table]`.`promo`=1 || `[table]`.`spromo`=1) && `[table]`.`data`<'" . ( time() - 1 * 24 * 60 * 60 ) . "' && `[table]`.`data`>'" . ( time() - 4 * 24 * 60 * 60 ) . "')";
	$endq = "ORDER BY `data` DESC";
	$data = getNewsFromLentas( $q, $endq );
	for ( $i = 0; $i < $data["total"]; $i++ ) {
		@mysql_data_seek( $data["result"], $i );
		$ar = @mysql_fetch_array( $data["result"] );
		$ar["pic"]  = "";
		if ( $ar["link"] != "ls" ) {
			$ar['link'] = "/{$ar['link']}/view/{$ar['id']}";
			$adv[] = $ar;
			$used[$ar['link']][] = $ar['id'];
		}
	}

	$q = "SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`data`, '[link]' as `link` FROM `[table]` WHERE (`[table]`.`stat`='1' && `[table]`.`redak`!=1 && `[table]`.`promo`<>1 && `[table]`.`spromo`<>1 [used])";
	$endq = "ORDER BY `data` DESC LIMIT 45";
	$data = getNewsFromLentas( $q, $endq );
	for ( $i = 0; $i < $data["total"]; $i ++ ) {
		@mysql_data_seek( $data["result"], $i );
		$ar = @mysql_fetch_array( $data["result"] );
		$ar['link'] = "/{$ar['link']}/view/{$ar['id']}";
		$list[] = $ar;
		$used[$ar['link']][] = $ar['id'];
	}

	/* ProGorodChelny */
	$proChelnyJson = getChelnyNews('lenta', ['limit' => 25]);
	$proChelnyList = json_decode($proChelnyJson, true);

	$JsonList = getBlockJsonNews(['progorodchelny' => $proChelnyList]);
	$list = array_merge($list, $JsonList);
	usort( $list, 'ArraySort' );
	$tmplist = $list;
	$list = [];
	$advid = 0;
	$cnt2 = 0;

	foreach($tmplist as $i => $tmpar) {
		if ( ( $cnt2 + 1 ) % 3 == 0 && $adv[$advid]["name"] != "" ) {
			$list[] = $adv[$advid];
			$advid ++;
		} 
		$list[] = $tmpar;
		$cnt2 ++;
		if(( $cnt2 + 1 ) % 4 == 0) $cnt2 = 0;
	}

	if(count($adv) > $advid)
	{
		while($advid != count($adv))
		{
			if($adv[$advid]["name"] != "")
			{
				$list[] = $adv[$advid];
				$advid ++;
			}
		}
	}

	$text = '';

	foreach ( $list as $ar ) {

		$text .= getBlocksContent($ar);

		if($cnt <= 4)
		{
			$text .= '<div style="height: 5px;"></div>';
		}

		if ( $cnt % 4 == 0 )
		{
			if ( $cnt == 4 )
			{
				$text .= "<noindex>";

				$text .= "<div class='banner' id='Banner-9-1'></div>";

				$longBannerData = [
					'show' => false,
					'items' => [
						[
							'url' => 'http://www.medel.ru/directions/flebologiya/',
							'text' => 'Лечение варикоза ног уникальная лазерная методика',
							'image' => '/template/advert/medel1.jpg',
						],
						[
							'url' => 'http://www.medel.ru/directions/podtyazhka-litsa-smas-extra-lifting/',
							'text' => 'Подтяжка лица без операции и без уколов',
							'image' => '/template/advert/medel2.jpg',
						],
					],
					'afterText' => 'Реклама',
					'bannerId' => '1907',
				];

				if( $longBannerData['show'] ) {
					$text .= '<div class="banner-toggleable" style="margin-bottom:45px;">';
					foreach ($longBannerData['items'] as $item) {
						if (empty($item['url']) && empty($item['text'])) {
							continue;
						}
						$url = '/advert/clickBanner.php?id=' . $longBannerData['bannerId'] . '%26away=' . urlencode($item['url']);
						$text .= '<div class="banner-toggleable__picture-wrapper">
						<a href="' . $url . '" rel="nofollow" target="_blank">
						<img class="banner-toggleable__picture" src="' . $item['image'] . '">
						</a>
						</div>
						<h3 class="banner-toggleable__header">
						<a href="' . $url . '" rel="nofollow" target="_blank">
						' . $item['text'] . '
						</a>
						</h3>';
					}
					$text .= '
					<span class="banner-toggleable__text banner-toggleable__text_stroke1">имеются противопоказания</span>
					<span class="banner-toggleable__text banner-toggleable__text_stroke2">необходима консультация специалиста</span>
					<span class="banner-toggleable__text-small">' . $longBannerData['afterText'] . '</a></span>
					<div class="banner-toggleable__button-wrapper">
					<button class="banner-toggleable__button"></button>
					</div>
					</div>
					<img src="/advert/showBanner.php?ids=' . $longBannerData['bannerId'] . '" style="width:0px;height:0px;">';
				} else {
					$text .= '<div style="height: 25px;"></div>';
				}

				$text .= "</noindex>";
			}

			if ( $ban10 < 10 )
			{
				$text  .= "<div class='banner3' id='Banner-10-" . $ban10 . "'></div>";
				$ban10 = $ban10 + 2;
			}
			if ( $ban10 >= 10 && $ban10 < 14 )
			{
				$text  .= '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script><ins style="display:inline-block; overflow:hidden; height:200px; width:200px;" class="adsbygoogle" data-ad-client="ca-pub-2073806235209608" data-ad-slot="9007081016"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script>' . $C25;
				$ban10 = $ban10 + 2;
			}
		}
		$cnt ++;
	}

	return $text;
}

function getTopBlock() {
	global $used, $GLOBAL, $BP;
	$text = "";
	$q    = "SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`lid`, `[table]`.`pic`, `[table]`.`data`, '[link]' as `link` 
	FROM `[table]` WHERE (`[table]`.`stat`='1' && `[table]`.`onind`=1 [used])";
	$endq = "ORDER BY `data` DESC LIMIT 1";
	$data = getNewsFromLentas( $q, $endq );

	if ( $GLOBAL["USER"]["role"] > 1 ) {
		$admText = '<div id="AdminEditItem" style="position:absolute;top:-7px;"><a href="' . $BP . '?nocache">Обновить кэш. Не злоупотреблять! =)</a></div>';
	} else {
		$admText = '';
	}

	if ( $data["total"] > 0 ) {
		@mysql_data_seek( $data["result"], 0 );
		$ar = @mysql_fetch_array( $data["result"] );
		$used[$ar["link"]][] = $ar["id"];

		$link = "/{$ar['link']}/view/{$ar['id']}";
		$ar['name'] = Hsc( $ar['name'] );
		$ar['lid'] = Hsc( $ar['lid'] );
		$text .= <<<HTML
		<div class="news-big">
		{$admText}
		<a href="{$link}">
		<img class="news-big__picture" src="/userfiles/semya/{$ar['pic']}" alt="{$ar['name']}" title="{$ar['name']}">

		<div class="news-big__content">
		<h1 class="news-big__header" title="{$ar['name']}">{$ar['name']}</h1>
		<p class="news-big__text" title="{$ar['lid']}">{$ar['lid']}</p>
		</div>
		</a>
		</div>
HTML;
		$live_video = DB('SELECT * FROM `livestream_lenta` WHERE `stat` = 1 AND `stream_link` != "" AND `onind` = 1 AND `start` <= ' . time() . ' AND `end` > ' . time());

		if( 0 < (int) $live_video['total'] ) {
			$livestream = mysql_fetch_assoc($live_video['result']);
			if( ! empty( $livestream['pic'] )) {
				if(file_exists( $_SERVER['DOCUMENT_ROOT'] . '/userfiles/picarticle/' . $livestream['pic'] )) {
					$picsrc = '/userfiles/picarticle/' . $livestream['pic'];
				} else {
					$picsrc = '/userfiles/picintv/' . $livestream['pic'];
				}
			} else {
				$picsrc = 'javascript:void(0);';
			}
			$text .= <<<HTML
			<div class="live live_active live_top">
			<div class="live__buttons">
			<button class="live__button"><span class="live__icon_expand"></span></button>
			<button class="live__button"><span class="live__icon_hide"></span></button>
			<button class="live__button"><span class="live__icon_close"></span></button>
			</div>
			<a class="fancybox" data-fancybox-type="iframe" href="{$livestream['stream_link']}">
			<img class="live__picture" src="{$picsrc}" alt="live">
			</a>
			<span class="live__plug">прямой эфир</span>
			<span class="live__name">{$livestream['name']}</span>
			</div>
HTML;
		}

		$text .= <<<HTML
		<div class="banner-aside">
		<div class="banner3" id="Banner-1-1"></div>
		</div>
HTML;

	}

	return $text;
}
