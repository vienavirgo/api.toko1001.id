<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Variable {
	/* default value */

	protected $action; //0,int,1
	protected $app_user_id;
	protected $app_token;
	protected $app_version;
	protected $app_device_lang;
	protected $app_device_id;
	protected $app_device_model;
	protected $app_latitude;
	protected $app_longitude;
	protected $app_os;
	protected $app_name;
	protected $app_id;
	protected $address;
	protected $address_seq;
	protected $alias;
	protected $amount;
	protected $birthday;
	protected $coupon_seq;
	protected $district_seq;
	protected $error_android;
	protected $exp_service_seq;
	protected $expedition_seq;
	protected $exp_real_fee;
	protected $exp_code;
	protected $exp_promo_seq;
	protected $email;
	protected $phone;
	protected $gender;
	protected $ip_address;
	protected $last_login;
	protected $member_seq;
	protected $member_name;
	protected $name;
	protected $mobile_phone;
	protected $merchant_message;
	protected $merchant_seq;
	protected $mulai; //rename variable when constant START_OFFSET change
	protected $new_password;
	protected $old_password;
	protected $order; //fieldname to be ordered
	protected $order_code; //1-9 order code ASC or DESC 1=ASC,2=DESC other DESC
	protected $order_seq;
	protected $page;
	protected $phone_no;
	protected $password;
	protected $pic_name;
	protected $product_variant_seq;
	protected $payment_seq;
	protected $payment_code;
	protected $product_weight;
	protected $rate;
	protected $review;
	protected $receiver_name;
	protected $receiver_address;
	protected $receiver_district_seq;
	protected $receiver_zip_code;
	protected $receiver_phone_no;
	protected $rate_exp;
	protected $sell_price;
	protected $quantity;
	protected $token;
	protected $voucher_seq;
	protected $zip_code;

	/* end of default value */

	private function get_variable_type_length($params = '')
	{
		$retval = new stdClass();
		switch ($params)
		{
			case 'action':
				$retval = $this->_set_variable_type_length('real', '1');
				break;
			case 'address_seq':
			case 'coupon_seq':
			case 'district_seq':
			case 'exp_service_seq':
			case 'expedition_seq':
			case 'exp_promo_seq':
			case 'member_seq':
			case 'merchant_seq':
			case 'order_seq':
			case 'product_variant_seq':
			case 'payment_seq':
			case 'receiver_district_seq':
			case 'mulai':
			case 'quantity':
			case 'voucher_seq':
				$retval = $this->_set_variable_type_length('whole', '10');
				break;
			case 'token': case 'app_token':
				$retval = $this->_set_variable_type_length('string', '40');
				break;
			case 'alias':
			case 'email':
			case 'exp_real_fee':
			case 'receiver_phone_no':
			case 'rate_exp':
			case 'sell_price':
			case 'phone_no' : case "mobile_phone":
			case 'pic_name':
			case 'app_user_id':
			case 'app_version':
			case 'app_device_lang':
			case 'app_device_id':
			case 'app_device_model':
			case 'app_latitude':
			case 'app_longitude':
			case 'app_os':
			case 'app_name':
			case 'app_id': case 'ip_address':
			case 'new_password';
			case 'old_password';
				$retval = $this->_set_variable_type_length('string', '50');
				break;
			case 'exp_code':
			case 'gender':
			case 'payment_code':
			case 'receiver_zip_code':
			case 'zip_code':
				$retval = $this->_set_variable_type_length('string', '10');
				break;
			case 'merchant_message':
			case 'address':
			case 'receiver_address':
			case 'review':
			case 'error_android':
				$retval = $this->_set_variable_type_length('string', '10000000');
				break;
			case 'password':
			case 'member_name':
			case 'receiver_name':
			case 'name': case 'page': case 'order':
				$retval = $this->_set_variable_type_length('string', '100');
				break;
			case 'birthday':
				$retval = $this->_set_variable_type_length('date', '10');
				break;
			case 'last_login':
				$retval = $this->_set_variable_type_length('datetime', '19');
				break;
			case 'amount':
				$retval = $this->_set_variable_type_length('integer', '10');
				break;
			case 'order_code':
			case 'rate':
				$retval = $this->_set_variable_type_length('whole', '1');
				break;
			case 'product_weight':
				$retval = $this->_set_variable_type_length('decimal', '15'); //ex 100.00 length =5
				break;
			default:
				$retval = new stdClass();
				$retval->type = NULL;
				$retval->length = NULL;
		}
		return $retval;
	}

	public function validate($input = array(), $default_value = array())
	{
		$class_attribute = array_keys(get_object_vars($this));
		foreach ($input as $key => $value)
		{
			if (in_array($key, $class_attribute) && $default_value[$key]->type == 'date')
			{
				check_date($value, true);
			}
			if (in_array($key, $class_attribute) && $default_value[$key]->type == 'datetime')
			{
				check_datetime($value, true);
			}
			if (in_array($key, $class_attribute) && $default_value[$key]->type == 'decimal')
			{
				check_decimal($value, true);
			}
			if (in_array($key, $class_attribute) && $default_value[$key]->type == 'integer')
			{
				check_integer($value, true);
			}
			if (in_array($key, $class_attribute) && $default_value[$key]->type == 'real')
			{
				check_real($value, true);
			}
			if (in_array($key, $class_attribute) && $default_value[$key]->type == 'time')
			{
				check_time($value, true);
			}
			if (in_array($key, $class_attribute) && $default_value[$key]->type == 'timestamp')
			{
				check_timestamp($value, true);
			}
			if (in_array($key, $class_attribute) && $default_value[$key]->type == 'whole')
			{
				check_whole($value, true);
			}
			if (in_array($key, $class_attribute) && strlen($value) > $default_value[$key]->length)
			{
				show_response(406, 'error on ' . $key . ' variable'); //406
				exit();
			}
			if (!in_array($key, $class_attribute) && $default_value[$key]->type === NULL)
			{
				show_response(500, 'error on ' . $key . ' variable');
				exit();
			}
		}
		return true;
	}

	public function set_default_value($input = array())
	{
		$default_value = array();
		foreach ($input as $name => $value)
		{
			$default_value[$name] = $this->get_variable_type_length($name);
		}
		return $default_value;
	}

	private function _set_variable_type_length($type = '', $length = '')
	{
		$output = new stdClass();
		$output->type = $type;
		$output->length = $length;
		return $output;
	}

	public function check_paging_allow_with_null($get_variable = array(), $post_variable = array())
	{
		return (
			isset($get_variable[START_OFFSET]) OR
			isset($post_variable[START_OFFSET])
			);
	}

}
