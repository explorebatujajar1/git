<?php

class _persib_ {

	private $END_POINT = "http://api.persib.co.id";

	public function sendRequest($url, $param, $headers, $request = 'POST') 
	{
		$ch = curl_init();
		$data = array(
			CURLOPT_URL				=> $url,
			CURLOPT_POSTFIELDS		=> $param,
			CURLOPT_HTTPHEADER 		=> $headers,
			CURLOPT_CUSTOMREQUEST 	=> $request,
			CURLOPT_HEADER 			=> true,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_FOLLOWLOCATION 	=> true,
			CURLOPT_SSL_VERIFYPEER	=> false
		);
		curl_setopt_array($ch, $data);
		$execute = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($execute, 0, $header_size);
		$body = substr($execute, $header_size);
		curl_close($ch);
		return $body;
	}


	public function alarmRestock()
	{
		$data = array(
			"url" 		=> $this->END_POINT."/api/v1/rewards?page=&per_page=10000&access_token=f82c573234a120b068603a5111f03ebc5d0e4e8a416b463b0f36986ebd72666b",
			"post"		=> null,
			"headers" 	=> explode("\n", "Host: api.persib.co.id"),
			"request"	=> "GET"
		);

		$request = $this->sendRequest($data["url"], $data["post"], $data["headers"], $data["request"]);
		$object = json_decode($request, true);

		foreach($object["data"] as $key => $TmpData)
		{
			$nama = $TmpData["name"];
			$hargaPoint = $TmpData["point_needed"];
			$limit = $TmpData["claim_limit"];

			if ($limit != 0)
			{
				print PHP_EOL."[".date("Y-m-d H:i:s")."] Voucher Ready : ".$nama." - ".$hargaPoint." Points - Limit : ".$limit;
				$text = "[".date("Y-m-d H:i:s")."] Voucher Ready : ".$nama." - ".$hargaPoint." Points - Limit : ".$limit;
				file_get_contents("https://api.telegram.org/bot857764314:AAECM2jj1q1aLJR9CQdPQuLmsnPJ-En7wxY/sendMessage?chat_id=729254758&text=".$text);

				for ($as = 1; $as <= 1; $as++)
				{
					$id = $TmpData["id"];
					$data = array(
						"url" 		=> $this->END_POINT."/api/v1/rewards/".$id."/claim?access_token=f82c573234a120b068603a5111f03ebc5d0e4e8a416b463b0f36986ebd72666b",
						"post"		=> null,
						"headers" 	=> explode("\n", "Host: api.persib.co.id"),
						"request"	=> "POST"
					);

					$requests = $this->sendRequest($data["url"], $data["post"], $data["headers"], $data["request"]);
					if(strpos($requests, '"status":"ok"'))
					{
						print PHP_EOL."[".date("Y-m-d H:i:s")."] Sukses Redeem : ".$nama." - ".$hargaPoint." Points - Limit : ".$limit." - ".$as."x";
						$text = "[".date("Y-m-d H:i:s")."] Sukses Redeem diga : ".$nama." - ".$hargaPoint." Points - Limit : ".$limit." - ".$as."x";
						file_get_contents("https://api.telegram.org/bot857764314:AAECM2jj1q1aLJR9CQdPQuLmsnPJ-En7wxY/sendMessage?chat_id=729254758&text=".$text);
					} else {
						print PHP_EOL."[".date("Y-m-d H:i:s")."] Gagal Redeem : ".$nama." - ".json_decode($requests, true)["error"]." - ".$as."x";
						$text = "[".date("Y-m-d H:i:s")."] Gagal Redeem diga : ".$nama." - ".json_decode($requests, true)["error"]." - ".$as."x";
						file_get_contents("https://api.telegram.org/bot857764314:AAECM2jj1q1aLJR9CQdPQuLmsnPJ-En7wxY/sendMessage?chat_id=729254758&text=".$text);
						continue;
					}
				}	
			} else {
				print PHP_EOL."[".date("Y-m-d H:i:s")."] Voucher Sold : ".$nama;
			}
		}
		sleep(1);
		print PHP_EOL;
	}
}

$persib = new _persib_();

while(true)
{
	print $persib->alarmRestock();
}

?>