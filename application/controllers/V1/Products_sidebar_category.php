<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Products_sidebar_category extends APITOKO1001_Controller {

	var $base_source = '';

	function __construct()
	{
		parent::__construct();
		$this->load->model('V1/Product_model');
		$this->base_source = $this->config->item('base_source');
	}

	private function _sidebar_category()
	{
		$query_sidebar_category = $this->Product_model->get_category_sidebar();
		$query_sidebar_category_data = $query_sidebar_category->query_data;
		$row = get_row_query($query_sidebar_category);
		$data_sidebar = array();
		if ($query_sidebar_category_data)
		{
			foreach ($query_sidebar_category_data->result() as $each_result)
			{
				$result = new stdClass;
				$result->seq = $each_result->seq;
				$result->name = $each_result->name;
				$result->parent_seq = $each_result->parent_seq;
				$result->level = $each_result->level;
				$result->order = $each_result->order;
				$result->url = base_url() . 'v1/product/category/' . url_title(strtolower($result->name)) . "-" . CATEGORY . ($result->seq);
				$icon_default = str_replace(' ', '_', strtolower($result->name));
				$icon_default = preg_replace("/[^a-zA-Z0-9_]+/", "", $icon_default);
				$icon_default = preg_replace('/[_]+/', '_', $icon_default);
				$icon_default = $this->base_source . ASSET_IMG_ICON . $icon_default;
				if ($each_result->level == '1')
				{
					$result->icon = array($icon_default . '_' . 'black.png', $icon_default . '_' . 'white.png');
				}
				$data_sidebar[] = $result;
			}
		}
		$data = buildtree($data_sidebar);
		return $data;
	}

	public function main()
	{
		$data = array('sidebar_category' => $this->_sidebar_category());
		$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
		show_response_custom($response);
	}

}

/* End of file Product_sidebar_category.php */
/* Location: ./application/controller/V1/Product_sidebar_category.php */