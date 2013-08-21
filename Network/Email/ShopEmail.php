<?php
App::uses('SuperEmail', 'SuperEmail.Network/Email');
//App::uses('App', 'Controller')
class ShopEmail extends SuperEmail {
	private $copyHtmlFormat = false;
	
	function __construct($config = null) {
		$config = array_merge(array(
			'from' => array(COMPANY_EMAIL => COMPANY_NAME),
			'host' => COMPANY_EMAIL_HOST,
			'username' => COMPANY_EMAIL_USER,
			'password' => COMPANY_EMAIL_PASSWORD,
			'transport' => COMPANY_EMAIL_TRANSPORT,
			'port' => COMPANY_EMAIL_PORT,
		), (array) $config);
		parent::__construct($config);
	}
	
	function setHelpers() {
		if (!empty($this->helpers)) {
			$this->helpers($this->helpers);
		}
	}
	
	function sendResult($result) {
		if (empty($result[$this->model]['email'])) {
			return null;
		}
		$this->setHelpers();
		return $this
			->to($result[$this->model]['email'])
			->send();
	}
}