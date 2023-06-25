<?php
session_start();
require_once ('../config.php');
require ('classes/Password.php');
require_once ('classes/LDUtils.php');
require_once ('classes/JsonHelper.php');
require_once ('classes/Articles.php');
require_once ('classes/AdminArticleUtils.php');

$p = new Password();
$p->readFromFile('../' . PASSWORD_FILE);

function removeArticle()
{
	$uri = $_GET['url'];
	$u = new AdminArticleUtils(ARTICLES_DIR, ARTICLES_FILE);
	if ($u->removeArticle($uri, '..') === false) 
		return false;
}

$p->secure("Eliminazione Articolo", "admin_articles_list.php", 'removeArticle');
?>
