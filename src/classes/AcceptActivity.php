<?php
require_once 'ActivityPubObject.php';
/**
 * Activity to accept an ActivityPubObject 
 */
class AcceptActivity extends ActivityPubObject{

	public string $actor;
	public string $object;
	/**
	 * @param string $srcURI URI of the src directory 
	 * @param string $type activity type
	 */
	function __construct(string $srcURI, string $acceptedActivityId){
		parent::__construct($srcURI.'activities/'.microtime(true),'Accept');
		$this->{'@context'}='https://www.w3.org/ns/activitystreams';
		$this->actor=$srcURI.'actor.php';
		$this->object=$acceptedActivityId;
	}
}
