<?php
// bootstrap app
$app = new \Slim\Slim();
//todo add auth middleware
//todo add common Lessnichy\Server injection

$app->get(
    '/',
    function () {
        print "<h1>Hello</h1>";
        //todo login
    }
);
// CDN
$app->get(
    '/css/:file',
    function ($file) {
        print file_get_contents(__DIR__ . '/css/' . $file);
    }
);
// save less rendered
$app->put(
    '/css/:file',
    function ($file) use ($app) {
        //todo minify
        file_put_contents($file, $app->request->put('contents'));
    }
);
$app->get(
    '/js/:file',
    function ($file) {
        print file_get_contents(__DIR__ . '/js/' . $file);
    }
);


// aaaaand action
$app->run();