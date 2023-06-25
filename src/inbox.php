<?php
use src\classes\ActivityPubOrderedCollection;

require_once ('../config.php');
require_once ('classes/Activity.php');
require_once ('classes/ActivityPubUtils.php');
require_once ('classes/AccessLogUtils.php');
require_once ('classes/ActivityPubOrderedCollection.php');

header('Access-Control-Allow-Origin: *');

function handleFollowRequest($followRequestActivity)
{
	$requester = $followRequestActivity->actor;
	if ($requester === null) {
		http_response_code(400);
		print 'Invalid format for the incoming message: missing actor field';
		exit();
	}
	$utils = new ActivityPubUtils('../');
	$inbox = $utils->saveInbox($requester);

	$acceptActivity = Activity::getAcceptActivity($followRequestActivity->id);
	$utils->send($acceptActivity, array($inbox));
}

/**
 * Store an activity into my inbox
 * @param stdClass $activity
 */
function saveToInbox($activity)
{
	$file = new SplFileObject('../'.INBOX_FILE, 'c+');
	$file->flock(LOCK_EX);

	/*
	 * If file not exists, create an empty inbox
	 */
	if ($file->getSize()<=0)
		$collection = ActivityPubOrderedCollection::getEmpty(ActivityPubObject::getSrcDirURI() . 'inbox.php');
	else {
		$collection = ActivityPubOrderedCollection::read($file);
		$file->rewind();
	}
	$collection->add($activity);
	$jsonUpdated = json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	//TODO limit activity size and number of activities
	$file->fwrite($jsonUpdated);
	$file->fflush();
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
			if (Activity::getOrganizationActorURI() == $activity->object) {
				AccessLogUtils::logAccess("follow request", '../' . ACCESS_FILE_PATH);
				handleFollowRequest($activity);
			}
			//just discard if it isn't a follow request to me
			break;
		case 'Create':
			saveToInbox($activity);
			break;
		// other requests are ignored
		default:
	}
	exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
	header("Content-Type: application/activity+json");
	$s=file_get_contents('../'.INBOX_FILE);
	print $s;
}
// otherwise, return the inbox