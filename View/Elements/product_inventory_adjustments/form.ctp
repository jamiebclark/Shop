<h1><?php echo $product['Product']['title']; ?></h1>
<?php
echo $this->Form->create('ProductInventoryAdjustment');
echo $this->Form->inputs(array(
	'fieldset' => false,
	'id' => array('type' => 'hidden'),
	'product_id' => array('type' => 'hidden'),
	'quantity',
	'title' => array('label' => 'Description'),

));
echo $this->FormLayout->dateInput('available', array(
	'label' => 'Date Available',
	'control' => array('today', 'clear')
));
echo $this->FormLayout->submit('Update');
echo $this->Form->end();