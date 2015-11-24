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

				echo $this->FormLayout->inputRow(['first_name', 'last_name']);
				echo $this->AddressBookForm->inputAddress('Order');

				echo $this->Layout->toggle('', $this->AddressBookForm->inputAddress('Invoice', array(
						'before' => '<h4>Billing Information</h4>' . $this->FormLayout->inputRow(['Invoice.first_name', 'Invoice.last_name'])
					)),
					'Billing information is same as Shipping', 
					['name' => 'same_billing']
				);
				echo $this->FormLayout->inputRows(array(
					[
						'email' => [
							'type' => 'email',
							'after' => '<span class="help-block">We can keep you up to date on when your order has shipped</span>',
							'placeholder' => 'yourname@email.com',
						],
					],
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
		<?php
			echo $this->FormLayout->buttons([
				'Complete Order <i class="fa fa-check"></i>' => [
					'class' => 'btn-primary btn-lg pull-right'
				],
				'<i class="fa fa-arrow-left"></i> Edit Cart' => [
					'url' => ['action' => 'view', $order['Order']['id']],
					'escape' => false,
					'class' => 'prev',
				],
			]);
		?>
	</div>
	<div class="col-sm-6"><?php
		echo $this->element('orders/cart', [
			'condensed' => true,
			'form' => false,
			'links' => false,
			'images' => false,
			'title' => 'Cart Contents',
			'titleUrl' => true,
		]);
	?></div>
</div>
<?php echo $this->Form->end(); ?>