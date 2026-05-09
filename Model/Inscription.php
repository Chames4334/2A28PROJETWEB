<?php
    class Inscription{
        private DateTime $Date_souscription;
        private DateTime $Date_expiration;
        private string $Payment_status;
        private string $Payment_method;
        private int $Montant_paye;
        private DateTime $Date_creation;
        private string $Choix;

        public function getSouscription(){return $this->Date_souscription;}
        public function getExpiration(){return $this->Date_expiration;}
        public function getStatus(){return $this->Payment_status;}
        public function getMethod(){return $this->Payment_method;}
        public function getMontant(){return $this->Montant_paye;}
        public function getCreation(){return $this->Date_creation;}
        public function getChoix(){return $this->Choix;}

        public function setSouscription(DateTime $Date_souscription){$this->Date_souscription=$Date_souscription;}
        public function setExpiration(DateTime $Date_expiration){$this->Date_expiration=$Date_expiration;}
        public function setStatus(string $Payment_status){$this->Payment_status=$Payment_status;}
        public function setMethod(string $Payment_method){$this->Payment_method=$Payment_method;}
        public function setMontant(int $Montant_paye){$this->Montant_paye=$Montant_paye;}
        public function setCreation(DateTime $Date_creation){$this->Date_creation=$Date_creation;}
        public function setChoix(string $Choix){$this->Choix=$Choix;}

        public function __construct(DateTime $Date_souscription,DateTime $Date_expiration, string $Payment_status, string $Payment_method, int $Montant_paye, DateTime $Date_creation, string $Choix){
            $this->Date_souscription=$Date_souscription;
            $this->Date_expiration=$Date_expiration;
            $this->Payment_status=$Payment_status;
            $this->Payment_method=$Payment_method;
            $this->Montant_paye=$Montant_paye;
            $this->Date_creation=$Date_creation;
            $this->Choix=$Choix;
        }
    }
    class carte extends Inscription{
        private int $Cart_number;
        private string $Billing_address;
        private int $PostalCode;
        private string $Region;
        //private int $Phone;

        public function getNumbr(){return $this->Cart_number;}
        public function getAddrs(){return $this->Billing_address;}
        public function getPostalCode(){return $this->PostalCode;}
        public function getRegion(){return $this->Region;}
        //public function getPhone(){return $this->Phone}

        public function setNumbr(int $Cart_number){$this->Cart_number=$Cart_number;}
        public function setAddrs(string $Billing_address){$this->Billing_address=$Billing_address;}
        public function setPostalCode(int $PostalCode){$this->PostalCode=$PostalCode;}
        //public function setPhone(int $Phone){$this->Phone=$Phone;}

        public function __construct(int $Cart_number, string $Billing_address, int $PostalCode, string $Region /*int $Phone*/){
            $this->Cart_number=$Cart_number;
            $this->Billing_address=$Billing_address;
            $this->PostalCode=$PostalCode;
            $this->Region=$Region;
            //$this->Phone=$Phone;
        }
    }
?>