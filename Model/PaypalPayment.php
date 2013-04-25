<?php
class PaypalPayment extends ShopAppModel {
	var $name = 'PaypalPayment';
	var $belongsTo = array(
		'Invoice' => array(
			'className' => 'Shop.Invoice',
			'foreignKey' => 'invoice'
		)
	);
	//OnlineStore, Bowlathon
	
	function afterSave($created) {
		//Updates Invoice
		$id = $this->id;
		$this->updateCompletedInvoice($id);
		$this->read(null, $id);
		return parent::afterSave($created);
	}
	
	function syncInvoice($id, $soft = true) {
		$result = $this->find('first', array(
			'fields' => array('*'),
			'link' => array('Invoice'),
			'conditions' => array(
				$this->alias . '.id' => $id
			)
		));
		
		$syncFields = array(
			'first_name',
			'last_name',
			'address_street' => 'addline1',
			'address_city' => 'city',
			'address_state' => 'state',
			'address_zip' => 'zip',
			'payer_email' => 'email',
		);
		
		$invoiceData = array();
		if (!empty($result['Invoice']['id'])) {
			$invoiceData['id'] = $result['Invoice']['id'];
			$created = false;
		} else {
			$syncFields['mc_gross'] = 'amt';
			$created = true;
		}
		
		foreach ($syncFields as $paypalField => $invoiceField) {
			if (is_numeric($paypalField)) {
				$paypalField = $invoiceField;
			}
			if (!$soft || empty($result['Invoice'][$invoiceField])) {
				$invoiceData[$invoiceField] = $result[$this->alias][$paypalField];
			}
		}
		
		if (!empty($invoiceData)) {
			
			if ($created) {
				$invoiceData['created'] = $this->_datePaid($result);
			}
			
			$this->Invoice->create();
			$data = array(
				'Invoice' => $invoiceData,
				'PaypalPayment' => array('id' => $id)
			);
			if (!$this->Invoice->saveAll($data, array('validate' => false))) {
				return false;
			}			
			$this->updateCompletedInvoice($id);
			return true;
		} else {
			return false;
		}
	}
	
	function updateCompletedInvoice($id) {
		$this->create();
		$result = $this->read(null, $id);
		if ($result[$this->alias]['payment_status'] == 'Completed') {
			$invoiceId = $result[$this->alias]['invoice'];
			$date = $this->_datePaid($result);
			
			$this->Invoice->create();
			$invoice = $this->Invoice->read(null, $invoiceId);
			if (!empty($invoice)) {
				$this->Invoice->save(array(
						'id' => $invoiceId,
						'paid' => date('Y-m-d H:i:s', strtotime($date)),
						'invoice_payment_method_id' => 1,	//Type: PayPal
					), 
					array('validate' => false)
				);
			}
		}
	}
	
	function _datePaid($result) {
		$date = null;
		if (!empty($result[$this->alias]['payment_date'])) {
			$date = $result[$this->alias]['payment_date'];
		} else if (!empty($result[$this->alias]['created'])) {
			$date = $result[$this->alias]['created'];
		} else {
			$date = date('Y-m-d H:i:s');
		}
		return $date;
	}
}
