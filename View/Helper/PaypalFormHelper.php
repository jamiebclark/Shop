<<<<<<< HEAD
<?php
class PaypalFormHelper extends AppHelper {
	var $name = 'PaypalForm';
	var $helpers = array('Form', 'Html');
	
	var $business = 'webmaster@souperbowl.org';
	
	var $returnUrl = 'http://www.souperbowl.org';
	var $cancelReturnUrl = 'http://www.souperbowl.org';
	var $cbt = 'Return to SouperBowl.org';
	var $imageUrl = 'http://souperbowl.org/images/logos/sboc/paypal.gif';
	
	var $cmd = '_xclick'; 	//Alternated: '_cart'
	
	var $settings = array();
	var $settingsCache = array();
	
	function beforeRender($viewFile) {
		$this->cancelReturnUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		parent::beforeRender($viewFile);
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
		$return = '';
		foreach ($settings as $settingName => $settingValue) {
			$return .= $this->inputSetting($settingName, $settingValue);
		}
		return $return;
	}
		
			
	function create($options = array()) {
		$options = array_merge(array(
			'url' => 'https://www.paypal.com/cgi-bin/webscr',
			'type' => 'POST',
			'class' => 'paypalForm fullFormWidth',
		), $options);
		return $this->Form->create(false, $options) . "\n";
	}
	
	function end() {
		$this->addSetting('cmd',$this->cmd,false);
		$this->addSetting('upload','1',false);
		$this->addSetting('business',$this->business,false);
		$this->addSetting('no_shipping','0',false);
		$this->addSetting('return',$this->returnUrl,false);
		$this->addSetting('cbt',$this->cbt,false);
		$this->addSetting('cancel_return',$this->cancelReturnUrl,false);
		$this->addSetting('image_url',$this->imageUrl,false);
		$this->addSetting('currency_code','USD',false);
		
		/*
		//Also helpful:
			- item_name		:	Souper Bowl of Caring Donation
			- item_number	:	donation, or card::176
		*/
		
		$return = '';
		foreach($this->settings as $settingName => $settingValue) {
			if ($settingName == 'amt') {
				$settingValue = round($settingValue, 2);
			}
			if (strstr($settingName, 'phone')) {
				$settingValue = $this->_phoneFormat($settingName, $settingValue);
			}
			
			if (is_array($settingValue)) {
				$return .= $this->inputSettings($settingValue);
			} else {
				$return .= $this->inputSetting($settingName, $settingValue);
			}
		}
		$return .= $this->Form->end();
		return $return;
	}
	
	function _phoneFormat($name, $value) {
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
=======
<?php
class PaypalFormHelper extends AppHelper {
	var $name = 'PaypalForm';
	var $helpers = array('Form', 'Html');
	
	var $business = 'webmaster@souperbowl.org';
	
	var $returnUrl = 'http://www.souperbowl.org';
	var $cancelReturnUrl = 'http://www.souperbowl.org';
	var $cbt = 'Return to SouperBowl.org';
	var $imageUrl = 'http://souperbowl.org/images/logos/sboc/paypal.gif';
	
	var $cmd = '_xclick'; 	//Alternated: '_cart'
	
	var $settings = array();
	var $settingsCache = array();
	
	function beforeRender($viewFile) {
		$this->cancelReturnUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		parent::beforeRender($viewFile);
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
		$return = '';
		foreach ($settings as $settingName => $settingValue) {
			$return .= $this->inputSetting($settingName, $settingValue);
		}
		return $return;
	}
		
			
	function create($options = array()) {
		$options = array_merge(array(
			'url' => 'https://www.paypal.com/cgi-bin/webscr',
			'type' => 'POST',
			'class' => 'paypalForm fullFormWidth',
		), $options);
		return $this->Form->create(false, $options) . "\n";
	}
	
	function end() {
		$this->addSetting('cmd',$this->cmd,false);
		$this->addSetting('upload','1',false);
		$this->addSetting('business',$this->business,false);
		$this->addSetting('no_shipping','0',false);
		$this->addSetting('return',$this->returnUrl,false);
		$this->addSetting('cbt',$this->cbt,false);
		$this->addSetting('cancel_return',$this->cancelReturnUrl,false);
		$this->addSetting('image_url',$this->imageUrl,false);
		$this->addSetting('currency_code','USD',false);
		
		/*
		//Also helpful:
			- item_name		:	Souper Bowl of Caring Donation
			- item_number	:	donation, or card::176
		*/
		
		$return = '';
		foreach($this->settings as $settingName => $settingValue) {
			if ($settingName == 'amt') {
				$settingValue = round($settingValue, 2);
			}
			if (strstr($settingName, 'phone')) {
				$settingValue = $this->_phoneFormat($settingName, $settingValue);
			}
			
			if (is_array($settingValue)) {
				$return .= $this->inputSettings($settingValue);
			} else {
				$return .= $this->inputSetting($settingName, $settingValue);
			}
		}
		$return .= $this->Form->end();
		return $return;
	}
	
	function _phoneFormat($name, $value) {
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
>>>>>>> 7f1010ba1dfec77e6fe69120dbda39b9bea5eb76
}