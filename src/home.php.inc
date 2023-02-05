<?php 
require_once('classes/SocialAccounts.php');
require_once('classes/Address.php');
require_once('classes/Michelf/MarkdownInterface.php');
require_once('classes/Michelf/Markdown.php');

//name is mandatory
$name=htmlentities($j->{'foaf:name'});
$css=isset($_GET['bw']) ? 'styles/bw.css' :  'styles/cyangray.css';

?>
	<header>
<?php
	if (isset($logo)){
?>
		<img class="logo" src="<?=$logo?>" />
<?php } ?>
		<h1><?=$name?></h1>
		<p class="indirizzo">
<?php
	if (isset($j->{'org:hasPrimarySite'}) && isset($j->{'org:hasPrimarySite'}->{'locn:address'})){
		echo "\t\t\t".Address::jsonToString($j->{'org:hasPrimarySite'}->{'locn:address'})."\n";
	}
	echo "\t\t\t<a $disablelinktxt href=\"$srcpath/rss1feed.php\" title=\"RSS 1.0 Feed\" type=\"application/rss+xml\"><img src=\"$srcpath/icons/rss-feed-icon.png\" alt=\"RSS feed icon\"></img></a>\n";
?>
		</p>
	</header>

<?php
	if (isset($j->{'dcterms:description'})){
		$description=$j->{'dcterms:description'};
		$parser = new \Michelf\Markdown();
		$descriptionHtml=$parser->transform($description);
?>
	<section>
		<h2>Chi Siamo</h2>
<?=$descriptionHtml?>
	</section>
<?php
}

if (isset($j->{'foaf:mbox'}) || isset($j->{'foaf:account'}) || isset($j->{'foaf:phone'}) ){
?>
	<section>
		<h2>Contatti</h2>
		<ul class="contacts">
<?php
	if (isset($j->{'foaf:mbox'})){
			$mail=str_replace('mailto:','',$j->{'foaf:mbox'}->{'@id'});
			echo "\t\t".'<li><img src="https://upload.wikimedia.org/wikipedia/commons/8/8b/PICOL_icon_Mail.svg" /> e-mail <a href="mailto:'.$mail.'">'.$mail.'</a></li>'."\n";
	}

	if (isset($j->{'foaf:phone'})){
			$phone=str_replace('tel:','',$j->{'foaf:phone'}->{'@id'});
			echo "\t\t".'<li><img src="'.$srcpath.'/icons/iconfinder_Call01_928412.svg" /> tel <a href="tel:'.$phone.'">'.$phone.'</a></li>'."\n";
	}

	$socialAccounts=new SocialAccounts();
	if (isset($j->{'foaf:account'}))
		foreach($j->{'foaf:account'} as $account){
			$service=$account->{'foaf:accountServiceHomepage'}->{'@id'};
			$name=$account->{'foaf:accountName'};
			$url=$account->{'@id'};
			$p=$socialAccounts->presentations[$service];
			$iconUrl=$p->serviceIcon;
			if (!$utils->isAbsoluteURL($iconUrl))
				$iconUrl=$srcpath.'/'.$iconUrl;
			$desc=$p->accountTypeDescription;
			echo "\t\t\t<li><img src=\"$iconUrl\"/> $desc <a $disablelinktxt href=\"$url\">$name</a></li>\n";
		}
?>
		</ul>
	</section>
<?php
	}
	$i=$l->getFirstPastArticleIndex();
	$articles=$l->json->{'rss:items'}->{'rdf:li'};
	$numArticles=isset($articles) ? count($articles) : 0;
	if ($i!==FALSE && $numArticles>$i){
?>
	<section>
		<h2>Articoli</h2>
		<ol>
<?php
		$n=min($numArticles,$i+2);
		for(;$i<$n;$i++){
			$a=$articles[$i];
			$title=$a->{'rss:title'};
			$url=$a->{'@id'};
			$date=DateTime::createFromFormat(Articles::ISO8601, $a->{'dc:date'})->format('d/m/Y');
			if (!$utils->isAbsoluteURL($url))
				$url=$srcpath.'/viewArticle.php?url='.urlencode('../'.$url);
			echo "<li><em class=\"date\">$date</em> <a $disablelinktxt href=\"$url\">$title</a></li>\n";
		}
?>
		</ol>
<?php 
		if ($numArticles>$n)
			echo "\t\t<p><a $disablelinktxt href=\"$srcpath/listArticles.php\">vedi altri articoli</a></p>\n";
?>
	</section>
<?php
	}
?>	

	<section>
		<h2>Credits</h2>
		<p>Questo sito &egrave; realizzato sulla piattaforma <a $disablelinktxt href="https://github.com/cristianolongoodhl/littlehome">LITTLE HOME</a>. </p>
	</section>
