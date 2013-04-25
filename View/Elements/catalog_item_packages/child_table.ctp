<?php
$this->Table->reset();
foreach ($result as $k => $catalogItemPackage):
	$url = null;
	$prefix = 'ChildOrderProduct.' . $k . '.';
	$title = $catalogItemPackage['CatalogItemChild']['title'];
	if (!$catalogItemPackage['CatalogItemChild']['hidden']) {
		$url = $this->CatalogItem->url($catalogItemPackage['CatalogItemChild']);
		$title = $this->Html->link($title, $url);
	}
	$thumb = $this->CatalogItem->thumb($catalogItemPackage['CatalogItemChild'], array(
		'dir' => 'thumb', 
		'url' => $url,
	)); 
	$this->Table->cells(array(
		array($thumb, null, array('class' => 'thumb')),
		array($title, 'Product'),
		array($this->element('catalog_item_options/input', array(
			'catalogItemOptions' => $catalogItemPackage['CatalogItemChild']['CatalogItemOption'],
			'prefix' => $prefix,
		)), 'Options'),
		array(number_format($catalogItemPackage['quantity']), 'Qty.', array('class' => 'qty')),
	), true);
	
	echo $this->Form->hidden($prefix . 'catalog_item_id', array(
		'value' => $catalogItemPackage['CatalogItemChild']['id'],
	));
	echo $this->Form->hidden($prefix . 'package_quantity', array(
		'value' => $catalogItemPackage['quantity']
	));
	echo $this->Form->hidden($prefix . 'quantity', array(
		'value' => $catalogItemPackage['quantity']
	));	
	
	if ($this->Html->value('Order.id')) {
		echo $this->Form->hidden($prefix . 'order_id', array('value' => $this->Html->value('Order.id')));
	}
	if (!empty($orderProductId)) {
		echo $this->Form->hidden($prefix . 'parent_id', array('value' => $orderProductId));
	}
endforeach;
echo $this->Table->output(array('class' => 'catalog-item-package-children', 'div' => false));