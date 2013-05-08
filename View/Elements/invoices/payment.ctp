<?php
$this->Asset->css('development');

if (empty($this->Invoice)) {
	App::import('Helper', 'InvoiceHelper');
	$this->Invoice = new InvoiceHelper();
}

$paypalButton = $this->Invoice->paypalForm(
	$Invoice, 
	$this->Form->button(
		$this->Html->image('btn/paypal.png'),
		array('img' => false)
	)
);
$payments = array();
if (empty($paypal) || $paypal !== false) {
	$payments[] = array(
		'title' => 'Pay with Credit Card / Paypal',
		'action' => $paypalButton,
		'description' => 'Using PayPal, you can pay for your order using a major credit card or your PayPal account. This method will 	generally ship faster. Note: a PayPal account is NOT necessary to use their credit card payment ',
	);
}

if (empty($check) || $check !== false) {
	$payments[] = array(
		'title' => 'Pay by Check',
		'action' => $this->Html->div('mailingAddress', $this->Html->tag('h4', 'Mail to:') . $this->Invoice->getMailingAddress()),
		'description' => $this->Invoice->checkPaymentSteps($Invoice),
	);
}

echo $this->Html->div('invoicePayments');
foreach ($payments as $payment) {
	
	echo $this->Html->div('invoicePayment');
	echo $this->Html->tag('h3', $payment['title']);
	echo $this->Html->div('invoicePaymentWrapper');
	echo $this->Html->div('paymentInfo', $payment['description']);
	echo $this->Html->div('paymentAction', $payment['action']);
	echo "</div>\n";
	echo "</div>\n";
}
echo "</div>\n";
?>