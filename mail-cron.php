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
$hash = $_GET['hash'];
$eqDb->where('hash', $hash);
$check = $eqDb->get('mail');
//var_dump($check);
if(count($check)) { 
    $eqDb->where('hash', $hash);
    $update = $eqDb->update('mail',[
       'read' => 1,
       'server' => $_SERVER['HTTP_HOST']
    ]);
//var_dump(json_stringify($_SERVER));	
}
