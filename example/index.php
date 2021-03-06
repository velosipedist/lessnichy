<?php
/*if (php_sapi_name() == "cli-server") {
    if (strpos(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/lessnichy-app') === 0) {
        $_SERVER["REQUEST_URI"] = str_replace('/lessnichy-app/', '/', $_SERVER["REQUEST_URI"]);
        require_once __DIR__ . "/lessnichy-app/index.php";
        return;
    }
}*/
/**
 * This example page is combined entry script and main php template
 * */
//require_once __DIR__ . "/../vendor/autoload.php"; //debug instead of phar inclusion
require_once __DIR__ . "/../build/lessnichy.phar"; // production include

// add() can be used in any sub-template before main layout rendering
LESS::connect('/lessnichy-app', true)->add(
    array(
        '/less/foo.less'
    )
);

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lessnichy example page</title>
    <!-- Here will be LESS scripts and sources -->
    <?
    LESS::head(
        array(
            LESS::WATCH => true,
            LESS::WATCH_INTERVAL => 5000,
        )
    );
    ?>
    <!-- End of LESS scripts and sources -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.0.min.js"></script>
</head>
<body>
<div class="foo">
    <div class="bar">.foo > .bar text</div>
</div>
</body>
</html>
<!--There other js will be appended-->