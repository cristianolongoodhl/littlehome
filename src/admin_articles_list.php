<?php
session_start();
require_once('../config.php');
require_once('classes/LDUtils.php');
require_once('classes/JsonHelper.php');
require_once('classes/Articles.php');
require_once('classes/Michelf/MarkdownInterface.php');
require_once('classes/Michelf/Markdown.php');
require_once('classes/Article.php');

$l=new Articles();
$l->readFromFile('../'.ARTICLES_FILE) or die('unable to read ../'.ARTICLES_FILE); 

$a=new Article();
if (isset($_POST['fromArticle'])){
	echo "<!-- store in session -->\n";
	$a->readFromForm();
	$a->storeInSession();
}

$utils=new LDUtils();
?>

<!DOCTYPE html>
<html lang="it">
<head>
	<title>LittleHome - Gestione Articoli</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="https://www.w3schools.com/w3css/4/w3.css" />
	<link rel="stylesheet" type="text/css" href="admin.css" />
</head>
<body>
	<h1>Amministrazione Sito - Gestione Articoli</h1>

<?php
	if ($l->count()>0){
		echo "<div class=\"w3-card-4\">\n\t<table class=\"w3-table w3-striped w3-bordered \">\n";	
		foreach($l->json->{'rss:items'}->{'rdf:li'} as $a){
			$title=$a->{'rss:title'};
			$url=$a->{'@id'};
			$date=DateTime::createFromFormat(Articles::ISO8601, $a->{'dc:date'})->format('d/m/Y');
			if (!$utils->isAbsoluteURL($url))
				$link='viewArticle.php?url='.urlencode('../'.$url);
			else
				$link=$url;

			$removeArticleURL='admin_article_remove.php?url='.urlencode($url);			
			$updateArticleURL='admin_article_update.php?updateURI='.urlencode($url);			
			echo "\t\t<tr>\n\t\t\t<td class=\"date\">$date</td>\n\t\t\t<td><a target=\"_blank\" href=\"$link\">$title</a></td>\n\t\t\t<td class=\"w3-right-align\"><a href=\"$updateArticleURL\" class=\"w3-btn w3-teal\">Modifica</a> <a href=\"$removeArticleURL\" class=\"w3-btn w3-teal\">&#x2501; Elimina</a></td>\n\t\t</tr>\n";
		} 
		echo "\t</table>\n</div>\n";	
	} else
		echo "<p>Non &egrave; presente alcun articolo.</p>\n";
?>		
	</div>

	<nav class="friendlyhome_admin_css nextprev">
		<a href="admin_article.php" class="w3-btn w3-teal">&#10010; Nuovo</a>
		<a href="admin.php" class="w3-btn w3-teal">&#10094; Indietro</a>
		<a href="admin_clear.php" class="w3-btn w3-teal">Esci &#10006;</a>
	</nav>
</body>
</html>
