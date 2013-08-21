<?php

class PaypalPaymentsController extends ShopAppController {
	public $name = 'PaypalPayments';
	
	//public $components = array('Shop.InvoiceEmail');
	
	//Log Variables
	private $_logDir = "webroot/logs/ipn/";
	private $_logFile;
	private $_logResource;
	private $_logFailed = false;

	function beforeFilter($options = array()) {
		if ($this->_logDir[0] != '/') {
			$this->_logDir = APP . 'Plugin/Shop/' . $this->_logDir;
		}
		return parent::beforeFilter($options);
	}
	
	function admin_test($txnId = null) {
		$post = array('txn_id' => $txnId);
		$this->_savePost($post, true);
		$this->redirect(array('action' => 'logs'));
	}
	
	function admin_logs($logFile = null) {
		if (!($folder = opendir($this->_logDir))) {
			throw new Exception('Could not open directory: ' . $this->_logDir);
		}
		$logFiles = array();
		while(($file = readdir($folder)) !== false) {
			if ($file[0] == '.' || $file == 'empty')  {
				continue;
			}
			$logFiles[$file] = $file;
		}
		closedir($folder);
		krsort($logFiles);
		
		if (empty($logFile) || empty($logFiles[$logFile])) {
			$logFile = array_shift($logFiles);
		}
		$logFilePath = $this->_logDir . $logFile;
		if (is_file($logFilePath)) {
			$logFileContent = file_get_contents($logFilePath);
		}
		$this->set(compact('logFiles', 'logFile', 'logFileContent'));
	}
	
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
		$header="POST /cgi-bin/webscr HTTP/1.1\r\n";
		$header .="Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n";
		$header .="Host: www.paypal.com\r\n";
		$header .="Connection: close\r\n\r\n";
		/** Old outdated 1.0 Header
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		*/
		//$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		

		$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
		if (!$fp) {
			// HTTP ERROR
			$this->_log('Could not connect to Paypal Socket. Aborting.');
		} else {
			$this->_log('Socket Opened Successfully');
			fputs ($fp, $header . $req);
			while (!feof($fp)) {
				$res = fgets ($fp, 1024);
				$this->_log($res);
				if (strcmp ($res, "VERIFIED") == 0 && $_POST['payment_status'] = 'Completed') {
				// check the payment_status is Completed
				// check that txn_id has not been previously processed
				// check that receiver_email is your Primary PayPal email
				// check that payment_amount/payment_currency are correct
				// process payment
					$this->_savePost($_POST);
				} else if (strcmp ($res, "INVALID") == 0) {
					// log for manual investigation
					$this->_log('Invalid Socket Connection. Aborting.');
				}
			}
			$this->_log('Closing Socket');
			fclose ($fp);
		}
		$this->_logClose();
		$this->layout = 'ajax';
		exit();
	}
	
	function _savePost($post = null, $test = false) {
		$this->_log('Saving POST value');
		if ($test) {
			$this->_log('TESTING ONLY');
		}
		if($post['txn_id'] != '') {
			//Checks for existing transaction
			$this->_log('Tranaction ID: ' . $post['txn_id']);
			$paypalPayment = $this->PaypalPayment->findByTxnId($post['txn_id']);
			
			if (!empty($paypalPayment)) {
				$this->_log('Updating existing payment ID: ' . $paypalPayment['PaypalPayment']['id']);
				$post['id'] = $paypalPayment['PaypalPayment']['id'];
			}
				
			if (!$test && !$this->PaypalPayment->save($post)) {
				$this->_log('Error saving info');
			} else {
				if ($test) {
					$this->PaypalPayment->id = $post['id'];
				}
				
				$this->_log('Successfully saved Paypal IPN info');
				$this->_log('Finding Invoice ID: ' . $this->PaypalPayment->id);
				
				$invoice = $this->PaypalPayment->Invoice->find('first', array(
					'link' => array('Shop.PaypalPayment'),
					'conditions' => array('PaypalPayment.id' => $this->PaypalPayment->id)
				));
				if (empty($invoice)) {
					$this->_log('Could not load Invoice to send notification email');
				} else {
					$this->_log('Invoice Found. Sending Email');
					$this->_log('Invoice ID: ' . $invoice['Invoice']['id']);
					
					App::uses('InvoiceEmail', 'Shop.Network/Email');
					if (!empty(COMPANY_ADMIN_EMAILS)) {
						$InvoiceEmail = new InvoiceEmail();
						$this->_log('Created InvoiceEmail Object');
						if ($InvoiceEmail->sendAdminPaid($invoice)) {
							$this->_log('Sent notification email to admins: ' . COMPANY_ADMIN_EMAILS);
						} else {
							$this->_log('Error sending notification email');
						}
					} else {
						$this->_log('No Admin Emails set, so nothing is being sent');
					}
					$this->_log('Finished Email');
				}
			}
		} else {
			$this->_log('TxnID was blank. Skipping.');
		}
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
		//$dir = '/home/souper/page_logs/ipn/';
		$this->_logFile = $this->_logDir . date('Y-m-d').'.log';
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