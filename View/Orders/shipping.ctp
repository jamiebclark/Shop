<h1>Shipping</h1>
<div class="row">
<div class="span8">
<?php echo $this->Form->create('Order', array('class' => 'largeFont')); 
echo $this->Form->inputs(array(
	'id' => array('type' => 'hidden'),
	'Invoice.id' => array('type' => 'hidden'),
	'first_name',
	'last_name',
	'addline1',
	'addline2',
	'city',
	'state',
	'zip',
	'country',
	'legend' => 'Shipping Information',
));
?>
</fieldset>
<fieldset><legend>Billing Information</legend>
<?php
echo $this->FormLayout->toggle('', $this->Form->inputs(array(
		'Invoice.first_name',
		'Invoice.last_name',
		'Invoice.addline1',
		'Invoice.addline2',
		'Invoice.city',
		'Invoice.state',
		'Invoice.country',
		'legend' => false,
	)), 
	'Billing information is same as Shipping', 
	array('name' => 'same_billing')
);
?>
</fieldset>
<?php	
echo $this->FormLayout->buttons(array(
	'Complete Order' => array('class' => 'submit'),
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