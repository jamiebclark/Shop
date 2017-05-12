<?php
/**
 * Converts data to PayPal POST data
 **/

App::uses('PaypalFormConstant', 'Shop.Lib/PaypalForm');

class PaypalForm {
	protected $formSettings = [
		'url' 				=> 'https://www.paypal.com/cgi-bin/webscr',
		'type' 				=> 'POST',
		'class' 			=> 'paypal-form',
	];

	// Default Settings
	protected $settings = [
		'cmd' 				=> '_xclick', 	//Alternate: '_cart'
		'currency_code' 	=> 'USD',
		'upload' 			=> 1,
		'no_shipping' 		=> 0,
	];

	public function __construct($settings = []) {
		$settingConstants = [
			'business' 			=> 'userName',
			'return' 			=> 'returnUrl',
			'cancel_return' 	=> 'cancelReturn',
			'image_url' 		=> 'imageUrl',
		];
		foreach ($settingConstants as $settingKey => $constantKey) {
			$this->set($settingKey, PaypalFormConstant::get($constantKey));
		}
	}

	public function set($name, $value, $overwrite = true) {
		if (is_array($name)) {
			$overwrite = $value;
			foreach ($name as $key => $value) {
				$this->set($key, $value, $overwrite);
			}
		} else {
			if($overwrite || (!isset($this->settings[$name]) && !isset($this->settingsCache[$name]))) {
				if ($value === false) {
					unset($this->settings[$name]);
				} else {
					if ($name == 'amt') {
						$value = round($value, 2);
					} else if (strstr($name, 'phone')) {
						unset($this->settings[$name]);
						$values = $this->phoneFormat($name, $value);
						foreach ($values as $k => $v) {
							$this->settings[$k] = $v;
						}
					}
					$this->settings[$name] = $value;
				}
			}
		}
		return true;
	}

	public function get() {
		return $this->settings;
	}

	public function getFormOptions() {
		return $this->formSettings;
	}

	public function getUrl() {
		$vars = $this->get();
		$formOptions = $this->getFormOptions();
		$url = $formOptions['url'];
		$vars = http_build_query($vars);
		return "$url?$vars";
	}

/**
 * Sets an entire model result as Paypal Form settings
 *
 * @param array $result The model result
 * @param array $translateKeys An array explaining the translation from result keys to Paypal Form keys
 * @return bool;
 **/
	public function setModelResult($result, $translateKeys = null) {
		$translateKeys = $this->getResultTranslateKeys($translateKeys);
		foreach ($translateKeys as $resultKey => $settingKey) {
			if (is_numeric($resultKey)) {
				$resultKey = $settingKey;
			}
			if (array_key_exists($resultKey, $result)) {
				$this->set($settingKey, $result[$resultKey]);
			}
		}
	}

	public function getResultTranslateKeys($result, $translateKeys = []) {
		if (empty($translateKeys)) {
			$translateKeys = array_keys($result);
		}
		return $translateKeys;
	}

	private function phoneFormat($name, $value) {
		$value = preg_replace('/[^0-9]/','',$value);
		$return = [];
		if(!empty($value)) {
			$return = array(
				$name . '_a' => substr($value, 0, 3),
				$name . '_b' => substr($value, 3, 3),
				$name . '_c' => substr($value, 6, 4)
			);
		}
		return $return;
	}
}

