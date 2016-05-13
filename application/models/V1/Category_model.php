<?php

class Category_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function get_filter_category_name($category_seq_implode = '',$category_seq)
	{

		$sql = "SELECT SQL_CALC_FOUND_ROWS
			seq as pc_seq, parent_seq as pc_parent, name as pc_name
			FROM m_product_category
			WHERE parent_seq IN ({$category_seq_implode})";
		$query_result = query_data_row($sql);
		$goods = ($query_result->query_data->result());
		foreach ($goods as $good) {
			if ($good->pc_seq == $category_seq)
			{
				$sql = "SELECT SQL_CALC_FOUND_ROWS
				seq as pc_seq, parent_seq as pc_parent, name as pc_name
				FROM m_product_category
				WHERE seq IN ({$category_seq})";
				$query_result = query_data_row($sql);
				break;
			}
		}

		return $query_result;
	}

	public function get_product_seq($data)
	{
		$counter = 0;
		$end = count($data);
		$sql = '';
		$Rs = '';

		foreach ($data as $attribute_value_seq)
		{
			if (count($attribute_value_seq) > 1)
			{
				$sql .= "SELECT product_seq from m_product_attribute where attribute_value_seq IN (" . implode(',', $attribute_value_seq) . ") GROUP BY product_seq HAVING COUNT(product_seq)>1";
			}
			else
			{
				$sql .= "SELECT product_seq from m_product_attribute where attribute_value_seq IN (" . implode(',', $attribute_value_seq) . ") GROUP BY product_seq";
			}
			if ($counter < ($end - 1))
				$sql .= " UNION ALL ";
			$counter++;
		}
		if ($sql != '')
		{
			$Rs = $this->db->query($sql);
			if (count($Rs->result()) > 0)
			{
				return $Rs->result();
			}
		}
		return array();
	}

	public function get_filter_attribute_name($category_seq_implode = '')
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS
				ac.`category_seq` AS attribute_category_category_seq, 
				ac.`attribute_seq` AS attribute_category_attribute_seq, 
				a.`seq` AS attribute_seq, 
				a.`name` AS attribute_name, 
				a.`display_name` AS attribute_display_name
			FROM m_attribute_category ac
			LEFT JOIN m_attribute AS a
			ON ac.`attribute_seq`=a.`seq`
			WHERE ac.`category_seq` IN ({$category_seq_implode})
			GROUP BY ac.`attribute_seq`,a.`seq`";

		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_filter_attribute_value($category_seq_implode = '')
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS
				ac.`attribute_seq` AS attribute_category_attribute_seq,
				av.seq AS attribute_value_seq, 
				av.value AS attribute_value_value 
			FROM m_attribute_category ac
			LEFT JOIN m_attribute_value av
			ON ac.`attribute_seq`= av.attribute_seq
			WHERE ac.`category_seq` IN ({$category_seq_implode})
			GROUP BY ac.`attribute_seq`,av.seq";

		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_thumbs_category_img($category_seq = '')
	{
		$sql = "SELECT 
					banner_img AS banner_image,
					banner_img_url AS banner_image_url
				FROM 
					`m_product_category_img` 
				WHERE 
					category_seq={$category_seq}
				LIMIT 1;";
		$query_result = query_data_row($sql);
		return $query_result;
	}

}

/* End of file Category_model.php */
/* Location: ./application/models/Category_model.php */