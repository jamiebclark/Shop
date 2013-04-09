<?php
echo $this->element('invoices/crumbs', array(
	'crumbs' => array(
		array('Invoice #' . $this->Html->value('Invoice.id'), array('action' => 'view', $this->Html->value('Invoice.id'))),
		'Edit Invoice'
	)
));
echo $this->element('invoices/form');
?>