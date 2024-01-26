<?php
require_once('../config.php');
require('classes/Password.php');
require_once('classes/LDUtils.php');
require_once('classes/SocialAccounts.php');
require_once('classes/Organization.php');
require_once('classes/Styles.php');
require_once('classes/Logo.php');
require_once('classes/Articles.php');

/**
 * Generate key pairs directory and keys in pem format, if keys directory not exists 
 * 
 * @return boolean true if the directory and key pair have been generated
 */
function generateKeyPairsIfNotExists(){
	$keyDirPath='../'.KEYS_DIR;
	// do nothing if keys dir already exists
	if (!(new LDUtils())->createKeysDirIfNotExists($keyDirPath))
		return false;
	// Generate a new private key
	$privateKey = openssl_pkey_new(array(
		"private_key_bits" => 2048,
		"private_key_type" => OPENSSL_KEYTYPE_RSA,
	));
	
	// Then save the pem for private key
	if (openssl_pkey_export_to_file($privateKey, $keyDirPath.'/private.pem')!=true){
		rmdir($keyDirPath);
		exit(openssl_error_string());
	}
	
	//now generate and save public key
	$publicKey=openssl_pkey_get_details($privateKey)['key'];
	file_put_contents($keyDirPath.'/public.pem', $publicKey);
	return true;
}
session_start();
$p=new Password();
$keyGenerated=false;
if (isset($_POST['password'])){
		$password=$_POST['password'];
		$o=new Organization();
		$o->readFromSession();

		$s=new Styles();
		$s->readFromSession();

		$l=new Logo('../'.IMG_DIR);
		$l->getTmpLogoFromOrgJson($o->json);

		if ($p->readFromFile('../'.PASSWORD_FILE))
			if ($p->check($password)){
				$o->writeToFile('../'.ORGANIZATION_FILE);
				$s->writeToFile('../'.STYLES_FILE);
				$l->handleTmpLogoConfirmed();
				$keyGenerated=generateKeyPairsIfNotExists();
				session_destroy();
				include('admin_save.php.inc');
			} else {
				$message="<p>Password Errata</p>\n";
				include('admin_pwd.php.inc');
			}
		else {
			if (strcmp($password, $_POST['confirm'])===0){
				$p->writeToFile($password,'../'.PASSWORD_FILE);		
				$o->writeToFile('../'.ORGANIZATION_FILE);
				$s->writeToFile('../'.STYLES_FILE);
				Articles::writeEmpty('../'.ARTICLES_FILE);
				$keyGenerated=generateKeyPairsIfNotExists();
				session_destroy();
				include('admin_save.php.inc');
			} else {
				$message="<p>Le due password non coincidono</p>";
				include('admin_create_pw.php.inc');
			}
		}
} else if ($p->readFromFile('../'.PASSWORD_FILE)){
	$message="";
	include('admin_pwd.php.inc');
} else {
	$message="";
	include('admin_create_pw.php.inc');
}
?>
