<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class History {

	protected $api_name = '';

	public function __construct()
	{
		$this->ci = & get_instance();
	}

	public function set_api_name($api_name = '')
	{
		if ($this->get_api_name() == '')
		{
			$this->api_name = $api_name;
		}
		else
		{
			//for multiple api call in once
			$this->api_name = $this->get_api_name() . ', ' . $api_name;
		}
	}

	public function get_api_name()
	{
		return $this->api_name;
	}

	public function save_history()
	{
		/* if ($this->ci->agent->is_browser())
		  {
		  $agent = $this->ci->agent->browser().' '.$this->ci->agent->version();
		  }
		  elseif ($this->ci->agent->is_robot())
		  {
		  $agent = $this->ci->agent->robot();
		  }
		  elseif ($this->ci->agent->is_mobile())
		  {
		  $agent = $this->ci->agent->mobile();
		  }
		  else
		  {
		  $agent = 'Unidentified User Agent';
		  }
		  //echo $this->agent->platform(); */
		$data['name'] = $this->get_api_name();
		$data['error'] = $this->ci->response->get_error();
		$data['user_agent'] = $this->ci->agent->agent_string();
		$data['url'] = current_url();
//		$data['ip'] = $this->ci->input->ip_address();
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$data['ip'] = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$data['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$data['ip'] = $_SERVER['REMOTE_ADDR'];
		}
		$data['secret_key'] = "";
		$data['request'] = $this->ci->request->get_param_request();
		$data['response'] = $this->ci->response->get_response();
		$data['app_version'] = $this->ci->request->get_app_version();
		$data['device_language'] = $this->ci->request->get_device_language();
		$data['device_id'] = $this->ci->request->get_device_id();
		$data['device_model'] = $this->ci->request->get_device_model();
		$data['latitude'] = $this->ci->request->get_latitude();
		$data['longitude'] = $this->ci->request->get_longitude();
		$data['os_version'] = $this->ci->request->get_os_version();
		$data['app_name'] = $this->ci->request->get_app_name();
		$data['app_id'] = $this->ci->request->get_app_id();
		$data['user_id'] = $this->ci->request->get_user_id();
		$data['token'] = $this->ci->request->get_token();
		$this->ci->History_model->save_history($data);
	}

	public function set_save_history($name = '', $error = '', $user_agent = '', $url = '', $ip = '', $secret_key = '', $request = '', $response = '', $app_version = '', $device_language = '', $device_id = '', $device_model = '', $latitude = '', $longitude = '', $os_version = '', $app_name = '', $app_id = '', $user_id = '', $token = '')
	{
		$ci = & get_instance();

		require APPPATH . 'config' . DIRECTORY_SEPARATOR . ENVIRONMENT . DIRECTORY_SEPARATOR . 'database.php';

		$hostname = $db['default']['hostname'];
		$username = $db['default']['username'];
		$password = $db['default']['password'];
		$database = $db['default']['database'];
		if ($con = mysqli_connect($hostname, $username, $password, $database))
		{
			$sql = "INSERT INTO tb_apilog (name, error, user_agent, url, ip, secret_key, request, response, app_version, device_language, device_id, device_model, latitude, longitude, os_version, app_name, app_id, user_id, token)" .
				"VALUES ('" . addslashes($name) . "', '" . addslashes($error) . "', '" . addslashes($user_agent) . "', '" . addslashes($url) . "', '" . addslashes($ip) . "', '" . addslashes($secret_key) . "', '" . addslashes($request) . "', '" . addslashes($response) . "', '" . addslashes($app_version) . "', '" . addslashes($device_language) . "', '" . addslashes($device_id) . "', '" . addslashes($device_model) . "', '" . addslashes($latitude) . "', '" . addslashes($longitude) . "', '" . addslashes($os_version) . "', '" . addslashes($app_name) . "', '" . addslashes($app_id) . "', '" . addslashes($user_id) . "', '" . addslashes($token) . "')";
			// Perform queries 
			mysqli_query($con, $sql);
			mysqli_close($con);
		}
		else
		{
			$code = 500;
			$error = $ci->status_code->getMessageForCode($code);
			$message = $error;
			$data = array();
			echo json_encode(array(
				'code' => $code,
				'message' => $message,
				'data' => $data
				)
			);
			exit();
		}
		// Check connection



		/* json_encode(
		  array('name' => $name,
		  'error' => $error,
		  'user_agent' => $user_agent,
		  'url' => $url,
		  'ip' => $ip,
		  'secret_key' => $secret_key,
		  'request' => $request,
		  'response' => $response,
		  'app_version' => $app_version,
		  'device_language' => $device_language,
		  'device_id' => $device_id,
		  'device_model' => $device_model,
		  'latitude' => $latitude,
		  'longitude' => $longitude,
		  'os_version' => $os_version,
		  'app_name' => $app_name,
		  'app_id' => $app_id,
		  )
		  ); */
	}

}

/* End of file History.php */
/* Location: ./application/libraries/History.php */