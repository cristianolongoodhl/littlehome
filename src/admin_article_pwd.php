<?php
session_start();
require_once('../config.php');
require('classes/Password.php');
require_once('classes/Article.php');
require_once('classes/LDUtils.php');
require_once('classes/JsonHelper.php');
require_once('classes/Articles.php');
require_once('classes/AdminArticleUtils.php');
require_once('classes/Activity.php');
require_once('classes/ActivityPubUtils.php');


$p=new Password();
$p->readFromFile('../'.PASSWORD_FILE);

function getCreateActivity(string $articleFileName, Article $a){
	$url=ActivityPubObject::getSrcDirURI().'viewArticle.php?url='.urlencode('../'.$articleFileName);
	$object=createActivityPubPageObject($url, $a);
	return Activity::getCreateActivity($url.'&activity', $object);
}
/**
 * Create an ActivityPub Object of type Page
 * @param string $url page url
 * @see https://www.w3.org/TR/activitystreams-vocabulary/#dfn-page
 */
function createActivityPubPageObject(string $url, Article $a){
	
	$o=new ActivityPubObject($url, 'Page');
	$o->name=$a->title;
	$o->published=$a->date->format(Articles::W3CDATE);
	$o->attributedTo=ActivityPubObject::getOrganizationActorURI();
	$o->to=array('https://www.w3.org/ns/activitystreams#Public');
	$o->url=$url;
	return $o;
}

function handleNewArticle(){
	$filename=(new DateTimeImmutable())->format('YmdHisu').'.md';
	$a=new Article();
	$a->readFromSession();
	session_destroy();
	
	//save article
	$u=new AdminArticleUtils(ARTICLES_DIR, ARTICLES_FILE);
	if ($u->addArticle($filename, $a, '..')==false){
		print 'Unable to save article';
		return false;
	}
	
	$articleFilePath= ARTICLES_DIR==='.' ? $filename : ARTICLES_DIR.'/'.$filename;
	$c=getCreateActivity($articleFilePath, $a);
	$u=new ActivityPubUtils('../');
	return $u->sendToAll($c);
}

$p->secure("Nuovo Articolo","admin_article_preview.php",'handleNewArticle');
?>
