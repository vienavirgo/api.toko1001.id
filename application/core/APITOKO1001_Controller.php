<?php

class APITOKO1001_Controller extends CI_Controller {

	var $jsondata;
	var $decode_jsondata;
	
	function __construct()
	{
		parent::__construct();
		$this->_request_variable();
		$this->_maintenance();
	}

	private function _get_request_variable($variable_name)
	{
		$retval = '';
		if (method_exists($this->input, 'post'))
		{
			if ($this->input->post($variable_name, NULL) !== NULL)
			{
				$retval = $this->input->post($variable_name, NULL);
			}
		}
		if (method_exists($this->input, 'get'))
		{
			if ($this->input->get($variable_name, NULL) !== NULL)
			{
				$retval = $this->input->get($variable_name, NULL);
			}
		}
		if (file_get_contents('php://input') != '')
		{
			$input = file_get_contents('php://input');
			$input_decode = json_decode($input);
			//			$input_current = current((array) $input_decode);
			$retval = isset($input_decode->$variable_name) ? $input_decode->$variable_name : '';
		}

		return $retval;
	}

	private function _request_variable()
	{
		$this->CI = & get_instance();
		$post_variable = array();
		$get_variable = array();
		$raw_variable = '';
		if (method_exists($this->input, 'post'))
		{
			$post_variable = $this->input->post();
		}
		if (count($post_variable)<=0 && method_exists($this->input, 'get'))
		{
			$get_variable = $this->input->get();
		}
		if (count($post_variable)<=0 && 
			count($get_variable)<=0 && 
			file_get_contents('php://input')!= '')
		{
			// !== FALSE
			$raw_variable = file_get_contents('php://input');
			$decoded_data_array = json_decode($raw_variable,TRUE);
			$decoded_data = json_decode($raw_variable);
			check_json_default_value($decoded_data_array);
			$this->jsondata = $raw_variable;
			$this->decode_jsondata = $decoded_data;
		}

		$app_user_id = $this->_get_request_variable('app_user_id');
		$app_token = $this->_get_request_variable('app_token');
		$app_version = $this->_get_request_variable('app_version');
		$app_device_lang = $this->_get_request_variable('app_device_lang');
		$app_device_id = $this->_get_request_variable('app_device_id');
		$app_device_model = $this->_get_request_variable('app_device_model');
		$app_latitude = $this->_get_request_variable('app_latitude');
		$app_longitude = $this->_get_request_variable('app_longitude');
		$app_os = $this->_get_request_variable('app_os');
		$app_name = $this->_get_request_variable('app_name');
		$app_id = $this->_get_request_variable('app_id');
		$variable_post = http_build_query($post_variable);
		$variable_get = http_build_query($get_variable);

		$request = '';
		if (count($post_variable) > 0)
		{
			$request .= $variable_post;
		}
		if (count($get_variable) > 0)
		{
			$request .= $variable_get;
		}
		if ($raw_variable != '')
		{
			$request .= $raw_variable;
		}
		$this->request->set_param_request($request);
		$this->request->set_user_id($app_user_id);
		$this->request->set_token($app_token);
		$this->request->set_app_version($app_version);
		$this->request->set_device_language($app_device_lang);
		$this->request->set_device_id($app_device_id);
		$this->request->set_device_model($app_device_model);
		$this->request->set_latitude($app_latitude);
		$this->request->set_longitude($app_longitude);
		$this->request->set_os_version($app_os);
		$this->request->set_app_name($app_name);
		$this->request->set_app_id($app_id);
	}

	private function _maintenance()
	{
		if ($this->config->item('maintenance_mode'))
		{
			$code = 503;
			$error = 'error maintenance';
			$this->response->set_error($error);
			$message = $this->status_code->getMessageForCode($code);
			$data = array();
			$this->response->show_multi($code, $message, $data);
			exit();
		}
	}

}
