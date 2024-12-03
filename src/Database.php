<?php declare(strict_types=1);
namespace Transfashion\Synctbsales;

use AgungDhewe\PhpLogger\Log;
use AgungDhewe\Setingan\Config;


final class Database {
	const string DB_MAIN = 'FRM2_DB';
	const array DB_PARAM = [
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
		\PDO::ATTR_PERSISTENT=>true
	];


	private static bool $_database_connected = false;
	private static array $_connections = [];

	public static function Connect() : void {
		try {
			if (!self::$_database_connected) {
				Log::info("Connecting to database...");

				$dbkeyname = Config::GetUsedConfig(self::DB_MAIN);
				$dbconf = Config::Get($dbkeyname);

				$DSN = $dbconf['DSN'];
				$user = $dbconf['user'];
				$pass = $dbconf['pass'];

				
				$db = new \PDO($DSN, $user, $pass, self::DB_PARAM);
				self::$_connections[self::DB_MAIN] = $db;


				self::$_database_connected = true;
				Log::info("Database Connected.");
			}
		} catch (\Exception $ex) {
			Log::error($ex->getMessage());
			throw new \Exception($ex->getMessage(), 500);
		}
	}


	public static function GetConnection(string $key) : \PDO {
		return self::$_connections[$key];
	}
}
