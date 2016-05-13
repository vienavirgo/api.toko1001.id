<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends APITOKO1001_Controller {

	var $code = '';
	var $message = '';

	function __construct()
	{
		parent::__construct();
		$this->base_source = $this->config->item('base_source');
		$this->load->model('V1/Transaction_model');
		$this->load->model('V1/Member_model');
		$this->load->model('V1/Merchant_model');
		$this->load->model('V1/Expedition_model');
		$this->load->library('Email');

		$this->_initialize();
	}

	private function _initialize()
	{
		$header_request = 'member_order';
		$this->member_order = check_current_exit($this, array('decode_jsondata', $header_request));		
		$data_token = check_token($header_request);
		$this->member_token = $data_token['member_token'];
		$this->member_seq = $data_token['member_seq'];
	}

	private function _check_action($action_allowed = array())
	{
		$action = $this->member_order->action;
		if (!in_array($action, $action_allowed))
		{
			show_response(406, "action not allowed");
			exit();
		}
	}

	public function index()
	{
		$this->_check_action(array(CREATE));
		switch ($this->member_order->action)
		{
			case CREATE:
				$data = $this->_create_order();
				$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
				show_response_custom($response);
				break;
			default:
				show_response(406, "action not allowed");
				exit();
		}
	}

	private function _create_order()
	{
		$this->history->set_api_name('_ORDER PAYMENT MEMBER_');
		$params = new stdClass();
		$order = $this->member_order;
		$order->payment_status = PAYMENT_UNPAID_STATUS_CODE;
		$order->order_no = $this->get_order_no();
		$dt_order = array();
		$order_detail = array();
		$order_seq = $this->Transaction_model->get_last_seq((array) $order);
		$add_order = $this->Transaction_model->save_add_order((array) $order);
		if ($add_order == FALSE)
		{
			show_response(400);
			exit();
		}
		$merchant_list = $order->merchant;
		$price = 0;
		$total_ins = 0;
		$total_ship_real = 0;
		$total_ship_charged = 0;
		$total_price = 0;
		$total_exp = 0;
		$total_payment = 0;
		if ($order->merchant != "")
		{
			$i = 0;
			foreach ($merchant_list as $val)
			{
				$i++;
				$get_merchant_info = $this->Transaction_model->find_merchant_info($val->merchant_seq);
				$dt_order['merchant_info_seq'] = isset($get_merchant_info->seq) ? $get_merchant_info->seq : 0;
				$dt_order['member_seq'] = $this->member_seq;
				$dt_order['order_seq'] = $order_seq;
				$dt_order['expedition_service_seq'] = isset($val->exp_service_seq) ? $val->exp_service_seq : NULL;
				$dt_order['real_expedition_service_seq'] = isset($val->expedition_seq) ? $val->expedition_seq : NULL;
				$dt_order['free_fee_seq'] = isset($val->exp_promo_seq) ? $val->exp_promo_seq : NULL;
				$dt_order['order_status'] = ORDER_PREORDER_STATUS_CODE;
				$dt_order['member_notes'] = isset($val->merchant_message) ? $val->merchant_message : "";
				$dt_order['ref_awb_no'] = $order->order_no . ($i > 9 ? $i : "0" . $i);
				$dt_order['awb_no'] = "";
				$dt_order['ship_by'] = "";
				$dt_order['ship_note_file'] = "";
				$dt_order['ship_notes'] = "";
				$dt_order['received_by'] = "";
				$exp_real_fee = $val->exp_real_fee;
				$product_order_list = $val->product;
				$order_detail = $dt_order;
				$merchant_order = $this->Transaction_model->save_order_merchant((array) $order_detail);
				if ($merchant_order == FALSE)
				{
					show_response(400, 'merchant order failed');
					exit();
				}
				foreach ($product_order_list as $p_order)
				{
					$query = $this->Transaction_model->get_trx($p_order->product_variant_seq);
					if ($query)
					{
						$trx_result = ($query->query_data->result());
						foreach ($trx_result as $trx_val)
						{
							$trx_free = $trx_val->trx_free;
							$trx_free1 = $trx_val->trx_free1;
						}
						if ($trx_free == NULL)
						{
							$trx_free = $trx_free1;
						}
					}

					$query_ins = $this->Transaction_model->get_ins($order_seq, $val->merchant_seq);
					$ins_rate_percent = 0;
					if (isset($query_ins))
					{
						$ins_rate_percent = $query_ins->ins_rate_percent;
					}
					$qty = isset($p_order->quantity) ? $p_order->quantity : 0;
					$sell_price = isset($p_order->sell_price) ? $p_order->sell_price : 0;
					$weight = isset($p_order->product_weight) ? $p_order->product_weight : 0;
					$rate = isset($val->rate_exp) ? $val->rate_exp : 0;

					$detail_prod['order_seq'] = $order_seq;
					$detail_prod['ins_rate_percent'] = $ins_rate_percent;
					$detail_prod['trx_fee_percent'] = isset($trx_free) ? $trx_free : "0.00";
					$detail_prod['member_seq'] = $this->member_seq;
					$detail_prod['trx_no'] = $order->order_no;
					$detail_prod['merchant_info_seq'] = isset($get_merchant_info->seq) ? $get_merchant_info->seq : 0;
					$detail_prod['product_variant_seq'] = isset($p_order->product_variant_seq) ? $p_order->product_variant_seq : 0;
					$detail_prod['variant_value_seq'] = DEFAULT_VALUE_VARIANT_SEQ;
					$detail_prod['qty'] = $qty;
					$detail_prod['sell_price'] = $sell_price;
					$detail_prod['weight_kg'] = $weight;
					$detail_prod['ship_price_real'] = $exp_real_fee;
					$detail_prod['ship_price_charged'] = isset($val->exp_promo_seq) ? "0" : $val->rate_exp;
					$detail_prod['product_status'] = PRODUCT_READY_STATUS_CODE;

					$detail = $detail_prod;

					$product_order = $this->Transaction_model->save_order_product((array) $detail);
					if ($product_order == FALSE)
					{
						show_response(400, 'product order failed');
						exit();
					}
					$price += $qty * $sell_price;
					$total_ins += ($qty * $sell_price * $ins_rate_percent / 100);
					$total_ship_real += $exp_real_fee * ceil($qty * $weight);
					$total_ship_charged += $rate * ceil($qty * $weight);
					//update stock
					
					$this->save_substrans_stock($detail);

					$params->product_variant_seq = $p_order->product_variant_seq;
					$get_coupon = $this->Expedition_model->coupon($params);

					$coupon_data = $get_coupon->query_data;
					$coupon_amount = 0;
					if ($coupon_data->num_rows() > 0 && $coupon_data->num_rows() <= 10)
					{
						foreach ($coupon_data->result() as $cp)
						{
							$coupon_amount = $cp->nominal;
						}
					}
				}

				$dt_merchant['order_seq'] = $order_seq;
				$dt_merchant['total_merchant'] = $price;
				$dt_merchant['total_ins'] = $total_ins;
				$dt_merchant['total_ship_real'] = $total_ship_real;
				$dt_merchant['total_ship_charged'] = $total_ship_charged;
				$merchant_trans = $dt_merchant;
				$total_price = $price;
				$total_exp = $total_ship_charged;

				// update total amount/ cant execute once time couse db problem
				// t_order_merchant
				$update_trans_merchant = $this->Transaction_model->update_trans_merchant($merchant_trans);
				if ($update_trans_merchant == FALSE)
				{
					show_response(400, 'update trans merchant failed');
					exit();
				}
// update total_order  t_order and check the coupon and voucher
				$get_voucher = $this->Transaction_model->voucher((array) $order);
				$voucher_amount = 0;
				if (isset($get_voucher))
				{
					$voucher_amount = $get_voucher->nominal;
				}
			}
			// return total_payment
			$total_order = $price + $total_ship_charged;
			$total_payment = $total_price + $total_exp - ($voucher_amount + $coupon_amount);
			$order->payment_amount = $total_payment;
			$update_order = $this->Transaction_model->update_trans_order($total_payment, $total_order, $order_seq);
			if ($update_order == FALSE)
			{
				show_response(400, 'update order failed');
				exit();
			}
			if (isset($order->voucher_seq))
			{
				$voucher = $this->Transaction_model->use_voucher_update((array) $order);
				if ($voucher == FALSE)
				{
					show_response(400, 'voucher failed');
					exit();
				}
			}
			if (isset($order->coupon_seq))
			{
				$coupon = $this->Transaction_model->use_coupon_update((array) $order);
				if ($coupon == FALSE)
				{
					show_response(400, 'coupon failed');
					exit();
				}
			}

			$this->check_payment_gateway_method($order);
			$this->send_email($order->order_no, $order->member_seq);
			//get return data
			$this->payment_confirm($order->order_no, $order->member_seq);
		}
		else
		{
			show_response(506);
		}
		return $this->data;
	}

	protected function get_order_no()
	{
		return generate_alnum(3, true) . date("Y") . generate_alnum(4, true) . date("m") . generate_alnum(3, true) . date("d");
	}

	protected function save_substrans_stock($data_stock)
	{
		$last_stock = 0;
		$params = new stdClass();
		$params->member_seq = $data_stock['member_seq'];
		$params->product_variant_seq = $data_stock['product_variant_seq'];
		$params->variant_value_seq = $data_stock['variant_value_seq'];
		$params->qty = $data_stock['qty'];
		$params->mutation_type = OUT_MUTATION_TYPE;
		$params->trx_type = STOCK_ORDER_MEMBER_TYPE;
		$params->trx_no = $data_stock['trx_no'];
		$query_stock = $this->Transaction_model->get_stock_product($data_stock);
		$params->stock = 0;
		if (isset($query_stock))
		{
			$params->stock = $query_stock->stock;
		}
		if ($params->stock < $data_stock['qty'] && $params->stock != 0)
		{
			show_response(406);
			exit();
		}
		$update_stock = $this->Transaction_model->save_subtrans_stock((array) $params);
		$add_stock_log = $this->Transaction_model->save_merchant_stock_log((array) $params);
		$query_last_stock = $this->Transaction_model->get_latest_product_stock((array) $params);
		if (isset($query_last_stock))
		{
			$last_stock = $query_last_stock->last_stock;
		}
	
		if ($last_stock == 0)
		{
			$gencode = generate_random_text(20, true);
			$get_merchant = $this->Merchant_model->get_merchant_by_product_var($params->product_variant_seq);
			$email_params = new stdClass();
			$email_params->member_seq = $data_stock['member_seq'];
			$email_params->RECIPIENT_NAME = $get_merchant->merchant_name;
			$email_params->PRODUCT_NAME = $get_merchant->product_name;
			$email_params->email_cd = MERCHANT_STOCK_EXCEEDED;
			$email_params->PRODUCT_VALUE = $get_merchant->value;
			$email_params->email_code = $gencode;
			$email_params->to_email = $get_merchant->merchant_email;
			//$email_params->to_email = 'rully_mart@yahoo.com';
			$email_params->LINK_MERCHANT = base_url() . "v1/merchant/" . $get_merchant->code;
			$email = get_email_template($email_params);
			$subject = $email['subject'];
			$content = $email['content'];

			$send_email = send_mail_log($get_merchant->merchant_name, $email_params->to_email, $subject, $content, 'MERCHANT_STOCK_EXCEEDED', $email_params->email_code);
		}
	}

	protected function check_payment_gateway_method($data)
	{
		switch ($data->payment_code)
		{
			case PAYMENT_TYPE_DEPOSIT: {
					$query = $this->Member_model->find_member($data->member_seq);
					$member = $query->query_data->result();

					if ($member[0]->deposit_amt < $data->payment_amount)
					{
						show_response(400, "Deposit Tidak Mencukupi");
						exit();
					};
				};
				break;
		}
	}

	protected function send_email($order_no, $member_seq)
	{
		$order_content = "<table border='1'>
                           <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga (Rp)</th>
                                <th>Biaya Kirim (Rp)</th>
                                <th>Total (Rp)</th>
                            </tr>
                           </thead><tbody>";
		$query_prod = $this->Transaction_model->get_order_product($order_no);
		$order_product = $query_prod->query_data;
		foreach ($order_product->result() as $product)
		{
			$order_content = $order_content . "<tr>
                                               <td>" . $product->display_name . "</td>
                                               <td>" . $product->qty . "</td>
                                               <td >" . number_format($product->sell_price) . "</td>
                                               <td >" . number_format(ceil($product->weight_kg * $product->qty) * $product->ship_price_charged) . "</td>
                                               <td > " . number_format(ceil($product->weight_kg * $product->qty) * $product->ship_price_charged + $product->sell_price * $product->qty) . "</td></tr>";
		}

		$get_member_order = $this->Transaction_model->get_member_order($order_no);
		if (isset($get_member_order))
		{
			$order_content = $order_content . "</tbody>
                                          <tr>
                                            <td colspan=4>Voucher</td>
                                            <td>" . number_format($get_member_order->nominal) . "</td>
                                          </tr>
                                          <tr>
										  <tr>
                                            <td colspan=4>Kupon</td>
                                            <td>" . number_format($get_member_order->coupon_nominal) . "</td>
                                          </tr>
                                          <tr>
                                            <td colspan=4>Total Bayar</td>
                                            <td>" . number_format($get_member_order->total_payment) . "</td>
                                          </tr>
                                          </table>";

			$address_content = "Penerima : " . $get_member_order->receiver_name . "<br>
                            Alamat : " . $get_member_order->receiver_address . "-" . $get_member_order->province_name . "-" . $get_member_order->city_name . "-" . $get_member_order->district_name . "<br>
                            No Tlp : " . $get_member_order->receiver_phone_no . "<br>";


			$info_bank = "<strong>3. Info Bank</strong><br/><table border='1'><thead>
                    <tr>
                        <th>Bank</th>
                        <th>Nomor Rekening</th>
                        <th>A/n</th>
                    </tr></thead><tbody>";

			$query_bank = $this->Transaction_model->get_bank_list();
			$bank_list = $query_bank->query_data;
			foreach ($bank_list->result() as $bank)
			{
				$info_bank = $info_bank . "<tr>
                                         <td>" . $bank->bank_name . "</td>
                                         <td>" . $bank->bank_acct_no . "</td>
                                         <td>" . $bank->bank_acct_name . "</td>
                                       </tr>";
			}
			$info_bank = $info_bank . "</tbody></table><br>";
			$params = new stdClass();
			$params->member_seq = $member_seq;
			$params->email_cd = ORDER_INFO;
			$params->to_email = $get_member_order->email;
			//$params->to_email = 'rully_mart@yahoo.com';
			$params->ORDER_NO = $get_member_order->order_no;
			$params->RECIPIENT_NAME = $get_member_order->member_name;
			$params->PAYMENT_LINK = base_url() . "member/payment/" . $order_no;
			$params->RECIPIENT_ADDRESS = $address_content;
			$params->ORDER_DATE = date("d-M-Y", strtotime($get_member_order->order_date));
			$params->ORDER_ITEMS = $order_content;
			$params->INFO_BANK = $get_member_order->payment_code == PAYMENT_TYPE_BANK ? $info_bank : "";
			$params->CONFIRM_LINK = base_url() . "member/payment/" . $get_member_order->order_no;
			$gencode = generate_random_text(20, true);
			$params->email_code = $gencode;
			$email = get_email_template($params);
			$subject = $email['subject'];
			$content = $email['content'];
			$send_email = send_mail_log($get_member_order->member_name, $params->to_email, $subject, $content, 'ORDER_INFO', $params->email_code);
		}
	}

	protected function payment_confirm($order_no, $member_seq)
	{
		$data_product = array();
		$data_bank = array();
		$get_member_order = $this->Transaction_model->get_member_order($order_no);
		if (isset($get_member_order))
		{
			if ($get_member_order->payment_status == PAYMENT_UNPAID_STATUS_CODE || $get_member_order->payment_status == PAYMENT_WAIT_CONFIRM_STATUS_CODE)
			{
				$this->data['data_order_member'] = $get_member_order;
				$qry_product = $this->Transaction_model->get_order_product($order_no);
				$product_orders = $qry_product->query_data->result();

				foreach ($product_orders as $value)
				{
					$detail_product = array();
					$detail_product['order_seq'] = $value->order_seq;
					$detail_product['merchant_seq'] = $value->merchant_seq;
					$detail_product['merchant_info_seq'] = $value->merchant_info_seq;
					$detail_product['display_name'] = $value->display_name;
					$detail_product['product_seq'] = $value->product_seq;
					$detail_product['img'] = $value->img;
					$detail_product['image_url'] = get_base_source() . PRODUCT_UPLOAD_IMAGE . $value->merchant_seq . '/' . S_IMAGE_UPLOAD . $value->img;
					$detail_product['value'] = $value->value;
					$detail_product['value_seq'] = $value->value_seq;
					$detail_product['variant_name'] = $value->variant_name;
					$detail_product['product_status'] = $value->product_status;
					$detail_product['qty'] = $value->qty;
					$detail_product['sell_price'] = $value->sell_price;
					$detail_product['weight_kg'] = $value->weight_kg;
					$detail_product['ship_price_real'] = $value->ship_price_real;
					$detail_product['ship_price_charged'] = $value->ship_price_charged;
					$detail_product['trx_fee_percent'] = $value->trx_fee_percent;
					$detail_product['ins_rate_percent'] = $value->ins_rate_percent;
					$detail_product['qty_return'] = $value->qty_return;
					$data_product[] = $detail_product;
				}
				$this->data['data_order_product'] = $data_product;
				$qry_merchant = $this->Transaction_model->get_order_merchant($order_no);
				$qry_bank = $this->Transaction_model->get_bank_list();
				$this->data['data_order_merchant'] = $qry_merchant->query_data->result();
				$bank_list = $qry_bank->query_data->result();
				foreach ($bank_list as $bank)
				{
					$detail_bank = array();
					$detail_bank['seq'] = $bank->seq;
					$detail_bank['bank_name'] = $bank->bank_name;
					$detail_bank['bank_branch_name'] = $bank->bank_branch_name;
					$detail_bank['bank_acct_no'] = $bank->bank_acct_no;
					$detail_bank['bank_acct_name'] = $bank->bank_acct_name;
					$detail_bank['logo_img'] = $bank->logo_img;
					$detail_bank['logo_img_url'] = get_base_source() . BANK_UPLOAD_IMAGE . $bank->logo_img;
					$data_bank[] = $detail_bank;
				}
				$this->data['data_bank_list'] = $data_bank;
				$this->get_payment_type_order($get_member_order);
			}
		}
	}

	public function get_payment_type_order($data)
	{
		switch ($data->payment_code)
		{
			case PAYMENT_TYPE_BANK:$this->payment_type_bank($data);
				break;
			case PAYMENT_TYPE_DEPOSIT: $this->payment_type_deposit($data);
				break;
			case PAYMENT_TYPE_MANDIRI_KLIKPAY: $this->payment_type_mandiri_click_pay($data);
				break;
			case PAYMENT_TYPE_BCA_KLIKPAY:
				break;
			case PAYMENT_TYPE_CREDIT_CARD: $this->payment_type_credit_card($data);
				break;
			case PAYMENT_TYPE_MANDIRI_ECASH : $this->payment_type_mandiri_ecash($data);
				break;
			case PAYMENT_TYPE_DOCU_ATM : $this->payment_type_docu_atm($data);
				break;
			case PAYMENT_TYPE_DOCU_ALFAMART : $this->payment_type_docu_alfamart($data);
				break;
		}
	}

	public function payment_type_bank($data)
	{
		$params = new stdClass();
		$params->member_seq = $data->member_seq;
		$params->order_seq = $data->seq;
		$params->payment_status = PAYMENT_WAIT_CONFIRM_STATUS_CODE;
		$params->order_status = ORDER_PREORDER_STATUS_CODE;
		$params->paid_date = date('Y-m-d');
		$this->Transaction_model->update_status_order($params);
		$this->Transaction_model->update_status_merchant($params);

		$params_payment = new stdClass();
		$params_payment->member_seq = $data->member_seq;
		$params_payment->order_seq = $data->seq;
		$params_payment->order_no = $data->order_no;
		$params_payment->payment_method_seq = $data->pg_method_seq;
		$params_payment->request = "";
		$params_payment->request_date = "";
		$params_payment->response = "";
		$params_payment->response_date = "";
		$params_payment->signature = "";

		$this->Transaction_model->save_payment_log((array) $params_payment);
	}

	public function payment_type_deposit($data)
	{
		$params = new stdClass();
		$params->member_seq = $data->member_seq;
		$params->order_seq = $data->seq;
		$params->payment_status = PAYMENT_PAID_STATUS_CODE;
		$params->order_status = ORDER_NEW_ORDER_STATUS_CODE;
		$params->paid_date = date('Y-m-d H:i:s');

		$this->Transaction_model->update_status_order($params);
		$this->Transaction_model->update_status_merchant($params);

		$params_payment = new stdClass();
		$params_payment->member_seq = $data->member_seq;
		$params_payment->order_seq = $data->seq;
		$params_payment->order_no = $data->order_no;
		$params_payment->payment_method_seq = $data->pg_method_seq;
		$params_payment->request = "";
		$params_payment->request_date = "";
		$params_payment->response = "";
		$params_payment->response_date = "";
		$params_payment->signature = "";

		$this->Transaction_model->save_payment_log((array) $params_payment);
		$this->substract_member_account_deposit($data);

		$get_member_order = $this->data["data_order_member"];
		if (isset($get_member_order))
		{
			$order_content = "<table border='1'>
                           <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga (Rp)</th>
                                <th>Biaya Kirim (Rp)</th>
                                <th>Total (Rp)</th>
                            </tr>
                           </thead><tbody>";
			$order_product = $this->data['data_order_product'];
			foreach ($order_product as $product)
			{
				$order_content = $order_content . "<tr>
                                               <td>" . $product->display_name . "</td>
                                               <td>" . $product->qty . "</td>
                                               <td>" . number_format($product->sell_price) . "</td>
                                               <td>" . number_format(ceil($product->weight_kg * $product->qty) * $product->ship_price_charged) . "</td>
                                               <td>" . number_format(ceil($product->weight_kg * $product->qty) * $product->ship_price_charged + $product->sell_price * $product->qty) . "</td></tr>";
			}

			$order_content = $order_content . "</tbody>
                                          <tr>
                                            <td colspan=4>Voucher</td>
                                            <td>" . number_format($get_member_order->nominal) . "</td>
                                          </tr>
                                          <tr>
                                            <td colspan=4>Total Bayar</td>
                                            <td>" . number_format($get_member_order->total_payment) . "</td>
                                          </tr>
                                          </table>";

			$address_content = "Penerima : " . $get_member_order->receiver_name . "<br>
        Alamat : " . $get_member_order->receiver_address . "-" . $get_member_order->province_name . "-" . $get_member_order->city_name . "-" . $get_member_order->district_name . "<br>
        No Tlp : " . $get_member_order->receiver_phone_no . "<br>";
			$params_email_member = new stdClass();
			$params_email_member->member_seq = $data->member_seq;
			$params_email_member->email_cd = ORDER_PAY_CONFIRM_SUCCESS_CODE;
			$params_email_member->RECIPIENT_NAME = $get_member_order->member_name;
			$params_email_member->ORDER_NO = $get_member_order->order_no;
			$params_email_member->ORDER_DATE = date("d-M-Y", strtotime($get_member_order->order_date));
			$params_email_member->RECIPIENT_ADDRESS = $address_content;
			$params_email_member->TOTAL_PAYMENT = $get_member_order->total_payment;
			$params_email_member->PAYMENT_METHOD = $get_member_order->payment_name;
			$params_email_member->ORDER_ITEMS = $order_content;
			$params_email_member->CONFIRM_LINK = base_url("member/profile/order_list_detail" . "/" . $get_member_order->order_no);
			$params_email_member->to_email = $get_member_order->email;
			//$params_email_member->to_email = 'rully_mart@yahoo.com';
			$gencode = generate_random_text(20, true);
			$params_email_member->email_code = $gencode;
			$email = get_email_template($params_email_member);
			$subject = $email['subject'];
			$content = $email['content'];
			$send_email = send_mail_log($get_member_order->member_name, $params_email_member->to_email, $subject, $content, 'ORDER_INFO', $params_email_member->email_code);

			$order_merchant = $this->data['data_order_merchant'];

			$params_email_merchant = new stdClass();
			foreach ($order_merchant as $merchant)
			{
				$params_email_merchant->member_seq = $data->member_seq;
				$params_email_merchant->email_cd = ORDER_NEW_CODE;
				$params->order_seq = $data->seq;
				$params->merchant_info_seq = $merchant->merchant_info_seq;

				$product_order_content = "";
				$product_order_content = "<table border='1'>
                                                            <thead>
                                                             <tr>
                                                                 <th>Produk</th>
                                                                 <th>Tipe Product</th>
                                                                 <th>Qty</th>
                                                             </tr>
                                                            </thead><tbody>";

				$qry_merch = $this->Transaction_model->get_product_by_merchant($params);
				$merchant_product = $qry_merch->query_data->result();
				foreach ($merchant_product as $product_merchant)
				{
					$product_order_content = $product_order_content . "<tr>
                                                                    <td>" . $product_merchant->product_name . "</td>
                                                                    <td>" . $product_merchant->variant_name . "</td>
                                                                    <td>" . $product_merchant->qty . "</td></tr>";
				}
				$product_order_content = $product_order_content . "</tbody> </table>";
				$params_email_merchant->ORDER_ITEMS = $product_order_content;
				$params_email_merchant->RECIPIENT_NAME = $merchant->merchant_name;
				$params_email_merchant->ORDER_NO = $get_member_order->order_no;
				$params_email_merchant->ORDER_DATE = date("d-M-Y", strtotime($get_member_order->order_date));
				$params_email_merchant->RECIPIENT_ADDRESS = $address_content;
				$params_email_merchant->email_code = generate_alnum(20, true);
				$params_email_merchant->to_email = $merchant->email;
				//$params_email_merchant->to_email = 'rully_mart@yahoo.com';
				$email = get_email_template($params_email_merchant);
				$subject = $email['subject'];
				$content = $email['content'];
				$send_email = send_mail_log($merchant->merchant_name, $params_email_merchant->to_email, $subject, $content, 'ORDER_NEW_CODE', $params_email_merchant->email_code);
			}
		}
	}

	public function substract_member_account_deposit($data)
	{
		$params = new stdClass();
		$params->member_seq = $data->member_seq;
		$params->account_type = ORDER_ACCOUNT_TYPE;
		$params->mutation_type = DEBET_MUTATION_TYPE;
		$params->pg_method_seq = $data->pg_method_seq;
		$params->trx_no = $data->order_no;
		$params->trx_date = date('Y-m-d H:i:s');
		$params->deposit_trx_amt = $data->total_payment;
		$params->non_deposit_amt = 0;
		$params->bank_name = "";
		$params->bank_branch_name = "";
		$params->bank_account_no = "";
		$params->bank_account_name = "";
		$params->status = APPROVE_STATUS_CODE;
		$this->Transaction_model->save_member_account((array) $params);
		$this->Transaction_model->update_deposit_member((array) $params);
	}

	private function payment_type_mandiri_click_pay($data)
	{

		$params = new stdClass();
		$params->member_seq = $data->member_seq;
		$params->order_seq = $data->seq;

		$data_items = array(array("product_title" => "TOTAL_PRODUCT", 'qty' => '1', 'price' => $data->total_payment));
		$signature = md5(MANDIRI_CLICKPAY_MERCHANT_ID . $data->total_payment . $data->order_trans_no . MANDIRI_CLICKPAY_MERCHANT_PWD);
		$xmlParams = ' <inputtrx_request> 
                            <user_id>' . MANDIRI_CLICKPAY_MERCHANT_ID . '</user_id> 
                            <amount>' . $data->total_payment . '</amount> 
                            <transaction_id>' . $data->order_trans_no . '</transaction_id>
                            <date_time>' . date("YmdHis") . '</date_time>
                            <bank_id>1</bank_id> 
                            <signature>' . $signature . '</signature>';
		$xmlParams .= '<items count="' . count($data_items) . '">';

		foreach ($data_items as $idx => $data_item)
		{
			$xmlParams .= '<item no="' . ($idx + 1) . '" name="' . $data_item['product_title'] . '" qty="' . $data_item['qty'] . '" price="' . str_replace(',', '', number_format($data_item['price'], 0)) . '" /> ';
		}
		$this->curl_api->set_url(MANDIRI_CLICKPAY_INSERTPAYMENT);
		$this->curl_api->setIsUsingUserAgent(true);
		$xmlParams .= '</items> </inputtrx_request>';
		$this->curl_api->setParams($xmlParams);
		$this->curl_api->setData();
		$response = $this->curl_api->getResponse();
		$xmlObject = simplexml_load_string($response);

		$params_payment = new stdClass();
		$params_payment->member_seq = $data->member_seq;
		$params_payment->order_seq = $data->seq;
		$params_payment->order_no = $data->order_no;
		$params_payment->payment_method_seq = $data->pg_method_seq;
		$params_payment->request = "[user_id]=" . $xmlObject->user_id . "
                                        [transaction_id]=" . $xmlObject->transaction_id . "
                                        [billreferenceno    ]=" . $xmlObject->billreferenceno . "
                                        [response_code]=" . $xmlObject->response_code . "
                                        [response_desc]" . $xmlObject->response_desc;
		$params_payment->request_date = date('Y-m-d H:i:s');
		$params_payment->response = "";
		$params_payment->response_date = "";
		$params_payment->signature = $xmlObject->billreferenceno;

		$params->signature = $xmlObject->billreferenceno;
		var_dump($params_payment);
		exit();
//		$this->M_order_member->save_update_order_signature($params);
//		$this->M_payment_member->save_payment_log($params_payment);


		if ($xmlObject->response_code == '0000')
		{
			header("Location: " . MANDIRI_CLICKPAY_REDIRECT_URL . "?user_id=" . MANDIRI_CLICKPAY_MERCHANT_ID . "&billreferenceno=" . $xmlObject->billreferenceno);
			die();
		}
	}

}

/* End of file Transaction.php */
/* Location: ./application/controller/V1/Transaction.php */


