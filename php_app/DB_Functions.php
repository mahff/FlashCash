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

  public function storePartUser($login, $email, $password, $number, $first, $last){
    $hash = sha1($password);
    $etat = "Maintenu";
    $sel = "";
    if(!empty($login) || !empty($email) || !empty($password) || !empty($number) || !empty($first) || !empty($last)){
      $query = pg_query("SELECT * FROM utilisateur WHERE login='" . $login . "' OR mail='" . $email . "';");
      $row = pg_fetch_array($query);
      $money = 0;
      if(!$row){
        $result = pg_query("INSERT INTO utilisateur(mail, login, telephone, hashMotDePasse, etat, solde)
        VALUES ('$email', '$login', '$number', '$hash', '$etat', '$money') RETURNING idutilisateur");
        $res = pg_query("SELECT * FROM utilisateur WHERE login='" . $login . "' OR mail='" . $email . "';");
        $return = pg_fetch_array($res);
        if($res){
          $insert = pg_query("INSERT INTO particulier(idutilisateur, nomPar, prenom)
          VALUES ($return[0],'$last', '$first')");
          if($insert){
            $set = pg_query("SELECT * FROM particulier p, utilisateur u WHERE u.idUtilisateur = '" . $return[0] . "' AND u.idUtilisateur=p.idUtilisateur;");
            return pg_fetch_array($set);
          }
        }
      }
    }
  }

  public function storeProUser($login, $email, $password, $number, $siret, $last){
    $hash = sha1($password);
    $etat = "Maintenu";
    $sel = "";
    if(!empty($login) || !empty($email) || !empty($password) || !empty($number) || !empty($siret) || !empty($last)){
      $query = pg_query("SELECT * FROM utilisateur WHERE login='" . $login . "' OR mail='" . $email . "';");
      $row = pg_fetch_array($query);
      $money = 0;
      if(!$row){
        $result = pg_query("INSERT INTO utilisateur(mail, login, telephone, hashMotDePasse, etat, solde)
        VALUES ('$email', '$login', '$number', '$hash', '$etat', '$money') RETURNING idutilisateur");
        $res = pg_query("SELECT * FROM utilisateur WHERE login='" . $login . "' OR mail='" . $email . "';");
        $return = pg_fetch_array($res);
        if($res){
          $insert = pg_query("INSERT INTO professionnel(idutilisateur, nomPro, siret)
          VALUES ($return[0],'$last', '$siret')");
          if($insert){
            $set = pg_query("SELECT * FROM professionnel p, utilisateur u WHERE u.idUtilisateur = '" . $return[0] . "' AND u.idUtilisateur=p.idUtilisateur;");
            return pg_fetch_array($set);
          }
        }
      }
    }
  }

  public function getUser($login, $password){
    $hash = sha1($password);
    $query = pg_query("SELECT * FROM utilisateur WHERE login='" . $login . "' AND hashMotDePasse='" . $hash . "';");
    return pg_fetch_array($query);
  }

  public function history($id){
    $query = pg_query("SELECT u.login as acheteur, u2.login as vendeur, service, montant, date FROM utilisateur u, utilisateur u2, qr q, transaction t
      WHERE t.idUtilisateur = '".$id."' AND t.idQr = q.idQr AND u2.idUtilisateur = destinataire AND u.idUtilisateur = t.idUtilisateur ORDER BY date DESC; ");
      $someArray = [];
      while ($row = pg_fetch_assoc($query)) {
        array_push($someArray, [
          'acheteur'   => $row['acheteur'],
          'vendeur' => $row['vendeur'],
          'service' => $row['service'],
          'montant' => $row['montant'],
          'date' => $row['date']
        ]);
      }
      return $someArray;
    }

    public function generate($dest, $service, $amount){
      $someArray = [];
      $query = pg_query("SELECT idUtilisateur FROM utilisateur where login = '". $dest ."' ");
      $row = pg_fetch_array($query);

      if($row){
        $idDest =$row[0];
        $result = pg_query("INSERT INTO qr(idqr, destinataire, montant, produit, dateexpiration)
        VALUES (DEFAULT, '$idDest', '$amount', '$service', CURRENT_TIMESTAMP+interval '5 minutes')");
        $res = pg_query("SELECT * FROM qr where montant='". $amount ."' AND produit='". $service ."' AND destinataire='". $idDest ."' AND dateexpiration>=CURRENT_TIMESTAMP");
        while ($get = pg_fetch_assoc($res)) {
          array_push($someArray, [
            'destinataire'   => $get['destinataire'],
            'idqr' => $get['idqr'],
            'service' => $get['produit'],
            'montant' => $get['montant'],
            'date' => $get['dateexpiration']
          ]);
        }
      }
      return $someArray;
    }

    public function scan($id, $iduser){
      $query = pg_query("SELECT idqr FROM qr");
      $someArray = [];
      while ($get = pg_fetch_array($query)) {
        $temp = $get[0];
        if($id == sha1($temp)){
          $pg_query = pg_query("SELECT * FROM qr WHERE idqr = '". $temp ."'");
          $row = pg_fetch_array($pg_query);
          if($row){
            $idqr =  $row['idqr'];
            $service = $row['produit'];
            $amount = $row['montant'];
            $seller = $row['destinataire'];
            $insert = pg_query("INSERT INTO transaction(idqr, idutilisateur, date, service)
            VALUES ($idqr, $iduser, CURRENT_TIMESTAMP, '$service')");
            $res = pg_query("SELECT * FROM transaction where idqr='". $idqr ."' AND service='". $service ."' AND idutilisateur='". $iduser ."'");
            $solde =pg_query("SELECT u1.solde AS buyer, u2.solde AS seller FROM utilisateur u1, utilisateur u2
              WHERE u1.idUtilisateur = '". $iduser ."'   AND u2.idUtilisateur = '". $seller ."' ;");
              $money = pg_fetch_array($solde);
              if($money){
                $moneyBuyer = $money[0] - $amount;
                $moneySeller = $money[1] + $amount;
                pg_query("UPDATE utilisateur SET solde = '".  $moneyBuyer ."' WHERE idUtilisateur='". $iduser ."'");
                pg_query("UPDATE utilisateur SET solde = '".  $moneySeller ."' WHERE idUtilisateur='". $seller ."'");

              }
            while ($get = pg_fetch_assoc($res)) {
              array_push($someArray, [
                'idqr' => $get['idqr'],
                'service' => $get['service'],
                'idutilisateur' => $get['idutilisateur'],
                'date' => $get['date']
              ]);
            }
          }
        }
      }
      return $someArray;
    }

    public function depot($idUser, $money, $idMachine){
      $query = pg_query("INSERT INTO depot(idutilisateur, idmachine, date, montant)
      VALUES ('$idUser', '$idMachine', CURRENT_DATE, '$money')");
      $select = pg_query("SELECT montant, idUtilisateur FROM depot WHERE idUtilisateur='". $idUser ."' AND montant = '". $money ."' ");
      $row = pg_fetch_row($select);
      if($row){
        $user = $row[1];
        $solde = $row[0];
        $getSolde = pg_query("SELECT solde FROM utilisateur WHERE idUtilisateur='". $idUser ."'");
        $rows = pg_fetch_row($getSolde);
        if($rows){
          $currentMoney = $rows[0] + $solde ;
          $update = pg_query("UPDATE utilisateur SET solde = '".  $currentMoney ."' WHERE idUtilisateur='". $user ."'");
          return pg_fetch_array($update);
        }
      }
    }
  }

  ?>
