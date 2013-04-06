<style type="text/css">
tr.negative td {
	color: red;
}
tr.warning td {
	color: orange;
}
tr.positive td {
	color: green;
}
tr.inactive td {
	color: #CCC;
}
</style>
<h1>Products</h1>
<?php
$this->Table->reset();
foreach ($products as $product) {
	$id = $product['Product']['id'];
	$qty = $product['Product']['stock'];
	
	$url = array('controller' => 'products', 'action' => 'view', $id);
	$active = $product['Product']['active'];
	$title = html_entity_decode($product['Product']['title']);
	
	if ($active) {
		$rowClass = $this->CatalogItem->getInventoryClass($qty);
	} else {
		$rowClass = 'inactive';
	}
	$this->Table->cells(array(
		array(
			$this->Html->link(
				$product['CatalogItem']['title'], 
				array(
					'controller' => 'catalog_items', 'aciton' => 'view', $product['CatalogItem']['id']
				)
			),
			'Catalog Item'
		), array(
			$this->Html->link($product['Product']['sub_title'], $url, array('class' => 'secondary')),
			'Product',
		), 
		array($this->CatalogItem->inventory($qty), 'Quantity in Stock'), 
		array($this->Calendar->niceShort($product['Product']['modified']), 'Last Updated'), 
		array(
			$this->Layout->actionMenu(array(
				'add' => array(
					'url' => array(
						'controller' => 'product_inventory_adjustments', 'action' => 'add', $id
					)
				),
			), compact('url')),
			'Actions'
		)
	), array('class' => $rowClass));
}
echo $this->Table->output(array('paginate' => true));