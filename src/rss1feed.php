<?php 
/**
 *
 * Create the articles atom-rss feed
 * Copyright 2019 Cristiano Longo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *d
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Cristiano Longo
 */

require_once('../config.php');
require_once('classes/LDUtils.php');
require_once('classes/SocialAccounts.php');
require_once('classes/Organization.php');
require_once('classes/Styles.php');
require_once('classes/ConfigHelper.php');
require_once('classes/Articles.php');
require_once('classes/RSS1Feed.php');
require_once('classes/AccessLogUtils.php');

$base=(new LDUtils())->getBaseURIOfSrc();
$remoteConfig=new ConfigHelper('../'.ORGANIZATION_FILE, '../'.STYLES_FILE);
$f=new RSS1Feed(BASE_URL,$remoteConfig->getName(),$base.'src/listArticles.php', $remoteConfig->getName().' blog');

$l=new Articles();
if ($l->readFromFile('../'.ARTICLES_FILE) && $l->count()>0)
	foreach($l->json->{'rss:items'}->{'rdf:li'} as $a){
		$url=$a->{'@id'};
		if (!$remoteConfig->utils->isAbsoluteURL($url))
			$url=$base.'src/viewArticle.php?url='.urlencode('../'.$url).'&from=rss';
		$f->add($url,$a->{'rss:title'},$a->{'dc:date'});
	}

header("Content-type:application/rss+xml");
$f->output();

AccessLogUtils::logAccess('rss1feed', '../'.ACCESS_FILE_PATH);
?>
