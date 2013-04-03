<?php
echo $this->element('product_inventories/staff_heading');

if (!empty($product)) {
	echo $this->Html->tag('h1', $product['Product']['title']);
} else {
	echo $this->Html->tag('h1', 'Product Inventory');
}

$this->Table->reset();
foreach ($productInventories as $productInventory) {
	$id = $productInventory['ProductInventory']['id'];
	$url = array('controller' => 'product_inventories', 'action' => 'view', $id);
	$active = $productInventory['Product']['active'];
	
	$title = html_entity_decode($productInventory['Product']['title']);
	$this->Table->cell($this->Html->link($title, $url, array('class' => $active ? 'active': 'inactive')), 'Product');
	$i = 0;
	while(isset($productInventory['ProductOptionChoice' . ++$i])) {
		$cell = (!empty($productInventory['ProductOptionChoice' . $i]['title'])) ? $productInventory['ProductOptionChoice' . $i]['title'] : '&nbsp;';
		$this->Table->cell($cell, 'Option ' . $i);
	}
	$this->Table->cell($this->Product->inventory($productInventory['ProductInventory']['quantity']), 'Quantity in Stock');
	$this->Table->cell($this->Calendar->niceShort($productInventory['ProductInventory']['modified']), 'Last Updated');
	$this->Table->cell($this->Layout->actionMenu(array(
		//'view', 
		//'edit', 
		'add' => array('url' => array('controller' => 'product_inventory_adjustments', 'action' => 'add', $id)), 
		//'delete'
	), compact('url')), 'Actions');
	$this->Table->rowEnd();
}
echo $this->Table->table(array('paginate'));
?>