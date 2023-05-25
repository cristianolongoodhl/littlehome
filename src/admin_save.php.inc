<!DOCTYPE html>
<html lang="it">
<head>
	<title>LittleHome - Amministrazione Sito - Fine</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="https://www.w3schools.com/w3css/4/w3.css" />
	<link id="style" rel="stylesheet" type="text/css" href="admin.css" />
</head>
<body>
	<h1>Amministrazione Sito - Fine</h1>
	<p>Le modifiche sono state effettuate con successo.</p>
<?php 
if ($keyGenerated)
	print '<p>È stata generata una nuova coppia di chiavi RSA.</p>';
?>
	<a href="admin.php" class="w3-btn w3-teal ">Torna alla pagina di amministrazione</a>
	<a href="../index.php" class="w3-btn w3-teal ">Vai al Sito</a>
</body>
</html>
