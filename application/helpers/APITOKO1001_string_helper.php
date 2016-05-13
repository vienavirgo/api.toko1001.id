<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('generate_random_text'))
{

	function generate_random_text($length = 10, $is_capital_num = false)
	{
		$par1 = range('A', 'Z');
		$par2 = range('a', 'z');
		$par3 = range('1', '9');
		if (!$is_capital_num)
		{
			$final_array = array_merge($par1, $par2, $par3);
		}
		else
		{
			$final_array = array_merge($par1, $par3);
		}
		$password = '';
		while ($length--)
		{
			$key = array_rand($final_array);
			$password .= $final_array[$key];
		}
		return $password;
	}

}

if (!function_exists('generate_random_alphabet'))
{

	function generate_random_alphabet($length = 12, $is_capital_num = false)
	{
		$character1 = range('A', 'Z');
		$character2 = range('a', 'z');

		if (!$is_capital_num)
		{
			$final_array = array_merge($character2);
		}
		else
		{
			$final_array = array_merge($character1);
		}
		$alphabet = '';
		while ($length--)
		{
			$key = array_rand($final_array);
			$alphabet .= $final_array[$key];
		}
		return $alphabet;
	}

}

if (!function_exists('generate_member_token'))
{

	function generate_member_token($member_seq = '', $email = '')
	{
		$ci = & get_instance();
		$ci->load->library('encrypt');
		$key = $ci->config->item('application_secret');
		$date_rand = strtotime("now") . rand(1, 1000);

		$words = sha1(trim($key) . trim($email) . trim($member_seq) . trim($date_rand));
		return $words;
	}

}

if (!function_exists('generate_member_token_log'))
{

	function generate_member_token_log($member_seq = '', $email = '')
	{
		$member_token = generate_member_token($member_seq, $email);
		add_member_token_log($member_seq, $member_token);
		return $member_token;
	}

}

if (!function_exists('add_member_token_log'))
{

	function add_member_token_log($member_seq = '', $member_token = '')
	{
		$ci = & get_instance();
		$sql = "INSERT INTO m_member_token(member_seq , member_token)";
		$sql .= " VALUES('" . addslashes($member_seq) . "', '" . addslashes($member_token) . "')";
		$query = $ci->db->query($sql);
		if ($query === TRUE)
		{
			$paramval = 1;
		}

		return $paramval;
	}

}



if (!function_exists('currency_amount'))
{

	function currency_amount($amount = '', $currency = 'IDR')
	{
		$retval = '';
		switch ($currency)
		{
			case 'IDR':
				$retval = 'Rp. ' . number_format($amount, 0, '', ',');
			default:
				$retval = 'Rp. ' . number_format($amount, 0, '', ',');
		}
		return $retval;
	}

}


if (!function_exists('datetime_format'))
{
	/* this function only support 2 format from date YmdHis or Ymd */

	function datetime_format($datetime = '', $from_format = 'YmdHis', $to_format = 'd-M-Y H:i:s')
	{
		$retval = '';
		switch ($from_format)
		{
			case 'YmdHis':
				$retval = date($to_format, strtotime($datetime));
				break;
			case 'Ymd':
				$retval = date($to_format, strtotime($datetime . ' 00:00:00'));
				break;
			default:
		}
		return $retval;
	}

}


if (!function_exists('set_error_response'))
{

	function set_error_response($message = '', $status = 'FAILED', $code = '406')
	{
		$ci = & get_instance();
		$data = new stdClass();
		$ci->response->show_multi($code, $message, $data);
	}

}

if (!function_exists('is_not_digit_set_error_response'))
{

	function is_not_digit_set_error_response($value = '', $message = '', $status = 'FAILED', $code = '406')
	{
		if (!ctype_digit($value))
		{
			set_error_response($message, $status = 'FAILED', $code = '406');
		}
	}

}

if (!function_exists('is_not_digit_set_error_response_exit'))
{

	function is_not_digit_set_error_response_exit($value = '', $message = '', $status = 'FAILED', $code = '406')
	{
		if (!ctype_digit($value))
		{
			set_error_response($message, $status = 'FAILED', $code = '406');
			exit();
		}
	}

}

if (!function_exists('generate_combinations'))
{

	function generate_combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0)
	{
		$keys = array_keys($data);
		if (isset($value) === true)
		{
			array_push($group, $value);
		}

		if ($i >= count($data))
		{
			array_push($all, $group);
		}
		else
		{
			$currentKey = $keys[$i];
			$currentElement = $data[$currentKey];
			foreach ($currentElement as $val)
			{
				generate_combinations($data, $all, $group, $val, $i + 1);
			}
		}

		return $all;
	}

}
if (!function_exists('get_variant_value'))
{

	function get_variant_value($variant_seq, $variant_value, $separator = "")
	{
		$retval = "";
		switch ($variant_seq)
		{
			case "1": //all product
				$retval = "";
				break;
			default:
				$retval = $separator . $variant_value;
		}
		return $retval;
	}

}

/* End of file APITOKO1001_string_helper.php */
/* Location: ./application/helpers/APITOKO1001_string_helper.php */