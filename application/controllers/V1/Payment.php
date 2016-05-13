<?php

class Payment extends APITOKO1001_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('V1/Member_model');
		$this->load->model('V1/Transaction_model');
		$this->_initialize();
	}

	private function _initialize()
	{
		$data_token = check_token();
		$this->member_token = $data_token['member_token'];
		$this->member_seq = $data_token['member_seq'];
	}

	public function payment_gateway()
	{
		$this->history->set_api_name('_Payment_gateway');
		$data = array(
			'items' => $this->_get_payment_gateway_method(),
		);
		$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
		show_response_custom($response);
	}

	private function _get_payment_gateway_method()
	{
		$get_payment_gateway = $this->Transaction_model->get_payment_gateway_method();
		$row = $get_payment_gateway->row;
		$each_data = array();
		$logo_dir = get_base_source() . 'assets/admin/tmp_payment/';
		foreach ($row as $each)
		{
			$each_data['seq'] = $each->seq;
			$each_data['name'] = $each->name;
			$each_data['code'] = $each->code;
			$each_data['active'] = 0;
			if ($each_data['code'] == 'BNK')
			{
				$each_data['active'] = 1;
			}
			$each_data['type'] = 'web';
			if ($each_data['code'] == 'BNK')
			{
				$each_data['type'] = 'info';
			}
			$each_data['logo_img'] = $logo_dir . $each->logo_img;
			$data[] = $each_data;
		}
		return $data;
	}

}
