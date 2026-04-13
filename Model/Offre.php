<?php
    class Offre{
        private string $Title;
        private string $Type;
        private int $PrixBase;
        private DateTime $Date_debut;
        private DateTime $Date_fin;
        private string $Status;

        public function getTitle(){return $this->Title;}
        public function getType(){return $this->Type;}
        public function getStatus(){return $this->Status;}
        public function getPrix(){return $this->PrixBase;}
        public function getDate_Debut(){return $this->Date_debut;}
        public function getDate_Fin(){return $this->Date_fin;}

        public function setTitle(string $Title){$this->Title=$Title;}
        public function setType(string $Type){$this->Type=$Type;}
        public function setStatus(string $Status){$this->Status=$Status;}
        public function setPrix(int $PrixBase){$this->PrixBase=$PrixBase;}
        public function setDate_Debut(DateTime $Date_debut){$this->Date_debut=$Date_debut;}
        public function setDate_Fin(DateTime $Date_fin){$this->Date_fin=$Date_fin;}

        public function __construct(string $Title,string $Type,int $PrixBase,DateTime $Date_debut,DateTime $Date_fin, string $Status)
        {
            $this->Title=$Title;
            $this->Type=$Type;
            $this->PrixBase=$PrixBase;
            $this->Date_debut=$Date_debut;
            $this->Date_fin=$Date_fin;
            $this->Status=$Status;
        }
    }
    class voiture extends Offre{
        private int $Annee;
        private string $Matricule;
        private string $Marque;
        private string $Modele;
        private int $Puissance_cv;
        private int $Valeur;
        private string $Type_Carburant;

        public function getAge(){return $this->Annee;}
        public function getMatricule(){return $this->Matricule;}
        public function getMarque(){return $this->Marque;}
        public function getModele(){return $this->Modele;}
        public function getPuissance(){return $this->Puissance_cv;}
        public function getValeur(){return $this->Valeur;}
        public function getType(){return $this->Type_Carburant;}

        public function setAge(int $Annee){$this->Annee=$Annee;}
        public function setMatricule(string $Matricule){$this->Matricule=$Matricule;}
        public function setMarque(string $Marque){$this->Marque=$Marque;}
        public function setModele(string $Modele){$this->Modele=$Modele;}
        public function setPuissance(int $Puissance_cv){$this->Puissance_cv=$Puissance_cv;}
        public function setValeur(int $Valeur){$this->Valeur=$Valeur;}
        public function setType(string $Type_Carburant){$this->Type_Carburant=$Type_Carburant;}

        public function __construct(string $Annee, string $Matricule, string $Marque, string $Modele, int $Puissance_cv,int $Valeur, string $Type_Carburant)
        {
            $this->Annee=$Annee;
            $this->Matricule=$Matricule;
            $this->Marque=$Marque;
            $this->Modele=$Modele;
            $this->Puissance_cv=$Puissance_cv;
            $this->Valeur=$Valeur;
            $this->Type_Carburant=$Type_Carburant;
        }
    }
    class habitant extends Offre{
        private string $Adress;
        private string $Localisation;
        private int $Surface;
        private string $Type_logement;
        private int $valeur_bien;
        private int $meuble;

        public function getAdress(){return $this->Adress;}
        public function getLocalisation(){return $this->Localisation;}
        public function getSurface(){return $this->Surface;}
        public function getType(){return $this->Type_logement;}
        public function getValeur(){return $this->valeur_bien;}
        public function getMeuble(){return $this->meuble;}
    }
    class sante extends Offre{
        private int $age_limit;
        private string $type_couverture;
        private int $plafond_remboursement;
        private int $dentaire;
        private int $optique;
        private int $maternite;
    }
?>