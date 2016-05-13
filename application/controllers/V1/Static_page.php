<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Static_page extends APITOKO1001_Controller {

	function __construct()
	{
		parent::__construct();
		$this->base_source = $this->config->item('base_source');
	}

	public function panduan_belanja()
	{
		$css = $this->base_source;
		$this->load->view('static/panduan-belanja.php', $css);
	}

	public function pengembalian_barang()
	{
		$this->load->view('static/pengembalian-barang.php');
	}

	public function pengembalian_dana()
	{
		$this->load->view('static/pengembalian-dana.php');
	}

}
