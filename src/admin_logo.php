<?php
session_start();
require_once('../config.php');
require_once('classes/LDUtils.php');
require_once('classes/SocialAccounts.php');
require_once('classes/Organization.php');
require_once('classes/Logo.php');

$utils=new LDUtils();
$o=new Organization();
$l=new Logo('../'.IMG_DIR);

$uploaderror=false;
if ($o->readFromForm($_POST))
	$o->storeInSession();
else{
	$o->readFromSession();
	$hasTmpLogo=$l->getTmpLogoFromOrgJson($o->json);
	if (isset($_POST['clearLogo'])){
		$l->clearTmpLogo();
		unset($o->json->{'foaf:logo'});
	} else if (isset($_FILES['newlogo'])){
		$filename=$l->upload('newlogo');
		if ($filename!=false){
			$l->clearTmpLogo();
			$utils->addObjectTripleIfNotEmpty($o->json, 'foaf:logo', IMG_DIR.'/'.$filename);
			$o->storeInSession();
		} else $uploaderror=true;
	}
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<title>LittleHome - Amministrazione Sito - Logo</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="https://www.w3schools.com/w3css/4/w3.css" />
	 <link rel="stylesheet" type="text/css" href="admin.css">
</head>
<body>
	<h1>Amministrazione Sito - Logo</h1>
<?php
?>		
	
	<div class="w3-card-4">
	<form action="admin_logo.php" method="POST" enctype="multipart/form-data" class="w3-container">
<?php
if (isset($o->json->{'foaf:logo'})){
	$logo=$o->json->{'foaf:logo'}->{'@id'};
	$logoFileName=basename($logo);
	echo "\t<p class=\"logopreview\">\n\t\t<img src=\"../$logo\" alt=\"$logo\"/>\n\t\t<p><code>$logoFileName</code></p>\n\t\t<p><label for=\"logo\">Modifica Logo</label>\n";
} else {
	echo "\t<p class=\"logopreview\">\n\t\t<em>Nessun logo specificato</em> </p>\n\t\t<p><label for=\"logo\">Inserisci Logo</label>\n";
}
if ($uploaderror)
	echo "\t<p class=\"logopreview\">\n\t\t<em>Impossibile caricare il nuovo logo</em> </p>\n\t\t<p><label for=\"logo\">Inserisci Logo</label>\n";
	?>
		<input type="file" name="newlogo" onchange="submit()" class="w3-input w3-border" /></p>
		<p><input type="submit" class="w3-btn w3-teal" name="clearLogo" value="Rimuovi Logo" /></p>
	</form>
	</div>

	<nav class="nextprev">
		<a href="admin_info.php" class="w3-btn w3-teal ">&#10094; Indietro</a>
		<a href="admin_clear.php" class="w3-btn w3-teal">Esci &#10006;</a>
		<a href="admin_css.php" class="w3-btn w3-teal ">Avanti &#10095;</a>
	</nav>
</body>
</html>
 