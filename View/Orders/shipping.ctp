<?php  echo $this->Form->create('Order'); ?>
<h2>Shipping</h2>
<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">Shipping</div>
			<div class="panel-body">
				<?php  
				echo $this->Form->hidden('id');
				echo $this->Form->hidden('Invoice.id');

				echo $this->FormLayout->inputRow(array('first_name', 'last_name'));
				echo $this->AddressBookForm->inputAddress('Order');

				echo $this->Layout->toggle('', $this->AddressBookForm->inputAddress('Invoice', array(
						'before' => '<h4>Billing Information</h4>' . $this->FormLayout->inputRow(array('Invoice.first_name', 'Invoice.last_name'))
					)),
					'Billing information is same as Shipping', 
					array('name' => 'same_billing')
				);
				echo $this->FormLayout->inputRows(array(
					array(
						'email' => array(
							'type' => 'email',
							'after' => '<span class="help-block">We can keep you up to date on when your order has shipped</span>',
							'placeholder' => 'yourname@email.com',
						),
					),
					array(
						'phone' => array(
							'type' => 'phone',
							'after' => '<span class="help-block">We need a phone number to add your order to Fed-Ex</span>',
							'placeholder' => '(xxx) xxx-xxxx',
						),
					)
				));
			?></div>
		</div>
	</div>
	<div class="col-sm-6"><?php
		echo $this->element('orders/cart', array(
			'condensed' => true,
			'form' => false,
			'links' => false,
			'images' => false,
			'title' => 'Cart Contents',
			'titleUrl' => true,
		));
	?></div>
</div>
<?php
echo $this->FormLayout->buttons(array(
	'Complete Order' => array(
		'class' => 'btn-primary btn-lg pull-right'
	),
	'Edit Cart' => array(
		'url' => array('action' => 'view', $order['Order']['id']),
		'class' => 'prev',
	),
));
echo $this->Form->end();