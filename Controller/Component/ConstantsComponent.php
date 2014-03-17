<?php
/**
 * Shop Constants Component
 * 10/4/2013
 * Jamie Clark
 * 
 * Manages the constants stored for the Shop plugin
 **/

App::uses('Component', 'Controller');
class ConstantsComponent extends Component {
	public $name = 'Constants';
	public $components = array('Session');
	
	public $controller;
	
	// Keeps track of when the global variables have been set
	private $_isSet = false;
	const SESSION_NAME = 'Shop.settings';
	
	public function initialize(Controller $controller) {
		$this->controller = $controller;
		$this->setConstantsInit();
	}
	
	public function beforeRender(Controller $controller) {
		// If setting has been postponed, make sure to handle it here
		$this->setConstantsCheck();
	}

	//Sets the constants only if they aren't currently being edited via request data
	public function setConstantsInit() {
		// If a user is making changes to ShopSetting, skips setting the variables until after the saving is complete
		if (empty($this->controller->request->data['ShopSetting'])) {
			$this->setConstantsCheck();
		}
	}
	
	//Sets the constants only if they haven't been set yet
	public function setConstantsCheck() {
		if (!$this->_isSet) {
			return $this->setConstants();
		} else {
			return null;
		}
	}
	
	public function setConstants($reset = false) {
		$constants = $this->getConstants($reset);
		extract($constants);	//Returns $encrypted and $decrypted
		if (!empty($decrypted)) {
			foreach ($decrypted as $name => $val) {
				if (!defined($name)) {
					define($name, $val);
				}
			}
		}
		$this->_isSet = true;
		return $this->Session->write(self::SESSION_NAME, $encrypted);
	}
	
	/**
	 * Determines how to best find stored constants
	 *
	 **/
	private function getConstants($reset = false) {
		if ($reset || !$this->Session->check(self::SESSION_NAME)) {
			$constants = $this->getModelConstants();
		} else {
			$constants = $this->getSessionConstants();
			if (empty($constants['encrypted']) && empty($constants['decrypted'])) {
				$constants = $this->getConstants(true);
			}
		}
		return $constants;
	}
	
	/**
	 * Finds constant data based on the ShopSetting model in the database
	 *
	 **/
	private function getModelConstants() {
		$encrypted = array();
		if ($decrypted = ClassRegistry::init('Shop.ShopSetting')->find('list')) {
			foreach ($decrypted as $name => $value) {
				$encrypted[$name] = base64_encode($value);
			}
		}
		return compact('encrypted', 'decrypted');
	}
	
	/**
	 * Finds constant data based on the encrypted session data
	 *
	 **/
	private function getSessionConstants() {
		$decrypted = array();
		if ($encrypted = $this->Session->read(self::SESSION_NAME)) {
			foreach ($encrypted as $name => $value) {
				$decrypted[$name] = base64_decode($value);
			}
		}
		return compact('encrypted', 'decrypted');
	}
}