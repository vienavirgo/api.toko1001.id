<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
  | -------------------------------------------------------------------------
  | URI ROUTING
  | -------------------------------------------------------------------------
  | This file lets you re-map URI requests to specific controller functions.
  |
  | Typically there is a one-to-one relationship between a URL string
  | and its corresponding controller class/method. The segments in a
  | URL normally follow this pattern:
  |
  |	example.com/class/method/id/
  |
  | In some instances, however, you may want to remap this relationship
  | so that a different class/function is called than the one
  | corresponding to the URL.
  |
  | Please see the user guide for complete details:
  |
  |	https://codeigniter.com/user_guide/general/routing.html
  |
  | -------------------------------------------------------------------------
  | RESERVED ROUTES
  | -------------------------------------------------------------------------
  |
  | There are three reserved routes:
  |
  |	$route['default_controller'] = 'welcome';
  |
  | This route indicates which controller class should be loaded if the
  | URI contains no data. In the above example, the "welcome" class
  | would be loaded.
  |
  |	$route['404_override'] = 'errors/page_missing';
  |
  | This route will tell the Router which controller/method to use if those
  | provided in the URL cannot be matched to a valid route.
  |
  |	$route['translate_uri_dashes'] = FALSE;
  |
  | This is not exactly a route, but allows you to automatically route
  | controller and method names that contain dashes. '-' isn't a valid
  | class or method name character, so it requires translation.
  | When you set this option to TRUE, it will replace ALL dashes in the
  | controller and method URI segments.
  |
  | Examples:	my-controller/index	-> my_controller/index
  |		my-controller/my-method	-> my_controller/my_method
 */ 
$route['default_controller'] = 'page_not_found';
$route['404_override'] = 'page_not_found';
$route['v1/main'] = 'V1/Home/main';

// ROUTE FOR MEMBER
$route['v1/member/register'] = 'V1/Authentication/register';
$route['v1/member/register_form'] = 'V1/Authentication/register_form';
$route['v1/member/login'] = 'V1/Authentication/login';
$route['v1/member/login_page'] = 'V1/Authentication/login_page';
$route['v1/member/login_fb'] = "V1/Authentication/login_fb";
$route['v1/member/forgot'] = 'V1/Authentication/forgot_password';
$route['v1/member/edit_member/(:num)'] = "V1/Member/edit_member/$1";
$route['v1/member/info'] = "V1/Member/update";
$route['v1/member/account_test'] = "V1/Member/account_test";
$route['v1/member/return_product'] = "V1/Member/return_product";
$route['v1/member/order'] = "V1/Member2/order";
$route['v1/member/review_admin'] = "V1/Member2/review_admin";
$route['v1/member/review_member'] = "V1/Member2/review_member";
$route['v1/member/review'] = "V1/Member2/review";

$route['v1/member/list_member_address'] = "V1/Member/list_member_address"; // for testing
$route['v1/member/add_member_address/(:num)'] = "V1/Member/add_member_address/$1"; //for testing
$route['v1/member/edit_member_address/(:num)/(:num)'] = "V1/Member/edit_member_address/$1/$2"; //for testing 
$route['v1/member/delete_member_address/(:num)/(:num)'] = "V1/Member/delete_member_address/$1/$2"; // for testing
$route['v1/member/cities/(:num)'] = "V1/Member/cities/$1";  // for testing
$route['v1/member/districts/(:num)'] = "V1/Member/districts/$1"; // for testing

$route['v1/member/delete_wishlist'] = "V1/Member/delete_wishlist";
$route['v1/test'] = 'V1/Testing/page';
$route['v1/error_android/list_error'] = "V1/Error_android/list_error";

// NEW MEMBER
$route['v1/member'] = 'V1/Member2';
$route['v1/member/address'] = "V1/Member2/address";
$route['v1/member/wishlist'] = "V1/Member2/wishlist";
$route['v1/member/account'] = "V1/Member2/account";
$route['v1/member/return_product'] = "V1/Member2/return_product";
$route['v1/member/change_password'] = "V1/Member2/change_password";
$route['v1/member/upload_profile'] = "V1/Member2/upload_img";

//ROUTE FOR EXPEDITION
$route['v1/expedition'] = "V1/Expedition";
//ROUTE TRANSACTION
$route['v1/payment'] = 'V1/Transaction';

//ROUTE FOR PRODUCT
$route['v1/product/category_seq'] = 'V1/Products_category/main'; //ask toriq and paul
$route['v1/product/category/(:any)-b(:num)'] = 'V1/Products_category'; 
$route['v1/product/category/kategori'] = 'V1/Products_category'; //ask toriq and paul
$route['v1/product/sidebar_category'] = 'V1/Products_sidebar_category/main'; 
$route['v1/product/detail/(:num)'] = 'V1/Products_detail/main'; 
$route['v1/product_new'] = 'V1/Home/product_new';
$route['v1/product_promo'] = 'V1/Home/product_promo';
$route['v1/product/search'] = 'V1/Products_search/index'; //ask toriq and paul

$route['v1/area/province'] = 'V1/area/province';
$route['v1/area/province/(:num)'] = 'V1/area/province';
$route['v1/area/city'] = 'V1/area/city';
$route['v1/area/city/(:num)'] = 'V1/area/city';
$route['v1/area/city/(:num)/(:num)'] = 'V1/area/city';
$route['v1/area/district'] = 'V1/area/district';
$route['v1/area/district/(:num)'] = 'V1/area/district';
$route['v1/area/district/(:num)/(:num)'] = 'V1/area/district';

//ROUTE FOR MERCHANT PAGE
$route['v1/merchant/(:any)'] = 'V1/Merchant/main'; 

$route['translate_uri_dashes'] = FALSE;

//ROUTE FOR PAYMENT GATEWAY
$route['v1/payment/payment_method'] = 'V1/Payment/payment_gateway';

//STATIC PAGE
$route['v1/panduan_belanja'] = 'V1/Static_page/panduan_belanja';
$route['v1/pengembalian_barang'] = 'V1/Static_page/pengembalian_barang';
$route['v1/pengembalian_dana'] = 'V1/Static_page/pengembalian_dana';
