<?php
    include "C:/xampp/htdocs/GreenSecure/Model/Variables.php";
    class AssuranceType{
        private string $Titre;
        private string $Description;
        private $Image;
        private DateTime $Published_AT;
        //private array $variables;

        public function getTitre(){return $this->Titre;}
        public function getDescription(){return $this->Description;}
        public function getImage(){return $this->Image;}
        public function getPublished_AT(){return $this->Published_AT;}
        //public function getVariables(){return $this->variables;}

        public function setTitre(string $Titre){$this->Titre=$Titre;}
        public function setDescription(string $Description){$this->Description=$Description;}
        public function setPublished_AT(DateTime $Published_AT){$this->Published_AT=$Published_AT;}
        public function setImage($Image){$this->Image=$Image;}
        //public function setVariables(array $variables){$this->variables=$variables;}

        public function __construct(string $Titre,string $Description,$Image = "../View/Backoffice/images/default.png",DateTime $Published_AT,/*array $variables=[]*/){
            $this->Titre=$Titre;
            $this->Description=$Description;
            $this->Image = $Image;
            $this->Published_AT=$Published_AT;
            //$this->variables=$variables;
        }
        /*public function addVariable(Variable $variable){
            $this->variables[] = $variable;
        }
        public function removeVariable(int $index){
            array_splice($this->variables, $index, 1);
        }
        public function validateData(array $postData): array {
            $clean = [];
            foreach ($this->variables as $var) {
                $value         = $postData[$var->getName()] ?? '';
                $clean[$var->getName()] = $var->validate($value);
            }
            return $clean;
        }
        public function loadVariablesFromPost(){
            $names = $_POST['var_name'] ?? [];
            $types = $_POST['var_type'] ?? [];
    
            $this->variables = [];
            foreach ($names as $i => $name) {
                $name = trim($name);
                $type = $types[$i] ?? 'VARCHAR';
                if ($name !== '') {
                    $this->variables[] = new Variable($name, $type);
                }
            }
        }
        public function loadVariablesFromJson(string $json){
            $data = json_decode($json, true) ?? [];
            $this->variables = array_map(
                fn($v) => Variable::fromArray($v),
                $data
            );
        }
        public function validateSubmission(array $postData): array {
            $clean  = [];
            $errors = [];

            foreach ($this->variables as $var) {
                $raw    = $postData[$var->getName()] ?? '';
                $result = $var->validate($raw);

                if (isset($result['error'])) {
                    $errors[] = $result['error'];
                } else {
                    $clean[$var->getName()] = $result['value'];
                }
            }

            return empty($errors)
                ? ['data'   => $clean]
                : ['errors' => $errors];
        }
        public function variablesToJson(){
            return json_encode(array_map(fn(Variable $v) => $v->toArray(), $this->variables));
        }*/
    }
?>