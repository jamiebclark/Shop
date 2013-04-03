<?php
$this->Asset->css('products');

echo $this->element('orders/key');

$this->Table->reset();
foreach ($orders as $order) {
	$url = array('controller' => 'orders', 'action' => 'view', $order['Order']['id']);
	
	if ($order['Order']['cancelled']) {
		$class = 'cancelled';
	} else {
		$class = !empty($order['Order']['shipped']) ? 'shipped' : 'notShipped';
		$class .= !empty($order['Invoice']['paid']) ? 'Paid' : 'NotPaid';
	}
	$class .= ' orderNumber';

	$this->Table->cell(
		$this->Html->link('Order #' . $order['Order']['id'], $url), 
		'Order', 
		'id',
		null,
		compact('class')
	);
	
	$this->Table->cell($this->DisplayText->cash($order['Order']['total']), 'Total', null, null, array('class' => 'orderTotal'));
	$this->Table->cell($this->Contact->location($order['Order'], array('beforeField' => 'name')), 'Shipping To');
	
	$dateOptions = array('format' => 'tiny', 'time' => false, 'year');
	$this->Table->cells(array(
		array($this->Calendar->niceShort($order['Order']['created'], $dateOptions), 'Ordered', 'created'),
		array($this->Calendar->niceShort($order['Invoice']['paid'], $dateOptions), 'Paid', 'Invoice.paid'),
		array($this->Calendar->niceShort($order['Order']['shipped'], $dateOptions), 'Shipped', 'shipped'),
	));
	
	$this->Table->cell($this->Layout->actionMenu(array('view', 'edit', 'delete'), compact('url')), 'Actions');
	$this->Table->rowEnd();
}
echo $this->Table->table(array('paginate', 'div' => 'orders'));
?>
