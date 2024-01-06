<?php
require_once('JsonHelper.php');
require_once('LDUtils.php');
require_once('SocialAccounts.php');

/**
  * Methods related to organizations
  */ 
class Organization extends JsonHelper{

	/**
	  * create an Organization instance represented by a json object.
          */
	function __construct() {
        	parent::__construct('org.json'); 
	}
	


	/**
	 * Set the json object to represent the organization described by the user by using the admin form
 	 * 
	 * @param $vars array of variables submitted by the form
	 *
	 * @return true if the json object has been created from the post request, false otherwise
	 */
	public function readFromForm($vars){
		if (!isset($_POST['fromInfo'])){ 
			return false;
		}
		$j=new stdClass();
		
		$context=json_decode(file_get_contents('org_context.json'));
		$j->{'@context'}=$context;
		$j->{'@id'}='me';
		$j->{'@type'}='org:Organization';
		$j->{'foaf:name'}=$vars['name'];
		$this->utils->addDataTripleIfNotEmpty($j,'dcterms:description',$vars['description']);
	
		if (isset($vars['email']) && strlen($vars['email'])>0)
			$this->utils->addObjectTripleIfNotEmpty($j,'foaf:mbox','mailto:'.$vars['email']);
		if (isset($vars['phone']) && strlen($vars['phone'])>0)
			$this->utils->addObjectTripleIfNotEmpty($j,'foaf:phone','tel:'.$vars['phone']);
		if (isset($vars['logo']) && strlen($vars['logo'])>0)
			$this->utils->addObjectTripleIfNotEmpty($j,'foaf:logo',$vars['logo']);
		$accounts=$this->getAllSocialAccountsFromForm($vars);
		if (count($accounts)>0)
			$j->{'foaf:account'}=$accounts;
		
		$address=$this->getAddressFromForm($vars);
		if ($address!=null){
			$site=new stdClass();
			$site->{'@id'}='site';
			$site->{'@type'}='locn:Location';
			$j->{'org:hasPrimarySite'}=$site;
			
			$site->{'locn:address'}=$address;
		}
		
		$externalPage=$this->getExternalPageFromForm($vars);
		if ($externalPage!=null)
			$j->{'foaf:isPrimaryTopicOf'}=$externalPage;
		
		$this->json=$j;			
		return true;
	}

	/**
	  * Create the json object representing the address indicated by the user by filling the corresponding form fields.
	  *
	  * @param $vars array POST variables
	  * @return Object a json(ld) object representing the address, or null if no address is specified 
	  */
	private function getAddressFromForm($vars){
		$address=new stdClass();
		$address->{'@id'}='address';
		$address->{'@type'}='locn:Address';
		$addressComponents=['thoroughfare','locatorDesignator','poBox','postName'];	
		$hasAddress=false;
		foreach($addressComponents as $component){
			$componentValue=$vars[$component];
			if (strlen($componentValue)>0){
				$hasAddress=true;
				$address->{"locn:$component"}=$componentValue;
			}
		}
		if ($hasAddress)
			return $address;
		else
			return null;
	}

	/**
	 * Create the json object representing the external link indicated by the user by filling the corresponding form fields.
	 *
	 * @param $vars array POST variables
	 * @return Object a json(ld) object representing the external link, or null if no external link is specified
	 */
	private function getExternalPageFromForm($vars){
		$page=new stdClass();
		if (!isset($vars['linkurl']) || strlen(($vars['linkurl']))<=0) return null;
		$page->{'@id'}=$vars['linkurl'];
		if (isset($vars['linkname'])&& strlen($vars['linkname'])>0)
			$page->{'dcterms:title'}=$vars['linkname'];
		return $page;
	}
		
	/**
	  * retrieve a social account on a specified service from form fields, return null if the user specified no account on the given service.
	  *
	  * @param $presentation AccountPresentation the instance of AccountPresentation relative to the specified service
	  *
	  * @return Object the json representation of the social account, null if no such account has been specified
	  */
	private function getSocialAccountFromForm($vars, $service,$presentation){
		$accountName=$vars[$presentation->nameField];
		if (strlen($accountName)===0)
			return null;
		$account=new stdClass();
		$account->{'@id'}=$vars[$presentation->urlField];
		$account->{'@type'}='foaf:OnlineAccount';
		$this->utils->addObjectTripleIfNotEmpty($account,'foaf:accountServiceHomepage',$service);
		$account->{'foaf:accountName'}=$accountName;
		return $account;
	}

	/**
	  * retrieve all the social accounts indicated by the user when submitting the form
	  *
	  *
	  * @return array an array of json object (may be empty)
	  */
	private function getAllSocialAccountsFromForm($vars){
		$socialAccounts=new SocialAccounts();
		$accountsJson=array();
		$i=0;
		foreach($socialAccounts->presentations as $service => $presentation){
			$accountJson=$this->getSocialAccountFromForm($vars, $service, $presentation);
			if ($accountJson!=null)
				$accountsJson[$i++]=$accountJson;
		}
		return $accountsJson;
	}	
}
?>