<?php
echo $this->Layout->defaultHeader();

$urlLen = 50;

foreach ($shippingMethods as $shippingMethod) {
	$url = array('action' => 'view', $shippingMethod['ShippingMethod']['id']);
	
	$trackUrl = $shippingMethod['ShippingMethod']['url'];
	if (strlen($trackUrl) > $urlLen) {
		$trackUrl = substr($trackUrl, 0, floor($urlLen/2)) . '...' . substr($trackUrl, -1 * ceil($urlLen/2));
	}
	
	$this->Table->cells(array(
		array(
			$this->Html->link($shippingMethod['ShippingMethod']['title'], $url),
			'Method',
		), array(
			$trackUrl,
			'Track URL',
		), array(
			$this->Layout->actionMenu(array('view', 'edit', 'delete'), compact('url')),
			'Actions',
		)
	), true);
}
echo $this->Table->output(array('paginate' => true));