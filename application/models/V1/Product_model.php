<?php

class Product_model extends CI_Model {

	protected $sql_product;

	function __construct()
	{
		parent::__construct();
		$sql_product = "SELECT SQL_CALC_FOUND_ROWS
					pv.seq AS product_variant_seq,
					pv.product_seq AS product_seq,
					p.name AS `product_name`,
					p.`merchant_seq` AS merchant_seq,
					pv.disc_percent AS product_disc_percent,
					vv.seq AS variant_seq,
					vv.value AS `variant_value`,
					pv.`product_price` AS `product_price`,
					pv.`sell_price` AS `product_sell_price`,
					pv.`seq` AS `product_variant_seq`,
					ps.`stock` AS `product_stock`,
					ps.`merchant_sku` AS `product_sku`,
					p.description AS description, 
                    vr.`display_name` AS variant_name, "
			. $this->get_field_m_product_variant_pic_n_img() . "
				FROM 
					m_product_variant pv
				LEFT JOIN m_product p 
					ON pv.product_seq=p.seq
				INNER JOIN m_variant_value vv 
					ON pv.variant_value_seq=vv.seq 
                INNER JOIN m_variant vr
                    ON vv.variant_seq = vr.seq
				LEFT JOIN m_product_stock ps
					ON ps.`product_variant_seq` = pv.`seq`";
		$this->sql_product = $sql_product;
	}

	private function get_field_m_product_variant_pic_n_img()
	{
		$str = '';
		for ($i = 1; $i <= M_PRODUCT_VARIANT_MAX_PIC_IMG; $i++)
		{
			$str .= "pv.pic_{$i}_img AS product_image_{$i},";
		}
		return rtrim($str, ",");
	}

	public function get_product($limit1, $limit2)
	{
		$sql = $this->sql_product . " WHERE pv.active = '1' AND pv.status IN ('L','C')" . " LIMIT " . addslashes($limit1) . ", " . addslashes($limit2);
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_product2()
	{
		$sql = $this->sql_product;
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_product_seq($product_variant_seq = '')
	{
		$sql = "SELECT product_seq FROM m_product_variant WHERE seq = '{$product_variant_seq}'";
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;
	}

	public function get_product_new($limit1, $limit2)
	{
		$sql = $this->sql_product . " WHERE pv.active = '1' AND pv.status IN ('L','C')" . " ORDER BY pv.seq DESC" . " LIMIT " . addslashes($limit1) . ", " . addslashes($limit2);
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_promo($limit1, $limit2)
	{
		$sql = $this->sql_product . " WHERE pv.active = '1' AND pv.disc_percent>0 AND pv.status IN ('L','C')" . " ORDER BY pv.seq DESC" . " LIMIT " . addslashes($limit1) . ", " . addslashes($limit2);
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_banner_main($limit1, $limit2)
	{
		$sql = "SELECT 
					category_seq AS category_seq,
					banner_img AS banner_image, 
					banner_img_url AS banner_image_url, 
					adv_1_img AS advertise1_image, 
					adv_1_img_url AS advertise1_image_url, 
					adv_2_img AS advertise2_image, 
					adv_2_img_url AS advertise2_image_url, 
					adv_3_img AS advertise3_image, 
					adv_3_img_url AS advertise3_image_url		
				FROM 
					`m_product_category_img` 
				WHERE 
					category_seq=0";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_banner_slide_show($limit1, $limit2)
	{
		$sql = "SELECT 
					seq AS image_slide_seq, 
					img AS image, 
					img_url AS image_url 
				FROM 
					m_img_slide_show 
				WHERE 
					`status`='A' AND `active`='1' ORDER BY `order`";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_banner_image($limit1, $limit2)
	{
		$sql = "SELECT
					category_seq,
					banner_img,
					banner_img_url,
					adv_1_img,
					adv_1_img_url,
					adv_2_img,
					adv_2_img_url,
					adv_3_img,
					adv_3_img_url,
					adv_4_img,
					adv_4_img_url,
					adv_5_img,
					adv_5_img_url,
					adv_6_img,
					adv_6_img_url,
					adv_7_img,
					adv_7_img_url,
					created_by,
					created_date,
					modified_by,
					modified_date
				FROM m_product_category_img";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_tree_view_category($limit1 = '', $limit2 = '')
	{
		$sql = "SELECT `seq`, `name`                                        
				FROM m_product_category
				WHERE seq> 0 AND level=1 
				ORDER BY `order` ASC;";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_tree_view_category_all($limit1 = '', $limit2 = '')
	{
		$sql = "SELECT `seq`, `name`, IFNULL(parent_seq, 0) AS parent_seq, `level`                                         
				FROM m_product_category
				WHERE seq> 0
				ORDER BY `order` ASC;";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_products_by_category_lvl1($params = '')
	{
		$sql_get_product = "SELECT 
                    mp.`name` as product_name , mvv.seq as variant_value_seq,mvv.value as variant_name ,mp.merchant_seq,
                    mpv.seq as product_variant_seq , mp.seq as product_seq, 
                    mpv.disc_percent ,mpv.product_price, mpv.sell_price, mpv.pic_1_img as img_product , mps.stock 
                    FROM m_product_variant mpv 
                    INNER JOIN m_product mp ON mpv.product_seq = mp.seq 
                    INNER JOIN m_product_category mpc ON mp.category_l2_seq = mpc.seq                    
                    INNER JOIN m_product_stock mps ON mps.product_variant_seq = mpv.seq                                        
                    INNER JOIN m_variant_value mvv on mpv.variant_value_seq = mvv.seq
                    INNER JOIN m_variant mv on mvv.variant_seq = mv.seq
                    WHERE mpv.status in ('L','C') AND mpv.active = '1' AND mpc.parent_seq = '{$params->category_seq}'";

		$sql_get_category_lvl_1 = "SELECT name,seq from m_product_category WHERE seq = '{$params->category_seq}'";

		$sql_get_category_lvl_2 = "SELECT name,seq from m_product_category WHERE parent_seq = '{$params->category_seq}'";
		$query_get_product = $this->db->query($sql_get_product);
		$row_get_product = $query_get_product->result();

		$query_get_category_lvl_1 = $this->db->query($sql_get_category_lvl_1);
		$row_get_category_lvl_1 = $query_get_category_lvl_1->result();

		$query_get_category_lvl_2 = $this->db->query($sql_get_category_lvl_2);
		$row_get_category_lvl_2 = $query_get_category_lvl_2->result();

		$data = array("category_lvl1" => $row_get_category_lvl_1, "category_lvl2" => $row_get_category_lvl_2, "products" => $row_get_product);

		return $data;
	}

	public function get_products_by_category_lvl2($params = '')
	{
		$sql_get_product = "SELECT 
                    mp.`name` as product_name,mvv.seq as variant_value_seq ,mvv.value as variant_name ,mp.merchant_seq,mpv.product_price,
                    mpv.seq as product_variant_seq , mp.seq as product_seq, 
                    mpv.disc_percent , mpv.sell_price, mpv.pic_1_img as img_product  , mps.stock
                    FROM m_product_variant mpv 
                    INNER JOIN m_product mp ON mpv.product_seq = mp.seq 
                    INNER JOIN m_product_stock mps ON mps.product_variant_seq = mpv.seq                    
                    INNER JOIN m_product_category mpc ON mp.category_l2_seq = mpc.seq    
                    INNER JOIN m_variant_value mvv on mpv.variant_value_seq = mvv.seq
                    WHERE mpv.status in ('L','C') AND mpv.active = '1' AND mp.category_l2_seq = '{$params->categorylvl2_seq}' ";

		$sql_get_category_lvl_2 = "SELECT name,seq from m_product_category WHERE seq = '{$params->categorylvl2_seq}'";

		$sql_get_category_lvl_3 = "SELECT name,seq from m_product_category WHERE parent_seq = '{$params->categorylvl2_seq}'";

		$query_get_product = $this->db->query($sql_get_product);
		$row_get_product = $query_get_product->result();

		$query_get_category_lvl_2 = $this->db->query($sql_get_category_lvl_2);
		$row_get_category_lvl_2 = $query_get_category_lvl_2->result();

		$query_get_category_lvl_3 = $this->db->query($sql_get_category_lvl_3);
		$row_get_category_lvl_3 = $query_get_category_lvl_3->result();

		$data = array("category_lvl2" => $row_get_category_lvl_2, "category_lvl3" => $row_get_category_lvl_3, "products" => $row_get_product);
		return $data;
	}

	public function get_products_by_category_lvl3($params = '')
	{
		$sql_get_product = "SELECT 
                    mp.`name` as product_name,mvv.seq as variant_value_seq,mvv.value as variant_name ,mp.merchant_seq,mpv.product_price,
                    mpv.seq as product_variant_seq , mp.seq as product_seq, 
                    mpv.disc_percent , mpv.sell_price, mpv.pic_1_img as img_product  , mps.stock
                    FROM m_product_variant mpv 
                    INNER JOIN m_product mp ON mpv.product_seq = mp.seq 
                    INNER JOIN m_product_stock mps ON mps.product_variant_seq = mpv.seq                    
                    INNER JOIN m_product_category mpc ON mp.category_ln_seq = mpc.seq    
                    INNER JOIN m_variant_value mvv on mpv.variant_value_seq = mvv.seq
                    WHERE mpv.status in ('L','C') AND mpv.active = '1' AND mp.category_ln_seq = '{$params->categorylvl3_seq}' ";

		$sql_get_category_lvl_3 = "SELECT name,seq from m_product_category WHERE seq = '{$params->categorylvl3_seq}'";

		$query_get_product = $this->db->query($sql_get_product);
		$row_get_product = $query_get_product->result();

		$query_get_category_lvl_3 = $this->db->query($sql_get_category_lvl_3);
		$row_get_category_lvl_3 = $query_get_category_lvl_3->result();

		$data = array("category_lvl3" => $row_get_category_lvl_3, "products" => $row_get_product);
		return $data;
	}

	public function get_product_by_product_seq($params = '')
	{
		$seq = $params->seq;
		$sql = $this->sql_product . " WHERE pv.active = '1' AND pv.status IN ('L','C') AND pv.seq IN ({$seq})";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_product_detail($product_variant_seq)
	{
		$seq = $product_variant_seq;
		$sql = $this->sql_product . " WHERE pv.status in ('L','C') AND pv.active = '1' AND pv.seq = '{$seq}'  ";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_product_attribute($product_seq = '')
	{
		$seq = $product_seq;
		$sql = "SELECT 
                        mpa.product_seq , ma.name as attribute_name , mav.value as attribute_value
                        FROM m_product_attribute mpa 
                        INNER JOIN m_attribute_value mav ON mpa.attribute_value_seq = mav.seq
                        INNER JOIN m_attribute ma ON mav.attribute_seq = ma.seq 
                        WHERE mpa.product_seq = '{$seq}' ";
//                                    var_dump($sql);exit();
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;
	}

	public function get_product_spec($product_seq = '')
	{
		$seq = $product_seq;
		$sql = "SELECT name as spec_name,value as spec_value FROM m_product_spec WHERE product_seq = '{$seq}' ";
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;
	}

	public function get_product_review($product_variant_seq = '')
	{
		$seq = $product_variant_seq;
		$sql = "SELECT m.seq as member_seq , m.name as member_name , m.profile_img as member_profile_img , 
                        mpv.rate,mpv.review_admin 
                        FROM m_product_review mpv 
                        INNER JOIN t_order `to` ON mpv.order_seq = `to`.seq
                        INNER JOIN m_member m ON `to`.member_seq = m.seq
                        WHERE mpv.product_variant_seq = '{$seq}' AND mpv.status =  'A'";
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;
	}

	public function get_merchant($product_seq = '')
	{
		$seq = $product_seq;
		$sql = "SELECT mc.seq AS merchant_seq,
                        mc.`name` AS merchant_name,
                        md.`name` AS merchant_district,
                        mcty.`name` AS merchant_city,
                        mprov.`name` AS merchant_province,
                        mc.`code` AS merchant_code , 
                        mc.logo_img , 
                        mc.banner_img 
                        FROM m_merchant mc
                        INNER JOIN m_product mp ON mp.merchant_seq = mc.seq 
                        INNER JOIN m_district md ON mc.`district_seq` = md.seq
                        INNER JOIN m_city mcty ON md.`city_seq` = mcty.`seq`
                        INNER JOIN m_province mprov ON mcty.`province_seq` = mprov.`seq`
                        WHERE mp.seq = '{$seq}'";
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;
	}

	public function get_order_seq($product_variant_seq = '')
	{
		$seq = $product_variant_seq;
		$sql = "SELECT order_seq FROM t_order_product WHERE product_variant_seq = '{$seq}' AND product_status NOT IN ('X')";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_product_variant_seq_related($order_seq = '', $product_variant_seq = '')
	{
		$sql = "SELECT product_variant_seq FROM t_order_product WHERE order_seq = '{$order_seq}' AND product_variant_seq NOT IN ('{$product_variant_seq}') AND product_status NOT IN ('X')";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_product_related($product_variant_seq)
	{
		$sql = $this->sql_product . " WHERE pv.seq = '{$product_variant_seq}'";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_product_category($list_product_seq = '', $list_seq_self_child_category, $search = '', $price_min = '', $price_max = '', $order = '', $limit1 = '', $limit2 = '')
	{
		$sql = $this->sql_product . " WHERE pv.active = '1' AND pv.status IN ('L','C')";
		if ($list_product_seq != '')
		{
			$sql .= " AND p.seq IN ({$list_product_seq})";
		}
		if ($list_seq_self_child_category != '')
		{
			$sql .= " AND p.category_ln_seq IN ({$list_seq_self_child_category})";
		}
		if ($search != '')
		{
			$sql .= " AND p.name LIKE '%{$search}%'";
		}
		if ($price_min != '')
		{
			$sql .= " AND pv.sell_price>={$price_min}";
		}
		if ($price_max != '')
		{
			$sql .= " AND pv.sell_price<={$price_max}";
		}
		if ($order != '')
		{
			switch ($order)
			{
				case NEW_TO_OLD_PRODUCT:
					$sql .= " ORDER BY pv.seq DESC";
					break;
				case OLD_TO_NEW_PRODUCT:
					$sql .= " ORDER BY pv.seq ASC";
					break;
				case PRICE_EXPENSIVE_TO_CHEAP:
					$sql .= " ORDER BY pv.sell_price DESC";
					break;
				case PRICE_CHEAP_TO_EXPENSIVE:
					$sql .= " ORDER BY pv.sell_price ASC";
					break;
				case BIGGEST_DISCOUNT_TO_SMALL:
					$sql .= " ORDER BY pv.disc_percent DESC";
					break;
				case SMALLEST_DISCOUNT_TO_BIGGEST:
					$sql .= " ORDER BY pv.disc_percent ASC";
					break;
				default:
					$sql .= " ORDER BY pv.seq DESC";
			}
		}
		else
		{
			$sql .= " ORDER BY pv.seq DESC";
		}
		$sql .= " LIMIT " . addslashes($limit1) . ", " . addslashes($limit2);
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_product_search_result($keyword)
	{
		$sql = $this->sql_product . "WHERE p.name LIKE '%{$keyword}%'";                
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_category_level_1_sidebar()
	{
		$sql = "SELECT seq,name FROM m_product_category WHERE level = '1'";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_category_sidebar()
	{
		$sql = "SELECT seq,`name`,parent_seq,`level`,`order` FROM m_product_category WHERE seq > 0";
		$query_result = query_data_row($sql);
		return $query_result;
	}

	public function get_category_child_sidebar($parent_seq = "", $level = "")
	{
		$sql = "SELECT seq,name FROM m_product_category WHERE parent_seq='{$parent_seq}' AND level = '{$level}'";
		$query_result = query_data_row($sql);
		return $query_result;
	}
}

/* End of file Product_model.php */
/* Location: ./application/models/Product_model.php */