<?php
error_reporting(E_ERROR);
ini_set('display_errors',0);
define('ARTICLES_USER', '7eb216e5a92a5069cc0b4d7f298bfc13');
require_once(realpath($_SERVER['DOCUMENT_ROOT'].'/'.ARTICLES_USER.'/narticle.php'));
$narticle = new ArticleClient();
echo $narticle->getLastIndex(5);
?>