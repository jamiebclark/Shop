<?php
class PaypalFormConstant {
	protected static $config = [
		'returnUrl' => [
			'constant' => 'PAYPAL_RETURN_URL',
			'configure' => 'Shop.Paypal.returnUrl'
		],
		'cancelReturnUrl' => [
			'constant' => 'PAYPAL_CANCEL_URL',
			'configure' => 'Shop.Paypal.cancelUrl'
		],
		'imageUrl' => [
			'constant' => null, 
			'configure' => 'Shop.Paypal.imageUrl'
		],
		'userName' => [
			'constant' => 'PAYPAL_USER_NAME',
			'configure' => 'Shop.Paypal.userName'
		],
		'companyName' => [
			'constant' => 'COMPANY_NAME',
			'configure' => 'Shop.Paypal.companyName'
		],
		'cancelReturnUrl' => '___URL___',
	];

	public static function get($key) {
		$value = null;
		if (isset(self::$config[$key])) {
			$value = self::$config[$key];
			if (is_array($value)) {
				if (array_key_exists('constant', $value) && defined($value['constant'])) {
					$value = constant($value['constant']);
				} else if (array_key_exists('configure', $value) && Configure::check($value['configure'])) {
					$value = $value['configure'];
				} else {
					$value = null;
				}
			}
		}
		$value = str_replace(
			'___URL___', 
			'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'], 
			$value
		);
		return $value;
	}
}