<?php
session_start();
require_once('../config.php');
require_once('classes/LDUtils.php');
require_once('classes/SocialAccounts.php');
require_once('classes/Organization.php');
require_once('classes/Styles.php');
require_once('classes/ConfigHelper.php');
require_once('classes/Michelf/MarkdownInterface.php');
require_once('classes/Michelf/Markdown.php');
require_once('classes/Article.php');

$remoteConfig=new ConfigHelper('../'.ORGANIZATION_FILE, '../'.STYLES_FILE);
$css=$remoteConfig->getCss('../');
$orgName=$remoteConfig->getName();
$logo=$remoteConfig->getLogo('../');
$address=$remoteConfig->getAddress('../');

$a=new Article();
if (isset($_POST['fromArticle'])){
	$a->readFromForm();
	$a->storeInSession();
} else
	$a->readFromSession();
$disablelinktxt='onclick="return false"';
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<title>LittleHome - Amministrazione Sito - Modifica Articolo - Anteprima</title>
	<meta charset="UTF-8" />
	<link id="style" rel="stylesheet" type="text/css" href="<?=$css?>" />
	<link id="style" rel="stylesheet" type="text/css" href="admin_css.css" />
</head>
<body>
	<nav class="friendlyhome_admin_css">
		<a href="admin_article_update.php" class="w3-btn w3-teal">&#10094; Indietro</a>
		<a href="admin_clear.php" class="w3-btn w3-teal">Esci &#10006;</a>
		<a href="admin_article_update_pwd.php" class="w3-btn w3-teal">Avanti &#10095;</a>
	</nav>
<?php 
include('viewArticle.php.inc');
?>
</body>
