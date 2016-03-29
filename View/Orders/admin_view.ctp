<?php
$archived = !empty($order['Order']['archived']);
//$this->set('back_link', ['Back to Store Orders', ['action' => 'index']]);

echo $this->Layout->defaultHeader($order['Order']['id'], array(
	array('Print Invoice', 
		['action' => 'invoice', $order['Order']['id']] + Prefix::reset(), 
		['target' => '_blank']
	)), [
		'title' => $order['Order']['title'],
	]
);

echo $this->Form->create('Order');
echo $this->Form->hidden('id');

$info = array(
	'Shipping Address' => $this->AddressBook->location($order['Order'], [
		'beforeField' => ['name']
	]),
	'Billing Address' => $this->AddressBook->location($order['Invoice'], [
		'beforeField' => ['name']
	]),
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
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading"><span class="panel-title">About</span></div>
			<?php echo $this->Layout->infoTable($info); ?>
		</div>
	</div>

	<div class="col-sm-6">
		<?php echo $this->element('orders/input_shipping'); ?>
		<?php echo $this->element('orders/input_payment'); ?>
		<?php echo $this->Form->submit('Update'); ?>
	</div>
</div>
<?php echo $this->Form->end(); ?>

<div class="row">
	<div class="col-sm-10">
		<h2>Order Contents</h2>
		<?php
		echo $this->element('orders/cart', ['shipping' => true, 'delete' => true]);
		if (empty($order['Order']['archived'])):
			echo $this->Form->create('OrderProduct', ['url' => ['action' => 'add'], 'type' => 'GET']);
			?>
			<fieldset>
				<legend>Add a product to the order</legend>
				<?php 
				echo $this->Form->inputs([
					'fieldset' => false,
					'order_id' => ['type' => 'hidden', 'value' => $order['Order']['id']],
					'product_id' => ['options' => $products],
				]);
				echo $this->FormLayout->submit('Add Product to order');
				?>
			</fieldset>
			<?php
			echo $this->Form->end();
		endif;

		$info = [];
		if ($canceled) {
			$info[] = 'This order has been canceled';
		}
		if ($archived) {
			$info[] = 'This order has been Archived, usually because it has already been paid or shipped. If changes are made to existing 
			handling or shipping charges in the system, no changes will be made to this order';
		} else {
			$autoFields = [
				'auto_handling' => [
					'Auto-Handling',
					'It will be updated as changes are made to handling charges',
					'Only updates made to this form will be reflected in handling charges',
				],
				'auto_shipping' => [
					'Auto-Shipping',
					'It will be updated as changes are made to shipping charges',
					'Only updates made to this form will be reflected in shipping charges',
				],
				'auto_price' => [
					'Auto-Pricing',
					'All prices are set by the products database',
					'You can change the default prices of items',
				],
			];
			foreach ($autoFields as $field => $fieldInfo) {
				$on = $order['Order'][$field];
				$out = $this->Html->tag('span', $fieldInfo[0] . ' is ' . ($on ? 'ON' : 'OFF'), array(
					'class' => 'label label-' . ($on ? 'success' : 'error'),
				)) . ' ';
				$out .= $on ? $fieldInfo[1] : $fieldInfo[2];
				$info[] = $out;
			}
		}
		?>
		<div class="panel panel-default">
			<div class="panel-heading"><span class="panel-title">Order Auto-Updating</span></div>
			<ul class="list-group">
				<?php foreach ($info as $row): ?>
					<li class="list-group-item"><?php echo $row; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>