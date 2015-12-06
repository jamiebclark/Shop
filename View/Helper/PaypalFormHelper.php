<?php
App::uses('PluginConfig', 'Shop.Lib');

PluginConfig::init('Shop');

class PaypalFormHelper extends AppHelper {
	public $name = 'PaypalForm';
	public $helpers = ['Form', 'Html'];
	
	public $userName;
	public $companyName;
	public $returnUrl;
	public $cancelReturnUrl;
	public $imageUrl;
	
	public $cmd = '_xclick'; 	//Alternated: '_cart'
	
	public $settings = [];
	public $settingsCache = [];
	
	private $_areConstantsSet = false;

	public function beforeRender($viewFile) {
		$this->setConstants();
		parent::beforeRender($viewFile);
	}
	
	public function addSetting($name, $value, $overwrite = true) {
		//Adds a PayPal setting
		if($overwrite || (!isset($this->settings[$name]) && !isset($this->settingsCache[$name]))) {
			$this->settings[$name] = $value;
			$this->settingsCache[$name] = $value;
		}
		return true;
	}
	
	public function addSettings($settings = [], $overwrite = true) {
		foreach ($settings as $name => $value) {
			$this->addSetting($name, $value, $overwrite);
		}
		return true;
	}
	
	public function inputSetting($name, $value, $options = []) {
		$options = array_merge([
			'name' => $name,
			'type' => 'hidden',
			'value' => $value,
		], $options);
		$this->_set($name, $value);
		$this->settingsCache[$name] = $value;
		return "\t" . $this->Html->tag('input', false, $options) . "\n";
	}
	
	public function inputSettings($settings) {
		$out = '';
		foreach ($settings as $settingName => $settingValue) {
			$out .= $this->inputSetting($settingName, $settingValue);
		}
		return $out;
	}
		
	public function create($options = []) {
		if (!$this->_areConstantsSet) {
			$this->setConstants();
		}

		$options = array_merge([
			'url' => 'https://www.paypal.com/cgi-bin/webscr',
			'type' => 'POST',
			'class' => 'paypal-form',
		], $options);

		return $this->Form->create(false, $options) . "\n";
	}
	
	public function end() {
		$this->addSetting('cmd',$this->cmd,false);
		$this->addSetting('upload', '1', false);
		$this->addSetting('business', $this->userName, false);
		$this->addSetting('no_shipping','0',false);
		$this->addSetting('return',$this->returnUrl,false);
		$this->addSetting('cbt', "Return to " . $this->companyName, false);
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

	private function getConstantValue($constantName, $configKey = null) {
		if (defined($constantName)) {
			return constant($constantName);
		} else if (Configure::check($configKey)) {
			return Configure::read($configKey);
		}
		return null;
	}

	private function setConstants() {
		// Set variables
		$this->returnUrl = $this->getConstantValue('PAYPAL_RETURN_URL', 'Shop.Paypal.returnUrl');
		$this->cancelReturnUrl = $this->getConstantValue('PAYPAL_CANCEL_URL', 'Shop.Paypal.cancelUrl');
		$this->imageUrl = $this->getConstantValue(null, 'Shop.Paypal.imageUrl');
		$this->userName = $this->getConstantValue('PAYPAL_USER_NAME', 'Shop.Paypal.userName');
		$this->companyName = $this->getConstantValue('COMPANY_NAME', 'Shop.Paypal.companyName');

		$this->cancelReturnUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		$this->_areConstantsSet = true;
	}
}