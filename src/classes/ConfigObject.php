<?php
require_once '../config.php';

/**
 * Object representing a littlehome installation configuration;
 */
class ConfigObject{
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
	
}