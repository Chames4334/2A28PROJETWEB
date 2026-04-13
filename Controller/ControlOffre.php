<?php
    include "C:/xampp/htdocs/GreenSecure/Model/Offre.php";
    include "C:/xampp/htdocs/GreenSecure/config.php";
    class controlOffre{
        public function listeOffre(){
            $db=config::getConnexion();
            try{
                $liste=$db->query('SELECT * FROM offre');
                return $liste;
            }catch(Exception $e){
                die('Erreur:'.$e->getMessage());
            }
        }
        public function addOffre($offre){
            $db=config::getConnexion();
            try{
                $req=$db->prepare('INSERT INTO offre VALUES (NULL, :t, :ty, :p, :dd, :df, :s)');
                $req->execute([
                    't' => $offre->getTitle(),
                    'ty' => $offre->getType(),
                    'p' => $offre->getPrix(),
                    'dd' => $offre->getDate_Debut()->format('Y-m-d'),
                    'df' => $offre->getDate_Fin()->format('Y-m-d'),
                    's' => $offre->getStatus()
                ]);
            }catch(Exception $e){
                die('Erreur:'.$e->getMessage());
            }
        }
        public function deleteOffre($id){
            $db = config::getConnexion();
            try{
                $req = $db->prepare('
                DELETE FROM offre where OffreID=:OffreID
                ');
                $req->execute([
                    'OffreID'=>$id
                ]);
            } catch (Exception $e) {
                die('Erreur: '.$e->getMessage());
            }
        }
        public function updateOffre($offre,$OffreID){
            $db = config::getConnexion();
            try{
                $req = $db->prepare('UPDATE offre SET Title=:Title, Type=:Type, Prix_mensuel=:Prix_mensuel, Date_Debut=:Date_Debut, Date_Fin=:Date_Fin, Status=:Status  WHERE OffreID=:OffreID');
                $req->execute([
                    'OffreID'=>$OffreID,
                    'Title'=>$offre->getTitle(),
                    'Type'=>$offre->getType(),
                    'Prix_mensuel'=>$offre->getPrix(),
                    'Date_Debut' =>$offre->getDate_Debut()->format('Y-m-d'),
                    'Date_Fin' =>$offre->getDate_Fin()->format('Y-m-d'),
                    'Status' =>$offre->getStatus()
                ]);
            } catch (Exception $e) {
                die('Erreur: '.$e->getMessage());
            }
        }
    }

?>