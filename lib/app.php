<?php
// bootstrap app
use Slim\Slim;

$app = new Slim();
//todo add auth middleware
//todo add common Lessnichy\Server injection

$app->get(
    '/',
    function () {
        print "<h1>Youddle!</h1>";
    }
);
// CDN
$app->get(
    '/css/:file',
    function ($file) use ($app) {
        Slim::getInstance()
            ->response->header('content-type', 'text/css');
        print file_get_contents(__DIR__ . '/css/' . $file);
    }
);
// save less rendered
$app->post(
    '/css/',
    function () use ($app) {
        $sheets  = $app->request->post('sheets', array());
        $results = array();
        foreach ($sheets as $lessStylesheetUrl => $cssContent) {
            if (\Lessnichy\Client::isCss($lessStylesheetUrl)) {
                continue;
            }
            $parts = parse_url($lessStylesheetUrl);
            $cssStylesheetFilename = $_SERVER['DOCUMENT_ROOT'] . $parts['path'] . '.css';
            file_put_contents($cssStylesheetFilename, $cssContent);
            $results[ $lessStylesheetUrl ] = $cssStylesheetFilename;
        }

        //todo minify
        $app->response->header('content-type', 'application/json');
        print json_encode($results);
    }
);
$app->get(
    '/js/:file',
    function ($file) use ($app) {
        //todo glue and gzip lessnichy.js, clean-css.js, less***.js
        Slim::getInstance()
            ->response->header('content-type', 'text/javascript');
        print file_get_contents(__DIR__ . '/js/' . $file);
    }
);


// aaaaand action
$app->run();