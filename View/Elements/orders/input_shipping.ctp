<?php 
$class = 'panel ';
$class .= $this->Html->value('Order.shipped') ? 'panel-success' : 'panel-warning';
?>
<div class="<?php echo $class;?>">
	<div class="panel-heading">Shipping</div>
	<div class="panel-body">
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
				'beforeInput' => '<div class="input-group"><span class="input-group-addon">$</span>',
				'afterInput' => '</div>',
				'placeholder' => '0.00',
			),
		));
		
		$shippedEmailOptions = array(
			'type' => 'checkbox',
			'class' => 'checkbox',
			'label' => array('text' => 'Send Confirmation Email', 'class' => 'control-label'),
			'checked' => !$this->Html->value('Order.shipped') && !$this->Html->value('Order.shipped_email'),
		);
		$helpBlock = 'Send the customer an email saying their order has shipped. ';
		if ($this->Html->value('Order.shipped_email')) {
			$helpBlock .= ' <em>Previously sent ';
			$helpBlock .= $this->Calendar->niceShort($this->Html->value('Order.shipped_email'));
			$helpBlock .= '</em>';
		}
		$shippedEmailOptions['afterInput'] = '<span class="help-block">' . $helpBlock . '</span>';
		echo $this->Form->input('send_shipped_email', $shippedEmailOptions);
		//debug($this->request->data);
		?>
	</div>
</div>
