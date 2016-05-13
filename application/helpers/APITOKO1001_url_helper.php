<?php

if ( ! function_exists('website_url'))
{
	function website_url()
	{
		return get_instance()->config->config['website_url'];
	}
}

if ( ! function_exists('mobile_url'))
{
	function mobile_url()
	{
		return get_instance()->config->config['mobile_url'];
	}
}

if ( ! function_exists('get_base_source'))
{
	function get_base_source()
	{
		return get_instance()->config->config['base_source'];
	}
}

/* End of file APITOKO1001_url_helper.php */
/* Location: ./application/helpers/APITOKO1001_url_helper.php */