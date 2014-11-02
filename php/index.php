<?php
require_once __DIR__ . '/vendor/autoload.php';

// Nette RobotLoader autoloading
$loader = new Nette\Loaders\RobotLoader;
$loader->ignoreDirs .= 'vendor/';
$loader->addDirectory(__DIR__ . '/libs/');
$loader->setCacheStorage(new Nette\Caching\Storages\FileStorage(__DIR__ . '/cache'));
$loader->register();

$program = new Program();
$program->main();
