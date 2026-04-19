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

        public function __destruct(){}
    }
?>