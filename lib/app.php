<?php
// bootstrap app
use Slim\Slim;

$app = new Slim();
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
$app->post(
    '/css/',
    function () use ($app) {
        $sheets = $app->request->post('sheets', array());
        $results = array();
        foreach ($sheets as $lessStylesheetUrl => $cssContent) {
            $parts = parse_url($lessStylesheetUrl);

            $cssStylesheetFilename = $_SERVER['DOCUMENT_ROOT'] . $parts['path'] . '.css';
            file_put_contents($cssStylesheetFilename, $cssContent);
            $results[$lessStylesheetUrl] = $cssStylesheetFilename;
        }

        //todo minify
        print json_encode($results);
    }
);
$app->get(
    '/js/:file',
    function ($file) {
        //todo glue and gzip lessnichy.js, clean-css.js, less***.js
        print file_get_contents(__DIR__ . '/js/' . $file);
    }
);


// aaaaand action
$app->run();