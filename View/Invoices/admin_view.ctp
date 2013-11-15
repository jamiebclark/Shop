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