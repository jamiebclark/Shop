<?php
App::uses('PaypalIpn', 'Shop.Lib');

class PaypalPaymentsController extends ShopAppController {
	public $name = 'PaypalPayments';
	
	//public $components = ['Shop.InvoiceEmail'];
	
	public function admin_index() {
		$this->paginate = ['order' => ['PaypalPayment.created' => 'DESC']];
		$paypalPayments = $this->paginate();
		$this->set(compact('paypalPayments'));
	}
	
	public function admin_test($txnId = null) {
		$post = ['txn_id' => $txnId];
		$this->_savePost($post, true);
		$this->redirect(['action' => 'logs']);
	}
	
	public function admin_logs($logFile = null) {
		$logFiles = PaypalIpn::getLogFiles();
		$logDir = PaypalIpn::getLogDir();
		if (empty($logFile) || empty($logFiles[$logFile])) {
			$logFile = array_shift($logFiles);
		}
		$logFileContent = '';
		$logFilePath = $logDir . $logFile;
		if (is_file($logFilePath)) {
			$logFileContent = file_get_contents($logFilePath);
		}
		$this->set(compact('logFiles', 'logFile', 'logFileContent'));
	}

	public function admin_fix() {
		$paypalPayments = $this->PaypalPayment->find('all', [
			'fields' => '*',
			'link' => ['Shop.Invoice']
		]);
		$count = 0;
		foreach ($paypalPayments as $paypalPayment) {
			unset($paypalPayment['PaypalPayment']['modified']);
			$this->PaypalPayment->create();
			$this->PaypalPayment->read(null, $paypalPayment['PaypalPayment']['id']);
			$this->PaypalPayment->save($paypalPayment['PaypalPayment']);

			if (empty($paypalPayment['Invoice']['user_id'])) {
				$this->PaypalPayment->syncInvoice($paypalPayment['PaypalPayment']['id'], true);
			}

			$count++;
		}
		debug('Re-saved ' . number_format($count) . ' Paypal Payments');
	}
	
	public function ipn() {
		PaypalIpn::log('Received IPN');
		$post = $_POST;

		if (!PaypalIpn::isStatusCompleted()) {
			exit();
		}

		PaypalIpn::log('Payment has status "Completed". Saving');
		// check the payment_status is Completed
		// check that txn_id has not been previously processed
		// check that receiver_email is your Primary PayPal email
		// check that payment_amount/payment_currency are correct
		// process payment
		PaypalIpn::log('Connection verified. Saving.');
		$this->_savePost($post);

		$this->layout = 'ajax';
		exit();
	}
	
	function _savePost($post = null, $test = false) {
		PaypalIpn::log('Saving POST value');
		if ($test) {
			PaypalIpn::log('TESTING ONLY');
		}
		if($post['txn_id'] != '') {
			//Checks for existing transaction
			PaypalIpn::log('Transaction ID: ' . $post['txn_id']);
			$paypalPayment = $this->PaypalPayment->findByTxnId($post['txn_id']);
			
			if (!empty($paypalPayment)) {
				PaypalIpn::log('Updating existing payment ID: ' . $paypalPayment['PaypalPayment']['id']);
				$post['id'] = $paypalPayment['PaypalPayment']['id'];
			}
				
			if (!$test && !$this->PaypalPayment->save($post)) {
				PaypalIpn::log('Error saving info');
			} else {
				if ($test) {
					$this->PaypalPayment->id = $post['id'];
				}
				
				PaypalIpn::log('Successfully saved Paypal IPN info');
				PaypalIpn::log('Finding Invoice ID: ' . $this->PaypalPayment->id);
				
				//Makes sure any missing info is copied from PayPal
				$this->PaypalPayment->syncInvoice($this->PaypalPayment->id);
				
				$invoice = $this->PaypalPayment->Invoice->find('first', [
					'link' => ['Shop.PaypalPayment'],
					'conditions' => ['PaypalPayment.id' => $this->PaypalPayment->id]
				]);
				if (empty($invoice)) {
					PaypalIpn::log('Could not load Invoice to send notification email');
				} else {
					PaypalIpn::log('Invoice Found. Sending Email');
					PaypalIpn::log('Invoice ID: ' . $invoice['Invoice']['id']);
					
					App::uses('InvoiceEmail', 'Shop.Network/Email');
					if (defined('COMPANY_ADMIN_EMAILS')) {
						$InvoiceEmail = new InvoiceEmail();
						PaypalIpn::log('Created InvoiceEmail Object');
						if ($InvoiceEmail->sendAdminPaid($invoice)) {
							PaypalIpn::log('Sent notification email to admins: ' . COMPANY_ADMIN_EMAILS);
						} else {
							PaypalIpn::log('Error sending notification email');
						}
					} else {
						PaypalIpn::log('No admin emails set, so nothing is being sent');
					}
					PaypalIpn::log('Finished Email');
				}
			}
		} else {
			PaypalIpn::log('TxnID was blank. Skipping.');
		}
	}
}