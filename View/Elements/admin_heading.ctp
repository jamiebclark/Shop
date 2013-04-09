<?php
$this->Asset->css('products');

echo $this->element('online_store/crumbs', compact('defaultCrumbs', 'crumbs'));
$tabs = array(
	'products',
	'shop_orders' => 'Orders',
	'product_inventories' => 'Inventory',
	'product_categories',
	'product_promos' => 'Promotional Codes',
	'product_handlings' => 'Order Handling Charges',
);
$tabInfo = array();
foreach ($tabs as $controller => $label) {
	if (is_numeric($controller)) {
		$controller = $label;
		$label = Inflector::humanize($controller);
	}
	$tabInfo[] = array($label, array('controller' => $controller, 'action' => 'index'));
}
$this->Layout->tabMenu($tabInfo, array('currentSelect' => array('controller' => true), 'pre_crumb' => true));
?>