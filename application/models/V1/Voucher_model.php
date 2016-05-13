<?php

class Voucher_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function get_promo($params = '')
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS seq, date_from, date_to, `name`, `code`,
				node_cd, `type`, voucher_count, nominal, exp_days, trx_get_amt, trx_use_amt, notes, `status`, active
				FROM m_promo_voucher_period
				WHERE node_cd = '{$params->node_cd}' 
				AND `status` ='A' 
				AND NOW() BETWEEN date_from AND date_to
				AND trx_get_amt <= '{$params->total_order}'
				order by date_from ASC LIMIT 1";
		$query_result = query_data_row($sql);
		return $query_result;		
	}
	
	public function save_add_voucher($params = '')
	{
		$new_seq = new_seq('m_promo_voucher', 'seq');
		$sql = "INSERT INTO m_promo_voucher (promo_seq, seq, member_seq, `code`, nominal, active_date, exp_date, trx_use_amt, trx_no, created_by, 
				created_date, modified_by, modified_date)
				VALUES({$params->seq},{$new_seq},{$params->member_seq},'{$params->code}','{$params->nominal}','{$params->active_date}','{$params->exp_date}',
				'{$params->trx_use_amt}','',{$params->member_seq},NOW(),{$params->member_seq},NOW())";
		$query = $this->db->query($sql);
		return $query;
	}
	
	public function save_update_voucher($params = '')
	{
		$sql = "UPDATE m_promo_voucher 
				SET
				member_seq = {$params->member_seq},
				active_date = '{$params->active_date}',
				exp_date = '{$params->exp_date}',
				modified_by = 'SYSTEM',
				modified_date = NOW()
				WHERE 
				promo_seq = '{$params->seq}' AND member_seq IS NULL 
				ORDER BY seq ASC LIMIT 1;";
		$query = $this->db->query($sql);
		return $query;
	}

}

/* End of file Product_model.php */
/* Location: ./application/models/Product_model.php */