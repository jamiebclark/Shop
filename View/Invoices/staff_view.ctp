<?php
echo $this->element('invoices/crumbs', array(
	'crumbs' => 'Invoice #' . $invoice['Invoice']['id']
));

echo $this->Html->tag('h1', 'Invoice #' . $invoice['Invoice']['id']);
echo $this->Layout->headerMenu(array(
	array('Edit Invoice', array('action' => 'edit',$invoice['Invoice']['id'])),
	array('Copy Payment Info', array('action' => 'copy_payment', $invoice['Invoice']['id'])),
	array('Resend Notify Email', array('action' => 'resend_email', $invoice['Invoice']['id'])),
));
echo $this->element('invoices/info');
?>