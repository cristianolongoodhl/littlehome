<?php
require_once '../config.php';
require_once 'LDUtils.php';

/**
 * Object representing a littlehome installation configuration;
 */
class ConfigObject{
	public $base;
	public $organization;
	public $styles;
	public $password;
	public $articles;
	public $accesslog;
	public $accesslogjs;
	public $keysdir;
	public $knowninboxes;
	public $inbox;

	/**
	 * Load values from the local configuration
	 * @param $base string base for URLs 
	 */
	public function loadLocal(string $base){
		$this->base=$base;
		$this->organization=$base.ORGANIZATION_FILE;
		$this->styles=$base.STYLES_FILE;
		$this->password=$base.PASSWORD_FILE;
		$this->articles=$base.ARTICLES_FILE;
		$this->accesslog=$base.ACCESS_FILE_PATH;
		$this->accesslogjs=$base.ACCESS_FILE_PATH_JS;
		$this->keysdir=$base.KEYS_DIR;
		$this->knowninboxes=$base.KNOWN_INBOXES_FILE;
		$this->inbox=$base.INBOX_FILE;
	}
	
	/**
	 * Load the configuration available on the web at the specified URI
	 * @param string $uri
	 * @param string $failurecause 
	 * @return true if configuration loading succeeded, a string representing the failure cause otherwise.
	 */
	public function loadRemote(string $uri, string &$failurecause){
		$utils=new LDUtils();
		$confJson=$utils->loadRemoteJson($uri, $failurecause);
		if ($confJson==false){
			return false;
		}			
		$this->base=$confJson->base;
		$this->organization=$confJson->organization;
		$this->styles=$confJson->styles;
		$this->password=$confJson->password;
		$this->articles=$confJson->articles;
		$this->accesslog=$confJson->accesslog;
		$this->accesslogjs=$confJson->accesslogjs;
		$this->keysdir=$confJson->keysdir;
		$this->knowninboxes=$confJson->knowninboxes;
		$this->inbox=$confJson->inbox;
		return true;			
	}
}