<?
$pg=$dir[1]?$dir[1]:1; $file="_index-sam2news_".(int)$pg; $VARS["cachepages"]=0; $Page["Caption"]=$node["name"]; $CSSmodules["авто включение ленты"]="/modules/lenta/lenta.css";
if (RetCache($file)=="true") { list($text, $cap)=GetCache($file, 0); } else { list($text, $cap)=KazanNews(); SetCache($file, $text, ""); } $Page["Content"]=$text;
#list($text, $cap)=KazanNews(); $Page["Content"]=$text;


function KazanNews() { $C=""; 
	global $VARS, $GLOBAL, $C10, $C30, $C25, $C, $used, $VAR, $C, $C20, $C10, $C25, $dir, $UserSetsSite; $onpage=50; $list=array(); $pg=$dir[1]?$dir[1]:1; $from=($pg-1)*$onpage; $onblock=4;
	
	// Находим все таблицы с lenta ==================
	$q="SELECT `[table]`.`id`, `[table]`.`name`, `[table]`.`lid`, `[table]`.`data`, `[table]`.`comcount`, `[table]`.`pic`, '[link]' as `link` FROM `[table]` WHERE (`[table]`.`stat`='1')"; $endq="ORDER BY `data` DESC LIMIT ".$from.", ".$onpage; $data=getNewsFromLentas($q, $endq);
	for ($i=0; $i<$data["total"]; $i++) { @mysql_data_seek($data["result"], $i); $ar=@mysql_fetch_array($data["result"]); $ar["link"]="/".$ar["link"]."/view/".$ar["id"]; $ar["pic"]=$src."/userfiles/pictavto/".$ar["pic"]; $list[]=$ar; }
	
	$xml = simplexml_load_file("http://bubr.ru/prokazan_news.xml"); $bubr=array(); if (!empty($xml)) { $count_str = count($xml->channel->item); $i=0; while ($i <= ($count_str-1)): $item=$xml->channel->item[$i];
	$bubr["name"]=(string)$item->title; $bubr["link"]=(string)$item->link; $bubr["pic"]=(string)$item->picmiddle; $bubr["data"]=(string)$item->data; $bubr["lid"]=(string)$item->ttwo; $ar["style"]="Bubr"; $list[]=$bubr; $i++; endwhile; }
	
	usort($list, ArraySort);
	
	// выводим новости ==============================

	$cnt=1; foreach($list as $ar) {
		$text.="<div class='RedNews ".$ar["style"]."'>";
		if (strpos($ar["link"], "ls")!==false || strpos($ar["link"], "bubr")!==false) { $rel="target='_blank' rel='nofollow'"; } else { $rel=""; }
		if ($ar["pic"]!="") { $text.="<div class='img'><a href='".$ar["link"]."' $rel><img src='".$ar["pic"]."'></a></div><div class='stext'>"; } else { $text.="<div class='ftext'>"; }
		$text.="<a href='".$ar["link"]."' class='caption' $rel>".$ar["name"]."</a>"; $text.=$C.Dater($ar);
		$text.="</div></div>".$C30;
		if ($cnt%4==0) { if ($ban6<10) { $text.="<div class='banner2' id='Banner-6-".$ban6."'></div>"; $ban6++; }}
	$cnt++; } 
	
	// строим пагер =================================
	
	
	$q="SELECT `[table]`.`id` FROM `[table]` WHERE (`[table]`.`stat`='1')"; $endq="";
	$data=getNewsFromLentas($q, $endq);	$text.=Pager2($pg, $onpage, ceil($data["total"]/$onpage), $dir[0]."/"."[page]");
	// ==============================================
 	return (array($text, $C));
}
?>