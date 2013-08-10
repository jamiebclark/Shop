<?php
$archived = !empty($order['Order']['archived']);
//$this->set('back_link', array('Back to Store Orders', array('action' => 'index')));

echo $this->Layout->defaultHeader($order['Order']['id'], array(
	array('Print Invoice', 
		array('action' => 'invoice', $order['Order']['id']) + Prefix::reset(), 
		array('target' => '_blank')
	)), array(
		'title' => $order['Order']['title'],
	)
);

echo $this->Form->create('Order', array('class' => 'form-horizontal'));
echo $this->Form->hidden('id');

$info = array(
	'Shipping Address' => $this->AddressBook->location($order['Order'], array(
		'beforeField' => array('name')
	)),
	'Billing Address' => $this->AddressBook->location($order['Invoice'], array(
		'beforeField' => array('name')
	)),
	'Email' => $this->AddressBook->email($order['Order']['email']),
	'Phone' => $this->AddressBook->phone($order['Order']['phone']),
	'Created' => $this->Calendar->niceShort($order['Order']['created']),
	'Last Updated' => $this->Calendar->niceShort($order['Order']['modified']),
	'Paid' => $this->Order->paid($order),
	'Shipped' => $this->Order->shipped($order),
	'Track' => $this->Order->tracking($order)
);

?>
<div class="row">
	<div class="span6">
		<h3>Info</h3>
		<?php 		
			echo $this->Layout->infoTable($info); 
		?>
	</div>

	<div class="span6">
		<h3>Shipping</h3>
			<?php echo $this->element('orders/input_shipping'); ?>
		<h3>Payment</h3>
			<?php echo $this->element('orders/input_payment'); ?>
		<?php echo $this->Form->submit('Update'); ?>
	</div>
</div>
<?php echo $this->Form->end(); ?>

<div class="row">
	<div class="span10">
		<h2>Order Contents</h2>
		<?php
		echo $this->element('orders/cart', array('shipping' => true, 'delete' => true));
		if (empty($order['Order']['archived'])) {
			echo $this->Form->create('OrderProduct', array('action' => 'add', 'type' => 'GET'));
			echo $this->Layout->fieldset('Add a product to the order');
			echo $this->Form->inputs(array(
				'fieldset' => false,
				'order_id' => array('type' => 'hidden', 'value' => $order['Order']['id']),
				'product_id' => array('options' => $products),
			));
			echo $this->FormLayout->submit('Add Product to order');
			echo "</fieldset>\n";
			echo $this->Form->end();
		}

		$info = array();
		if ($canceled) {
			$info[] = 'This order has been canceled';
		}
		if ($archived) {
			$info[] = 'This order has been Archived, usually because it has already been paid or shipped. If changes are made to existing 
			handling or shipping charges in the system, no changes will be made to this order';
		} else {
			$autoFields = array(
				'auto_handling' => array(
					'Auto-Handling',
					'It will be updated as changes are made to handling charges',
					'Only updates made to this form will be reflected in handling charges',
				),
				'auto_shipping' => array(
					'Auto-Shipping',
					'It will be updated as changes are made to shipping charges',
					'Only updates made to this form will be reflected in shipping charges',
				),
				'auto_price' => array(
					'Auto-Pricing',
					'All prices are set by the products database',
					'You can change the default prices of items',
				),
			);
			foreach ($autoFields as $field => $fieldInfo) {
				$on = $order['Order'][$field];
				$out = $this->Html->tag('span', $fieldInfo[0] . ' is ' . ($on ? 'ON' : 'OFF'), array(
					'class' => 'label label-' . ($on ? 'success' : 'error'),
				)) . ' ';
				$out .= $on ? $fieldInfo[1] : $fieldInfo[2];
				$info[] = $out;
			}
		}
		echo $this->Layout->fieldset('Order Automatic Updating', $this->Layout->menu($info));
	?>
	</div>
</div>