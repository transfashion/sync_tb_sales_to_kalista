<?php declare(strict_types=1);
namespace Transfashion\Synctbsales;

use AgungDhewe\PhpLogger\Log;
use AgungDhewe\PhpSqlUtil\SqlUpdate;
use AgungDhewe\PhpSqlUtil\SqlDelete;

final class SalesQueue {

	const int MAX_ROW = 10;
	const string QUEUE_TABLE_NAME = "queue_hepos";

	private static \PDOStatement $stmt_getpending;
	private static \PDOStatement $stmt_reset;


	private static SqlUpdate $cmd_markprocessing;
	private static SqlUpdate $cmd_markcompleted;
	private static SqlDelete $cmd_removecompleted;
	

	public static final function ResetProcessing() : void {
		$conn = Database::GetConnection(Database::DB_MAIN);

		try {
			if (!isset(self::$stmt_reset)) {
				$query = "update ". self::QUEUE_TABLE_NAME ." set queue_isprocessing=0 where queue_iscomplete = 0";
				self::$stmt_reset = $conn->prepare($query);
			}

			self::$stmt_reset->execute();
		} catch (\Exception $ex) {
			Log::error($ex->getMessage());
			throw $ex;
		}
	}


	public static final function GetPending() : array {
		$conn = Database::GetConnection(Database::DB_MAIN);

		try {
			if (!isset(self::$stmt_getpending)) {
				$query = "select TOP 10 * from ". self::QUEUE_TABLE_NAME ." where queue_isprocessing = 0 and queue_iscomplete = 0 order by queue_timestamp asc";
				self::$stmt_getpending = $conn->prepare($query);
			}

			Log::info("get pending queue ...");
			self::$stmt_getpending->execute();
			$rows = self::$stmt_getpending->fetchall();
			$n = count($rows);
			Log::info("found $n rows.");

			return $rows;
		} catch (\Exception $ex) {
			Log::error($ex->getMessage());
			throw $ex;
		}
	}

	public static final function MarkProcessing(string  $bon_id) : void {
		$conn = Database::GetConnection(Database::DB_MAIN);

		try {
			$obj = new \stdClass;
			$obj->bon_id = $bon_id;
			$obj->queue_isprocessing = 1;
			if (!isset(self::$cmd_markprocessing)) {
				self::$cmd_markprocessing = new SqlUpdate(self::QUEUE_TABLE_NAME, $obj, ['bon_id']);
				self::$cmd_markprocessing->bind($conn);
			}
			self::$cmd_markprocessing->execute($obj);
		} catch (\Exception $ex) {
			throw $ex;
		}

	}



	public static final function MarkCompleted(array $processed) : void {
		$conn = Database::GetConnection(Database::DB_MAIN);

		try {
			foreach ($processed as $bon_id) {
				$obj = new \stdClass;
				$obj->bon_id = $bon_id;
				$obj->queue_iscomplete = 1;
				if (!isset(self::$cmd_markcompleted)) {
					self::$cmd_markcompleted = new SqlUpdate(self::QUEUE_TABLE_NAME, $obj, ['bon_id']);
					self::$cmd_markcompleted->bind($conn);
				}
				self::$cmd_markcompleted->execute($obj);
			}
		} catch (\Exception $ex) {
			throw $ex;
		}


	}
	
	
	public static final function RemoveCompleted() : void {
		$conn = Database::GetConnection(Database::DB_MAIN);

		try {
			$obj = new \stdClass;
			$obj->queue_iscomplete = 1;
			if (!isset(self::$cmd_removecompleted)) {
				self::$cmd_removecompleted = new SqlDelete(self::QUEUE_TABLE_NAME, $obj, ['queue_iscomplete']);
				self::$cmd_removecompleted->bind($conn);
			}
			self::$cmd_removecompleted->execute($obj);
		} catch (\Exception $ex) {
			throw $ex;
		}
	}	
	
}