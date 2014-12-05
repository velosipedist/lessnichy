<?php
require __DIR__ . '/../../build/lessnichy.phar'; // production include
//require_once __DIR__ . "/../../vendor/autoload.php"; //debug instead of phar inclusion
// require php on dev mode or phar on production
LESS::listen();
