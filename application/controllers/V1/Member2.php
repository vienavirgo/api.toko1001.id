<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Member2 extends APITOKO1001_Controller {

	var $action = '';
	var $code = '';
	var $message = '';
	var $paging_data_member_review = '';
	var $paging_data_admin_review = '';

	function __construct()
	{
		parent::__construct();
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
	}

	private function _check_action($action_allowed = array())
	{
		$action = get('single', 'action');
		if (!in_array($action, $action_allowed))
		{
			show_response(406, "action not allowed");
			exit();
		}
	}

	private function _update_member()
	{
		$this->history->set_api_name('_Member Update_');
		$params = get('object');
		$this->Member_model->save_update((array) $params, $params->member_seq);
		output_with_gender($params);
		return array($params);
	}

	public function index()
	{
		$params = get('object');
		$this->_check_action(array(UPDATE));
		switch ($params->action)
		{
			case UPDATE:
				$data = $this->_update_member();
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			default:
				show_response(406, "action not allowed");
				exit();
		}
	}

	public function password()
	{
		$method = $this->uri->segment(4);
		$params = get('object');
		$method = '_password';
		if ($media == 'change')
		{
			$method = '_change_password';
		}
		if ($media == 'forgot')
		{
			$method = '_forgot_password';
		}
		$this->_check_action(array(UPDATE));
		switch ($params->action)
		{
			case READ:
				$this->$method();
				break;
			default:
				show_response(406, "action not allowed");
				exit();
		}
	}

	private function _save_address()
	{
		$this->history->set_api_name('_Member Save Address_');
		$params = get('object');
		$this->Member_model->member_save_address((array) $params);
		$this->code = 200;
		$this->message = "Adding Address Success";
		return array($params);
	}

	private function _update_address()
	{
		$this->history->set_api_name('_Member Update Address_');
		$params = get('object');
		$this->Member_model->member_update_address((array) $params);
		return array($params);
	}

	private function _delete_address()
	{
		$this->history->set_api_name('_Member Delete Address');
		$params = get('object');
		$this->Member_model->member_delete_address($params);
		return array($params);
	}

	private function _read_address()
	{
		$this->history->set_api_name('_Member List Address');
		$member_seq = get('single', 'member_seq');

		$data = array();

		$query = $this->Member_model->member_find_all_address($member_seq);
		$lists = $query->query_data;
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
		return $data;
	}

	public function address()
	{
		$action = get('single', 'action');
		$this->_check_action(array(CREATE, UPDATE, DELETE, READ));
		switch ($action)
		{
			case CREATE:
				$data = $this->_save_address();
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			case UPDATE:
				$data = $this->_update_address();
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			case DELETE:
				$data = $this->_delete_address();
				show_response(200, "delete address complete");
				break;
			case READ:
				$data = $this->_read_address();
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			default:
				show_response(406, "action not allowed");
				exit();
		}
	}

	private function _save_wishlist()
	{
		$this->history->set_api_name('_Save Wishlist');
		$params = get('object');
		$member_seq = $params->member_seq;
		$product_variant_seq = $params->product_variant_seq;
		$delete_wishlist = $this->Member_model->delete_wishlist($member_seq, $product_variant_seq);
		$save_wishlist = $this->Member_model->save_wishlist($member_seq, $product_variant_seq);
		if (!$save_wishlist OR !$delete_wishlist)
		{
			show_response(500);
			exit();
		}
		return $data = array("status" => $this->status_code->getMessageForCode(200), "message" => "Save Wishlist Success", "data" => new stdClass());
		;
	}

	private function _delete_wishlist()
	{
		$this->history->set_api_name('_Delete Wishlist');
		$params = get('object');
		$member_seq = $params->member_seq;
		$product_variant_seq = $params->product_variant_seq;
		$delete_wishlist = $this->Member_model->delete_wishlist($member_seq, $product_variant_seq);
		if (!$delete_wishlist)
		{
			show_response(500);
			exit();
		}
		return $data = array("status" => $this->status_code->getMessageForCode(200), "message" => "Save Wishlist Success", "data" => new stdClass());
		;
	}

	private function _read_wishlist()
	{
		$this->history->set_api_name('_Wish List_');
		$params = get('object');
		$member_seq = $params->member_seq;
		$query = $this->Member_model->get_member_wishlist($member_seq);
		$wishlist = $query->query_data;
		if (get_row_query($query) == 0)
		{
			show_response(500);
			exit();
		}
		return $data = array("status" => $this->status_code->getMessageForCode(200), "message" => "Wishlist load complete", "data" => $wishlist->result());
		;
	}

	public function wishlist()
	{
		$action = get('single', 'action');
		$this->_check_action(array(CREATE, DELETE, READ));
		switch ($action)
		{
			case CREATE:
				$data_child = $this->_save_wishlist();
				$data = array('save_wishlist' => $data_child);
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			case DELETE:
				$data_child = $this->_delete_wishlist();
				$data = array('delete_wishlist' => $data_child);
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			case READ:
				$data_child = $this->_read_wishlist();
				$data = array('delete_wishlist' => $data_child);
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			default:
				show_response(406, "action not allowed");
				exit();
		}
	}

	private function _account_list()
	{
		$data = array();
		$this->history->set_api_name('_Member List Account');
		$params = get('object');
		$offset = get('single', START_OFFSET, TRUE);
		if ($offset == "")
		{
			$offset = 0;
		}
		$per_page = isset($params->page) ? $params->page : '10';

		$params->order_code = isset($params->order_code) ? get_order_by($params->order_code) : '';
		$params->order = isset($params->order) ? $params->order : 'trx_date';
		$default_order = array(
			'seq' => 'seq',
			'member_seq' => 'member_seq',
			'mutation_type' => 'mutation_type',
			'trx_no' => 'trx_no',
			'trx_date' => 'trx_date',
			'deposit_trx_amt' => 'deposit_trx_amt',
			'bank_name' => 'bank_name',
			'status' => 'status');
		$statement_order = '';
		if (array_key_exists($params->order, $default_order))
		{
			$params->order = $default_order[$params->order];
		}
		if ($per_page < 1)
		{
			$status = "FAIL";
			$message = "Paging Error";
			$data = array("status" => $status, "message" => $message, "data" => new stdClass());
			$response = array('code' => 406, 'message' => $this->status_code->getMessageForCode(406), 'data' => $data);
			show_response_custom($response);
			exit();
		}
		$limit = get_offset($offset, $per_page);
		$params->limit1 = $limit[0];
		$params->limit2 = $limit[1];

		$query = $this->Member_model->account_lists($params);
		$query_data = $query->query_data;
		$query_row = $query->query_row;

		$base_url = base_url() . 'v1/member/account';
		$paging = post_paging_link($base_url, $query_row, $per_page);

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
				$detail['trx_name'] = constant("T_MEMBER_ACCOUNT_TRX_TYPE_" . $trx_type);
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

		return array('account' => $data) + $paging;
	}

	public function account()
	{
		$action = get('single', 'action');
		$this->_check_action(array(READ, SAVE_ADD));
		switch ($action)
		{
			case READ:
				$data = $this->_account_list();
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			case SAVE_ADD:
				$this->_withdraw();
				break;
			default:
				show_response(406, "action not allowed");
				exit();
		}
	}

	public function order()
	{
		$action = get('single', 'action');
		$this->_check_action(array(READ));
		switch ($action)
		{
			case READ:
				$data = $this->_order_detail_list();
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			default:
				show_response(406, "action not allowed");
				exit();
		}
	}

	private function _order_detail_list()
	{
		$data = array();
		$this->history->set_api_name('_Member List Order');
		$params = get('object');
		$offset = get('single', START_OFFSET, TRUE);
		if ($offset == "")
		{
			$offset = 0;
		}
		$per_page = isset($params->page) ? $params->page : '10';

		$params->order_code = isset($params->order_code) ? get_order_by($params->order_code) : '';
		$params->order = isset($params->order) ? $params->order : 'order_date';
		$default_order = array(
			'order_date' => 't.order_date',
			'seq' => 't.seq');
		$statement_order = '';
		if (array_key_exists($params->order, $default_order))
		{
			$params->order = $default_order[$params->order];
		}

		if ($per_page < 1)
		{
			$status = "FAIL";
			$message = "Paging Error";
			$data = array("status" => $status, "message" => $message, "data" => new stdClass());
			$response = array('code' => 406, 'message' => $this->status_code->getMessageForCode(406), 'data' => $data);
			show_response_custom($response);
			exit();
		}

		$limit = get_offset($offset, $per_page);
		$params->limit1 = $limit[0];
		$params->limit2 = $limit[1];

		$query = $this->Member_model->order_lists($params);
		$query_data = $query->query_data;
		$query_row = $query->query_row;

		$base_url = base_url() . 'v1/member/order';
		$paging = post_paging_link($base_url, $query_row, $per_page);
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
			$detail['order_merchant'][] = $detail_merhant;
			$data[] = $detail;
		}
		return array('order' => $data) + $paging;
	}

	private function _return_product_list()
	{
		$data = array();
		$this->history->set_api_name('_Member List Return Product');
		$params = get('object');
		$offset = get('single', START_OFFSET, TRUE);
		if ($offset == "")
		{
			$offset = 0;
		}
		$per_page = isset($params->page) ? $params->page : '10';

		if ($per_page < 1)
		{
			$status = "FAIL";
			$message = "Paging Error";
			$data = array("status" => $status, "message" => $message, "data" => new stdClass());
			$response = array('code' => 406, 'message' => $this->status_code->getMessageForCode(406), 'data' => $data);
			show_response_custom($response);
			exit();
		}

		$params->order_code = isset($params->order_code) ? get_order_by($params->order_code) : '';
		$params->order = isset($params->order) ? $params->order : 'o.created_date';

		$default_order = array(
			'return_no' => 'o.return_no',
			'created_date' => 'o.created_date',
			'status' => 'o.return_status',
			'review_member' => 'o.review_member',
			'review_admin' => 'o.review_admin',
			'merchant_name' => 'm.name',
			'review_merchant' => 'o.review_merchant');
		$statement_order = '';
		if (array_key_exists($params->order, $default_order))
		{
			$params->order = $default_order[$params->order];
		}

		$limit = get_offset($offset, $per_page);
		$params->limit1 = $limit[0];
		$params->limit2 = $limit[1];

		$query = $this->Member_model->return_product_lists($params);
		$query_data = $query->query_data;
		$query_row = $query->query_row;

		$base_url = base_url() . 'v1/member/return_product';

		$paging = post_paging_link($base_url, $query_row, $per_page);

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

		return array('return_product' => $data) + $paging;
	}

	public function return_product()
	{
		$action = get('single', 'action');
		$this->_check_action(array(READ));
		switch ($action)
		{
			CASE READ:
				$data = $this->_return_product_list();
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			default:
				show_response(406, "action not allowed");
				exit();
		}
	}

private function _change_password_member()
	{ 
		$this->history->set_api_name('_Change password_');  //

		$params = get('object'); 
//
		$params->old_password_encrypt = md5($params->old_password); //
		$params->new_password_encrypt = md5($params->new_password); //
		if ($params->member_seq != '') //
		{
			$query = $this->Member_model->find_member($params->member_seq); //
			$user_email = $query->query_data->result(); //
		}
		if (get_row_query($query) == 0) //
		{
			$status = "FAIL";
			$message = "User not exists";
			$data = array("status" => $status, "message" => $message, "data" => new stdClass());
			$response = array('code' => 406, 'message' => $this->status_code->getMessageForCode(406), 'data' => $data);
			show_response_custom($response);
			exit();
		}
		$password = '';
		foreach ($user_email as $each_user_email)
		{
			$password = $each_user_email->new_password;
		}
		
		if ($password != $params->old_password_encrypt)
		{
			$status = "FAIL";
			$message = "Unable to change password";
			$data = array("status" => $status, "message" => $message, "data" => new stdClass());
			$response = array('code' => 406, 'message' => $this->status_code->getMessageForCode(406), 'data' => $data);
			show_response_custom($response);
			exit();
		}
		$save_change_log = $this->Member_model->save_member_change_password_log((array) $params);
		$update = $this->Member_model->update_new_password((array) $params);
		if (!$update OR !$save_change_log)
		{
			show_response(500);
			exit();
		}

		$status = $this->status_code->getMessageForCode(200);
		$message = "Password Change";
		$data = array("status" => $status, "message" => $message, "data" => new stdClass);

		return $data;
	}

	public function change_password()
	{

		$data = array(
			'change_password' => $this->_change_password_member(),
		);

		$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
		show_response_custom($response);
	}

	private function _withdraw()
	{
		$input = array();
		$this->history->set_api_name('_withdraw_');
		$params = get('object');
		$query = $this->Member_model->get_member_account($params->member_seq);
		$lists = $query->query_data;

		$refund_code = REFUND_STATUS_CODE . date('my') . generate_random_text(3, TRUE);
		$gencode = generate_random_alphabet('2', TRUE) . date('d') .
			generate_random_alphabet(2, TRUE) . date('m') .
			generate_random_alphabet(2, TRUE) . date('y');
		$code_mail = $gencode . $refund_code;
		if ($lists)
		{
			foreach ($lists->result() as $value)
			{
				$input["member_seq"] = $params->member_seq;
				$input["email"] = $value->email;
				$input["mutation_type"] = DEBET_MUTATION_TYPE;
				$input["trx_type"] = WITHDRAW_ACCOUNT_TYPE;
				$input["pg_method_seq"] = PAYMENT_SEQ_DEPOSIT;
				$input["name"] = $value->name;
				$input["status"] = WEB_STATUS_CODE;
				$input["trx_no"] = $refund_code;
				$input["non_deposit_trx_amt"] = '0';
				$input["bank_name"] = $value->bank_name;
				$input["bank_branch_name"] = $value->bank_branch_name;
				$input["bank_acct_no"] = $value->bank_acct_no;
				$input["bank_acct_name"] = $value->bank_acct_name;
				$input["deposit_amt"] = $value->deposit_amt;
			}
		}
		$amount = check_object_exit($params, 'amount', 406);
		if ($amount <= '0')
		{
			show_response(406, ERROR_DEPOSIT_NOMINAL);
			exit();
		}
		if ($amount >= $input["deposit_amt"])
		{
			show_response(406, ERROR_DEPOSIT);
			exit();
		}

		$input['deposit_trx_amt'] = $amount;
		$this->Member_model->save_member_account($input);

		$input_mail = new stdClass();
		$input_mail->deposit_trx_amt = $amount;
		$input_mail->email_cd = MEMBER_WITHDRAW_CODE;
		$input_mail->email_code = $code_mail;
		$input_mail->RECIPIENT_NAME = $input["name"];
		$input_mail->TOTAL_DEPOSIT = $input["deposit_trx_amt"];
		$input_mail->LINK = $this->config->item('website_url') .
			"member/verifikasi/" . $code_mail;
		$input_mail->to_email = $input["email"];

		$email = get_email_template($input_mail);
		$subject = $email['subject'];
		$content = $email['content'];
		$send_email = send_mail_log($input_mail->RECIPIENT_NAME, $input_mail->to_email, $subject, $content, $input_mail->email_cd, $input_mail->email_code);

		if ($send_email['sent_status'] === TRUE)
		{ //if send mail is successfully
			show_response(200);
			exit();
		}
		show_response(500);
	}

	private function _review_admin()
	{
		$offset = get('single', START_OFFSET, TRUE);
		$params = get('object');
		$order = isset($params->order) ? ($params->order) : '';
		$order_code = isset($params->order_code) ? get_order_by($params->order_code) : 'DESC';
		$default_order = array(
			'order_no' => 't_o.order_no',
			'order_date' => 't_o.order_date',
			'product_name' => 'p.`name`',
			'rate' => 'mpr.`rate`',
			'created_date' => 'mpr.`created_date');
		$statement_order = '';
		if (array_key_exists($order, $default_order))
		{
			$statement_order = $default_order[$order] . ' ' . $order_code;
		}

		$limit = get_offset($offset, 10);
		$limit_1 = $limit[0];
		$limit_2 = $limit[1];
		$output = array();
		$query = $this->Member_model->get_review_product_list_admin($params->member_seq, $limit_1, $limit_2, $statement_order);
		$lists = $query->query_data;
		$query_row = $query->query_row;
		$base_url = base_url() . 'v1/member/review_admin';
		$this->paging_data_admin_review = post_paging_link($base_url, $query_row, 10, START_OFFSET);
		if ($lists)
		{
			foreach ($lists->result() as $value)
			{
				$input["order_seq"] = $value->order_seq;
				$input["product_variant_seq"] = $value->product_variant_seq;
				$input["image_url"] = '';
				if (get_base_source() != NULL && check_constant("PRODUCT_UPLOAD_IMAGE") != '' && $value->merchant_seq != '' && check_constant("XS_IMAGE_UPLOAD") != '' && $value->pic_1_img != '')
				{
					$input["image_url"] = get_base_source() . check_constant("PRODUCT_UPLOAD_IMAGE") . $value->merchant_seq . "/" . check_constant("XS_IMAGE_UPLOAD") . $value->pic_1_img;
				}
				$input["product_name"] = $value->product_name;
				$input["product_url"] = '';
				if (website_url() != NULL && $value->product_name != '' && $value->variant_seq != '' && $value->product_variant_seq != '')
				{
					$input["product_url"] = website_url() . strtolower(url_title($value->product_name . get_variant_value($value->variant_seq, $value->variant_value, " "))) . '-' . $value->product_variant_seq;
				}
				$input["order_no"] = $value->order_no;
				$input["order_date"] = $value->order_date;
				$input["rate"] = $value->rate;
				$input["created_date"] = $value->created_date;
				$input["review"] = $value->review;
				$output[] = $input;
			}
		}
		return $output;
	}

	private function _review_member()
	{
		$offset = get('single', START_OFFSET, TRUE);
		$params = get('object');
		$order = isset($params->order) ? ($params->order) : '';
		$order_code = isset($params->order_code) ? get_order_by($params->order_code) : 'DESC';
		$default_order = array(
			'order_no' => 't_o.order_no',
			'order_date' => 't_o.order_date',
			'product_name' => 'p.`name`');
		$statement_order = '';
		if (array_key_exists($order, $default_order))
		{
			$statement_order = $default_order[$order] . ' ' . $order_code;
		}
		$limit = get_offset($offset, 10);
		$limit_1 = $limit[0];
		$limit_2 = $limit[1];
		$output = array();
		$query = $this->Member_model->get_review_product_list_member($params->member_seq, $limit_1, $limit_2, $statement_order);
		$lists = $query->query_data;
		$query_row = $query->query_row;
		$base_url = base_url() . 'v1/member/review_member';
		$this->paging_data_member_review = post_paging_link($base_url, $query_row, 10, START_OFFSET);
		if ($lists)
		{
			foreach ($lists->result() as $value)
			{
				$input["order_seq"] = $value->order_seq;
				$input["product_variant_seq"] = $value->product_variant_seq;
				$input["image_url"] = '';
				if (get_base_source() != NULL && check_constant("PRODUCT_UPLOAD_IMAGE") != '' && $value->merchant_seq != '' && check_constant("XS_IMAGE_UPLOAD") != '' && $value->pic_1_img != '')
				{
					$input["image_url"] = get_base_source() . check_constant("PRODUCT_UPLOAD_IMAGE") . $value->merchant_seq . "/" . check_constant("XS_IMAGE_UPLOAD") . $value->pic_1_img;
				}

				$input["product_name"] = $value->product_name;
				$input["product_url"] = '';
				if (website_url() != NULL && $value->product_name != '' && $value->variant_seq != '' && $value->product_variant_seq != '')
				{
					$input["product_url"] = website_url() . strtolower(url_title($value->product_name . get_variant_value($value->variant_seq, $value->variant_value, " "))) . '-' . $value->product_variant_seq;
				}
				$input["order_no"] = $value->order_no;
				$input["order_date"] = $value->order_date;

				$output[] = $input;
			}
		}
		return $output;
	}

	private function _insert_review_member()
	{
		$params = get('object');
		$member_seq = check_object_exit($params, 'member_seq');
		$product_variant_seq = check_object_exit($params, 'product_variant_seq');
		$order_seq = check_object_exit($params, 'order_seq');
		$rate = check_object_exit($params, 'rate');
		$review = check_object_exit($params, 'review');
		$data = $this->Member_model->review_product_list_member($product_variant_seq, $order_seq, $rate, $review, $member_seq);
		return $data;
	}

	public function review_admin()
	{
		$this->history->set_api_name('_review admin_');
		$this->_check_action(array(READ));
		$params = get('object');
		switch ($params->action)
		{
			case READ:
				$data = array('admin' => array('items' => $this->_review_admin()) + $this->paging_data_admin_review);
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			default:
				show_response(406, "action not allowed");
		}
	}

	public function review_member()
	{
		$this->_check_action(array(READ, CREATE));
		$params = get('object');
		switch ($params->action)
		{
			case READ:
				$this->history->set_api_name('_review member_');
				$data = array('member' => array('items' => $this->_review_member()) + $this->paging_data_member_review);
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			case CREATE:
				$this->history->set_api_name('_insert review member_');
				$data = $this->_insert_review_member();
				$response = array('code' => 304, 'message' => 'Failed to save review', 'data' => array());
				if ($data)
				{
					$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => array());
				}
				show_response_custom($response);
				break;
			default:
				show_response(406, "action not allowed");
		}
	}

	public function review()
	{
		$this->history->set_api_name('_review_');
		$this->_check_action(array(READ));
		$params = get('object');
		switch ($params->action)
		{
			case READ:
				$data = array(
					'member' => array('items' => $this->_review_member()) + $this->paging_data_member_review,
					'admin' => array('items' => $this->_review_admin()) + $this->paging_data_admin_review,
				);
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			default:
				show_response(406, "action not allowed");
		}
	}

	function upload_img()
	{
		$this->history->set_api_name('_Uploads Profile Images_');
		$this->config->load('image_profile');
		$config = $this->config->item('image_profile');
		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if (!$this->upload->do_upload())
		{
			show_response(406, 'error upload image profile');
		}
		else
		{
			$query = $this->Member_model->find_member($this->member_seq);
			$member = $query->query_data;
			if ($member)
			{
				$data = array();
				foreach ($member->result() as $value)
				{
					if ($value->profile_img != "")
					{
						if (file_exists('../' . ORDER_UPLOAD_IMAGE . $value->profile_img))
						{
							unlink('../' . ORDER_UPLOAD_IMAGE . $value->profile_img);
						}
					}
					$detail = array();
					$detail["profile_img"] = $config['file_name'];
					$data = $detail;
				}

				$this->Member_model->save_update($data, $this->member_seq);
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
			}
		}
	}

}

/* End of file Member.php */
	/* Location: ./application/controller/V1/Member.php */	