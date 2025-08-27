<?php

error_reporting(-1);
ini_set('display_errors', 'on');
ini_set('max_execution_time', 0);

define('ROOT', realpath('../../..'));
define('INCLUDES', realpath(ROOT.'/includes').'/');
define('VENDORS', realpath(ROOT.'/vendor').'/');

include INCLUDES.'Autoloader.php';

$loader = new \Autoloader;
$loader->register( );
$loader->addNamespace('DLX', VENDORS.'DLX/src');
$loader->addNamespace('DLX\Puzzles', VENDORS.'DLX/puzzles');
