<?php declare(strict_types=1);
namespace Transfashion\Synctbsales;

use AgungDhewe\PhpLogger\Log;

final class Main {
	const int MAX_ITTER = 0;


	public static final function run():void {
		$conn = Database::GetConnection(Database::DB_MAIN);

		try {

			SalesQueue::RemoveCompleted();
			SalesQueue::ResetProcessing();

			$i=0;
			$queue=SalesQueue::GetPending();
			$n = count($queue);
			Log::info("sending $n data");
			while (count($queue) > 0) {
				$i++;
				if (self::MAX_ITTER>0 && $i>self::MAX_ITTER) {
					echo "Maximum itteration: $i";
					break; // berhenti jika melebihi batas, nanti bisa diexekusi lagi
				}

				$conn->beginTransaction();
				try {

					$processed = [];
					foreach ($queue as $row) {
						$bon_id = self::ProcessData($row);
						
						$processed[] = $bon_id; // tambahkan kode bon id dalam data processed
						SalesQueue::MarkProcessing($bon_id);
					}

					SalesQueue::MarkCompleted($processed);
					SalesQueue::RemoveCompleted();
					$conn->commit();
				} catch (\Exception $ex) {
					$conn->rollBack();
					throw $ex;
				}

				$queue=SalesQueue::GetPending(); // ambil lagi yang masih pending untuk kembali di proses dari atas
			}
		} catch (\Exception $ex) {
			throw $ex;
		}

	}

	public static final function ProcessData(array $row) : string {
		$bon_id = $row['bon_id'];
		echo "$bon_id\n";

		try {
	
			$header = Bon::GetHeader($bon_id);
			$items = Bon::GetItems($bon_id);
			$payments = Bon::GetPayments($bon_id);
			$report = Bon::GetReport($bon_id);			

			$data = [
				'header' => $header,
				'items' => $items,
				'payments' => $payments,
				'report' => $report
			];


			$jsondata = json_encode($data);
			$compressed_jsondata = gzcompress($jsondata);

			// kirim ke kalista
			$base64data = base64_encode($compressed_jsondata);
			$res = Kalista::SendData($base64data);
			if (!$res['success']) {
				$errmsg = Log::error($res['message']);
				throw new \Exception($errmsg);
			}

			return $bon_id;
		} catch (\Exception $ex) {
			throw $ex;
		}
	}



}

