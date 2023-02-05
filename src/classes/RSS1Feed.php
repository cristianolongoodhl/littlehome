<?php
/**
 * Copyright 2019  Cristiano Longo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
  * Generate an RSS 1.0 Feed (see http://web.resource.org/rss/1.0/spec)
  */

class RSS1Feed{
	private $doc;
	private $channelEl;
	private $seqEl;

	/**
	 * Create a channel with no items.
	 *
	 * @param $baseURI a base uri for RDF resources
	 * @param $title A descriptive title for the channel.
	 * @param $link The URL to which an HTML rendering of the channel title will link, commonly the parent site's home or news page. 
	 * @param  $description A brief description of the channel's content, function, source, etc. 
	 */
	public function __construct($baseURI, $title, $link, $description) {
		$domImplementation = new DOMImplementation();  
		$rdfDocumentType = $domImplementation->createDocumentType("rdf:RDF");
		$this->doc = $domImplementation->createDocument("http://www.w3.org/1999/02/22-rdf-syntax-ns#", "rdf:RDF",$rdfDocumentType);
		$this->doc->documentElement->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns', 'http://purl.org/rss/1.0/');
		$this->doc->documentElement->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:dc', 'http://purl.org/dc/elements/1.1/');

		$this->doc->preserveWhiteSpace = false;
		$this->doc->formatOutput = true;
		$this->doc->version="1.0";
		$this->doc->encoding="UTF-8";
		$this->doc->formatOutput = true;

		$this->channelEl=$this->doc->createElement('channel');
		$this->channelEl->setAttribute('rdf:about',$baseURI.'rss1feed.rss');
		$this->doc->documentElement->appendChild($this->channelEl);

		$titleEl=$this->doc->createElement('title');
		$this->channelEl->appendChild($titleEl);
		$titleEl->appendChild($this->doc->createTextNode($title));

		$linkEl=$this->doc->createElement('link');
		$this->channelEl->appendChild($linkEl);
		$linkEl->appendChild($this->doc->createTextNode($link));

		$descriptionEl=$this->doc->createElement('description');
		$this->channelEl->appendChild($descriptionEl);
		$descriptionEl->appendChild($this->doc->createTextNode($description));

		$itemsEl=$this->doc->createElement('items');
		$this->channelEl->appendChild($itemsEl);

		$this->seqEl=$this->doc->createElement('rdf:Seq');
		$itemsEl->appendChild($this->seqEl);

	}

	/**
	  * Add an item to the feed.
	  *
	  * @param $url string the link where the article can be viewed 
	  * @param $title string the article dtitle
	  * @param $date string the article pubblication date in ISO8601 format.
	  */
	public function add($link, $title, $date){
		$liEl=$this->doc->createElement('rdf:li');
		$this->seqEl->appendChild($liEl);
		$liEl->setAttribute('rdf:resource',$link);
	
		$itemEl=$this->doc->createElement('item');
		$this->doc->documentElement->appendChild($itemEl);
		$itemEl->setAttribute('rdf:about',$link);

		$titleEl=$this->doc->createElement('title');
		$itemEl->appendChild($titleEl);
		$titleEl->appendChild($this->doc->createTextNode($title));

		$linkEl=$this->doc->createElement('link');
		$itemEl->appendChild($linkEl);
		$linkEl->appendChild($this->doc->createTextNode($link));

		$dateEl=$this->doc->createElement('dc:date');
		$itemEl->appendChild($dateEl);
		$dateEl->appendChild($this->doc->createTextNode($date));
	}

	public function output(){
		echo $this->doc->saveXML();
	}
}
?>
