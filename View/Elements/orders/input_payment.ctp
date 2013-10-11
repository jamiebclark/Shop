<?php 
$class = 'form-horizontal alert ';
$class .= $this->Html->value('Invoice.paid') ? 'alert-success' : 'alert-warning';
?>
<div class="<?php echo $class;?>">
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
			'label' => 'Amount',
			'prepend' => '$',
			'step' => 'any',
			'placeholder' => '0.00',
		));
	}

	echo $this->Form->hidden('Invoice.paid_email');
	
	echo $this->FormLayout->inputDatetime('Invoice.paid', array(
		'control' => array('today', 'clear'), 
		'class' => 'datetime', 
		'label' => 'Date Paid',
		'blank' => true,
	));
	echo $this->Form->input('Invoice.invoice_payment_method_id', array('label' => 'Pay Method'));

	$paidEmailOptions = array(
		'type' => 'checkbox',
		'label' => 'Send Confirmation Email',
		'helpBlock' => 'Send the customer an email saying their order has been paid',
		'checked' => !$this->Html->value('Invoice.paid') && !$this->Html->value('Invoice.paid_email'),
	);
	if ($this->Html->value('Invoice.paid_email')) {
		$paidEmailOptions['helpBlock'] .= ' <em>Previously sent ';
		$paidEmailOptions['helpBlock'] .= $this->Calendar->niceShort($this->Html->value('Order.paid_email'));
		$paidEmailOptions['helpBlock'] .= '</em>';
	}
	echo $this->Form->input('Invoice.send_paid_email', $paidEmailOptions);
?></div>