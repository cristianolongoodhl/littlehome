<?php
session_start();
require_once('../config.php');
require('classes/Password.php');
require_once('classes/Article.php');
require_once('classes/LDUtils.php');
require_once('classes/JsonHelper.php');
require_once('classes/Articles.php');
require_once('classes/AdminArticleUtils.php');


$p=new Password();
$p->readFromFile('../'.PASSWORD_FILE);

function updateArticle(){
	$a=new Article();
	$a->readFromSession();
	$oldURI=$_SESSION['oldURI'];
	session_destroy();

	$u=new AdminArticleUtils(ARTICLES_DIR, ARTICLES_FILE);
	return $u->updateArticle($oldURI, $a, '..');
//	if ($u->removeArticle($oldURI,'..')==FALSE) return FALSE;	
//	return $u->addArticle($filename,$a,'..');
}

$p->secure("Modifica Articolo","admin_article_update_preview.php",'updateArticle');
?>
