<?php 

	header("Access-Control-Allow-Origin: *");

	ini_set('display_errors', 0);     # don't show any errors...
	error_reporting(E_ALL ^ E_STRICT);  # ...but do log them

	date_default_timezone_set('america/los_angeles');

	session_start();

	require('libs/functions.php');
	require('libs/config.php');
	require('libs/boot.php');

	require('libs/database.php');
	require('libs/view.php');
	require('libs/controller.php');
	require('libs/navigation.php');
	require('libs/model.php');

	// Load all helpers!
	$helpers = glob("libs/helpers/*.php");
	foreach($helpers as $file) {
		require_once($file);
	}
	// Boot it up!
	$app = new Boot();
?>