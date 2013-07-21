<?php
echo $this->Layout->defaultHeader($product['Product']['id'],array(
	array('Add More Stock', array(
		'controller' => 'product_inventory_adjustments', 
		'action' => 'add', 
		$product['Product']['id']
	))
));

$info = array(
	'Catalog Item' => $this->CatalogItem->link($product['CatalogItem']),
	'Options' => $product['Product']['sub_title'],
);
$info['Total Stock'] = $this->CatalogItem->inventory($product['Product']['stock']);

if (!empty($products)) {
	$list = array();
	foreach ($products as $key => $otherProduct) {
		$active = null;
		$title = $otherProduct['Product']['title'];
		$title .= ' (' . number_format($otherProduct['Product']['stock']) . ')';
		if ($product['Product']['id'] == $otherProduct['Product']['id']) {
			$active = $key;
		}
		$list[] = array($title, $this->Product->url($otherProduct['Product']), compact('class', 'active'));
	}
	$info['Other Options'] = $this->Layout->nav($list, array('class' => 'nav-list'));
}
echo $this->Layout->infoTable($info);

$this->Table->reset();
foreach ($productInventoryAdjustments as $productInventoryAdjustment) {
	$url = array(
		'controller' => 'product_inventory_adjustments', 
		'action' => 'edit', 
		$productInventoryAdjustment['ProductInventoryAdjustment']['id']
	);
	$this->Table->cells(array(
		array($this->Calendar->niceShort($productInventoryAdjustment['ProductInventoryAdjustment']['available']), 'Date Added'),
		array($this->CatalogItem->inventory($productInventoryAdjustment['ProductInventoryAdjustment']['quantity']), 'Amount'),
		array($productInventoryAdjustment['ProductInventoryAdjustment']['title'], 'Description'),
		array($this->ModelView->actionMenu(array('edit', 'delete'), compact('url')), 'Actions'),
	), true);
}
echo $this->Table->output(array('paginate' => true));