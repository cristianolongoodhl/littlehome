<?php

/**
  * Some helper method to deal with JSON-LD
  *
  */
class LDUtils{
	function addObjectTripleIfNotEmpty($subject, $property, $objecturl){
		if (!isset($objecturl) || strlen($objecturl)===0) return;
		$subject->$property=new stdClass();
		$subject->$property->{'@id'}=$objecturl;
	}
	
	function addDataTripleIfNotEmpty($subject, $property, $value){
		if (strlen($value)===0) return;
		$subject->$property=$value;
	}

	/**
	 * see https://stackoverflow.com/questions/7392274/checking-for-relative-vs-absolute-paths-urls-in-php
	 */
	function isAbsoluteUrl($url)
	{
		$pattern = "/^(?:ftp|https?|feed)?:?\/\/(?:(?:(?:[\w\.\-\+!$&'\(\)*\+,;=]|%[0-9a-f]{2})+:)*
		(?:[\w\.\-\+%!$&'\(\)*\+,;=]|%[0-9a-f]{2})+@)?(?:
		(?:[a-z0-9\-\.]|%[0-9a-f]{2})+|(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\]))(?::[0-9]+)?(?:[\/|\?]
		(?:[\w#!:\.\?\+\|=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})*)?$/xi";
		
		return (bool) preg_match($pattern, $url);
	}

	/**
	 * Get the URI of the page currently viewing
	 * See https://www.php.net/manual/en/reserved.variables.server.php
	 */
	function getCurrentPageURI(){
		$proto=empty($_SERVER['HTTPS']) ? 'http' : 'https';
		$host=$_SERVER['HTTP_HOST'];
		$path=$_SERVER['PHP_SELF'];
		$query=empty($_SERVER['QUERY_STRING']) ? '' : '?'.$_SERVER['QUERY_STRING'];
		return $proto.'://'.$host.$path.$query; 
	}
	
	/**
	 * Get the URI of the current installation for pages in the src directory
	 * 
	 * NOTE use only in scripts inside the src directory
	 */
	function getBaseURIOfSrc(){
		$proto=empty($_SERVER['HTTPS']) ? 'http' : 'https';
		$host=$_SERVER['HTTP_HOST'];
		$currentScriptPath=$_SERVER['PHP_SELF'];
		$pathStartingFromSrc='src/'.basename($_SERVER['PHP_SELF']);
		$pathWithoutSrc=substr($currentScriptPath, 0, strlen($currentScriptPath)-strlen($pathStartingFromSrc));
		return $proto.'://'.$host.$pathWithoutSrc;
	}
	
	/**
	 * Load a json object available on the web at the specified URI
	 * @param string $uri
	 * @param string $failurecause if loading fails, it will contain the failure cause
	 * @return Object|boolean the json objet if loading succeeded, false otherwise 
	 */
	public function loadRemoteJson(string $uri, string &$failurecause){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $uri);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept: application/ld+json']);
		$str = curl_exec($curl);
		if ($str == false || $str == null) {
			$failurecause=' Curl error: ' . curl_error($curl);
			curl_close($curl);
			return false;
		}
		curl_close($curl);
		$json = json_decode($str, false);
		if ($json != null) return $json; 
		$failurecause=json_last_error_msg();
		return false;		
	}
	
	/**
	 * Download a file from $sourceUri and store it in $targetFilePath
	 * @param string $sourceUri
	 * @param string $targetFilePath
	 * @param bool $acceptJson if true, an accept header for ld+json is specified
	 * @param string $failurecause return parameter which contains the error message if the download fails.
	 * 
	 * @return boolean true if download suceeds and the downloaded file is actually stored, false otherwise
	 */
	public function download(string $sourceUri, string $targetFilePath, bool $acceptJsonLD, string &$failurecause){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $sourceUri);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		if ($acceptJsonLD)
			curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept: application/ld+json']);
		$str = curl_exec($curl);
		if ($str == false || $str == null) {
			$failurecause=' Curl error: ' . curl_error($curl);
			curl_close($curl);
			return false;
		}
		curl_close($curl);
		
		if (file_put_contents($targetFilePath,$str)==false){
			$failurecause='Unable to write to '.$targetFilePath;
			return false;
		}
		return true;
	}
}
?>
