<?php
/**
 * provide a json-ld file with links to all the configuration files
 * @autor cristiano longo
 */
require_once '../config.php';
require_once 'classes/LDUtils.php';
require_once 'classes/ConfigObject.php';

$utils=new LDUtils();
$localConf=new ConfigObject();
$localConf->{'@context'}=new stdClass();
$localConf->{'@context'}->{'@vocab'}='https://www.opendatahacklab.org/ns/littlehome.json#';
$localConf->{'@id'}=$utils->getCurrentPageURI();
$localConf->loadLocal($utils->getBaseURIOfSrc());

header('Content-Type: application/ld+json');
header('Access-Control-Allow-Origin: *');
print json_encode($localConf, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
?>