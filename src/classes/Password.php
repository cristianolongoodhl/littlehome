<?php
class Password{
	private $passwordMD5;

	/**
	  * Read the md5 sum of the expected password from a file
	  *
	  * @return true if the file exists, false otherwise
	  */
	public function readFromFile($path){
		if (!file_exists($path)) return false;
		$p=file_get_contents($path);
		if (!$p) return false;
		$this->passwordMD5=$p;	
		return true;
	}

	/**
	  * Write the md5 sum of the passowr  to a file
	  *
	  */
	public function writeToFile($password, $path){
		$p=md5($password);
		file_put_contents($path,$p);
	}

	/**
	  * check if password matches the expected one
	 */
	public function check($password){
		return isset($this->passwordMD5) && strcmp(md5($password),$this->passwordMD5)===0;
	}

	/**
	  * Execute an operation only if the correct password is provided.
	  *
	  * @param $title string used to construct password form title
	  * @param $backUrl string link of the back button
	  * @param $operation string a function wich will be executed if the password is correct, it must return true if succeeds, false otherwise
	  */
	public function secure($title,$backUrl,$operation){
		if (isset($_POST['password']))
			if ($this->check($_POST['password']))
				if ($operation())
					$this->showOKPage($title);
				else
					$this->showNOKPage($title);
			else
				$this->showPasswordForm($title,$backUrl,'<p>Password errata</p>');
		else
				$this->showPasswordForm($title,$backUrl);
	}

	private function showPasswordForm($title,$backUrl,$message=''){
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<title>LittleHome - <?=$title?> - Conferma</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="https://www.w3schools.com/w3css/4/w3.css" />
	<link id="style" rel="stylesheet" type="text/css" href="admin.css" />
</head>
<body>
	<h1><?=$title?> - Conferma</h1>
	<div class="w3-card-4">
		<form class="w3-container" method="POST">
			<p>Per confermare le modifiche &egrave; necessario inserire la password.</p>
			<?=$message?>
			<p><label>Password</label> <input type="password" name="password" class="w3-input w3-border" /></p>
			<nav class="nextprev">
					<a href="<?=$backUrl?>" class="w3-btn w3-teal ">&#10094; Indietro</a>
					<a href="admin_clear.php" class="w3-btn w3-teal">Esci &#10006;</a>
					<input type="submit" class="w3-btn w3-teal " value="Avanti &#10095;" />
			</nav>
		</form>
	</div>
</body>
</html>
<?php			
	}
	
	/**
	  * Show that the operation completed successfully.
	  */
	private function showOKPage($title){
		$this->showEndPage($title,'Le modifiche sono state effettuate con successo.');
	} 

	/**
	  * Show that the operation completed with errors
	  */
	private function showNOKPage($title){
		$this->showEndPage($title,'Non &egrave; stato possibile effettuare le modifiche. Contattare l\'amministratore.');
	} 

	/**
	  * Show the page concluding the secured operation execution.
	  */
	private function showEndPage($title,$message){
		print '<!DOCTYPE html>';

?>
<html lang="it">
<head>
	<title>LittleHome - <?=$title?> - Fine</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="https://www.w3schools.com/w3css/4/w3.css" />
	<link id="style" rel="stylesheet" type="text/css" href="admin.css" />
</head>
<body>
	<h1><?=$title?> - Fine</h1>
	<p><?=$message?></p>
	<a href="admin.php" class="w3-btn w3-teal ">Torna alla pagina di amministrazione</a>
	<a href="../index.php" class="w3-btn w3-teal ">Vai al Sito</a>
</body>
</html>
<?php
	}
}
?>