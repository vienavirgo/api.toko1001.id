<?php

class History_model extends CI_Model {
	function __construct()
    {
        parent::__construct();
    }

	public function save_history($data) 
	{
		$this->db->insert('tb_apilog', $data); 
		return $data;
	}
}

/* End of file History_model.php */
/* Location: ./application/models/History_model.php */