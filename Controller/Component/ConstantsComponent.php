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
	
	// Keeps track of when the global variables have been set
	private $_isSet = false;
	private $sessionName = 'Shop.settings';
	
	public function beforeFilter(Controller $controller) {
		// If a user is making changes to ShopSetting, skips setting the variables until after the saving is complete
		if (empty($controller->request->data['ShopSetting'])) {
			$this->setConstants();
		}
	}
	
	public function beforeRender(Controller $controller) {
		// If setting has been postponed, make sure to handle it here
		if (!$this->_isSet) {
			$this->setConstants();
		}
	}

	public function setConstants($reset = false) {
		$constants = $this->getConstants($reset);
		extract($constants);	//Returns $encrypted and $decrypted
		if (!empty($decrypted)) {
			foreach ($decrypted as $name => $val) {
				define($name, $val);
			}
		}
		return $this->Session->write($this->sessionName, $encrypted);
	}
	
	/**
	 * Determines how to best find stored constants
	 *
	 **/
	private function getConstants($reset = false) {
		if ($reset || !$this->Session->check($this->sessionName)) {
			return $this->getModelConstants();
		} else {
			return $this->getSessionConstants();
		}
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
		if ($encrypted = $this->Session->read($this->sessionName)) {
			foreach ($encrypted as $name => $value) {
				$decrypted[$name] = base64_decode($value);
			}
		}
		return compact('encrypted', 'decrypted');
	}
}