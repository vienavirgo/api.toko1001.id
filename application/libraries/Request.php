<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Request {

	protected $ci;
	protected $request;
	protected $auth;
	protected $data;
	protected $info;
	protected $user_id;
	protected $token;
	protected $limit_1;
	protected $limit_2;
	protected $app_version;
	protected $device_language;
	protected $device_id;
	protected $device_model;
	protected $latitude;
	protected $longitude;
	protected $os_version;
	protected $app_name;
	protected $app_id;
	protected $param_request;
	protected $used_content_type = true;
	protected $content_type = '';

	public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model('V1/History_model');
	}

	public function is_Json($request = '')
	{
		return (json_decode($request) != NULL) ? true : false;
	}

	public function set_content_type($content_type = '')
	{
		$this->content_type = $content_type;
	}

	public function get_content_type()
	{
		return $this->content_type;
	}

	public function set_param_request($param_request = '')
	{
		$this->param_request = $param_request;
	}

	public function get_param_request()
	{
		return $this->param_request;
	}

	public function set_request($request = '')
	{
		$this->request = $request;
	}

	public function get_request()
	{
		return $this->request;
	}

	public function set_auth($auth = '')
	{
		$this->auth = $auth;
	}

	public function get_auth()
	{
		return $this->auth;
	}

	public function set_data($data = '')
	{
		$this->data = $data;
	}

	public function get_data()
	{
		return $this->data;
	}

	public function set_info($info = '')
	{
		$this->info = $info;
	}

	public function get_info()
	{
		return $this->info;
	}

	public function set_user_id($user_id = '')
	{
		$this->user_id = $user_id;
	}

	public function get_user_id()
	{
		return $this->user_id;
	}

	public function set_token($token = '')
	{
		$this->token = $token;
	}

	public function get_token()
	{
		return $this->token;
	}

	public function set_limit_1($limit_1 = '')
	{
		$this->limit_1 = $limit_1;
	}

	public function get_limit_1()
	{
		return $this->limit_1;
	}

	public function set_limit_2($limit_2 = '')
	{
		$this->limit_2 = $limit_2;
	}

	public function get_limit_2()
	{
		return $this->limit_2;
	}

	public function set_app_version($app_version = '')
	{
		$this->app_version = $app_version;
	}

	public function get_app_version()
	{
		return $this->app_version;
	}

	public function set_device_language($device_language = '')
	{
		$this->device_language = $device_language;
	}

	public function get_device_language()
	{
		return $this->device_language;
	}

	public function set_device_id($device_id = '')
	{
		$this->device_id = $device_id;
	}

	public function get_device_id()
	{
		return $this->device_id;
	}

	public function set_device_model($device_model = '')
	{
		$this->device_model = $device_model;
	}

	public function get_device_model()
	{
		return $this->device_model;
	}

	public function set_latitude($latitude = '')
	{
		$this->latitude = $latitude;
	}

	public function get_latitude()
	{
		return $this->latitude;
	}

	public function set_longitude($longitude = '')
	{
		$this->longitude = $longitude;
	}

	public function get_longitude()
	{
		return $this->longitude;
	}

	public function set_os_version($os_version = '')
	{
		$this->os_version = $os_version;
	}

	public function get_os_version()
	{
		return $this->os_version;
	}

	public function set_app_name($app_name = '')
	{
		$this->app_name = $app_name;
	}

	public function get_app_name()
	{
		return $this->app_name;
	}

	public function set_app_id($app_id = '')
	{
		$this->app_id = $app_id;
	}

	public function get_app_id()
	{
		return $this->app_id;
	}

	public function set_used_content_type($used_content_type = true)
	{
		$this->used_content_type = $used_content_type;
	}

	public function get_used_content_type()
	{
		return $this->used_content_type;
	}

	public function validate($request = '')
	{
		//get param request
		$param_request = $request;

		if ($param_request == '')
		{
			$param_request = file_get_contents('php://input');
		}

		$this->set_param_request($param_request);

		//if param request null

		if ($param_request == '')
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'param request null';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}

		//get content_type
		$content_type = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : '';
		//set content_type
		$this->set_content_type($content_type);

		//if content type not application/json
		if ($this->used_content_type AND $this->get_content_type() != 'application/json')
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'content type not application/json';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}

		//if request is not json
		if (!$this->is_Json($param_request))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'parameter request is not json';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}

		$obj_param_request = json_decode($param_request);

		//set request
		if (!isset($obj_param_request->request))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'request is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$request = $obj_param_request->request;
		$this->request = $request;

		///set auth
		if (!isset($request->auth))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'auth is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$auth = $request->auth;
		$this->auth = $auth;

		///set data
		if (!isset($request->data))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'data is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$data = $request->data;
		$this->data = $data;

		///set info
		if (!isset($request->info))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'info is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$info = $request->info;
		$this->info = $info;

		///set user_id
		if (isset($auth->user_id))
		{
			$user_id = $auth->user_id;
			$this->user_id = $user_id;
		}

		///set token
		if (isset($auth->token))
		{
			$token = $auth->token;
			$this->token = $token;
		}

		///set limit_1
		if (isset($data->limit_1))
		{
			$limit_1 = $data->limit_1;
			$this->limit_1 = $limit_1;
		}

		///set limit_2
		if (isset($data->limit_2))
		{
			$limit_2 = $data->limit_2;
			$this->limit_2 = $limit_2;
		}

		///set app_version
		if (!isset($info->app_version))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'app_version is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$app_version = $info->app_version;
		$this->app_version = $app_version;

		///set device_language
		if (!isset($info->device_language))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'device_language is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$device_language = $info->device_language;
		$this->device_language = $device_language;

		///set device_id
		if (!isset($info->device_id))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'device_id is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$device_id = $info->device_id;
		$this->device_id = $device_id;

		///set device_model
		if (!isset($info->device_model))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'device_model is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$device_model = $info->device_model;
		$this->device_model = $device_model;

		///set latitude
		if (!isset($info->latitude))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'latitude is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$latitude = $info->latitude;
		$this->latitude = $latitude;

		///set longitude
		if (!isset($info->longitude))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'longitude is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$longitude = $info->longitude;
		$this->longitude = $longitude;

		///set os_version
		if (!isset($info->os_version))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'os_version is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$os_version = $info->os_version;
		$this->os_version = $os_version;

		///set app_name
		if (!isset($info->app_name))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'app_name is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$app_name = $info->app_name;
		$this->app_name = $app_name;

		///set app_id
		if (!isset($info->app_id))
		{
			$response_status_code = 500;
			$response_message = $this->ci->status_code->getMessageForCode($response_status_code);
			$error = 'app_id is not defined';
			$this->ci->response->set_error($error);
			$this->ci->response->show($response_status_code, $response_message);
			return $error;
		}
		$app_id = $info->app_id;
		$this->app_id = $app_id;

		return "true";
	}

}

/* End of file Request.php */
/* Location: ./application/libraries/Request.php */