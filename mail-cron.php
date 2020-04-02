<?php
global $eqDb;

require_once('libs/vendor/autoload.php');

require 'libs/database.php';
$config = [
    'db_host' => 'localhost',
    'db_name' => 'uniques',
    'db_user' => 'root',
    'db_pass' => 'Mothugs123!',
];

var_dump($eqDb);
