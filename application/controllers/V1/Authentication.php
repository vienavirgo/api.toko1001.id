<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_controller {

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
	}

	public function login()
	{
		$status_allowed = "'A'";
		$params = get('object');
		$email = check_current_exit($params, array('email'));
		$password = check_current_exit($params, array('password'));
		$password = md5($password);
		$data = array(
			'member_info' => $this->_member_info($status_allowed, $email, $password),
			'member_address' => $this->_member_address(),
			'member_wishlist' => $this->_member_wishlist(),
		);
		$code = $this->code;
		$message = $this->message;
		$response = array('code' => $code, 'message' => $message, 'data' => $data);
		show_response_custom($response);
	}

	public function register()
	{
//		echo $this->_register();
		$params = get('object');
		$gender = check_current_exit($params, array('gender'));
		$password = check_current_exit($params, array('password'));
		$birthday = check_current_exit($params, array('birthday'));
		$email = check_current_exit($params, array('email'));
		$name = check_current_exit($params, array('name'));

		$data = array(
			'register' => $this->_register($gender, $password, $birthday, $email, $name),
		);
		$code = $this->code;
		$message = $this->message;
		$response = array('code' => $code, 'message' => $message, 'data' => $data);
		show_response_custom($response);
	}

	public function login_fb()
	{
		$status_allowed = "'A','N','U'";
		$this->history->set_api_name('_Login with facebook_');
		$params = get('object');
		$params->gender = check_current_exit($params, array('gender'));
		$params->birthday = check_current_exit($params, array('birthday'));
		$params->email = check_current_exit($params, array('email'));
		$params->name = check_current_exit($params, array('name'));
		$params->last_login = '';
		$params->ip_address = '';

		$params->gender = save_gender_format($params->gender);
		if ($params->birthday === NULL)
		{
			$params->birthday = '0000-00-00';
		}
		$email_exist = FALSE;
		$email_valid = FALSE;

		$query_check_user_email = $this->Member_model->check_user_email($params);
		$query_check_user_email_data = $query_check_user_email->query_data;

		if (get_row_query($query_check_user_email) > 0)
		{
			$email_exist = TRUE;
		}

		if (valid_email($params->email) && $params->email !== NULL)
		{
			$email_valid = TRUE;
		}
		$status = '';
		$password = '';
		if ($query_check_user_email_data)
		{
			foreach ($query_check_user_email_data->result() as $result)
			{
				$status = $result->status;
				$password = $result->new_password;
			}
		}

		$data = array(
			'member_info' => new stdClass(),
			'member_address' => array(),
			'member_wishslist' => array(),
		);
		$code = 200;
		$message = $this->status_code->getMessageForCode($code);

		if ($email_exist && $email_valid)
		{
			$message = "Email already registered";
			$data['member_info'] = $this->_member_info($status_allowed, $params->email, $password);
			$data['member_address'] = $this->_member_address();
			$data['member_wishslist'] = $this->_member_wishlist();
			$result = array('code' => $code, 'message' => $message, 'data' => $data);
			show_response_custom($result);
			exit();
		}

		/* insert send email */
		/* send email and create password */
		$fb_password = generate_random_text(8);
		$gencode = generate_random_text(20, true);
		$params->password = md5($fb_password);
		$params->RECIPIENT_NAME = $params->name;
		$params->to_email = $params->email;
		$params->email_cd = 'MEMBER_REG_FACEBOOK_SUCCESS';
		$params->WEB_TITLE = 'Toko1001';
		$params->email_code = $gencode;
		$params->status_member = 'A';
		$save_add = $this->Member_model->save_add((array) $params);

		$query_check_user_email = $this->Member_model->check_user_email($params);
		$query_check_user_email_data = $query_check_user_email->query_data;

		if (get_row_query($query_check_user_email) > 0)
		{
			$email_exist = TRUE;
		}

		$status = '';
		$password = '';
		$new_seq = '0';
		if ($query_check_user_email_data)
		{
			foreach ($query_check_user_email_data->result() as $result)
			{
				$new_seq = $result->seq;
				$status = $result->status;
				$password = $result->new_password;
			}
		}
		$params->seq = $new_seq;
		$save_log = $this->Member_model->save_log($params);

		$code = 500;
		$message = $this->status_code->getMessageForCode($code);
		;
		if ($save_add === TRUE && $save_log === TRUE)
		{

			$email = $this->Member_model->get_email_template($params);
			$subject = $email['subject'];
			$content = $email['content'];
			$send_email = send_mail_log($params->name, $params->email, $subject, $content, $params->email_cd, $params->email_code);
			if ($send_email['sent_status'] === TRUE) //if send mail is successfully
			{
				$code = 200;
				$message = $this->status_code->getMessageForCode($code);
				$data['member_info'] = $this->_member_info($status_allowed, $params->email, $password);
				$data['member_address'] = $this->_member_address();
				$data['member_wishslist'] = $this->_member_wishlist();
			}
			$params->node_cd = NODE_REGISTRATION_MEMBER;
			generate_voucher($params);
			$result = array('code' => $code, 'message' => $message, 'data' => $data);
			show_response_custom($result);
			exit();
		}
		$response = array('code' => $code, 'message' => $message, 'data' => $data);
		show_response_custom($response);
	}

	private function _member_info($status_allowed = '', $email = '', $password = '')
	{
		$this->history->set_api_name('_Info Member_');
		$params = new stdClass();
		$save_log = new stdClass();
		$params->status_allowed = $status_allowed;
		$params->email = $email;
		$params->password = $password;
//		$email = "harboens@yahoo.com";
//		$password = md5(md5("12345678"));

		$user_login = new stdClass();
		$check_user_login = $this->Member_model->check_user_login($params);
		if (get_row_query($check_user_login) > 0) //user login success
		{
			$code = 200;
			$message = "Success login";

			$data = $check_user_login->query_data;
			foreach ($data->result() as $each_data)
			{
				$member_seq = $each_data->member_seq;
				$this->member_seq = $member_seq;
				$user_login->seq = $member_seq;
				$user_login->email = $each_data->email;
				$user_login->user_name = $each_data->user_name;
				$user_login->phone = $each_data->mobile_phone;
				$user_login->bank_name = $each_data->bank_name;
				$user_login->bank_branch_name = $each_data->bank_branch_name;
				$user_login->bank_acct_no = $each_data->bank_acct_no;
				$user_login->bank_acct_name = $each_data->bank_acct_name;
				$user_login->status = $each_data->status;
				$gender = display_gender_format($each_data->gender);
				$user_login->gender = $gender;
				$user_login->deposit_amt = $each_data->deposit_amt;
				$user_login->birthday = datetime_format($each_data->birthday, 'Ymd', 'd-M-Y');
				$user_login->old_password = $each_data->old_password;
				$user_login->new_password = $each_data->new_password;
				$user_login->profile_img = $this->base_source . ORDER_UPLOAD_IMAGE . $each_data->profile_img;
				$user_login->last_login = datetime_format($each_data->last_login);

				$user_login->token = generate_member_token_log($member_seq, $email);
			}
			$save_log->member_seq = $this->member_seq;
			$save_log->status = 'S';
			$login_log = $this->Member_model->save_log((array) $save_log);
			$status = "OK";
		}
		else //user login fail
		{
			$query = $this->Member_model->get_member_seq_by_email($params);
			$query_data = $query->query_data;
			$row = get_row_query($query);
			$member_seq = 0;
			if ($query_data)
			{
				foreach ($query_data->result() as $result)
				{
					$member_seq = $result->seq;
				}
			}
			$save_log->member_seq = $member_seq;
			$save_log->status = 'F';
			$login_log = $this->Member_model->save_log((array) $save_log);

			$code = 401;
			$message = "Failed login";
			$status = "FAILED";
		}

		if (!$login_log)
		{
			show_response();
			exit();
		}
		$this->code = $code;
		$this->message = $message;
		$data = array('status' => $status, "message" => $message, "data" => $user_login);
		return $data;
	}

	private function _member_address()
	{
		$params = new stdClass();
		$params->member_seq = $this->member_seq;
		$member_address = $this->Member_model->get_member_address($params);

		$row = $member_address->query_row;
		$query_data = $member_address->query_data;

		$data = array();
		if ($query_data)
		{
			foreach ($query_data->result() as $each_query_data)
			{
				$each_data['member_seq'] = $each_query_data->member_seq;
				$each_data['address_seq'] = $each_query_data->address_seq;
				$each_data['alias'] = $each_query_data->alias;
				$each_data['address'] = $each_query_data->address;
				$each_data['district_seq'] = $each_query_data->district_seq;
				$each_data['district'] = $each_query_data->district;
				$each_data['city_seq'] = $each_query_data->city_seq;
				$each_data['city'] = $each_query_data->city;
				$each_data['province_seq'] = $each_query_data->province_seq;
				$each_data['province'] = $each_query_data->province;
				$each_data['zip_code'] = $each_query_data->zip_code;
				$each_data['pic_name'] = $each_query_data->pic_name;
				$each_data['phone_no'] = $each_query_data->phone_no;
				$each_data['default'] = $each_query_data->default;
				$each_data['active'] = $each_query_data->active;
				$each_data['created_by'] = $each_query_data->created_by;
				$each_data['created_date'] = $each_query_data->created_date;
				$each_data['modified_by'] = $each_query_data->modified_by;
				$each_data['modified_date'] = $each_query_data->modified_date;
				$data[] = $each_data;
			}
		}
		return $data;
	}

	private function _member_wishlist()
	{
		//get product variant seq from member_seq
		$params = new stdClass();
		$params->member_seq = $this->member_seq;
		$product_variant = $this->Member_model->get_member_wishlist($params->member_seq);
		$query_row = $product_variant->query_row;
		$query_data = $product_variant->query_data;

		$data_product_variant = array();
		if ($query_row)
		{
			foreach ($query_row->result() as $each_row)
			{
				$row = $each_row->total_row;
			}
		}
		if ($query_data)
		{
			foreach ($query_data->result() as $each_product_variant_seq)
			{
				$data_product_variant[] = $each_product_variant_seq->product_variant_seq;
			}
		}
		//get product info from product variant seq

		$row = 0;
		$params_product_variant_seq = new stdClass();
		$params_product_variant_seq->seq = implode(',', $data_product_variant);

		$query_product = $this->Product_model->get_product_by_product_seq($params_product_variant_seq);

		$query_product_row = $query_product->query_row;
		$query_product_data = $query_product->query_data;
		$data = display_product($query_product_data, $query_product_row);

		return $data;
	}

	public function login_page()
	{
		$this->load->view('login_member');
	}

	public function register_form()
	{
		$this->load->view('member_form');
	}

	private function _register($gender = '', $password = '', $birthday = '', $email = '', $name = '')
	{
		$this->history->set_api_name('_Member Register_');
		$gencode = generate_random_text(20, true);
		$params = get('object');
		$params->gender = $gender;
		$params->password = $password;
		$params->birthday = $birthday;
		$params->email = $email;
		$params->name = $name;

		$params->gender = save_gender_format($params->gender);
		$params->password = md5($params->password);
		$params->RECIPIENT_NAME = $params->name;
		$params->to_email = $params->email;
		$params->email_cd = 'MEMBER_REG';
		$params->WEB_TITLE = 'Toko1001';
		$params->email_code = $gencode;
		$params->LINK = website_url() . VERIFICATION_MEMBER . $gencode;

		if ($params->email === NULL)
		{
			$code = 406;
			$status = "FAIL";
			$message = "Email not exists";
			$data = array('code' => $code,
				'message' => $message,
				'data' => array("status" => $status, "message" => $message, "data" => $params)
			);
			show_response_custom($data);
			exit();
		}

		if (!valid_email($params->email))
		{
			$code = 406;
			$status = "FAIL";
			$message = "Email not valid";
			$data = array('code' => $code,
				'message' => $message,
				'data' => array("status" => $status, "message" => $message, "data" => $params)
			);
			show_response_custom($data);
			exit();
		}

		$check_user_email = $this->Member_model->check_user_email($params);
		if (get_row_query($check_user_email) > 0)
		{
			$code = 406;
			$status = "FAIL";
			$message = "Email is already exists";
			$data = array('code' => $code,
				'message' => $message,
				'data' => array("status" => $status, "message" => $message, "data" => $params)
			);
			show_response_custom($data);
			exit();
		}
		else
		{
			$save_add = $this->Member_model->save_add((array) $params);
			if ($save_add === TRUE)
			{
				$email = $this->Member_model->get_email_template($params);
				$subject = $email['subject'];
				$content = $email['content'];
				$send_email = send_mail_log($params->name, $params->email, $subject, $content, 'MEMBER_REG', $params->email_code);
				if ($send_email['sent_status'] === TRUE) //if send mail is successfully
				{
					$row = 0;
					$code = 200;
					$status = "OK";
					$message = "Register success, please check your email";
				}
				else
				{
					$row = 0;
					$code = 401;
					$status = "FAIL";
					$message = "Unable to send message to email";
				}
			}
		}
		$this->code = $code;
		$this->message = $message;
		$params->gender = display_gender_format($params->gender);
		$data = array("status" => $status, "message" => $message, "data" => $params);
		return $data;
	}

	private function _forgot_password($email = '')
	{
		$this->history->set_api_name('_Forgot password_');
		$parameter = new stdClass;
		$params = new stdClass();
		$params->email = $email;

		if ($params->email === NULL)
		{
			$row = 0;
			$code = 406;
			$status = "FAIL";
			$message = "Email not exists";
			$this->code = $code;
			$this->message = $message;
			$data = array("status" => $status, "message" => $message, "data" => $params);
			return $this->response->result($code, $message, $row, $data);
		}

		$check_user_email = $this->Member_model->check_user_email($params);
		if (get_row_query($check_user_email) > 0)
		{
			$query_user_email = $check_user_email->query_data;
			foreach ($query_user_email->result() as $each_user_email)
			{
				$recipient_name = $each_user_email->name;
				$member_seq = $each_user_email->seq;
				$password_now = $each_user_email->new_password;
			}

			$gencode = generate_random_text(3, true) . date('Y') . generate_random_text(4, true) . date('m') . generate_random_text(5, true) . date('d');
			$parameter->email_cd = "MEMBER_FORGOT_PASSWORD";
			$parameter->email_code = $gencode;
			$parameter->RECIPIENT_NAME = $recipient_name;
			$parameter->LINK = website_url() . VERIFICATION_MEMBER_FORGOT_PASSWORD . $gencode;
			$parameter->to_email = $params->email;
			$email = get_email_template($parameter);
			$subject = $email['subject'];
			$content = $email['content'];
			$send_email = send_mail_log($recipient_name, $params->email, $subject, $content, $parameter->email_cd, $parameter->email_code);
			$parameter->member_seq = $member_seq;
			$parameter->type = FORGOT_PASSWORD_TYPE;
			$parameter->password_now = $password_now;
			$parameter->ip_address = $this->input->ip_address();
			$add_member_log_security = $this->Member_model->add_member_log_security($parameter);

			if ($send_email['sent_status'] === TRUE && $add_member_log_security === TRUE) //if send mail is successfully
			{
				$row = 0;
				$code = 200;
				$status = "OK";
				$message = "Email successful sent";
			}
			else
			{
				$row = 0;
				$code = 401;
				$status = "FAIL";
				$message = "Unable to send message to email";
			}
			$data = array("status" => $status, "message" => $message, "data" => $params);
		}
		else
		{
			$row = 0;
			$code = 406;
			$status = "FAIL";
			$message = "Email not exists";
			$data = array("status" => $status, "message" => $message, "data" => $params);
		}
		$this->code = $code;
		$this->message = $message;
		return $data;
	}

	public function forgot_password()
	{
		$params = get('object');
		$email = check_current_exit($params, array('email'));
		$data = array(
			'forgot_password' => $this->_forgot_password($email),
		);
		$code = $this->code;
		$message = $this->message;
		$response = array('code' => $code, 'message' => $message, 'data' => $data);
		show_response_custom($response);
	}

}
