<?php
error_reporting(E_ALL);ini_set('display_errors',1);
$filename = __DIR__ . '/../' . $argv[1];
file_put_contents($filename, preg_replace('/^#!\s*/mi', '', file_get_contents($filename), 1));