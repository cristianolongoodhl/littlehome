<?php
/**
  * Helper class to manage logo. Assume that logo file is stored locally.
  *
  * Note: the organization json stored in the php session contains the new logo.
  */
class Logo{
	private $imgDir;
	private $utils;
	public $tmpLogo; //new logo filename

	function __construct($imgDir){
		$this->imgDir=$imgDir;
		$this->utils=new LDUtils();
	}

	/**
	  * Set the current logo by extracting it from the organization json stored in session
	  * 
	  * @param $orgjson object the json object representing the organization
	  * @return true if a logo is specified in $orgjson, false otherwise 	
	  */
	function getTmpLogoFromOrgJson($orgjson){
		if (isset($orgjson->{'foaf:logo'})){
			$logo=$orgjson->{'foaf:logo'}->{'@id'};
			echo "<!-- HAS CURRENT LOGO! $logo -->\n";
			if ($this->utils->isAbsoluteUrl($logo))
				echo "<!-- and it is an absolute url -->\n";
			else
				echo "<!-- and it is a relative path -->\n";
		}

		if (isset($orgjson->{'foaf:logo'}) && !($this->utils->isAbsoluteUrl($orgjson->{'foaf:logo'}->{'@id'}))){
			$this->tmpLogo=basename($orgjson->{'foaf:logo'}->{'@id'});
			return true;
		}
		unset($this->tmpLogo);
		return false;
	}

	/**
	  * Store the logo contained in the json representation of the organization as session variable
	  * delegated to save the old logo file name
	  */	
	function storeOldLogoInSession($orgjson){
		if (isset($orgjson->{'foaf:logo'}) && !($this->utils->isAbsoluteUrl($orgjson->{'foaf:logo'}->{'@id'}))){
 				$_SESSION['oldlogo']=basename($orgjson->{'foaf:logo'}->{'@id'});
		}
	}

	/**
	  * Store the logo file passed via post post variable.
	  *	
	  * @param $logoFileFieldName string name of the input field containing the logo file
	  *
	  * @return string new logo file name
	  */
	function upload($logoFileFieldName){
		$filename=$this->getFilenameAvoidCollisions(basename($_FILES[$logoFileFieldName]['name']));
		move_uploaded_file($_FILES[$logoFileFieldName]['tmp_name'], $this->imgDir.'/'.$filename);
		return $filename;
	}

	/**
	  * get a filename from a base one which does not collides with other files in the img dir.
	  */	
	function getFilenameAvoidCollisions($filename){
		if (file_exists($this->imgDir.'/'.$filename))
			return $this->getFilenameAvoidCollisions("_$filename");
		return $filename;
	}

	/**
	  * remove the temporary logo file if the case
	  * 	
	  * @param $filename
	  * @param $dir string path of the directory where logo file is stored
	  */
	function clearTmpLogo(){
		if (!(isset($this->tmpLogo))) 
			return;
		//don't remove if it is the old logo (i.e. the logo actually used in the web site)
		if (isset($_SESSION['oldlogo']))
			if (strcmp($_SESSION['oldlogo'], $this->tmpLogo)===0){
				unset($this->tmpLogo);
				return;
			}
		unlink($this->imgDir.'/'.$this->tmpLogo);
		unset($this->tmpLogo);
	}

	/**
	  * make the temporary logo as the definitive one. Clear the old logo if any.
	  */	
	function handleTmpLogoConfirmed(){
		if (isset($_SESSION['oldlogo'])) 
			if (!isset($this->tmpLogo) || strcmp($_SESSION['oldlogo'],$this->tmpLogo)!==0 )
				unlink($this->imgDir.'/'.$_SESSION['oldlogo']);
	}
}
?>