<?php
require_once('../config.php');
require_once('classes/AcceptActivity.php');
require_once('classes/ActivityPubUtils.php');
require_once('classes/AccessLogUtils.php');

//9 is size of inbox.php
$baseURI=(empty($_SERVER['HTTPS']) ? 'http' : 'https').'://'.$_SERVER['SERVER_NAME'].(substr($_SERVER['REQUEST_URI'],0,strlen($_SERVER['REQUEST_URI'])-9));

function handleFollowRequest($baseURI, $followRequestActivity)
{
	$requester=$followRequestActivity->actor;
	if ($requester===null){
		http_response_code(400);
		print 'Invalid format for the incoming message: missing actor field';
		exit;		
	}
	$utils=new ActivityPubUtils('../', $baseURI);
	$inbox=$utils->saveInbox($requester);	
	
	$acceptActivity=new AcceptActivity($baseURI, $followRequestActivity->id);	
	$utils->send($acceptActivity, $inbox);
}


/**
 * Inbox receiving Activity Pub activities.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {	
	$activity = json_decode(file_get_contents("php://input"), false);
	if ($activity === null) {
		http_response_code(400);
		print 'Invalid format for the incoming message: not a JSON';
		exit();
	}
	switch ($activity->type) {
		case 'Follow':
			if ($baseURI.'actor.php' == $activity->object){
				AccessLogUtils::logAccess("follow request", '../'.ACCESS_FILE_PATH);
				handleFollowRequest($baseURI, $activity);
			}
			//just discard if it isn't a follow request to me
			break;
	}
	// The request is using the POST method
}