<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('send_mail'))
{

	function send_mail($to_email = '', $subject = '', $message = '', $from_name = '', $from_email = '')
	{
		$ci = & get_instance();
		$email = $ci->config->item('email');
		$from_name = ($from_name == '') ? $email['from_name'] : '';
		$from_email = ($from_email == '') ? $email['from_email'] : '';

		$ci->email->initialize($email);
		$ci->email->from($from_email, $from_name);
		$ci->email->to($to_email);

		$ci->email->subject($subject);
		$ci->email->message($message);

		if (!$ci->email->send())
		{
			return $ci->email->print_debugger();
		}
	}

}

if (!function_exists('send_mail_log'))
{

	function send_mail_log($to_name = '', $to_email = '', $subject = '', $message = '', $email_cd = '', $code = '', $from_name = '', $from_email = '')
	{
		$sent_status = 0;
		$sent_status_message = FALSE;

		$status = send_mail($to_email, $subject, $message, $from_name, $from_email);
		if ($status === NULL)
		{
			$sent_status = 1;
			$sent_status_message = TRUE;
		}
		
		$save_status = add_mail_log($email_cd, $code, $to_name, $to_email, $subject, $message, $sent_status);
		$save_status_message = FALSE;
		if ($save_status === 1)
		{
			$save_status_message = TRUE;
		}
		return (array('sent_status' => $sent_status_message, 'save_status' => $save_status_message));
	}

}

if (!function_exists('add_mail_log'))
{

	function add_mail_log($email_cd = '', $code = '', $to_name = '', $to_email = '', $subject = '', $message = '', $sent_status = '0')
	{
		$ci = & get_instance();
		$paramval = 0;
		$t_email_seq = new_seq('t_email_log', 'seq');
		$sql = "INSERT INTO t_email_log(email_cd , seq, code, recipient_name, recipient_email, email_subject, email_content, sent_status, created_by, created_date)";
		$sql .= " VALUES('" . addslashes($email_cd) . "', '" . addslashes($t_email_seq) . "' ,'" . addslashes($code) . "',
			'" . addslashes($to_name) . "','" . addslashes($to_email) . "','" . addslashes($subject) . "','" . addslashes($message) . "','" . addslashes($sent_status) . "','ADMIN',NOW())";
		$query = $ci->db->query($sql);
		if ($query === TRUE)
		{
			$paramval = 1;
		}

		return $paramval;
	}

}

if (!function_exists('get_email_template'))
{

	function get_email_template($parameter = '')
	{		
        $params = new stdClass();		
		
		$parameter->email_cd =check_current_exit($parameter,array('email_cd'));
		$parameter->email_code =check_current_exit($parameter,array('email_code'));
		
		$ci = & get_instance();
		$sql = "SELECT subject,content FROM `m_email_template` where code = '{$parameter->email_cd}'";
		$query = $ci->db->query($sql);
		$row = $query->result();
		foreach ($row as $data)
		{
			$subject = $data->subject;
			$content = $data->content;
		}

		//get subject email template
		$replaced = '';
		$finded = '';
		$findpass = '';
		$string = $subject;
		foreach ($parameter as $key => $val)
		{
			if (is_array($val))
			{
				$finded = $key;
				foreach ($val as $key1 => $val1)
				{
					$replaced .= $val1 . '<br>';
				}
				$string = str_ireplace("[" . strtoupper($finded) . "]", $replaced, $string);
				$replaced = '';
				$finded = '';
			} else
			{
				$string = str_ireplace("[" . strtoupper($key) . "]", $val, $string);
			}
		}
		$subject = $string;

		// get content email template
		$replaced = '';
		$finded = '';
		$string = $content;
		foreach ($parameter as $key => $val)
		{
			if (is_array($val))
			{
				$finded = $key;
				foreach ($val as $key1 => $val1)
				{
					$replaced .= $val1 . '<br>';
				}

				$string = str_ireplace("[" . strtoupper($finded) . "]", $replaced, $string);
				$replaced = '';
				$finded = '';
			} else
			{
				if ("[" . strtoupper($key) . "]" == "[PASSWORD]")
				{
					$findpass = $val;
				}
				$string = str_ireplace("[" . strtoupper($key) . "]", $val, $string);
			}
		}
		$content = $string;

		$parameter_layout = new stdClass();
		// get master email template layout
		$parameter_layout->email_cd = "EMAIL_CONTENT";
		$sql = "SELECT subject, content FROM `m_email_template` where code = '{$parameter_layout->email_cd}'";
		$query = $ci->db->query($sql);
		$row = $query->result();
		foreach ($row as $data)
		{
			$template_subject = $data->subject;
			$template_content = $data->content;
		}
		$content = str_ireplace("[EMAIL_CONTENT]", $content, $template_content);
		// get setting email Website Name [WEB_TITLE]
		$type = 1;
		$sql = "SELECT `value` FROM s_setting WHERE seq = {$type};";
		$query = $ci->db->query($sql);
		$row = $query->result();
		foreach ($row as $data)
		{
			$webtitle = $data->value;
		}
		$subject = str_ireplace("[WEB_TITLE]", $webtitle, $subject);
		$content = str_ireplace("[WEB_TITLE]", $webtitle, $content);
		// get setting email Website URL [VIEW_URL]
		if (isset($parameter->email_code))
		{
			$params->email_code = $parameter->email_code;
		} else
		{
			$params->email_code = time() . generate_random_text(10);
		}
		
		$content = str_ireplace("[VIEW_URL]", "<a href='" . website_url() . "email/" . $params->email_code . "'>disini</a>", $content);
		// get setting email Website Logo Color [LOGO_URL]
		$type = 4;
		$sql = "SELECT `value` FROM s_setting WHERE seq = {$type};";
		$query = $ci->db->query($sql);
		$row = $query->result();
		foreach ($row as $data)
		{
			$replace = $data->value;
		}
		$content = str_ireplace("[LOGO_URL]", "<img src='" . get_base_source() . ASSET_IMG_HOME . $replace . "' alt='" . $webtitle . "'>", $content);
		$content = str_ireplace("[BASE_URL]", get_base_source(), $content);

		$data = array("subject" => $subject, "content" => $content);
		return $data;
	}

}

/* End of file APITOKO1001_email_helper.php */
/* Location: ./application/helpers/APITOKO1001_email_helper.php */