<?php

    include "C:/xampp/htdocs/GreenSecure/Model/Inscription.php";
    include_once "C:/xampp/htdocs/GreenSecure/config.php";

    class Controlnscription{
        public function listeInscription($txt){
            $db=config::getConnexion();
            try{
                if(empty($txt)){
                    $liste=$db->query('SELECT * FROM inscription');
                    return $liste;
                }
                $req = $db->prepare('SELECT * FROM inscription WHERE Choix LIKE :search OR CAST(date_souscription AS CHAR) LIKE :search OR Payment_status LIKE :search OR Payment_method LIKE :search');
                $req->bindValue(':search',"%$txt%");
                $req->execute();
                return $req->fetchAll(PDO::FETCH_ASSOC);
            }catch(Exception $e){
                die('Erreur:'.$e->getMessage());
            }
        }
        public function addInscription($inscription){
            $db = config::getConnexion();
            try{
                $req=$db->prepare('INSERT INTO inscription VALUES (NULL, :ds, :de, :ps, :pm, :m, :c, :ch)');
                $req->execute([
                    'ds' => $inscription->getSouscription()->format('Y-m-d'),
                    'de' => $inscription->getExpiration()->format('Y-m-d'),
                    'ps' => $inscription->getStatus(),
                    'pm' => $inscription->getMethod(),
                    'm' => $inscription->getMontant(),
                    'c' => $inscription->getCreation()->format('Y-m-d'),
                    'ch' => $inscription->getChoix()
                ]);
                return true;
            }catch(Exception $e){
                die('Erreur:'.$e->getMessage());
            }
        }
        public function deleteInscription($id){
            $db = config::getConnexion();
            try{
                $req = $db->prepare('
                DELETE FROM inscription where InscriptionID=:InscriptionID
                ');
                $req->execute([
                    'InscriptionID'=>$id
                ]);
            } catch (Exception $e) {
                die('Erreur: '.$e->getMessage());
            }
        }
        public function updateInscription($inscription,$InscriptionID){
            $db = config::getConnexion();
            try{
                $req = $db->prepare('UPDATE inscription SET date_souscription=:date_souscription, date_expiration=:date_expiration, Payment_status=:Payment_status, Payment_method=:Payment_method, Montant_paye=:Montant_paye, Created_AT=:Created_AT, Choix=:Choix  WHERE InscriptionID =:InscriptionID ');
                $req->execute([
                    'InscriptionID'=>$InscriptionID ,
                    'date_souscription'=>$inscription->getSouscription()->format('Y-m-d'),
                    'date_expiration'=>$inscription->getExpiration()->format('Y-m-d'),
                    'Payment_status'=>$inscription->getStatus(),
                    'Payment_method' =>$inscription->getMethod(),
                    'Montant_paye' =>$inscription->getMontant(),
                    'Created_AT' =>$inscription->getCreation()->format('Y-m-d'),
                    'Choix' =>$inscription->getChoix()
                ]);
            } catch (Exception $e) {
                die('Erreur: '.$e->getMessage());
            }
        }
        public function listeInscriptionByOffre($titre) {
            $db = config::getConnexion();
            try {
                $req = $db->prepare('SELECT * FROM inscription WHERE Choix = :titre');
                $req->execute(['titre' => $titre]);
                return $req->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
    }
?>