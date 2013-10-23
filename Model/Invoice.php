<?php
App::uses('InvoiceEmail', 'Shop.Network/Email');
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
		
		$this->copyToModels($this->id);
		
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
			debug(compact('ids', 'data'));
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