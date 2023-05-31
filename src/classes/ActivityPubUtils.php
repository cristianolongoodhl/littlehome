<?php
class ActivityPubUtils{

	public $rootDirPath;
	
	/**
	 * Relative path to the root installation directory
	 * 
	 * @param string $rootDirPath
	 */
	public function __construct($rootDirPath){
		$this->rootDirPath=$rootDirPath;
	}
	/**
	 * Put in the storage of known inboxes the sharedInbox of the actor (whose description can be retrieved ad $actorURI) or, if the actor has no shared inbox, the inbox itself 
	 * @param string $actorURI URI characterizing the actor
	 * @return string the saved inbox 
	 * 
	 */
	public function saveInbox($actorURI){
		$actor=json_decode(file_get_contents($actorURI), false);
		if ($actor==null){
			http_response_code(400);
			print 'Invalid actor '.$actorURI;
			exit();			
		}
		$inbox=$this->getInbox($actor);
		$knownInboxesFile=fopen($this->rootDirPath.KNOWN_INBOXES_FILE,'c+');
		flock($knownInboxesFile, LOCK_EX);
		$inboxWithNL=$inbox."\n";
		//if the inbox was already stored, just return it
		while (($storedInbox=fgets($knownInboxesFile)) !== false)
			if ($storedInbox==$inboxWithNL){
				fclose($knownInboxesFile);
				return $inbox;
			}
		// otherwise save it
		fwrite($knownInboxesFile, $inboxWithNL);
		fclose($knownInboxesFile);
	}
		
	/**
	 * Get the sharedInbox of the actor, if any,  or the actor inbox, otherwise.
	 * @param object $actor the JSON object representing the actor
	 * @return string the retrieved inbox URI
	 */
	private function getInbox($actor){
		if (isset($actor->endpoints) && isset($actor->endpoints->sharedInbox))
			return $actor->endpoints->sharedInbox;
		return $actor->inbox;
	}
}