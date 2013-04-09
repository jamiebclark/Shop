<?php
class InvoicesController extends AppController {
	var $name = 'Invoices';
	var $components = array(
		'Email',
		'Shop.InvoiceEmail',
	);
	
	var $helpers = array(
		'Layout.Table', 
		'Layout.Calendar', 
		'Layout.DateBuild', 
		'Shop.Invoice'
	);
	
	var $paginate = array('limit' => 50);
	
	function view($id) {
		$this->Invoice->recursive = 1;
		$invoice = $this->Invoice->find('first', array(
			'fields' => '*',
			'postContain' => array('Order' => array('postContain' => 'OrderProduct')),
			'conditions' => array('Invoice.id' => $id)
		));
		$this->set(compact('invoice'));
	}
	
	function admin_index() {
		if (!empty($this->request->data['Invoice']['id'])) {
			$invoice = $this->Invoice->findById($this->request->data['Invoice']['id']);
			if (!empty($invoice)) {
				$this->redirect(array('action' => 'view', $invoice['Invoice']['id']));
			} else {
				$this->_redirectMsg(true, 'Invoice #' . $this->request->data['Invoice']['id'] . ' Not Found');
			}
		}
		
		$invoices = $this->paginate();
		$this->set(compact('invoices'));
	}
	
	function admin_view($id = null) {
		if (!empty($this->request->params['named']['notify'])) {
			$msg = $this->InvoiceEmail->adminNotify($id) ? 'Email sent' : 'Error sending email';
			$this->_redirectMsg(array($id), $msg);
		}

		$this->Invoice->recursive = 1;
		$invoice = $this->Invoice->findById($id);
		$this->set(compact('invoice'));
		
		$this->set('states', $this->Invoice->State->selectList());
		$this->set('countries', $this->Invoice->Country->selectList());
		$this->set('invoicePaymentMethods', $this->Invoice->InvoicePaymentMethod->selectList());
		$this->set('back_link', array('Back to Invoices', array('action' => 'index')));
	}
	
	function admin_edit($id = null) {
		if ($this->_saveData(null, null, array('validate' => false)) === null) {
			$this->request->data = $this->Invoice->findById($id);
			
			//Preloads Bowlathon Pledge info
			if (!empty($this->request->data['BowlathonPledge']['id']) && empty($this->request->data['Invoice']['first_name'])) {
				$result = $this->Invoice->BowlathonPledge->BowlathonPledger->find('first', array(
					'link' => array('BowlathonPledge'),
					'conditions' => array(
						'BowlathonPledge.id' => $this->request->data['BowlathonPledge']['id'],
					)
				));
				$result = $result['BowlathonPledger'];
				if (empty($result['first_name']) && empty($result['last_name']) && !empty($result['name'])) {
					$names = explode(' ', $result['name']);
					$result['first_name'] = array_shift($names);
					$result['last_name'] = implode(' ', $names);
				}
				$keys = array('user_id', 'first_name', 'last_name', 'addline1', 'addline2', 'city', 'state', 'zip', 'country', 'email', 'phone');
				foreach ($keys as $key) {
					if (empty($this->request->data['Invoice'][$key]) && !empty($result[$key])) {
						$this->request->data['Invoice'][$key] = $result[$key];
					}
				}
			}

		}

		$this->set('states', $this->Invoice->State->selectList());
		$this->set('countries', $this->Invoice->Country->selectList());
		$this->set('invoicePaymentMethods', $this->Invoice->InvoicePaymentMethod->selectList());
	}
	
	function admin_add() {
		$this->_saveData();
		
		$this->set('states', $this->Invoice->State->selectList());
		$this->set('countries', $this->Invoice->Country->selectList());
	}
	
	function admin_delete($id = null) {
		$this->_deleteData($id);
	}
	
	function admin_filter() {
		$this->render('/FindFilters/filter');
	}
		function admin_resend_email($id = null, $test = false) {		$this->FormData->findModel($id);				if ($this->InvoiceEmail->adminNotify($id, $test)) {			$msg = 'Email successfully sent';		} else {			$msg = 'There was an error sending email';		}				$this->_redirectMsg(array('action' => 'view', $id), $msg);	}	
	function admin_copy_payment($id = null) {
		$invoice = $this->Invoice->find('first', array(
			'fields' => array('*'),
			'link' => array('PaypalPayment'),
			'conditions' => array(
				'Invoice.id' => $id,
			)
		));
		
		if (!empty($invoice['PaypalPayment'])) {
			if ($this->Invoice->PaypalPayment->syncInvoice($invoice['PaypalPayment']['id'])) {
				$msg = 'Successfully updated Invoice information';
			} else {
				$msg = 'Could not update Invoice information';
			}
		}
		$this->_redirectMsg(true, $msg);
	}
	
	function emailUpdate($id = null) {
		$invoice = $this->Invoice->find('first', array(
			'conditions' => array(
				'Invoice.id' => $id
			)
		));
		
		
	}
}
