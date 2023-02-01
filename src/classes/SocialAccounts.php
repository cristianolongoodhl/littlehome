<?php
/**
  * Utilities about social, blog, ... accounts of an organization.
  */
class SocialAccounts{
	public $services;  //services handled
	public $presentations;

	function __construct(){
	    $this->services=array('http://www.facebook.com','http://www.twitter.com','https://github.com', 'http://telegram.org');
		$this->presentations = array(
			'http://www.facebook.com' => new AccountPresentation('icons/iconfinder_facebook_alt_115478.png','facebook','facebook_page_name','facebook_page_url'),
			'http://www.twitter.com' => new AccountPresentation('icons/iconfinder_02_111031.png','twitter','twitter_name','twitter_feed_url'),
			'https://github.com' => new AccountPresentation('icons/GitHub-Mark-120px-plus.png','Organizzazione su GitHub','github_name','github_url'),
		    'https://www.youtube.com' => new AccountPresentation('icons/youtube.png','Canale Youtube','youtube_name','youtube_url'),
		    'http://telegram.org' => new AccountPresentation('icons/Font_Awesome_5_brands_telegram-plane.svg','Canale Telegram','telegram_name','telegram_url')
		);
	}

	/**
	 * Get the element representing the account of the organization on the specified service 
	 * by extracting it from the json representation of the organization.  Return null if no such account
	 * exists in the specified representation.
	 */
	function getAccount($orgJson, $service){
		if (!isset($orgJson->{'foaf:account'}))
			return null;
	
		foreach($orgJson->{'foaf:account'} as $account)
			if (strcmp($service, $account->{'foaf:accountServiceHomepage'}->{'@id'})===0)
				return $account;
	}
}

/**
  * Registry of presentation elements associated to social accounts.
  */
class AccountPresentation{
	public $serviceIcon; //URL of the icon used to present the account on the specified service
	public $accountTypeDescription; //i.e. facebook page, twitter account, ...
	public $nameField; //name to be used in form field for the account name
	public $urlField; //name to be used in form field for the account URL

	function __construct($serviceIcon, $accountTypeDescription, $nameField, $urlField){
		$this->serviceIcon=$serviceIcon;
		$this->accountTypeDescription=$accountTypeDescription;
		$this->nameField=$nameField;
		$this->urlField=$urlField;
	}
}
?>
