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


  /*---------------------------------------------------------------------------
  Ce fichier contient les fonctions utilisées dans la partie utilisateurs
  (c'est à dire pour les particuliers ou professionnels)
  ---------------------------------------------------------------------------*/

  //Vérifie les identifiants des utilisateus pour autoriser ou non la connexion
  public function connectionPart(){
    if(isset($_POST['login']) && isset($_POST['pass'])){
      $username =htmlentities(trim($_POST['login']));
      $password =htmlentities(trim($_POST['pass']));
      //Vérification de la correspondance des données avec celles de la BDD
      if($username && $password){
        $pass = sha1($password);
        $log= pg_query("SELECT * FROM utilisateur WHERE login='$username' AND hashMotDePasse='$pass'");
        $rows = pg_fetch_row($log);
        if($rows){  //Autorisation de la connexion si la requête est valide
          $_SESSION['login']=$username;
          $_SESSION['id']=$rows[0];
          echo "<script type='text/javascript'>document.location.replace('history.php');</script>";
        }else echo "Identifiant et/ou mot de passe incorrect";
      }else echo "Veuillez saisir tous les champs";
    }
  }

  //Ajoute un compte utilisateur de type particulier
  public function storeUserPart(){
    if(isset($_POST['login']) && isset($_POST['pass']) && isset($_POST['mail'])
    && isset($_POST['number']) && isset($_POST["confirm"]) && isset($_POST['nomPart']) && isset($_POST['prenom'])){
      //Récupération des données entrer dans les formulaires
      $username =htmlentities(trim($_POST['login']));
      $confirm = htmlentities(trim($_POST['confirm']));
      $password =htmlentities(trim($_POST['pass']));
      $mail =htmlentities(trim($_POST['mail']));
      $number =htmlentities(trim($_POST['number']));
      $nomPart = htmlentities(trim($_POST['nomPart']));
      $prenom = htmlentities(trim($_POST['prenom']));
      if($username && $password && $mail && $number && $nomPart && $confirm && $prenom){
        if($password == $confirm){    //Vérification de l'exactitude des mots de passe
          $hash = sha1($password);    //Hachage des mots de passe pour sécurisation dans la BDD
          $etat = "Maintenu";         //Ajout des nouvelles valeurs de départ
          $solde = "0";
          $query = pg_query("SELECT * FROM utilisateur WHERE login='" . $username . "' OR mail='" . $mail . "';");
          $row = pg_fetch_row($query);
          if(!$row){
            //Ajout des valeurs correspondant à la table utilisateur dans cet table
            $result = pg_query(("INSERT INTO utilisateur(idutilisateur, mail, login, telephone, hashMotDePasse, etat,solde)
            VALUES (DEFAULT, '$mail', '$username', '$number', '$hash', '$etat','$solde')"));
            $new = pg_query("SELECT * FROM utilisateur WHERE login='" . $username . "' OR mail='" . $mail . "';");
            $rows = pg_fetch_row($new);
            if($rows){
              //Ajout des valeurs correspondant à la table particulier dans cet table
              $insert = pg_query(("INSERT INTO particulier(idutilisateur, nomPar, prenom)
              VALUES ('$rows[0]', '$nomPart', '$prenom')"));
              echo "<script type='text/javascript'>document.location.replace('index_user.php');</script>";
            }
          }else echo "Identifiant ou mot de passe déjà existant, <a href=\"index.php\">veuillez vous connecter</a> ";
        }else echo "Mot de passe et confirmation non identique";
      }else echo "Veuillez saisir tous les champs";
    }
  }

  //Ajoute un compte utilisateur de type professionnel
  public function storeUserPro(){
    if(isset($_POST['login']) && isset($_POST['pass']) && isset($_POST['mail'])
    && isset($_POST['number']) && isset($_POST["confirm"]) && isset($_POST['nomPro']) && isset($_POST['siret'])){
      //Récupération des données entrer dans les formulaires
      $username =htmlentities(trim($_POST['login']));
      $confirm = htmlentities(trim($_POST['confirm']));
      $password =htmlentities(trim($_POST['pass']));
      $mail =htmlentities(trim($_POST['mail']));
      $number =htmlentities(trim($_POST['number']));
      $nomPro = htmlentities(trim($_POST['nomPro']));
      $siret = htmlentities(trim($_POST['siret']));
      if($username && $password && $mail && $number && $nomPro && $confirm && $siret){
        if($password == $confirm){      //Vérification de l'exactitude des mots de passe
          $hash = sha1($password);      //Hachage des mots de passe pour sécurisation dans la BDD
          $etat = "Maintenu";           //Ajout des nouvelles valeurs de départ
          $solde = "0";
          $query = pg_query("SELECT * FROM utilisateur WHERE login='" . $username . "' OR mail='" . $mail . "';");
          $row = pg_fetch_row($query);
          if(!$row){
            //Ajout des valeurs correspondant à la table utilisateur dans cet table
            $result = pg_query(("INSERT INTO utilisateur(idutilisateur, mail, login, telephone, hashMotDePasse, etat, solde)
            VALUES (DEFAULT, '$mail', '$username', '$number', '$hash', '$etat','$solde')"));
            $new = pg_query("SELECT * FROM utilisateur WHERE login='" . $username . "' OR mail='" . $mail . "';");
            $rows = pg_fetch_row($new);
            if($rows){
              //Ajout des valeurs correspondant à la table particulier dans cet table
              $insert = pg_query(("INSERT INTO professionnel(idutilisateur, nomPro, siret)
              VALUES ('$rows[0]', '$nomPro', '$siret')"));
              echo "<script type='text/javascript'>document.location.replace('index_user.php');</script>";
            }
          }else echo "Identifiant ou mot de passe déjà existant, <a href=\"index.php\">veuillez vous connecter</a> ";
        }else echo "Mot de passe et confirmation non identique";
      }else echo "Veuillez saisir tous les champs";
    }
  }

  //Affiche l'historique des utilisateurs dans un tableau
  public function getHistory($query){
    if(isset($_SESSION["id"]) && isset($_SESSION["login"])) {
      $id = $_SESSION["id"];
      $index = 0;
      echo '<h2>Voici les informations concernant vos achats </h2>';
      echo '<table><CAPTION> Historique de vos transactions </CAPTION> <tr>';
      while ($index < pg_num_fields($query)){
        $fieldName = pg_field_name($query, $index);
        echo '<td>' . $fieldName . '</td>';
        $index = $index + 1;
      }
      echo '</tr>';
      $index = 0;
      while ($line = pg_fetch_array($query, null, PGSQL_ASSOC)) {
        echo "\t<tr>\n";
        foreach ($line as $col_value) {
          echo "\t\t<td>$col_value</td>\n";
        }
        echo "\t</tr>\n";
      }
      echo '</table>';
    }
  }

  //Donne les informations personnelles des utilisateurs selon leur profil (particulier ou professionnel)
  public function getMyInfo(){
    if(isset($_SESSION["id"]) && isset($_SESSION["login"])){
      $id = $_SESSION["id"];
      //Requête de base pour vérifier si l'utilisateur est de type particulier ou professionnel
      $equalID = pg_query("SELECT particulier.idutilisateur
        FROM particulier
        WHERE particulier.idutilisateur='$id'");
        $row = pg_fetch_array($equalID);
        if($row[0] == $id) {    //Test si l'utilisateur est de type particulier
          //Requête pour sélectionner les éléments du particulier connecté
          $query = pg_query("SELECT utilisateur.idutilisateur,mail,login,telephone,solde,nompar,prenom
            FROM utilisateur
            INNER JOIN particulier
            ON utilisateur.idutilisateur=particulier.idutilisateur
            WHERE utilisateur.idutilisateur='$id'");
          }
          else{
            //Requête pour sélectionner les éléments du professionnel connecté
            $query = pg_query("SELECT utilisateur.idutilisateur,mail,login,telephone,solde,nompro,siret
              FROM utilisateur
              INNER JOIN professionnel
              ON utilisateur.idutilisateur=professionnel.idutilisateur
              WHERE professionnel.idutilisateur='$id'
              AND  utilisateur.idutilisateur=professionnel.idutilisateur");
            }
            $index = 0;
            //Affichage des résultats de la requête dans un tableau
            echo '<h2>Voici les informations vous concernant </h2>';
            echo '<table><CAPTION> Mes informations </CAPTION> <tr>';
            while ($index < pg_num_fields($query)){
              $fieldName = pg_field_name($query, $index);
              echo '<td>' . $fieldName . '</td>';    //Affichage du nom des champs
              $index = $index + 1;
            }
            echo '</tr>';
            $index = 0;
            while ($line = pg_fetch_array($query, null, PGSQL_ASSOC)) {
              echo "\t<tr>\n";
              foreach ($line as $col_value) {
                echo "\t\t<td>$col_value</td>\n";   //Affichage du contenu des champs
              }
              echo "\t</tr>\n";
            }
            echo '</table>';
          }
        }

        //Permet de chercher des informations dans les tables en lien avec les utilisateurs
        public function search_user(){
          if(isset($_SESSION["id"]) && isset($_SESSION["login"])) {
            $id = $_SESSION["id"];
            $query = "SELECT transaction.idqr,idmachine,utilisateur.idutilisateur
            FROM transaction,machine,qr,utilisateur
            WHERE utilisateur.idUtilisateur=".$id."
            ORDER BY date";
            $queryult = pg_query($query);
            $index = 0;
            //Formulaire déroulant du nom des tables
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
              if($value =='transaction') {
                $query = pg_query("SELECT transaction.idqr,idutilisateur,date,service,qr.montant
                  FROM ".$value.",qr
                  WHERE idUtilisateur=".$id."
                  AND transaction.idqr=qr.idqr");
                }
                else if($value =='machine') {
                  $query = pg_query("SELECT idmachine,lieu
                    FROM ".$value.",qr,transaction
                    WHERE idUtilisateur=".$id."
                    AND transaction.service=qr.produit
                    AND qr.destinataire=machine.idmachine");
                  }
                  else if($value =='utilisateur') {
                    $equalID = pg_query("SELECT particulier.idutilisateur
                      FROM particulier
                      WHERE particulier.idutilisateur=".$id);
                      $row = pg_fetch_array($equalID);
                      if($row[0] == $id) {  //Test pour voir si l'utilisateur est de type particulier ou professionnel
                        //Requête qui sélectionne les informations pour le particulier connecté
                        $query = pg_query("SELECT utilisateur.idutilisateur,mail,login,telephone,nompar,prenom,solde
                          FROM utilisateur
                          INNER JOIN particulier
                          ON utilisateur.idutilisateur=particulier.idutilisateur
                          WHERE utilisateur.idutilisateur=".$id);
                        }
                        else{
                          //Requête qui sélectionne les informations pour le particulier connecté
                          $query = pg_query("SELECT utilisateur.idutilisateur,mail,login,telephone,nompro,siret,solde
                            FROM utilisateur
                            INNER JOIN professionnel
                            ON utilisateur.idutilisateur=professionnel.idutilisateur
                            WHERE professionnel.idutilisateur=".$id."
                            AND  utilisateur.idutilisateur=professionnel.idutilisateur");
                          }
                        }
                        //Affichage dans un tableau du résultat de la requête
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

                  //Modifie une information sur l'utilisateur après choix de cet valeur par ce dernier
                  public function user_modif(){
                    if(isset($_SESSION["id"]) && isset($_SESSION["login"])){
                      $id = $_SESSION["id"];
                      $index = 0;
                      $equalID = pg_query("SELECT particulier.idutilisateur
                        FROM particulier,utilisateur
                        WHERE particulier.idutilisateur=".$id);
                        $row = pg_fetch_array($equalID);
                        if($row[0] == $id) { //Test pour voir si l'utilisateur est de type particulier ou professionnel
                          //Requête qui sélectionne les informations pour le particulier connecté
                          $query = "SELECT mail,login,telephone,nompar,prenom FROM utilisateur,particulier
                          WHERE utilisateur.idUtilisateur=".$id."
                          AND utilisateur.idutilisateur=particulier.idutilisateur";
                        }
                        else {
                          //Requête qui sélectionne les informations pour le professionnel connecté
                          $query = "SELECT mail,login,telephone,nompro,siret FROM utilisateur,professionnel
                          WHERE utilisateur.idUtilisateur=".$id."
                          AND utilisateur.idutilisateur=professionnel.idutilisateur";
                        }
                        $queryult = pg_query($query);
                        //Formulaire déroulant avec les champs modifiables par les utilisateurs
                        echo "<form action=\"#\" method=\"post\">";
                        echo "<label for='data'>Choississez la donnée à modifier</label><br />";
                        echo "<select name='data' id='data'>";
                        while ($index < pg_num_fields($queryult)){
                          $fieldName = pg_field_name($queryult, $index);
                          echo '<option>' . $fieldName . '</option>';
                          $index = $index + 1;
                        }
                        echo "</select>";

                        //Formulaire texte dans lequel indiquer la nouvelle valeur
                        echo "<br/><label>Indiquer la nouvelle donnée</label><br />";
                        echo "<p><input type='text' name='new_data'/></p>";
                        echo "<br/>";

                        //Bouton de validation des formulaires précédents
                        echo "<br/><p><input type=\"submit\" value=\"Choisir\"/></p>";
                        echo "</form>";
                        echo "<br/>";

                        if(isset($_POST['data']) && isset($_POST['new_data'])){
                          $modif_data=$_POST['data'];
                          $new_data=$_POST['new_data'];
                          $queryult_general=pg_query("SELECT mail,login,telephone FROM utilisateur WHERE utilisateur.idUtilisateur=".$id);
                          $index=0;
                          $index=0;
                          if($modif_data =='mail' || $modif_data =='login' || $modif_data =='telephone'){
                            $queryult=pg_query("UPDATE utilisateur SET $modif_data='$new_data'
                              WHERE utilisateur.idUtilisateur=$id");
                              echo '<meta http-equiv="refresh" content="0;url=#" />'; //Rafraichît la page pour afficher les bonnes valeurs dans la table de la fonction getMyInfo()
                            }
                            else if(($row[0] == $id)){  //Test pour voir si l'utilisateur est de type particulier ou professionnel
                              $queryult=pg_query("UPDATE particulier SET $modif_data='$new_data'
                                WHERE particulier.idUtilisateur=$id");
                                echo '<meta http-equiv="refresh" content="0;url=#" />';
                              }
                              else{
                                $queryult=pg_query("UPDATE professionnel SET $modif_data='$new_data'
                                  WHERE professionnel.idUtilisateur=$id");
                                  echo '<meta http-equiv="refresh" content="0;url=#" />';
                                }
                              }
                              else{
                                echo "Veuillez compléter le formulaire";
                              }

                            }

                          }

                        }
                        ?>
