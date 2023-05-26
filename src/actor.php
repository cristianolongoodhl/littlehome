<?php
/**
 * Generate the Activity Pub Actor descriptor from the organization description
 * @see https://www.w3.org/TR/activitypub/#actor-objects
 * @see https://www.w3.org/TR/activitystreams-vocabulary/#actor-types
 */

require_once('../config.php');
require_once('classes/LDUtils.php');
require_once('classes/Organization.php');
require_once('classes/Styles.php');
require_once('classes/ConfigHelper.php');
require_once('classes/AccessLogUtils.php');

$utils=new LDUtils();
AccessLogUtils::logAccess($utils->getCurrentPageURI(), '../'.ACCESS_FILE_PATH);

//9 is the lenght of actor.php
$baseURI=(empty($_SERVER['HTTPS']) ? 'http' : 'https').'://'.$_SERVER['SERVER_NAME'].(substr($_SERVER['REQUEST_URI'],0,strlen($_SERVER['REQUEST_URI'])-9));

$c=new ConfigHelper('../'.ORGANIZATION_FILE, '../'.STYLES_FILE);
$o=$c->organization;
//4 is the lenght of 'src/';
$logo=$c->getLogo(substr($baseURI, 0, strlen($baseURI)-4));


$actor=new stdClass();
$actor->{"@context"}=array("https://www.w3.org/ns/activitystreams", "https://w3id.org/security/v1");
$actor->id=$baseURI."actor.php";
$actor->type="Organization";
$actor->preferredUsername="info";
$actor->name=$c->getName();
$actor->summary=$o->json->{'dcterms:description'};
if ($logo!=false)
	$actor->icon=$logo;	

//actor collections
$actor->inbox=$baseURI."inbox.php";
$actor->outbox=$baseURI."outbox.php";
//dummy followers URI, just used in activity targets
$actor->followers=$baseURI."followers";

//RSA key (assuming it has been created)
$pubKeyPath='../'.KEYS_DIR."/public.pem";

$actor->publicKey=new stdClass();
$actor->publicKey->id=$actor->id."#main-key";
$actor->publicKey->owner=$actor->id;
$actor->publicKey->publicKekPem=file_get_contents($pubKeyPath);

header("Content-Type: application/activity+json");
header('Access-Control-Allow-Origin: *');

print json_encode($actor, JSON_PRETTY_PRINT);
?>
