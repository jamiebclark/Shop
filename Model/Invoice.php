<?php
App::uses('InvoiceEmail', 'Shop.Network\Email');
class Invoice extends ShopAppModel {
	var $name = 'Invoice';
	var $actsAs = array(
		'Location.Mappable', 
		'Shop.ChangedFields'
	);

	var $virtualFields = array(
		'title' => 'CONCAT("Invoice #", $ALIAS.id)',
	);
	
	var $order = '$ALIAS.created DESC';
	
	var $hasOne = array(
		'Shop.Order',
		//'BowlathonPledge',
		//'Donation',
		//'FundraisingProfileDonation',
		'PaypalPayment' => array(
			'className' => 'Shop.PaypalPayment',
			'foreignKey' => 'invoice',
		),
		//'NsaMember',
		//'DonorCardOrder',
	);	
	var $belongsTo = array(
		//'User' => array('foreignKey' => 'user_id'),
		//'Shop.PaypalPayment',
		//'State' => array('foreignKey' => 'Invoice.state'),
		//'Country' => array('foreignKey' => 'Invoice.country'),
		'Shop.InvoicePaymentMethod',
	);
	
	var $validate = array(
		'first_name' => array(
			'rule' => 'notEmpty',
			'message' => 'Please enter a first name',
		),
		'last_name' => array(
			'rule' => 'notEmpty',
			'message' => 'Please enter a last name',
		),
		'amt' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a donation amount',
			),
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please only enter a number for your donation amount',
			)
		),
		'email' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter your email address',
			),
			'email' => array(
				'rule' => 'email',
				'message' => 'Please enter a valid email address',
			)
		)
	);

	//Tracks from beforeSave to afterSave whether a confirmation email should be sent
	private $sendPaidEmail = false;
	public $syncedModels = array();
	
	function beforeFind($queryData) {
		$queryData['fields'] = array_merge(array('*'), (array) $queryData['fields']);
		foreach ($this->hasOne as $alias) {
			$queryData['link'][] = $alias;
		}
		return parent::beforeFind($queryData);
	}
	
	function beforeSave($options = array()) {
		$data =& $this->getData();
		if (!empty($data['send_paid_email'])) {
			$this->sendPaidEmail = true;
		}
		return parent::beforeSave($options);
	}
	
	function afterSave($created) {
		$result = $this->read(null, $this->id);
		$data =& $this->getData();
		
		$this->copyToModels($this->id);
		
		if ($created || array_search('paid', $this->changedFields)) {
			$this->updatePaid($this->id);
		}
		
		if ($this->sendPaidEmail && !empty($result[$this->alias]['paid'])) {
			$this->sendPaidEmail($this->id);
		}
		
		return parent::afterSave($created);
	}
	
	function updatePaid($id = null) {
		return true;
	}
	
/**
 * Finds any related models using the InvoiceSyncBehavior and updates them with the new Invoice information
 *
 * @param int $id Invoice id
 * @return void
 **/
	private function copyToModels($id) {
		$fn = 'copyInvoiceToModel';
		$contain = $fields = array();
		$models = array_merge($this->hasOne, $this->hasMany);
		foreach ($models as $model => $config) {
			$fields[] = "$model.*";
			if (is_numeric($model)) {
				$model = $config;
				$config = null;
			}
			$contain[] = $model;
		}
		$conditions = array($this->alias . '.id' => $id);
		$result = $this->find('first', compact('fields', 'contain', 'conditions'));
		if (!empty($result)) {
			foreach ($result as $model => $vals) {
				if ($model == $this->alias) {	//Avoids Invoice results
					continue;
				}
				if (method_exists($this->{$model}, $fn)) {
					$this->{$model}->$fn($vals[$this->{$model}->primaryKey], $id);
				}
			}
		}		
	}
	
	function sendPaidEmail($id = null) {
		$Email = new InvoiceEmail();
		$result = $this->read(null, $id);
		if ($result[$this->alias]['paid'] && ($Email->sendPaid($result) !== false)) {
			return $this->updateAll(
				array("{$this->alias}.paid_email" => 'NOW()'), 
				array("{$this->alias}.id" => $id)
			);
		}
		return null;
	}
	
	function sendAdminPaidEmail($id) {
		$Email = new InvoiceEmail();
		$result = $this->read(null, $id);
		return $Email->sendAdminPaid($result);
	}
}
