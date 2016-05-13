<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class APITOKO1001_Router extends CI_Router {

	function _parse_routes()
	{
		// Turn the segment array into a URI string
		$uri = implode('/', $this->uri->segments);

		// Is there a literal match?  If so we're done
		if (isset($this->routes[$uri]))
		{
			return $this->_set_request(explode('/', $this->routes[$uri]));
		}

		// Loop through the route array looking for wild-cards
		foreach ($this->routes as $key => $val)
		{
			// Convert wild-cards to RegEx
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

			// Does the RegEx match?
			if (preg_match('#^' . $key . '$#', $uri))
			{
				// Do we have a back-reference?
				if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^' . $key . '$#', $val, $uri);
				}

				return $this->_set_request(explode('/', $val));
			}
		}

		// If we got this far it means we didn't encounter a
		// matching route so we'll set the site default route
		// $this->_set_request($this->uri->segments);
		// INSTEAD show 404..
		if (count($this->uri->segments !== 0))
		{
			
		} else
		{
			// If we got this far it means we didn't encounter a
			// matching route so we'll set the site default route
			$this->_set_request($this->uri->segments);
		}
	}

}