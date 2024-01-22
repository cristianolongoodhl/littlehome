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
	
}
?>
