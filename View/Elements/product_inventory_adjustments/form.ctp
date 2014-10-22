<?php
$productIdType = !empty($product) ? 'hidden' : 'select';
?>
<h2>Adjust Inventory</h2>
<?php
echo $this->Form->create('ProductInventoryAdjustment');
echo $this->Form->inputs(array(
	'fieldset' => false,
	'id' => array('type' => 'hidden'),
	'product_id' => array('type' => 'select'),
	'quantity' => array(
		'type' => 'number',
		'label' => 'Amount Added',
		'after' => '<span class="help-block">If you are removing inventory, use a negative value</span>',
	),
	'title' => array(
		'label' => 'Description',
		'after' => '<span class="help-block">Optional description behind why this stock was added</span>',
	),

));
echo $this->FormLayout->inputDate('available', array(
	'label' => 'Date Available',
	'control' => array('today', 'clear')
));
echo $this->FormLayout->end('Update');