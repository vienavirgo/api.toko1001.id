<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESCTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

define("PRODUCT_UPLOAD_IMAGE", "assets/img/product/");
define("ASSET_IMG_HOME", 'assets/img/home/');
define("ASSET_IMG_ICON", "assets/img/home/icons/");
define("SLIDE_UPLOAD_IMAGE", "assets/img/admin/slide/");
define("ADV_UPLOAD_IMAGE", "assets/img/admin/floor/");
define("ORDER_UPLOAD_IMAGE", "assets/img/member/");
define("XS_IMAGE_UPLOAD", "xs-img/");
define("S_IMAGE_UPLOAD", "s-img/");
define("M_IMAGE_UPLOAD", "m-img/");
define("L_IMAGE_UPLOAD", "l-img/");
define("XL_IMAGE_UPLOAD", "xl-img/");
define("BANK_UPLOAD_IMAGE", "assets/img/admin/bank_logo/");

define("M_PRODUCT_VARIANT_MAX_PIC_IMG",6);
define("MERCHANT_LOGO","assets/img/merchant/logo/");
define("MEMBER_PROFILE_IMAGE","assets/img/member/");
define("VERIFICATION_MEMBER", "member/verifikasi_registrasi/");
define("VERIFICATION_MEMBER_FORGOT_PASSWORD", "member/verify_password/");

define("SLIDER_CATEGORY_TYPE", "1");
define("SLIDER_DETAIL_TYPE", "2");
define("SLIDER_REGISTER_TYPE", "3");
define("SLIDER_WEB_TYPE", "4");

define("SEARCH_PARAMETER_CODE", "e");
define("CATEGORY_PARAMETER_CODE", "b");


define("START_OFFSET", "mulai");
define("SEARCH", "e"); //search parameter
define("CATEGORY", "b"); //category identifier
define("PRICE_RANGE", "pr"); //category filter price parameter
define("OLD_TO_NEW_PRODUCT", "on"); //category filter order parameter
define("NEW_TO_OLD_PRODUCT", "no"); //category filter order parameter
define("PRICE_EXPENSIVE_TO_CHEAP", "ec"); //category filter order parameter
define("PRICE_CHEAP_TO_EXPENSIVE", "ce"); //category filter order parameter
define("BIGGEST_DISCOUNT_TO_SMALL", "bd"); //category filter discount order parameter
define("SMALLEST_DISCOUNT_TO_BIGGEST", "sd"); //category filter discount order parameter
define("ORDER_CATEGORY", "g");
define("ALL_CATEGORY", "kategori"); //show all category url
define("PARAMETER_CATEGORY_ATTRIBUTE", "attribute_list");
define("PARAMETER_CATEGORY_LIST", "category_list");

define("SAVE_ADD", "1");
define("SAVE_UPDATE", "2");
define("DELETE", "3");
define("FIND_ALL", "4");

define("CREATE", "1");
define("UPDATE", "2");
//define("DELETE", "3");
define("READ", "4");

define("NODE_REGISTRATION_MEMBER", "REG");
define("VOUCHER_TYPE_AUTOMATIC", 'A');

define("T_MEMBER_ACCOUNT_TRX_TYPE_CNL", "Batal Kirim");
define("T_MEMBER_ACCOUNT_TRX_TYPE_WDW", "Member Withdraw (Member Tarik)");
define("T_MEMBER_ACCOUNT_TRX_TYPE_ORD", "Pembayaran Order");
define("T_MEMBER_ACCOUNT_TRX_TYPE_RTR", "Retur Member");

/*
 * EXPEDITION CODE
 */
define("AWB_EXPEDITION_ERROR", "Koneksi ke Expedisi tidak berhasil dilakukan, silakan kontak administrator untuk dapat melanjutkan proses pengiriman");
define("EXPEDITION_CHECK", "1,5");

/*
 * WEB FUNCTION SERVICE
 */
define("FUNCTION_GET_JNE_RATE_EXPEDITION", "get_jne_expedition_rate");
define("FUNCTION_GET_PANDU_RATE_EXPEDITION", "get_pandu_expedition_rate");
define("FUNCTION_GET_TIKI_RATE_EXPEDITION", "get_tiki_expedition_rate");
define("FUNCTION_GET_RAJA_KIRIM_RATE_EXPEDITION", "get_raja_kirim_expedition_rate");

/*
 * WEB SERVICE TYPE
 */
define("GET_RATE_TYPE", "RAT");

define("URL_TIKI_API", "http://203.77.231.130");

define("URL_JNE_API", "http://api.jne.co.id:8889");
define("SUB_URL_JNE_API", "dms/generateCnoteTraining");
define("API_USERNAME_JNE", "DMS");
define("API_KEY_JNE", "a68591589a0b0160749ee15fb5d3cbfa");
define("JNE_CUST_ID", "11041400");
define("URL_JNE_API_TRACE", "http://api.jne.co.id:8889/tracing/");
define("GET_AWB_TYPE", "AWB");
define("FUNCTION_GET_JNE_AWB_EXPEDITION", "get_jne_expedition_awb");
define("FUNCTION_GET_JNE_AWB_TRACKING", "get_jne_tracking_awb");

define("URL_PANDU_API", "http://202.152.56.230:8080/soap/deltamas/index.php");
define("API_KEY_PANDU", "988211222");
define("PANDU_ACCOUNT_NO", "1500400497");

define("URL_RAJA_KIRIM_API", "http://api.rajakirim.co.id/pda_api.php");
define("API_USER_RAJA_KIRIM", "TK1");
define("API_KEY_RAJA_KIRIM", "a394559246e982c35816a2d03a8765ea");
define("URL_RAJA_KIRIM_API_CHECK", "http://primatama.dyndns.info/api/pda_api.php");
define("FUNCTION_GET_RK_AWB_TRACKING", "pda_json_cek_status");

/*
 * DEFAULT
 */
define("DEFAULT_PASSWORD", "********");
define("DEFAULT_RECORD_PER_PAGE", "5");
define("DEFAULT_QTY_PRODUCT", "1");
define("DEFAULT_EXPEDITION_SEQ_RATE", "1");
define("DEFAULT_EXPEDITION_SEQ_MERCHANT", "0");
define("DEFAULT_ERROR_404", "error_404.jpg");
define("DEFAULT_ERROR_404_XS", "error_404_xs.jpg");
define("DEFAULT_ERROR_404_SM", "error_404_sm.jpg");
define("DEFAULT_EXPEDITION_SERVICE_RATE", "REG");
define("DEFAULT_VALUE_VARIANT_SEQ", "1");
define("DEFAULT_PRODUCT_CATEGORY_HOME", "0");
define("DEFAULT_NAME", "Toko1001");
define("DEFAULT_SLOGAN", "Toko1001.id Aman Cepat Nyaman");
define("DEFAULT_TITLE", "Toko1001.id : Pusat Belanja Online Aman Cepat Nyaman untuk Handphone, Tablet, Komputer, Kamera, Fashion dan banyak lagi.");
define("DEFAULT_KEYWORD", "");
define("DEFAULT_DESCRIPTION", "Pusat belanja online dengan konsep \"Aman Cepat Nyaman\" untuk produk Handphone, Tablet, Komputer, Kamera, Fashion dan banyak lagi.");
define("DEFAULT_WEBSITE", "Toko1001.id");
define("DEFAULT_MALE_GENDER", "M");
define("DEFAULT_FEMALE_GENDER", "F");
define("DEFAULT_MAX_LIMIT_IMAGE", "2097152");
define("CURL_CONNECTION_TIMEOUT", 30);
define("OPERATION_TIMEOUT", 30);
define("CAPTCHA_POOL", 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
define("JNE_CTC_REG_CODE", 'CTC15');
define("JAKARTA_PROVINCE_SEQ", '11');

define("T_ORDER_PRODUCT_RETURN_RETURN_STATUS_N", "Pengajuan");
define("T_ORDER_PRODUCT_RETURN_RETURN_STATUS_R", "diTolak");
define("T_ORDER_PRODUCT_RETURN_RETURN_STATUS_F", "Refund");
define("T_ORDER_PRODUCT_RETURN_RETURN_STATUS_I", "Tukar Barang");

define("T_ORDER_PRODUCT_RETURN_SHIPMENT_STATUS_M", "Kirim Ke Toko1001");
define("T_ORDER_PRODUCT_RETURN_SHIPMENT_STATUS_A", "Diterima Toko1001");
define("T_ORDER_PRODUCT_RETURN_SHIPMENT_STATUS_S", "Dikirim ke Merchant");
define("T_ORDER_PRODUCT_RETURN_SHIPMENT_STATUS_R", "Diterima Merchant");
define("T_ORDER_PRODUCT_RETURN_SHIPMENT_STATUS_T", "Dikirim ke Member");
define("T_ORDER_PRODUCT_RETURN_SHIPMENT_STATUS_C", "Diterima Member");

define("FORGOT_PASSWORD_TYPE", "2");

/*
 * PAYMENT STATUS
 */
define("PAYMENT_UNPAID_STATUS_CODE", "U");
define("PAYMENT_WAIT_CONFIRM_STATUS_CODE", "W");
define("PAYMENT_CONFIRM_STATUS_CODE", "C");
define("PAYMENT_PAID_STATUS_CODE", "P");
define("PAYMENT_FAILED_STATUS_CODE", "F");
define("PAYMENT_CANCEL_BY_ADMIN_STATUS_CODE", "X");
define("PAYMENT_WAIT_CONFIRM_THIRD_PARTY", "T");

define("PAYMENT_TYPE_CASH", "C");
define("PAYMENT_TYPE_TRANSFER", "T");


/*
 * STOCK TRX TYPE
 */
define("STOCK_ADJUSTMENT_TYPE", "ADJ");
define("STOCK_ORDER_MEMBER_TYPE", "ORD");
define("STOCK_CANCEL_ORDER_MEMBER_TYPE", "CNL");

/*
 * EMAIL CODE
 */
define("MEMBER_REG_CODE", "MEMBER_REG");
define("MEMBER_REG_PROMO", "MEMBER_REG_PROMO");
define("MEMBER_REG_CODE_SUCCESS", "MEMBER_REG_SUCCESS");
define("MEMBER_REG_FROM_FACEBOOK", "MEMBER_REG_FACEBOOK_SUCCESS");
define("MEMBER_FORGOT_PASSWORD", "MEMBER_FORGOT_PASSWORD");
define("MERCHANT_FORGOT_PASSWORD", "MERCHANT_FORGOT_PASSWORD");
define("MERCHANT_FORGOT_PASSWORD_VERIFICATION", "MERCHANT_FORGOT_PASSWORD_VERIFICATION");
define("MEMBER_FORGOT_PASSWORD_VERIFICATION", "MEMBER_FORGOT_PASSWORD_VERIFICATION");
define("MERCHANT_REG_CODE", "MERCHANT_REG");
define("MERCHANT_REG_ADMIN_CODE", "MERCHANT_REG_ADMIN");
define("MERCHANT_CANCEL_ORDER", "MERCHANT_CANCEL_ORDER");
define("MERCHANT_STOCK_EXCEEDED", "MERCHANT_STOCK_EXCEEDED");
define("ORDER_SENT_CODE", "ORDER_SENT");
define("ORDER_INFO", "ORDER_INFO");


/*
 * PAYMENT TYPE
 */
define("PAYMENT_TYPE_BANK", "BNK");
define("PAYMENT_TYPE_DEPOSIT", "DPT");
define("PAYMENT_TYPE_CREDIT_CARD", "CC");
define("PAYMENT_TYPE_BCA_KLIKPAY", "BKP");
define("PAYMENT_TYPE_MANDIRI_KLIKPAY", "MKP");
define("PAYMENT_TYPE_MANDIRI_ECASH", "MEC");
define("PAYMENT_TYPE_DOCU_ATM", "ATM");
define("PAYMENT_TYPE_DOCU_ALFAMART", "ALF");
define("CURENCY_CODE", "IDR");
define("PAYMENT_DESC", "Pembayaran atas Pembelian Produk di toko1001.id");
define("RP", "Rp. ");
/*
 * STATUS
 */
define("APPROVE_STATUS_CODE", "A");
define("REJECT_STATUS_CODE", "R");
define("NEW_STATUS_CODE", "N");
define("READY_STATUS_CODE", "R");
define("PROCESS_STATUS_CODE", "S");
define("OPEN_STATUS_CODE", "O");
define("CLOSED_STATUS_CODE", "C");
define("REFUND_CODE", "RFD");
define("VERIFIED", "V");
define("UNVERIFIED", "U");
define("SUCCESS_STATUS", "success");
define("FAILED_STATUS", "failed");
define("NOT_FOUND_STATUS", "notfound");
define("VALUE", 'value');
define("REFUND_STATUS_CODE", "R");
define("LIVE_STATUS_CODE", "L");
define("CHANGE_STATUS", "C");
/*
 * DEPOSIT ACCOUNT TYPE
 */
define("CANCEL_ACCOUNT_TYPE", "CNL");
define("ORDER_ACCOUNT_TYPE", "ORD");
define("RETUR_ACCOUNT_TYPE", "RTR");
define("DEPOSIT_ACCOUNT_TYPE", json_encode(array("CNL" => "Batal Order", "WDW" => "Tarik Dana", "ORD" => "Order", "RTR" => "Retur")));
define("DEPOSIT_ACCOUNT_TYPE_FOR_REFUND_ADMIN", json_encode(array("CNL" => "Batal Order", "RTR" => "Retur")));


/*
 * ORDER STATUS
 */
define("ORDER_PREORDER_STATUS_CODE", "P");
define("ORDER_NEW_ORDER_STATUS_CODE", "N");
define("ORDER_READY_TO_SHIP_STATUS_CODE", "R");
define("ORDER_SHIPPING_STATUS_CODE", "S");
define("ORDER_DELIVERED_STATUS_CODE", "D");
define("ORDER_CANCEL_BY_MERCHANT_STATUS_CODE", "X");
define("ORDER_CANCEL_BY_SYSTEM_STATUS_CODE", "Y");


/*
 * PRODUCT STATUS
 */
define("PRODUCT_READY_STATUS_CODE", "R");
define("PRODUCT_CANCEL_BY_MERCHANT_STATUS_CODE", "X");
/*
 * MUTATION TYPE
 */
define("IN_MUTATION_TYPE", "I");
define("OUT_MUTATION_TYPE", "O");
define("CREDIT_MUTATION_TYPE", "C");
define("DEBET_MUTATION_TYPE", "D");
define("WITHDRAW_ACCOUNT_TYPE", "WDW");
define("PAYMENT_SEQ_DEPOSIT","2");
define("WEB_STATUS_CODE", "W");
define("ERROR_DEPOSIT_NOMINAL","Nominal harus lebih besar dari 0");
define("ERROR_DEPOSIT","Nominal yang anda masukan lebih besar dari saldo");



define("ORDER_NEW_CODE", "ORDER_NEW");
define("ORDER_PAY_CONFIRM_CANCEL_CODE", "ORDER_PAY_CONFIRM_CANCEL");
define("ORDER_PAY_CONFIRM_FAIL_CODE", "ORDER_PAY_CONFIRM_FAIL");
define("ORDER_PAY_CONFIRM_SUCCESS_CODE", "ORDER_PAY_CONFIRM_SUCCESS");
define("ORDER_RETURN_REJECT", "ORDER_RETURN_REJECT");
define("ORDER_RETURN_MERCHANT_REFUND", "ORDER_RETURN_MERCHANT_REFUND");
define("ORDER_RETURN_MERCHANT_APPROVE", "ORDER_RETURN_MERCHANT_APPROVE");
define("MEMBER_WITHDRAW_CODE", "MEMBER_WITHDRAW");
define("MEMBER_WITHDRAW_SUCCESS", "MEMBER_WITHDRAW_SUCCESS");
define("MEMBER_WITHDRAW_FAIL", "MEMBER_WITHDRAW_FAIL");
define("MEMBER_REFUND_SUCCESS_CODE", "MEMBER_REFUND_SUCCESS");
define("MEMBER_REFUND_FAIL_CODE", "MEMBER_REFUND_FAIL");
define("MEMBER_REG_SUCCESS_CODE", "MEMBER_REG_SUCCESS");
define("MEMBER_REG_SUCCESS_CODE_VOUCHER", "MEMBER_REG_SUCCESS_VOUCHER");
define("MEMBER_CONFIRM_PAYMENT", "MEMBER_CONFIRM_PAYMENT");

//4617007700000039
define("MANDIRI_CLICKPAY_MERCHANT_ID", "TOKO1001");
define("MANDIRI_CLICKPAY_MERCHANT_PWD", "8605O1Ao1");
define("MANDIRI_CLICKPAY_INSERTPAYMENT", "https://mitrapay.mitracomm.com/mandiri_clickpay_dev/insertpay");
define("MANDIRI_CLICKPAY_REDIRECT_URL", "https://mitrapay.mitracomm.com/mandiri_clickpay_dev/redirect");
define("MANDIRI_CLICKPAY_CHECKPAYMENT", "https://mitrapay.mitracomm.com/mandiri_clickpay_dev/checkpay");
define("MANDIRI_CLICKPAY_REVERSAL", "https://mitrapay.mitracomm.com/mandiri_clickpay_dev/reversal");
