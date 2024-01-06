<?php 

//see also https://www.w3schools.com/w3css/w3css_input.asp

require_once('../config.php');
require_once('classes/LDUtils.php');
require_once('classes/SocialAccounts.php');
require_once('classes/Organization.php');
require_once('classes/Logo.php');

session_start();

$o=new Organization();
$l=new Logo('../'.IMG_DIR);

if (!$o->readFromSession()){
	$o->readFromFile("../".ORGANIZATION_FILE);
	$l->storeOldLogoInSession($o->json);
}

//name is mandatory
$name=isset($o->json->{'foaf:name'}) ? $o->json->{'foaf:name'} : '';
$description=isset($o->json->{'dcterms:description'}) ? $o->json->{'dcterms:description'} : '';
$logo=isset($o->json->{'foaf:logo'}) ? $o->json->{'foaf:logo'}->{'@id'} : '';
$mail=isset($o->json->{'foaf:mbox'}) ? substr($o->json->{'foaf:mbox'}->{'@id'},7) : '';
$phone=isset($o->json->{'foaf:phone'}) ? substr($o->json->{'foaf:phone'}->{'@id'},4) : '';
if (isset($o->json->{'foaf:isPrimaryTopicOf'})){
	$linkURL=$o->json->{'foaf:isPrimaryTopicOf'}->{'@id'};
	if (isset($o->json->{'foaf:isPrimaryTopicOf'}->{'dcterms:title'}))
		$linkName=$o->json->{'foaf:isPrimaryTopicOf'}->{'dcterms:title'};
	else
		$linkName='';
} else {
	$linkURL='';
	$linkName='';
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<title>LittleHome - Amministrazione Sito - Informazioni</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="https://www.w3schools.com/w3css/4/w3.css" />
	<link rel="stylesheet" type="text/css" href="admin.css" />
</head>
<body>
	<h1>Amministrazione Sito - Informazioni</h1>
	<form action="admin_logo.php" method="POST" enctype="multipart/form-data">
		<div class="w3-card-4">
			<div class="w3-container w3-teal">
				<h2>Informazioni di Base</h2>
			</div>
			<fieldset class="w3-container">
				<p><label for="name">Nome</label><input type="text" class="w3-input w3-border" name="name" value="<?=$name?>" required /></p>
				<p><label for="description">Descrizione</label>
<?php
if ($logo!=='')
	echo "\t\t\t\t<input type=\"hidden\" name=\"logo\" value=\"$logo\" />\n"
?>
				<textarea name="description" class="w3-input w3-border" rows="10"><?=$description?></textarea></p>
			</fieldset>
		</div>

		<div class="w3-card-4">
			<div class="w3-container w3-teal">
				<h2>Indirizzo</h2>
			</div>
			<fieldset class="w3-container">
<?php
if (isset($o->json->{'org:hasPrimarySite'}) && isset($o->json->{'org:hasPrimarySite'}->{'locn:address'})){
	$address=$o->json->{'org:hasPrimarySite'}->{'locn:address'};
	$thoroughfareValueStr=isset($address->{'locn:thoroughfare'}) ? 'value="'.$address->{'locn:thoroughfare'}.'"' : ''; 
   	$locatorDesignatorValueStr=isset($address->{'locn:locatorDesignator'}) ? 'value="'.$address->{'locn:locatorDesignator'}.'"' : '';
	$poBoxValueStr=isset($address->{'locn:poBox'}) ? 'value="'.$address->{'locn:poBox'}.'"':'';
	$postNameValueStr=isset($address->{'locn:postName'}) ? 'value="'.$address->{'locn:postName'}.'"':'';
} else {
	$thoroughfareValueStr=''; 
	$locatorDesignatorValueStr='';
	$poBoxValueStr='';
	$postNameValueStr='';
}
?>
				<p><label for="thoroughfare">Via/Strada/Piazza</label> <input type="text" name="thoroughfare" class="w3-input w3-border" <?=$thoroughfareValueStr?> /></p>
				<p><label for="locatorDesignator">Numero Civico</label> <input type="text" name="locatorDesignator" class="w3-input w3-border" <?=$locatorDesignatorValueStr?> /></p>
				<p><label for="poBox"><abbr title="Codice di Avviamento Postale">CAP</abbr></label> <input type="text" name="poBox" class="w3-input w3-border" <?=$poBoxValueStr?> /></p>
				<p><label for="postName">Comune</label> <input type="text" name="postName" class="w3-input w3-border" <?=$postNameValueStr?> /></p>
			</fieldset>
		</div>

		<div class="w3-card-4">
			<div class="w3-container w3-teal">
				<h2>Contatti</h2>
			</div>
			<fieldset class="w3-container">
				<p><label for="email">E-mail</label> <input name="email" type="email" value="<?=$mail?>" class="w3-input w3-border" /> </p>
				<p><label for="phone">Telefono</label>  <input name="phone" type="tel" value="<?=$phone?>" class="w3-input w3-border" /> </p>

				<h3>Collegamento ad una pagina esterna</h3>
				<p><label for="linkurl">Indirizzo pubblico (URL) della pagina esterna</label><input name="linkurl" type="url" value="<?=$linkURL?>" class="w3-input w3-border" /></p>
				<p><label for="linkname">Etichetta con la quale sar&agrave; mostrato il collegamento</label> <input name="linkname" type="text" value="<?=$linkName?>" class="w3-input w3-border" /></p>

<?php
$socialAccounts=new SocialAccounts();
foreach($socialAccounts->presentations as $service => $presentation){
	$serviceId=$presentation->accountTypeDescription;
	$account=$socialAccounts->getAccount($o->json,$service);
	$nameField=$presentation->nameField;
	$urlField=$presentation->urlField;
	echo "\t\t\t\t<h3>$serviceId</h3>\n";
	if ($account===NULL){
 		echo "\t\t\t\t<p><label for=\"$nameField\">Nome dell'account</label><input type=\"text\" name=\"$nameField\" class=\"w3-input w3-border\" /></p>\n";
		echo "\t\t\t\t<p><label for=\"$urlField\">indirizzo pubblico (URL) dell'account</label><input type=\"url\" name=\"$urlField\" class=\"w3-input w3-border\" /></p>\n";
	} else {
		$accountName=$account->{'foaf:accountName'};
		$accountURL=$account->{'@id'};
		echo "\t\t\t\t<label for=\"$nameField\">Nome dell'account</label><input type=\"text\" name=\"$nameField\" value=\"$accountName\" class=\"w3-input w3-border\" />\n";
		echo "\t\t\t\t<label for=\"$urlField\">Indirizzo pubblico (URL) dell'account</label><input type=\"url\" name=\"$urlField\" value=\"$accountURL\" class=\"w3-input w3-border\" />\n";
	}
}
?>			
			</fieldset>
		</div>

		<nav class="nextprev">
			<a href="admin_clear.php" class="w3-btn w3-teal ">Esci &#10006;</a>
			<input type="submit" name="fromInfo"  value="Avanti &#10095;" class="w3-btn w3-teal " />
		</nav>
	</form>
</body>
</html>