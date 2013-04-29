<?php
echo $this->Layout->defaultHeader($invoice['Invoice']['id'], array(
	array('Copy Payment Info', array('action' => 'copy_payment', $invoice['Invoice']['id'])),
	array('Resend Notify Email', array('action' => 'resend_email', $invoice['Invoice']['id'])),
));
echo $this->element('invoices/info');