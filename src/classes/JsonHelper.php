<?php
/**
  * Base class for objects which helps managing json based configuration files.
 */
class JsonHelper{
	private $sessionVariableName;
	protected $utils;
	public $json;
	
	/**
	  * @param $sessionVariableName string the name of the session variable where the json is stored
	  */
	protected function __construct($sessionVariableName){
		$this->sessionVariableName=$sessionVariableName;
		$this->json=new stdClass(); //start with an empty json
		$this->utils=new LDUtils();
	}

	/**
	  * Set the json by retrieving it from session variables
	  *
	  * @return true if the corresponding session variable exists, false otherwise (so that json is not set)
	  */
	public function readFromSession(){
		if (isset($_SESSION[$this->sessionVariableName])){
			$this->json=$_SESSION[$this->sessionVariableName];
			return true;
		}
		return false;
	}

	/**
	  * Store the json as session variable.
	  */
	public function storeInSession(){
		$_SESSION[$this->sessionVariableName]=$this->json;
	}

	/**
	  * Set the json by reading it from a file
	  *
	  * @return true if the file exists, false otherwise
	  */
	public function readFromFile($path){
		if (!file_exists($path)) return false;
		$s=file_get_contents($path);
		$this->json=json_decode($s,false);
		return true;
	}

	/**
	  * Write the json to a file
	  *
	  */
	public function writeToFile($path){
		$s=json_encode($this->json);
		return file_put_contents($path,$s)!=FALSE;
	}
}
?>