<?php
$this->Asset->css('products');
$this->Asset->js('product');


echo $this->Form->create('Order');
echo $this->Form->hidden('id');

//Shipping Info
echo $this->Layout->fieldset('Shipping Information', null, array('class' => 'halfFormWidth'));

echo $this->Form->input('cancelled', array('label' => 'Order has been cancelled'));

echo "<table><tr><td>\n";
echo $this->element('orders/input_shipping');
echo "</td><td>\n";
echo $this->element('profiles/full_name_input', array('model' => 'Order'));
echo $this->element('locations/form', array(
	'model' => 'Order',
	'addline' => 2,
	'location' => true,
	'cityStateLine' => false,
));
echo "</td></tr></table>\n";
echo "</fieldset>\n";

//Payment Info
echo $this->Layout->fieldset('Payment Information', null, array('class' => 'halfFormWidth'));
echo $this->Form->input('same_billing', array('type' => 'checkbox', 'label' => 'Shipping address is same as Billing address'));


echo "<table><tr><td>";
echo $this->element('orders/input_payment', array('amt' => false));
echo "</td><td>\n";

echo $this->element('profiles/full_name_input', array('model' => 'Invoice'));
echo $this->element('locations/form', array(
	'model' => 'Invoice',
	'addline' => 2,
	'cityStateLine' => false,
));

echo "</td></tr></table>\n";

echo "</fieldset>\n";


//Products
echo $this->Layout->fieldset('Order Items');
echo $this->Form->inputs(array(
	'fieldset' => false,
	'auto_shipping' => array(
		'type' => 'checkbox',
		'label' => 'Let the system calculate what shipping charges should be'
	),
	'auto_price' => array(
		'type' => 'checkbox',
		'label' => 'Let the system calculate product prices',
	)
));
$this->Table->reset();
foreach($this->request->data['OrderProduct'] as $k => $orderProduct) {
	$prefix = 'OrderProduct.' . $k . '.';
	echo $this->Form->input($prefix . 'id');
	$productOption = !empty($productOptions[$orderProduct['product_id']]) ? $productOptions[$orderProduct['product_id']] : null;
	$this->Table->cells(array(
		array($this->Form->input($prefix . 'product_id', array('label' => false)), 'Product'),
		array($this->element('products/product_option_select', array(
			'model' => 'OrderProduct', 
			'key' => $k, 
			'productOptions' => $productOption,
		))),
		array($this->Form->input($prefix . 'price', array('label' => '$', 'div' => false)), 'Price per Item', null, null, array('class' => 'number')),
		array($this->Form->input($prefix . 'shipping', array('label' => '$', 'div' => false)), 'Shipping', null, null, array('class' => 'number')),
		array($this->Form->input($prefix . 'quantity', array('label' => false, 'div' => false)), 'Quantity', null, null, array('class' => 'number')),
	), true);
}
echo $this->Table->table();
echo "</fieldset>\n";

//Handling
echo $this->Html->div('orderProductsForm');
echo $this->Layout->fieldset('Handling Charges');
echo $this->Form->input('auto_handling', array(
	'type' => 'checkbox',
	'label' => 'Let the system calculate what handling charges should be'
));
echo $this->Table->reset();
for ($k = 0; $k <= count($this->request->data['ProductHandlingsOrder']); $k++) {
	$prefix = 'ProductHandlingsOrder.' . $k . '.';
	
	if (!empty($this->request->data['ProductHandlingsOrder'][$k])) {
		$total = ($this->request->data['Order']['sub_total'] + $this->request->data['Order']['shipping']);
		$total *= $this->request->data['ProductHandlingsOrder'][$k]['pct'];
		$total += $this->request->data['ProductHandlingsOrder'][$k]['amt'];
	} else {
		$total = 0;
	}
	echo $this->Form->hidden($prefix . 'id');
	echo $this->Form->hidden($prefix . 'handling_method_id');
	$this->Table->cells(array(
		array($this->Form->input($prefix . 'title', array('label' => false)), 'Title'),
		array($this->Form->input($prefix . 'amt', array('label' => '$')), 'Amount'),
		array($this->Form->input($prefix . 'pct', array('label' => false, 'after' => '%')), 'Percent'),
		array($this->DisplayText->cash($total), 'Charge'),
	), true);
}
echo $this->Table->table();
echo "</fieldset>\n";



echo $this->FormLayout->submit('Update');
echo $this->Form->end();
echo "</div>\n";
?>
