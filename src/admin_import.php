<?php 
require_once 'classes/ConfigObject.php';
require_once 'classes/LDUtils.php';
require_once 'classes/ActivityPubUtils.php';


if (!isset($_POST['configurl']))
	die("No configuration specified");

if (!isset($_POST['privatekey']))
	die("No private key specified");

$remoteConfig=new ConfigObject();
$error='No error';
$loadconfigstatus=$remoteConfig->loadRemote($_POST['configurl'], $error);
$privatekey=trim($_POST['privatekey'])."\n";
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<title>LittleHome - Amministrazione Sito - Importazione della configurazione</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="https://www.w3schools.com/w3css/4/w3.css" />
	<link rel="stylesheet" type="text/css" href="admin.css" />
</head>
<body>
	<h1>Amministrazione Sito - Importazione configurazione</h1>
<?php 
if ($loadconfigstatus==true){
	$utils=new LDUtils();
	createRequiredDirectories($utils);
	storePrivateKeyOrDie($privatekey);
	
	$downloads=array($remoteConfig->styles => STYLES_FILE,
		$remoteConfig->accesslog =>ACCESS_FILE_PATH,
		$remoteConfig->accesslogjs => ACCESS_FILE_PATH_JS,
		$remoteConfig->knowninboxes => KNOWN_INBOXES_FILE,
		$remoteConfig->inbox => INBOX_FILE,
		$remoteConfig->password => PASSWORD_FILE,
		$remoteConfig->keysdir.'/public.pem' => KEYS_DIR.'/public.pem'
	);
	
	
	echo "\t<table class=\"w3-table-all\">\n";
	echo "\t\t<tr><th>from</th><th>to</th><th>result</th></tr>\n";
	
	// organization
	$failurecause='';
	$error=!importOrganization($utils, $remoteConfig, $downloads, $failurecause);
	$organizationDownloadOutcome= $error ? $failurecause : 'OK';
	echo "\t\t\t<tr><td>$remoteConfig->organization</td><td>".ORGANIZATION_FILE."</td><td>$organizationDownloadOutcome</td></tr>\n";
	
	//articles
	$articlesDownloadOutcome=importArticles($utils, $remoteConfig, $downloads, $failurecause);
	$articlesDownloadOutcomeMessage= $articlesDownloadOutcome ? 'OK' : $failurecause;
	echo "\t\t\t<tr><td>$remoteConfig->articles</td><td>".ARTICLES_FILE."</td><td>$articlesDownloadOutcomeMessage</td></tr>\n";
	$error|=$articlesDownloadOutcome;
	/*
	 *	$this->keysdir=$base.KEYS_DIR;
	 */
	
	foreach ($downloads as $sourceUri => $targetFilePath){
		$failurecause='';
		if ($utils->download($sourceUri, '../'.$targetFilePath, false, $failurecause))
			$result='OK';
		else {
			$result='Failed: '.$failurecause;
			$error=true;
		}
		echo "\t\t\t<tr><td>$sourceUri</td><td>$targetFilePath</td><td>$result</td></tr>\n";
	}
	echo "\t<table>\n";
	
	$updateOutcome=(new ActivityPubUtils('../'))->sendActorUpdate();
	print $updateOutcome ? '<p>Le notifiche ActivityPub sono state inviate.</p>' :
		'<p>Errore nell\'invio delle notifiche ActivityPub.</p>';
?>
	<!-- p>Configurazione importata con successo</p -->
	<nav class="nextprev">
		<a href="admin.php" class="w3-btn w3-teal ">Torna alla pagina di amministrazione</a>
		<a href="../index.php" class="w3-btn w3-teal ">Vai al Sito</a>
	</nav>
<?php 
} else {
?>	
	<p>Errore nell'importazione: <?=$error?></p>
	<nav class="nextprev">
		<a href="admin.php" class="w3-btn w3-teal ">Indietro</a>
	</nav>
<?php 
} 
?>	
</body>
</html>	
<?php 
/**
 * Attempt to create a directory if not exists, die if the creation fails.
 * @param string $path
 */
function createDirectoryIfNotExistsOrDie($path){
	if (!file_exists($path))
		if (mkdir($path)==false)
			die('Unable to create directory '.$path);
}

/**
 * Create the directories required by the local configuration
 * 
 * @param LDUtils $utils
 */
function createRequiredDirectories(LDUtils $utils){
	createDirectoryIfNotExistsOrDie('../'.IMG_DIR);
	createDirectoryIfNotExistsOrDie('../'.ARTICLES_DIR);
	if ($utils->createKeysDirIfNotExists('../'.KEYS_DIR)==false)
		die('Unable to create directory '.KEYS_DIR);
}

/**
 * Store the private key in the keysDir
 * @param string $privatekey
 */
function storePrivateKeyOrDie(string $privatekey){
	$privatekeypath='../'.KEYS_DIR.'/private.pem';
	if (file_put_contents($privatekeypath, $privatekey)==false)
		die('Unable to write private key '.$privatekeypath);
}

/**
 * Setup the org file by downloading and updating it in accordance with the 
 * local configuration.
 *  
 * @param LDUtils $utils
 * @param ConfigObject $remoteConfig configuration of the remote installation
 * @param array downloads put here additional files which must be downloaded
 * @param string $failurecause return parameter for eventual error messages
 * 
 * @return boolean true if succeds, false otherwise
 */
function importOrganization(LDUtils $utils, ConfigObject $remoteConfig, array &$downloads, string &$failurecause){
	$uri=$remoteConfig->organization;
	$organization=$utils->loadRemoteJson($uri, $failurecause);
	if ($organization==false) return false;
	if (isset($organization->{'foaf:logo'})){
		$oldSiteLogoURI=($utils->isAbsoluteUrl($organization->{'foaf:logo'}->{'@id'}) ? '' : $remoteConfig->base).$organization->{'foaf:logo'}->{'@id'};
		$targetLogoFile=IMG_DIR.'/'.basename($oldSiteLogoURI);
		$downloads[$oldSiteLogoURI]=$targetLogoFile;
		$organization->{'foaf:logo'}->{'@id'}=$targetLogoFile;
	}
	
	if (file_put_contents('../'.ORGANIZATION_FILE,json_encode($organization))!=false)
		return true;
	$failurecause='unable to write organization file'.ORGANIZATION_FILE;
	return false;
}

/**
 * Setup the articles file by downloading and updating it in accordance with the
 * local configuration.
 *
 * @param LDUtils $utils
 * @param ConfigObject $remoteConfig configuration of the remote installation
 * @param array downloads put here additional files which must be downloaded
 * @param string $failurecause return parameter for eventual error messages
 *
 * @return boolean true if succeds, false otherwise
 */
function importArticles(LDUtils $utils, ConfigObject $remoteConfig, array &$downloads, string &$failurecause){
	$articles=$utils->loadRemoteJson($remoteConfig->articles, $failurecause);
	if ($articles==false) return false;
	if ($articles->{'rss:items'}!=null && $articles->{'rss:items'}->{'rdf:li'}!=null)
		foreach ($articles->{'rss:items'}->{'rdf:li'} as $a)
			if (!$utils->isAbsoluteUrl($a->{'@id'})){
				$oldUri=$remoteConfig->base.$a->{'@id'};
				$filename=basename($a->{'@id'});
				$a->{'@id'}=ARTICLES_DIR.'/'.$filename;
				$downloads[$oldUri]=$a->{'@id'};
			}
	if (file_put_contents('../'.ARTICLES_FILE,json_encode($articles))!=false)
		return true;
	$failurecause='unable to write articles file'.ARTICLES_FILE;
	return false;		
}
	
?>