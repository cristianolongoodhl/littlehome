<?php
/**
 * Helper page to log an access to the webpage specified in the resource parameter 
 */
require_once('../config.php');
require_once('classes/AccessLogUtils.php');
AccessLogUtils::logAccess($_GET['resource'], '../'.ACCESS_FILE_PATH_JS);

header('Content-type: application/javascript');
?>