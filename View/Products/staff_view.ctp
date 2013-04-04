<?php
echo $this->element('product_inventories/staff_heading');
echo $this->Html->tag('h1', 'Product Inventory');
echo $this->Layout->headerMenu(array(
	array('Add More Inventory', array(
		'controller' => 'product_inventory_adjustments', 
		'action' => 'add', 
		$productInventory['ProductInventory']['id']
	))
));

$info = array(
<<<<<<< HEAD
	'Product' => $this->CatalogItem->link($productInventory['Product']),
=======
	'Product' => $this->Product->link($productInventory['Product']),
>>>>>>> 7f1010ba1dfec77e6fe69120dbda39b9bea5eb76
);
$i = 0;
while(isset($productInventory['ProductOptionChoice' . ++$i]['id'])) {
	$info['Option ' . $i] = $productInventory['ProductOptionChoice' . $i]['title'];
}
<<<<<<< HEAD
$info['Total Stock'] = $this->CatalogItem->inventory($productInventory['ProductInventory']['quantity']);
=======
$info['Total Stock'] = $this->Product->inventory($productInventory['ProductInventory']['quantity']);
>>>>>>> 7f1010ba1dfec77e6fe69120dbda39b9bea5eb76

if (!empty($productInventories)) {
	$list = array();
	foreach ($productInventories as $inventory) {
		$title = '';
		$i = 0;
		while(isset($inventory['ProductOptionChoice' . ++$i]['id'])) {
			if (!empty($title)) {
				$title .= ', ';
			}
			$title .= $inventory['ProductOptionChoice' . $i]['title'];
		}
		if (empty($title)) {
			$title = $this->Html->tag('em', 'No option');
		}
		$title .= '(' . number_format($inventory['ProductInventory']['quantity']) . ')';
		
		$class = $inventory['ProductInventory']['id'] == $productInventory['ProductInventory']['id'] ? 'selected' : false;
		
		$list[] = array(
			$title, 
			array('action' => 'view', $inventory['ProductInventory']['id']), 
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
<<<<<<< HEAD
		array($this->CatalogItem->inventory($productInventoryAdjustment['ProductInventoryAdjustment']['quantity']), 'Amount'),
=======
		array($this->Product->inventory($productInventoryAdjustment['ProductInventoryAdjustment']['quantity']), 'Amount'),
>>>>>>> 7f1010ba1dfec77e6fe69120dbda39b9bea5eb76
		array($productInventoryAdjustment['ProductInventoryAdjustment']['title'], 'Description'),
		array($this->Layout->actionMenu(array('edit', 'delete'), compact('url')), 'Actions'),
	), true);
}
echo $this->Table->table(array('paginate'));
?>