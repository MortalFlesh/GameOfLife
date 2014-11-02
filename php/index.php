<!DOCTYPE html>
<html>
	<head>
		<title>Game of Life</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="/php/css/style.css" />
	</head>
	<body>
		<?php
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		require_once __DIR__ . '/vendor/autoload.php';

		// Nette RobotLoader autoloading
		$loader = new Nette\Loaders\RobotLoader;
		$loader->ignoreDirs .= 'vendor/';
		$loader->addDirectory(__DIR__ . '/libs/');
		$loader->setCacheStorage(new Nette\Caching\Storages\FileStorage(__DIR__ . '/cache'));
		$loader->register();

		$program = new Program();

		if (isset($_GET['test']) && $_GET['test'] = 1) {
			$program->test();
		} else {
			$program->main();
		}
		?>
	</body>
</html>
