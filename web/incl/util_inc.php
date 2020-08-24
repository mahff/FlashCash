<?php

/*---------------------------------------------------------------------------
  Ce fichier contient les fonctions récurrentes des pages
---------------------------------------------------------------------------*/

  //Affiche le prénom de l'utilisateur
  function bienvenue() {
    if(isset($_SESSION["id"]) && isset($_SESSION["login"])) {
      $id = $_SESSION["id"];
      echo  "<h1> Bienvenue ".$_SESSION["login"]."</h1>";
    }
  }

  //Fonction qui récupère la date
  function dateFRUS($format="us"){
    $mois = array('Janvier', 'février' ,'mars' ,	'avril' ,	'mai' 	,'juin '	,'juillet '	,'août' ,	'septembre' ,	'octobre', 	'novembre' ,	'décembre');
    $jours = array('Lundi', 'Mardi' , 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
    $months = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' , 'Sunday');
    $day = date('N');
    $day_number = date('d');
    $month = date('n');
    $year = date('Y');
    if($format == "fr") return $jours[$day-1]." ".$day_number." ".$mois[$month-1]." ".$year;
    elseif($format == "us") return $days[$day-1].", ".$months[$month-1]." ".$day_number.", ".$year;
  }

  //Utilisée dans le footer elle permet de récupérer la date (en français ou anglais) et d'afficher les noms des membres du projet
  function footer(){
    if(isset($_GET['lang'])){
      if($_GET['lang']=="fr") echo '<p>'.dateFRUS("fr").'</p>';
      elseif($_GET['lang']=="en") echo '<p>'.dateFRUS("us").'</p>';
    }
    else echo '<p>'.dateFRUS("us").'</p>';
    echo "<p>© Aurélien Ottaviano, Mahfoud Amhiyen, Solène Bisch, Amira Maaloul</p>";
  }

?>
