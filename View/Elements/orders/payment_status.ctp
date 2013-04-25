<?php
$title = 'Payment Information';
$info = array();
if (!empty($order['Invoice']['addline1'])) {
	$info['Billing Address'] = $this->AddressBook->location($order['Invoice'], array('beforeField' => array('name')));
} else {
	$info['Billing Address'] = $this->Html->link('<em>Not set yet</em>', array(
		'controller' => 'orders',
		'action' => 'shipping',
		$order['Order']['id'],
	), array('escape' => false));
}
$info['Status'] = $this->Order->paid($order);
echo $this->element('orders/status', compact('info', 'blank', 'title', 'mode', 'tag'));