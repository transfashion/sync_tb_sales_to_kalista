<?php declare(strict_types=1);
namespace Transfashion\Synctbsales;

final class Kalista {
	public static final function SendData(string $data) : array {
		try {

			$endpoint = "https://kalista.localhost/api/Transfashion/KalistaApi/TransbrowserSales/SyncSales/Sync";

			$AppId = "transfashionid";
			$AppSecret = "n3k4n2fdmf3fse";
			$txid = uniqid();
			$datetime = new \DateTime("now", new \DateTimeZone("UTC"));
	
	
			$param = [
				"txid" => $txid,
				"timestamp" => $datetime->format("Y-m-d\TH:i:s\Z"),
				"request" => [
					"data" => $data,    
				]
			];
			
	
			// Mengonversi data menjadi JSON
			$jsonData = json_encode($param);
	
			// Buat Code Verifier
			$codeVerifier = hash_hmac('sha256', join(":", [$AppId, $jsonData]), $AppSecret);
	
			// Inisialisasi cURL
			$ch = curl_init($endpoint);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Menerima output sebagai string
			// curl_setopt($ch, CURLOPT_HEADER, true);         // Sertakan header dalam output
			curl_setopt($ch, CURLOPT_NOBODY, false);        // Tetap sertakan body (ubah ke true jika hanya butuh header)
			curl_setopt($ch, CURLOPT_POST, true); // Menggunakan metode POST
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Content-Type: application/json", // Header untuk JSON
				"App-Id: $AppId",
				"App-Secret: $AppSecret",	
				"Code-Verifier: $codeVerifier",
				"Content-Length: " . strlen($jsonData)
			]);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Data yang dikirim
	
			// Eksekusi cURL dan ambil responsnya
			$response = curl_exec($ch);
			curl_close($ch);	
	
	
			// Cek apakah ada kesalahan
			if (curl_errno($ch)) {
				throw new \Exception(curl_error($ch));
			} else {
				$res = json_decode($response, true);
				if (json_last_error() !== JSON_ERROR_NONE) {
					throw new \Exception(json_last_error_msg());
				}

				$code = array_key_exists("code", $res) ? $res["code"] : null;
				if ($code!=0) {
					$message = array_key_exists("message", $res) ? $res["message"] : "Error Code: $code from kalista";
					throw new \Exception($message);
				}

				$response = array_key_exists("response", $res) ? $res["response"] : null;
				if (!is_array($response)) {
					throw new \Exception("Invalid response from kalista");
				}

				$success = array_key_exists("success", $response) ? $response["success"] : null;
				$message = array_key_exists("message", $response) ? $response["message"] : "error on sending data";
				
				return [
					'success' => $success,
					'message' => $message
				];

			}
		} catch (\Exception $ex) {
			throw $ex;
		}
	}




}