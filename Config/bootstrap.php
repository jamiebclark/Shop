<?php
define('STORE_SHIPPING_CUTOFF', date('Y-m-d', strtotime('- 2 weeks')));

//Invoice Info
define('COMPANY_NAME', 'Souper Bowl of Caring');
define('COMPANY_EMAIL', 'webmaster@souperbowl.org');

//Comma-separated list of every email that should receive the admin emails
define('COMPANY_ADMIN_EMAILS', 'jamie@souperbowl.org,killcast@gmail.com');

//Sending Email Connection Options
define('COMPANY_EMAIL_USER', 'webmaster@souperbowl.org');
define('COMPANY_EMAIL_PASSWORD', 'souper');
define('COMPANY_EMAIL_HOST', 'ssl://smtp.gmail.com');
define('COMPANY_EMAIL_TRANSPORT', 'Smtp');
define('COMPANY_EMAIL_PORT', 465);

define('COMPANY_ADDRESS', 'Souper Bowl of Caring<br/>PO Box 23224<br/>Columbia, SC 29224');

define('PAYPAL_USER_NAME', 'webmaster@souperbowl.org');
define('PAYPAL_RETURN_URL', 'http://souperbowl.org/');
define('PAYPAL_CANCEL_URL', 'http://souperbowl.org/');

define('SHOP_VARS_LOADED', true);

function trace($msg = null) {
	$msgs = array($msg);
	$trace = debug_backtrace();
	foreach ($trace as $row) {
		$row += array('function' => '','file' => '', 'line' => 0);
		$msgs[] = sprintf('%s() %s on line %d', $row['function'],$row['file'],$row['line']);
	}
	debug($msgs);
}