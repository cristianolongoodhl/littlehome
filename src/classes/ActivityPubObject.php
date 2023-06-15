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
}
