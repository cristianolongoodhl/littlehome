<?php
/**
  * Configuration file, rename it to config.php and put the correct values
  */

/**
  * path of the file where the information about the organization will be stored.
  */	
define('ORGANIZATION_FILE','data/org.json');	

/**
  * path of the file where the web page presentation style are stored
  */	
define('STYLES_FILE','data/styles.json');	


/**
  * path of the file where the MD5 sum of the password is stored. It should be placed out of the public_html directory
  * so that it will not be accessible from web.
  */
define('PASSWORD_FILE','data/pwd.txt');

/**
  * path of the directory where images will be stored
  */
define('IMG_DIR','img');

/**
  * path of the file with the article sources list.
  */
define('ARTICLES_FILE','data/articles.json');

/**
  * path of the directroy where article sources will be stored
  */
define('ARTICLES_DIR','blog');

/**
  * path of the access log file
  */
define('ACCESS_FILE_PATH','access.log');
?>
