<?php
class AmazonFormHelper extends AppHelper {
	var $name = 'AmazonForm';
	var $helpers = array('Form', 'Html', 'Layout.Asset');
	
	var $returnUrl = PAYPAL_RETURN_URL;
	var $cancelReturnUrl = PAYPAL_CANCEL_URL;
	var $imageUrl = 'http://souperbowl.org/images/logos/sboc/paypal.gif';
	
	var $cmd = '_xclick'; 	//Alternated: '_cart'
	
	var $settings = array();
	var $settingsCache = array();
	
	// Amazon Mareketplace Web Service
	const MERCHANT_ID = 'A1A2YCFM1QZQCM';		//Also known as Seller ID
	const MARKETPLACE_ID = 'A3BXB0YN3XH17H';
	const AWS_ACCESS_KEY_ID = 'AKIAIFKWRN2HWD676NOQ';
	const SECRET_KEY = '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';
	
	// Amazon Payment App
	const CLIENT_ID = 'amzn1.application-oa2-client.d8cea9ff3821423cbffed5d1e0a2ff20';
	const CLIENT_SECRET = '3e6805145db6a06df9b77057e0aed3475c036dd29b8f2afd5c79688c732f99d3';
	

	public $useSandbox = true;

	// URLS that change when in sandbox mode. Key 0 is Production. Key 1 is Sandbox
	private $urls = array(
		'mws_endpoint' => array(
			'https://mws.amazonservices.com/OffAmazonPayments/2013-01-01/',
			'https://mws.amazonservices.com/OffAmazonPayments_Sandbox/2013-01-01/',
		),
		'widgets_script' => array(
			'https://static-na.payments-amazon.com/OffAmazonPayments/us/js/Widgets.js',
			'https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js',
		),
		'buttons' => array(
			'https://payments.amazon.com/gp/widgets/button',
			'https://payments-sandbox.amazon.com/gp/widgets/button',
		),
		'profile_endpoint' => array(
			'https://api.amazon.com/user/profile',
			'https://api.sandbox.amazon.com/user/profile',
		),
	);
	
	function beforeRender($options = array()) {
		$this->Asset->block('
			window.onAmazonLoginReady = function() {
				amazon.Login.setClientId("' . self::CLIENT_ID . '");
			};
		');
		$this->Asset->js($this->_getUrl('widgets_script') . '?sellerId=' . self::MERCHANT_ID, array(
			'afterBlock' => true,
		));
		return parent::beforeRender($options);
	}
	
	/**
	 * Generates a "Login with Amazon" button
	 * @param string $type
	 * - "LwA": "a" logo and "Login with Amazon"
	 * - "Login": "a" logo and "Login"
	 * - "A": "a" logo
	 * @param string $color Can be "Gold", "DarkGray", or "LightGray"
	 * @param string $size "small", "medium", "large", or "x-large"
	 **/
	public function loginButton($type = 'LwA', $color = 'Gold', $size = 'medium') {
		$scope = 'profile payments:widget payments:shipping_address';
		return $this->button($scope, compact('type', 'color', 'size'));
	}

	/**
	 * Generates a "Pay with Amazon" button
	 * @param string $type
	 * - "PwA": "a" logo and "Pay with Amazon"
	 * - "Pay": "a" logo and "Pay"
	 * - "A": "a" logo
	 * @param string $color Can be "Gold", "DarkGray", or "LightGray"
	 * @param bool $guest Whether or not a user can pay as a guest
	 **/
	public function paymentButton($type = 'PwA', $color = 'Gold', $guest = false) {
		$scope = $guest ? 'payments:widget' : 'profile payments:widget payments:shipping_address';
		return $this->button($scope, compact('type', 'color'));
	}
	
	
	private function button($scope, $options = array()) {
		$id = 'AmazonPayButton';
		$options = array_merge(compact('type') + array(
			'type' => 'LwA',
			'useAmazonAddressBook' => 'true',
			'authorization' => "function() {
				loginOptions = {scope: '$scope'}
				authRequest = amazon.Login.authorize(loginOptions);
			}",
			'onSignIn' => "function(orderReference) {
				// The following OAuth 2 response parameters will be included in
				// the query string when the customer's browser is redirected to
				// the URL below: access_token, token_type, expires_in, and scope.
				authRequest.onComplete('/redirect_page?session=' + orderReference.getAmazonOrderReferenceId());
			}",
			'onError' => 'function(error) {
				// your error handling code
			}',
		), $options);
		
		$script = "\nvar authRequest;\n";
		$script .= sprintf('OffAmazonPayments.Button("AmazonPayButton", "%s", %s);' . "\n", 
			self::MERCHANT_ID,
			$this->arrayToJs($options)
		);
		$out = $this->Html->div(null, '', compact('id'));
		$this->Asset->block($script);
		return $out;
	}
	
	function addSetting($name,$value,$overwrite=true) {
		//Adds a PayPal setting
		if($overwrite || (!isset($this->settings[$name]) && !isset($this->settingsCache[$name]))) {
			$this->settings[$name] = $value;
			$this->settingsCache[$name] = $value;
		}
		return true;
	}
	
	function addSettings($settings = array(), $overwrite = true) {
		foreach ($settings as $name => $value) {
			$this->addSetting($name, $value, $overwrite);
		}
		return true;
	}
	
	function inputSetting($name, $value, $options = array()) {
		$options = array_merge(array(
			'name' => $name,
			'type' => 'hidden',
			'value' => $value,
		), $options);
		$this->_set($name, $value);
		$this->settingsCache[$name] = $value;
		return "\t" . $this->Html->tag('input', false, $options) . "\n";
	}
	
	function inputSettings($settings) {
		$out = '';
		foreach ($settings as $settingName => $settingValue) {
			$out .= $this->inputSetting($settingName, $settingValue);
		}
		return $out;
	}
		
	function create($options = array()) {
		$options = array_merge(array(
			'url' => 'https://www.paypal.com/cgi-bin/webscr',
			'type' => 'POST',
			'class' => 'paypal-form',
		), $options);
		return $this->Form->create(false, $options) . "\n";
	}
	
	function end() {
		$this->addSetting('cmd',$this->cmd,false);
		$this->addSetting('upload', '1', false);
		$this->addSetting('business', PAYPAL_USER_NAME, false);
		$this->addSetting('no_shipping','0',false);
		$this->addSetting('return',$this->returnUrl,false);
		$this->addSetting('cbt', "Return to " . COMPANY_NAME,false);
		$this->addSetting('cancel_return',$this->cancelReturnUrl,false);
		$this->addSetting('image_url',$this->imageUrl,false);
		$this->addSetting('currency_code','USD',false);
		
		/*
		//Also helpful:
			- item_name		:	Souper Bowl of Caring Donation
			- item_number	:	donation, or card::176
		*/
		
		$out = '';
		foreach($this->settings as $settingName => $settingValue) {
			if ($settingName == 'amt') {
				$settingValue = round($settingValue, 2);
			}
			if (strstr($settingName, 'phone')) {
				$settingValue = $this->phoneFormat($settingName, $settingValue);
			}
			
			if (is_array($settingValue)) {
				$out .= $this->inputSettings($settingValue);
			} else {
				$out .= $this->inputSetting($settingName, $settingValue);
			}
		}
		$out .= $this->Form->end();
		return $out;
	}
	
	private function phoneFormat($name, $value) {
		$value = preg_replace('/[^0-9]/','',$value);
		$return = array();
		if(!empty($value)) {
			$return = array(
				$name . '_a' => substr($value, 0, 3),
				$name . '_b' => substr($value, 3, 3),
				$name . '_c' => substr($value, 6, 4)
			);
		}
		return $return;
	}

	// Returns value stored in the $urls array depending if it's in sandbox mode or not
	private function _getUrl($urlKey) {
		if (isset($this->urls[$urlKey])) {
			return $this->urls[$urlKey][$this->useSandbox];
		}
	}
	
	private function arrayToJs($array, $level = 0) {
		$out = '';
		$total = count($array);
		$count = 0;
		foreach ($array as $key => $value) {
			$out .= str_repeat("\t", $level + 1) . "$key: ";
			if (is_array($value)) {
				$out .= $this->arrayToJs($value, $level + 1);
			} else {
				if (substr($value, 0, 8) != 'function' && $value != 'true' && $value != 'false') {
					$value = '"' . $value . '"';
				}
				$out .= $value;
			}
			if (++$count != $total) {
				$out .= ',';
			}
			$out .= "\n";
		}
		return "{\n" . $out . "}\n";
	}
}