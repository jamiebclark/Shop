<h1>Shipping</h1>
<div class="row">
<div class="span8">
<?php  echo $this->Form->create('Order', array('class' => 'largeFont')); 
	echo $this->Form->inputs(array(
		'id' => array('type' => 'hidden'),
		'Invoice.id' => array('type' => 'hidden'),
		'fieldset' => false,
	));
	echo $this->FormLayout->inputRow(array('first_name', 'last_name'), array('span' => 6));
	echo $this->FormLayout->inputRows(array(
		array('addline1' => array('label' => 'Street Address')),
		array('addline2' => array('label' => 'Apt. #')),
		array('city', 'state', 'zip'),
		array('country')
	), array('span' => 6, 'placeholder' => true));
	?>
<fieldset><legend>Billing Information</legend>
<?php
echo $this->FormLayout->toggle('', $this->FormLayout->inputRows(array(
		array('Invoice.first_name', 'Invoice.last_name'),
		array('Invoice.addline1' => array('label' => 'Street Address')),
		array('Invoice.addline2' => array('label' => 'Apt. #')),
		array('Invoice.city', 'Invoice.state', 'Invoice.zip'),
		array('Invoice.country')
	), array('span' => 6, 'placeholder' => true)), 
	'Billing information is same as Shipping', 
	array('name' => 'same_billing')
);
?>
</fieldset>
<?php
echo $this->FormLayout->buttons(array(
	'Complete Order' => array('class' => 'btn-primary'),
	'Edit Cart' => array(
		'url' => array('action' => 'view', $order['Order']['id']),
		'class' => 'prev',
		'align' => 'left',
	),
), array('align' => 'right'));
?>
</div>
<div class="span4">
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