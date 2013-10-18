<?php
echo $this->element('orders/key');

$this->Table->reset();
$dateOptions = array('format' => 'tiny', 'time' => false, 'year');
foreach ($orders as $order) {
	$url = $this->Order->modelUrl($order['Order'], array('admin' => true));
	$class = $this->Order->getStatusClass($order);

	$this->Table->cells(array(
		array(
			$this->Html->link('Order #' . $order['Order']['id'], $url),
			'Order', 'id'
		), array(
			$this->DisplayText->cash($order['Order']['total']), 
			'Total', 
			array('class' => 'orderTotal')
		), array(	
			$this->AddressBook->location($order['Order'], array('beforeField' => 'name')), 
			'Shipping To'
		), array(
			$this->Calendar->niceShort($order['Order']['created'], $dateOptions), 
			'Ordered', 'created'
		), array(
			$this->Calendar->niceShort($order['Invoice']['paid'], $dateOptions), 
			'Paid', 'Invoice.paid'
		), array(
			$this->Calendar->niceShort($order['Order']['shipped'], $dateOptions), 
			'Shipped', 'shipped'
		), array(
			$this->Order->actionMenu(array('view', 'edit', 'delete'), compact('url')), 
			'Actions'
		)
	), compact('class'));
}
echo $this->Table->output(array('paginate' => true, 'div' => 'orders'));