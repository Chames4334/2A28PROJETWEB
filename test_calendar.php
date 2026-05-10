<?php
$_GET['month'] = '05';
$_GET['year'] = '2026';
require 'config/database.php';
require 'controllers/AIController.php';

$c = new AIController();
$c->analyzeCalendar();
