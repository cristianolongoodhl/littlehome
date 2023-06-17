<?php
require_once('../config.php');
require_once('classes/Activity.php');
require_once('classes/ActivityPubUtils.php');
require_once('classes/AccessLogUtils.php');

function handleFollowRequest($followRequestActivity)
{
	$requester=$followRequestActivity->actor;
	if ($requester===null){
		http_response_code(400);
		print 'Invalid format for the incoming message: missing actor field';
		exit;		
	}
	$utils=new ActivityPubUtils('../');
	$inbox=$utils->saveInbox($requester);	
	
	$acceptActivity=Activity::getAcceptActivity($followRequestActivity->id);	
	$utils->send($acceptActivity, array($inbox));
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
			if (Activity::getOrganizationActorURI() == $activity->object){
				AccessLogUtils::logAccess("follow request", '../'.ACCESS_FILE_PATH);
				handleFollowRequest($activity);
			}
			//just discard if it isn't a follow request to me
			break;
	}
	// The request is using the POST method
}