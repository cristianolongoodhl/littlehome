<?php
/**
  * Manage the articles list.
  */
class Articles extends JsonHelper{

	const ISO8601 = 'Y-m-d\TH:i:sO';
	const W3CDATE = 'Y-m-d\TH:i:sP';

	public function __construct(){
        	parent::__construct('articles.json'); 
	}

	/**
	  * Create an empty articles list and write to the file at the specified path.
	  *
	  * @return TRUE if success, FALSE otherwise.
	  */	
	public static function writeEmpty($path){
		$l=new Articles();
		$l->createEmpty();
		return $l->writeToFile($path);
	}
	/**
	  * Setup the json object with no articles.
	  */
	public function createEmpty(){
		$this->json=json_decode(file_get_contents('articles_base.json'));
	}

	/**
	  * Add an article to this list. Articles are ordered by date.
	  * 
	  * @param $a Article an instance of Article
	  */
	public function add($uri, $a){
		$item=new stdClass();
		$item->{'rss:title'}=$a->title;
		$item->{'@id'}=$uri;
		$item->{'dc:date'}=$a->date->format(Articles::W3CDATE);

		$items=$this->json->{'rss:items'};
		if (!isset($items->{'rdf:li'})){
			$items->{'rdf:li'}=array();
			$items->{'rdf:li'}[0]=$item;
			return;
		}

		$n=count($items->{'rdf:li'});
		for($i=0; $i<$n; $i++){
			$x=$items->{'rdf:li'}[$i];
			$xdate=DateTime::createFromFormat(Articles::W3CDATE, $x->{'dc:date'});
			if ($xdate->getTimestamp()<$a->date->getTimestamp())
				break;
		}
		if ($i===$n){
			$items->{'rdf:li'}[$i]=$item;
			return;
		}
	
		$pos=$i;
		for($j=$n;$j>$i; $j--)
			$items->{'rdf:li'}[$j]=$items->{'rdf:li'}[$j-1]; 
		$items->{'rdf:li'}[$i]=$item;
	}

	/**
	  * Remove the article with the specified uri.
	  *
	  * @return TRUE if an article with this URI exists, FALSE otherwise.
	  */
	public function remove($uri){
		$items=$this->json->{'rss:items'};
		if (!isset($items->{'rdf:li'}))
			return FALSE;

		$i=0; $n=count($items->{'rdf:li'});
		for($i=0, $n=count($items->{'rdf:li'}); $i<$n && strcmp($uri,$items->{'rdf:li'}[$i]->{'@id'}); $i++);

		if ($i===$n) return FALSE;

		if ($n===1){
			unset($items->{'rdf:li'});
			return TRUE;
		}

		for($m=$n-1;$i<$m;$i++){
			$src=$items->{'rdf:li'}[$i+1]->{'@id'};
			$tgt=$items->{'rdf:li'}[$i]->{'@id'};
			$items->{'rdf:li'}[$i]=$items->{'rdf:li'}[$i+1];		
		}
	
		unset($items->{'rdf:li'}[$m]);

		return TRUE;
	}

	/**
	 * Get the index in the json->{'rss:items'}->{'rdf:li'} array of the more recent article with a date not in the future
	 *
	 * @return int the index of the first past article, if any. FALSE otherwise.
	 */
	public function getFirstPastArticleIndex(){
		if (!isset($this->json->{'rss:items'}->{'rdf:li'}))
			return FALSE;
		$articles=$this->json->{'rss:items'}->{'rdf:li'};
		$currentTime=(new DateTimeImmutable())->getTimestamp();
		$n=count($articles);
		for($i=0; $i<$n; $i++){
			$a=$articles[$i];
			$atime=DateTime::createFromFormat(Articles::ISO8601, $a->{'dc:date'})->getTimestamp();
			if ($atime<=$currentTime)
				return $i;
		}
		return FALSE;
	}
}
?>
