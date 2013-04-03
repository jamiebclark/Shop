<?php
echo $this->DateBuild->input('shipped', array(
	'control' => array('today', 'clear'), 
	'class' => 'datetime', 
	'label' => 'Date Shipped',
	'blank' => true,
));
echo $this->Form->inputs(array(
	'fieldset' => false,
	'order_shipping_method_id' => array('label' => 'Ship Method'),
	'tracking' => array('label' => 'Tracking #', 'type' => 'text'),
	'shipping_cost' => array('label' => 'Cost to ship $'),
));
?>