<?php

class APITOKO1001_Pagination extends CI_Pagination {

	protected $api_first_url = '';
	protected $api_base_url = '';
	protected $api_base_page = '';
	protected $api_uri_page_number = '';

	public function __construct()
	{
		parent::__construct();
	}

	public function execute()
	{
		// If our item count or per-page total is zero there is no need to continue.
		// Note: DO NOT change the operator to === here!
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			return '';
		}

		// Calculate the total number of pages
		$num_pages = (int) ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages === 1)
		{
			return '';
		}

		// Check the user defined number of links.
		$this->num_links = (int) $this->num_links;

		if ($this->num_links < 0)
		{
			show_error('Your number of links must be a non-negative number.');
		}

		// Keep any existing query string items.
		// Note: Has nothing to do with any other query string option.
		if ($this->reuse_query_string === TRUE)
		{
			$get = $this->CI->input->get();

			// Unset the controll, method, old-school routing options
			unset($get['c'], $get['m'], $get[$this->query_string_segment]);
		}
		else
		{
			$get = array();
		}

		// Put together our base and first URLs.
		// Note: DO NOT append to the properties as that would break successive calls
		$base_url = trim($this->base_url);
		$this->api_base_url = $base_url;
		$first_url = $this->first_url;
		$this->api_first_url = $first_url;

		$query_string = '';
		$query_string_sep = (strpos($base_url, '?') === FALSE) ? '?' : '&amp;';

		// Are we using query strings?
		if ($this->page_query_string === TRUE)
		{
			// If a custom first_url hasn't been specified, we'll create one from
			// the base_url, but without the page item.
			if ($first_url === '')
			{
				$this->api_first_url = $base_url;

				// If we saved any GET items earlier, make sure they're appended.
				if (!empty($get))
				{
					$this->api_first_url .= $query_string_sep . http_build_query($get);
				}
			}

			// Add the page segment to the end of the query string, where the
			// page number will be appended.
			$base_url .= $query_string_sep . http_build_query(array_merge($get, array($this->query_string_segment => '')));
			$this->api_base_url = $base_url;
		}
		else
		{
			// Standard segment mode.
			// Generate our saved query string to append later after the page number.
			if (!empty($get))
			{
				$query_string = $query_string_sep . http_build_query($get);
				$this->suffix .= $query_string;
			}

			// Does the base_url have the query string in it?
			// If we're supposed to save it, remove it so we can append it later.
			if ($this->reuse_query_string === TRUE && ($base_query_pos = strpos($base_url, '?')) !== FALSE)
			{
				$base_url = substr($base_url, 0, $base_query_pos);
				$this->api_base_url = $base_url;
			}

			if ($first_url === '')
			{
				$this->api_first_url = $base_url . $query_string;
			}

			$base_url = rtrim($base_url, '/') . '/';
			$this->api_base_url = $base_url;
		}

		// Determine the current page number.
		$base_page = ($this->use_page_numbers) ? 1 : 0;
		$this->api_base_page = $base_page;

		// Are we using query strings?
		if ($this->page_query_string === TRUE)
		{
			$this->cur_page = $this->CI->input->get($this->query_string_segment);
		}
		else
		{
			// Default to the last segment number if one hasn't been defined.
			if ($this->uri_segment === 0)
			{
				$this->uri_segment = count($this->CI->uri->segment_array());
			}

			$this->cur_page = $this->CI->uri->segment($this->uri_segment);

			// Remove any specified prefix/suffix from the segment.
			if ($this->prefix !== '' OR $this->suffix !== '')
			{
				$this->cur_page = str_replace(array($this->prefix, $this->suffix), '', $this->cur_page);
			}
		}

		// If something isn't quite right, back to the default base page.
		if (!ctype_digit($this->cur_page) OR ($this->use_page_numbers && (int) $this->cur_page === 0))
		{
			$this->cur_page = $base_page;
		}
		else
		{
			// Make sure we're using integers for comparisons later.
			$this->cur_page = (int) $this->cur_page;
		}

		// Is the page number beyond the result range?
		// If so, we show the last page.
		if ($this->use_page_numbers)
		{
			if ($this->cur_page > $num_pages)
			{
				$this->cur_page = $num_pages;
			}
		}
		elseif ($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}

		$uri_page_number = $this->cur_page;
		$this->api_uri_page_number = $uri_page_number;

		// If we're using offset instead of page numbers, convert it
		// to a page number, so we can generate the surrounding number links.
		if (!$this->use_page_numbers)
		{
			$this->cur_page = (int) floor(($this->cur_page / $this->per_page) + 1);
		}

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with.
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;
	}

	public function execute_post()
	{
		// If our item count or per-page total is zero there is no need to continue.
		// Note: DO NOT change the operator to === here!
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			return '';
		}

		// Calculate the total number of pages
		$num_pages = (int) ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages === 1)
		{
			return '';
		}

		// Check the user defined number of links.
		$this->num_links = (int) $this->num_links;

		if ($this->num_links < 0)
		{
			show_error('Your number of links must be a non-negative number.');
		}

		// Keep any existing query string items.
		// Note: Has nothing to do with any other query string option.
		if ($this->reuse_query_string === TRUE)
		{
			$post = $this->CI->input->post();

			// Unset the controll, method, old-school routing options
			unset($post['c'], $post['m'], $post[$this->query_string_segment]);
		}
		else
		{
			$post = array();
		}

		// Put together our base and first URLs.
		// Note: DO NOT append to the properties as that would break successive calls
		$base_url = trim($this->base_url);
		$this->api_base_url = $base_url;
		$first_url = $this->first_url;
		$this->api_first_url = $first_url;

		$query_string = '';
		$query_string_sep = (strpos($base_url, '?') === FALSE) ? '?' : '&amp;';

		// Are we using query strings?
		if ($this->page_query_string === TRUE)
		{
			// If a custom first_url hasn't been specified, we'll create one from
			// the base_url, but without the page item.
			if ($first_url === '')
			{
				$this->api_first_url = $base_url;

				// If we saved any GET items earlier, make sure they're appended.
				if (!empty($post))
				{
					$this->api_first_url .= $query_string_sep . http_build_query($post);
				}
			}

			// Add the page segment to the end of the query string, where the
			// page number will be appended.
			$base_url .= $query_string_sep . http_build_query(array_merge($post, array($this->query_string_segment => '')));
			$this->api_base_url = $base_url;
		}
		else
		{
			// Standard segment mode.
			// Generate our saved query string to append later after the page number.
			if (!empty($post))
			{
				$query_string = $query_string_sep . http_build_query($post);
				$this->suffix .= $query_string;
			}

			// Does the base_url have the query string in it?
			// If we're supposed to save it, remove it so we can append it later.
			if ($this->reuse_query_string === TRUE && ($base_query_pos = strpos($base_url, '?')) !== FALSE)
			{
				$base_url = substr($base_url, 0, $base_query_pos);
				$this->api_base_url = $base_url;
			}

			if ($first_url === '')
			{
				$this->api_first_url = $base_url . $query_string;
			}

			$base_url = rtrim($base_url, '/') . '/';
			$this->api_base_url = $base_url;
		}

		// Determine the current page number.
		$base_page = ($this->use_page_numbers) ? 1 : 0;
		$this->api_base_page = $base_page;

		// Are we using query strings?
		if ($this->page_query_string === TRUE)
		{
			//	$this->cur_page = $this->CI->input->post($this->query_string_segment);
			$this->cur_page = get('single', $this->query_string_segment, TRUE);
		}
		else
		{
			// Default to the last segment number if one hasn't been defined.
			if ($this->uri_segment === 0)
			{
				$this->uri_segment = count($this->CI->uri->segment_array());
			}

			$this->cur_page = $this->CI->uri->segment($this->uri_segment);

			// Remove any specified prefix/suffix from the segment.
			if ($this->prefix !== '' OR $this->suffix !== '')
			{
				$this->cur_page = str_replace(array($this->prefix, $this->suffix), '', $this->cur_page);
			}
		}

		// If something isn't quite right, back to the default base page.
		if (!ctype_digit($this->cur_page) OR ($this->use_page_numbers && (int) $this->cur_page === 0))
		{
			$this->cur_page = $base_page;
		}
		else
		{
			// Make sure we're using integers for comparisons later.
			$this->cur_page = (int) $this->cur_page;
		}

		// Is the page number beyond the result range?
		// If so, we show the last page.
		if ($this->use_page_numbers)
		{
			if ($this->cur_page > $num_pages)
			{
				$this->cur_page = $num_pages;
			}
		}
		elseif ($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}

		$uri_page_number = $this->cur_page;
		$this->api_uri_page_number = $uri_page_number;

		// If we're using offset instead of page numbers, convert it
		// to a page number, so we can generate the surrounding number links.
		if (!$this->use_page_numbers)
		{
			$this->cur_page = (int) floor(($this->cur_page / $this->per_page) + 1);
		}

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with.
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;
	}

	public function get_first_link()
	{
		$output = '';
		if ($this->first_link !== FALSE && $this->cur_page > ($this->num_links + 1 + !$this->num_links))
		{
			$output = $this->api_first_url;
		}
		return $output;
	}

	public function get_first_link_url()
	{
		$output = '';
		if ($this->first_link !== FALSE && $this->cur_page > ($this->num_links + 1 + !$this->num_links))
		{
			$output = $this->base_url;
		}
		return $output;
	}

	public function get_first_link_name()
	{
		return '';
	}

	public function get_first_link_value()
	{
		return '';
	}

	public function get_last_link()
	{
		$output = '';
		$num_pages = (int) ceil($this->total_rows / $this->per_page);
		if ($this->last_link !== FALSE && ($this->cur_page + $this->num_links + !$this->num_links) < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $num_pages : ($num_pages * $this->per_page) - $this->per_page;
			$output = $this->api_base_url . $this->prefix . $i . $this->suffix;
		}
		return $output;
	}
		
	public function get_last_link_url()
	{
		$output = '';
		$num_pages = (int) ceil($this->total_rows / $this->per_page);
		if ($this->last_link !== FALSE && ($this->cur_page + $this->num_links + !$this->num_links) < $num_pages)
		{
			$output = $this->base_url;
		}
		return $output;		
	}

	public function get_last_link_name()
	{
		$output = '';
		$num_pages = (int) ceil($this->total_rows / $this->per_page);
		if ($this->last_link !== FALSE && ($this->cur_page + $this->num_links + !$this->num_links) < $num_pages
			&& $this->page_query_string === TRUE)
		{
			$output = $this->query_string_segment;
		}
		return $output;		
	}

	public function get_last_link_value()
	{
		$output = '';
		$num_pages = (int) ceil($this->total_rows / $this->per_page);
		if ($this->last_link !== FALSE && ($this->cur_page + $this->num_links + !$this->num_links) < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $num_pages : ($num_pages * $this->per_page) - $this->per_page;
			$output = $i;
		}
		return $output;
	}	

	public function get_prev_link()
	{
		$output = '';
		$num_pages = (int) ceil($this->total_rows / $this->per_page);
		if ($this->last_link !== FALSE && ($this->cur_page + $this->num_links + !$this->num_links) < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $num_pages : ($num_pages * $this->per_page) - $this->per_page;
			$output = $this->api_base_url . $this->prefix . $i . $this->suffix;
		}

		$output = '';
		if ($this->prev_link !== FALSE && $this->cur_page !== 1)
		{
			$i = ($this->use_page_numbers) ? $this->api_uri_page_number - 1 : $this->api_uri_page_number - $this->per_page;
			if ($i === $this->api_base_page)
			{
				// First page
				$output = $this->api_first_url;
			}
			else
			{
				$append = $this->prefix . $i . $this->suffix;
				$output = $this->api_base_url . $append;
			}
		}
		return $output;
	}

	public function get_prev_link_url()
	{
		$output = '';
		if ($this->prev_link !== FALSE && $this->cur_page !== 1)
		{
			$output = $this->base_url;
		}
		return $output;
	}

	public function get_prev_link_name()
	{
		$output = '';
		if ($this->prev_link !== FALSE && $this->cur_page !== 1 && $this->page_query_string === TRUE)
		{
			$i = ($this->use_page_numbers) ? $this->api_uri_page_number - 1 : $this->api_uri_page_number - $this->per_page;
			if (!($i === $this->api_base_page))
			{
				$output = $this->query_string_segment;
			}
		}
		return $output;
	}

	public function get_prev_link_value()
	{
		$output = '';
		if ($this->prev_link !== FALSE && $this->cur_page !== 1)
		{
			$i = ($this->use_page_numbers) ? $this->api_uri_page_number - 1 : $this->api_uri_page_number - $this->per_page;
			if (!($i === $this->api_base_page))
			{
				$output = $i;
			}
		}
		return $output;
	}

	public function get_next_link()
	{
		$output = '';
		$num_pages = (int) ceil($this->total_rows / $this->per_page);
		if ($this->next_link !== FALSE && $this->cur_page < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $this->cur_page + 1 : $this->cur_page * $this->per_page;
			$output = $this->api_base_url . $this->prefix . $i . $this->suffix;
		}
		return $output;
	}

	public function get_next_link_url()
	{
		$output = '';
		$num_pages = (int) ceil($this->total_rows / $this->per_page);
		if ($this->next_link !== FALSE && $this->cur_page < $num_pages)
		{
			$output = $this->base_url;
		}
		return $output;
	}

	public function get_next_link_name()
	{
		$output = '';
		$num_pages = (int) ceil($this->total_rows / $this->per_page);
		if ($this->next_link !== FALSE && $this->cur_page < $num_pages && $this->page_query_string === TRUE)
		{
			$output = $this->query_string_segment;
		}
		return $output;
	}

	public function get_next_link_value()
	{
		$output = '';
		$num_pages = (int) ceil($this->total_rows / $this->per_page);
		if ($this->next_link !== FALSE && $this->cur_page < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $this->cur_page + 1 : $this->cur_page * $this->per_page;
			$output = $i;
		}
		return $output;
	}

}

/* End of file APITOKO1001_Pagination.php */
/* Location: ./application/libraries/APITOKO1001_Pagination.php */