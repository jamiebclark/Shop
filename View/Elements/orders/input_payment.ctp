<?php
echo $this->Form->hidden('Invoice.id');
if ($this->Html->value('Invoice.id')) {
	echo $this->FormLayout->fakeInput(
		$this->Html->tag('strong', $this->Html->value('Invoice.id')), 
		array('label' => 'Invoice #')
	);
}
if (!isset($amt) || $amt !== false) {
	echo $this->Form->input('Invoice.amt', array(
		'label' => 'Amount: $'
	));
}

echo $this->DateBuild->input('Invoice.paid', array(
	'control' => array('today', 'clear'), 
	'class' => 'datetime', 
	'label' => 'Date Paid',
	'blank' => true,
));

echo $this->Form->input('Invoice.invoice_payment_method_id', array('label' => 'Pay Method'));
?>