<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Products_search
 *
 * @author evercoss
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Products_search extends CI_Controller {

	var $base_source = '';

	function __construct()
	{
		parent::__construct();
		$this->load->model('V1/Product_model');
		$this->base_source = $this->config->item('base_source');
		$this->load->helper('form');
	}

	public function index()
	{
		$this->load->view('search_product');
	}

	private function _result()
	{
		$keyword = $this->input->get('e', TRUE);                
		$search = $this->Product_model->get_product_search_result($keyword);
		$result_row = $search->query_row;
		$result_data = $search->query_data;
		$row = get_row_query($search);
		if ($result_data)
		{
			if (!empty($result_data->result()))
			{
				$result = display_product($result_data, $result_row);
			}
			else
			{
				$result = null;
			}
		}

		$data = array('keyword' => $keyword, 'results' => $result);
		$code = 200;
		$message = $this->status_code->getMessageForCode($code);
		return $this->response->result($code, $message, $row, $data);
	}

	public function main()
	{                
		$data = array($this->_result());
		$code = 200;
		$message = $this->status_code->getMessageForCode($code);
		$this->response->show_multi($code, $message, $data);
	}

}

/* End of file Product_search.php */
/* Location: ./application/controller/V1/Product_search.php */