<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends APITOKO1001_Controller {

	var $code = '';
	var $message = '';
	var $base_source = '';
	var $member_seq = '';
	var $member_token = '';
	var $action = '';

	function __construct()
	{
		parent::__construct();
		$this->base_source = $this->config->item('base_source');
		$this->load->model('V1/Member_model');
		$this->load->model('V1/Product_model');
		$this->load->library('Email');
		$this->_initialize();
	}

	private function _initialize()
	{
		$data_token = check_token();
		$this->member_token = $data_token['member_token'];
		$this->member_seq = $data_token['member_seq'];

		$action = $this->input->post('action');
		switch ($action)
		{
			case SAVE_ADD:
				$this->action = SAVE_ADD;
				break;
			case SAVE_UPDATE:
				$this->action = SAVE_UPDATE;
				break;
			case DELETE:
				$this->action = DELETE;
				break;
			case FIND_ALL:
				$this->action = FIND_ALL;
				break;
		}
	}

	private function _find_all_member_address_by_token()
	{
		$this->history->set_api_name('_Member List Address');
		$member_seq = $this->member_seq;

		$data = array();

		$lists = $this->Member_model->member_find_all_address($member_seq);

		if ($lists)
		{
			foreach ($lists->result() as $value)
			{
				$detail = array();
				$detail["member_seq"] = $value->member_seq;
				$detail["member_address_seq"] = $value->member_address_seq;
				$detail["alias"] = $value->alias;
				$detail["address"] = $value->address;
				$detail["district_seq"] = $value->district_seq;
				$detail["district"] = $value->district;
				$detail["city_seq"] = $value->city_seq;
				$detail["city"] = $value->city;
				$detail["province_seq"] = $value->province_seq;
				$detail["province"] = $value->province;
				$detail["zip_code"] = $value->zip_code;
				$detail["pic_name"] = $value->pic_name;
				$detail["phone_no"] = $value->phone_no;
				$detail["email"] = $value->email;
				$detail["name"] = $value->name;
				$detail['delete_url'] = base_url() . 'v1/member/address/' . $value->member_seq . '/' . $value->member_address_seq;
				$data[] = $detail;
			}
		}
		$code = 200;
		$status = "OK";
		$message = "Load Listing Address Complete";

		$row = 0;
		$this->code = $code;
		$this->message = $message;
		$data_find = array('code' => $code, 'message' => $message, 'data' => $data);
		return $this->response->show_custom($data_find);
	}

	private function _save_add_member_address()
	{

		$this->history->set_api_name('_Member Add Address');

		$params = new stdClass;
		$params->member_seq = htmlspecialchars(trim($this->input->post('member_seq')));
		$params->pic_name = htmlspecialchars(trim($this->input->post('pic_name')));
		$params->address = htmlspecialchars(trim($this->input->post('address')));
		$params->district_seq = htmlspecialchars(trim($this->input->post('district_seq')));
		$params->phone_no = htmlspecialchars(trim($this->input->post('phone_no')));
		$params->zip_code = htmlspecialchars(trim($this->input->post('zip_code')));
		$params->alias = htmlspecialchars(trim($this->input->post('alias')));

		$seq = $this->Member_model->member_save_address($params);
		if ($seq != "")
		{
			$code = 200;
			$status = "OK";
			$message = "Adding Address Success";
		}
		else
		{
			$code = 406;
			$status = "Failed";
			$message = "Unable update member address....";
		}
		$row = 0;
		$this->code = $code;
		$this->message = $message;
		//$data = array("status" => $status, "message" => $message, "data" => $params);
		return $this->response->result($code, $message, $row, $params);
	}

	private function _save_update_member_address()
	{
		$this->history->set_api_name('_Member Update Address');
		$params = get('object');
		$seq = $this->Member_model->member_update_address($params);

		if ($seq != "")
		{
			$code = 200;
			$status = "OK";
			$message = "Update Address Success";
		}
		else
		{
			$code = 406;
			$status = "Failed";
			$message = "Unable update member address....";
		}
		$row = 0;
		$this->code = $code;
		$this->message = $message;
		//$data = array("status" => $status, "message" => $message, "data" => $params);
		return $this->response->result($code, $message, $row, $params);
	}

	private function _delete_member_address()
	{

		$this->history->set_api_name('_Member Delete Address');

		$params = new stdClass;
		$params->member_seq = $this->member_seq;
		$params->address_seq = $this->input->post('address_seq');

		$member = $this->Member_model->find_member_address($params->member_seq, $params->address_seq);

		if ($member != "")
		{
			$code = 200;
			$message = "Delete Address Success";
			$this->Member_model->member_delete_address($params);
		}
		else
		{
			$code = 406;
			$status = "Failed";
			$message = "Unable delete member address....";
		}

		$row = 0;

		$this->code = $code;
		$this->message = $message;
		//$data = array("status" => $status, "message" => $message, "data" => $params);
		return $this->response->result($code, $message, $row, $params);
	}

	public function address()
	{
		$code = $this->code;
		$message = $this->message;
		$show_multi = 0;
		switch ($this->action)
		{
			case SAVE_ADD:
				$data = array($this->_save_add_member_address());
				$show_multi = 1;
				break;
			case SAVE_UPDATE:
				$data = array($this->_save_update_member_address());
				$show_multi = 1;
				break;
			case DELETE:
				$data = array($this->_delete_member_address());
				$show_multi = 1;
				break;
			case FIND_ALL:
				$data = array($this->_find_all_member_address_by_token());
				break;
		}
		if ($show_multi == 1)
		{
			$this->response->show_multi($code, $message, $data);
		}
	}

	public function list_member_address()
	{
		$this->load->view('member_address_by_token');
	}

	public function delete_member_address($member_seq, $seq)
	{
		$data['seq'] = $seq;
		$data['member_seq'] = $member_seq;
		$this->load->view('delete_member_address', $data);
	}

	public function add_member_address($seq)
	{
		$data['provinces'] = $this->Member_model->find_province_list();
		$data['member'] = '';
		$data['cityQ'] = '';
		$data['prov'] = '';
		$data['member_seq'] = $seq;
		$this->load->view('member_address', $data);
	}

	public function edit_member_address($member_seq, $seq)
	{
		$member = $this->Member_model->find_member_address($member_seq, $seq);
		$cityQ = $this->Member_model->find_city($member->city_seq);
		$prov = $this->Member_model->find_province($cityQ->province_seq);

		$data['provinces'] = $this->Member_model->find_province_list();
		$data['member'] = $member;
		$data['cityQ'] = $cityQ;
		$data['prov'] = $prov;
		$data['member_seq'] = $member_seq;
		$this->load->view('member_address', $data);
	}

	public function cities($province_seq)
	{
		header('Content-Type: application/x-json; charset=utf-8');
		echo(json_encode($this->Member_model->find_city_by_province($province_seq)));
	}

	public function districts($city_seq)
	{
		header('Content-Type: application/x-json; charset=utf-8');
		echo(json_encode($this->Member_model->find_district_by_city($city_seq)));
	}

	public function edit_member($seq)
	{
		$datas = $this->Member_model->find_member($seq);
		foreach ($datas as $value)
		{
			$data['member_seq'] = $value->seq;
			$data['name'] = $value->name;
			$data['birthday'] = $value->birthday;
			$data['mobile_phone'] = $value->mobile_phone;
			$data['gender'] = $value->gender;
		}
		$this->load->view('member_form', $data);
	}

	private function _update_member()
	{

		$this->history->set_api_name('_Member Update_');

		$params = new stdClass;
		$seq = $this->input->post("member_seq");
		$params->name = $this->input->post("name");
		$params->birthday = $this->input->post("birthday");
		$params->mobile_phone = $this->input->post("mobile_phone");
		$gender = save_gender_format($this->input->post("gender"));
		$params->gender = $gender;

		$save_update = $this->Member_model->save_update($params, $seq);

		if ($save_update != "")
		{
			$code = 200;
			$status = "OK";
			$message = "Update Success";
		}
		else
		{
			$code = 406;
			$status = "Failed";
			$message = "Unable update member....";
		}

		$row = 0;

		$this->code = $code;
		$this->message = $message;

		$params->gender = display_gender_format($params->gender);

		//$data = array("status" => $status, "message" => $message, "data" => $params);
		return $this->response->result($code, $message, $row, $params);
	}

	public function update()
	{
//		echo $this->_register();
		$data = array($this->_update_member());
		$code = $this->code;
		$message = $this->message;
		$this->response->show_multi($code, $message, $data);
	}

	private function _change_password_member()
	{
		$this->history->set_api_name('_Change password_');
		$params = new stdClass();
		$output = new stdClass();
		$token = $this->input->post("token");
		$params->seq = $this->input->post("member_seq");
		$check_token = $this->check_token($token, $params->seq);

		$params->old_password = $this->input->post("old_password", TRUE);
		$params->new_password = $this->input->post("new_password", TRUE);

		$params->old_password_encrypt = md5($params->old_password);
		$params->new_password_encrypt = md5($params->new_password);
		if ($params->seq != '')
		{
			$user_email = $this->Member_model->find_member($params->seq);
		}
//                print_r($user_email);exit();
		if (count($user_email) < 1)
		{
			$row = 0;
			$code = 406;
			$status = "FAIL";
			$message = "User not exists";
			$data = array("status" => $status, "message" => $message, "data" => new stdClass());
			$this->code = $code;
			$this->message = $message;
			return $this->response->result($code, $message, $row, $data);
		}
		$password = '';
		foreach ($user_email as $each_user_email)
		{
			$password = $each_user_email->new_password;
		}

		if ($password != $params->old_password_encrypt)
		{
			$row = 0;
			$code = 401;
			$status = "FAIL";
			$message = "Unable to change password";
			$data = array("status" => $status, "message" => $message, "data" => new stdClass());
			$this->code = $code;
			$this->message = $message;
			return $this->response->result($code, $message, $row, $data);
		}
		$save_change_log = $this->Member_model->save_member_change_password_log($params);
		$update = $this->Member_model->update_new_password($params);
		if (!$update OR !$save_change_log)
		{
			$row = 0;
			$code = 500;
			$status = $this->status_code->getMessageForCode($code);
			$message = $status;
			$data = array("status" => $status, "message" => $status, "data" => new stdClass());
			$this->code = $code;
			$this->message = $message;
			return $this->response->result($code, $message, $row, $data);
		}
		$output->seq = $params->seq;
		$output->old_password = $params->old_password;
		$output->new_password = $params->new_password;
		$row = 0;
		$code = 200;
		$status = $this->status_code->getMessageForCode($code);
		$message = "Password Change";
		$data = array("status" => $status, "message" => $message, "data" => new stdClass);
		$this->code = $code;
		$this->message = $message;
		return $this->response->result($code, $message, $row, $data);
	}

	public function change_password()
	{
		$data = array(
			'change_password' => $this->_change_password_member(),
		);
		$code = $this->code;
		$message = $this->message;
		$this->response->show_multi($code, $message, $data);
	}

	private function _save_wishlist()
	{
		$member_seq = $this->member_seq;
		$product_variant_seq = $this->input->post('product_variant_seq');
		is_not_digit_set_error_response_exit($product_variant_seq, "product variant is not exceptable");
		$delete_wishlist = $this->Member_model->delete_wishlist($member_seq, $product_variant_seq);
		$save_wishlist = $this->Member_model->save_wishlist($member_seq, $product_variant_seq);
		if (!$save_wishlist OR !$delete_wishlist)
		{
			$row = 0;
			$code = 500;
			$status = $this->status_code->getMessageForCode($code);
			$message = $status;
			$data = array("status" => $status, "message" => $status, "data" => new stdClass());
			$this->code = $code;
			$this->message = $message;
			return $this->response->result($code, $message, $row, $data);
		}
		$row = 0;
		$code = 200;
		$status = $this->status_code->getMessageForCode($code);
		$message = "Save Wishlist Success";
		$data = array("status" => $status, "message" => $message, "data" => new stdClass());
		$this->code = $code;
		$this->message = $message;
		return $this->response->result($code, $message, $row, $data);
	}

	private function _delete_wishlist()
	{
		$params = new stdClass();
		$member_seq = $this->member_seq;
		$product_variant_seq = $this->input->post('product_variant_seq');
		is_not_digit_set_error_response_exit($product_variant_seq, "product variant is not exceptable");
		$delete_wishlist = $this->Member_model->delete_wishlist($member_seq, $product_variant_seq);
		if (!$delete_wishlist)
		{
			$row = 0;
			$code = 500;
			$status = $this->status_code->getMessageForCode($code);
			$message = $status;
			$data = array("status" => $status, "message" => $status, "data" => new stdClass());
			$this->code = $code;
			$this->message = $message;
			return $this->response->result($code, $message, $row, $data);
		}
		$row = 0;
		$code = 200;
		$status = $this->status_code->getMessageForCode($code);
		$message = "DELETE Wishlist Success";
		$data = array("status" => $status, "message" => $message, "data" => new stdClass());
		$this->code = $code;
		$this->message = $message;
		return $this->response->result($code, $message, $row, $data);
	}

	public function delete_wishlist()
	{
		$data = array(
			'delete_wishlist' => $this->_delete_wishlist()
		);
		$code = $this->code;
		$message = $this->message;
		$this->response->show_multi($code, $message, $data);
	}

	public function save_wishlist()
	{
		$data = array(
			'save_wishlist' => $this->_save_wishlist()
		);
		$code = $this->code;
		$message = $this->message;
		$this->response->show_multi($code, $message, $data);
	}

	public function wishlist()
	{

		$action = $this->input->post('action');
		switch ($action)
		{
			case SAVE_ADD:
				$this->save_wishlist();
				break;
			CASE DELETE:
				$this->delete_wishlist();
				break;
		}
	}

	private function _account_list()
	{
		$data = array();
		$this->history->set_api_name('_Member List Account');
		$params = new stdClass;
		$params->member_seq = $this->input->post('member_seq');
		$params->order = $this->input->post('order');
		$params->fieldname = $this->input->post('fieldname');

		$offset = $this->input->post(START_OFFSET);
		if ($params->fieldname = "")
		{
			$params->fieldname = "";
		}
		if ($offset == "")
		{
			$offset = 0;
		}
		$per_page = $this->input->post('page');
		if ($per_page == "")
		{
			$per_page = 10;
		}

		if ($per_page < 1)
		{
			$row = 0;
			$code = 406;
			$status = "FAIL";
			$message = "Paging Error";
			$data = array("status" => $status, "message" => $message, "data" => new stdClass());
			$this->code = $code;
			$this->message = $message;
			$data_find = array('code' => $code, 'message' => $message,
				'data' => $data);
			return $this->response->show_custom($data_find);
		}
		$limit = get_offset($offset, $per_page);
		$params->limit1 = $limit[0];
		$params->limit2 = $limit[1];

		$query = $this->Member_model->account_lists($params);

		$query_data = $query->query_data;
		$query_row = $query->query_row;

		$base_url = base_url() . 'v1/member/account';

		$paging = post_paging_link($base_url, $query_row, $per_page);


		$more = $paging['next_link'];
		$less = $paging['prev_link'];
		$first = $paging['first_link'];
		$last = $paging['last_link'];
		//$row = get_row_query($query);
		$saving_in = 0;
		$saving_out = 0;
		$saldo = 0;
		if ($query_data)
		{
			foreach ($query_data->result() as $result)
			{
				$detail = array();
				$detail['member_seq'] = $result->member_seq;
				$detail['seq'] = $result->seq;
				$detail['mutation_type'] = $result->mutation_type;
				$detail['pg_method'] = $result->pg_method_seq;
				$trx_type = $result->trx_type;
				$detail['trx_type'] = $trx_type;
				$detail['trx_name'] = display_transaction(constant("T_MEMBER_ACCOUNT_TRX_TYPE_" . $trx_type));
//                echo constant("T_MEMBER_ACCOUNT_TRX_TYPE_" . $trx_type); die();
				$detail['trx_no'] = $result->trx_no;
				$detail['trx_date'] = $result->trx_date;
				$detail['deposit_trx_amt'] = $result->deposit_trx_amt;
				$detail['non_deposit_trx_amt'] = $result->non_deposit_trx_amt;
				$detail['bank_name'] = $result->bank_name;
				$detail['bank_branch_name'] = $result->bank_branch_name;
				$detail['bank_acct_no'] = $result->bank_acct_no;
				$detail['bank_acct_name'] = $result->bank_acct_name;
				$detail['refund_date'] = $result->refund_date;
				$detail['status'] = $result->status;

				if ($result->trx_type == 'WDW' OR $result->trx_type == 'ORD')
				{
					$saving_out = $result->deposit_trx_amt;
				}
				if ($result->trx_type == 'CNL' OR $result->trx_type == 'RTR')
				{
					$saving_in = $result->deposit_trx_amt;
				}

				$detail['saving_in'] = $saving_in;
				$detail['saving_out'] = $saving_out;

				if ($result->mutation_type == 'C' OR $result->mutation_type == 'N')
				{
					$saldo = $saldo + $result->deposit_trx_amt;
				}
				if ($result->mutation_type == 'D')
				{
					$saldo = $saldo - $result->deposit_trx_amt;
				}

				$detail['saldo'] = $saldo;

				if ($result->non_deposit_trx_amt > 0 && $result->deposit_trx_amt == 0)
				{
					$detail['transaction_amount'] = 'CC  ' . $result->non_deposit_trx_amt;
				}
				else
				{
					$detail['transaction_amount'] = 'Deposit ' . $result->deposit_trx_amt;
				}

				$data[] = $detail;
			}
		}

		$account_list = array(
			'account' => $data
			, 'first' => $first, 'more' => $more,
			'less' => $less, 'last' => $last
		);

		$code = 200;
		$status = "OK";
		$message = "Load Listing Account Complete";

		$row = 0;
		$this->code = $code;
		$this->message = $message;
		$data_find = array('code' => $code, 'message' => $message,
			'data' => $account_list);
		return $this->response->show_custom($data_find);
	}

	private function _return_product_list()
	{
		$data = array();
		$this->history->set_api_name('_Member List Return Product');
		$params = new stdClass;
		$params->member_seq = $this->input->post('member_seq');
		$params->order = $this->input->post('order');
		$params->fieldname = $this->input->post('fieldname');

		$offset = $this->input->post(START_OFFSET);
		if ($params->fieldname = "")
		{
			$params->fieldname = "";
		}
		if ($offset == "")
		{
			$offset = 0;
		}
		$per_page = $this->input->post('page');
		if ($per_page == "")
		{
			$per_page = 10;
		}

		if ($per_page < 1)
		{
			$row = 0;
			$code = 406;
			$status = "FAIL";
			$message = "Paging Error";
			$data = array("status" => $status, "message" => $message, "data" => new stdClass());
			$this->code = $code;
			$this->message = $message;
			$data_find = array('code' => $code, 'message' => $message,
				'data' => $data);
			return $this->response->show_custom($data_find);
		}
		$limit = get_offset($offset, $per_page);
		$params->limit1 = $limit[0];
		$params->limit2 = $limit[1];

		$query = $this->Member_model->return_product_lists($params);
		$query_data = $query->query_data;
		$query_row = $query->query_row;

		$base_url = base_url() . 'v1/member/return_product';

		$paging = post_paging_link($base_url, $query_row, $per_page);

		$more = $paging['next_link'];
		$less = $paging['prev_link'];
		$first = $paging['first_link'];
		$last = $paging['last_link'];
		//$row = get_row_query($query);
		if ($query_data)
		{
			foreach ($query_data->result() as $result)
			{
				$detail = array();
				$detail['member_seq'] = $result->member_seq;
				$detail['return_no'] = $result->return_no;
				$detail['return_date'] = $result->created_date;
				$detail['order_seq'] = $result->order_seq;
				$detail['order_no'] = $result->order_no;
				$detail['product_variant_seq'] = $result->product_variant_seq;
				$detail['variant_value_seq'] = $result->variant_value_seq;
				$detail['qty'] = $result->qty;
				$detail['return_status'] = check_constant("T_ORDER_PRODUCT_RETURN_RETURN_STATUS_" . $result->return_status);
				$detail['shipment_status'] = check_constant("T_ORDER_PRODUCT_RETURN_SHIPMENT_STATUS_" . $result->shipment_status);
				$detail['review_member'] = $result->review_member;
				$detail['review_admin'] = $result->review_admin;
				$detail['review_merchant'] = $result->review_merchant;
				$detail['awb_member_no'] = $result->awb_member_no;
				$detail['exp_seq_to_admin'] = $result->exp_seq_to_admin;
				$detail['admin_received_date'] = $result->admin_received_date;
				$detail['ship_to_merchant_date'] = $result->ship_to_merchant_date;
				$detail['exp_seq_to_merchant'] = $result->exp_seq_to_merchant;
				$detail['awb_admin_no'] = $result->awb_admin_no;
				$detail['merchant_received_date'] = $result->merchant_received_date;
				$detail['ship_to_member_date'] = $result->ship_to_member_date;
				$detail['awb_merchant_no'] = $result->awb_merchant_no;
				$detail['exp_seq_to_member'] = $result->exp_seq_to_member;
				$detail['member_received_date'] = $result->member_received_date;
				$detail['product_name'] = $result->name;
				$detail['variant_value_seq'] = $result->variant_value_seq;
				$detail['value'] = $result->value;
				$detail['merchant'] = $result->merchant_name;
				$detail['img_1'] = base_url() . PRODUCT_UPLOAD_IMAGE . $result->merchant_seq . '/' . S_IMAGE_UPLOAD . $result->pic_1_img;
				$detail['url_detail'] = base_url() . 'v1/product/detail/' . $result->product_variant_seq;

				$data[] = $detail;
			}
		}

		$account_list = array(
			'return_product' => $data
			, 'first' => $first, 'more' => $more,
			'less' => $less, 'last' => $last
		);

		$code = 200;
		$status = "OK";
		$message = "Load Listing Return Product Complete";

		$row = 0;
		$this->code = $code;
		$this->message = $message;
		$data_find = array('code' => $code, 'message' => $message,
			'data' => $account_list);
		return $this->response->show_custom($data_find);
	}

	private function _order_detail_list()
	{
		$data = array();
		$this->history->set_api_name('_Member List Order');
		$params = new stdClass;
		$params->member_seq = $this->input->post('member_seq');
		$params->order = $this->input->post('order');
		$params->fieldname = $this->input->post('fieldname');
		$offset = $this->input->post(START_OFFSET);
		if ($params->fieldname = "")
		{
			$params->fieldname = "";
		}
		if ($offset == "")
		{
			$offset = 0;
		}
		$per_page = $this->input->post('page');
		if ($per_page == "")
		{
			$per_page = 10;
		}

		if ($per_page < 1)
		{
			$row = 0;
			$code = 406;
			$status = "FAIL";
			$message = "Paging Error";
			$data = array("status" => $status, "message" => $message, "data" => new stdClass());
			$this->code = $code;
			$this->message = $message;
			$data_find = array('code' => $code, 'message' => $message,
				'data' => $data);
			return $this->response->show_custom($data_find);
		}
		$limit = get_offset($offset, $per_page);
		$params->limit1 = $limit[0];
		$params->limit2 = $limit[1];

		$query = $this->Member_model->order_lists($params);


		$query_data = $query->query_data;
		$query_row = $query->query_row;

		$base_url = base_url() . 'v1/member/order_detail';

		$paging = post_paging_link($base_url, $query_row, $per_page);

		$more = $paging['next_link'];
		$less = $paging['prev_link'];
		$first = $paging['first_link'];
		$last = $paging['last_link'];
		//$row = get_row_query($query);
		$old_merchant_seq = 0;
		$old_order_seq = 0;
		if ($query_data)
		{
			foreach ($query_data->result() as $result)
			{
				if ($old_order_seq != $result->seq)
				{
					if ($old_order_seq != 0)
					{
						$detail['order_merchant'][] = $detail_merhant;
						$data[] = $detail;
					}
					$detail = array();
					$detail['order_seq'] = $result->seq;
					$detail['order_no'] = $result->order_no;
					$detail['order_date'] = $result->order_date;
					$detail['member_seq'] = $result->member_seq;
					$detail['receiver_name'] = $result->receiver_name;
					$detail['receiver_address'] = $result->receiver_address;
					$detail['receiver_district_seq'] = $result->receiver_district_seq;
					$detail['receiver_zip_code'] = $result->receiver_zip_code;
					$detail['receiver_phone_no'] = $result->receiver_phone_no;
					$detail['payment_status'] = $result->payment_status;
					$detail['pg_method_seq'] = $result->pg_method_seq;
					$detail['paid_date'] = $result->paid_date;
					$detail['coupon_seq'] = $result->coupon_seq;
					$detail['voucher_seq'] = $result->voucher_seq;
					$detail['coupon_nominal'] = $result->coupon_nominal;
					$detail['voucher_nominal'] = $result->voucher_nominal;
					$detail['total_order'] = $result->total_order;
					$detail['total_payment'] = $result->total_payment;
					$detail['total_voucher'] = $result->total_voucher;
					$detail['free_fee_seq'] = $result->free_fee_seq;
					$detail['product_status'] = base_url() . PRODUCT_UPLOAD_IMAGE . $result->merchant_seq . '/' . S_IMAGE_UPLOAD . $result->pic_1_img;
					$old_order_seq = $result->seq;
				}

				if ($old_merchant_seq != $result->merchant_info_seq)
				{
					if ($old_merchant_seq != 0)
					{
						$detail['order_merchant'][] = $detail_merhant;
					}
					$detail_merhant = array();
					$detail_merhant['merchant_info_seq'] = $result->merchant_info_seq;
					$detail_merhant['merchant_name'] = $result->merchant_name;
					$detail_merhant['member_notes'] = $result->member_notes;
					$detail_merhant['order_status'] = $result->order_status;
					$detail_merhant['received_date'] = $result->received_date;
					$detail_merhant['awb_no'] = $result->awb_no;
					$old_merchant_seq = $result->merchant_info_seq;
				}

				$detail_product = array();
				$detail_product['order_seq'] = $result->order_no;
				$detail_product['merchant_info_seq'] = $result->merchant_info_seq;
				$detail_product['product_name'] = $result->product_name;
				$detail_product['qty'] = $result->qty;
				$detail_product['ship_price_charged'] = $result->ship_price_charged;
				$detail_product['product_status'] = $result->product_status;
				$detail_product['sell_price'] = $result->sell_price;
				$detail_product['weight_kg'] = $result->weight_kg;
				$detail_product['img_1'] = base_url() . PRODUCT_UPLOAD_IMAGE . $result->merchant_seq . '/' . S_IMAGE_UPLOAD . $result->pic_1_img;
				$detail['url_detail'] = base_url() . 'v1/product/detail/' . $result->product_variant_seq;

				$detail_merhant['order_product'][] = $detail_product;
			}
		}
		$detail['order_merchant'][] = $detail_merhant;
		$data[] = $detail;

		$order_list = array(
			'order' => $data
			, 'first' => $first, 'more' => $more,
			'less' => $less, 'last' => $last
		);

		$code = 200;
		$status = "OK";
		$message = "Load Listing Order Complete";

		$row = 0;
		$this->code = $code;
		$this->message = $message;
		$data_find = array('code' => $code, 'message' => $message,
			'data' => $order_list);
		return $this->response->show_custom($data_find);
	}

	public function account()
	{
		$action = $this->input->post('action');
		switch ($action)
		{
			CASE FIND_ALL:
				$this->_account_list();
				break;
		}
	}

	public function return_product()
	{
		$action = $this->input->post('action');
		switch ($action)
		{
			CASE FIND_ALL:
				$this->_return_product_list();
				break;
			CASE SAVE_ADD:
				$this->_return_product_list();
				break;
		}
	}

	public function order()
	{
		$action = $this->input->post('action');
		switch ($action)
		{
			CASE FIND_ALL:
				$this->_order_detail_list();
				break;
			CASE SAVE_ADD:
				$this->_order_list();
				break;
		}
	}

}

/* End of file Member.php */
/* Location: ./application/controller/V1/Member.php */
