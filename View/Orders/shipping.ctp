<?php
$this->Asset->js('form_edit_check');

echo $this->element('products/crumbs', array(
	'crumbs' => array(
		array('Your Order', array('action' => 'view', $order['Order']['id'])),
		array('Shipping / Billing Info'),
	)
));

echo $this->Html->tag('h1', 'Shipping Inforation', array('class' => 'topTitle'));

echo $this->Html->div('span-16 shippingInformation');

echo $this->Form->create('Order', array('class' => 'largeFont'));
echo $this->Form->hidden('id');

//Shipping Info
echo $this->Layout->fieldset('Shipping Information');
echo $this->element('profiles/full_name_input', array('model' => 'Order'));
echo $this->element('locations/form', array(
	'model' => 'Order',
	'location' => true,
	'addline' => 2,
	'cityStateLine' => false,
));
echo $this->Form->input('email');
echo $this->Form->input('phone');
echo "</fieldset>\n";


//Payment Info
echo $this->Layout->fieldset('Payment Information');
echo $this->Form->input('same_billing', array(
	'type' => 'checkbox', 
	'label' => 'Billing information is same as Shipping Info',
));
echo $this->Html->div('billingInfo', null, array(
	'style' => $this->Html->value('Order.same_billing') ? 'display:none' : ''
));
echo $this->Form->hidden('Invoice.id');
echo $this->element('profiles/full_name_input', array('model' => 'Invoice'));
echo $this->element('locations/form', array(
	'model' => 'Invoice',
	'addline' => 2,
	'cityStateLine' => false,
));
echo "</div>\n";
echo "</fieldset>\n";

echo $this->element('orders/shipping_cutoff_msg');

echo $this->FormLayout->buttons(array(
	'Complete Order' => array('class' => 'submit'),
	'Edit Cart' => array(
		'url' => array('action' => 'view', $order['Order']['id']),
		'class' => 'prev',
		'align' => 'left',
	),
), array(
	'align' => 'right'
));

echo "</div>\n";
echo $this->Html->div('span-8 last');
echo $this->element('orders/cart', array(
	'condensed' => true,
	'form' => false,
	'links' => false,
	'images' => false,
));
echo "</div>\n";
?>
