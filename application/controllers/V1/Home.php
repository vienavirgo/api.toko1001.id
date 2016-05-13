<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends APITOKO1001_Controller {

	var $base_source = '';
	var $data_new_product_icon = '';
	var $data_promo_product_icon = '';
	var $paging_product_new = '';
	var $paging_product_promo = '';

	function __construct()
	{
		parent::__construct();
		$this->load->model('V1/Product_model');
		$this->base_source = $this->config->item('base_source');
	}

	private function _product_new()
	{
		$offset = get('single', START_OFFSET, TRUE);
		$limit = get_offset($offset);
		$limit_1 = $limit[0];
		$limit_2 = $limit[1];
		$query_product = $this->Product_model->get_product_new($limit_1, $limit_2);
		$query_product_row = $query_product->query_row;
		$query_product_data = $query_product->query_data;
		$base_url = base_url() . 'v1/product_new';
		$paging = get_paging_link($base_url, $query_product_row);
		$this->data_new_product_icon = $this->base_source . ASSET_IMG_HOME . 'product_newest.png';
		$this->paging_product_new = $paging;
		$data_new = display_product($query_product_data, $query_product_row);
		return $data_new;
	}

	private function _product_promo()
	{
		$offset = get('single', START_OFFSET, TRUE);
		$limit = get_offset($offset);
		$limit_1 = $limit[0];
		$limit_2 = $limit[1];
		$query_product_promo = $this->Product_model->get_promo($limit_1, $limit_2);
		$query_product_promo_row = $query_product_promo->query_row;
		$query_product_promo_data = $query_product_promo->query_data;
		$base_url = base_url() . 'v1/product_promo';
		$paging = get_paging_link($base_url, $query_product_promo_row);
		$this->data_promo_product_icon = '';
		$this->paging_product_promo = $paging;

		$data_promo = display_product($query_product_promo_data, $query_product_promo_row);
		return $data_promo;
	}

	private function _product()
	{
		$this->history->set_api_name('_Product new and promo_');
		$data_new = $this->_product_new();
		$data_promo = $this->_product_promo();
		$data_new_product = array('title' => 'new', 'items' => $data_new, 'icon' => $this->data_new_product_icon) + $this->paging_product_new;
		$data_promo_product = array('title' => 'promo', 'items' => $data_promo, 'icon' => $this->data_promo_product_icon) + $this->paging_product_promo;
		$data = array($data_new_product, $data_promo_product);
		return $data;
	}

	public function _tree_view_category()
	{
		$this->history->set_api_name('_Tree View Category_');
		$limit_1 = $this->request->get_limit_1();
		$limit_2 = $this->request->get_limit_2();
		$params = "";
		$query = $this->Product_model->get_tree_view_category($limit_1, $limit_2);
		$query_data = $query->query_data;
		$row = get_row_query($query);
		$data = array();
		if ($query_data)
		{
			foreach ($query_data->result() as $result)
			{
				$params = new stdClass;
				$params->category_seq = $result->seq;
				$each_data['seq'] = $result->seq;
				$each_data['name'] = $result->name;
				$each_data['image'] = '';
				$each_data['more'] = base_url() . 'v1/product/category/' . url_title(strtolower($result->name)) . "-" . CATEGORY . ($result->seq);
				$data[] = $each_data;
			}
		}
		return $data;
	}

	private function _banner()
	{
		$limit_1 = $this->request->get_limit_1();
		$limit_2 = $this->request->get_limit_2();
		$query = $this->Product_model->get_banner_main($limit_1, $limit_2);
		$query_data = $query->query_data;
		$row = get_row_query($query);
		$data_banner = array();
		if ($query_data)
		{
			foreach ($query_data->result() as $result)
			{
				$each_data['seq'] = $result->category_seq;
				$each_data['image'] = $this->base_source . ADV_UPLOAD_IMAGE . $result->banner_image;
				$each_data['image_url'] = $result->banner_image_url;
				array_push($data_banner, $each_data);
			}
			foreach ($query_data->result() as $result)
			{
				$each_data['seq'] = $result->category_seq;
				$each_data['image'] = $this->base_source . ADV_UPLOAD_IMAGE . $result->advertise1_image;
				$each_data['image_url'] = $result->advertise1_image_url;
				array_push($data_banner, $each_data);
			}
			foreach ($query_data->result() as $result)
			{
				$each_data['seq'] = $result->category_seq;
				$each_data['image'] = $this->base_source . ADV_UPLOAD_IMAGE . $result->advertise2_image;
				$each_data['image_url'] = $result->advertise2_image_url;
				array_push($data_banner, $each_data);
			}
			foreach ($query_data->result() as $result)
			{
				$each_data['seq'] = $result->category_seq;
				$each_data['image'] = $this->base_source . ADV_UPLOAD_IMAGE . $result->advertise3_image;
				$each_data['image_url'] = $result->advertise3_image_url;
				array_push($data_banner, $each_data);
			}
		}
		return $data_banner;
	}

	private function _slider()
	{
		$limit_1 = $this->request->get_limit_1();
		$limit_2 = $this->request->get_limit_2();
		$query = $this->Product_model->get_banner_slide_show($limit_1, $limit_2);
		$query_data = $query->query_data;
		$row = get_row_query($query);
		if ($query_data)
		{
			foreach ($query_data->result() as $result)
			{
				$each_data['seq'] = $result->image_slide_seq;
				$each_data['image'] = $this->base_source . SLIDE_UPLOAD_IMAGE . $result->image;
				if (strpos($result->image_url, 'kategori') !== FALSE)
				{
					$each_data['type'] = SLIDER_CATEGORY_TYPE;
					$each_data['api_url'] = base_url() . 'v1/' . $result->image_url;
				}
				else
				{
					switch ($result->image_url)
					{
						case 'registration';
							$each_data['type'] = SLIDER_REGISTER_TYPE;
							$each_data['api_url'] = base_url() . 'v1/member/register';
							break;
						case 'merchant-backend';
							$each_data['type'] = SLIDER_WEB_TYPE;
							$each_data['api_url'] = base_url() . 'v1/merchant-backend';
							break;
						default :
							$keyword = explode('-', $result->image_url);
							$endword = end($keyword);
							if (strpos($endword, CATEGORY_PARAMETER_CODE) !== FALSE)
							{
								$each_data['type'] = SLIDER_CATEGORY_TYPE;
								$each_data['api_url'] = base_url() . 'v1/product/category/' . $result->image_url;
							}
							else
							{
								$each_data['type'] = SLIDER_DETAIL_TYPE;
								$each_data['api_url'] = base_url() . 'v1/product/detail/' . $endword;
							}
							break;
					}
				}
				$data[] = $each_data;
			}
		}
		return $data;
	}

	public function product_new()
	{
		$this->history->set_api_name('_Product new_');
		$data_new = $this->_product_new();
		$attribute = new stdClass();
		$banner_attr = new stdClass();
		$banner_attr->type = "";
		$data = array(
			'items' => $data_new,
			'icon' => $this->data_new_product_icon,
			'attribute' => $attribute,
			'sort' => array(),
			'banner' => $banner_attr,
			"breadcrumb" => array(),
			) + $this->paging_product_new;
		$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
		show_response_custom($response);
	}

	public function product_promo()
	{
		$this->history->set_api_name('_Product promo_');
		$data_promo = $this->_product_promo();
		$attribute = new stdClass();
		$banner_attr = new stdClass();
		$banner_attr->type = "";
		$data = array(
			'items' => $data_promo,
			'icon' => $this->data_promo_product_icon,
			'attribute' => $attribute,
			'sort' => array(),
			'banner' => $banner_attr,
			"breadcrumb" => array(),
		)+ $this->paging_product_promo;
		$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
		show_response_custom($response);
	}

	public function main()
	{
		$this->history->set_api_name('_main_');
		$data = array(
			'banner' => $this->_banner(),
			'slider' => $this->_slider(),
			'category_0' => $this->_tree_view_category(),
			'product' => $this->_product(),
		);
		$response = array('code' => 200, 'message' => $this->status_code->getMessageForCode(200), 'data' => $data);
		show_response_custom($response);
	}

}

/* End of file Main.php */
/* Location: ./application/controller/V1/Main.php */