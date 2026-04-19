<?php
    include "C:/xampp/htdocs/GreenSecure/Model/AssuranceType.php";
    include_once "C:/xampp/htdocs/GreenSecure/config.php";


    class ControlTypes{
        public function listeType(){
            $db=config::getConnexion();
            try{
                $liste=$db->query('SELECT * FROM type_offre');
                return $liste;
            }catch(Exception $e){
                die('Erreur:'.$e->getMessage());
            }
        }
        public function addType($type){
            $db=config::getConnexion();
            try{
                $req=$db->prepare('INSERT INTO type_offre VALUES (NULL, :t, :d, :i, :c)');
                $req->execute([
                    't' => $type->getTitre(),
                    'd' => $type->getDescription(),
                    'i' => $type->getImage(),
                    'c' => $type->getPublished_AT()->format('Y-m-d'),
                ]);
                //$type_id = $db->lastInsertId();

                // Store normalized rows in assurance_variable
                /*foreach ($type->getVariables() as $var) {
                    $q2 = $db->prepare(
                        "INSERT INTO assurance_variable (type_id, name, type)
                        VALUES (:tid, :name, :type)"
                    );
                    $q2->execute([
                        'tid'  => $type_id,
                        'name' => $var->getName(),
                        'type' => $var->getType(),
                    ]);
                }*/

                // Create the dynamic inscription table
                //$this->createInscriTable($type->getVariables(), "inscription_type_$type_id");

                //return $type_id;
            }catch(Exception $e){
                die('Erreur:'.$e->getMessage());
            }
        }
        public function deleteType($id){
            $db = config::getConnexion();
            try{
                $req = $db->prepare('
                DELETE FROM type_offre where TypeID=:TypeID
                ');
                $req->execute([
                    'TypeID'=>$id
                ]);
            } catch (Exception $e) {
                die('Erreur: '.$e->getMessage());
            }
        }
        public function updateType($type,$TypeID){
            $db = config::getConnexion();
            try{
                $req = $db->prepare('UPDATE type_offre SET Titre=:Titre, Description=:Description, Image=:Image WHERE TypeID=:TypeID');
                $req->execute([
                    'TypeID'=>$TypeID,
                    'Titre'=>$type->getTitre(),
                    'Description'=>$type->getDescription(),
                    'Image'=>$type->getImage()
                ]);
            } catch (Exception $e) {
                die('Erreur: '.$e->getMessage());
            }
        }
    }