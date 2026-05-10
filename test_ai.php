<?php 
$_GET['id_employe'] = 1;
require 'config/database.php'; 
require 'controllers/AIController.php'; 
$c = new AIController(); 
$c->getSmartSuggestions();