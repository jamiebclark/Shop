<?php
echo $this->Layout->defaultHeader($product['Product']['id'],array(
	array('Add More Stock', [
		'controller' => 'product_inventory_adjustments', 
		'action' => 'add', 
		$product['Product']['id']
	])
));

$info = array(
	'Catalog Item' => $this->CatalogItem->link($product['CatalogItem']),
	'Options' => $product['Product']['sub_title'],
);
$info['Total Stock'] = $this->CatalogItem->inventory($product['Product']['stock']);

if (!empty($products)) {
	$list = [];
	foreach ($products as $key => $otherProduct) {
		$active = null;
		$title = $otherProduct['Product']['title'];
		$title .= ' (' . number_format($otherProduct['Product']['stock']) . ')';
		if ($product['Product']['id'] == $otherProduct['Product']['id']) {
			$active = $key;
		}
		$list[] = array($title, $this->Product->modelUrl($otherProduct['Product']), compact('class', 'active'));
	}
	$info['Other Options'] = $this->Layout->nav($list, ['class' => 'nav-list']);
}
?>
<div class="panel panel-default">
	<div class="panel-heading"><span class="panel-title">About</span></div>
	<?php echo $this->Layout->infoTable($info); ?>
</div>

<?php
$this->Table->reset();
foreach ($productInventoryAdjustments as $productInventoryAdjustment) {
	$url = [
		'controller' => 'product_inventory_adjustments', 
		'action' => 'edit', 
		$productInventoryAdjustment['ProductInventoryAdjustment']['id']
	];
	$this->Table->cells(array(
		array($this->Calendar->niceShort($productInventoryAdjustment['ProductInventoryAdjustment']['available']), 'Date Added'),
		array($this->CatalogItem->inventory($productInventoryAdjustment['ProductInventoryAdjustment']['quantity']), 'Amount'),
		[$productInventoryAdjustment['ProductInventoryAdjustment']['title'], 'Description'],
		array($this->ModelView->actionMenu(['edit', 'delete'], compact('url')), 'Actions'),
	), true);
}
?>
<div class="panel panel-default">
	<div class="panel-heading"><span class="panel-title">Inventory History</span></div>
	<?php echo $this->Table->output(['paginate' => true]); ?>
</div>