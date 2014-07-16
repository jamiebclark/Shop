<?php
App::uses('InvoiceEmail', 'Shop.Network/Email');
App::uses('ShopAppModel', 'Shop.Model');

class Invoice extends ShopAppModel {
	var $name = 'Invoice';
	var $actsAs = array(
		'Location.Mappable' => array('validate' => true), 
		'Shop.ChangedFields',
		'Shop.EmailTrigger' => array(
			'send_paid_email' => 'sendPaidEmail',
			'send_admin_email' => 'sendAdminPaidEmail',
		)
	);
	var $virtualFields = array('title' => 'CONCAT("Invoice #", $ALIAS.id)');
	var $order = array('$ALIAS.created' => 'DESC');
	
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

	public $syncedModels = array();
	
	function beforeFind($queryData) {
		$queryData['fields'] = array_merge(array('*'), (array) $queryData['fields']);
		foreach ($this->hasOne as $alias) {
			$queryData['link'][] = $alias['className'];
		}
		return parent::beforeFind($queryData);
	}
	
	function afterSave($created, $options = array()) {
		$result = $this->read(null, $this->id);
		$data =& $this->getData();
		
		//debug($this->data);
		/*
		if (empty($this->_syncingWithModel)) {	//Prevents infinite looping with Invoice Sync Behavior
			$this->copyToModels($this->id);
		}
		*/
		
		if ($created || array_search('paid', $this->changedFields)) {
			$this->updatePaid($this->id);
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
		$contain = $fields = $link = array();
		$models = array_merge($this->hasOne, $this->hasMany);
		foreach ($models as $model => $config) {
			$fields[] = "`$model`.*";
			$link[] = $config['className'];
			if (is_numeric($model)) {
				$model = $config;
				$config = null;
			}
			$contain[] = $model;
		}
		$conditions = array($this->escapeField('id') => $id);
		$result = $this->find('first', compact('fields', 'link', 'conditions'));
		
		if (!empty($result) && !empty($result[$this->alias]['model']) && !empty($models[$result[$this->alias]['model']])) {
			$model = $result[$this->alias]['model'];
			if (!empty($this->{$model}->actsAs['Shop.InvoiceSync']) || method_exists($this->{$model}, $fn)) {
				$this->{$model}->$fn($id);
			}
		}		
		exit();
	}
	
	function sendPaidEmail($id = null) {
		$Email = new InvoiceEmail();
		$result = $this->read(null, $id);
		if ($result[$this->alias]['paid'] && ($Email->sendPaid($result) !== false)) {
			return $this->updateAll(
				array($this->escapeField('paid_email') => 'NOW()'), 
				array($this->escapeField('id') => $id)
			);
		}
		return null;
	}
	
	function sendAdminPaidEmail($id) {
		$Email = new InvoiceEmail();
		$result = $this->read(null, $id);
		if (!empty($result[$this->alias]['paid'])) {	
			return $Email->sendAdminPaid($result);
		}
		return null;
	}
	
/**
 * Copies PaypalPayment information to Invoice model
 * 
 * @param int $id Model id
 * @param bool $soft If true, only copies into blank invoice fields
 *
 **/
	public function syncPaypal($id, $soft = true) {
		$result = $this->find('first', array(
			'fields' => 'PaypalPayment.id',
			'link' => array('Shop.PaypalPayment'),
			'conditions' => array($this->escapeField($this->primaryKey) => $id),
		));
		if (!empty($result['PaypalPayment']['id'])) {
			return $this->PaypalPayment->syncInvoice($result['PaypalPayment']['id'], $soft);
		} else {
			return null;
		}
	}
	
	public function fixTotals() {
		$Pdo = getModelPDO($this);
		$Sth = $Pdo->query('SELECT id, model, model_id, paid FROM `invoices` WHERE amt = 0');
		$startCount = $Sth->rowCount();
		
		$ids = array();
		while ($row = $Sth->fetch()) {
			$ids[] = $row['id'];
			if (!empty($row['model']) && !empty($row['model_id'])) {
				$Model = ClassRegistry::init($row['model'], true);
				if (!empty($Model)) {
					//If Invoice has been paid or the Model exists, sync the totals, otherwise delete it
					if (!empty($row['paid']) || $Model->read(null, $row['model_id'])) {
						$Model->create();
						$Model->copyModelToInvoice($row['model_id']);
					} else {
						$this->delete($row['id']);
					}
				}
			}
		}
		$result = $this->find('all', array('conditions' => array(
			'Invoice.id' => $ids,
			'Invoice.amt' => 0,
		)));
		$endCount = count($result);
		if ($startCount != $endCount) {
			debug("$endCount Found after initially finding $startCount");
		}
	}
	
	public function fixDuplicates() {
		$Pdo = getModelPDO($this);
		$Sth = $Pdo->query('SELECT 
			id, model, model_id, COUNT(id) AS dup_count 
		FROM `invoices` 
		WHERE model IS NOT NULL AND model IS NOT NULL 
		GROUP BY model, model_id 
		HAVING dup_count > 1');
		$SthDup = $Pdo->prepare('SELECT * FROM `invoices` WHERE model=:model AND model_id=:modelId ORDER BY created ASC');
		while($row = $Sth->fetch()) {
			$SthDup->bindParam(':model', $row['model']);
			$SthDup->bindParam(':modelId', $row['model_id']);
			$SthDup->execute();
			$data = array();
			$ids = array();
			while ($invoice = $SthDup->fetch()) {
				$ids[] = $invoice['id'];
				foreach ($invoice as $field => $val) {
					if (!is_numeric($field) && !empty($val)) {
						$data[$field] = $val;
					}
				}
			}
			$this->create();
			//Removes Extra Ids
			$this->deleteAll(array(
				$this->escapeField('id') => $ids,
				'NOT' => array($this->escapeField('id') => $data['id'])
			));
			//Saves Combined Entry
			$this->save($data, array('callbacks' => false, 'validate' => false));
		}
	}
}