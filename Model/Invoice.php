<?php
class Invoice extends ShopAppModel {
	var $name = 'Invoice';
	var $actsAs = array(
	//	'Location', 
		'Shop.ChangedFields'
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
		//'PaypalPayment',
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

	function beforeFind($queryData) {
		$queryData['fields'] = array_merge(array('*'), (array) $queryData['fields']);
		foreach ($this->hasOne as $alias) {
			$queryData['link'][] = $alias;
		}
		return parent::beforeFind($queryData);
	}
	
	function afterSave($created) {
		if (1 || $created || array_search('paid', $this->changedFields)) {
			$this->updatePaid($this->id);
		}
		return parent::afterSave($created);
	}
	
	function updatePaid($id = null) {
		$contain = array();
		$models = array_merge($this->hasOne, $this->hasMany);
		foreach ($models as $model => $config) {
			if (is_numeric($model)) {
				$model = $config;
				$config = null;
			}
			$contain[] = $model;
		}
		$conditions = array($this->alias . '.id' => $id);
		$invoice = $this->find('first', compact('contain', 'conditions'));
		
		if (!empty($invoice)) {
			foreach ($invoice as $model => $vals) {
				if ($model == $this->alias) {
					continue;
				}
				if (method_exists($this->{$model}, 'syncInvoiceToModel')) {
					$this->{$model}->syncInvoiceToModel($vals[$this->{$model}->primaryKey], $invoice['Invoice']);
				}
			}
		}
		return true;
	}	
}
