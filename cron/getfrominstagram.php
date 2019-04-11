<?
if ($ROOT=="" || !$ROOT) { $ROOT=$_SERVER["DOCUMENT_ROOT"]; }
### Данные подключения
$ID="3574c2b80f584138af2d1d2277efb877";
$SECRET="0fe655c3d1ea440fb30a196f123b9815";
if ($GLOBAL["sitekey"]!=1) { $GLOBAL["sitekey"]=1; @require_once($ROOT."/modules/standart/DataBase.php"); }

$ban=array(
"dinanuretdinova",
"",
"",
"",
"",
);

### Получения данных
$TAG="мояказань";
$url="https://api.instagram.com/v1/tags/".$TAG."/media/recent?client_id=".$ID; $answer=file_get_contents($url);
var_dump($answer);
### Обработка данных
$obj=json_decode($answer); echo "<hr>TAG = #".$TAG.": ".$obj->meta->code."<hr>";
if ($obj->meta->code==200):
	foreach ($obj->data as $item) {
		$id =$item->id;
		$data =$item->created_time;
		$likes =$item->likes->count;
		$piclink =$item->link;
		$picname =$item->caption->text;
		$picpreview =$item->images->low_resolution->url;
		$picoriginal =$item->images->standard_resolution->url;
		$username =$item->user->full_name;
		$userlink =$item->user->username;
		$useravatar =$item->user->profile_picture;
		$query="INSERT INTO `_widget_insta` VALUES ('".$id."','1','','".$data."','".$likes."','".$piclink."','".$picname."','".$picpreview."','".$picoriginal."','".$username."','".$userlink."','".$useravatar."') ON DUPLICATE KEY UPDATE `likes`='".$likes."'";
		if ($id!="" && (int)$id!=0 && !in_array($userlink, $ban)) { DB($query); }
	}
endif;
	
$TAG="МояКазань";
### Получения данных
$url="https://api.instagram.com/v1/tags/".$TAG."/media/recent?client_id=".$ID; $answer=file_get_contents($url);
var_dump($answer);
### Обработка данных
$obj=json_decode($answer); echo "<hr>TAG = #".$TAG.": ".$obj->meta->code."<hr>";
if ($obj->meta->code==200):
	foreach ($obj->data as $item) {
		$id =$item->id;
		$data =$item->created_time;
		$likes =$item->likes->count;
		$piclink =$item->link;
		$picname =$item->caption->text;
		$picpreview =$item->images->low_resolution->url;
		$picoriginal =$item->images->standard_resolution->url;
		$username =$item->user->full_name;
		$userlink =$item->user->username;
		$useravatar =$item->user->profile_picture;
		$query="INSERT INTO `_widget_insta` VALUES ('".$id."','1','','".$data."','".$likes."','".$piclink."','".$picname."','".$picpreview."','".$picoriginal."','".$username."','".$userlink."','".$useravatar."') ON DUPLICATE KEY UPDATE `likes`='".$likes."'";
		if ($id!="" && (int)$id!=0 && !in_array($userlink, $ban)) { DB($query); }
	}
endif;
?>
