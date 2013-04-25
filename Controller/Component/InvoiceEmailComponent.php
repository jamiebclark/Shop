<?php
App::uses('CakeEmail', 'Network/Email');

class InvoiceEmailComponent extends Component {
	var $name = 'InvoiceEmail';
	var $components = array('Email');
	
	var $controller;
	var $settings;
	
	var $Invoice;
	
	var $from = 'webmaster@souperbowl.org';
	
	var $to = array(
		'Order' => array(
			'intern@souperbowl.org',
		)
	);
	
	function __construct(ComponentCollection $collection, $settings = array()) {
		$this->settings = $settings;
		parent::__construct($collection, $settings);
	}

	function initialize(&$controller) {
		$this->controller =& $controller;
		
		App::import('Model', 'Invoice');
		$this->Invoice = new Invoice();
		
		$helpers = array('Contact', 'DisplayText', 'Invoice', 'Email');
		foreach ($helpers as $helper => $config) {
			if (is_numeric($helper)) {
				$helper = $config;
				$config = array();
			}
			if (empty($controller->helpers) || !isset($controller->helpers[$helper]) || array_search($helper, $controller->helpers, true) === false) {
				$controller->helpers[$helper] = $config;
			}
		}
	}
	
	function adminNotify($invoiceId = null, $test = false) {
		$invoice = $this->Invoice->findById($invoiceId);
		$to = array();
		$itemName = $invoice['Invoice']['model_title'];
		
		//Finds who it should be sent to
		$userTypes = array('admin', 'hr', 'development');
		switch($itemName) {
			case 'Order':
				$userTypes[] = 'store';
			break;
			case 'NsaMember':
				$userTypes[] = 'nsa_admin';
			break;
			case 'BowlathonPledge':
				$userTypes[] = 'nyab_admin';
			break;
		}
		
		foreach (array('*', $itemName) as $emailType) {
			if (!empty($this->to[$emailType])) {
				if (!is_array($this->to[$emailType])) {
					$to[] = $this->to[$emailType];
				} else {
					foreach ($this->to[$emailType] as $toEmail) {
						$to[] = $toEmail;
					}
				}
			}
		}
		
		$users = $this->Invoice->User->find('all', array(
			'fields' => array('User.email', 'User.full_name'),
			'link' => array('StaffMember' => array('table' => 'admin.admin_members')),
			'userType' => $userTypes,
			'conditions' => array(
				'User.active' => 1,
				'StaffMember.active' => 1,
			)
		));
		if (empty($users)) {
			return false;
		}
		
		//Debug mode
		/*
		$users = array(
			0 => array(
				'User' => array(
					'full_name' => 'Jamie Clark (debug mode)',
					'email' => 'jamie@souperbowl.org',
				)
			)
		);
		*/
		foreach ($users as $user) {
			$to[$user['User']['email']] = $user['User']['full_name'];
		}
		
		if (!empty($invoice)) {
			$subject = 'Invoice #' . $invoice['Invoice']['id'] . ' ';
			$subject .= $invoice['Invoice']['paid'] ? 'PAID' : 'UNPAID';

			$email = new CakeEmail();			$email->from($this->from)				->to($to)				->subject($subject)				->emailFormat('both')				->template('invoice')				->viewVars(compact('invoice'));
			
			return $test ? true : $email->send();
		} else {
			return false;
		}
	}
}
