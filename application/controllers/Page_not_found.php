<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page_not_found extends APITOKO1001_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$response_status_code = 404;
		$response_message = $this->status_code->getMessageForCode($response_status_code);
		$error = 'page not foud';
		$this->response->set_error($error);
		$data = array();
		$this->response->show_multi($response_status_code, $response_message, $data);		
	}

}

/* End of file Page_not_found.php */
/* Location: ./application/controller/Page_not_found.php */