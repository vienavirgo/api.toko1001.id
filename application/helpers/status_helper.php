<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('show_response'))
{

	function show_response($code = '500', $message = '', $data = array(), $error = '')
	{
		$CI = & get_instance();
		$response_message = $CI->status_code->getMessageForCode($code);
		if ($message != '')
		{
			$response_message = $message;
		}
		$CI->response->set_error($error);
		$CI->response->show_multi($code, $response_message, $data);
	}

}

if (!function_exists('show_response_custom'))
{

	function show_response_custom($data = array())
	{
		$CI = & get_instance();
		$CI->response->show_custom($data);
	}

}
