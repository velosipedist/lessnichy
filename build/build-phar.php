<?php
require_once __DIR__ . '/../vendor/autoload.php';

$brg = new Burgomaster(__DIR__ . '/phar', __DIR__ . '/..');

// Composer autoload
$brg->recursiveCopy('vendor/composer', 'vendor/composer');
$brg->deepCopy('vendor/autoload.php', 'vendor/autoload.php');


// Build library
$brg->recursiveCopy('lib', 'lib', array('php','js','css'));

// Dependencies
$brg->recursiveCopy('vendor/mtdowling/burgomaster/src', 'vendor/mtdowling/burgomaster/src');
$brg->recursiveCopy('vendor/slim/slim/Slim', 'vendor/slim/slim/Slim');

$brg->createPhar('build/lessnichy.phar', null, 'vendor/autoload.php');