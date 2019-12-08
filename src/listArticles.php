<?php
/**
  * Show an article whose source is written in MarkDown.
  */
require_once('../config.php');
require_once('classes/LDUtils.php');
require_once('classes/Organization.php');
require_once('classes/Styles.php');
require_once('classes/ConfigHelper.php');
require_once('classes/Articles.php');

$c=new ConfigHelper('../'.ORGANIZATION_FILE, '../'.STYLES_FILE);
$orgName=$c->getName();
$css=$c->getCss('../');
$logo=$c->getLogo('../');
$address=$c->getAddress('../');

$l=new Articles();
$l->readFromFile('../'.ARTICLES_FILE) or die('unable to read ../'.ARTICLES_FILE); 

$utils=new LDUtils();
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<title><?=$orgName?> - Articoli</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?=$css?>">
</head>
<body>
	<header>
<?php
	if ($logo){
?>
		<a href="../index.php" title="pagina principale"><img class="logo" src="<?=$logo?>" /></a>
<?php } ?>
		<p class="orgname"><?=$orgName?></p>
<?php
	if ($address)
		echo "\t\t<p class=\"indirizzo\">$address</p>\n";
?>
	</header>
	<nav> <a href="../index.php">&lt;&lt; pagina principale</a></nav>
	
	<section>
		<h1 class="title">Articoli</h1>
	<ol>
<?php
	$i=$l->getFirstPastArticleIndex();
	$lis=$l->json->{'rss:items'}->{'rdf:li'};
	$n=count($lis);
	if ($n>$i)
		for(;$i<$n; $i++){
			$a=$lis[$i];
			$date=DateTime::createFromFormat(Articles::W3CDATE, $a->{'dc:date'});	
			$dateStr=$date->format('d/m/Y');
			$title=$a->{'rss:title'};
			$url=$a->{'@id'};
			if (!$utils->isAbsoluteURL($url))
				$url='viewArticle.php?url='.urlencode('../'.$url);
			echo "<li><em class=\"date\">$dateStr</em> <a href=\"$url\">$title</a></li>\n";
		}
?>		
	</ol>
	</section>

</body>
</html>
