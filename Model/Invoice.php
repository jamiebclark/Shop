<?php
App::uses('InvoiceEmail', 'Shop.Network/Email');
App::uses('ShopAppModel', 'Shop.Model');

class Invoice extends ShopAppModel {
	public $name = 'Invoice';
	public $actsAs = [
		'Location.Mappable' => ['validate' => true], 
		'Shop.ChangedFields',
		'Shop.EmailTrigger' => [
			'send_paid_email' => 'sendPaidEmail',
			'send_admin_email' => 'sendAdminPaidEmail',
		]
	];
	public $virtualFields = array('title' => 'CONCAT("Invoice #", $ALIAS.id)');
	public $order = ['$ALIAS.created' => 'DESC'];
	
	public $hasOne = [
		'Shop.Order',
		//'BowlathonPledge',
		//'Donation',
		//'FundraisingProfileDonation',
		'PaypalPayment' => [
			'className' => 'Shop.PaypalPayment',
			'foreignKey' => 'invoice',
		],
		//'NsaMember',
		//'DonorCardOrder',
	];
	
	public $belongsTo = [
		//'User' => ['foreignKey' => 'user_id'],
		//'Shop.PaypalPayment',
		//'State' => ['foreignKey' => 'Invoice.state'],
		//'Country' => ['foreignKey' => 'Invoice.country'],
		'Shop.InvoicePaymentMethod',
	];
	
	public $validate = [
		'first_name' => [
			'rule' => 'notEmpty',
			'message' => 'Please enter a first name',
		],
		'last_name' => [
			'rule' => 'notEmpty',
			'message' => 'Please enter a last name',
		],
		'amt' => [
			'notEmpty' => [
				'rule' => 'notEmpty',
				'message' => 'Please enter a donation amount',
			],
			'numeric' => [
				'rule' => 'numeric',
				'message' => 'Please only enter a number for your donation amount',
			]
		],
		'email' => [
			'notEmpty' => [
				'rule' => 'notEmpty',
				'message' => 'Please enter your email address',
			],
			'email' => [
				'rule' => 'email',
				'message' => 'Please enter a valid email address',
			]
		]
	];

	public $syncedModels = [];
	
	public function beforeSave($options = []) {
		$data =& $this->getData();

		// Makes sure net amount is set
		if (isset($data['amt']) && isset($data['net']) && empty($data['net'])) {
			$data['net'] = $data['amt'];
		}
		return parent::beforeSave($options);
	}

	public function beforeFind($queryData) {
		$queryData['fields'] = array_merge(['*'], (array) $queryData['fields']);
		foreach ($this->hasOne as $alias) {
			$queryData['link'][] = $alias['className'];
		}
		return parent::beforeFind($queryData);
	}
	
	public function afterSave($created, $options = []) {
		$id = $this->id;

		/*
		if (empty($this->_syncingWithModel)) {	//Prevents infinite looping with Invoice Sync Behavior
			$this->copyToModels($id);
		}
		*/
		// Fires if Payment information has changed
		$appModels = [];
		$lookForModels = ['models'];
		$plugins = CakePlugin::loaded();
		foreach ($plugins as $plugin) {
			$lookForModels[] = "$plugin.Model";
		}
		foreach ($lookForModels as $modelKey) {
			$models = App::objects($modelKey);
			foreach ($models as $className) {
				$Model = ClassRegistry::init($className, true);
				if (is_subclass_of($Model, 'Model') && $Model->hasMethod('copyInvoiceToModel')) {
					if (empty($this->_syncingWithModel)) {
						$Model->copyInvoiceToModel($id);
					}
				}

				/*
				if ($Model->hasMethod('afterInvoiceSave')) {
					if (!empty($Model->actsAs['Shop.InvoiceSync'])) {
						$config = $Model->actsAs['Shop.InvoiceSync'];
					} else {
						$config = [];
					}


					//$Model->Behaviors->unload('Shop.InvoiceSync');
					//$Model->afterInvoiceSave($created, $id);
					//$Model->Behaviors->load('Shop.InvoiceSync', $config);
				}
				*/
			}
		}
		$this->read(null, $id);
		return parent::afterSave($created);
	}
	
	
/**
 * Finds any related models using the InvoiceSyncBehavior and updates them with the new Invoice information
 *
 * @param int $id Invoice id
 * @return void
 **/
	private function copyToModels($id) {
		$result = $this->read(null, $id);
		$fn = 'copyInvoiceToModel';
		$contain = $fields = $link = [];
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
		$query = compact('fields', 'link', 'conditions');
		$result = $this->find('first', $query);
		
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
			'link' => ['Shop.PaypalPayment'],
			'conditions' => array($this->escapeField($this->primaryKey) => $id),
		));
		if (!empty($result['PaypalPayment']['id'])) {
			return $this->PaypalPayment->syncInvoice($result['PaypalPayment']['id'], $soft);
		} else {
			return null;
		}
	}
	
/**
 * Deletes invoice entries with 0 amount that don't sync to any sort of model
 *
 **/
	public function fixTotals() {
		$Pdo = getModelPDO($this);
		$Sth = $Pdo->query('SELECT id, model, model_id, paid, amt FROM `invoices` WHERE amt = 0');
		$startCount = $Sth->rowCount();
		
		$ids = [];
		while ($row = $Sth->fetch()) {
			$deleteId = null;
			$ids[] = $row['id'];
			if (!empty($row['model']) && !empty($row['model_id'])) {
				$Model = ClassRegistry::init($row['model'], true);
				if (!empty($Model)) {
					//If Invoice has been paid or the Model exists, sync the totals, otherwise delete it
					$result = $Model->read(null, $row['model_id']);
					$isFound = !empty($result);
					$isPaid = $row['amt'] > 0 && !empty($row['paid']);

					if ($isPaid || $isFound) {
						$Model->create();
						$Model->copyModelToInvoice($row['model_id']);
					} else {
						$deleteId = $row['id'];
					}
				}
			} else {
				$deleteId = $row['id'];
			}

			if (!empty($deleteId)) {
				debug(sprintf('ID #%d should be deleted', $deleteId));
				$this->delete($row['id']);
			}
		}

		$result = $this->find('all', ['conditions' => [
			'Invoice.id' => $ids,
			'Invoice.amt' => 0,
		]]);
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
			$data = [];
			$ids = [];
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
			$this->save($data, ['callbacks' => false, 'validate' => false]);
		}
	}

	public function findModelTypes() {
		$result = $this->find('all', array(
			'fields' => array($this->escapeField('model')),
			'group' => $this->escapeField('model')
		));
		return Hash::extract($result, '{n}.Invoice.model');
	}
}