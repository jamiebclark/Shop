<?php
$title = 'Shipping Information';
$info = array();
if (empty($order['Order']['addline1'])) {
	$info['Shipping Address'] = $this->Html->link('<em>Not set yet</em>', array(
		'controller' => 'orders',
		'action' => 'shipping',
		$order['Order']['id'],
	), array('escape' => false));
} else {
	$info['Shipping Address'] = $this->AddressBook->location($order['Order'], array(
		'beforeField' => array('name'),
	));
	$info['Email'] = $this->AddressBook->email($order['Order']['email']);
	$info['Phone'] = $this->AddressBook->phone($order['Order']['phone']);
	$info['Status'] = $this->Order->shipped($order);
	if (!empty($order['Order']['tracking'])) {
		$info['Tracking'] = $this->Order->tracking($order);
	}
}
$url = array('controller' => 'orders', 'action' => 'shipping', $order['Order']['id']);
echo $this->element('orders/status', compact('info', 'blank', 'title', 'mode', 'tag', 'url'));