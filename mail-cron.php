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

new Database($config);

$eq = $eqDb;
$eqDb->where('hash', $hash);
$check = $eqDb->get('mail');
if(count($check)) { 
    $eqDb->where('hash', $hash);
    $update = $eqDb->update('mail',[
        'read' => 1
    ]);
}