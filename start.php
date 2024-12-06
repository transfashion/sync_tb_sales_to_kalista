<?php declare(strict_types=1);
date_default_timezone_set('Asia/Jakarta');
require_once join(DIRECTORY_SEPARATOR, [__DIR__, 'vendor', 'autoload.php']);

use AgungDhewe\Cli\color;
use AgungDhewe\Setingan\Config;
use AgungDhewe\PhpLogger\Logger;


use Transfashion\Synctbsales\Database;
use Transfashion\Synctbsales\Main;

// kirim data sales transbrowser ke kalista
// Agung Nugroho <agung@transfashionindonesia.com>
// Created at 5 Desember 2024


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
	

	Logger::SetLogFilepath(join(DIRECTORY_SEPARATOR, [__DIR__,  'log.txt']));
	Logger::SetDebugFilepath(join(DIRECTORY_SEPARATOR, [__DIR__,  'debug.txt']));

	Database::Connect();
	Main::run();
} catch (\Exception $ex) {
	echo color::FG_RED . "ERROR" . color::RESET . "\n";
	echo $ex->getMessage() . "\n";
} finally {
	echo "\n\n";
}