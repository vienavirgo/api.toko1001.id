<?php

class Member_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function account_lists($params)
	{
		$sql = "Select SQL_CALC_FOUND_ROWS
				member_seq,
				seq,
				mutation_type,
				pg_method_seq,
				trx_type,
				trx_no,
				trx_date,
				deposit_trx_amt,
				non_deposit_trx_amt,
				bank_name,
				bank_branch_name,
				bank_acct_no,
				bank_acct_name,
				refund_date,
				`status` 
				FROM t_member_account
	WHERE member_seq = {$params->member_seq} order by $params->order  $params->order_code " . " LIMIT " . addslashes($params->limit1) . ", " . addslashes($params->limit2);
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function return_product_lists($params)
	{
		$sql = "Select SQL_CALC_FOUND_ROWS
				o.return_no,
                o.created_date,
				o.order_seq,
				o.product_variant_seq,
				o.variant_value_seq,
				o.qty,
				o.return_status,
				o.shipment_status,
				o.review_member,
				o.review_admin,
				o.review_merchant,
				o.awb_member_no,
				o.exp_seq_to_admin,
				o.admin_received_date,
				o.ship_to_merchant_date,
				o.exp_seq_to_merchant,
				o.awb_admin_no,
				o.merchant_received_date,
				o.ship_to_member_date,
				o.awb_merchant_no,
				o.exp_seq_to_member,
				o.member_received_date,
				p.name,
				pv.variant_value_seq,
				vv.value,
				tor.member_seq,
				tor.order_no,
				m.name as merchant_name,
				m.seq as merchant_seq,
				pv.pic_1_img
		FROM t_order_product_return o 
				join m_product_variant pv On pv.seq = o.product_variant_seq
                join m_product p On p.seq = pv.product_seq
                join m_variant_value vv on vv.seq = pv.variant_value_seq
                join t_order tor On tor.seq = o.order_seq
				join m_merchant m On m.seq = p.merchant_seq
	WHERE tor.member_seq = {$params->member_seq} 
		order by $params->order  $params->order_code " . " LIMIT " . addslashes($params->limit1) . ", " . addslashes($params->limit2);
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function check_Mytoken($token, $member_seq)
	{
		$match = FALSE;
		$sql = "SELECT member_token, member_seq from m_member_token where member_seq = '{$member_seq}' and member_token = '{$token}'";
		$query = $this->db->query($sql);
		$data = $query->num_rows();
		if ($data > 0)
		{
			$match = TRUE;
		}
		else
		{
			$match = FALSE;
		}
		return $match;
	}

	public function order_lists($params)
	{
		$sql = "Select SQL_CALC_FOUND_ROWS
                        t.seq,
                        t.order_no,
                        t.order_date,
                        t.member_seq,
                        t.receiver_name,
                        t.receiver_address,
                        t.receiver_district_seq,
                        t.receiver_zip_code,
                        t.receiver_phone_no,
                        t.payment_status,
                        t.pg_method_seq,
                        t.paid_date,
                        t.coupon_seq,
                        t.voucher_seq,
                        c.nominal as coupon_nominal,
                        v.nominal as voucher_nominal,
                        t.total_order,
                        t.total_payment,
                        case When om.free_fee_seq is null Then 0 Else om.free_fee_seq End as free_fee_seq,
                        om.order_status,
                        om.member_notes,
                        om.awb_no,
                        op.qty,
                        op.product_variant_seq,
                        op.variant_value_seq,
                        op.sell_price,
                        op.weight_kg,
                        op.ship_price_charged,
                        op.product_status,
                        m.seq as merchant_seq,
                        pv.pic_1_img,
                        om.merchant_info_seq,
                        m.name as merchant_name,
                        om.received_date,
                        op.ship_price_charged,
                        op.product_status,
                        op.sell_price,
                        op.weight_kg,
                        case 
                            When t.voucher_seq is not null then  v.nominal
                            When t.coupon_seq is not null then c.nominal Else 0 
                        End as total_voucher,                        
                        case when pv.variant_value_seq = '1' then pr.name Else concat(pr.name,' - ',vv.value) End as product_name 
		FROM t_order t join t_order_merchant om On
                        om.order_seq = t.seq
                               join t_order_product op On
                        op.order_seq = om.order_seq and
                        op.merchant_info_seq = om.merchant_info_seq
                               join m_merchant_info mi on
                        mi.seq = op.merchant_info_seq
                               join m_merchant m On
                        m.seq = mi.merchant_seq 
                               join m_member mm On
                        mm.seq = t.member_seq 
                               left join m_promo_voucher v On
                        v.seq = t.voucher_seq
                               left join m_coupon c On
                        c.seq = t.coupon_seq
                               join m_district d On
                        d.seq = t.receiver_district_seq
                               join m_city ci On
                        ci.seq = d.city_seq 
                               join m_province p On
                        p.seq = ci.province_seq 
                               join m_product_variant pv On
                        pv.seq = op.product_variant_seq
                                join m_product pr on
                        pr.seq = pv.product_seq
                                join m_variant_value vv On
                        vv.seq = pv.variant_value_seq

	WHERE t.member_seq = {$params->member_seq} order by $params->order  $params->order_code " . " LIMIT " . addslashes($params->limit1) . ", " . addslashes($params->limit2);
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function member_find_all_address($member_seq)
	{
		/* province name, city name */
		$sql = "SELECT MM.seq AS member_seq, MA.seq AS member_address_seq, alias, address, 
				MA.district_seq, MD.`name` AS district, MD.`city_seq` AS city_seq, MC.`name` AS city, MC.`province_seq` AS province_seq, MC.`name` AS province, 
				zip_code, pic_name, phone_no, email, MM.`name` AS `name`
				FROM toko1001_dev.m_member_address MA
				INNER JOIN m_member MM ON MM.seq = MA.member_seq 
				LEFT JOIN m_district MD ON MD.`seq` = MA.`district_seq`
				LEFT JOIN m_city MC ON MC.seq = MD.`city_seq` 
				LEFT JOIN m_province MP ON MP.`seq`=MC.`province_seq`
				WHERE MM.seq = {$member_seq}";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function member_delete_address($params)
	{
		$sql = "Delete FROM m_member_address WHERE seq = '{$params->address_seq}' and member_seq = '{$params->member_seq}'";
		$query = $this->db->query($sql);
		return $query;
	}

	public function member_update_address($params)
	{
		$table = 'm_member_address';
		$data = get_fields($table, $params);
		$data['modified_date'] = date("Y-m-d H:i:s");
		$data['modified_by'] = $data['member_seq'];
		$condition = array('member_seq' => $params['member_seq'], 'seq' => $params['address_seq']);
		$this->db->where($condition);
		$query = $this->db->update($table, $data);
		return $query;
	}

	public function find_member_address($member_seq, $seq)
	{
		$sql = "SELECT member_seq, MA.seq, alias, address, district_seq, zip_code, phone_no, city_seq, D.name district_name, D.seq as district_seq, pic_name FROM m_member_address MA
				inner join m_district D on D.seq = MA.district_seq
 				where MA.member_seq = {$member_seq} and MA.seq = {$seq} ";
		$query = $this->db->query($sql);
		$member = $query->row();
		return $member;
	}

	public function find_province($seq)
	{
		$sql = "SELECT seq, name FROM m_province where seq = {$seq}";
		$query = $this->db->query($sql);
		$province = $query->row();
		return $province;
	}

	public function find_city($city_seq)
	{
		$sql = "SELECT seq, name, province_seq FROM m_city where seq = $city_seq";
		$query = $this->db->query($sql);
		$city = $query->row();
		return $city;
	}

	public function member_save_address($data = "")
	{
		$new_seq = new_seq('m_member_address', 'seq', "WHERE member_seq = {$data['member_seq']}");
		$data['seq'] = $new_seq;
		$data["created_date"] = date("Y-m-d H:i:s");
		$data['created_by'] = $data['member_seq'];
		$data['modified_date'] = date("Y-m-d H:i:s");
		$data['modified_by'] = $data['member_seq'];

		$table = 'm_member_address';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $query;
	}

	public function find_district_by_city($city_seq)
	{
		$sql = "SELECT seq, name FROM m_district where city_seq = {$city_seq}";

		$districts = array();
		$query = $this->db->query($sql);
		if ($query->result())
		{
			foreach ($query->result() as $district)
			{
				$districts[$district->seq] = $district->name;
			}
			return $districts;
		}
		else
		{
			return FALSE;
		}
	}

	public function find_city_by_province($province_seq)
	{
		$sql = "SELECT seq, name FROM m_city where province_seq = {$province_seq}";
		$cities = array();
		$query = $this->db->query($sql);
		if ($query->result())
		{
			foreach ($query->result() as $city)
			{
				$cities[$city->seq] = $city->name;
			}
			return $cities;
		}
		else
		{
			return FALSE;
		}
	}

	public function find_province_list()
	{
		$sql = "SELECT seq, name FROM m_province";
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function find_member($seq)
	{
		$sql = "SELECT seq, name, mobile_phone, birthday, gender, new_password, deposit_amt, profile_img FROM m_member WHERE seq = {$seq}";
		$data_member = query_data_row($sql);
		return $data_member;
	}

	public function save_update($params, $seq)
	{
		$table = 'm_member';
		$data = get_fields($table, $params);
		$data['modified_date'] = date("Y-m-d H:i:s");
		$data['modified_by'] = $seq;
		$this->db->where('seq', $seq);
		$query = $this->db->update($table, $data);
		return $query;
	}

	public function check_user_email($data = "")
	{
		$sql = "SELECT seq, name, status, new_password FROM m_member WHERE email = '{$data->email}'";
		$user_email = query_data_row($sql);
		return $user_email;
	}

	public function add_member_log_security($data = "")
	{
		$new_seq = new_seq('m_member_log_security', 'seq');
		$sql = "INSERT INTO m_member_log_security VALUES ('{$data->member_seq}','{$new_seq}','{$data->type}','{$data->email_code}','{$data->password_now}','{$data->ip_address}','{$data->member_seq}','now()')";
		$query = $this->db->query($sql);
		return $query;
	}

	public function check_user_login($data = "")
	{
		$sql = "SELECT seq as member_seq, email, old_password, new_password, `name` as user_name, 
				birthday, gender, mobile_phone, bank_name, bank_branch_name, 
				bank_acct_no, bank_acct_name, profile_img, deposit_amt, status, last_login
                FROM m_member	
				WHERE email = '{$data->email}' AND new_password = '{$data->password}' AND status IN ({$data->status_allowed})";
		$data_user_login = query_data_row($sql);
		return $data_user_login;
	}

	public function save_add($data = "")
	{
		$last_login = NULL;
		$ip_address = NULL;
		$data['status'] = 'N';
		if (isset($data['status_member']))
		{
			$data['status'] = $data['status_member'];
		}
		$new_seq = new_seq("m_member", "seq");
		$data['seq'] = $new_seq;
		$data["created_date"] = date("Y-m-d H:i:s");
		$data['new_password'] = $data['password'];
		$data['created_by'] = 'SYSTEM';
		$data['modified_date'] = date("Y-m-d H:i:s");
		$data['modified_by'] = 'SYSTEM';
		$table = 'm_member';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $query;
	}

	public function save_log($data = "")
	{
		$status = 'S';
		if (isset($data->status))
		{
			$status = $data->status;
		}
		$new_seq = new_seq('m_member_login_log', 'seq');
		$data = (array) $data;
		$data['seq'] = $new_seq;
		$data['status'] = $status;
		$data['ip_address'] = $this->input->ip_address();
		$data["created_date"] = date("Y-m-d H:i:s");

		$table = 'm_member_login_log';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $query;
	}

	public function get_email_template($data = '')
	{
		$get_email = get_email_template($data);
		return $get_email;
	}

	public function get_member_address($data = "")
	{
		$member_seq = $data->member_seq;
		$sql = "SELECT MA.member_seq, MA.seq AS address_seq, MA.alias, MA.address, MA.`zip_code`,
				district_seq, MD.`name` AS district, MD.`city_seq` AS city_seq, MC.`name` AS city, MC.`province_seq` AS province_seq, MC.`name` AS province,
				MA.pic_name, MA.phone_no, MA.`default`, MA.active, MA.`created_by`, MA.`created_date`, MA.`modified_by`, MA.`modified_date`
				FROM m_member_address MA
				LEFT JOIN m_district MD ON MD.`seq` = MA.`district_seq`
				LEFT JOIN m_city MC ON MC.seq = MD.`city_seq` 
				LEFT JOIN m_province MP ON MP.`seq`=MC.`province_seq`
				WHERE MA.member_seq={$member_seq}";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	/* input: member seq
	 * output: product_variant_seq
	 */

	public function get_member_wishlist($member_seq)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS product_variant_seq 
				FROM m_member_wishlist
				WHERE member_seq={$member_seq}";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function update_new_password($params = '')
	{
		$table = 'm_member';
		$data = get_fields($table, $params);
		$data['new_password'] = $params['new_password_encrypt'];
		$data['old_password'] = $params['old_password_encrypt'];
		$this->db->where('seq', $params['member_seq']);
		$query = $this->db->update($table, $data);
		return $query;
	}

	public function save_member_change_password_log($data = '')
	{
		$new_seq = new_seq('m_member_log_security', 'seq');
		$data['seq'] = $new_seq;
		$data['ip_addr'] = $this->input->ip_address();
		$data["created_date"] = date("Y-m-d H:i:s");
		$data["created_by"] = $data['member_seq'];
		$data["type"] = '1';
		$data["code"] = '';
		$data["old_pass"] = $data['old_password_encrypt'];
		$table = 'm_member_log_security';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);

		return $query;
	}

	public function get_member_seq_by_email($params = '')
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS seq FROM m_member WHERE email = '{$params->email}'";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function delete_wishlist($member_seq = '', $product_variant = '')
	{
		$sql = "DELETE FROM m_member_wishlist WHERE member_seq={$member_seq}";
		if ($product_variant == '')
		{
			$sql .= " AND product_variant={$product_variant}";
		}
		$query = $this->db->query($sql);
		return $query;
	}

	public function save_wishlist($member_seq = '', $product_variant = '')
	{
		if ($member_seq == '' OR $product_variant == '')
		{
			return '';
		}
		$sql = "INSERT INTO m_member_wishlist
                        (member_seq, product_variant_seq, created_by, created_date)
			VALUES('{$member_seq}','{$product_variant}','{$member_seq}',NOW())";
		$query = $this->db->query($sql);
		return $query;
	}

	public function get_member_account($member_seq = '')
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS bank_name, bank_branch_name, bank_acct_no, 
				bank_acct_name, deposit_amt, status, name, email
				FROM m_member
				WHERE seq = {$member_seq}";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function save_member_account($input = '')
	{
		$new_seq = new_seq('t_member_account', 'seq', "WHERE member_seq={$input["member_seq"]}");
		$data['seq'] = $new_seq;
		$data['member_seq'] = $input['member_seq'];
		$data['mutation_type'] = $input['mutation_type'];
		$data["trx_type"] = $input['trx_type'];
		$data["pg_method_seq"] = $input['pg_method_seq'];
		$data["trx_no"] = $input['trx_no'];
		$data["deposit_trx_amt"] = $input ['deposit_trx_amt'];
		$data["non_deposit_trx_amt"] = $input['non_deposit_trx_amt'];
		$data["bank_name"] = $input['bank_name'];
		$data["bank_branch_name"] = $input['bank_branch_name'];
		$data["bank_acct_no"] = $input["bank_acct_no"];
		$data["status"] = $input["status"];
		$data["trx_date"] = date("Y-m-d");
		$data["bank_acct_name"] = $input['bank_acct_name'];
		$data["refund_date"] = '0000-00-00 00:00:00';
		$data["created_by"] = $input['member_seq'];
		$data["created_date"] = date("Y-m-d H:i:s");
		$data["modified_by"] = $input['member_seq'];
		$data["modified_date"] = date("Y-m-d H:i:s");
		$table = 't_member_account';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $query;
	}

	public function get_review_product_list_member($member_seq = '', $limit_1 = '', $limit_2 = '', $order = '')
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS
					t_op.order_seq,
					t_op.product_variant_seq,
					t_o.order_no,
					t_o.order_date,
					p.`name` AS product_name,
					pv.`pic_1_img`,
					vv.`value` AS variant_value,
					mmi.`merchant_seq`,
					vv.`variant_seq` AS variant_seq
				FROM `t_order_product` t_op
				JOIN `t_order_merchant` t_om ON t_op.order_seq = t_om.order_seq AND t_op.`merchant_info_seq` = t_om.`merchant_info_seq`
				JOIN `m_merchant_info` mmi ON t_op.`merchant_info_seq`=mmi.seq
				JOIN t_order t_o ON t_op.order_seq = t_o.seq
				JOIN m_product_variant pv ON t_op.product_variant_seq = pv.seq
				JOIN m_product p ON pv.product_seq = p.seq
				JOIN m_variant_value vv ON pv.variant_value_seq = vv.seq
				WHERE 
					t_o.`payment_status`='P' AND t_om.`order_status`='D' AND
					t_o.`member_seq` = {$member_seq} AND (t_o.`seq`,t_op.`product_variant_seq`) NOT IN
					(SELECT order_seq,product_variant_seq FROM `m_product_review`)";
		if ((string) $order != '')
		{
			$sql .=" ORDER BY " . addslashes($order);
		}
		if ((string) $limit_1 != '' && (string) $limit_2 != '')
		{
			$sql .=" LIMIT " . addslashes($limit_1) . ", " . addslashes($limit_2);
		}
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_review_product_list_admin($member_seq = '', $limit_1 = '', $limit_2 = '', $order = '')
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS
					t_op.order_seq,
					t_op.product_variant_seq,
					p.`name` AS product_name,
					t_o.order_no,
					t_o.order_date,
					mpr.`rate`,
					mpr.`created_date`,
					mpr.`review`,
					pv.`pic_1_img`,
					vv.`value` AS variant_value,
					mmi.`merchant_seq`,
					vv.`variant_seq` AS variant_seq
			FROM `t_order_product` t_op
			JOIN m_merchant_info mmi 
			ON t_op.`merchant_info_seq`=mmi.seq
			JOIN t_order t_o 
			ON t_op.order_seq = t_o.seq
			JOIN m_product_variant pv 
			ON t_op.product_variant_seq = pv.seq
			JOIN m_product p 
			ON pv.product_seq = p.seq
			JOIN m_variant_value vv
			ON pv.variant_value_seq = vv.seq
			LEFT JOIN m_product_review mpr ON
			t_o.`seq`=mpr.order_seq AND t_op.`product_variant_seq`=mpr.product_variant_seq
			WHERE mpr.created_by={$member_seq}";

		if ((string) $order != '')
		{
			$sql .=" ORDER BY " . addslashes($order);
		}
		if ((string) $limit_1 != '' && (string) $limit_2 != '')
		{
			$sql .=" LIMIT " . addslashes($limit_1) . ", " . addslashes($limit_2);
		}
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function review_product_list_member($product_variant_seq = '', $order_seq = '', $rate = '', $review = '', $member_seq = '')
	{
		$data['product_variant_seq'] = $product_variant_seq;
		$data['order_seq'] = $order_seq;
		$data['rate'] = $rate;
		$data['review'] = $review;
		$data['review_admin'] = '';
		$data['status'] = 'N';
		$data['created_by'] = $member_seq;
		$data["created_date"] = date("Y-m-d H:i:s");
		$data['modified_by'] = $member_seq;
		$data['modified_date'] = date("Y-m-d H:i:s");

		$table = 'm_product_review';
		$data = get_fields($table, $data);
		$query = $this->db->insert($table, $data);
		return $query;
	}

}

/* End of file Member_model.php */
/* Location: ./application/models/Member_model.php */