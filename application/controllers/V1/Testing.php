<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Testing extends APITOKO1001_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('V1/Member_model');
		$this->load->model('V1/Product_model');
		$this->load->helper('form');
		$this->load->library('Email');
	}
	public function page()
	{
		//test member
	
		//testing update member
//		$datas = $this->Member_model->find_member('1');
//		
//		foreach ($datas as $value)
//		{
//			$data['provinces'] = $this->Member_model->find_province_list();
//			$data['member_seq'] = $value->seq;
//			$data['name'] = $value->name;
//			$data['birthday'] = $value->birthday;
//			$data['mobile_phone'] = $value->mobile_phone;
//			$data['gender'] = $value->gender;
//		}
//		$this->load->view('member_form',$data);
	//testing member address	
	//	$this->load->view('member_address_by_token');
		//$this->load->view('member_address',$data);
		$this->load->view('login_member');
		//$this->load->view('member_form');
	}
}

/* End of file Testing.php */
/* Location: ./application/controller/V1/Testing.php */
