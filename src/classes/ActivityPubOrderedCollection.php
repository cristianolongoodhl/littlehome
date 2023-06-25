<?php
namespace src\classes;

use SplFileObject;

/**
 * @author cristiano longo
 * @see https://www.w3.org/TR/activitystreams-vocabulary/#dfn-orderedcollection
 */
class ActivityPubOrderedCollection extends \ActivityPubObject
{

	public int $totalItems;

	public array $orderedItems;

	/**
	 * Create an ordered collection with the specified items
	 * @param string $id
	 * @param array $orderedItems array of \ActivityPubObject instances
	 */
	function __construct(string $id, array $orderedItems)
	{
		parent::__construct($id, 'OrderedCollection');
		$this->actor = self::getOrganizationActorURI();
		$this->totalItems = count($orderedItems);
		$this->orderedItems = $orderedItems;
	}

	/**
	 * Return an empty collection with the specified id.
	 * @param string $id
	 * @return \src\classes\ActivityPubOrderedCollection
	 */
	public static function getEmpty($id){
		return new ActivityPubOrderedCollection($id, array());
	}
	/**
	 * Create an ActivityPubOrderedCollection object by reading it from a json file.
	 *
	 * @param $file SplFileObject a non-empty file
	 * @return ActivityPubOrderedCollection the object described in the file
	 */
	public static function read(SplFileObject $file)
	{
		$src = $file->fread($file->getSize());
		$srcJson = json_decode($src, false);
		return new ActivityPubOrderedCollection($srcJson->id, $srcJson->orderedItems);
	}

	/**
	 * Enqueue the given object to the ordered items array 
	 * @param Object $o
	 */
	public function add($o)
	{
		$this->orderedItems[$this->totalItems++] = $o;
	}
}

