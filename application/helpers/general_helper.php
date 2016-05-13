<?php

/* this function is general function use anywhere */
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('display_product'))
{

	function display_product($query_data = FALSE, $query_row = FALSE, $detail = FALSE)
	{
		$ci = & get_instance();
		$base_source = $ci->config->item('base_source');
		$label = "";

		$row = 0;
		if ($query_row)
		{
			foreach ($query_row->result() as $each_row)
			{
				$row = $each_row->total_row;
			}
		}

		$data = array();
		$data_new = array();
		if ($query_data)
		{
			foreach ($query_data->result() as $result)
			{
				$product_name = $result->product_name;
				$variant_value = $result->variant_value;
				$variant_seq = $result->variant_seq;
				$merchant_seq = $result->merchant_seq;
				$product_discout = $result->product_disc_percent;
				$product_price = $result->product_price;
				$product_sell_price = $result->product_sell_price;
				$product_variant_seq = $result->product_variant_seq;
				$product_stock = $result->product_stock;
				$product_sku = $result->product_sku;
				$product_seq = $result->product_seq;
				$data['product_variant_seq'] = $product_variant_seq;

				if ($detail === TRUE)
				{
					$data_variant['product_variant_seq'] = $product_variant_seq;
					if ($result->variant_seq != '1')
					{
						$data_variant['product_variant_name'] = $result->variant_name;
						$data_variant['product_variant_value'] = $result->variant_value;
					}
					else
					{
						$data_variant['product_variant_name'] = "";
						$data_variant['product_variant_value'] = "";
					}
					$data['product_variant'] = $data_variant;
					$description = $result->description;
					for ($i = 1; $i <= M_PRODUCT_VARIANT_MAX_PIC_IMG; $i++)
					{
						$product_image_{$i} = $result->{"product_image_$i"};
					}
				}
				$product_image_1 = $result->product_image_1;
				if ($result->variant_seq != '1')
				{
					$product_name .= ' - ' . $variant_value;
				}
//				$data['product_variant_seq'] = $product_variant_seq;
				$data['product_seq'] = $product_seq;
				if ($detail === TRUE)
				{
					for ($i = 1; $i <= M_PRODUCT_VARIANT_MAX_PIC_IMG; $i++)
					{
						if ($product_image_{$i} != "")
						{
							$product_image_{$i} = $base_source . PRODUCT_UPLOAD_IMAGE . $merchant_seq . '/' . S_IMAGE_UPLOAD . $product_image_{$i};
						}
						$product_image[] = array('img' => $product_image_{$i});
					}
					$data['product_images'] = $product_image;
					$data['product_description'] = $description;
				}
				else
				{
					$data["product_image_1"] = $base_source . PRODUCT_UPLOAD_IMAGE . $merchant_seq . '/' . S_IMAGE_UPLOAD . $product_image_1;
				}
				$data['product_name'] = $product_name;
				$data['product_discount'] = $product_discout . '%';
				$data['merchant_seq'] = $merchant_seq;
				$data['product_price'] = $product_price;
				$data['product_sell_price'] = $product_sell_price;
				if ($product_stock == 0)
				{
					$label = "Sold Out";
				}
				$data['product_stock'] = $product_stock;
				$data['product_sku'] = $product_sku;
				$data['url_detail'] = base_url() . 'v1/product/detail/' . $product_variant_seq;
				$data['product_label'] = $label;
				$data_new[] = $data;
			}
		}
		return $data_new;
	}

}

if (!function_exists('get_total_row'))
{

	function get_total_row($query_row = FALSE)
	{
		$total_row = '0';
		if ($query_row !== FALSE)
		{
			foreach ($query_row->result() as $row)
			{
				$total_row = $row->total_row;
			}
		}
		return $total_row;
	}

}

if (!function_exists('get_paging_link'))
{

	function get_paging_link($base_url = '', $query_row = FALSE, $per_page = 10, $query_string_segment = 'mulai', $use_page_number = FALSE, $page_query_string = TRUE)
	{
		$ci = & get_instance();
		$total_row = get_total_row($query_row);
		$config['base_url'] = $base_url;
		$config['total_rows'] = $total_row;
		$config['per_page'] = $per_page;
		$config['use_page_numbers'] = $use_page_number;
		$config['page_query_string'] = $page_query_string;
		$config['query_string_segment'] = $query_string_segment;
		$config['num_links'] = 0;

		$ci->pagination->initialize($config);
		$ci->pagination->execute();

		$paging['firstpage'] = $ci->pagination->get_first_link();
		$paging['firstpage_url'] = $ci->pagination->get_first_link_url();
		$paging['firstpage_name'] = $ci->pagination->get_first_link_name();
		$paging['firstpage_value'] = $ci->pagination->get_first_link_value();
		$paging['less'] = $ci->pagination->get_prev_link();
		$paging['less_url'] = $ci->pagination->get_prev_link_url();
		$paging['less_name'] = $ci->pagination->get_prev_link_name();
		$paging['less_value'] = $ci->pagination->get_prev_link_value();
		$paging['more'] = $ci->pagination->get_next_link();
		$paging['more_url'] = $ci->pagination->get_next_link_url();
		$paging['more_name'] = $ci->pagination->get_next_link_name();
		$paging['more_value'] = $ci->pagination->get_next_link_value();
		$paging['lastpage'] = $ci->pagination->get_last_link();
		$paging['lastpage_url'] = $ci->pagination->get_last_link_url();
		$paging['lastpage_name'] = $ci->pagination->get_last_link_name();
		$paging['lastpage_value'] = $ci->pagination->get_last_link_value();

		return $paging;
	}

}

if (!function_exists('post_paging_link'))
{

	function post_paging_link($base_url = '', $query_row = FALSE, $per_page = 10, $query_string_segment = 'mulai', $use_page_number = FALSE, $page_query_string = TRUE)
	{
		$ci = & get_instance();
		$total_row = get_total_row($query_row);
		$config['base_url'] = $base_url;
		$config['total_rows'] = $total_row;
		$config['per_page'] = $per_page;
		$config['use_page_numbers'] = $use_page_number;
		$config['page_query_string'] = $page_query_string;
		$config['query_string_segment'] = $query_string_segment;
		$config['num_links'] = 0;

		$ci->pagination->initialize($config);
		$ci->pagination->execute_post();

		$paging['firstpage'] = $ci->pagination->get_first_link();
		$paging['firstpage_url'] = $ci->pagination->get_first_link_url();
		$paging['firstpage_name'] = $ci->pagination->get_first_link_name();
		$paging['firstpage_value'] = $ci->pagination->get_first_link_value();
		$paging['less'] = $ci->pagination->get_prev_link();
		$paging['less_url'] = $ci->pagination->get_prev_link_url();
		$paging['less_name'] = $ci->pagination->get_prev_link_name();
		$paging['less_value'] = $ci->pagination->get_prev_link_value();
		$paging['more'] = $ci->pagination->get_next_link();
		$paging['more_url'] = $ci->pagination->get_next_link_url();
		$paging['more_name'] = $ci->pagination->get_next_link_name();
		$paging['more_value'] = $ci->pagination->get_next_link_value();
		$paging['lastpage'] = $ci->pagination->get_last_link();
		$paging['lastpage_url'] = $ci->pagination->get_last_link_url();
		$paging['lastpage_name'] = $ci->pagination->get_last_link_name();
		$paging['lastpage_value'] = $ci->pagination->get_last_link_value();

		return $paging;
	}

}

if (!function_exists('get_limit'))
{

	function get_limit($page, $per_page = 10)
	{
		if ($page === NULL)
		{
			$page = 1;
		}
		$limit = array();
		$limit[0] = ($page - 1) * $per_page;
		$limit[1] = $per_page;
		return $limit;
	}

}

if (!function_exists('get_offset'))
{

	function get_offset($offset, $per_page = 10)
	{
		if ($offset === NULL)
		{
			$offset = 0;
		}
		$limit = array();
		$limit[0] = $offset;
		$limit[1] = $per_page;
		return $limit;
	}

}

if (!function_exists('get_tree_view_category'))
{

	function get_tree_view_category()
	{
		$result = array();
		$ci = & get_instance();
		$query = $ci->Product_model->get_tree_view_category_all();
		$query_row = $query->query_row;
		$query_data = $query->query_data;
		$row = 0;
		if ($query_row)
		{
			foreach ($query_row->result() as $each_row)
			{
				$row = $each_row->total_row;
			}
		}
		if ($query_data)
		{
			foreach ($query_data->result() as $each_query_data)
			{
				$result[$each_query_data->parent_seq][] = $each_query_data;
			}
		}
		return $result;
	}

}

if (!function_exists('get_category_seq_self_parent'))
{

	function get_category_seq_self_parent(&$categories, $datas, $seq, $parent = 0, $p_level = 0, $get_self_seq = TRUE)
	{
		if ($get_self_seq)
		{
			$categories[] = $seq;
		}
		foreach ($datas as $each_datas)
		{
			foreach ($each_datas as $each_datas_detail)
			{
				if (isset($each_datas_detail->seq))
				{
					if ($each_datas_detail->seq == $seq)
					{
						if ($each_datas_detail->parent_seq != 0)
							$categories[] = $each_datas_detail->parent_seq;
						get_category_seq_self_parent($categories, $datas, $each_datas_detail->parent_seq, 0, 0, FALSE);
						return false;
					}
				}
			}
		}
	}

}

if (!function_exists('get_category_seq_self_child'))
{

	function get_category_seq_self_child(&$categories, $datas, $parent = 0, $p_level = 0, $get_self_seq = TRUE)
	{
		$parent_level = "";
		if ($get_self_seq)
		{
			$categories[] = $parent;
		}
		if (isset($datas[$parent]))
		{
			foreach ($datas[$parent] as $vals)
			{
				if ($vals->level == 1)
				{
					$parent_level = $vals->seq;
					$p_level = $vals->seq;
				}
				$categories[] = $vals->seq;
				get_category_seq_self_child($categories, $datas, $vals->seq, $parent_level, FALSE);
			}
//            return $seq;
		}
	}

}

if (!function_exists('add_current_url_with_query_string_category'))
{

	function add_current_url_with_query_string_category($value = '', $parameter_name = PARAMETER_CATEGORY_LIST, $exploder_parameter = ',', $exploder_parent_child = '-')
	{
		//this function is not only add the parameter but it also remove parent data previous
		$output_query_string = '';
		$output = '';
		$query_string_parameter_name = array();
		if ($value != '')
		{
			$exploder_for_parent_child_param = explode($exploder_parent_child, $value);
			$parent_param = $exploder_for_parent_child_param[0];
			$child_param = $exploder_for_parent_child_param[1];
			$query_string = $_SERVER['QUERY_STRING'];
			parse_str($query_string, $query_string_array);
			if (isset($query_string_array[$parameter_name]))
			{
				if ($query_string_array[$parameter_name] == "")
				{
					redirect(current_url_with_query_string(array(PARAMETER_CATEGORY_LIST)));
				}
				$parameter_value_list = explode($exploder_parameter, $query_string_array[$parameter_name]);

				//query_string_attribute_cleaning;
				foreach ($parameter_value_list as $each_parameter_value_list)
				{
					$exploder_for_parent_child = explode($exploder_parent_child, $each_parameter_value_list);
					$parent_uri = $exploder_for_parent_child[0];
					$child_uri = $exploder_for_parent_child[1];
					$query_string_parameter_name[$parent_uri][] = $child_uri;
				}
				//make query_string_unique for double value
				foreach ($query_string_parameter_name as $parent_uri => $each_query_string_parameter_name)
				{
					$query_string_parameter_name[$parent_uri] = array_unique($query_string_parameter_name[$parent_uri]);
				}
				//if uri has child value 0 then set parent url value to 0
				foreach ($query_string_parameter_name as $parent_uri => $each_query_string_parameter_name)
				{
					if (in_array('0', $query_string_parameter_name[$parent_uri]))
					{
						$query_string_parameter_name[$parent_uri] = array('0');
					}
				}
				if ($child_param == 0)
				{
					foreach ($query_string_parameter_name as $parent_uri => $each_query_string_parameter_name)
					{
						foreach ($each_query_string_parameter_name as $child_uri)
						{
							if ($parent_param != $parent_uri)
							{
								$output_query_string .= $parent_uri . $exploder_parent_child . $child_uri . $exploder_parameter;
							}
						}
					}
					$output_query_string .= $parent_param . $exploder_parent_child . $child_param;
				}
				else
				{
					$is_parent_uri_equal_parent_param_n_child_uri_equal_child_param = false; //unset for child same
					foreach ($query_string_parameter_name as $parent_uri => $each_query_string_parameter_name)
					{
						foreach ($each_query_string_parameter_name as $child_uri)
						{
							if ($parent_param != $parent_uri)
							{
								$output_query_string .= $parent_uri . $exploder_parent_child . $child_uri . $exploder_parameter;
							}
							else
							{
								if ($child_uri != '0')
								{
									if ($child_uri != $child_param)
									{
										$output_query_string .= $parent_uri . $exploder_parent_child . $child_uri . $exploder_parameter;
									}
									else
									{
										$is_parent_uri_equal_parent_param_n_child_uri_equal_child_param = true;
									}
								}
							}
						}
					}
					if (!$is_parent_uri_equal_parent_param_n_child_uri_equal_child_param)
					{
						$output_query_string .= $parent_param . $exploder_parent_child . $child_param;
					}
					else
					{
						$output_query_string = rtrim($output_query_string, $exploder_parameter);
					}
				}
				if ($output_query_string != "")
				{
					$output .= current_url_with_query_string(array($parameter_name), array($parameter_name => $output_query_string));
				}
				else
				{
					$output .= current_url_with_query_string(array($parameter_name));
				}
			}
			else
			{
				$output = current_url_with_query_string();
				$output .= ($_SERVER['QUERY_STRING'] != '') ? '&' : '?';
				$output .= $parameter_name . '=' . $value;
			}
		}
		else
		{
			$output .= current_url_with_query_string();
		}
		return $output;
	}

}

if (!function_exists('add_current_url_with_query_string_special'))
{

	function add_current_url_with_query_string_special($value = '', $parameter_name = PARAMETER_CATEGORY_ATTRIBUTE, $exploder_parameter = ',', $exploder_parent_child = '-')
	{
		//this function is not only add the parameter but it also remove parent data previous
		$output_query_string = '';
		$output = '';
		$query_string_parameter_name = array();
		if ($value != '')
		{
			$exploder_for_parent_child_param = explode($exploder_parent_child, $value);
			$parent_param = $exploder_for_parent_child_param[0];
			$child_param = $exploder_for_parent_child_param[1];
			$query_string = $_SERVER['QUERY_STRING'];
			parse_str($query_string, $query_string_array);
			if (isset($query_string_array[$parameter_name]))
			{
				if ($query_string_array[$parameter_name] == "")
				{
					redirect(current_url_with_query_string(array(PARAMETER_CATEGORY_ATTRIBUTE)));
				}
				$parameter_value_list = explode($exploder_parameter, $query_string_array[$parameter_name]);
				//query_string_attribute_cleaning;
				foreach ($parameter_value_list as $each_parameter_value_list)
				{
					$exploder_for_parent_child = explode($exploder_parent_child, $each_parameter_value_list);
					$parent_uri = $exploder_for_parent_child[0];
					$child_uri = $exploder_for_parent_child[1];
					$query_string_parameter_name[$parent_uri][] = $child_uri;
				}
				//make query_string_unique for double value
				foreach ($query_string_parameter_name as $parent_uri => $each_query_string_parameter_name)
				{
					$query_string_parameter_name[$parent_uri] = array_unique($query_string_parameter_name[$parent_uri]);
				}
				//if uri has child value 0 then set parent url value to 0
				foreach ($query_string_parameter_name as $parent_uri => $each_query_string_parameter_name)
				{
					if (in_array('0', $query_string_parameter_name[$parent_uri]))
					{
						$query_string_parameter_name[$parent_uri] = array('0');
					}
				}
				if ($child_param == 0)
				{
					foreach ($query_string_parameter_name as $parent_uri => $each_query_string_parameter_name)
					{
						foreach ($each_query_string_parameter_name as $child_uri)
						{
							if ($parent_param != $parent_uri)
							{
								$output_query_string .= $parent_uri . $exploder_parent_child . $child_uri . $exploder_parameter;
							}
						}
					}
					$output_query_string .= $parent_param . $exploder_parent_child . $child_param;
				}
				else
				{
					$is_parent_uri_equal_parent_param_n_child_uri_equal_child_param = false; //unset for child same
					foreach ($query_string_parameter_name as $parent_uri => $each_query_string_parameter_name)
					{
						foreach ($each_query_string_parameter_name as $child_uri)
						{
							if ($parent_param != $parent_uri)
							{
								$output_query_string .= $parent_uri . $exploder_parent_child . $child_uri . $exploder_parameter;
							}
							else
							{
								if ($child_uri != '0')
								{
									if ($child_uri != $child_param)
									{
										$output_query_string .= $parent_uri . $exploder_parent_child . $child_uri . $exploder_parameter;
									}
									else
									{
										$is_parent_uri_equal_parent_param_n_child_uri_equal_child_param = true;
									}
								}
							}
						}
					}
					if (!$is_parent_uri_equal_parent_param_n_child_uri_equal_child_param)
					{
						$output_query_string .= $parent_param . $exploder_parent_child . $child_param;
					}
					else
					{
						$output_query_string = rtrim($output_query_string, $exploder_parameter);
					}
				}
				if ($output_query_string != "")
				{
					$output .= current_url_with_query_string(array($parameter_name), array($parameter_name => $output_query_string));
				}
				else
				{
					$output .= current_url_with_query_string(array($parameter_name));
				}
			}
			else
			{
				$output = current_url_with_query_string();
				$output .= ($_SERVER['QUERY_STRING'] != '') ? '&' : '?';
				$output .= $parameter_name . '=' . $value;
			}
		}
		else
		{
			$output .= current_url_with_query_string();
		}
		return $output;
	}

}

if (!function_exists('current_url_with_query_string'))
{

	function current_url_with_query_string($removed_parameter = array(), $added_parameter = array(), $rawurlencode = true)
	{
		$ret_url = current_url();
		$result_query_string = '';
		$counter = 0;
		parse_str($_SERVER['QUERY_STRING'], $query_string);
		foreach ($query_string as $key => $each_query_string)
		{
			if (!in_array($key, $removed_parameter))
			{
				if ($counter > 0)
				{
					$result_query_string .= '&';
				}
				if ($rawurlencode == true)
				{
					$result_query_string .= $key . '=' . rawurlencode($each_query_string);
				}
				else
				{
					$result_query_string .= $key . '=' . $each_query_string;
				}
				$counter++;
			}
		}
		$ret_url .= ($counter > 0) ? ('?' . $result_query_string) : $result_query_string;
		foreach ($added_parameter as $parameter_name => $parameter_value)
		{
			if ($counter > 0 != '')
			{
				$ret_url .= '&';
			}
			else
			{
				$ret_url .= '?';
			}
			if ($rawurlencode == true)
			{
				$ret_url .= $parameter_name . '=' . rawurlencode($parameter_value);
			}
			else
			{
				$ret_url .= $parameter_name . '=' . $parameter_value;
			}
		}
		return $ret_url;
	}

}

if (!function_exists('get_data_product_category_seq_array'))
{

	function get_data_product_category_seq_array($category_tree, $product_category_seq = array())
	{
		$data_product_category_seq = array();
		foreach ($category_tree as $each_category_tree)
		{
			foreach ($each_category_tree as $each_data_category_tree)
			{
				if (isset($each_data_category_tree->seq))
				{
					if (in_array($each_data_category_tree->seq, $product_category_seq))
					{
						$data_product_category_seq[$each_data_category_tree->seq] = $each_data_category_tree;
					}
				}
			}
		}
		return $data_product_category_seq;
	}

}
if (!function_exists('sort_level_category'))
{

	function sort_level_category($group_of_category, $order = 'ASC')
	{
		$new_group_of_category_index = array();
		$new_group_of_category_index_level = array();

		foreach ($group_of_category as $key => $each_group_of_category)
		{
			$new_group_of_category_index_level[$each_group_of_category->level] = $each_group_of_category;
		}
		if ($order == 'ASC')
		{
			ksort($new_group_of_category_index_level);
		}
		elseif ($orde == 'DESC')
		{
			krsort($new_group_of_category_index_level);
		}
		else
		{
			exit('Unknown order');
		}
		foreach ($new_group_of_category_index_level as $each_new_group_of_category_index_level)
		{
			$new_group_of_category_index[$each_new_group_of_category_index_level->seq] = $each_new_group_of_category_index_level;
		}
		return $new_group_of_category_index;
	}

}

if (!function_exists('buildtree'))
{

	function buildtree($src_arr, $parent_seq = 0, $tree = array(), $result = array())
	{
		foreach ($src_arr as $idx => $row)
		{
			if ($row->parent_seq == $parent_seq)
			{
				foreach ($row as $key_row => $each_row)
				{
					$tree[$key_row] = $row->$key_row;
				}
				unset($src_arr[$idx]);
				$tree['children'] = buildtree($src_arr, $row->seq);
				$result[] = $tree;
			}
		}
		return $result;
	}

}

if (!function_exists('generate_voucher'))
{

	function generate_voucher($params = '')
	{
		$ci = & get_instance();
		if (!isset($params->total_order))
		{
			$params->total_order = 0;
		}
		$ci->load->model("V1/Voucher_model");
		$query = $ci->Voucher_model->get_promo($params);
		$query_data = $query->query_data;
//		$row = get_row_query($query);
		if ($query_data)
		{
			foreach ($query_data->result() as $result)
			{
				$promo_seq = $result->seq;
				$active_date = date('Y-m-d');

				$date_create = date_create($active_date);
				$exp_days = $result->exp_days;
				date_add($date_create, date_interval_create_from_date_string($exp_days . " days"));
				$date = date_format($date_create, "Y-m-d");

				$select = new stdClass();
				$select->seq = $promo_seq;
				$select->member_seq = $params->seq;
				$select->active_date = $active_date;
				$select->nominal = $result->nominal;
				$select->trx_use_amt = $result->trx_use_amt;
				$select->exp_date = $date;

				$voucher = '';
				if ($result->type == VOUCHER_TYPE_AUTOMATIC)
				{
					$voucher = generate_random_text(17, true);
					$select->code = NODE_REGISTRATION_MEMBER . $voucher;
					$ci->Voucher_model->save_add_voucher($select);
				}
				else
				{
					$ci->Voucher_model->save_update_voucher($select);
				}
				return $voucher;
			}
		}
	}

}
if (!function_exists('display_gender_format'))
{

	function display_gender_format($gender)
	{
		$retval = 'male';
		if (trim(strtolower($gender)) == 'male')
		{
			$retval = 'male';
		}
		if (trim(strtolower($gender)) == 'm')
		{
			$retval = 'male';
		}

		if (trim(strtolower($gender)) == 'female')
		{
			$retval = 'female';
		}
		if (trim(strtolower($gender)) == 'f')
		{
			$retval = 'female';
		}
		return $retval;
	}

}

if (!function_exists('save_gender_format'))
{

	function save_gender_format($gender)
	{
		$retval = 'm';
		if (trim(strtolower($gender)) == 'male')
		{
			$retval = 'm';
		}
		if (trim(strtolower($gender)) == 'm')
		{
			$retval = 'm';
		}

		if (trim(strtolower($gender)) == 'female')
		{
			$retval = 'f';
		}
		if (trim(strtolower($gender)) == 'f')
		{
			$retval = 'f';
		}
		return $retval;
	}

}

if (!function_exists('check_token'))
{

	function check_token($header_request = '')
	{
		$ci = & get_instance();
		$token = NULL;
		if ($token === NULL && method_exists($ci->input, 'post'))
		{
			$token = $ci->input->post('token');
			$member_seq = $ci->input->post('member_seq');
		}
		if ($token === NULL && method_exists($ci->input, 'get'))
		{
			$token = $ci->input->get('token');
			$member_seq = $ci->input->get('member_seq');
		}
		if ($token === NULL && file_get_contents('php://input') != '')
		{
			$inputjson = file_get_contents('php://input');
			$input = json_decode($inputjson);
			$get_token = array($header_request, 'token');
			$token = check_current_exit($input, $get_token);
			$get_member_seq = array($header_request, 'member_seq');
			$member_seq = check_current_exit($input, $get_member_seq);
		}

		$match = $ci->Member_model->check_Mytoken($token, $member_seq);
		$data = new stdClass;
		if ($match == FALSE)
		{
			$code = 406;
			$message = $ci->status_code->getMessageForCode($code);
			$data_find = array('code' => $code, 'message' => $message, 'data' => $data);
			echo $ci->response->show_custom($data_find);
			exit();
		}
		return array('member_token' => $token, 'member_seq' => $member_seq);
	}

}

/* End of file general_helper.php */
/* Location: ./application/helpers/general_helper.php */