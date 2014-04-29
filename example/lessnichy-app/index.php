<?php
//require_once __DIR__ . "/../../vendor/autoload.php"; //debug instead of phar inclusion
//shebang workaround
ob_start();
require __DIR__.'/lessnichy.phar'; // production include
ob_end_clean();
// require php on dev mode or phar on production
LESS::listen();
