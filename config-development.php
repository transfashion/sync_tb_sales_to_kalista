<?php 


use AgungDhewe\Setingan\Config;
use AgungDhewe\PhpLogger\Logger;
use AgungDhewe\PhpLogger\LoggerOutput;
use Transfashion\Synctbsales\Database;

Config::Setup([

	'FRM2_DB' => [
		'DSN' => 'dblib:host=172.18.10.254;dbname=E_FRM2_BACKUP',
		'user' => 'sa',
		'pass' => 'meg@tower2018'	
	]

]);

Config::UseConfig([
	Database::DB_MAIN => 'FRM2_DB',
]);

Logger::SetDebugMode(true);
Logger::SetOutput(LoggerOutput::FILE);
