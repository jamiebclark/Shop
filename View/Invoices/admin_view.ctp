<?php
$menu = array(
	array('Copy Payment Info', array('action' => 'copy_payment', $invoice['Invoice']['id'])),
	array('Resend Notify Email', array('action' => 'resend_email', $invoice['Invoice']['id']))
);

if (!empty($invoice['PaypalPayment']['id'])) {
	$menu[] = array('Sync PayPal Information', array('action' => 'sync_paypal', $invoice['Invoice']['id']));
}

echo $this->Layout->defaultHeader($invoice['Invoice']['id'], $menu);
echo $this->element('invoices/info');

if (!empty($invoice['PaypalPayment']['id'])) {
	echo $this->Html->link(
		'View in PayPal',
		'https://history.paypal.com/cgi-bin/webscr?cmd=_history-details-from-hub&id=' . $invoice['PaypalPayment']['txn_id'],
		array('class' => 'btn btn-lg btn-primary', 'target' => '_blank')
	);
}