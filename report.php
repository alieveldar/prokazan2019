<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.12.16
 * Time: 3:49
 */

 if (isset($_POST['start']) && !empty($_POST['start'])){
global $GLOBAL;
$GLOBAL["sitekey"] = 1;
require_once "modules/standart/DataBase.php";

$date1 = strtotime($_POST['start']);
$date2 = strtotime($_POST['end']);

$query = 'SELECT
                `news_lenta`.`id`,
                `news_lenta`.`name`,
                `news_lenta`.`data`
                FROM `news_lenta`
WHERE `news_lenta`.`data` BETWEEN ' . $date1 . ' AND ' . $date2
. ' ORDER BY data ASC';

$result = DB($query);
if ($result['total']){
$fname = $date1 . '-' . $date2 . '_' . time() . '.csv';
$file = fopen('userfiles/docs/' . $fname, 'ab+');

while($row = mysql_fetch_array($result['result'], MYSQL_ASSOC)){
  $row['link'] = 'http://prokazan.ru/news/view/' . $row['id'];
  $row['data'] = date('d.m.Y', (int)$row['data']);
  fputcsv($file, $row, ',');
}

fclose($file);

print '<a href="http://prokazan.ru/userfiles/docs/' . $fname . '">http://prokazan.ru/userfiles/docs/' . $fname . '</a><br><br>';

} else {?>
  <h1>Ничего не найдено</h1>
<?php }
} ?>
<form action="/report.php" method="post">
  С <input type="date" name="start" format="dd.mm.YYYY" class="date">
  по <input type="date" name="end" format="dd.mm.YYYY" class="date">
  <input type="submit" value="Сгенерировать">
</form>
<link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
<script src="https://unpkg.com/flatpickr"></script>
<script type="text/javascript">
flatpickr(".date", {
	dateFormat: 'd.m.Y',
	altInput: true,
	altFormat: 'd.m.Y'
}); // [Flatpickr, Flatpickr, ...]
</script>