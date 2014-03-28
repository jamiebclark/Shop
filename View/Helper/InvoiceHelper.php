<?php
App::uses('ModelViewHelper', 'Layout.View/Helper');
class InvoiceHelper extends ModelViewHelper {
	var $name = 'Invoice';
	var $modelPlugin = 'Shop';
	
	var $helpers = array(
		'Form', 
		'Html',		'Layout.AddressBook',
		'Layout.Asset',
		'Layout.Calendar',
		'Layout.DisplayText',
		'Layout.FormLayout',
		'Layout.Layout', 		'Shop.PaypalForm', 	);
	
	var $companyName = COMPANY_NAME;
	var $mailingAddress = COMPANY_ADDRESS;
	
	function address($invoice) {
		$invoice = $this->_getResult($invoice);
		return $this->AddressBook->address($invoice, array(
			'beforeField' => array(array('first_name', 'last_name')),
		));
	}

	public function paid($result) {
		$invoice = $this->_getResult($result);
		if ($invoice['paid']) {
			$paid = 'Paid ' . $this->Calendar->niceShort($invoice['paid'], array('time' => false));
			if (!empty($result['InvoicePaymentMethod'])) {
				$paid .= " (<em>{$result['InvoicePaymentMethod']['title']}</em>)";
			}
			return $paid;
		} else {
			return false;
		}
	}
	
	public function amount($invoice) {
		$invoice = $this->_getResult($invoice);
		$out = '$' . number_format($invoice['amt'], 2);
		if (!empty($invoice['recur'])) {
			$out .= ' every month for ' . number_format($invoice['recur']) . ' months';
		}
		return $out;	
	}
	
	public function title($invoice, $options = array()) {
		$invoice = $this->_getResult($invoice);
		if (empty($options['text'])) {
			$options['text'] = 'Invoice #' . $invoice['id'];
		}
		return parent::title($invoice, $options);
	}
	
	function relatedTitle($invoice) {
		$invoice = $this->_getResult($invoice);
		return "{$invoice['model_title']} #{$invoice['model_id']}";
	}
	
	function relatedLink($invoice, $options = array()) {
		$invoice = $this->_getResult($invoice);
		if (empty($invoice['model'])) {
			return '';
		}
		list($plugin, $model) = pluginSplit($invoice['model']);
		$url = array(
				'controller' => Inflector::tableize($model),
				'action' => 'view',
				$invoice['model_id'],
				'plugin' => strtolower($plugin),
			);
		if (!empty($options['public'])) {
			$url['admin'] = false;
			unset($options['public']);
		}
		return $this->Html->link($this->relatedTitle($invoice), $url, $options);
	}

	function paypalForm($invoice, $content = null, $options = array()) {
		$cols = array(
			'first_name',
			'last_name',
			'address1' => 'addline1',
			'address2' => 'addline2',
			'city',
			'state',
			'zip',
			'country',
			'day_phone' => 'phone',
			'night_phone' => 'phone',
			'email',
			'amount' => 'amt',
			'invoice' => 'id',
		);
		$here = Router::url(null, true);
		$settings = array(
		//	'cmd' => '_cart',
			'return' => $here,
			'cancel_return' => $here,
		);
		
		if (!empty($invoice['model_title'])) {
			$settings['item_name'] = $this->relatedTitle($invoice);
			$settings['item_number'] = $invoice['model_id'];
		} else {
			$settings['item_name'] = !empty($invoice['title']) ? $invoice['title'] : 'Invoice';
			$settings['item_number'] = $invoice['id'];
		}
		
		//Recurring payments
		$recurUnit = 'M';
		
		if (!empty($invoice['recur'])) {
			unset($settings['amt']);
			$settings['cmd'] = '_xclick-subscriptions';
			$settings['model_title'] = 'Subscription Payment';
			$settings['a3'] = $invoice['amt'];
			$settings['p3'] = 1;					//Once
			$settings['t3'] = $recurUnit;			//Every Month
			$settings['src'] = 1;					//Do not recur when it completes the cycle
			$settings['srt'] = $invoice['recur'];	//Repeat this many times
		}
		
		$optionSettings = Param::keyCheck($options, 'settings', true, array());
		$settings = array_merge($settings, $optionSettings);

		foreach ($cols as $paypalCol => $dbCol) {
			if (is_numeric($paypalCol)) {
				$paypalCol = $dbCol;
			}
			$settings[$paypalCol] = $invoice[$dbCol];
		}
		$out = '';
		$out .= $this->PaypalForm->create($options);
		$out .= $this->PaypalForm->inputSettings($settings);
		if (!empty($content)) {
			$out .=  $content . $this->PaypalForm->end();
		} 
		return $out;
	}
	
	function paypalFormClose() {
		return $this->PaypalForm->end();
	}
	
	function checkPaymentSteps($invoice) {
		$memo = 'Invoice #' . $invoice['id'];
		$steps = array(
			'Make check payable to <strong>' . $this->companyName . '</strong>',
			'Payment should be the amount of <strong>$' . number_format($invoice['amt'],2) . '</strong>',
			'Be sure to write <strong>' . $memo . '</strong> in the "Memo" section',
			'If possible, attach a ' . $this->Html->link(
				'printed copy of your invoice', array(
					'controller' => 'invoices',
					'action' => 'view',
					'plugin' => 'shop',
					$invoice['id'],
				) + Prefix::reset(), 
				array('target' => '_blank')
			),
		);
		
		return $this->Html->tag('ol', '<li>' . implode('</li><li>', $steps) . '</li>', array(
			'class' => 'invoice-check-payment-steps',
		));
	}
	
	function getMailingAddress() {
		return $this->mailingAddress;
	}
	
	function paymentForm($invoice, $options = array()) {
		$options = array_merge(array(
			'paypal' => true,
			'check' => true,
		), $options);
		extract($options);
		
		$paypalForm = $this->paypalForm(
			$invoice, 
			$this->Form->submit ('Pay with Credit Card or PayPal', array('class' => 'btn btn-lg btn-primary'))
		);
		$payments = array();
		
		if (!empty($paypal)) {
			$payments[] = array(
				'Pay with Credit Card / PayPal',
				$paypalForm,
				'Using PayPal, you can pay for your order using a major credit card or your PayPal account. This method will 	generally ship faster. Note: a PayPal account is NOT necessary to use their credit card payment ',
			);
		}
		
		if (!empty($check)) {
			$payments[] = array(
				'Pay by Check',
				$this->Html->div(
					'mailing-address', 
					$this->Html->tag('h4', 'Mail to:') . $this->getMailingAddress()
				),
				$this->checkPaymentSteps($invoice),
			);
		}
		
		//Begin Output
		$out = '';
		$col = 12 / count($payments);
		foreach ($payments as $k => $payment) {
			list($title, $action, $info) = $payment + array(null, null, null);
			$out .= $this->Html->div("invoice-payment col-sm-$col",
				$this->Html->tag('h3', $title) . $this->Html->div('invoice-payment-wrap',
					$this->Html->div('action', $action) . $this->Html->div('info', $info)
				)
			);
		}
		return $this->Html->div('invoice-payments row', $out);;
	}
}