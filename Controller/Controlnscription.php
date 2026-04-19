<?php

    include "C:/xampp/htdocs/GreenSecure/Model/Inscription.php";
    include_once "C:/xampp/htdocs/GreenSecure/config.php";

    class Controlnscription{
        /*private function mapType($type) {
            switch ($type) {
                case "INT":
                    return "INT";
                case "DATE":
                    return "DATE";
                default:
                    return "VARCHAR(255)";
            }
        }*/
        public function listeInscription(){
            $db=config::getConnexion();
            try{
                $liste=$db->query('SELECT * FROM inscription');
                return $liste;
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
        /*public function createInscriTable($variable,$table){
            $db=config::getConnexion();
            try {
                $sql = "CREATE TABLE `$table` (";
                $sql .= "`{$table}ID` INT PRIMARY KEY AUTO_INCREMENT,";

                foreach ($variable as $var) {
                    $name = $var['name'];
                    $type = $this->mapType($var['type']);

                    $sql .= "`$name` $type,";
                }

                $sql = rtrim($sql, ",");
                $sql .= ")";

                $db->exec($sql);
                
            } catch (Exception $e) {
                die('Erreur:'.$e->getMessage());
            }
        }*/
        /*public function addType($type){
            $db = config::getConnexion();

            try {
                // 1. Insert type
                $sql = "INSERT INTO assurance_type (title, description)
                        VALUES (:t, :d)";
                $query = $db->prepare($sql);
                $query->execute([
                    't' => $type->getTitle(),
                    'd' => $type->getDescription()
                ]);

                $type_id = $db->lastInsertId();

                foreach ($type->getVariables() as $var) {
                    $sqlVar = "INSERT INTO assurance_variable (type_id, name, type)
                            VALUES (:type_id, :name, :type)";

                    $q = $db->prepare($sqlVar);
                    $q->execute([
                        'type_id' => $type_id,
                        'name' => $var['name'],
                        'type' => $var['type']
                    ]);
                }
                $this->createInscriTable(
                    $type->getVariables(),
                    "inscription_type_" . $type_id
                );

            } catch (Exception $e) {
                die('Erreur:'.$e->getMessage());
            }
        }
        public function getVariablesByType($type_id){
            $db = config::getConnexion();

            $sql = "SELECT * FROM assurance_variable WHERE type_id = :id";
            $q = $db->prepare($sql);
            $q->execute(['id' => $type_id]);

            return $q->fetchAll();
        }
        public function addInscription($type_id, $data){
            $db = config::getConnexion();

            try {
                $table = "inscription_type_" . $type_id;

                $columns = [];
                $values = [];
                $params = [];

                foreach ($data as $key => $value) {
                    $columns[] = "`$key`";
                    $values[] = ":$key";
                    $params[$key] = $value;
                }

                $sql = "INSERT INTO `$table` (" . implode(",", $columns) . ")
                        VALUES (" . implode(",", $values) . ")";

                $q = $db->prepare($sql);
                $q->execute($params);

            } catch (Exception $e) {
                die('Erreur:'.$e->getMessage());
            }
        }*/
    }
?>