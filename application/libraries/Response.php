<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once("./vendor/autoload.php");
use Webmozart\Json\JsonEncoder;

class Response {

	protected $ci;
	protected $response = '';
	protected $total_rows = 0;
	protected $message = '';
	protected $code = '900';
	protected $data = '';
	protected $error = '';
	protected $webmozzart;

	public function __construct()
	{
		$this->ci = & get_instance();
		$this->webmozzart = new JsonEncoder();
	}

	public function set_error($error = '')
	{
		$this->error = $error;
	}

	public function get_error()
	{
		return $this->error;
	}

	public function set_response($response = '')
	{
		$this->response = $response;
	}

	public function get_response()
	{
		return $this->response;
	}

	public function set_total_rows($total_rows = '')
	{
		$this->total_rows = $total_rows;
	}

	public function get_total_rows()
	{
		return $this->total_rows;
	}

	public function set_message($message = '')
	{
		$this->message = $message;
	}

	public function get_message()
	{
		return $this->message;
	}

	public function set_code($code = '')
	{
		$this->code = $code;
	}

	public function get_code()
	{
		return $this->code;
	}

	public function set_data($data = '')
	{
		$this->data = $data;
	}

	public function get_data()
	{
		return $this->data;
	}

	public function result($code = '', $message = '', $total_rows = '0', $data = array())
	{
		if (ENVIRONMENT === 'production' && $code !== 200)
		{
			$total_rows = 0;
			$code = 500;
			$message = $this->ci->status_code->getMessageForCode($code);
			$data = array();
		}

		/* 		if (ENVIRONMENT !== 'production' && $code !== 200)
		  {
		  $message = ($message === '') ? $this->ci->status_code->getMessageForCode($code) : $message;
		  $data = array('error' => $message);
		  } */

		/*        $response = json_encode(
		  array('response' => array(
		  'total_rows' => $total_rows,
		  'message' => $message,
		  'code' => $code,
		  'data' => $data
		  ))
		  ); */
//		$this->webmozzart->setEscapeSlash(FALSE);
//		$response = $this->webmozzart->encode($data);
		$response = json_encode($data);
		$this->set_response($response);
		return $this->get_response();
	}

	public function show($code = '', $message = '', $total_rows = '0', $data = array())
	{
		echo $this->result($code, $message, $total_rows, $data);
		$this->ci->history->save_history();
	}

	public function result_multi($code = '', $message = '', $data = array())
	{
//		$this->ci->output->enable_profiler(TRUE);
		/* using for multi result show */
		$new_data = array();

		foreach ($data as $key => $value)
		{
			$new_data[$key] = json_decode($value);
		}
		$new_response = array('code' => $code,
			'message' => $message,
			'data' => $new_data,
			'execution_time' => $this->ci->benchmark->elapsed_time('total_execution_time_start'),
		);
		$this->webmozzart->setEscapeSlash(FALSE);
		$response = $this->webmozzart->encode($new_response);
		$this->set_response($response);
		return $this->get_response();
	}

	public function show_multi($code = '', $message = '', $data = array())
	{
		echo $this->result_multi($code, $message, $data);
		$this->ci->history->save_history();
	}
	
	public function show_custom($data = array())
	{
		echo $this->result_custom($data);
		$this->ci->history->save_history();
	}
	
	public function result_custom($data = array())
	{
		$data = array_merge($data, array('execution_time' => $this->ci->benchmark->elapsed_time('total_execution_time_start')));
		$this->webmozzart->setEscapeSlash(FALSE);
		$response = $this->webmozzart->encode($data);
		$this->set_response($response);
		return $this->get_response();		
	}

}

/* End of file Response.php */
/* Location: ./application/libraries/Response.php */