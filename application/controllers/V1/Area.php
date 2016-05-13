<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Area extends APITOKO1001_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('V1/Area_model');
	}

	public function province()
	{
		$this->history->set_api_name('_area province_');
		$seq = '';
		if ($this->uri->segment(4) !== NULL && $this->uri->segment(4) > 0)
		{
			$seq = $this->uri->segment(4);
		}
		$query_province = $this->Area_model->get_province($seq);
		$query_province_data = $query_province->query_data;

		$data_province = array();
		if ($query_province_data)
		{
			foreach ($query_province_data->result() as $each_provice_data)
			{
				$data_attr['seq'] = $each_provice_data->seq;
				$data_attr['name'] = $each_provice_data->name;
				$data_province[] = $data_attr;
			}
		}
		$code = 200;
		$url = base_url() . 'v1/area/city/0';
		$message = $this->status_code->getMessageForCode($code);
		$data = array('code' => $code, 'message' => $message, 'data' => $data_province, 'url' => $url);
		$this->response->show_custom($data);
	}

	public function city()
	{
		$this->history->set_api_name('_area city_');
		$seq = '';
		$province_seq = '';

		$code = 200;
		$url = base_url() . 'v1/area/district/0';
		$message = $this->status_code->getMessageForCode($code);

		if ($this->uri->segment(5) == '0')
		{
			$data_city = array();
			$data = array('code' => $code, 'message' => $message, 'data' => $data_city, 'url' => $url);
			$this->response->show_custom($data);
			return;
		}


		if ($this->uri->segment(4) !== NULL && $this->uri->segment(4) > 0)
		{
			$seq = $this->uri->segment(4);
		}
		if ($this->uri->segment(5) !== NULL && $this->uri->segment(5) > 0)
		{
			$province_seq = $this->uri->segment(5);
		}
		$query_city = $this->Area_model->get_city($seq, $province_seq);
		$query_city_data = $query_city->query_data;

		$data_city = array();
		if ($query_city_data)
		{
			foreach ($query_city_data->result() as $each_city_data)
			{
				$data_attr['seq'] = $each_city_data->seq;
				$data_attr['name'] = $each_city_data->name;
				$data_attr['province_seq'] = $each_city_data->province_seq;
				$data_city[] = $data_attr;
			}
		}
		$data = array('code' => $code, 'message' => $message, 'data' => $data_city, 'url' => $url);
		$this->response->show_custom($data);
	}

	public function district()
	{
		$this->history->set_api_name('_area district_');
		$seq = '';
		$city_seq = '';

		$code = 200;
		$url = null;
		$message = $this->status_code->getMessageForCode($code);

		if ($this->uri->segment(5) == '0')
		{
			$data_city = array();
			$data = array('code' => $code, 'message' => $message, 'data' => $data_city, 'url' => $url);
			$this->response->show_custom($data);
			return;
		}

		if ($this->uri->segment(4) !== NULL && $this->uri->segment(4) > 0)
		{
			$seq = $this->uri->segment(4);
		}
		if ($this->uri->segment(5) !== NULL && $this->uri->segment(5) > 0)
		{
			$city_seq = $this->uri->segment(5);
		}
		$query_district = $this->Area_model->get_district($seq, $city_seq);
		$query_district_data = $query_district->query_data;


		$data_district = array();
		if ($query_district_data)
		{
			foreach ($query_district_data->result() as $each_district_data)
			{
				$data_attr['seq'] = $each_district_data->seq;
				$data_attr['name'] = $each_district_data->name;
				$data_attr['city_seq'] = $each_district_data->city_seq;
				$data_district[] = $data_attr;
			}
		}

		$data = array('code' => $code, 'message' => $message, 'data' => $data_district, 'url' => $url);
		$this->response->show_custom($data);
	}

}

/* End of file Area.php */
/* Location: ./application/controller/V1/Area.php */