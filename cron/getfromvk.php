<?
if ($ROOT=="" || !$ROOT) { $ROOT=$_SERVER["DOCUMENT_ROOT"]; }
if ($GLOBAL["sitekey"]!=1) { $GLOBAL["sitekey"]=1; @require_once($ROOT."/modules/standart/DataBase.php"); }

$tag = "#сити_любовь_кзн";
$url = "https://api.vk.com/method/newsfeed.search?q=".urlencode($tag)."&count=30&extended=1&version=5.28"; 

$json = @file_get_contents($url); $obj = json_decode($json);
foreach ($obj->response as $item) {  if ($item->owner_id>0) {
	
	$id=$item->id;
	$uid=$item->owner_id;
	$vkid=$uid."_".$id;
	$data=$item->date;
	$name=$item->user->first_name." ".$item->user->last_name;
	$avatar=$item->user->photo;
	$text=preg_replace('/\[.*?\]/', '', $item->text);
	$pic=$item->attachment->photo->src_big;
	
	DB("INSERT INTO `_widget_vk` (`tag`,`vkid`,`name`,`avatar`,`userlink`,`data`,`text`,`pic`) VALUES ('".$tag."','".$vkid."','".$name."','".$avatar."','".$uid."','".$data."','".$text."','".$pic."')
	ON DUPLICATE KEY UPDATE `text`='".$text."', `pic`='".$pic."'"); 
	
	echo "$uid / $data / $name / $avatar<br />$text<br>$pic<hr />";
}}
		  
?>
