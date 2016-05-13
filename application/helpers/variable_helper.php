<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get'))
{
	/* type = single,object,array */

	function get($type = '', $parameter_name = '', $allow_null = '')
	{
		$ci = & get_instance();

		if (!in_array($type, array('single', 'object', 'array')))
		{
			show_response(500, 'type is incorrect');
			exit();
		}

		if ($type == 'single' && $parameter_name == '')
		{
			show_response(500, 'parameter is empty');
			exit();
		}

		//get input data 
		$get_variable = $ci->input->get(NULL, TRUE);
		$post_variable = $ci->input->post(NULL, TRUE);

		if ($type == 'single' && $allow_null === TRUE && !$ci->variable->check_paging_allow_with_null($get_variable, $post_variable))
		{
			return NULL;
		}

		$ci->all_variable_array = array_merge($get_variable, $post_variable);

		//set input data to input_variable
		$input = array();
		foreach ($get_variable as $name => $value)
		{
			$input[$name] = $value;
		}
		foreach ($post_variable as $name => $value)
		{
			$input[$name] = $value;
		}

		//call library with parameter input_variable
		$default_value = $ci->variable->set_default_value($input);
		$ci->variable->validate($input, $default_value);
		$output = '';
		switch ($type)
		{
			case 'single':
				if (!isset($input[$parameter_name]))
				{
					show_response(406, "$parameter_name not set");
					exit();
				}
				$output = $input[$parameter_name];
				break;
			case 'object':
				$output = new stdClass();
				foreach ($input as $name => $value)
				{
					$output->$name = $value;
				}
				break;
			case 'array':
				$output = $input;
				break;
			default:
		}
		return $output;
	}

}

if (!function_exists('check_json_default_value'))
{

	function check_json_default_value($input_data)
	{
		$ci = &get_instance();
		$input = array();
//		echo json_encode($input_data);
//		echo "<br>";
//		echo "<br>";
		foreach ($input_data as $key => $value)
		{
			if (is_array($value))
			{
				check_json_default_value($value);
			}
			else
			{
				$input[$key] = $value;
				$default_value = $ci->variable->set_default_value($input);
				$ci->variable->validate($input, $default_value);
			}
		}
	}

}

if (!function_exists('check_date'))
{

	function check_date($date, $is_exit = false)
	{
		$dt = DateTime::createFromFormat("Y-m-d", $date);
		if (!($dt !== false && !array_sum($dt->getLastErrors())))
		{
			show_response(406);
			if ($is_exit)
				exit();
			return false;
		}
		return true;
	}

}

if (!function_exists('check_datetime'))
{

	function check_datetime($datetime, $is_exit = false)
	{
		if (!date('Y-m-d H:i:s', strtotime($datetime)) == $datetime)
		{
			show_response(406);
			if ($is_exit)
				exit();
			return false;
		}
		return true;
	}

}

if (!function_exists('check_time'))
{

	function check_time($time, $is_exit = false)
	{
		if (!date('H:i:s', strtotime($time)) == $time)
		{
			show_response(406);
			if ($is_exit)
				exit();
			return false;
		}
		return true;
	}

}

if (!function_exists('check_whole'))
{

	function check_whole($number, $is_exit = false)
	{
		if (ctype_digit($number) === FALSE)
		{
			show_response(406);
			if ($is_exit)
				exit();
			return false;
		}
		return true;
	}

}

if (!function_exists('check_timestamp'))
{

	function check_timestamp($number, $is_exit = false)
	{
		if (ctype_digit($number) === FALSE)
		{
			show_response(406);
			if ($is_exit)
				exit();
			return false;
		}
		return true;
	}

}

if (!function_exists('check_real'))
{

	function check_real($number = '', $is_exit = false)
	{
		if (!(ctype_digit($number) && $number > "0"))
		{
			show_response(406);
			if ($is_exit)
				exit();
			return false;
		}
		return true;
	}

}

if (!function_exists('check_integer'))
{

	function check_integer($number = '', $is_exit = false)
	{
		if (!preg_match('/^0$|^[-]?[1-9][0-9]*$/', $number))
		{
			show_response(406);
			if ($is_exit)
				exit();
			return false;
		}
		return true;
	}

}

if (!function_exists('check_decimal'))
{

	function check_decimal($number = '', $is_exit = false)
	{
		if (!preg_match('/^\d+(\.\d{1,2})$/', $number))
		{
			show_response(406);
			if ($is_exit)
				exit();
			return false;
		}
		return true;
	}

}

if (!function_exists('check_exit'))
{

	function check_exit($variable, $code = 406)
	{
		if (!isset($variable))
		{
			$message = $variable . ' not exists';
			show_response($code, $message);
			exit();
		}
		return $variable;
	}

}

if (!function_exists('check_object_exit'))
{

	function check_object_exit($object, $method_name, $code = 500)
	{
		if (!isset($object->$method_name))
		{
			$message = 'methods not exists';
			show_response($code, $message);
			exit();
		}
		return $object->$method_name;
	}

}

if (!function_exists('check_current_exit'))
{

	function check_current_exit($object, $method_name=array(), $code = 500)
	{
		$object_result = $object;
		foreach($method_name as $each_method_name)
		{
			check_object_exit($object_result, $each_method_name);
			$object_result = $object_result->$each_method_name;
		}
		return $object_result;
	}

}

if (!function_exists('output_with_gender'))
{

	function output_with_gender(&$params)
	{
		$ci = & get_instance();

		if ($ci->input->get('gender') !== NULL OR $ci->input->post('gender') !== NULL)
		{
			$params->gender = display_gender_format(get('single', 'gender'));
		}
		return $params;
	}

}

if (!function_exists('check_constant'))
{

	function check_constant($trx = '')
	{
		if (!defined($trx))
		{
			$trx = '';
			return $trx;
		}
		return constant($trx);
	}

}

if (!function_exists('generate_alnum'))
{

	function generate_alnum($length = 8, $is_capital_num = false)
	{
		$random = "";
		srand((double) microtime() * 1000000);
		if (!$is_capital_num)
		{
			$data = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$data .= "0123456789";
		}
		else
		{
			$data = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$data .= "0123456789";
		}

		for ($i = 0; $i < $length; $i++)
		{
			$random .= substr($data, (rand() % (strlen($data))), 1);
		}
		return $random;
	}

}
   


/* End of file variable_helper.php */
/* Location: ./application/helpers/variable_helper.php */