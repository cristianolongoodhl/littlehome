<?php
require_once 'ActivityPubObject.php';

/**
 * Base class for ActivityPub Activities
 * @author cristiano longo
 *
 */
class Activity extends ActivityPubObject{
	public string $actor;
	public $object;
	
	/**
	 * @param string $id 
	 * @param string $type activity type
	 * @param object $object activity object
	 */
	function __construct(string $id, string $type){
		parent::__construct($id,$type);
		$this->actor=self::getOrganizationActorURI();
	}
	
	//UTILS
		
	/**
	 * Generate a unique url for an activity 
	 */
	public static function getDummyId(){
		return self::getSrcDirURI().'activities/'.microtime(true);
	}
	
	//FACTORY METHODS
	
	/**
	 * Create an Accept activity with a dummy id and the local actor
	 * 
	 * @see https://www.w3.org/TR/activitystreams-vocabulary/#dfn-accept
	 * @param string $object URL of the object to be accepted
	 */
	public static function getAcceptActivity(string $object){
		$a=new Activity(self::getDummyId(), 'Accept');		
		$a->object=$object;
		return $a;
	}
	
	/**
	 * Create a Create activity with the specified id and the local actor
	 *
	 * @see https://www.w3.org/TR/activitystreams-vocabulary/#dfn-create
	 */
	public static function getCreateActivity(string $id, ActivityPubObject $object){
		$a=new Activity($id, 'Create');
		$a->object=$object;
		return $a;
	}	
	
	/**
	 * Get a Delete activity with a dummy id
	 * @see https://www.w3.org/TR/activitystreams-vocabulary/#dfn-delete
	 */ 
	public static function getDeleteActivity(string $object){
		$a=new Activity(self::getDummyId(), 'Delete');
		$a->object=$object;
		return $a;
	}
	
	/**
	 * Get a Update activity with a unique id
	 * @see https://www.w3.org/TR/activitystreams-vocabulary/#dfn-delete
	 */
	public static function getUpdateActivity(string $object){
		$a=new Activity(self::getDummyId(), 'Update');
		$a->object=$object;
		return $a;
	}
	
}