<?php
/**
  * The body of a page showing an article.
  */	


if (!$a) 
	die("No such article");

$dateStr=$a->getDateFormatted();
$remoteConfig=$a->getContentAsHTML();
$title=htmlentities($a->title);
?>
	<header>
<?php
	if ($logo){
?>
		<a <?=$disablelinktxt?> href="../index.php" title="pagina principale"><img class="logo" src="<?=$logo?>" /></a>
<?php } ?>
		<p class="orgname"><?=$orgName?></p>
<?php
	if ($address)
		echo "\t\t<p class=\"indirizzo\">$address</p>\n";
?>
	</header>
	<nav> <a <?=$disablelinktxt?> href="listArticles.php">&lt;&lt; articoli</a></nav>
	
	<section id="article">
<?php
	if ($dateStr!==null){
		echo "<p class=\"date\">$dateStr</p>\n";
	}
?>
		<h1 class="title"><?=$title?></h1>
<?php
	if (isset($a->image)){
		$image=$a->image;
?>
		<img src="<?=$image?>" />
<?php
	}
?>

		<?=$remoteConfig?>
	</section>
