<?php 
require_once('classes/LDUtils.php');
require_once('classes/SocialAccounts.php');
require_once('classes/Organization.php');
require_once('classes/Styles.php');
require_once('classes/ConfigHelper.php');
require_once('classes/Articles.php');
require_once('classes/AccessLogUtils.php');

$c=new ConfigHelper(ORGANIZATION_FILE, STYLES_FILE);
$j=$c->organization->json;

$title=$c->getName();
$css=$c->getCSS();

if (isset($j->{'foaf:logo'}))
	$logo=$j->{'foaf:logo'}->{'@id'};

$l=new Articles();
$l->readFromFile(ARTICLES_FILE) or die('unable to read '.ARTICLES_FILE); 

$utils=new LDUtils();
$srcpath='src';
$uri=$utils->getCurrentPageURI();
$disablelinktxt='';
AccessLogUtils::logAccess($uri, ACCESS_FILE_PATH);
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<title><?=$title?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?=$css?>" />
	<link rel="alternate" href="src/rss1feed.php" title="atricles feed" type="application/rss+xml" />
	<meta property="og:url" content="<?=$uri?>" />	
	<meta property="og:title" content="<?=$title?>" />
	<meta property="og:type" content="website" />
	<script src="src/logaccess.php?resource=<?=urlencode($uri)?>"></script>
</head>
<body>
<?php
require('home.php.inc');
?>
</body>
</html>
