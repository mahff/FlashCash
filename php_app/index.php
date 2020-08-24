<?php
  if(isset($_POST['tag']) && $_POST['tag']!=''){
    $tag = $_POST['tag'];
    require_once 'DB_Functions.php';
    $db = new DB_Functions();
    $response = array("tag" => $tag, "error" => false);
    if($tag == 'registerPart'){
      $name = $_POST['login'];
      $email = $_POST['mail'];
      $password = $_POST['password'];
      $number = $_POST['telephone'];
      $last = $_POST["nomPar"];
      $first = $_POST["prenom"];
      $user = $db -> storePartUser($name, $email, $password, $number, $last, $first);
      if($user){
        $response["error"] = false;
        $response["id"] = $user["idutilisateur"];
        $response["user"]["login"] = $user["login"];
        $response["user"]["mail"] = $user["mail"];
        $response["user"]["password"] = $user["hashmotdepasse"];
        $response["user"]["telephone"] = $user["telephone"];
        $response["user"]["nomPar"] = $user["nompar"];
        $response["user"]["prenom"] = $user["prenom"];
        echo json_encode($response);
      }else{
        $response["error"] = true;
        echo json_encode($response);
      }
    }

    if($tag == 'registerPro'){
      $name = $_POST['login'];
      $email = $_POST['mail'];
      $password = $_POST['password'];
      $number = $_POST['telephone'];
      $last = $_POST["nomPro"];
      $siret = $_POST["siret"];
      $user = $db -> storeProUser($name, $email, $password, $number, $siret, $last);
      if($user){
        $response["error"] = false;
        $response["id"] = $user["idutilisateur"];
        $response["user"]["login"] = $user["login"];
        $response["user"]["mail"] = $user["mail"];
        $response["user"]["password"] = $user["hashmotdepasse"];
        $response["user"]["telephone"] = $user["telephone"];
        $response["user"]["nomPro"] = $user["nompro"];
        $response["user"]["siret"] = $user["siret"];
        echo json_encode($response);
      }else{
        $response["error"] = true;
        echo json_encode($response);
      }
    }

    if($tag == 'connection'){
      $name = $_POST['login'];
      $password = $_POST['hashmotdepasse'];
      $user = $db -> getUser($name, $password);
      if($user){
        $response["error"] = false;
        $response["id"] = $user["idutilisateur"];
        $response["user"]["login"] = $user["login"];
        $response["user"]["hashmotdepasse"] = $user["hashmotdepasse"];
        echo json_encode($response);
      }else{
        $response["error"] = true;
        echo json_encode($response);
      }
    }
    if($tag == 'history'){
      $id = $_POST['idutilisateur'];
      $user = $db -> history($id);
      if($user){
        echo json_encode($user);
      }
    }
    if($tag == 'generate'){
      $dest = $_POST['destinataire'];
      $service = $_POST['service'];
      $amount = $_POST['montant'];
      $user = $db -> generate($dest, $service, $amount);
      if($user){
        $response["error"] = false;

        $response["user"] = $user;
        echo json_encode($response);
      }else {
        $response["error"] = true;
        echo json_encode($response);
      }

    }

    if($tag == 'scan'){
      $id = $_POST['idqr'];
      $iduser = $_POST['iduser'];
      $user = $db -> scan($id, $iduser);
      if($user){
        $response["error"] = false;
        $response["user"] = $user;
        echo json_encode($response);
      }
      else {
        $response["error"] = true;
        echo json_encode($response);
      }
    }

    if($tag == 'depot'){
      $idUser = $_POST['user'];
      $idMachine = $_POST['machine'];
      $money = $_POST['solde'];
      $user = $db -> depot($idUser, $money,$idMachine);
      if($user){
        $response["error"] = false;
        $response["user"] = $user;
        echo json_encode($response);
      }else {
        $response["error"] = true;
        echo json_encode($response);
      }
    }
  }

 ?>
