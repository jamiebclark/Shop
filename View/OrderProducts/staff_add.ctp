<?php
echo $this->element('orders/staff_heading', array(
	'crumbs' => array(
		array('Order #'. $order['Order']['id'], array(
			'controller' => 'orders',
			'action' => 'view',
			$order['Order']['id']
		)),
		'Add '. $product['Product']['title'],
	)
));
echo $this->element('order_products/form');
?>