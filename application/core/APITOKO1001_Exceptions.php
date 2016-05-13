<?php

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2016, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Exceptions Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Exceptions
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/exceptions.html
 */
class APITOKO1001_Exceptions extends CI_Exceptions {

	/**
	 * Nesting level of the output buffering mechanism
	 *
	 * @var	int
	 */
	public $ob_level;

	/**
	 * List of available error levels
	 *
	 * @var	array
	 */
	public $levels = array(
		E_ERROR => 'Error',
		E_WARNING => 'Warning',
		E_PARSE => 'Parsing Error',
		E_NOTICE => 'Notice',
		E_CORE_ERROR => 'Core Error',
		E_CORE_WARNING => 'Core Warning',
		E_COMPILE_ERROR => 'Compile Error',
		E_COMPILE_WARNING => 'Compile Warning',
		E_USER_ERROR => 'User Error',
		E_USER_WARNING => 'User Warning',
		E_USER_NOTICE => 'User Notice',
		E_STRICT => 'Runtime Notice'
	);

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->ob_level = ob_get_level();
		// Note: Do not log messages from this constructor.
	}

	// --------------------------------------------------------------------

	/**
	 * Exception Logger
	 *
	 * Logs PHP generated error messages
	 *
	 * @param	int	$severity	Log level
	 * @param	string	$message	Error message
	 * @param	string	$filepath	File path
	 * @param	int	$line		Line number
	 * @return	void
	 */
	public function log_exception($severity, $message, $filepath, $line)
	{
		$severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
		log_message('error', 'Severity: ' . $severity . ' --> ' . $message . ' ' . $filepath . ' ' . $line);
	}

	// --------------------------------------------------------------------

	/**
	 * 404 Error Handler
	 *
	 * @uses	CI_Exceptions::show_error()
	 *
	 * @param	string	$page		Page URI
	 * @param 	bool	$log_error	Whether to log the error
	 * @return	void
	 */
	public function show_404($page = '', $log_error = TRUE)
	{
		if (is_cli())
		{
			$heading = 'Not Found';
			$message = 'The controller/method pair you requested was not found.';
		} else
		{
			$heading = '404 Page Not Found';
			$message = 'The page you requested was not found.';
		}

		// By default we log this, but allow a dev to skip it
		if ($log_error)
		{
			log_message('error', $heading . ': ' . $page);
		}

		echo $this->show_error($heading, $message, 'error_404', 404);
		exit(4); // EXIT_UNKNOWN_FILE
	}

	// --------------------------------------------------------------------

	/**
	 * General Error Page
	 *
	 * Takes an error message as input (either as a string or an array)
	 * and displays it using the specified template.
	 *
	 * @param	string		$heading	Page heading
	 * @param	string|string[]	$message	Error message
	 * @param	string		$template	Template name
	 * @param 	int		$status_code	(default: 500)
	 *
	 * @return	string	Error page output
	 */
	public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
/*		$templates_path = config_item('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = VIEWPATH . 'errors' . DIRECTORY_SEPARATOR;
		}

		if (is_cli())
		{
			$message = "\t" . (is_array($message) ? implode("\n\t", $message) : $message);
			$template = 'cli' . DIRECTORY_SEPARATOR . $template;
		} else
		{
			set_status_header($status_code);
			$message = '<p>' . (is_array($message) ? implode('</p><p>', $message) : $message) . '</p>';
			$template = 'html' . DIRECTORY_SEPARATOR . $template;
		}

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		ob_start();
		include($templates_path . $template . '.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;*/
		$message = $heading.' '.json_encode($message, JSON_UNESCAPED_SLASHES);
		$response_status_code = 500;
		$api_name = $template;
		$error = $message;
		$this->_save_error_to_database($response_status_code, $api_name, $error);
		exit();
		
	}

	// --------------------------------------------------------------------

	public function show_exception($exception)
	{
		$templates_path = config_item('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = VIEWPATH . 'errors' . DIRECTORY_SEPARATOR;
		}

		$message = $exception->getMessage();
		if (empty($message))
		{
			$message = '(null)';
		}

		if (is_cli())
		{
			$templates_path .= 'cli' . DIRECTORY_SEPARATOR;
		} else
		{
			set_status_header(500);
			$templates_path .= 'html' . DIRECTORY_SEPARATOR;
		}

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}

		/* 		ob_start();
		  include($templates_path.'error_exception.php');
		  $buffer = ob_get_contents();
		  ob_end_clean();
		  echo $buffer; */
		$message = 'An uncaught Exception was encountered.  severity: ' . $severity . ', message: ' . $message . ', filename: ' . $filepath . ',line number: ' . $line;

		if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE)
		{

			$message .= '   Backtrace:';
			foreach (debug_backtrace() as $error)
			{

				if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0)
				{

					$message .= ' File: ' . $error['file'];
					$message .= ' Line: ' . $error['line'];
					$message .= ' Function: ' . $error['function'];
				}
			}
		}
		$ci = & get_instance();

		$param_request = file_get_contents('php://input');
		$ci->request->set_param_request($param_request);

		$response_status_code = 500;
		$api_name = "_exception_";
		$ci->history->set_api_name($api_name);
		$response_message = $ci->status_code->getMessageForCode($response_status_code);
		$error = $message;

		$ci->response->set_error($error);
		echo $ci->response->result($response_status_code, $response_message);
		$ci->history->save_history();
		exit();
	}

	// --------------------------------------------------------------------

	/**
	 * Native PHP error handler
	 *
	 * @param	int	$severity	Error level
	 * @param	string	$message	Error message
	 * @param	string	$filepath	File path
	 * @param	int	$line		Line number
	 * @return	string	Error page output
	 */
	public function show_php_error($severity, $message, $filepath, $line)
	{
		$templates_path = config_item('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = VIEWPATH . 'errors' . DIRECTORY_SEPARATOR;
		}

		$severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;

		// For safety reasons we don't show the full file path in non-CLI requests
		if (!is_cli())
		{
			$filepath = str_replace('\\', '/', $filepath);
			if (FALSE !== strpos($filepath, '/'))
			{
				$x = explode('/', $filepath);
				$filepath = $x[count($x) - 2] . '/' . end($x);
			}

			$template = 'html' . DIRECTORY_SEPARATOR . 'error_php';
		} else
		{
			$template = 'cli' . DIRECTORY_SEPARATOR . 'error_php';
		}

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		/* 		ob_start();
		  include($templates_path.$template.'.php');
		  $buffer = ob_get_contents();
		  ob_end_clean();
		  echo $buffer;
		  exit(); */
		$message = 'A PHP Error was encountered.  severity: ' . $severity . ', message: ' . $message . ', filename: ' . $filepath . ',line number: ' . $line;

		if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE)
		{

			$message .= '   Backtrace:';
			foreach (debug_backtrace() as $error)
			{

				if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0)
				{

					$message .= ' File: ' . $error['file'];
					$message .= ' Line: ' . $error['line'];
					$message .= ' Function: ' . $error['function'];
				}
			}
		}
		$response_status_code = 500;
		$api_name = 'php_error';
		$error = $message;
		$this->_save_error_to_database($response_status_code, $api_name, $error);
		exit();
	}

	private function _save_error_to_database($response_status_code = 500, $api_name = '', $error = '')
	{
		require_once APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'Status_code.php';
		require_once APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'History.php';
		require_once APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'Request.php';
		require_once APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'Response.php';
		require_once BASEPATH . 'libraries' . DIRECTORY_SEPARATOR . 'User_agent.php';
		require_once BASEPATH . 'core' . DIRECTORY_SEPARATOR . 'Input.php';

		$status_code_obj = new Status_code();
		$history_obj = new History();
		$request_obj = new Request();
		$response_obj = new Response();
		$user_agent_obj = new CI_User_agent();
		$input_obj = new CI_Input();
		$response_message = $status_code_obj->getMessageForCode($response_status_code);

		$user_agent = $user_agent_obj->agent_string();
		$url = current_url();
		$ip = $input_obj->ip_address();
		$secret_key = '';

		$param_request = file_get_contents('php://input');
		$content_type = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : '';
		$request = $param_request;
		$response = $response_obj->result($response_status_code, $response_message,'0');
		$request_obj = json_decode($param_request);
		$app_version = isset($request_obj->request->info->app_version) ? $request_obj->request->info->app_version : '';
		$device_language = isset($request_obj->request->info->device_language) ? $request_obj->request->info->device_language : '';
		$device_id = isset($request_obj->request->info->device_id) ? $request_obj->request->info->device_id : '';
		$device_model = isset($request_obj->request->info->device_model) ? $request_obj->request->info->device_model : '';
		$latitude = isset($request_obj->request->info->latitude) ? $request_obj->request->info->latitude : '';
		$longitude = isset($request_obj->request->info->longitude) ? $request_obj->request->info->longitude : '';
		$os_version = isset($request_obj->request->info->os_version) ? $request_obj->request->info->os_version : '';
		$app_name = isset($request_obj->request->info->app_name) ? $request_obj->request->info->app_name : '';
		$app_id = isset($request_obj->request->info->app_id) ? $request_obj->request->info->app_id : '';

		$history_obj->set_api_name($api_name);
		$response_obj->set_error($error);
		
		$history_obj->set_save_history($api_name, $error, $user_agent, $url, $ip, $secret_key, $request, $response, $app_version, $device_language, $device_id, $device_model, $latitude, $longitude, $os_version, $app_name, $app_id);

		echo $response;
	}

}
