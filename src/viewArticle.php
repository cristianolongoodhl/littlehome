<?php
/**
  * Show an article whose source is written in MarkDown.
  */
require_once('../config.php');
require_once('classes/LDUtils.php');
require_once('classes/Organization.php');
require_once('classes/Styles.php');
require_once('classes/ConfigHelper.php');
require_once('classes/Michelf/MarkdownInterface.php');
require_once('classes/Michelf/Markdown.php');
require_once('classes/Article.php');
require_once('classes/AccessLogUtils.php');

$utils=new LDUtils(); 
$c=new ConfigHelper('../'.ORGANIZATION_FILE, '../'.STYLES_FILE);
//$orgName=$c->organization->json->{'foaf:name'};
$orgName=$c->getName();
$css=$c->getCss('../');
$logo=$c->getLogo('../');
$address=$c->getAddress('../');
$a=Article::readFromGETParameterURL();
$title=$a->title;
$uri=$utils->getCurrentPageURI();
$disablelinktxt='';
$currentPageURI=$utils->getCurrentPageURI();
AccessLogUtils::logAccess($utils->getCurrentPageURI(), '../'.ACCESS_FILE_PATH);
?>
<!DOCTYPE html>
<html lang="it" xmlns:og="http://ogp.me/ns#">
<head>
	<title><?=$orgName;?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?=$css?>">
	<meta property="og:url" content="<?=$uri?>" />	
	<meta property="og:title" content="<?=$title?>" />
	<meta property="og:type" content="article" />
<?php
	if (isset($a->image)){
?>
	<meta property="og:image" content="<?=$a->image?>" />
<?php
	}
?>
	<script src="logaccess.php?resource=<?=urlencode($currentPageURI)?>"></script>
</head>
<body>
<?php
include('viewArticle.php.inc');
?>


</body>
</html>
