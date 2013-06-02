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
	foreach ($products as $product) {
		$title = $product['Product']['title'];
		$title .= ' (' . number_format($product['Product']['stock']) . ')';
		$class = $product['Product']['id'] == $product['Product']['id'] ? 'selected' : false;
		$list[] = array(
			$title, 
			array('action' => 'view', $product['Product']['id']), 
			array('escape' => false, 'class' => $class)
		);
	}
	$info['Other Options'] = $this->Layout->topMenu($list);
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
		array($this->Layout->actionMenu(array('edit', 'delete'), compact('url')), 'Actions'),
	), true);
}
echo $this->Table->output(array('paginate'));