<div class="form-horizontal">
	<?php
	echo $this->FormLayout->inputDatetime('shipped', array(
		'control' => array('today', 'clear'), 
		'label' => 'Date Shipped',
		'blank' => true,
	));
	echo $this->Form->inputs(array(
		'fieldset' => false,
		'shipping_method_id' => array('label' => 'Ship Method'),
		'tracking' => array('label' => 'Tracking #', 'type' => 'text'),
		'shipped_email' => array('type' => 'hidden'),
		'shipping_cost' => array(
			'label' => 'Cost to ship',
			'step' => 'any',
			'prepend' => '$',
			'placeholder' => '0.00',
		),
	));
	
	$shippedEmailOptions = array(
		'type' => 'checkbox',
		'label' => 'Send Confirmation Email',
		'helpBlock' => 'Send the customer an email saying their order has shipped',
		'checked' => !$this->Html->value('Order.shipped_email'),
	);
	if ($this->Html->value('Order.shipped_email')) {
		$shippedEmailOptions['helpBlock'] .= ' <em>Previously sent ';
		$shippedEmailOptions['helpBlock'] .= $this->Calendar->niceShort($this->Html->value('Order.shipped_email'));
		$shippedEmailOptions['helpBlock'] .= '</em>';
	}
	echo $this->Form->input('send_shipped_email', $shippedEmailOptions);
	//debug($this->request->data);
	?>
</div>