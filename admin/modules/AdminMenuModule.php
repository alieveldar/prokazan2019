<?
if ($GLOBAL["sitekey"]==1 && $GLOBAL["database"]==1) {

$mods=array();

### Стандартные модули
$data=DB("SELECT `id`,`shortname`,`module` FROM `_pages` WHERE (`main`='1' && `hidden`!='1') ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++):
@mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $mods[1].="<li><a href='?cat=adm_".$ar["module"]."'>".AIco(71).$ar["shortname"]."</a></li>"; endfor;
### Список меню
$data=DB("SELECT `id`,`name`,`stat` FROM `_menulist` ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); if ($ar["stat"]==1) {
$mods[2].="<li><a href='?cat=adm_menuedit&id=".$ar["id"]."'>".AIco(76).$ar["name"]."</a></li>"; } else { $mods[2].="<li><a href='?cat=adm_menuedit&id=".$ar["id"]."'>".AIco(75).$ar["name"]."</a></li>"; } endfor;
### Модульные страницы
$data=DB("SELECT `shortname` as `name`,`link`,`id` FROM `_pages` WHERE (`module`!='' && main!='1' && `hidden`!='1') ORDER BY `name` ASC"); for ($i=0; $i<$data["total"]; $i++): @mysql_data_seek($data["result"],$i); $ar=@mysql_fetch_array($data["result"]); $mods[3].='<li><a href="?cat='.$ar["link"].'_list">'.AIco(25).$ar["name"].'</a><ul><li><a href="?cat='.$ar["link"].'_add">'.AIco(11).'Добавить материал</a></li><li><a href="?cat='.$ar["link"].'_list">'.AIco(28).'Список материалов</a></li><li><a href="?cat=adm_razdelsets&id='.$ar["id"].'">'.AIco(72).'Настройки раздела</a></li></ul></li>'; endfor;


if ((int)$_SESSION['userrole']>1) { $AdminMenu.='<ul id="menu"><!-- главные страницы --><li><a href="/admin/">'.AIco(62).' </a></li>';}
if ((int)$_SESSION['userrole']>1) { $AdminMenu.='<li><a href="/admin/?cat=adm_planesnow">'.AIco(31).'Календарь</a><ul>
<li><a href="/admin/?cat=adm_planesadd">'.AIco("plus").'Запланировать событие</a></li>
<li><a href="/admin/?cat=adm_planesnow">'.AIco(30).'Ближайшие события</a></li>
<li><a href="/admin/?cat=adm_planes">'.AIco(31).'Список всех событий</a></li>
</ul></li>';}

if ((int)$_SESSION['userrole']>2) { $AdminMenu.='<!-- Меню сайта --><li><a href="javascript:void(0);">'.AIco(23).'Навигация</a><ul><li><a href="?cat=adm_menuadd">'.AIco(11).'Добавить меню</a></li>'.$mods[2].'</ul></li>'; }

if ((int)$_SESSION['userrole']>1) { $AdminMenu.='<!-- Содержание сайта -->
<li><a href="javascript:void(0);">'.AIco(26).'Содержимое</a><ul>
	<li><a href="?cat=adm_static">'.AIco(26).'Статичные страницы</a><ul>
		<li><a href="?cat=adm_staticadd">'.AIco(11).'Добавить материал</a></li>
		<li><a href="?cat=adm_static">'.AIco(28).'Список материалов</a></li>
	</ul></li>'.$mods[3].'</ul></li>';
}

if ((int)$_SESSION['userrole']>3) {
$AdminMenu.='<!-- Разделы сайта на модулях -->
<li><a href="javascript:void(0);">'.AIco(27).'Модули сайта</a><ul>
	<li><a href="?cat=adm_razdelnew">'.AIco(27).'Список модулей</a></li>
		<li><a href="?cat=strochki_list">'.AIco(17).'Прием объявлений</a><ul>
			<li><a href="?cat=strochki_list">'.AIco(27).'Список объявлений</a></li>
			<li><a href="?cat=strochki_stat">'.AIco(53).'Статистика оплаты</a></li>
			<li><a href="?cat=strochki_users">'.AIco(73).'Список клиентов</a></li>
			<li><a href="?cat=strochki_razdel">'.AIco(40).'Разделы объявлений</a></li>
			<li><a href="?cat=strochki_sets">'.AIco(72).'Настройки объявлений</a></li>
			<li><a href="?cat=strochki_pay">'.AIco(25).'Текст перед оплатой</a></li>
		</ul></li>
	<li><a href="?cat=adm_imagemaster">'.AIco(54).'Обработка картинок</a></li>
	<li><a href="javascript:void(0);">'.AIco(67).'Стандартные модули</a><ul>'.$mods[1].'</ul></li>
	<li><a href="?cat=adm_cron">'.AIco(30).'Задания по расписанию</a></li>
	<li><a href="?cat=adm_rss">'.AIco(58).'RSS ленты и тизеры</a></li>
	<li><a href="?cat=import_list">'.AIco(59).'Импорт материалов</a></li>
	<li><a href="?cat=adm_editors">'.AIco(26).'Редакция</a></li>
</ul></li>'; }
else if ((int)$_SESSION['userrole']>1) {
$AdminMenu.='<!-- Разделы сайта на модулях -->
	<li><a href="javascript:void(0);">'.AIco(27).'Модули сайта</a><ul>
	<li><a href="javascript:void(0);">'.AIco(67).'Стандартные модули</a><ul>'.$mods[1].'</ul></li>
	<li><a href="?cat=adm_cron">'.AIco(30).'Задания по расписанию</a></li>
	<li><a href="?cat=adm_rss">'.AIco(58).'RSS ленты и тизеры</a></li>
	<li><a href="?cat=import_list">'.AIco(59).'Импорт материалов</a></li>
		<li><a href="?cat=strochki_list">'.AIco(17).'Прием объявлений</a><ul>
			<li><a href="?cat=strochki_list">'.AIco(27).'Список объявлений</a></li>
			<li><a href="?cat=strochki_stat">'.AIco(53).'Статистика оплаты</a></li>
			<li><a href="?cat=strochki_users">'.AIco(73).'Список клиентов</a></li>
			<li><a href="?cat=strochki_razdel">'.AIco(40).'Разделы объявлений</a></li>
			<li><a href="?cat=strochki_sets">'.AIco(72).'Настройки объявлений</a></li>
			<li><a href="?cat=strochki_pay">'.AIco(25).'Текст перед оплатой</a></li>
		</ul></li>
	<li><a href="?cat=adm_editors">'.AIco(26).'Редакция</a></li>
</ul></li>'; }

if ((int)$_SESSION['userrole']>2) {
$AdminMenu.='<!-- Пользователи -->
<li><a href="javascript:void(0);">'.AIco(37).'Пользователи</a><ul>
	<li><a href="?cat=adm_users">'.AIco(37).'Все пользователи сайта</a></li>
	<li><a href="?cat=adm_usersettings">'.AIco(71).'Настройки пользователей</a></li>
	<li><a href="?cat=adm_usersearch">'.AIco(37).'Поиск пользователей</a></li>
	<li><a href="?cat=adm_blockusers">'.AIco(73).'Блокированные пользователи</a></li>
	<li><a href="?cat=adm_superusers">'.AIco(38).'Пользователи с правами</a></li>
	<li><a href="?cat=adm_comments">'.AIco(17).'Комментарии материалов</a></li>
	<li><a href="?cat=adm_userlog">'.AIco(31).'Лог активности</a></li>
</ul></li>'; }

if ((int)$_SESSION['userrole']>3) {
$AdminMenu.='<!-- Настройки -->
<li><a href="javascript:void(0);">'.AIco(72).'Настройки</a><ul>
	<li><a href="?cat=adm_settings">'.AIco(72).'Основные настройки</a></li>
	<li><a href="?cat=adm_social">'.AIco(16).'Социальные сети</a></li>
	<li><a href="?cat=adm_vars">'.AIco(50).'Параметры сайта</a></li>
	<li><a href="?cat=adm_cache">'.AIco(45).'Настройки кэша</a></li>
	<li><a href="?cat=adm_design">'.AIco(27).'Шаблоны дизайна</a></li>
	<li><a href="?cat=adm_domains">'.AIco(13).'Домен и поддомены</a></li>
	<li><a href="?cat=adm_page404">'.AIco(50).'Страница не найдена (404)</a></li>
	<li><a href="?cat=adm_page403">'.AIco(56).'Доступ запрещен (403)</a></li>
</ul></li>'; }

if ((int)$_SESSION['userrole']>1) {
$AdminMenu.='<!-- Баннерная система -->
<li><a href="?cat=banners_list">'.AIco(22).'Баннеры</a><ul>
	<li><a href="?cat=banners_list">'.AIco(22).'Список баннеров</a></li>
	<li><a href="?cat=banners_arhive">'.AIco(22).'Архив баннеров</a></li>
	<li><a href="?cat=banners_order">'.AIco(22).'Заявки</a></li>
	<li><a href="?cat=banners_company">'.AIco(22).'Компании</a></li>
	<li><a href="?cat=banners_stat">'.AIco(22).'Статистика баннеров</a></li>
	<li><a href="?cat=banners_artstat">'.AIco(22).'Статистика статей</a></li>
	<li><a href="?cat=banners_type">'.AIco(22).'Типы баннеров</a></li>
	<li><a href="?cat=banners_system">'.AIco(22).'Баннерная система</a></li>
</ul></li>'; }

if ((int)$_SESSION['userrole']>0) { $AdminMenu.='<!-- Выход --><li><a href="?cat=adm_exit">'.AIco(56).'Выход</a></li></ul><div class="C20"></div>'; }




}
?>