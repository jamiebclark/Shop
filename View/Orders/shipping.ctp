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
		echo $this->FormLayout->inputRow(array('first_name', 'last_name'));
		echo $this->AddressBookForm->inputAddress('Order', array('placeholder' => true));
		
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
		));
	?>
		<fieldset>
			<legend>Billing Information</legend>
		<?php
<<<<<<< HEAD
=======
		echo $this->Layout->toggle('', $this->AddressBookForm->inputAddress('Invoice', array(
				'placeholder' => true,
				'before' => $this->FormLayout->inputRow(array('Invoice.first_name', 'Invoice.last_name'))
			)),
			'Billing information is same as Shipping', 
			array('name' => 'same_billing')
		);
		
>>>>>>> ef478f205f59f09d8287e867b839528a1b628e12
		echo $this->Layout->toggle('', $this->FormLayout->inputRows(array(
				array('Invoice.addline1' => array('label' => 'Street Address')),
				array('Invoice.addline2' => array('label' => 'Apt. #')),
				array('Invoice.city', 'Invoice.state', 'Invoice.zip'),
				array('Invoice.country' => array('default' => 'US'))
			), array(
				'placeholder' => true,
				'before' => $this->FormLayout->inputRow(array(
					'Invoice.first_name', 
					'Invoice.last_name'
				))
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