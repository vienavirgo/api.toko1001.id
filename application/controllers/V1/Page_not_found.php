<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Page_not_found extends APITOKO1001_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
		show_response(404);
    }

}

/* End of file Page_not_found.php */
/* Location: ./application/controller/V1/Page_not_found.php */