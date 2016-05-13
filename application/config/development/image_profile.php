<?php

$real_name = $_FILES['userfile']['name'];
$ext = pathinfo($real_name, PATHINFO_EXTENSION);

$config['image_profile'] = array(
	'upload_path' => '../' . ORDER_UPLOAD_IMAGE,
	'file_name' => get('single', 'member_seq') . strtotime("now") .'.'. $ext,
	'allowed_types' => 'gif|jpg|png',
	'max_size' => '100',
	'max_width' => '1024',
	'max_height' => '768',
);
