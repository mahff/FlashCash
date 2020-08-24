<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Index</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<?php
		require 'incl/util_inc.php';
	?>
</head>

<body>
	<header>
		<img src="./img/logo.png" alt="logo de flash code" style="float:left;	width: 200px; height: 200px; margin-left: 20px; margin-bottom: 10px; margin-top: 5px">
	</header>

	<section id="connexion">
		<h1>Bienvenue sur le site FlashCash</h1>
    <p> Veuillez choisir votre type de connexion </p>
    <ul>
	 	 	<li><a href="src/index_admin.php">Gestionnaire</a></li>
			<li><a href="src/index_user.php">Utilisateur</a></li>
		</ul>
	</section>

	<footer id="footer_index">
    <?php
    	footer();
    ?>
  </footer>
</body>
</html>
