<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('new_seq'))
{

	function new_seq($table = '', $field = 'seq', $condition = '')
	{
		$ci = & get_instance();
		$sql = "SELECT MAX(`{$field}`) AS seq from {$table}";
		if ($condition != '')
		{
			$sql .= ' ' . $condition;
		}

		$query = $ci->db->query($sql);
		$max_seq = 0;
		if ($query)
		{
			foreach ($query->result() as $result)
			{
				$max_seq = $result->seq;
			}
		}

		return ++$max_seq;
	}

}
if (!function_exists('check_user_exists'))
{

	function check_user_exists($field = "", $table = "", $paramater = "")
	{
		$ci = & get_instance();
		$sql = "SELECT {$field} from {$table} where `{$field}` = '{$paramater}'";
		$query = $ci->db->query($sql);
		foreach ($query->result() as $result)
		{
			$field = $result->email;
		}
		return $field;
	}

}

if (!function_exists('query_data_row'))
{

	function query_data_row($sql = '', $total_row_name = 'total_row')
	{
		$ci = & get_instance();
		$query_result = new stdClass;
		$sql_row = "SELECT FOUND_ROWS() AS {$total_row_name}";
		$query_data = $ci->db->query($sql);
		$query_row = $ci->db->query($sql_row);
		$query_result->query_data = $query_data;
		$query_result->query_row = $query_row;
		return $query_result;
	}

}

if (!function_exists('get_row_query'))
{

	function get_row_query($query)
	{
		$query_row = $query->query_row;
		$row = 0;
		if ($query_row)
		{
			foreach ($query_row->result() as $each_row)
			{
				$row = $each_row->total_row;
			}
		}
		return $row;
	}

}

if (!function_exists('get_fields'))
{

	function get_fields($table = '', $params = array())
	{
		$ci = & get_instance();
		$fields = array();
		foreach ($params as $key => $val)
		{
			if ($ci->db->field_exists($key, $table))
			{
				$fields[$key] = $val;
				if ($key == 'gender')
				{
					$fieds[$key] = save_gender_format($val);
				}
			}
		}
		return $fields;
	}

}

if (!function_exists('get_order_by'))
{

	function get_order_by($order_code = '')
	{
		$order = "DESC";
		if ($order_code == "1")
		{
			$order = "ASC";
		}
		return $order;
	}

}

/* End of file database_helper.php */
/* Location: ./application/helpers/database_helper.php */