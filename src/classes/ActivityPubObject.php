<?php
/**
 * Base class for all ActivityPub objects
 * @see https://www.w3.org/TR/activitystreams-vocabulary/#dfn-object 
 */
class ActivityPubObject{

	public string $id;
	public string $type;
	
	/**
	 * @param string $id IRI characterizing the object, in the sense of JSON-LD
	 * @param string $type the object type, in the sense of JSON-LD
	 */
	function __construct(string $id, string $type){
		$this->{'@context'}='https://www.w3.org/ns/activitystreams';
		$this->id=$id;
		$this->type=$type;
	}
	
	//UTILS 
	/**
	 * Assuming that the script currently executing is placed in the src directory, return the URL of this directory.
	 *
	 * TODO duplicated with ActivityPubUtils
	 */
	public static function getSrcDirURI(){
		return (empty($_SERVER['HTTPS']) ? 'http' : 'https').'://'.$_SERVER['SERVER_NAME'].pathinfo($_SERVER['REQUEST_URI'])['dirname'].'/';
	}
	
	/**
	 * Use this method to get the URI of the actor representing the organization
	 */
	public static function getOrganizationActorURI(){
		return self::getSrcDirURI().'actor.php';
	}
}
