<?php
echo $this->element('orders/staff_heading', array(
	'crumbs' => array(
		array('Order #'. $this->Html->value('Order.id'), array('action' => 'view', $this->Html->value('Order.id'))),
		'Edit Order',
	)
));
echo $this->element('orders/form');
?>