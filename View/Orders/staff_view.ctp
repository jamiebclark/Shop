<?php

$archived = !empty($order['Order']['archived']);

echo $this->element('orders/staff_heading', array(
	'crumbs' => 'Order #'. $order['Order']['id']
));

$this->set('back_link', array('Back to Store Orders', array('action' => 'index')));


echo $this->Html->tag('h1', 'Order #' . $order['Order']['id'], array('class' => 'topTitle'));
echo $this->Layout->headerMenu(array(
	array('Edit Order', array('action' => 'edit', $order['Order']['id'])),
	array(
		'Print Invoice', 
		array('action' => 'print_invoice', $order['Order']['id']) + Prefix::reset(), 
		array('target' => '_blank')
	),
));

echo $this->Form->create('Order', array('class' => 'halfFormWidth'));
echo $this->Form->hidden('id');
echo $this->Html->div('span-12');
echo $this->Layout->fieldset('Shipping Information');
$info = array(
	'Shipping Address' => $this->Contact->location($order['Order'], array('beforeField' => array('name'))),
);
$info['Email'] = $this->Contact->email($order['Order']['email']);
$info['Phone'] = $this->Contact->phone($order['Order']['phone']);
$info['Created'] = $this->Calendar->niceShort($order['Order']['created']);
$info['Last Updated'] = $this->Calendar->niceShort($order['Order']['modified']);
echo $this->Layout->infoTable($info);
echo $this->element('orders/input_shipping');
echo $this->FormLayout->submit('Update');

echo "</fieldset>\n";
echo "</div>\n";
echo $this->Html->div('span-12 last');
echo $this->Layout->fieldset('Billing Information');
$info = array(
	'Billing Address' => $this->Contact->location($order['Invoice'], array('beforeField' => array('name')))
);
echo $this->Layout->infoTable($info);
echo $this->element('orders/input_payment');
echo $this->FormLayout->submit('Update');

echo "</fieldset>\n";
echo "</div>\n";
echo $this->Form->end();


echo "<hr/>\n";

echo $this->Html->tag('h2', 'Order Contents');


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

echo $this->Table->table(array('class' => 'orderList'));

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
if ($archived) {
	$info[] = 'This order has been Archived, usually because it has already been paid or shipped. If changes are made to existing 
	handling or shipping charges in the system, no changes will be made to this order';
} else {
	if ($order['Order']['auto_handling']) {
		$info[] = 'Auto-Handling is ON. It will be updated as changes are made to handling charges';
	} else {
		$info[] = 'Auto-Handling is OFF. Only updates made to this form will be reflected in handling charges';
	}
	if ($order['Order']['auto_shipping']) {
		$info[] = 'Auto-Shipping is ON. It will be updated as changes are made to shipping charges';
	} else {
		$info[] = 'Auto-Shipping is OFF. Only updates made to this form will be reflected in shipping charges';
	}
	if ($order['Order']['auto_price']) {
		$info[] = 'Auto-Pricing is ON. All prices are set by the products database';
	} else {
		$info[] = 'Auto-Pricing is OFF. You can change the default prices of items';
	}

}
echo $this->Layout->fieldset('Order Automatic Updating', $this->Layout->menu($info));

?>