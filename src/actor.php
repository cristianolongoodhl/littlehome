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
require_once('classes/Michelf/MarkdownInterface.php');
require_once('classes/Michelf/Markdown.php');
require_once('classes/ActivityPubObject.php');

//URI of the src directory
//9 is the lenght of actor.php
$srcURI=ActivityPubObject::getSrcDirURI();

//URI of the installation root
//4 is the lenght of src/
$baseURI=substr($srcURI, 0, strlen($srcURI)-4);

$remoteConfig=new ConfigHelper('../'.ORGANIZATION_FILE, '../'.STYLES_FILE);
//4 is the lenght of 'src/';
$logo=$remoteConfig->getLogo($baseURI);


$actor=new stdClass();
$actor->{"@context"}=array("https://www.w3.org/ns/activitystreams", "https://w3id.org/security/v1");
$actor->id=ActivityPubObject::getOrganizationActorURI();
$actor->type="Organization";
$actor->preferredUsername="info";
$actor->name=$remoteConfig->getName();
$actor->summary=$remoteConfig->getHTMLDescription();
$actor->url=$baseURI;
if ($logo!=false){
	$actor->icon=new stdClass();
	$actor->icon->type="image";
	$actor->icon->url=$logo;
}
//actor collections
$actor->inbox=$srcURI."inbox.php";
$actor->outbox=$srcURI."outbox.php";
//dummy followers URI, just used in activity targets
$actor->followers=$srcURI."followers";

//RSA key (assuming it has been created)
$pubKeyPath='../'.KEYS_DIR."/public.pem";

$actor->publicKey=new stdClass();
$actor->publicKey->id=$actor->id."#main-key";
$actor->publicKey->owner=$actor->id;
$actor->publicKey->publicKeyPem=file_get_contents($pubKeyPath);

header("Content-Type: application/activity+json");
header('Access-Control-Allow-Origin: *');

print json_encode($actor, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
?>
