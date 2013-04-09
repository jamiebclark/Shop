<?php
echo $this->element('orders/admin_heading', array(
	'crumbs' => array(
		array('Order #'. $order['Order']['id'], array(
			'controller' => 'orders',
			'action' => 'view',
			$order['Order']['id']
		)),
		'Edit Order Product',
	)
));
echo $this->element('order_products/form');
?>