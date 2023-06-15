<?php
require_once 'AcceptActivity.php';

class ActivityPubUtils
{

	private $rootDirPath;

	private $srcDirURI;
	
	/**
	 * 
	 * 
	 * @param string $rootDirPath Relative path to the root installation directory
	 * @param string $srcDirURI absolute URI of the src directory 
	 */
	public function __construct($rootDirPath, $srcDirURI)
	{
		$this->rootDirPath = $rootDirPath;
		$this->srcDirURI = $srcDirURI;
	}

	private function retrieveActor($uri){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $uri);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept: application/activity+json']);
		$actorStr = curl_exec($curl);
		if ($actorStr==false || $actorStr==null){
			print ' Curl error: ' . curl_error($curl);
			curl_close($curl);
			return null;
		}
		curl_close($curl);
		$actor=json_decode($actorStr, false);
		if ($actor==null)
			print json_last_error_msg();
		return $actor;
	}
	
	/**
	 * Put in the storage of known inboxes the sharedInbox of the actor (whose description can be retrieved ad $actorURI) or, if the actor has no shared inbox, the inbox itself 
	 * @param string $actorURI URI characterizing the actor
	 * @return string the saved inbox 
	 * 
	 */
	public function saveInbox($actorURI)
	{
		$actor = $this->retrieveActor($actorURI);
		if ($actor == null) {
			http_response_code(400);
			print 'Invalid actor ' . $actorURI;
			exit();
		}
		$inbox = $this->getInbox($actor);
		$knownInboxesFile = fopen($this->rootDirPath . KNOWN_INBOXES_FILE, 'c+');
		flock($knownInboxesFile, LOCK_EX);
		$inboxWithNL = $inbox . "\n";
		//if the inbox was already stored, just return it
		while (($storedInbox = fgets($knownInboxesFile)) !== false)
			if ($storedInbox == $inboxWithNL) {
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
	private function getInbox($actor)
	{
		if (isset($actor->endpoints) && isset($actor->endpoints->sharedInbox)) return $actor->endpoints->sharedInbox;
		return $actor->inbox;
	}

	/**
	 * Use this method to get the URI of the actor representing the organization
	 */
	public function getOrganizationActorURI(){
		return $this->srcDirURI=$this->srcDirURI.'actor.php';		
	}
	/**
	 * Send activity to the specified remote inbox
	 * @param object $activity the JSON object representing the activity 
	 * @param string $inbox
	 */
	public function send($activity, $inbox)
	{
		$date=(new DateTime("now", new DateTimeZone('UTC')))->format(DateTimeInterface::RFC7231);
		$body=json_encode($activity, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		$digest='SHA-256='.base64_encode(openssl_digest($body, 'SHA256', true));
		$toBeSigned="date: $date\ndigest: $digest";
		$signature='';
		$privatekey=trim(file_get_contents($this->rootDirPath.KEYS_DIR.'/private.pem'));
		openssl_sign($toBeSigned, $signature, $privatekey, OPENSSL_ALGO_SHA256);
		
		$inboxHost=parse_url($inbox)['host'];
		
		$sigHeader = 'keyId="'.$this->srcDirURI.'actor.php#main-key",algorithm="rsa-sha256",headers="date digest",signature="'. base64_encode($signature).'"';
		$headers=['Host: '.$inboxHost,'Date: '.$date, 'Digest: '.$digest, 'Signature: '.$sigHeader, 'Content-Type: application/activity+json'];
		
		$ch=curl_init();
		//curl_setopt($ch, CURLOPT_URL, $inbox );
		curl_setopt($ch, CURLOPT_URL, $inbox);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		
		$result=curl_exec($ch);
		$responseInfo = curl_getInfo($ch);
		print 'HTTP Response code '.$responseInfo["http_code"];
		if ($result===false)
			print ' Curl error: ' . curl_error($ch);
		curl_close($ch);
		return $result;
	}	
}