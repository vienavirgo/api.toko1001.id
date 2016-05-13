<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products_category extends APITOKO1001_Controller {

	var $base_source = '';
	var $category_seq;
	var $price_min;
	var $price_max;
	var $order; // 1 = produk terbaru ke terlama, 2 = produk terlama ke terbaru, 3 = harga mahal ke murah, 4 = harga murah ke mahal
	var $attribute_list = array(); //this attribute_list consist of unique value and 0 will replace all its unique value for its param
	var $combination_attribute_value = array(); //this field is used for combination attribute list
	var $category_list = array();
	var $combination_category_value = array();
	var $product_seq = array();
	var $search;
	var $offset;
	var $paging_data_product_category;
	var $product_name_variant_seq;
	var $tree_view_category;
	var $seq_self_parent_category;
	var $seq_self_child_category;

	function __construct()
	{
		parent::__construct();
		$this->load->model('V1/Product_model');
		$this->load->model('V1/Category_model');
		$this->base_source = $this->config->item('base_source');
		$this->_initialize();
	}

	public function get_product_name_variant_seq()
	{
		return $this->product_name_variant_seq;
	}

	public function get_base_source()
	{
		return $this->base_source;
	}

	public function get_category_seq()
	{
		return $this->category_seq;
	}

	public function get_price_min()
	{
		return $this->price_min;
	}

	public function get_price_max()
	{
		return $this->price_max;
	}

	public function get_order()
	{
		return $this->order;
	}

	public function get_attribute_list()
	{
		return $this->attribute_list;
	}

	public function get_category_list()
	{
		return $this->category_list;
	}

	public function get_combination_attribute_value()
	{
		return $this->combination_attribute_value;
	}

	public function get_combination_category_value()
	{
		return $this->combination_category_value;
	}

	public function get_offset()
	{
		return $this->offset;
	}

	public function get_product_seq()
	{
		return $this->product_seq;
	}

	public function get_search()
	{
		return $this->search;
	}

	private function _set_catetory_seq()
	{
		$product_name_variant_seq = $this->uri->segment('4');
		$this->product_name_variant_seq = $product_name_variant_seq;
		if ($product_name_variant_seq == ALL_CATEGORY)
		{
			$this->category_seq = '0';
		}
		else
		{
			$exploder_product_category_seq = explode('-', $product_name_variant_seq);
			$product_category_seq_unclear = end($exploder_product_category_seq);
			$filter_category_seq = ltrim($product_category_seq_unclear, CATEGORY);
			$message = "Category seq error";
			if ($filter_category_seq === NULL OR $filter_category_seq === "")
			{
				$message = "Category seq is not set";
				set_error_response($message);
				return;
			}
			is_not_digit_set_error_response_exit($filter_category_seq, "category seq is not acceptable");
			$this->category_seq = $filter_category_seq;
		}
	}

	private function _set_price_range()
	{
		$filter_price_range = $this->input->get(PRICE_RANGE, TRUE);
		if ($filter_price_range === NULL OR $filter_price_range === "")
		{
			$price_min = "";
			$price_max = "";
			$this->price_min = $price_min;
			$this->price_max = $price_max;
			return;
		}
		$price_range = explode(",", $filter_price_range, 2);

		$price_min = isset($price_range[0]) ? $price_range[0] : '';
		$price_max = isset($price_range[1]) ? $price_range[1] : '';

		is_not_digit_set_error_response_exit($price_min, "price min in price range not acceptable");
		is_not_digit_set_error_response_exit($price_max, "price max in price range not acceptable");
		$this->price_min = $price_min;
		$this->price_max = $price_max;
	}

	private function _set_order()
	{
		$filter_order = $this->input->get(ORDER_CATEGORY, TRUE);
		if ($filter_order === NULL OR $filter_order === "")
		{
			$filter_order = "";
			$this->order = $filter_order;
			return;
		}
//		is_not_digit_set_error_response_exit($filter_order, "order not acceptable");
		$this->order = $filter_order;
	}

	private function _set_search()
	{
		$this->search = $this->input->get(SEARCH, TRUE);
	}

	private function _set_attribute_list()
	{
		$delimiter_each_attribute_list = ',';
		$delimiter_category_attribute = '-';
		$attribute_list = array();
		$attribute_list_without_0 = array();

		$param_filter_attribute_list = $this->input->get(PARAMETER_CATEGORY_ATTRIBUTE, TRUE);
		if ($param_filter_attribute_list === NULL OR $param_filter_attribute_list === "")
		{
			$param_filter_attribute_list = "";
			$this->attribute_list = $param_filter_attribute_list;
			return;
		}

		$filter_attribute_list = rtrim($param_filter_attribute_list, $delimiter_each_attribute_list);
		$parameter_value_list = explode($delimiter_each_attribute_list, $filter_attribute_list);
		foreach ($parameter_value_list as $each_parameter_value_list)
		{
			$exploder_category_attribute = explode($delimiter_category_attribute, $each_parameter_value_list);
			$category = $exploder_category_attribute[0];
			$attribute = $exploder_category_attribute[1];
			$attribute_list[$category][] = $attribute;
		}

		//make unique for double value
		foreach ($attribute_list as $category => $each_attribute_list)
		{
			$attribute_list[$category] = array_unique($attribute_list[$category]);
		}

		//if uri has child value 0 then set parent url value to 0
		foreach ($attribute_list as $category => $each_query_string_parameter_name)
		{
			if (in_array('0', $attribute_list[$category]))
			{
				$attribute_list[$category] = array('0');
			}
		}
		$this->attribute_list = $attribute_list;

		//if uri has child value 0 then set parent 
		foreach ($attribute_list as $category => $each_query_string_parameter_name)
		{
			if (!in_array('0', $attribute_list[$category]))
			{
				$attribute_list_without_0[$category] = $attribute_list[$category];
			}
		}

		$this->combination_attribute_value = generate_combinations($attribute_list_without_0); //combination of attribute value
		$this->_set_product_seq_from_attribute();
	}

	private function _set_category_list()
	{
		$delimiter_each_category_list = ',';
		$delimiter_category_category = '-';
		$category_list = array();

		$param_filter_category_list = $this->input->get(PARAMETER_CATEGORY_LIST, TRUE);
		if ($param_filter_category_list === NULL OR $param_filter_category_list === "")
		{
			$param_filter_category_list = "";
			$this->category_list = $param_filter_category_list;
			return;
		}

		$filter_category_list = rtrim($param_filter_category_list, $delimiter_each_category_list);
		$parameter_value_list = explode($delimiter_each_category_list, $filter_category_list);
		var_dump($parameter_value_list);
		foreach ($parameter_value_list as $each_parameter_value_list)
		{
			$exploder_category = explode($delimiter_category_category, $each_parameter_value_list);
			$category = $exploder_category[1];
			$attribute = $exploder_category[0];
			$category_list[$category][] = $attribute;
			$category_list_sec[$attribute][] = $category;
		}

		if ($category_list_sec != "")
		{
			foreach ($category_list_sec as $category => $each_category_list)
			{
				$category_list[$attribute] = array_unique($category_list_sec[$attribute]);
			}
		}
		else
		{
			foreach ($category_list as $category => $each_attribute_list)
			{
				$category_list[$category] = array_unique($category_list[$category]);
			}
		}
		$this->category_list = $category_list;
		$this->combination_category_value = generate_combinations($this->category_list);
	}

	private function _set_offset()
	{
		$filter_offset = $this->input->get(START_OFFSET);
		if ($filter_offset === NULL OR $filter_offset === "")
		{
			$filter_offset = "0";
			$this->offset = $filter_offset;
			return;
		}
		is_not_digit_set_error_response_exit($filter_offset, "offset not acceptable");
		$this->offset = $filter_offset;
	}

	private function _initialize()
	{
		$this->_set_catetory_seq();
		$this->_set_price_range();
		$this->_set_order();
		$this->_set_attribute_list();
		$this->_set_category_list();
		$this->_set_search();
		$this->_set_offset();
	}

	private function _set_product_seq_from_attribute()
	{
		$combination_attribute_value = $this->get_combination_attribute_value();
		if (!empty($combination_attribute_value[0]))
		{
			$this->product_seq = $this->Category_model->get_product_seq($combination_attribute_value);
		}
	}

	private function _display_category_product()
	{
		$tree_view_category = get_tree_view_category();
		$seq_self_parent_category = array();
		$seq_self_child_category = array();
		$list_of_product_seq = '';
		$list_of_seq_self_child_category = '';
		$list_of_product_seq_array = array();

		$category_list = $this->category_list;
		if ($category_list == "")
		{
			get_category_seq_self_parent($seq_self_parent_category, $tree_view_category, $this->get_category_seq()); // get self and parent
			get_category_seq_self_child($seq_self_child_category, $tree_view_category, $this->get_category_seq()); // get self and parent

			$this->tree_view_category = $tree_view_category;
			$this->seq_self_parent_category = $seq_self_parent_category;
			$this->seq_self_child_category = $seq_self_child_category;
			if (count($seq_self_child_category) > 0 && $this->get_category_seq() != 0)
			{
				$list_of_seq_self_child_category = "'" . implode("','", $seq_self_child_category) . "'";
			}
			//take product_seq, seq_self_child_category, search, price, order
			$product_seq = $this->get_product_seq();
			if (count($product_seq) > 0)
			{
				foreach ($product_seq as $each_product_seq)
				{
					$list_of_product_seq_array[] = $each_product_seq->product_seq;
				}
				$list_of_product_seq = "'" . implode("','", $list_of_product_seq_array) . "'";
			}
		}
		else
		{
			foreach ($category_list as $value)
			{
				$list = implode('","', $value);
			}
			get_category_seq_self_parent($seq_self_parent_category, $tree_view_category, $list); // get self and 
			get_category_seq_self_child($seq_self_child_category, $tree_view_category, $list); // get self and parent

			$this->seq_self_parent_category = $seq_self_parent_category;
			$this->seq_self_child_category = $seq_self_child_category;
			if (count($seq_self_child_category) > 0)
			{
				$list_of_seq_self_child_category = "'" . implode("','", $seq_self_child_category) . "'";
			}

			$product_seq = $this->get_product_seq();
			if (count($product_seq) > 0)
			{
				foreach ($product_seq as $each_product_seq)
				{
					//$list_of_product_seq_array[] = $each_product_seq->seq;
					$list_of_product_seq_array[] = $each_product_seq->product_seq;
				}
				$list_of_product_seq = "'" . implode("','", $list_of_product_seq_array) . "'";
			}
		}

		$search = $this->get_search();
		$price_min = $this->get_price_min();
		$price_max = $this->get_price_max();
		$order = $this->get_order();
		$offset = $this->get_offset();
		$limit = get_offset($offset);
		$limit_1 = $limit[0];
		$limit_2 = $limit[1];
		$query_product = $this->Product_model->get_product_category($list_of_product_seq, $list_of_seq_self_child_category, $search, $price_min, $price_max, $order, $limit_1, $limit_2);

		$query_product_row = $query_product->query_row;
		$query_product_data = $query_product->query_data;

		$removed_parameter = array(START_OFFSET);
		$base_url = current_url_with_query_string($removed_parameter);

		$this->paging_data_product_category = get_paging_link($base_url, $query_product_row);
				
		$data_prod_category = display_product($query_product_data, $query_product_row);
		return $data_prod_category;
	}

	private function _display_filter_name($attribute_name = '', $attribute_display_name = '')
	{
		return ($attribute_display_name == '' OR $attribute_display_name == '-') ? $attribute_name : $attribute_display_name;
	}

	private function _attribute_category()
	{
		$category_seq = $this->get_category_seq();
		if ($category_seq != "")
		{ //if not all category
			$seq_self_parent_category = $this->seq_self_parent_category;
			if ($seq_self_parent_category != NULL)
			{
				$category_id_self_parent = "'" . implode("','", $seq_self_parent_category) . "'";
			}
			$query_attribute_name = $this->Category_model->get_filter_attribute_name($category_id_self_parent);
			$query_data_attribute_name = $query_attribute_name->query_data;
			$query_attribute_value = $this->Category_model->get_filter_attribute_value($category_id_self_parent);
			$query_data_attribute_value = $query_attribute_value->query_data;
			$row = get_row_query($query_attribute_name);
			$data_attr = array();

			if ($query_data_attribute_name->num_rows() != 0)
			{
				foreach ($query_data_attribute_name->result() as $each_filter_name)
				{
					$attribute_name_display = $this->_display_filter_name($each_filter_name->attribute_name, $each_filter_name->attribute_display_name);
					$name_attribute_category_attribute_seq = $each_filter_name->attribute_category_attribute_seq;

					$data_items['name'] = 'Semua';
					$data_items['url'] = add_current_url_with_query_string_special($name_attribute_category_attribute_seq . '-' . '0');
					$data_items['parameter_name'] = PARAMETER_CATEGORY_ATTRIBUTE;
					$data_items['cat_value'] = '0';
					$data_items['value'] = $name_attribute_category_attribute_seq . '-0';
					$data_all_items[] = $data_items;

					foreach ($query_data_attribute_value->result() as $each_filter_value)
					{
						$value_attribute_category_attribute_seq = $each_filter_value->attribute_category_attribute_seq;
						$attribute_value_seq = $each_filter_value->attribute_value_seq;
						$attribute_value_value = $each_filter_value->attribute_value_value;
						if ($name_attribute_category_attribute_seq == $value_attribute_category_attribute_seq)
						{
							$data_items['name'] = $attribute_value_value;
							$data_items['url'] = add_current_url_with_query_string_special($name_attribute_category_attribute_seq . '-' . $attribute_value_seq);
							$data_items['parameter_name'] = PARAMETER_CATEGORY_ATTRIBUTE;
							$data_items['cat_value'] = $attribute_value_seq;
							$data_items['value'] = $name_attribute_category_attribute_seq . '-' . $attribute_value_seq;
							$data_all_items[] = $data_items;
						}
					}
					$data_attr[] = array(
						'name' => $attribute_name_display,
						'url' => add_current_url_with_query_string_special($name_attribute_category_attribute_seq . '-' . '0'),
						'parameter_name' => PARAMETER_CATEGORY_ATTRIBUTE,
						'cat_value' => $each_filter_name->attribute_seq,
						'value' => $name_attribute_category_attribute_seq,
						'items' => $data_all_items);
					$data_items = array();
					$data_all_items = array();
				}
			}
			else
			{
				$query_category_name = $this->Category_model->get_filter_category_name($category_id_self_parent, $category_seq);
				$query_data_category_name = $query_category_name->query_data;

				if ($query_data_category_name->num_rows() > 1)
				{
					foreach ($query_data_category_name->result() as $each_filter_name)
					{
						$category_name_display = $this->_display_filter_name($each_filter_name->pc_name);

						$data_items['name'] = 'Semua';
						$data_items['url'] = add_current_url_with_query_string_category($each_filter_name->pc_seq . '-' . '0');
						$data_items['parameter_name'] = PARAMETER_CATEGORY_LIST;
						$data_items['cat_value'] = '0';
						$data_items['value'] = $each_filter_name->pc_seq . '-0';
						$data_all_items[] = $data_items;

						$query_category_value = $this->Category_model->get_filter_category_name($each_filter_name->pc_seq, $category_seq);
						$query_data_category_value = $query_category_value->query_data;

						foreach ($query_data_category_value->result() as $each_filter_value)
						{
							$category_value_seq = $each_filter_value->pc_seq;
							$category_value_value = $each_filter_value->pc_name;

							$data_items['name'] = $category_value_value;
							$data_items['url'] = add_current_url_with_query_string_category($each_filter_name->pc_seq . '-' . $category_value_seq);
							$data_items['parameter_name'] = PARAMETER_CATEGORY_LIST;
							$data_items['cat_value'] = $category_value_seq;
							$data_items['value'] = $each_filter_name->pc_seq . '-' . $category_value_seq;
							$data_all_items[] = $data_items;
						}

						$data_attr[] = array(
							'name' => $category_name_display,
							'url' => add_current_url_with_query_string_category($each_filter_name->pc_seq . '-' . '0'),
							'parameter_name' => PARAMETER_CATEGORY_LIST,
							'cat_value' => 0,
							'value' => $each_filter_name->pc_seq,
							'items' => $data_all_items);
						$data_items = array();
						$data_all_items = array();
					}
				}
				else
				{
					foreach ($query_data_category_name->result() as $each_filter_name)
					{
						$query_category_value = $this->Category_model->get_filter_category_name($each_filter_name->pc_seq, $category_seq);
						$query_data_category_value = $query_category_value->query_data;

						if ($query_data_category_value->num_rows() > 0)
						{
							$category_name_display = $this->_display_filter_name($each_filter_name->pc_name);

							$data_items['name'] = 'Semua';
							$data_items['url'] = add_current_url_with_query_string_category($each_filter_name->pc_seq . '-' . '0');
							$data_items['parameter_name'] = PARAMETER_CATEGORY_LIST;
							$data_items['cat_value'] = '0';
							$data_items['value'] = $each_filter_name->pc_seq . '-0';
							$data_attr[] = $data_items;

							foreach ($query_data_category_value->result() as $each_filter_value)
							{
								$category_value_seq = $each_filter_value->pc_seq;
								$category_value_value = $each_filter_value->pc_name;

								$data_items['name'] = $category_value_value;
								$data_items['url'] = add_current_url_with_query_string_category($each_filter_name->pc_seq . '-' . $category_value_seq);
								$data_items['parameter_name'] = PARAMETER_CATEGORY_LIST;
								$data_items['cat_value'] = $category_value_seq;
								$data_items['value'] = $each_filter_name->pc_seq . '-' . $category_value_seq;
								$data_attr[] = $data_items;
							}
						}
					}
				}
			}
			$attribute_category = array('items' => $data_attr,
				'submit_url' => current_url_with_query_string(),);

			return $attribute_category;
		}
	}

	private function _banner()
	{
		$category_seq = $this->get_category_seq();
		$type = "";
		$data = array();
		$row = 0;
		if ($category_seq != 0)
		{
			$query_category_image = $this->Category_model->get_thumbs_category_img($category_seq);
			$query_data_category_image = $query_category_image->query_data;
			$row = get_row_query($query_category_image);
			if ($query_data_category_image)
			{
				foreach ($query_data_category_image->result() as $each_category_image)
				{
					$each_data['banner_image'] = $this->config->item('base_source') . ADV_UPLOAD_IMAGE . $each_category_image->banner_image;
					$each_data['banner_image_url'] = $each_category_image->banner_image_url;
					$each_data['type'] = $type;
					$data_banner = $each_data;
				}
			}
		}
		return $data_banner;
	}

	private function _filter()
	{
		/* 		$price = array();
		  for ($price_range = 0; $price_range <= 10000000; $price_range += 1000000)
		  {
		  $price_attr['value'] = price_range;
		  $price_attr['value'] = price_range;
		  } */

		$data_filter = array(
			array('name' => 'Produk terbaru ke terlama',
				'url' => current_url_with_query_string(array(ORDER_CATEGORY, START_OFFSET), array(ORDER_CATEGORY => NEW_TO_OLD_PRODUCT)),
				'parameter_name' => ORDER_CATEGORY,
				'value' => NEW_TO_OLD_PRODUCT,
			),
			array('name' => 'Produk terlama ke terbaru',
				'url' => current_url_with_query_string(array(ORDER_CATEGORY, START_OFFSET), array(ORDER_CATEGORY => OLD_TO_NEW_PRODUCT)),
				'parameter_name' => ORDER_CATEGORY,
				'value' => OLD_TO_NEW_PRODUCT,
			),
			array('name' => 'Harga mahal ke murah',
				'url' => current_url_with_query_string(array(ORDER_CATEGORY, START_OFFSET), array(ORDER_CATEGORY => PRICE_EXPENSIVE_TO_CHEAP)),
				'parameter_name' => ORDER_CATEGORY,
				'value' => PRICE_EXPENSIVE_TO_CHEAP,
			),
			array('name' => 'Harga murah ke mahal',
				'url' => current_url_with_query_string(array(ORDER_CATEGORY, START_OFFSET), array(ORDER_CATEGORY => PRICE_CHEAP_TO_EXPENSIVE)),
				'parameter_name' => ORDER_CATEGORY,
				'value' => PRICE_CHEAP_TO_EXPENSIVE,
			),
			array('name' => 'Diskon terbesar ke terkecil',
				'url' => current_url_with_query_string(array(ORDER_CATEGORY, START_OFFSET), array(ORDER_CATEGORY => BIGGEST_DISCOUNT_TO_SMALL)),
				'parameter_name' => ORDER_CATEGORY,
				'value' => BIGGEST_DISCOUNT_TO_SMALL,
			),
			array('name' => 'Diskon terkecil ke terbesar',
				'url' => current_url_with_query_string(array(ORDER_CATEGORY, START_OFFSET), array(ORDER_CATEGORY => SMALLEST_DISCOUNT_TO_BIGGEST)),
				'parameter_name' => ORDER_CATEGORY,
				'value' => SMALLEST_DISCOUNT_TO_BIGGEST,
			),
		);
		return $data_filter;
	}

	private function _breadcumb()
	{
		$tree_view_category = get_tree_view_category();
		$seq_self_parent_category = $this->seq_self_parent_category;
		$category_seq_self_parent_with_name = get_data_product_category_seq_array($tree_view_category, $seq_self_parent_category);
		//$sorted_category = sort_level_category($category_seq_self_parent_with_name); //if with sorted
		$data_breadcrumb = array();
		foreach ($category_seq_self_parent_with_name as $each_sorted_category)
		{
			$each_data['seq'] = $each_sorted_category->seq;
			$each_data['name'] = $each_sorted_category->name;
			$each_data['level'] = $each_sorted_category->level;
			$data_breadcrumb[] = $each_data;
		}
		return $data_breadcrumb;
	}

	public function index()
	{
		$this->history->set_api_name('_product_category_');
		$this->_initialize();

		$product_category = $this->_display_category_product();
		$attribute = $this->_attribute_category();
		$filter = $this->_filter();
		$banner = $this->_banner();
		$breadcumb = $this->_breadcumb();
		$icon = "";

		$data = array(
			'items' => $product_category,
			'icon' => $icon,
			'attribute' => $attribute,
			'sort' => $filter,
			'banner' => $banner,
			"breadcrumb" => $breadcumb,
		)+$this->paging_data_product_category;
		$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
		show_response_custom($response);
	}

}

/* End of file Product_category.php */
/* Location: ./application/controller/V1/Product_category.php */