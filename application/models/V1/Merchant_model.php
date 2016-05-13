<?php

class Merchant_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function get_merchant_profile($data = "")
	{
		$sql = "SELECT 
                        m.`seq` AS merchant_seq,
                        m.`name` AS merchant_name , 
                        m.`address` AS merchant_address , 
                        md.`name` AS district_name , 
                        mc.`name` AS city_name ,
                        mp.`name` AS province_name  ,
                        m.`welcome_notes` AS merchant_welcome_notes,
                        m.`banner_img` AS merchant_banner_img,
                        m.`logo_img` AS merchant_logo_img,
                        m.`auth_date` AS merchant_register_date
                        FROM m_merchant m 
                        INNER JOIN m_district AS md ON m.district_seq = md.seq  
                        INNER JOIN m_city AS mc ON md.city_seq = mc.seq
                        INNER JOIN m_province AS mp ON mc.province_seq = mp.seq 
                        WHERE m.`code` = '{$data}'";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_merchant_product($data = "")
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS
			pv.seq AS product_variant_seq,
			pv.product_seq AS product_seq,
			p.name AS `product_name`,
			p.`merchant_seq` AS merchant_seq,
			pv.disc_percent AS product_disc_percent,
			vv.seq AS variant_seq,
			vv.value AS `variant_value`,
			pv.`product_price` AS `product_price`,
			pv.`sell_price` AS `product_sell_price`,
			pv.`seq` AS `product_variant_seq`,
			ps.`stock` AS `product_stock`,
			ps.`merchant_sku` AS `product_sku`,
			p.description AS description ,
                        pv.pic_1_img AS product_image_1
			FROM 
			m_product_variant pv
			LEFT JOIN m_product p 	ON pv.product_seq=p.seq
			INNER JOIN m_variant_value vv	ON pv.variant_value_seq=vv.seq 
			LEFT JOIN m_product_stock ps	ON ps.`product_variant_seq` = pv.`seq`
                        INNER JOIN m_merchant mc ON p.merchant_seq = mc.seq
			WHERE mc.`code` = '{$data}'";
		$query_result = query_data_row($sql);
		return $query_result;
	}
	
	public function get_merchant_by_product_var($var_seq = "")
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS m.seq as merchant_seq, m.name as merchant_name, m.code, 
			p.name as product_name, m.email as merchant_email, p.seq as product_seq, v.seq as value_seq, v.value
			from m_product_variant pv 
			JOIN m_product p on p.seq = pv.product_seq
            JOIN m_merchant m on m.seq = p.merchant_seq  
            JOIN m_variant_value v on v.seq = pv.variant_value_seq
			WHERE pv.seq = '{$var_seq}'";
		$query = $this->db->query($sql);
		$result_merchant = $query->row();
		return $result_merchant;
	}

}

/* End of file Merchant_model.php */
/* Location: ./application/models/Merchant_model.php */