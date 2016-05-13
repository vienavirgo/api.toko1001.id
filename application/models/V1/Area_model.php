<?php

class Area_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function get_province($seq = '')
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS seq AS seq, name AS name FROM m_province WHERE active='1'";
		if ($seq != '')
		{
			$sql .= " AND seq={$seq}";
		}
		$sql .= ' ORDER BY name ASC';
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_district($seq = '', $city_seq = '')
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS seq AS seq, city_seq AS city_seq, name AS name FROM m_district WHERE active='1'";
		if ($seq != '')
		{
			$sql .= " AND seq={$seq}";
		}
		if ($city_seq != '')
		{
			$sql .= " AND city_seq={$city_seq}";
		}
		$sql .= ' ORDER BY name ASC';
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_city($seq = '', $province_seq = '')
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS seq AS seq, province_seq AS province_seq, name AS name FROM m_city WHERE active='1'";
		if ($seq != '')
		{
			$sql .= " AND seq={$seq}";
		}
		if ($province_seq != '')
		{
			$sql .= " AND province_seq={$province_seq}";
		}
		$sql .= ' ORDER BY name ASC';
		$query_result = query_data_row($sql);
		return $query_result;
	}

}

/* End of file Area_model.php */
/* Location: ./application/models/Area_model.php */