<?php
//shebang workaround
ob_start();
require __DIR__ . '/lessnichy.phar';
ob_end_clean(); // production include
//require_once __DIR__ . "/../../vendor/autoload.php"; //debug instead of phar inclusion
// require php on dev mode or phar on production
LESS::listen();
