<?php

class Transaction_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function find_merchant_info($merchant_seq)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS max(seq) as seq FROM 
			m_merchant_info WHERE merchant_seq = {$merchant_seq}";
		$query = $this->db->query($sql);
		$result_merc_info = $query->row();
		return $result_merc_info;
	}
	
	public function get_payment_gateway_method()
	{
		$sql = "SELECT `seq` , `name` , `code`  , `logo_img`  FROM m_payment_gateway_method WHERE active = '1' ";
		$data = new stdClass();
		$data->query = $this->db->query($sql);
		$data->row = $data->query->result();
		return $data;
	}

	public function use_voucher_update($params)
	{
		$table = 'm_promo_voucher';
		$data['trx_no'] = $params['order_no'];
		$data = get_fields($table, $data);
		$this->db->where('seq', $params['voucher_seq']);
		$query = $this->db->update($table, $data);
		return $query;
	}

	public function use_coupon_update($data)
	{
		$new_seq = new_seq('m_coupon_trx', 'seq', "WHERE coupon_seq = {$data['coupon_seq']}");
		$data['seq'] = $new_seq;
		$table = 'm_coupon_trx';
		$data['trx_no'] = $data['order_no'];
		$data['created_by'] = $data['member_seq'];
		$data['created_date'] = date("Y-m-d H:i:s");
		$table = 'm_coupon_trx';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $query;
	}

	public function get_last_seq($data)
	{
		$new_seq = new_seq('t_order', 'seq');
		return $new_seq;
	}

	public function save_add_order($data)
	{
		$new_seq = new_seq('t_order', 'seq');
		$data['seq'] = $new_seq;
		$data['order_date'] = date("Y-m-d H:i:s");
		$data['pg_method_seq'] = $data['payment_seq'];
		$data['conf_pay_note_file'] = "";
		$data['created_by'] = $data['member_seq'];
		$data['created_date'] = date("Y-m-d H:i:s");
		$data['modified_by'] = $data['member_seq'];
		$data['modified_date'] = date("Y-m-d H:i:s");
		$table = 't_order';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $new_seq;
	}

	public function save_order_merchant($data)
	{
		$data['created_by'] = $data['member_seq'];
		$data['created_date'] = date("Y-m-d H:i:s");
		$data['modified_by'] = $data['member_seq'];
		$data['modified_date'] = date("Y-m-d H:i:s");
		$table = 't_order_merchant';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $query;
	}

	public function save_order_product($data)
	{
		$data['created_by'] = $data['member_seq'];
		$data['created_date'] = date("Y-m-d H:i:s");
		$data['modified_by'] = $data['member_seq'];
		$data['modified_date'] = date("Y-m-d H:i:s");
		$table = 't_order_product';
		$data = get_fields($table, $data);
		$query = 'SET foreign_key_checks = 0';
		$query = $this->db->insert($table, $data);

		return $query;
	}

	public function get_trx($product_var_seq)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS m.trx_fee_percent as trx_free, c.trx_fee_percent as trx_free1 From m_product p 
			JOIN m_product_variant v on v.product_seq = p.seq 
			JOIN m_product_category c on c.seq = p.category_l2_seq left 
			JOIN m_merchant_trx_fee m on m.category_l2_seq = c.seq and m.merchant_seq = p.merchant_seq
			WHERE v.seq = {$product_var_seq} LIMIT 1";
		$result = query_data_row($sql);
		return $result;
	}

	public function get_ins($order_no, $merchant_seq)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS e.ins_rate_percent FROM t_order_merchant m 
			JOIN m_expedition_service s on s.seq = m.expedition_service_seq 
			JOIN m_expedition e on e.seq = s.exp_seq
			WHERE m.order_seq = {$order_no} AND m.merchant_info_seq = {$merchant_seq} LIMIT 1";
		$query = $this->db->query($sql);
		$result_ins = $query->row();
		return $result_ins;
	}

	public function get_stock_product($data)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS stock FROM m_product_stock 
			WHERE product_variant_seq = {$data['product_variant_seq']} and
			variant_value_seq = {$data['variant_value_seq']}";
		$query = $this->db->query($sql);
		$result_stock = $query->row();
		return $result_stock;
	}

	public function save_subtrans_stock($data)
	{
		$data['stock'] = $data['stock'] - $data['qty'];
		$table = 'm_product_stock';
		$data = get_fields($table, $data);
		$condition = array('product_variant_seq' => $data['product_variant_seq'], 'variant_value_seq' => $data['variant_value_seq']);
		$this->db->where($condition);
		$query = $this->db->update($table, $data);
		return $query;
	}

	public function save_merchant_stock_log($data)
	{
		$new_seq = new_seq('m_product_stock_log', 'seq');
		$data['seq'] = $new_seq;
		$data['created_by'] = $data['member_seq'];
		$data['created_date'] = date("Y-m-d H:i:s");
		$data['trx_date'] = date("Y-m-d H:i:s");
		$table = 'm_product_stock_log';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $query;
	}

	public function get_latest_product_stock($data)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS stock  as last_stock FROM m_product_stock 
			WHERE product_variant_seq = {$data['product_variant_seq']} and
			variant_value_seq = {$data['variant_value_seq']}";
		$query = $this->db->query($sql);
		$last_s = $query->row();
		return $last_s;
	}

	public function update_trans_merchant($data)
	{
		$table = 't_order_merchant';
		$data = get_fields($table, $data);
		$this->db->where('order_seq', $data['order_seq']);
		$query = $this->db->update($table, $data);
		return $query;
	}

	public function voucher($data)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS nominal FROM m_promo_voucher
			WHERE exp_date > now() and member_seq = {$data['member_seq']} and seq = {$data['voucher_seq']}";
		//and trx_amt = ''
		$query = $this->db->query($sql);
		$last_s = $query->row();
		return $last_s;
	}

	public function update_trans_order($total, $t_order, $order_seq)
	{
		$table = 't_order';
		$data['total_payment'] = $total;
		$data['total_order'] = $t_order;
		$data = get_fields($table, $data);
		$this->db->where('seq', $order_seq);
		$query = $this->db->update($table, $data);
		return $query;
	}

	public function get_order_product($order_no = "")
	{
		$sql = "Select SQL_CALC_FOUND_ROWS
			t.order_seq,
			mi.merchant_seq,
			t.merchant_info_seq,
			p.`name` as display_name,
			v.seq as product_seq,
			v.pic_1_img as img,
			mv.`value`,
			mv.seq as value_seq,
			mv.`value` as variant_name,
			t.product_status,
			t.qty,
			t.sell_price,
			t.weight_kg,
			t.ship_price_real,
			t.ship_price_charged,
			t.trx_fee_percent,
			t.ins_rate_percent,
			t.product_status,
			t.qty_return,
			t.created_by,
			t.created_date,
			t.modified_by,
			t.modified_date
			FROM
			t_order_product t 
				JOIN m_product_variant v On v.seq = t.product_variant_seq
				JOIN m_product p On v.product_seq = p.seq
				JOIN m_variant_value vv On vv.seq = t.variant_value_seq
				JOIN m_variant_value mv On mv.seq = v.variant_value_seq
				JOIN t_order o On o.seq = t.order_seq
				JOIN m_merchant_info mi On mi.seq = t.merchant_info_seq
				JOIN m_merchant mm On mm.seq = mi.merchant_seq
				where o.order_no = '{$order_no}'";
		$result_product = query_data_row($sql);
		return $result_product;
	}

	public function get_member_order($order_no)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS o.seq,
			o.order_no,
			o.order_date,
			o.member_seq,
			o.signature,
			m.`name` member_name,
			m.email,
			m.mobile_phone,
			c.`name` city_name,
			case when o.payment_retry <> '0'  Then concat(o.order_no, '-', cast(o.payment_retry as char(2))) Else o.order_no End order_trans_no,	
			o.receiver_name,
			o.receiver_address,
			o.receiver_district_seq,
			d.`name` district_name,
			c.`name` city_name,
			p.`name` province_name,
			o.receiver_zip_code,
			o.receiver_phone_no,
			o.payment_status,
			o.pg_method_seq,
			pg.`code` payment_code,
			pg.name payment_name,
			o.paid_date,
			case when pv.nominal is null then mc.nominal else pv.nominal end as nominal,
			pv.code as voucher_code,
			o.payment_retry,
			o.total_order,
			o.voucher_seq,
			o.voucher_refunded,
			o.total_payment,
			o.conf_pay_type,
			o.conf_pay_amt_member,
			o.conf_pay_date,
			o.conf_pay_note_file,
			o.conf_pay_bank_seq,
			o.conf_pay_amt_admin,
			mc.nominal as coupon_nominal
			from 
			t_order o 
			join m_district d on o.receiver_district_seq = d.seq
			join m_city c on c.seq = d.city_seq 
			join m_province p on p.seq = c.province_seq
			join m_payment_gateway_method pg on pg.seq = o.pg_method_seq
			left join m_promo_voucher pv on o.voucher_seq = pv.seq
			left join m_coupon mc on o.coupon_seq = mc.seq
			join m_member m on m.seq = o.member_seq where o.order_no = '{$order_no}'";
		$query = $this->db->query($sql);
		$result_memb = $query->row();
		return $result_memb;
	}

	public function get_order_merchant($order_no = "")
	{
		$sql = "Select SQL_CALC_FOUND_ROWS
			t.order_seq,
			t.merchant_info_seq,
			t.expedition_service_seq,
			e.`name` expedition_name,
			mm.`name` merchant_name,
			mm.email,
			mm.code as merchant_code,
			mm.seq merchant_seq,
			t.real_expedition_service_seq,    
			t.total_merchant,
			t.total_ins,
			t.total_ship_real,
			t.total_ship_charged,
			t.free_fee_seq,
			t.order_status,
			t.member_notes,
			t.printed,
			t.print_date,
			t.awb_seq,
			t.awb_no,
			t.ref_awb_no,
			t.ship_by,
			t.ship_by_exp_seq,
			t.ship_date,
			t.ship_note_file,
			t.ship_notes,
			t.received_date,
			t.received_by,
			t.redeem_seq,
			t.exp_invoice_seq,
			t.exp_invoice_awb_seq
		FROM 
			t_order_merchant t 
			join m_merchant_info m on m.seq = t.merchant_info_seq 
			join m_district d on d.seq = m.pickup_district_seq
			join m_city c on c.seq = d.city_seq 
			join m_province p on p.seq = c.province_seq
			join m_merchant mm on mm.seq = m.merchant_seq
			join m_expedition_service s on s.seq =  t.expedition_service_seq
			join m_expedition e on s.exp_seq = e.seq
			join t_order o on o.seq = t.order_seq	
			where o.order_no = '{$order_no}'";
		$result_merch = query_data_row($sql);
		return $result_merch;
	}

	public function get_bank_list()
	{
		$sql = "Select SQL_CALC_FOUND_ROWS
			seq,
			bank_name,
			bank_branch_name,
			bank_acct_no,
			bank_acct_name,
			logo_img
		From m_bank_account
		Where active = '1' ";
		$result_bank = query_data_row($sql);
		return $result_bank;
	}

	public function update_status_order($params)
	{
		$table = 't_order';
		$data['modified_date'] = date("Y-m-d H:i:s");
		$data['modified_by'] = $params->member_seq;
		$data = get_fields($table, $params);
		$this->db->where('seq', $params->order_seq);
		$query = $this->db->update($table, $data);
		return $query;
	}

	public function update_status_merchant($params)
	{
		$table = 't_order_merchant';
		$data['modified_date'] = date("Y-m-d H:i:s");
		$data['modified_by'] = $params->member_seq;
		$data = get_fields($table, $params);
		$this->db->where('order_seq', $params->order_seq);
		$query = $this->db->update($table, $data);
		return $query;
	}

	public function save_payment_log($data)
	{
		$new_seq = new_seq('t_order_payment_log', 'seq');
		$data['seq'] = $new_seq;
		$data['created_by'] = $data['member_seq'];
		$data['created_date'] = date("Y-m-d H:i:s");
		$data['modified_date'] = date("Y-m-d H:i:s");
		$data['modified_by'] = $data['member_seq'];
		$table = 't_order_payment_log';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $new_seq;
	}

	public function save_member_account($data)
	{
		$new_seq = new_seq('t_member_account', 'seq');
		$data['seq'] = $new_seq;
		$data['created_by'] = $data['member_seq'];
		$data['created_date'] = date("Y-m-d H:i:s");
		$table = 't_member_account';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $new_seq;
	}

	public function update_deposit_member($params)
	{
		$sql = "SELECT deposit_amt FROM m_member where seq = {$params['member_seq']}";
		$query = $this->db->query($sql);
		$member = $query->row();
		
		$deposit = $member->deposit_amt;
		
		$data['deposit_amt'] = $deposit - $params['deposit_trx_amt'];
		$table = 'm_member';
		$data['modified_date'] = date("Y-m-d H:i:s");
		$data['modified_by'] = $params['member_seq'];
		$data = get_fields($table, $data);
		$this->db->where('seq', $params['member_seq']);
		$query = $this->db->update($table, $data);
		return $query;
	}
	
	public function get_product_by_merchant($params)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS
			o.order_no,
			m.email,
			pv.pic_1_img,
			p.name AS product_name,
			vv.value AS variant_name,
			m.seq AS merchant_seq,
			op.ship_price_charged,
			op.qty,
			op.sell_price,
			op.ship_price_real,
			op.weight_kg,
			m.name AS merchant_name,
			op.product_status,
			pv.seq product_variant_seq
			FROM
				t_order_merchant om
				JOIN t_order o ON om.order_seq = o.seq
				JOIN t_order_product op ON om.merchant_info_seq = op.merchant_info_seq AND om.order_seq = op.order_seq
				JOIN m_product_variant pv ON op.product_variant_seq = pv.seq
				JOIN m_product p ON pv.product_seq = p.seq
				JOIN m_variant_value vv ON pv.variant_value_seq = vv.seq
				JOIN m_merchant_info mi ON om.merchant_info_seq = mi.seq
				JOIN m_merchant m ON mi.merchant_seq = m.seq
			WHERE om.order_seq = {$params->order_seq}
			AND om.merchant_info_seq = {$params->merchant_info_seq}
			";
		$result_product_merchant = query_data_row($sql);
		return $result_product_merchant;
	}
	
}
