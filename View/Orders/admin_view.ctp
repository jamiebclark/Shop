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

<h2>Order Contents</h2>
<?php
echo $this->element('orders/cart', array(
	'shipping' => true,
	'delete' => true,
));
/*
$this->Table->reset();
foreach ($order['OrderProduct'] as $orderProduct) {
	$productUrl = array(
		'controller' => 'order_products',
		'action' => 'edit',
		$orderProduct['id']
	);
	$hasParent = !empty($orderProduct['parent_id']);
	
	if (!empty($orderProduct['Product']['id'])) {
		$productDisplay = $this->Html->link($orderProduct['title'], $productUrl);
	} else {
		$productDisplay = $orderProduct['title'];
	}
	$actions = $archived ? array() : array('delete');
	
	if ($hasParent) {
		$price = '---';
		$shipping = '---';
		$subTotal = '---';
	} else {
		$price = $this->DisplayText->cash($orderProduct['price']);
		$shipping = $this->DisplayText->cash($orderProduct['shipping']);
		$subTotal = $this->DisplayText->cash($orderProduct['sub_total']);
	}
	
	$this->Table->cells(array(
		array($this->Layout->actionMenu($actions, array('url' => $productUrl))),
		array(
			$this->CatalogItem->thumb($orderProduct['Product'], array('url' => $productUrl, 'dir' => 'thumb')), 
			null, 
			null, 
			null, 
			array(
				'width' => 40,
				'class' => $hasParent ? 'hasParent' : null,
			)
		),
		array($productDisplay, 'Product'),
		array($price, 'Price', null, null, array('class' => 'figure')),
		array(number_format($orderProduct['quantity']), 'Quantity', null, null, array('class' => 'figure')),
		array($shipping, 'Shipping', null, null, array('class' => 'figure')),
		array($subTotal, 'Sub Total', null, null, array('class' => 'figure subTotal')),
	), true);
}
$colspan = 6;
$this->Table->cells(array(
	array('Sub-Total', null, null, null, array('colspan' => $colspan, 'class' => 'total top')),
	array($this->DisplayText->cash($order['Order']['sub_total']), null, null, null, array('class' => 'total top figure')),
), true);

$this->Table->cells(array(
	array('Shipping', null, null, null, array('colspan' => $colspan, 'class' => 'total ')),
	array($this->DisplayText->cash($order['Order']['shipping']), null, null, null, array('class' => 'total figure')),
), true);

$this->Table->cells(array(
	array('Handling', null, null, null, array('colspan' => $colspan, 'class' => 'total')),
	array($this->DisplayText->cash($order['Order']['handling']), null, null, null, array('class' => 'total figure')),
), true);
$this->Table->cells(array(
	array('Promos', null, null, null, array('colspan' => $colspan, 'class' => 'total')),
	array($this->DisplayText->cash($order['Order']['promo_discount']), null, null, null, array('class' => 'total figure')),
), true);

$this->Table->cells(array(
	array('Total', null, null, null, array('colspan' => $colspan, 'class' => 'total grandTotal')),
	array($this->DisplayText->cash($order['Order']['total']), null, null, null, array('class' => 'total grandTotal figure')),
), true);
echo $this->Table->output(array('class' => 'orderList'));
*/

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
if ($canceled) 
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