<?php declare(strict_types=1);
namespace Transfashion\Synctbsales;

use AgungDhewe\PhpSqlUtil\SqlSelect;
use AgungDhewe\PhpLogger\Log;


final class Bon {

	private static \PDOStatement $stmt_getheader;
	private static \PDOStatement $stmt_getitems;
	private static \PDOStatement $stmt_getpayments;
	private static \PDOStatement $stmt_getreport;
	

	public static final function GetHeader(string $bon_id) : array {
		$conn = Database::GetConnection(Database::DB_MAIN);

		try {
			
			if (!isset(self::$stmt_getheader)) {
				$query = self::sqlGetHeader();
				self::$stmt_getheader = $conn->prepare($query);
			}
			self::$stmt_getheader->execute([':bon_id'=>$bon_id]);
			$row = self::$stmt_getheader->fetch();
			foreach ($row as $columnname => $value) {
				// echo "$columnname,\n";
			}
			// die();
			return $row;
		} catch (\Exception $ex) {
			throw $ex;
		}


		
	}

	public static final function GetItems(string $bon_id) : array {
		$conn = Database::GetConnection(Database::DB_MAIN);

		try {
			if (!isset(self::$stmt_getitems)) {
				$query = self::sqlGetItems();
				self::$stmt_getitems = $conn->prepare($query);
			}
			self::$stmt_getitems->execute([':bon_id'=>$bon_id]);
			$rows = self::$stmt_getitems->fetchall();
			foreach ($rows as $row) {
				foreach ($row as $columnname => $value) {
					// echo "$columnname,\n";
				}
			}
			// die();
			return $rows;
		} catch (\Exception $ex) {
			throw $ex;
		}
	}

	public static final function GetPayments(string $bon_id) : array {
		$conn = Database::GetConnection(Database::DB_MAIN);

		try {
			if (!isset(self::$stmt_getpayments)) {
				$query = self::sqlGetPayment();
				self::$stmt_getpayments = $conn->prepare($query);
			}
			self::$stmt_getpayments->execute([':bon_id'=>$bon_id]);
			$rows = self::$stmt_getpayments->fetchall();
			foreach ($rows as $row) {
				foreach ($row as $columnname => $value) {
					// echo "$columnname,\n";
				}
			}
			// die();
			return $rows;
		} catch (\Exception $ex) {
			throw $ex;
		}
	}


	public static final function GetReport(string $bon_id) : array {
		$conn = Database::GetConnection(Database::DB_MAIN);

		try {
			if (!isset(self::$stmt_getreport)) {
				$query = "EXEC SyncGetHeposDetil :bon_id";
				self::$stmt_getreport = $conn->prepare($query);
			}
			self::$stmt_getreport->execute([':bon_id'=>$bon_id]);
			$rows = self::$stmt_getreport->fetchall();
			foreach ($rows as $row) {
				foreach ($row as $columnname => $value) {
					if (in_array($columnname, ['heinv_lastrvdate', 'heinv_lasttrdate', 'payments', 'report'])) {
						if ($value=='') {
							$row[$columnname] = null;
						}
					}
				}
			}
			return $rows;
		} catch (\Exception $ex) {
			throw $ex;
		}
	}



	public static final function sqlGetHeader() : string {
		return "
			select 
				bon_id,
				bon_idext,
				bon_event,
				bon_date,
				bon_createby,
				bon_createdate,
				bon_modifyby,
				bon_modifydate,
				bon_isvoid,
				bon_voidby,
				bon_voiddate,
				bon_replacefromvoid,
				bon_msubtotal,
				bon_msubtvoucher,
				bon_msubtdiscadd,
				bon_msubtredeem,
				bon_msubtracttotal,
				bon_msubtotaltobedisc,
				bon_mdiscpaympercent,
				bon_mdiscpayment,
				bon_mtotal,
				bon_mpayment,
				bon_mrefund,
				bon_msalegross,
				bon_msaletax,
				bon_msalenet,
				bon_itemqty,
				bon_rowitem,
				bon_rowpayment,
				bon_npwp,
				bon_fakturpajak,
				bon_adddisc_authusername,
				bon_disctype,
				customer_id,
				customer_name,
				customer_telp,
				customer_npwp,
				customer_ageid,
				customer_agename,
				customer_genderid,
				customer_gendername,
				customer_nationalityid,
				customer_nationalityname,
				customer_typename,
				customer_passport,
				customer_disc,
				voucher01_id,
				voucher01_name,
				voucher01_codenum,
				voucher01_method,
				voucher01_type,
				voucher01_discp,
				salesperson_id,
				salesperson_name,
				pospayment_id,
				pospayment_name,
				posedc_id,
				posedc_name,
				machine_id,
				region_id,
				branch_id,
				syncode,
				syndate,
				rowid,
				site_id_from
			from transaksi_hepos where bon_id = :bon_id
		";
	}

	public static final function sqlGetItems() : string {
		return "
			select 
				bon_id,
				bondetil_line,
				bondetil_gro,
				bondetil_ctg,
				bondetil_art,
				bondetil_mat,
				bondetil_col,
				bondetil_size,
				bondetil_descr,
				bondetil_qty,
				bondetil_mpriceori,
				bondetil_mpricegross,
				bondetil_mdiscpstd01,
				bondetil_mdiscrstd01,
				bondetil_mpricenettstd01,
				bondetil_mdiscpvou01,
				bondetil_mdiscrvou01,
				bondetil_mpricecettvou01,
				bondetil_vou01id,
				bondetil_vou01codenum,
				bondetil_vou01type,
				bondetil_vou01method,
				bondetil_vou01discp,
				bondetil_mpricenett,
				bondetil_msubtotal,
				bondetil_rule,
				heinv_id,
				heinvitem_id,
				heinvitem_barcode,
				region_id,
				region_nameshort,
				colname,
				sizetag,
				[proc],
				bon_idext,
				pricing_id,
				rowid,
				season_id
			from transaksi_heposdetil where bon_id = :bon_id
		";
	}

	public static final function sqlGetPayment() : string {
		return "
			select 
				bon_id,
				payment_line,
				payment_cardnumber,
				payment_cardholder,
				payment_mvalue,
				payment_mcash,
				payment_installment,
				pospayment_id,
				pospayment_name,
				pospayment_bank,
				posedc_id,
				posedc_name,
				posedc_approval,
				bon_idext,
				rowid
			from transaksi_hepospayment where bon_id = :bon_id
		";
	}
	

}