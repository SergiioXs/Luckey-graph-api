<?php

// Import configs
require 'Slim/config.php';
 
// My functions 
require 'Slim/LDS.php';

$app->group('/luckey', function() use($db,$app){

	// Default route  
	$app->get('/', function() use($db){
	    echo "Welcome to Luckey API!."; 
	});

	// Routes
	require 'Slim/routes/user.php';
	require 'Slim/routes/business.php';
	require 'Slim/routes/credit_card.php';
	require 'Slim/routes/service.php';
	require 'Slim/routes/done_service.php';
	// Routes for tiradoApp
	require 'Slim/routes/app.php';
});

// Run application
$app->run();
?>

