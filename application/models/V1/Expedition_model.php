<?php

class Expedition_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function find_expedition($params)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS
				pv.sell_price,
				p.`name` as product_name,
				m.expedition_seq as expedition_seq,
				pv.seq as product_seq,
				pv.product_price,
				pv.disc_percent,
				pv.sell_price,
				pv.max_buy,
				vv.seq as variant_value,
				vv.value as variant_name,
				pv.pic_1_img,
				p.merchant_seq,
				m.`name` as merchant_name,
				m.`code` as merchant_code,
				p.p_weight_kg,
				p.p_length_cm,
				p.p_width_cm,
				p.p_height_cm,
				p.b_weight_kg,
				p.b_length_cm,
				p.b_width_cm,
				p.b_height_cm,
				e.volume_divider as e_volume_divider,
				e.code as e_code,
				e.name as e_name,
				D.city_seq as city_seq
			FROM 
				m_product p 
				JOIN m_product_variant pv on pv.product_seq = p.seq
				JOIN m_variant_value vv	on vv.seq = pv.variant_value_seq
				JOIN m_merchant m on m.seq = p.merchant_seq
				JOIN m_expedition e	on e.seq = m.expedition_seq
				JOIN m_district D on m.district_seq = D.seq
			WHERE
				pv.seq IN({$params->product_variant_seq})";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function exp_info($exp_seq, $district_seq)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS med.exp_city_code as from_district_code, med.exp_district_code as to_district_code, s.seq as exp_service_seq, e.name as exp_code FROM m_expedition_district as med"
			. " INNER JOIN m_expedition e on med.exp_seq = e.seq "
			. " INNER JOIN m_expedition_service s on s.exp_seq = e.seq "
			. " WHERE med.exp_seq = {$exp_seq} and med.district_seq = {$district_seq}  and s.`default` = '1'";

		$result = query_data_row($sql);
		return $result;
	}

	public function exp_promo($city_seq, $merchant_seq)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS p.seq
		From  m_promo_free_fee_period p 
		JOIN m_promo_free_fee_city c on p.seq = c.promo_seq  
		JOIN m_promo_free_fee_city f on f.promo_seq = c.promo_seq
        JOIN m_promo_free_fee_merchant m on m.promo_seq = p.seq
		WHERE
		Date(now()) between p.date_from and p.date_to And
		status= 'A' And
		active = '1' And
		m.merchant_seq = {$merchant_seq} And
		((all_origin_city ='1' And all_destination_city ='1') OR 
		(all_origin_city ='1' and c.city_seq = {$city_seq} and c.`type`='D') OR 
		(all_destination_city ='1' and c.city_seq = {$city_seq} and c.`type`='O') OR
		((c.city_seq= {$city_seq} And c.`type`='D' And f.city_seq = {$city_seq} and f.`type`='O'))) LIMIT 1";

		$query_promo = query_data_row($sql);
		return $query_promo;
	}

	public function get_rate_cache($params)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS rate FROM t_expedition_rate_cache WHERE 
			exp_seq = {$params->expedition_seq} and
			from_district_code = '{$params->from_district_code}' and
			to_district_code = '{$params->to_district_code}' and
			exp_service_seq = '{$params->exp_service_seq}'";
		$query_rate = query_data_row($sql);
		return $query_rate;
	}

	public function save_web_service($data)
	{
		$new_seq = new_seq('t_webservice_log', 'seq');
		$data['seq'] = $new_seq;
		$table = 't_webservice_log';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $query;
	}

	public function save_update_service($params)
	{
		$table = 't_webservice_log';
		$data = get_fields($table, $params);
		$this->db->where('seq', $params->web_service_seq);
		$query = $this->db->update($table, $data);
		return $query;
	}

	public function save_rate_cache($data)
	{	
		$new_seq = new_seq('t_expedition_rate_cache', 'seq');
		$data['seq'] = $new_seq;
		$data['modified_date'] = date("Y-m-d H:i:s");
		$data['modified_by'] = 'SYSTEM';
		$data['scheduler'] = '0';
		$table = 't_expedition_rate_cache';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $query;
	}

	public function voucher($member_seq, $total_order)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS seq, code, nominal FROM m_promo_voucher
			WHERE member_seq = {$member_seq} and exp_date >= now() and trx_use_amt < {$total_order} and
			trx_no = ''";
		$query_vcr = query_data_row($sql);
		return $query_vcr;
	}

	public function coupon($params)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS m.seq, m.coupon_code, m.coupon_name, m.nominal FROM m_coupon m 
			JOIN m_coupon_product mp on mp.coupon_seq = m.seq           
			JOIN m_product_variant mv on mp.product_seq = mv.product_seq  
			WHERE mv.seq in ({$params->product_variant_seq}) and CURDATE() between m.coupon_period_from and m.coupon_period_to and m.status = 'A'";
		$query_cpn = query_data_row($sql);
		return $query_cpn;
	}

}

/* End of file Expedition_model.php */
/* Location: ./application/models/Expedition_model.php */