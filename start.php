<?php declare(strict_types=1);
require_once join(DIRECTORY_SEPARATOR, [__DIR__, 'vendor', 'autoload.php']);

use AgungDhewe\Cli\color;
use AgungDhewe\Setingan\Config;


use Transfashion\Synctbsales\Database;
use Transfashion\Synctbsales\Main;

// baca file debug

try {
	Config::SetRootDir(__DIR__);

	$options = getopt("", ["config:"]);

	$configFileName = "config-production.php";
	if (isset($options['config'])) {
		$configFileName = $options['config'];
	}

	$configPath = join(DIRECTORY_SEPARATOR, [__DIR__, $configFileName]);
	if (!is_file($configPath)) {
		throw new \Exception("File config '$configPath' not found");
	}
	require_once $configPath;
	

	Database::Connect();
	Main::run();
} catch (\Exception $ex) {
	echo color::FG_RED . "ERROR" . color::RESET . "\n";
	echo $ex->getMessage() . "\n";
} finally {
	echo "\n\n";
}