<?php
require __DIR__."/vendor/autoload.php";

// bootstrap
$app = new \Slim\Slim();

//todo add auth middleware

// index page
$app->get('/', function(){
	print "<h1>Hello</h1>";
	//todo login
});

// CDN
$app->get('/css/:file', function($file){
	print file_get_contents(__DIR__.'/css/'.$file);
});
$app->get('/js/:file', function($file){
	print file_get_contents(__DIR__.'/js/'.$file);
});

// save less rendered
$app->put('/css/:file', function($file) use ($app){
	//todo minify
	file_put_contents($file, $app->request->put('contents'));
});

// aaaaand action
$app->run();