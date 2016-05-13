<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Error_android extends APITOKO1001_Controller {

	var $code = '';
	var $message = '';

	public function __construct()
	{
		parent::__construct();
	}

	private function _insert_error_android()
	{

		$params = get('object');
		$error_android = check_object_exit($params, 'error_android');
		$user_agent = $this->input->user_agent();
		$ip = $this->input->ip_address();
		$data = file_put_contents($this->config->item('logs_path'). 'error_android', '[' . date('Y-m-d H:i:s') . '] [' . $ip . '] ' . $error_android . '   ' . $user_agent . PHP_EOL, FILE_APPEND);
		return $data;
	}

	public function list_error()
	{
		$this->history->set_api_name('_insert error android_');
		$data = $this->_insert_error_android();
		$response = array('code' => 304, 'message' => 'Failed to save review', 'data' => array());
		if ($data)
		{
			$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => array());
		}
		show_response_custom($response);
	}

}
