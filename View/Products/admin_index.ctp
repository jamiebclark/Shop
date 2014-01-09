<p><?php echo $this->Html->link('View Inventory History', array('controller' => 'product_inventory_adjustments', 'action' => 'index')); ?></p>
<h1>Products</h1>
<?php

$this->Table->reset();
foreach ($products as $product) {
	$id = $product['Product']['id'];
	$qty = $product['Product']['stock'];
	$qtyDisplay = $this->CatalogItem->inventory($qty);
	
	$url = array('controller' => 'products', 'action' => 'view', $id);
	$active = $product['CatalogItem']['active'];
	$title = html_entity_decode($product['Product']['title']);
	$unlimited = $product['CatalogItem']['unlimited'];
	
	$rowClass = $active ? $this->CatalogItem->getInventoryClass($qty, $unlimited) : 'inactive';

	$this->Table->checkbox($id);
	$this->Table->cells(array(
		array(
			$this->Html->link(
				$product['CatalogItem']['title'], 
				array(
					'controller' => 'catalog_items', 
					'action' => 'view', 
					$product['CatalogItem']['id']
				)
			),
			'Catalog Item',
			'CatalogItem.title',
		), array(
			$this->Html->link($product['Product']['sub_title'], $url, array('class' => 'secondary')),
			'Product',
		), 
		array($this->CatalogItem->inventory($qty, $unlimited), 'Quantity in Stock'), 
		array($this->Calendar->niceShort($product['Product']['modified']), 'Last Updated'), 
		array($this->Product->actionMenu(array('active', 'add', 'delete'), $product['Product']), 'Actions')
	), array('class' => $rowClass));
}
echo $this->Table->output(array(
	'paginate' => true,
	'withChecked' => array('delete'),
));