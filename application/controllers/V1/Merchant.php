<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Merchant extends APITOKO1001_Controller {

	var $base_source = '';

	function __construct()
	{
		parent::__construct();
		$this->load->model('V1/Merchant_model');
		$this->base_source = $this->config->item('base_source');
	}

	public function get_merchant_code()
	{
		$code = $this->uri->segment(3);
		return $code;
	}

	private function _merchant_profile()
	{
		$merchant_data = array();
		$merchant_code = $this->get_merchant_code();
		$query = $this->Merchant_model->get_merchant_profile($merchant_code);
		$query_data = $query->query_data;
		$row = get_row_query($query);
		if ($query_data)
		{
			foreach ($query_data->result() as $result)
			{
				$data_merchant['merchant_name'] = $result->merchant_name;
				$data_merchant['merchant_address'] = $result->merchant_address;
				$data_merchant['district_name'] = $result->district_name;
				$data_merchant['city_name'] = $result->city_name;
				$data_merchant['province_name'] = $result->province_name;
				$data_merchant['merchant_welcome_notes'] = $result->merchant_welcome_notes;
				if ($result->merchant_banner_img != "")
				{
					$banner = $this->base_source . MERCHANT_LOGO . $result->merchant_seq . '/' . $result->merchant_banner_img;
				}
				else
				{
					$banner = "";
				}

				if ($result->merchant_logo_img != "")
				{
					$logo = $this->base_source . MERCHANT_LOGO . $result->merchant_seq . '/' . $result->merchant_logo_img;
				}
				else
				{
					$logo = "";
				}
				$data_merchant['merchant_banner_img'] = $banner;
				$data_merchant['merchant_logo_img'] = $logo;
				$data_merchant['merchant_register_date'] = datetime_format($result->merchant_register_date, 'YmdHis', 'd-M-Y');
				$merchant_data = $data_merchant;
			}
		}
		return $merchant_data;
	}

	private function _merchant_product()
	{
		$merchant_code = $this->get_merchant_code();
		$query = $this->Merchant_model->get_merchant_product($merchant_code);
		$query_data = $query->query_data;
		$query_row = $query->query_row;
		$row = get_row_query($query);
		if ($query_data)
		{
			$data = display_product($query_data, $query_row);
		}
		return $data;
	}

	public function main()
	{
		$data = array("merchant_profile" => $this->_merchant_profile(), "merchant_product" => $this->_merchant_product());
		$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
		show_response_custom($response);
	}

}

/* End of file Merchant.php */
/* Location: ./application/controller/V1/Merchant.php */