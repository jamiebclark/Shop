<?php 
$class = 'panel ';
$class .= $this->Html->value('Invoice.paid') ? 'panel-success' : 'panel-warning';
?>
<div class="<?php echo $class;?>">
	<div class="panel-heading"><span class="panel-title">Payment</span></div>
	<div class="panel-body">
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
			'beforeInput' => '<div class="input-group"><span class="input-group-addon">$</span>',
			'afterInput' => '</div>',
			'step' => 'any',
			'placeholder' => '0.00',
		));
	}

	echo $this->Form->hidden('Invoice.paid_email');
	
	echo $this->FormLayout->inputDatetime('Invoice.paid', array(
		'control' => array('today', 'clear'), 
		'class' => 'form-control datetime', 
		'label' => 'Date Paid',
		'blank' => true,
	));
	echo $this->Form->input('Invoice.invoice_payment_method_id', array('label' => 'Pay Method'));

	$helpBlock = 'Send the customer an email saying their order has been paid. ';

	$paidEmailOptions = array(
		'type' => 'checkbox',
		'class' => 'checkbox',
		'label' => array('text' => 'Send Confirmation Email', 'class' => 'control-label'),
		'checked' => !$this->Html->value('Invoice.paid') && !$this->Html->value('Invoice.paid_email'),
	);
	if ($this->Html->value('Invoice.paid_email')) {
		$helpBlock .= ' <em>Previously sent ';
		$helpBlock .= $this->Calendar->niceShort($this->Html->value('Order.paid_email'));
		$helpBlock .= '</em>';
	}
	$paidEmailOptions['afterInput'] = '<span class="help-block">' . $helpBlock . '</span>';
	echo $this->Form->input('Invoice.send_paid_email', $paidEmailOptions);
	?>
	</div>
</div>