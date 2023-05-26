<?php

//change this with the relative path of the src directory with respect to the host URL
define("SRC_PATH", "src");

/**
 * Handle WebFinger (RFC7033) requests in order to return user actor URI from actor preferred name.
 */  
$resource=$_GET['resource'];
if (!isset($resource)){
	http_response_code(400); 
	print 'no resource provided';
	exit;
}

if (!str_starts_with($resource,'acct:')){
	http_response_code(400); 
	print 'resource must start with acct:';
	exit;
}

//5 is strlen('acct:');
$resourcePieces=explode('@',substr($resource, 5));
if (count($resourcePieces)!=2){
	http_response_code(400); 
	print 'invalid actor';
	exit;
}

$username=$resourcePieces[0];
$hostname=$resourcePieces[1];
if (!($username=='info' && $hostname==$_SERVER['SERVER_NAME'])){
	http_response_code(404); 
	print $username.'@'.$hostname.' not found';
	exit;
}

header('Content-Type: application/jrd+json');
header('Access-Control-Allow-Origin: *');
$jrd=new stdClass();
$jrd->subject=$resource;
$link=new stdClass();
$jrd->links=array($link);
$link->rel='self';
$link->type='application/activity+json';
$link->href=(empty($_SERVER['HTTPS']) ? 'http' : 'https').'://'.$_SERVER['SERVER_NAME'].'/'.SRC_PATH.'/actor.php';

print json_encode($jrd, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
?>
