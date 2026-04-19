<?php
/*class Variable {
    private string $name;
    private string $type;

    public function getName(){return $this->name;}
    public function getType(){return $this->type;}

    public function __construct(string $name, string $type) {
        $this->name = $name;
        $this->type = $type;
    }
    public function validate($value) {
        if ($value === '' || $value === null) {
            die("Le champ '{$this->name}' est obligatoire.");
        }
        if ($this->type === 'INT') {
            return $this->validateInt($value);
        } elseif ($this->type === 'DATE') {
            return $this->validateDate($value);
        } else {
            return $this->validateVarchar($value);
        }
    }
    private function validateInt($v) {
        if (!filter_var($v, FILTER_VALIDATE_INT)) {
            die("Le champ '{$this->name}' doit être un nombre entier.");
        }
        return (int) $v;
    }

    private function validateDate($v) {
        $d = DateTime::createFromFormat('Y-m-d', $v);
        if (!$d || $d->format('Y-m-d') !== $v) {
            die("Le champ '{$this->name}' doit être une date valide (YYYY-MM-DD).");
        }
        return $v;
    }

    private function validateVarchar($v) {
        $clean = strip_tags(trim($v));
        if (strlen($clean) > 255) {
            die("Le champ '{$this->name}' ne doit pas dépasser 255 caractères.");
        }
        return $clean;
    }
    public function toArray(){
        return ['name' => $this->name, 'type' => $this->type];
    }
    
    public static function fromArray(array $data){
        return new self($data['name'], $data['type']);
    }
}*/