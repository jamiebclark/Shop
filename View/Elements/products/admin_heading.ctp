<?php
if (empty($defaultCrumbs)) {
	$defaultCrumbs = array();
}
$defaultCrumbs = array_merge(array(
	array('Product Inventory', array('controller' => 'product_inventories', 'action' => 'index'))
), $defaultCrumbs);

echo $this->element('admin_heading', compact('defaultCrumbs', 'crumbs'));
?>