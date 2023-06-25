<?php
/**
  * Helper class for site configurations.
  */

class ConfigHelper{
	public $organization;
	public $styles;
	public $utils;

	public function __construct($organizationFilePath, $stylesFilePath){
		$this->organization=new Organization();
		$this->organization->readFromFile($organizationFilePath);
		$this->styles=new Styles();
		$this->styles->readFromFile($stylesFilePath);
		$this->utils=new LDUtils();
	}

	/**
	  * @param $currentToRootPath string path to go from the directory where the invoking script is placed to the root directory of the project. For example, if the script which call this method
	  * is placed in the src dir, then $currentToRootPath must be set to '../'. If null it is assumed that the script is running in the root directory
	  */	
	function getCss($currentToRootPath=''){
		$selected=$this->styles->json->selected;
		if ($this->utils->isAbsoluteUrl($selected))
			return $selected;
		return $currentToRootPath.$selected;
	}

	/**
	  * Get the organization name, with html entities.
	  */
	function getName(){
		return htmlentities($this->organization->json->{'foaf:name'});
	}

	/**
	  * return the path of the logo, if any. FALSE otherwise.
	  *
	  * @param $currentToRootPath string path to go from the directory where the invoking script is placed to the root directory of the project. For example, if the script which call this method
	  * is placed in the src dir, then $currentToRootPath must be set to '../'. If null it is assumed that the script is running in the root directory
	  */
	function getLogo($currentToRootPath=''){
		if (isset($this->organization->json->{'foaf:logo'})){
			$logoUrl=$this->organization->json->{'foaf:logo'}->{'@id'};
			if ($this->utils->isAbsoluteUrl($logoUrl)) return $logoUrl;
			else return $currentToRootPath.$logoUrl;
		} else 
			return FALSE;
	}

	/**
	  * Get the organization address formatted in a human readable form, following the italian notation of addresses
	  *
	  * @return string a string representing the address, if any. FALSE otherwise.
	  */
	function getAddress(){
		$j=$this->organization->json;
		if (!isset($j->{'org:hasPrimarySite'})) return FALSE;

		$jsonSite=$j->{'org:hasPrimarySite'};
		if (!isset($jsonSite->{'locn:address'})) return FALSE;

		$jsonAddress=$jsonSite->{'locn:address'};
		$preceeding=false;
		$s='';
		if (isset($jsonAddress->{'locn:thoroughfare'})){
			$s=$jsonAddress->{'locn:thoroughfare'};
			$preceeding=true; 
			if (isset($jsonAddress->{'locn:locatorDesignator'}))
				$s.=' '.$jsonAddress->{'locn:locatorDesignator'};
		}
		echo "<!-- uno $s -->\n";
		if (isset($jsonAddress->{'locn:poBox'})){
			if ($preceeding) $s.=', '; 
				else $preceeding=true; 
			$s.=$jsonAddress->{'locn:poBox'};
		}
		echo "<!-- due $s -->\n";
		if (isset($jsonAddress->{'locn:postName'})){
			if ($preceeding) $s.=', '; 
			$s.=$jsonAddress->{'locn:postName'};
			echo "<!-- tre $s -->\n";
			return $s;
		} else if ($preceeding) return $s;
		else return FALSE;
	}
	
	/**
	 * Convert markdown description of the orgnization in HTML
	 */
	function getHTMLDescription(){
		$parser = new \Michelf\Markdown();
		return $parser->transform($this->organization->json->{"dcterms:description"});		
	}
}
?>