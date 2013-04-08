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
if (!empty($order['Invoice']['paid'])) {
	$info['Status'] = $this->Html->tag('strong', 'Paid ' . $this->Calendar->niceShort($order['Invoice']['paid']));
} else {
	$info['Status'] = $this->Html->link('<em>Not paid yet</em>', array(
		'controller' => 'orders',
		'action' => 'checkout',
		$order['Order']['id'],
	), array('escape' => false));
}
echo $this->element('orders/status', compact('info', 'blank', 'title', 'mode', 'tag'));