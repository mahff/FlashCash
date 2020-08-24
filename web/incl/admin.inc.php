<?php
class DB_Functions{
  private $db;
  function __construct() {
    define("DB_HOST", "10.40.128.23");
    define("DB_USER", "y2018l3i_mamhiyen");
    define("DB_PASSWORD", "Fjk45tv*");
    define("DB_DATABASE", "db2018l3i_mamhiyen");
    $conn_string = "host=".DB_HOST. " port=5432 dbname=".DB_DATABASE." user=".DB_USER." password=".DB_PASSWORD;
    $dbconn4 = pg_connect($conn_string);
  }

  /*----------------------------------------------------------------------------------
  Ce fichier contient les fonctions utilisées dans la partie administrateurs
  ----------------------------------------------------------------------------------*/

  //Vérifie les identifiants des administrateurs pour autoriser ou non la connexion
  public function connectionAdmin(){
    if(isset($_POST['login']) && isset($_POST['pass'])){
      //Récupération des identifiants
      $username =htmlentities(trim($_POST['login']));
      $password =htmlentities(trim($_POST['pass']));
      //Vérification de la correspondance des données avec celles de la BDD
      if($username && $password){
        $pass = sha1($password);
        $log= pg_query("SELECT * FROM admin WHERE nomAdm='$username' AND hashMotDePasse='$pass'");
        $rows = pg_fetch_row($log);
        if($rows){       //Autorisation de la connexion si la requête est valide
          $_SESSION['login']=$username;
          $_SESSION['id']=$rows[0];
          echo "<script type='text/javascript'>document.location.replace('gestionnaire.php');</script>";
        }else echo "Identifiant et/ou mot de passe incorrect";
      }else echo "Veuillez saisir tous les champs";
    }
  }

  //Affichage des principales tables
  public function dbManage($request){
    if(isset($_SESSION["id"]) && isset($_SESSION["login"])){
      if ($request == 'utilisateur')    //Selection des données n'affichant pas le mot de passe utilisateur
      $query = "SELECT idutilisateur,mail,login,telephone,etat FROM ".$request.";";
      else
      $query = "SELECT * FROM ".$request.";";
      $queryult = pg_query($query);
      $index = 0;
      echo '<table><CAPTION>'. $request .'</CAPTION><tr>';   //Création d'un tableau contenant les données de la table
      while ($index < pg_num_fields($queryult)){
        $fieldName = pg_field_name($queryult, $index);    //Récupération des noms des champs
        echo '<td>' . $fieldName . '</td>';
        $index = $index + 1;
      }
      echo '</tr>';
      while ($line = pg_fetch_array($queryult, null, PGSQL_ASSOC)){
        echo "\t<tr>\n";
        foreach ($line as $col_value) {
          echo "\t\t<td>$col_value</td>\n";   //Affichage des données contenues dans les champs
        }
        echo "\t</tr>\n";
      }
      echo '</table>';
    }
  }

  //Formulaire déroulant pour chercher une table précise
  public function search(){
    if(isset($_SESSION["id"]) && isset($_SESSION["login"])){
      $query = "SELECT operationclient,operationmachine,machine.idmachine,depot.montant,idqr,utilisateur.idutilisateur
      FROM administrationclient,administrationmachine,machine,depot,qr,utilisateur";
      $queryult = pg_query($query);
      $index = 0;
      //Formulaire récupérant les titres des tables
      echo "<form action=\"#\" method=\"post\">";
      echo "<label for='search'>Faites votre choix</label><br />";
      echo "<select name='search' id='search'>";
      while ($index < pg_num_fields($queryult)){
        $fieldName = pg_field_table($queryult, $index);
        echo '<option>' . $fieldName . '</option>';
        $index = $index + 1;
      }
      echo "</select>";
      echo "<p><input type=\"submit\" value=\"Choisir\"/></p>";
      echo "</form>";
      echo "<br/>";

      if(!empty($_POST['search'])){
        $index = 0;
        $value=$_POST['search'];
        if ($value == 'utilisateur')
        $query = pg_query("SELECT idutilisateur,mail,login,telephone FROM ".$value);  //Selection des données utilisateurs hors mot de passe
        else
        $query = pg_query("SELECT * FROM ".$value);
        //Affichage du tableau résultant de la rêquete initiale
        echo '<table><CAPTION>' . $value . '</CAPTION> <tr>';
        while ($index < pg_num_fields($query)){
          $fieldName = pg_field_name($query, $index);
          echo '<td>' . $fieldName . '</td>';
          $index = $index + 1;
        }
        echo '</tr>';
        $index = 0;
        while ($line = pg_fetch_array($query, null, PGSQL_ASSOC)) {
          echo "<tr>";
          foreach ($line as $col_value) {
            echo "<td>$col_value</td>";
          }
          echo "</tr>";
        }
        echo '</table>';
      }
    }
  }

  //Permet d'ajouter les données dans une table choisie par l'utilisateur
  public function insertTable(){
    if(isset($_POST['table'])){
      $query = pg_query("SELECT * FROM ".$_POST['table']);    //Requête qui récupère (dans la fonction operationsTables())
      //la table où effectuer l'opération
      $fields = array();
      $str = "INSERT INTO ".$_POST['table']." (";             //Les variables $str et $values construisent la requête d'ajout
      $values = " VALUES ('";
      for($i=0;$i<pg_num_fields($query);$i++){
        $fieldName = pg_field_name($query, $i);
        if(isset($_POST[$fieldName])) $fields[$i] = htmlentities(trim($_POST[$fieldName]));
        $str .= $fieldName;
        $values .= $fields[$i];
        if($i+1!=pg_num_fields($query)){
          $str .= ",";
          $values .= "','";
        }
        else{
          $str .= ") ";
          $values .= "')";
        }
      }
      $queryult = pg_query($str.$values);  //Effectue la requête ajouter dans la BDD
    }
  }

  //Fonction pour ajouter ou supprimer une ligne dans la table voulue
  public function operationsTables(){
    if(isset($_SESSION["id"]) && isset($_SESSION["login"])){
      $this->insertTable(); //Appel de la fonction permettant l'ajout des données

      //------------------------------------------------
      //Affichage des différents formulaires

      $query = "SELECT operationclient,operationmachine,machine.idmachine,depot.montant,idqr
      FROM administrationclient,administrationmachine,machine,depot,qr";
      $queryult = pg_query($query);
      $index = 0;
      //Formulaire déroulant de choix de la table
      echo "<form action=\"#\" method=\"post\">";
      echo "<br/><label for='choice'>Choix de la table où effectuer l'action :</label><br />";
      echo "<select name='choice' id='choice'>";
      while ($index < pg_num_fields($queryult)){
        $fieldName = pg_field_table($queryult, $index);
        echo '<option>' . $fieldName . '</option>';
        $index = $index + 1;
      }
      echo "</select>";
      // Formulaire déroulant de choix de l'opération
      echo "<br/><label for='supa'>Supprimer ou ajouter :</label><br />";
      echo "<select name='supa' id='supa'>";
      echo '<option>' . 'ajouter' . '</option>';
      echo '<option>' . 'supprimer' . '</option>';
      echo "</select>";
      echo "<br/>";

      //Formulaire text dans lequel il faut indiqué la valeur du premier ID dans la table à supprimer ou ne rien indiquer pour l'opération ajouter
      echo "<br/><label>Si vous souhaitez supprimer veuillez indiquer la valeur du premier ID</label><br />";
      echo "<p><input type='text' name='value'/></p>";
      echo "<br/>";

      //Bouton submit de validation des formulaires précents
      echo "<p><input type=\"submit\" value=\"Entrer\"/></p>";
      echo "</form>";
      echo "<br/>";

      //-------------------------------------------------------------
      //Traitement des données des formulaires

      //Opération supprimer construite à partir de l'entrée de l'ID à supprimer
      if(isset($_POST['choice']) && isset($_POST['supa']) && !empty($_POST['value'])){
        $choice=$_POST['choice'];
        $operation=$_POST['supa'];
        $value=$_POST['value'];
        if(($operation =='supprimer') && ($value!='null')){
          if (($choice=='administrationclient') || ($choice='administrationmachine'))
          $id='idoperation';
          else if ($choice=='machine')
          $id='idmachine';
          else if ($choice=='depot')
          $id='iddepot';
          else if ($choice=='qr')
          $id='idqr';
          else if($choice=='utilisateur')
          $id='idutilisateur';
          $queryult = pg_query("DELETE FROM ".$choice." WHERE ".$id." = ".$value.";");
        }
      }

      if(isset($_POST['choice'])) $choice=$_POST['choice'];
      else $choice = "administrationclient";
      if(isset($_POST["supa"])) $operation=$_POST['supa'];
      else $operation = "rien";

      //Opération ajouter
      if(($operation == 'ajouter')){
        $index = 0;
        $query = pg_query("SELECT * FROM ".$choice.";" );
        //Affichage des champs à compléter correspondant à la table sélectionnée
        echo '<form method="post" action="search.php">';
        while ($index < pg_num_fields($query)){
          echo '<input type="hidden" value="'.$choice.'" name="table">';
          $fieldName = pg_field_name($query, $index);
          echo $fieldName .': <input type="text" name="'.$fieldName.'"></br>';
          $index++;
        }
        echo '<input type="submit" value="Entrer" name="submit">';
        echo '</form>';
      }
      else {
        echo "Veuillez compléter tout le formulaire";
      }
    }
  }

}
?>
