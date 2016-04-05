<?php
class InvoicesController extends ShopAppController {
	public $name = 'Invoices';

	public $components = [
		'FindFilter',
	];

	public $helpers = [
		'Layout.AddressBook',
		'Layout.AddressBookForm',
		'Layout.FormLayout',
		'Layout.Table', 
		'Layout.Calendar', 
		//'Layout.DateBuild', 
		'Shop.Invoice'
	];
	
	public $paginate = ['limit' => 50];
	
	public function _setFindFilters() {
		$invoiceModelTypes = $this->Invoice->findModelTypes();
		$invoiceModelTypes = ['' => ' --- Select a model --- '] + array_combine($invoiceModelTypes, $invoiceModelTypes);

		$filters = [];
		$filters['invoice'] = ['label' => 'Invoice #', 'type' => 'text'];
		$filters['model'] = ['options' => $invoiceModelTypes];
		$filters['model_id'] = [
			'label' => 'Model ID',
			'type' => 'text',
		];
		return $filters;
	}

	public function _findFilterVal($key, $val, $query = []) {
		if ($key == 'invoice') {
			$query['conditions']['Invoice.id'] = $val;
		} else if ($key == 'model') {
			$query['conditions']['Invoice.model'] = $val;
		} else if ($key == 'model_id') {
			$query['conditions']['Invoice.model_id'] = $val;
		}
		return $query;
	}

	public function view($id) {
		$this->Invoice->recursive = 1;
		$invoice = $this->Invoice->find('first', [
			'fields' => '*',
			'postContain' => ['Order' => ['postContain' => 'OrderProduct']],
			'conditions' => ['Invoice.id' => $id]
		]);
		$this->set(compact('invoice'));
	}
	
	public function admin_index() {
	//	$this->Invoice->fixDuplicates();
		$this->Invoice->fixTotals();
		
		if (!empty($this->request->data['Invoice']['id'])) {
			$invoice = $this->Invoice->findById($this->request->data['Invoice']['id']);
			if (!empty($invoice)) {
				$this->redirect(['action' => 'view', $invoice['Invoice']['id']]);
			} else {
				$this->redirectMsg(true, 'Invoice #' . $this->request->data['Invoice']['id'] . ' Not Found', false);
			}
		}
		$this->paginate = $this->FindFilter->findOptions();
		$invoices = $this->paginate();
		$this->set(compact('invoices'));
	}
	
	public function admin_view($id = null) {
		$this->Invoice->syncPaypal($id);
		if (!empty($this->request->params['named']['notify'])) {
			$msg = $this->Invoice->sendAdminPaidEmail($id) ? 'Email sent' : 'Error sending email';
			$this->redirectMsg([$id], $msg);
		}
		$this->FormData->findModel($id, null, ['contain' => ['PaypalPayment']]);
	}
	
	public function admin_edit($id = null) {
		$unsetValidates = ['email', 'addline1', 'city', 'state', 'zip', 'country', 'first_name', 'last_name'];
		foreach ($unsetValidates as $field) {
			unset($this->Invoice->validate[$field]);
		}

		$this->FormData->editData($id);
		/*
		if ($this->_saveData(null, null, ['validate' => false]) === null) {
			$this->request->data = $this->Invoice->findById($id);
			
			//Preloads Bowlathon Pledge info
			if (!empty($this->request->data['BowlathonPledge']['id']) && empty($this->request->data['Invoice']['first_name'])) {
				$result = $this->Invoice->BowlathonPledge->BowlathonPledger->find('first', [
					'link' => ['BowlathonPledge'],
					'conditions' => [
						'BowlathonPledge.id' => $this->request->data['BowlathonPledge']['id'],
					]
				]);
				$result = $result['BowlathonPledger'];
				if (empty($result['first_name']) && empty($result['last_name']) && !empty($result['name'])) {
					$names = explode(' ', $result['name']);
					$result['first_name'] = array_shift($names);
					$result['last_name'] = implode(' ', $names);
				}
				$keys = ['user_id', 'first_name', 'last_name', 'addline1', 'addline2', 'city', 'state', 'zip', 'country', 'email', 'phone'];
				foreach ($keys as $key) {
					if (empty($this->request->data['Invoice'][$key]) && !empty($result[$key])) {
						$this->request->data['Invoice'][$key] = $result[$key];
					}
				}
			}
		}
		*/
	}
	
	public function admin_add() {
		$this->FormData->addData();
	}
	
	public function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	public function admin_sync_paypal($id = null) {
		$success = $this->Invoice->syncPaypal($id);
		$redirect = ['action' => 'view', $id];

		if ($success === false) {
			$msg = 'Invoice not found';
			$element = 'error';
			$redirect = ['action' => 'index'];
		} else if (empty($success)) {
			$msg = 'PayPal payment not found';
			$element = 'warning';
		} else {
			$msg = 'Synced PayPal information';
			$element = 'success';
		}
		$this->Flash->set($msg, compact('element'));
		$this->redirect($redirect);
	}
	
	public function admin_resend_email($id = null) {
		$this->FormData->findModel($id);
		if ($success = $this->Invoice->sendAdminPaidEmail($id)) {
			$msg = 'Email successfully sent';
		} else {
			$msg = 'There was an error sending email';
		}
		$this->redirectMsg(['action' => 'view', $id], $msg, $success);
	}
	
	public function admin_copy_payment($id = null) {
		$invoice = $this->Invoice->find('first', [
			'fields' => ['*'],
			'link' => ['Shop.PaypalPayment'],
			'conditions' => [
				'Invoice.id' => $id,
			]
		]);
		if (!empty($invoice['PaypalPayment'])) {
			if ($success = $this->Invoice->PaypalPayment->syncInvoice($invoice['PaypalPayment']['id'])) {
				$msg = 'Successfully updated Invoice information';
			} else {
				$msg = 'Could not update Invoice information';
			}
		}
		$this->redirectMsg(true, $msg, $success);
	}
	
	public function _setFormElements() {
		$this->set('states', $this->Invoice->State->selectList());
		$this->set('countries', $this->Invoice->Country->selectList());
		$this->set('invoicePaymentMethods', $this->Invoice->InvoicePaymentMethod->selectList());
		//$this->set('models', ['' => ' --- No Model --- '] + $this->Invoice->syncedModels);
	}
}