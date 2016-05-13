<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expedition extends APITOKO1001_Controller {

	var $code = '';
	var $message = '';
	var $base_source = '';
	var $member_seq = '';
	var $member_token = '';
	var $action = '';
	var $expedition = '';
	var $district_seq = '';

	function __construct()
	{
		parent::__construct();
				
		$this->base_source = $this->config->item('base_source');
		$this->load->model('V1/Member_model');
		$this->load->model('V1/Expedition_model');
		$this->_initialize();
	}

	private function _initialize()
	{	
		$header_request = 'expedition';
		$this->expedition = check_current_exit($this,array('decode_jsondata',$header_request));
		$this->action = check_current_exit($this,array('decode_jsondata',$header_request,'action'));
		$this->product = check_current_exit($this,array('decode_jsondata',$header_request,'product'));
		$this->district_seq = check_current_exit($this,array('decode_jsondata',$header_request,'district_seq'));
		$data_token = check_token($header_request);
		$this->member_token = $data_token['member_token'];
		$this->member_seq = $data_token['member_seq'];
	}

	public function index()
	{
		$this->_check_action(array(READ));
		switch ($this->action)
		{
			case READ:
				$data = $this->_read_expedition();
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			default:
				show_response(406, "action not allowed");
				exit();
		}
	}

	private function _check_action($action_allowed = array())
	{
		if (!in_array($this->action, $action_allowed))
		{
			show_response(406, "action not allowed");
			exit();
		}
	}

	private function _read_expedition()
	{
		$this->history->set_api_name('_Expedition List');
		
		$params = new stdClass;
		$data = array();
		$qty = array();
		$voucher = array();
		$coupon = array();
		
		$expedition=$this->expedition;
		$product = $this->product;
		$product_variant_seq_array = array();
		$qty_array = array();
		foreach($product as $each_product)
		{
			$product_variant_seq_array[] = isset($each_product->product_variant_seq)? $each_product->product_variant_seq:0;
			$qty_array[] = isset($each_product->quantity)? $each_product->quantity:0;
		}
		$product_variant_seq = implode(',',$product_variant_seq_array);
		$qty = implode(',',$qty_array);
		$member_seq = $this->member_seq;
		$token = $this->member_token;
		$action = $this->action;
		$district_seq = $this->district_seq;
		
		$params->product_variant_seq = $product_variant_seq;
		$product_variant_seq = $params->product_variant_seq;
		if ($product_variant_seq != "")
		{
			$product_variant_seq = explode(',', $product_variant_seq, 0);
		}
		
		if ($qty != "")
		{
			$qty = explode(',', $qty);
		}
		else
		{
			$qty = 0;
		}

		$query = $this->Expedition_model->find_expedition($params);
		$query_data = $query->query_data;
		$total_order = 0;
		$promo_seq = 0;
		if ($query_data)
		{
			foreach ($query_data->result() as $key => $value)
			{
				$detail = array();
				$detail["product_name"] = $value->product_name;
				$detail['product_seq'] = $value->product_seq;
				$detail['product_price'] = $value->product_price;
				$detail["disc_percent"] = $value->disc_percent;
				$detail['sell_price'] = $value->sell_price;
				$detail['max_buy'] = $value->max_buy;
				$detail["variant_value"] = $value->variant_value;
				$detail['image'] = $this->base_source . PRODUCT_UPLOAD_IMAGE . $value->merchant_seq . '/' . S_IMAGE_UPLOAD . $value->pic_1_img;
				$detail['merchant_seq'] = $value->merchant_seq;
				$detail["merchant_name"] = $value->merchant_name;
				$detail['merchant_code'] = $value->merchant_code;
				$detail['p_weight_kg'] = $value->p_weight_kg;
				$detail['p_length_cm'] = $value->p_length_cm;
				$detail["p_width_cm"] = $value->p_width_cm;
				$detail['p_height_cm'] = $value->p_height_cm;
				$detail['b_weight_kg'] = $value->b_weight_kg;
				$detail["b_length_cm"] = $value->b_length_cm;
				$detail['b_width_cm'] = $value->b_width_cm;
				$detail['b_height_cm'] = $value->b_height_cm;
				$detail["volume_divider"] = $value->e_volume_divider;
				$detail['city_seq'] = $value->city_seq;
				$detail['sell_price'] = $value->sell_price;
				$expedition_seq = $value->expedition_seq;
				$detail['expedition_seq'] = $expedition_seq;
				if ($expedition_seq == 0)
				{
					$expedition_seq = 1;
				}
				$params->expedition_seq = $expedition_seq;
				$expedition_info = $this->Expedition_model->exp_info($expedition_seq, $district_seq);
				$exp_data = $expedition_info->query_data;

				if ($exp_data->result() != 0)
				{
					foreach ($exp_data->result() as $dt)
					{
						$params->exp_code = $dt->exp_code;
						$detail['exp_code'] = isset($val->exp_code)? $dt->exp_code:NULL;
						$params->from_district_code = $dt->from_district_code;
						$params->to_district_code = $dt->to_district_code;
						$params->exp_service_seq = $dt->exp_service_seq;
						$detail['exp_service_seq'] = $dt->exp_service_seq;
					}
				}
				else
				{
					show_response(406, "unable to get district or city");
					exit();
				}

				$promo = $this->Expedition_model->exp_promo($value->city_seq, $value->merchant_seq);
				$exp_promo = $promo->query_data;

				if ($exp_promo->num_rows() > 0)
				{
					foreach ($exp_promo->result() as $dt)
					{
						$detail['exp_promo_seq'] = isset($dt->seq)? $dt->seq:NULL;
					}
					$rate_real_exp = $this->get_rate_expedition($params);
					$detail['rate_real_exp'] = $rate_real_exp;
					$detail['rate_exp'] = 0;
					$rate_exp = 0;
				}
				else
				{
					$rate_exp = $this->get_rate_expedition($params);
					$detail['rate_real_exp'] = $rate_exp;
					$detail['rate_exp'] = $rate_exp;
				}

				$ctr = count($qty);
				if ($key >= $ctr)
				{
					$n_qty = 0;
				}
				else
				{
					$n_qty = $qty[$key];
				}
				$detail['qty'] = $n_qty;
				$count_price_weight = ceil($n_qty * $value->p_weight_kg);
				$detail["exp_real_fee"] = $rate_exp * $count_price_weight;
				$total_order += $n_qty * $value->sell_price;
				$data[] = $detail;
			}
			$get_voucher = $this->Expedition_model->voucher($this->member_seq, $total_order);
			$voucher_data = $get_voucher->query_data;
			if ($voucher_data->num_rows() > 0)
			{
				foreach ($voucher_data->result() as $vc)
				{
					$vch_d['voucher_seq'] = $vc->seq;
					$vch_d['voucher_code'] = $vc->code;
					$vch_d['voucher_amount'] = $vc->nominal;
					$voucher[] = $vch_d;
				}
			}
			else
			{
				$voucher = null;
			}

			$get_coupon = $this->Expedition_model->coupon($params);
			$coupon_data = $get_coupon->query_data;
			if ($coupon_data->num_rows() > 0)
			{
				foreach ($coupon_data->result() as $cp)
				{
					$cp_d['coupon_seq'] = $cp->seq;
					$cp_d['coupon_code'] = $cp->coupon_code;
					$cp_d['coupon_name'] = $cp->coupon_name;
					$cp_d['coupon_amount'] = $cp->nominal;
					$coupon[] = $cp_d;
				}
			}
			else
			{
				$coupon = null;
			}
		}

		$expedition = array(
			'expedition' => $data,
			'voucher' => $voucher,
			'coupon' => $coupon
		);

		return ($expedition);
	}

	protected function get_rate_expedition($exp_info)
	{
		$rate = 0;
		$params = new stdClass();
		$params->expedition_seq = $exp_info->expedition_seq;
		$params->from_district_code = $exp_info->from_district_code;
		$params->to_district_code = $exp_info->to_district_code;
		$params->exp_service_seq = $exp_info->exp_service_seq;

		$get_rate_cache = $this->Expedition_model->get_rate_cache($params);
		$rate_cache = $get_rate_cache->query_data->result();

		if (get_row_query($get_rate_cache) == 0)
		{
			switch ($exp_info->exp_code)
			{
				case "JNE" :
					$rate = $this->get_jne_expedition_rate($params);
					break;
				case "PANDU" :
					$rate = $this->get_pandu_expedition_rate($params);
					break;
				case "RK":
					$rate = $this->get_raja_kirim_expedition_rate($params);
					break;
				case "TIKI" :
					$rate = $this->get_tiki_expedition_rate($params);
					break;
				default: $rate = null;
			}

			if (isset($rate) && $rate != 0)
			{
				$params->rate = $rate;
				$this->Expedition_model->save_rate_cache($params);
			}

			if (!isset($rate) || $rate == 0)
			{
				show_response(406, "unable to get expedition rate");
				exit();
			}
		}
		else
		{
			foreach ($rate_cache as $dt)
			{
				$rate = $dt->rate;
			}
		}
		return $rate;
	}

	protected function get_tiki_expedition_rate($exp_info)
	{
		$url = URL_TIKI_API . '/services/api.cfc?method=tariff&origin=' . $exp_info->from_district_code . '&destination=' . $exp_info->to_district_code . '&weight=1';
		$this->curl_api->set_url($url);
		$this->curl_api->setGet(true);
		$this->curl_api->setIsUsingUserAgent(true);
		$this->curl_api->setData();
		$json_encoded = $this->curl_api->getResponse();
		$data_jsons = json_decode($json_encoded, true);
	}

	protected function get_jne_expedition_rate($exp_info)
	{
		$web_params = new stdClass();
		$web_params->type = GET_RATE_TYPE;
		$web_params->function_name = FUNCTION_GET_JNE_RATE_EXPEDITION;
		$web_params->request_datetime = date('Y-m-d H:i:s');

		$web_service_seq = $this->Expedition_model->save_web_service((array)$web_params);

		$params = 'username=' . API_USERNAME_JNE . '&api_key=' . API_KEY_JNE . '&from=' . $exp_info->from_district_code . '&thru=' . $exp_info->to_district_code . '&weight=1';
		$url = URL_JNE_API . '/tracing/' . strtolower(API_USERNAME_JNE) . '/price/';
		$this->curl_api->set_url($url);

		$this->curl_api->setIsUsingUserAgent(true);
		$this->curl_api->setParams($params);
		$this->curl_api->setData();
		$json_encoded = $this->curl_api->getResponse();
		$data_jsons = json_decode($json_encoded, true);
		$web_params->web_service_seq = $web_service_seq;
		$web_params->request = $this->curl_api->getRequestHeaders() . $this->curl_api->getRequest();
		$web_params->response = $this->curl_api->getResponseHeaders() . $this->curl_api->getResponse();
		$web_params->response_datetime = date('Y-m-d H:i:s');
		$this->Expedition_model->save_update_service((array)$web_params);

		if (!isset($data_jsons))
		{
			return null;
//            throw new Exception(ERROR_PLEASE_TRY_AGAIN);
		}

		foreach ($data_jsons as $key => $data)
		{
			if ($data == false)
			{
//                throw new Exception(ERROR_PLEASE_TRY_AGAIN);
				return null;
			}
			else
			{

				foreach ($data as $rate)
				{
					if ($exp_info->exp_service_seq == $rate["service_code"] || JNE_CTC_REG_CODE == $rate["service_code"])
					{
						return $rate["price"];
					}
				}
			}
		}
		return null;
	}

	protected function get_pandu_expedition_rate($exp_info)
	{
		$params = array(
			'PASS' => generate_pandu_api_key(),
			'ACCOUNT_NO' => PANDU_ACCOUNT_NO, // nomor akun  
			'TRANSPORT' => 'AIR', // Pilihan : AIR/LAND/SEA (sesuai yang terdapat dalam tariff)
			'SERVICETYPE' => $service_code, // REG/ONS/LAND/LTL cek tariff yg di berikan marketing
			'ORIG' => $kota_asal,
			'DEST' => $kota_tujuan,
			'WEIGHT' => '1',
		);
	}

	protected function generate_pandu_api_key()
	{
		return md5(API_KEY_PANDU . date("Ymdh"));
	}

	protected function get_raja_kirim_expedition_rate($exp_info)
	{

		if ($exp_info->province_seq == JAKARTA_PROVINCE_SEQ)
		{
			return null;
		}

		$web_params = new stdClass();
		$web_params->type = GET_RATE_TYPE;
		$web_params->function_name = FUNCTION_GET_RAJA_KIRIM_RATE_EXPEDITION;
		$web_params->request_datetime = date('Y-m-d H:i:s');
		$web_service_seq = $this->Expedition_model->save_web_service((array)$web_params);
		$params = "method=pda_json_cek_harga&apiKey=" . API_KEY_RAJA_KIRIM . "&apiUser=" . API_USER_RAJA_KIRIM . "  &city_code=" . $exp_info[0]->to_district_code . "&weight=1";

		$url = URL_RAJA_KIRIM_API;
		$this->curl_api->set_url($url);
		$this->curl_api->setIsUsingUserAgent(true);
		$this->curl_api->setParams($params);
		$this->curl_api->setData();
		$json_encoded = $this->curl_api->getResponse();
		$data_jsons = json_decode($json_encoded, true);

		$web_params->web_service_seq = $web_service_seq;
		$web_params->request = $this->curl_api->getRequestHeaders() . $this->curl_api->getRequest();
		$web_params->response = $this->curl_api->getResponseHeaders() . $this->curl_api->getResponse();
		$web_params->response_datetime = date('Y-m-d H:i:s');
		$this->Expedition_model->save_update_service((array)$web_params);

		foreach ($data_jsons as $key => $data)
		{
			foreach ($data as $rate)
			{
				if (isset($rate["err"]))
				{
					return null;
//                    throw new Exception(ERROR_PLEASE_TRY_AGAIN);
				}
				else
				{
					return $rate["harga"];
				}
			}
		}
		return null;
	}

}

/* End of file Expedition.php */
/* Location: ./application/controller/V1/Expedition.php */
