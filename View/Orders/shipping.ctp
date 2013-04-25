<h1>Shipping</h1>
<?php  echo $this->Form->create('Order'); ?>
<div class="row">
	<div class="span7">
	<?php  
		$span = 6;
		echo $this->Form->inputs(array(
			'id' => array('type' => 'hidden'),
			'Invoice.id' => array('type' => 'hidden'),
			'fieldset' => false,
		));
		echo $this->FormLayout->addressInput(compact('span') + array(
			'placeholder' => true,
			'before' => $this->FormLayout->inputRow(array('first_name', 'last_name'), compact('span'))
		));
		
		echo $this->FormLayout->inputRows(array(
			array(
				'email' => array(
					'helpBlock' => 'We can keep you up to date on when your order has shipped',
					'placeholder' => 'yourname@email.com',
				),
			),
			array(
				'phone' => array(
					'helpBlock' => 'We need a phone number to add your order to Fed-Ex',
					'placeholder' => '(xxx) xxx-xxxx',
				),
			)
		), compact('span'));
	?>
		<fieldset>
			<legend>Billing Information</legend>
		<?php
		echo $this->FormLayout->toggle('', $this->FormLayout->inputRows(array(
				array('Invoice.addline1' => array('label' => 'Street Address')),
				array('Invoice.addline2' => array('label' => 'Apt. #')),
				array('Invoice.city', 'Invoice.state', 'Invoice.zip'),
				array('Invoice.country' => array('default' => 'US'))
			), compact('span') + array(
				'placeholder' => true,
				'before' => $this->FormLayout->inputRow(array(
					'Invoice.first_name', 
					'Invoice.last_name'
				), compact('span'))
			)), 
			'Billing information is same as Shipping', 
			array('name' => 'same_billing')
		);
		?>
		</fieldset>
	</div>
	<div class="span5">
	<?php
	echo $this->element('orders/cart', array(
		'condensed' => true,
		'form' => false,
		'links' => false,
		'images' => false,
	));
	?>
	</div>
</div>
<?php
echo $this->FormLayout->buttons(array(
	'Complete Order' => array('class' => 'btn-primary'),
	'Edit Cart' => array(
		'url' => array('action' => 'view', $order['Order']['id']),
		'class' => 'prev',
	),
));
echo $this->Form->end();