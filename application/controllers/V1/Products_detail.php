<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products_detail extends APITOKO1001_Controller {

	var $base_source = '';

	function __construct()
	{
		parent::__construct();
		$this->load->model('V1/Product_model');
		$this->base_source = $this->config->item('base_source');
	}

	public function product_variant_seq()
	{
		$seq = $this->uri->segment(4);
		return $seq;
	}

	private function _product_detail()
	{
		$this->history->set_api_name('_Product Detail_');
		$seq = $this->product_variant_seq();
		$query_product = $this->Product_model->get_product_detail($seq);
                
		if (!empty($query_product))
		{
			$query_product_row = $query_product->query_row;
			$query_product_data = $query_product->query_data;                                                
			$tmp_product_detail_data = display_product($query_product_data, $query_product_row, TRUE);
                        if(count($tmp_product_detail_data) == 0 ) {
                            $product_detail_data = null;
                        }
                        else {
                            $product_detail_data = $tmp_product_detail_data[0];
                        }
		}
		else
		{
			$product_detail_data = "";
		}
		$row = 0;
		$code = 200;
		$data = $product_detail_data;
		$message = $this->status_code->getMessageForCode($code);
		return $this->response->result($code, $message, $row, $data);
	}

	private function _product_attribute()
	{
		$variant_seq = $this->product_variant_seq();
		$get_product_seq = $this->Product_model->get_product_seq($variant_seq);
		if (!empty($get_product_seq))
		{
			$get_product_attribute = $this->Product_model->get_product_attribute($get_product_seq[0]->product_seq);
			if (!empty($get_product_attribute))
			{
				$product_attribute_data = '';
				$product_attribute_data .= '<table>';
				foreach ($get_product_attribute as $result)
				{
					$product_attribute_data .= "<tr>";
					$product_attribute_data .= "<td>{$result->attribute_name}</td>";
					$product_attribute_data .= "<td>{$result->attribute_value}</td>";
					$product_attribute_data .= "</tr>";
				}
				$product_attribute_data .= "</table>";
				$code = 200;
				$row = 0;
				$message = $this->status_code->getMessageForCode($code);
			}
			else
			{
				$row = 0;
				$code = 200;
				$product_attribute_data = "";
				$message = $this->status_code->getMessageForCode($code);
			}
		}
		else
		{
			$code = 200;
			$row = 0;
			$message = $this->status_code->getMessageForCode($code);
			$product_attribute_data = "";
		}
		return $this->response->result($code, $message, $row, $product_attribute_data);
	}

	private function _product_spec()
	{
		$variant_seq = $this->product_variant_seq();
		$get_product_seq = $this->Product_model->get_product_seq($variant_seq);

		if (!empty($get_product_seq))
		{
			$get_product_spec = $this->Product_model->get_product_spec($get_product_seq[0]->product_seq);
			if (!empty($get_product_spec))
			{

				$product_spec_data = '';
				$product_spec_data .= '<table>';

				foreach ($get_product_spec as $result)
				{
					$product_spec_data .= "<tr>";
					$product_spec_data .= "<td>{$result->spec_name}</td>";
					$product_spec_data .= "<td>{$result->spec_value}</td>";
					$product_spec_data .= "</tr>";
				}

				$product_spec_data .= "</table>";
				$code = 200;
				$row = 0;
				$message = $this->status_code->getMessageForCode($code);
			}
			else
			{
				$code = 200;
				$row = 0;
				$message = $this->status_code->getMessageForCode($code);
				$product_spec_data = "";
			}
		}
		else
		{
			$code = 200;
			$row = 0;
			$message = $this->status_code->getMessageForCode($code);
			$product_spec_data = "";
		}
		return $this->response->result($code, $message, $row, $product_spec_data);
	}

	private function _product_review()
	{
		$variant_seq = $this->product_variant_seq();
		$get_product_seq = $this->Product_model->get_product_seq($variant_seq);
		if (!empty($get_product_seq))
		{
			$get_product_review = $this->Product_model->get_product_review($variant_seq);
			if (!empty($get_product_review))
			{
				foreach ($get_product_review as $result)
				{
					$data_review['rate'] = $result->rate;
					$data_review['member_name'] = $result->member_name;
					$data_review['member_profile_img'] = $this->base_source . MEMBER_PROFILE_IMAGE . $result->member_profile_img;
					$data_review['review_admin'] = $result->review_admin;
					$product_rate[] = $data_review['rate'];
					$product_review[] = $data_review;
				}
				$product_rate_average = number_format(array_sum($product_rate) / count($product_rate), 1);
				$product_review_data = array("product_rate_average" => $product_rate_average, "reviews" => $product_review);
			}
			else
			{
				$product_review_data = null;
			}
		}
		else
		{
			$product_review_data = null;
		}
		$code = 200;
		$row = 0;
		$message = $this->status_code->getMessageForCode($code);
		return $this->response->result($code, $message, $row, $product_review_data);
	}

	private function _product_merchant()
	{
		$variant_seq = $this->product_variant_seq();
		$get_product_seq = $this->Product_model->get_product_seq($variant_seq);
		if (!empty($get_product_seq))
		{
			$get_merchant = $this->Product_model->get_merchant($get_product_seq[0]->product_seq);
			if (!empty($get_merchant))
			{
				foreach ($get_merchant as $result)
				{
					$data_merchant['merchant_seq'] = $result->merchant_seq;
					$data_merchant['merchant_name'] = $result->merchant_name;
					$data_merchant['merchant_code'] = $result->merchant_code;
					$data_merchant['merchant_district'] = $result->merchant_district;
					$data_merchant['merchant_city'] = $result->merchant_city;
					$data_merchant['merchant_province'] = $result->merchant_province;
					$data_merchant['merchant_logo_img'] = base_url() . MERCHANT_LOGO . $result->merchant_seq . '/' . $result->logo_img;
					$data_merchant['merchant_banner_img'] = base_url() . MERCHANT_LOGO . $result->merchant_seq . '/' . $result->banner_img;
					$data_merchant['url_detail'] = base_url() . 'v1/merchant/' . $result->merchant_code;
					$product_merchant = $data_merchant;
				}
			}
			else
			{
				$product_merchant = "";
			}
		}
		else
		{
			$product_merchant = "";
		}
		$code = 200;
		$row = 0;
		$message = $this->status_code->getMessageForCode($code);
		return $this->response->result($code, $message, $row, $product_merchant);
	}

	private function _product_related()
	{
		$product_variant_seq = $this->product_variant_seq();
		$get_order_seq = $this->Product_model->get_order_seq($product_variant_seq);
		$query_row = $get_order_seq->query_row;
		$query_data = $get_order_seq->query_data;
		$row = get_row_query($get_order_seq);
		if ($query_data)
		{
			if (!empty($query_data->result()))
			{
				foreach ($query_data->result() as $result)
				{
					$order_seq[] = $result->order_seq;
				}
			}
			else
			{
				$order_seq = "";
			}
		}
		else
		{
			$order_seq = "";
		}

		if ($order_seq != "")
		{
			$product_variant_seq_related = array();
			foreach ($order_seq as $seq)
			{
				$get_product_related = $this->Product_model->get_product_variant_seq_related($seq, $product_variant_seq);
				$query_data_rel = $get_product_related->query_data;
				//get product_variant_seq on every related order_seq                    
				if (!empty($query_data_rel->result()))
				{
					foreach ($query_data_rel->result() as $each)
					{
						$product_variant_seq_related[] = $each->product_variant_seq;
					}
					$product_variant_seq_related = $product_variant_seq_related;
				}
				else
				{
					$product_variant_seq_related = "";
				}
			}
			echo json_encode($product_variant_seq_related);exit();
			
			//limit product display less than 6
			if (count($product_variant_seq_related) > 6)
			{
				$product_variant_seq_related = array_slice($product_variant_seq_related, 0, 6);
			}
			else
			{
				$product_variant_seq_related = $product_variant_seq_related;
			}

			//get product related thumbs 
			if ($product_variant_seq_related != "")
			{
				foreach ($product_variant_seq_related as $seq)
				{
					$query = $this->Product_model->get_product_related($seq);
					$query_row = $query->query_row;
					$query_data = $query->query_data;
					$products[] = display_product($query_data, $query_row)[0];
				}
			}
			else
			{
				$products = null;
			}
		}
		else
		{
			$products = null;
		}
		$data = $products;
		$code = 200;
		$message = $this->status_code->getMessageForCode($code);
		return $this->response->result($code, $message, $row, $data);
	}

	public function main()
	{
		$data = array(
			'product_merchant' => $this->_product_merchant(),
			'product_detail' => $this->_product_detail(),
			'product_attribute' => $this->_product_attribute(),
			'product_spec' => $this->_product_spec(),
			'product_review' => $this->_product_review(),
			'product_related' => $this->_product_related(),
		);
		$code = 200;
		$message = $this->status_code->getMessageForCode($code);
		$this->response->show_multi($code, $message, $data);
	}

}

/* End of file Products_detail.php */
/* Location: ./application/controller/V1/Products_detail.php */