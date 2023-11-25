<?php
/**
 * Utility functions to handle both the articles list and article files.
 *
 * @author Cristiano Longo	
 */
class AdminArticleUtils{
	private $articlesDir;
	private $articlesFile;
	private $l;
	/**
	 *
	 * @param $articlesDir string path of the directory where markdown files are stored
	 * @param $articlesFile string path of the file where articles are listed
	 */
	public function __construct($articlesDir, $articlesFile){
		$this->articlesDir=$articlesDir;
		$this->articlesFile=$articlesFile;
		$this->l=new Articles();
	}

	/*
	 * Save the markdown file and add the article to the articles list.
	 *  
	 * @param $filename the path of the file where the markdown representation of the article will be stored
	 * @param $article the article as Article instance
	 * @param $pathToRoot path from the current execution directory to the root directory (the one where index.php is placed)	
	 *
	 * @return FALSE if fail, the id of the added article otherwise
	 */
	public function addArticle($filename, $article, $pathToRoot='.'){
		if ($article->writeToFile($pathToRoot.'/'.$this->articlesDir.'/'.$filename)==FALSE)
			return FALSE;

		if ($this->l->readFromFile($pathToRoot.'/'.$this->articlesFile)==FALSE) return FALSE;
		$url=$this->articlesDir.'/'.$filename;
		$this->l->add($url,$article);
		$res=$this->l->writeToFile($pathToRoot.'/'.$this->articlesFile);
		if ($res==false) return false;
		return $url;
	}

	/**
	 * just replace the markdown file contents
	 *
	 * @return FALSE if fail
	 */
	public function updateArticle($uri, $article, $pathToRoot='.'){
		if ($this->updateArticleFile($uri, $article, $pathToRoot) === FALSE)
			return FALSE;
		return $this->updateArticleInList($uri, $article, $pathToRoot);
	}

	/**
	 * just replace the markdown file contents
	 *
	 * @return FALSE if fail
	 */
	private function updateArticleFile($uri, $article, $pathToRoot='.'){
		$u=new LDUtils();
		if ($u->isAbsoluteUrl($uri))
			return FALSE;
		return $article->writeToFile($pathToRoot.'/'.$uri);
	}

	private function updateArticleInList($uri, $article, $pathToRoot='.'){			
		if ($this->l->readFromFile($pathToRoot.'/'.$this->articlesFile)==FALSE) return FALSE;
		if ($this->l->update($uri, $article, $pathToRoot)==FALSE) return FALSE;
		return $this->l->writeToFile($pathToRoot.'/'.$this->articlesFile);
	}
	
	/**
	 * Remove the Markdown file and the article from the articles list.
	 * @param $uri string URI of the article to be removed.
	 * @param $pathToRoot string path from the current execution directory to the root directory (the one where index.php is 		 */
	public function removeArticle($uri, $pathToRoot='.'){
		if ($this->l->readFromFile($pathToRoot.'/'.$this->articlesFile)==FALSE) return FALSE;
		if ($this->l->remove($uri) && $this->l->writeToFile($pathToRoot.'/'.$this->articlesFile)==FALSE)
			return FALSE;
		$u=new LDUtils();
		if ($u->isAbsoluteUrl($uri))
			return TRUE;
		return unlink($pathToRoot.'/'.$uri);
	}
}
?>
