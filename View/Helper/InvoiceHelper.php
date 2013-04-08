<?php
class InvoiceHelper extends AppHelper {
	var $name = 'Invoice';
	var $helpers = array(
		'Layout.Asset',		'Html',		'Form', 		'Layout.Layout', 		'Shop.PaypalForm', 		'Layout.FormLayout'	);
	
	var $companyName = COMPANY_NAME;
	var $mailingAddress = COMPANY_ADDRESS;
	
	function beforeRender($viewFile) {
		$this->Asset->css('development');
		parent::beforeRender($viewFile);
	}		
	
	function relatedLink($Result, $options = array()) {
		$models = array(
			'Donation' => 'Online Donation', 
			'Order' => 'Online Store Order', 
			'NsaMember' => 'NSA Membership',
			'BowlathonPledge' => 'Bowlathon Pledge Payment',
			'DonorCardOrder' => 'Donor Card Order',
		);
		$list = array();
		foreach ($models as $model => $label) {
			if (is_numeric($model)) {
				$model = $label;
			}
			if (!empty($Result[$model]['id'])) {
				$list[] = $this->Html->link(
					$label . ' #' . $Result[$model]['id'],
					array(
						'controller' => Inflector::tableize($model),
						'action' => 'view',
						$Result[$model]['id'],
						'staff' => true,
					),
					$options
				);
			}
		} 
		$title = '';
		if (empty($list)) {
			if (!empty($Result['Invoice']['item_name'])) {
				$title = $Result['Invoice']['item_name'];
				if (!empty($Result['Invoice']['item_number'])) {
					$title .= '# ' . $Result['Invoice']['item_number'];
				}
			}
		} else {
			$title = implode(', ', $list);
		}
		return $title;
	}
	
	function paypalForm($Invoice, $content = null, $options = array()) {
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
		
		if (!empty($Invoice['title'])) {
			$settings['item_name'] = $Invoice['title'];
		} else if (!empty($Invoice['item_name'])) {
			$settings['item_name'] = $Invoice['item_name'];
		} else {
			$settings['item_name'] = 'Invoice';
		}
		$settings['item_number'] = !empty($Invoice['item_number']) ? $Invoice['item_number'] : $Invoice['id'];
		
		//Recurring payments
		$recurUnit = 'M';
		
		if (!empty($Invoice['recur'])) {
			unset($settings['amt']);
			$settings['cmd'] = '_xclick-subscriptions';
			$settings['item_name'] = 'Subscription Payment';
			$settings['a3'] = $Invoice['amt'];
			$settings['p3'] = 1;					//Once
			$settings['t3'] = $recurUnit;			//Every Month
			$settings['src'] = 1;					//Do not recur when it completes the cycle
			$settings['srt'] = $Invoice['recur'];	//Repeat this many times
		}
		
		$optionSettings = Param::keyCheck($options, 'settings', true, array());
		$settings = array_merge($settings, $optionSettings);

		foreach ($cols as $paypalCol => $dbCol) {
			if (is_numeric($paypalCol)) {
				$paypalCol = $dbCol;
			}
			$settings[$paypalCol] = $Invoice[$dbCol];
		}
		$output = '';
		$output .= $this->PaypalForm->create($options);
		$output .= $this->PaypalForm->inputSettings($settings);
		if (!empty($content)) {
			$output .=  $content . $this->PaypalForm->end();
		} 
		return $output;
	}
	
	function paypalFormClose() {
		return $this->PaypalForm->end();
	}
	
	function checkPaymentSteps($Invoice) {
		$memo = 'Invoice #' . $Invoice['id'];
		$steps = array(
			'Make check payable to <strong>' . $this->companyName . '</strong>',
			'Payment should be the amount of <strong>$' . number_format($Invoice['amt'],2) . '</strong>',
			'Be sure to write <strong>' . $memo . '</strong> in the "Memo" section',
			'If possible, attach a ' . $this->Html->link(
				'printed copy of your invoice', array(
					'controller' => 'invoices',
					'action' => 'view',
					$Invoice['id'],
				) + Prefix::reset(), 
				array(
					'target' => '_blank'
				)
			),
		);
		
		$output = $this->Html->div('invoiceCheckPaymentSteps');
		$output .= '<ol><li>' . implode('</li><li>', $steps) . '</li></ol>';
		$output .= "</div>\n";
		return $output;
	}
	
	function getMailingAddress() {
		return $this->mailingAddress;
	}
	
	function paymentForm($Invoice, $options = array()) {
		$options = array_merge(array(
			'paypal' => true,
			'check' => true,
		), $options);
		extract($options);
		
		$paypalButton = $this->paypalForm(
			$Invoice, 
			$this->Form->button(
				$this->Html->image('btn/paypal.png'),
				array(
					'img' => false,
					'style' => 'width: auto;',
				)
			)
		);
		$payments = array();
		
		if (!empty($paypal)) {
			$payments[] = array(
				'Pay with Credit Card / Paypal',
				$paypalButton,
				'Using PayPal, you can pay for your order using a major credit card or your PayPal account. This method will 	generally ship faster. Note: a PayPal account is NOT necessary to use their credit card payment ',
			);
		}
		if (!empty($check)) {
			$payments[] = array(
				'Pay by Check',
				$this->Html->div(
					'mailingAddress', 
					$this->Html->tag('h4', 'Mail to:') . $this->getMailingAddress()
				),
				$this->checkPaymentSteps($Invoice),
			);
		}
		//Begin Output
		$out = '';
		foreach ($payments as $k => $payment) {
			list($title, $action, $info) = $payment + array(null, null, null);
			$out .= $this->Html->div('invoice-payment',
				$this->Html->tag('h3', $title) . $this->Html->div('invoice-payment-wrap',
					$this->Html->div('action', $action) . $this->Html->div('info', $info)
				)
			);
		}
		return $this->Html->div('invoice-payments', $out);;
	}
}