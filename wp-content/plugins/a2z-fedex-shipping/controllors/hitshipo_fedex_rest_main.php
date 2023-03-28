<?php

	class fedex_rest
	{
		public $mode = "test";
		public $live_rate_url = "https://apis.fedex.com/rate/v1/rates/quotes";
		public $test_rate_url = "https://apis-sandbox.fedex.com/rate/v1/rates/quotes";
		public $live_auth_url = "https://apis.fedex.com/oauth/token";
		public $test_auth_url = "https://apis-sandbox.fedex.com/oauth/token";
		private $general_settings = [];
		private $ven_data = [];
		private $dest_data = [];
		private $fedex_packs = [];
		private $order_total = 0;
		private $total_pack_count = 0;
		private $total_pack_weight = 0;
		private $weg_unit = "";
		private $dim_unit = "";
		public function gen_access_token($grant_type='', $api_key='', $api_secret='')
		{
			$request_url = ($this->mode == "test") ? $this->test_auth_url : $this->live_auth_url;
			$result = wp_remote_post(
				$request_url,
				array(
					'method' => 'POST',
					'timeout' => 70,
					'sslverify' => 0,
					'body' => build_query(array("grant_type" => $grant_type, "client_id" => $api_key, "client_secret" => $api_secret)),
					'headers' => array("content-type" => "application/x-www-form-urlencoded")
				)
			);
			if (isset($result['body']) && !empty($result['body'])) {
				$auth_data = json_decode($result['body']);
				return $auth_data->access_token;
			}
			return;
		}
		public function make_rate_req_rest($general_settings=[], $ven_data=[], $dest_data=[], $fedex_packs=[], $order_total = 0){
			//load all data to class
			$this->general_settings = $general_settings;
			$this->ven_data = $ven_data;
			$this->dest_data = $dest_data;
			$this->fedex_packs = $fedex_packs;
			$this->order_total = $order_total;
			//make req params
			$req = [];
			$req['accountNumber']['value'] = isset($this->ven_data['hitshippo_fedex_rest_acc_no']) ? $this->ven_data['hitshippo_fedex_rest_acc_no'] : '';
			$req['requestedShipment']['shipper']['address'] = $this->make_ship_info();
			$req['requestedShipment']['recipient']['address'] = $this->make_dest_info();
			$req['requestedShipment']['rateRequestType'] = $this->make_rate_req_types_info();
			$req['requestedShipment']['pickupType'] = isset($this->general_settings['hitshippo_fedex_pickup_type']) ? $this->general_settings['hitshippo_fedex_pickup_type'] : 'CONTACT_FEDEX_TO_SCHEDULE';
			$req['requestedShipment']['requestedPackageLineItems'] = $this->make_line_items_info();
			$req['requestedShipment']['edtRequestType'] = 'ALL';
			$req['requestedShipment']['packagingType'] = $this->make_pack_type_info();
			$req['requestedShipment']['totalPackageCount'] = $this->total_pack_count;
			$req['requestedShipment']['totalWeight'] = $this->total_pack_weight;
			$spl_services = $this->make_spl_ser_info();
			$customs = $this->make_customs_info();
			if (!empty($spl_services)) {
				$req['requestedShipment']['shipmentSpecialServices'] = $spl_services;
			}
			if (!empty($customs)) {
				$req['requestedShipment']['customsClearanceDetail'] = $customs;
			}
			return $req;
		}
		private function make_ship_info()
		{
			$residential = "false";
			if (isset($this->general_settings['hitshippo_fedex_res_f']) && $this->general_settings['hitshippo_fedex_res_f'] == 'yes') {
				$residential = "true";
			}
			return array(
				"streetLines" => [isset($this->ven_data['hitshippo_fedex_address1']) ? $this->ven_data['hitshippo_fedex_address1'] : "", isset($this->ven_data['hitshippo_fedex_address2']) ? $this->ven_data['hitshippo_fedex_address2'] : ""],
				"city" => isset($this->ven_data['hitshippo_fedex_city']) ? $this->ven_data['hitshippo_fedex_city'] : "",
				"stateOrProvinceCode" => isset($this->ven_data['hitshippo_fedex_state']) ? $this->ven_data['hitshippo_fedex_state'] : "",
				"postalCode" => isset($this->ven_data['hitshippo_fedex_zip']) ? $this->ven_data['hitshippo_fedex_zip'] : "",
				"countryCode" => isset($this->ven_data['hitshippo_fedex_country']) ? $this->ven_data['hitshippo_fedex_country'] : "",
				"residential" => $residential);
		}
		private function make_dest_info()
		{
			$residential = "false";
			if (isset($this->general_settings['hitshippo_fedex_res_f']) && $this->general_settings['hitshippo_fedex_res_f'] == 'yes') {
				$residential = "true";
			}
			return array(
				"streetLines" => [isset($this->dest_data['address_1']) ? $this->dest_data['address_1'] : "", isset($this->dest_data['address_2']) ? $this->dest_data['address_2'] : ""],
				"city" => isset($this->dest_data['city']) ? $this->dest_data['city'] : "",
				"stateOrProvinceCode" => (isset($this->dest_data['state']) && isset($this->dest_data['country'])) ? str_replace($this->dest_data['country']."-", "", $this->dest_data['state']) : "",
				"postalCode" => isset($this->dest_data['postcode']) ? $this->dest_data['postcode'] : "",
				"countryCode" => isset($this->dest_data['country']) ? $this->dest_data['country'] : "",
				"residential" => $residential);
		}
		private function make_rate_req_types_info()
		{
			return array("LIST", "ACCOUNT");
		}
		private function make_line_items_info()
		{
			$line_items = [];
			if (!empty($this->fedex_packs)) {
				foreach ($this->fedex_packs as $p_key => $pack) {
					$line_items[$p_key]["subPackagingType"] = "BOX";
					$line_items[$p_key]["groupPackageCount"] = ($p_key+1);
					$line_items[$p_key]["weight"] = array(
														"units" => (isset($pack['Weight']['Units']) && $pack['Weight']['Units'] == "LBS") ? "LB" : "KG",
														"value" => (isset($pack['Weight']['Value']) && !empty($pack['Weight']['Value'])) ? round($pack['Weight']['Value'], 2) : "0.5"
													);
					if (isset($pack['Dimensions'])) {
						$line_items[$p_key]["dimensions"] = array(
																"length" => (isset($pack['Dimensions']['Length']) && !empty($pack['Dimensions']['Length'])) ? $pack['Dimensions']['Length'] :" 0.5",
																"width" => (isset($pack['Dimensions']['Width']) && !empty($pack['Dimensions']['Width'])) ? $pack['Dimensions']['Width'] : "0.5",
																"height" => (isset($pack['Dimensions']['Height']) && !empty($pack['Dimensions']['Height'])) ? $pack['Dimensions']['Height'] : "0.5",
																"units" => (isset($pack['Dimensions']['Units']) && $pack['Dimensions']['Units'] == "IN") ? "IN" : "CM"
															);
					}
					$this->total_pack_count += 1;
					$this->total_pack_weight += (isset($pack['Weight']['Value']) && !empty($pack['Weight']['Value'])) ? round($pack['Weight']['Value'], 2) : "0.5";
					$this->weg_unit = (isset($pack['Weight']['Units']) && $pack['Weight']['Units'] == "LBS") ? "LB" : "KG";
					$this->dim_unit = (isset($pack['Dimensions']['Units']) && $pack['Dimensions']['Units'] == "IN") ? "IN" : "CM";
				}
			}
			return $line_items;
		}
		private function make_pack_type_info()
		{
			$pack_type = "YOUR_PACKAGING";
			if ($this->ven_data['hitshippo_fedex_country'] == $this->dest_data['country']) {
				if (isset($this->general_settings['hitshippo_fedex_one_rates']) && $this->general_settings['hitshippo_fedex_one_rates'] == "yes") {
					if ($this->weg_unit == "KG") {
						$nor_weight = $this->total_pack_weight * 2.205;
					} else {
						$nor_weight = $this->total_pack_weight;
					}
					if ($nor_weight > 10 && $nor_weight <= 50) {
						$pack_type = 'FEDEX_MEDIUM_BOX';
					} else {
						$pack_type = 'FEDEX_SMALL_BOX';
					}
				}
			}
			if (isset($this->general_settings['hitshippo_fedex_send_pack_as_ship']) && $this->general_settings['hitshippo_fedex_send_pack_as_ship'] == "yes") {
				if (isset($this->general_settings['hitshippo_fedex_ship_pack_type']) && !empty($this->general_settings['hitshippo_fedex_ship_pack_type'])) {
					$pack_type = $this->general_settings['hitshippo_fedex_ship_pack_type'];
				}
			}
			return $pack_type;
		}
		private function make_spl_ser_info()
		{
			$spl_ser = [];
			if ($this->ven_data['hitshippo_fedex_country'] == $this->dest_data['country']) {
				if (isset($this->general_settings['hitshippo_fedex_one_rates']) && $this->general_settings['hitshippo_fedex_one_rates'] == "yes") {
					$spl_ser['specialServiceTypes'] = array("FEDEX_ONE_RATE");
				}
			}
			return $spl_ser;
		}
		private function make_customs_info()
		{
			$customs = [];
			if (in_array($this->ven_data['hitshippo_fedex_country'], array("IN", "AE"))) {
				$pay_type = (isset($this->general_settings['hitshippo_fedex_duty_type']) && $this->general_settings['hitshippo_fedex_duty_type'] == "R") ? "RECIPIENT" : "SENDER";
				$customs["commercialInvoice"]["shipmentPurpose"] = "SOLD";
				$customs["dutiesPayment"]["payor"]["responsibleParty"]["address"] = ($pay_type == "RECIPIENT") ? $this->make_dest_info() : $this->make_ship_info();
				if ($pay_type == "SENDER") {
					$customs["dutiesPayment"]["payor"]["responsibleParty"]["accountNumber"] = array("value" => isset($this->ven_data['hitshippo_fedex_rest_acc_no']) ? $this->ven_data['hitshippo_fedex_rest_acc_no'] : '');
				}
				$customs["dutiesPayment"]["payor"]["paymentType"] = $pay_type;
				$customs["commodities"][0]["weight"] = array("units" => $this->weg_unit, "value" => $this->total_pack_weight);
				$customs["commodities"][0]["quantity"] = $this->total_pack_count;
				$customs["commodities"][0]["customsValue"] = array("amount" => $this->order_total, "currency" => isset($this->ven_data['hitshippo_fedex_currency']) ? $this->ven_data['hitshippo_fedex_currency'] : "USD");
				$customs["commodities"][0]["unitPrice"] = array("amount" => $this->order_total, "currency" => isset($this->ven_data['hitshippo_fedex_currency']) ? $this->ven_data['hitshippo_fedex_currency'] : "USD");
				$customs["commodities"][0]["numberOfPieces"] = $this->total_pack_count;
				$customs["commodities"][0]["countryOfManufacture"] = isset($this->ven_data['hitshippo_fedex_country']) ? $this->ven_data['hitshippo_fedex_country'] : "";
				$customs["commodities"][0]["quantityUnits"] = "PACK";
			}
			return $customs;
		}
		public function get_rate_res_rest($req_data = [], $auth_token = "")
		{
			$request_url = ($this->mode == "test") ? $this->test_rate_url : $this->live_rate_url;
			$result = wp_remote_post(
				$request_url,
				array(
					'method' => 'POST',
					'timeout' => 70,
					'sslverify' => 0,
					'body' => json_encode($req_data),
					'headers' => array("content-type" => "application/json", "Authorization" => "Bearer " . $auth_token)
				)
			);
			if (isset($result['body']) && !empty($result['body'])) {
				$res_body = json_decode($result['body']);
				return $res_body;
			}
			return;
		}
	}

?>