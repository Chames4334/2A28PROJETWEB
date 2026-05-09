<?php

    class AssuranceType{
        private string $Titre;
        private string $Description;
        private $Image;
        private DateTime $Published_AT;

        public function getTitre(){return $this->Titre;}
        public function getDescription(){return $this->Description;}
        public function getImage(){return $this->Image;}
        public function getPublished_AT(){return $this->Published_AT;}

        public function setTitre(string $Titre){$this->Titre=$Titre;}
        public function setDescription(string $Description){$this->Description=$Description;}
        public function setPublished_AT(DateTime $Published_AT){$this->Published_AT=$Published_AT;}
        public function setImage($Image){$this->Image=$Image;}

        public function __construct(string $Titre,string $Description,$Image = "../View/Backoffice/images/default.png",DateTime $Published_AT){
            $this->Titre=$Titre;
            $this->Description=$Description;
            $this->Image = $Image;
            $this->Published_AT=$Published_AT;
        }
    }
?>
