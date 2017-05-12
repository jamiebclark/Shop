<?php
App::uses('PluginConfig', 'Shop.Lib');
App::uses('PaypalForm', 'Shop.Lib');

PluginConfig::init('Shop');

class PaypalFormHelper extends AppHelper {
	public $name = 'PaypalForm';
	public $helpers = ['Form', 'Html'];

	protected $_engine;

	public function __construct(View $View, $settings = array()) {
		$settings = Hash::merge(array('engine' => 'Shop.PaypalForm'), $settings);
		parent::__construct($View, $settings);
		list($plugin, $engineClass) = pluginSplit($settings['engine'], true);
		App::uses($engineClass, $plugin . 'Lib');
		if (class_exists($engineClass)) {
			$this->_engine = new $engineClass($settings);
		} else {
			throw new CakeException(__d('cake_dev', '%s could not be found', $engineClass));
		}
	}

	public function addSetting($name, $value, $overwrite = true) {
		return $this->_engine->add($name, $value, $overwrite);
	}
	
	public function addSettings($settings = [], $overwrite = true) {
		return $this->_engine->add($settings, $overwrite);
	}
	
	public function inputSetting($name, $value, $options = []) {
		if (is_array($name)) {
			foreach ($name as $n => $v) {
				$this->inputSetting($n, $v);
			}
		} else {
			$options = array_merge([
				'name' => $name,
				'type' => 'hidden',
				'value' => $value,
			], $options);
			$this->_set($name, $value);
			$this->settingsCache[$name] = $value;
			return "\t" . $this->Html->tag('input', false, $options) . "\n";
		}
	}
	
	public function inputSettings($settings) {
		$out = '';
		foreach ($settings as $settingName => $settingValue) {
			$out .= $this->inputSetting($settingName, $settingValue);
		}
		return $out;
	}
		
	public function create($options = []) {
		$options = array_merge(
			$this->_engine->getFormOptions(),
			$options
		);
		return $this->Form->create(false, $options) . "\n";
	}
	
	public function end() {
		$out = '';
		$settings = $this->_engine->get();
		foreach ($settings as $key => $val) {
			$out .= $this->inputSettings($key, $val);
		}
		$out .= $this->Form->end();
		return $out;
	}
}