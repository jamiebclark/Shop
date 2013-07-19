<?php
App::uses('InvoiceEmail', 'Shop.Network/Email');

class PaypalPaymentsController extends ShopAppController {
	public $name = 'PaypalPayments';
	
	//public $components = array('Shop.InvoiceEmail');
	
	//Log Variables
	private $_logFile;
	private $_logResource;
	private $_logFailed = false;

	function ipn() {
		$this->_log('Received IPN');

		// PHP 4.1
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';

		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		// post back to PayPal system to validate
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

		$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
		if (!$fp) {
			// HTTP ERROR
			$this->_log('Could not connect to Paypal Socket. Aborting.');
		} else {
			fputs ($fp, $header . $req);
			while (!feof($fp)) {
				$res = fgets ($fp, 1024);
				if (strcmp ($res, "VERIFIED") == 0 && $_POST['payment_status'] = 'Completed') {
				// check the payment_status is Completed
				// check that txn_id has not been previously processed
				// check that receiver_email is your Primary PayPal email
				// check that payment_amount/payment_currency are correct
				// process payment
					if($_POST['txn_id'] != '') {
						//Checks for existing transaction
						$paypalPayment = $this->PaypalPayment->findByTxnId($_POST['txn_id']);
						
						if (!empty($paypalPayment)) {
							$this->_log('Updating existing payment ID: ' . $paypalPayment['PaypalPayment']['id']);
							$_POST['id'] = $paypalPayment['PaypalPayment']['id'];
						}
							
						if (!$this->PaypalPayment->save($_POST)) {
							$this->_log('Error saving info');
						} else {
							$this->_log('Successfully saved Paypal IPN info');
							$invoice = $this->PaypalPayment->Invoice->find('first', array(
								'link' => array('Shop.PaypalPayment'),
								'conditions' => array(
									'PaypalPayment.id' => $this->PaypalPayment->id,
								)
							));
							if (empty($invoice)) {
								$this->_log('Could not load Invoice to send notification email');
							} else {
								if (InvoiceEmail::sendAdminPaid($invoice)) {
									$this->_log('Sent notification email to admin');
								} else {
									$this->_log('Error sending notification email');
								}
							}
						}
					} else {
						$this->_log('TxnID was blank. Skipping.');
					}
				} else if (strcmp ($res, "INVALID") == 0) {
					// log for manual investigation
					$this->_log('Invalid Socket Connection. Aborting.');
				}
			}
			fclose ($fp);
		}
		$this->_logClose();
		$this->layout = 'ajax';
		exit();
	}
	
	function admin_fix() {
		$paypalPayments = $this->PaypalPayment->find('all', array(
			'fields' => '*',
			'link' => array('Shop.Invoice')
		));
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
	
	function _logOpen() {
		$dir = '/home/souper/page_logs/ipn/';
		$this->_logFile = $dir . date('Y-m-d').'.log';
		$this->_logResource = fopen($this->_logFile, 'a');
		if (!$this->_logResource) {
			$this->_logFailed = true;
			return false;
		}
		$this->_log('******* Opening Log ********');
		return true;
	}
	
	function _log($msg) {
		if (empty($this->_logResource) && !$this->_logFailed) {
			$this->_logOpen();
		}
		if ($this->_logResource) {
			return fwrite($this->_logResource,date('c').' '.str_replace(array("\r","\t","\n"),' ',$msg)."\n");
		} else {
			return false;
		}
	}
	
	function _logClose() {
		if (!empty($this->_logResource)) {
			$this->_log('******* Closing Log ********');
			fclose($this->_logResource);
		}
	}

}
