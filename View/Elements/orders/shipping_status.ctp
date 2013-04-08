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
	if (!empty($order['Order']['shipped'])) {
		$info['Status'] = $this->Html->tag('strong', 'Shipped ' . $this->Calendar->niceShort($order['Order']['shipped']));
		if (!empty($order['OrderShippingMethod']['title'])) {
			$info['Shipping Method'] = $order['OrderShippingMethod']['title'];
		}
		if (!empty($order['Order']['tracking'])) {
			if (!empty($order['OrderShippingMethod']['url'])) {
				$tracking = $this->Html->link(
					$order['Order']['tracking'], 
					$order['OrderShippingMethod']['url'] . $order['Order']['tracking'], 
					array('target' => '_blank')
				);
			} else {
				$tracking = $order['Order']['tracking'];
			}
			$info['Tracking'] = $tracking;
		}		
	} else {
		$info['Status'] = $this->Html->tag('em', 'Not shipped yet');
	}
}
echo $this->element('orders/status', compact('info', 'blank', 'title', 'mode', 'tag'));