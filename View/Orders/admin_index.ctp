<?php
$actions = array();
if (!empty($shopCartId)) {
	$actions[] = array(
		'Clear Cart',
		array('action' => 'clear_cart'),
		array('icon' => 'x')
	);
}
echo $this->Layout->defaultHeader(null, $actions);
echo $this->element('orders/admin_list');
