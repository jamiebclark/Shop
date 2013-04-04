<?php
echo $this->element('catalog_items/crumbs', array(
	'crumbs' => $catalogItem['CatalogItem']['title']
));
echo $this->Html->div('catalogItem');
echo $this->Form->create('OrderProduct', array('action' => 'add'));

echo $this->Grid->col('1/4', null, array('class' => 'catalogItemViewImages'));
echo $this->CatalogItem->thumb(
	$catalogItem['CatalogItem'], array(
		'div' => false, 
		'dir' => 'thumb',
		'url' => array(
			'controller' => 'catalog_item_images',
			'action' => 'index',
			$catalogItem['CatalogItem']['id'],
		)
	)
);
if (count($catalogItem['CatalogItemImage']) > 1) {
	echo $this->element('catalog_item_images/thumb_list', array(
		'limit' => 3,
		'catalogItemImages' => $catalogItem['CatalogItemImage'],
	));
}

echo $this->Grid->colContinue('1/2');
echo $this->Html->tag('h1', $catalogItem['CatalogItem']['title']);

echo $this->DisplayText->text($catalogItem['CatalogItem']['description']);

if (!empty($catalogItem['CatalogItemPackageChild'])) {
	echo $this->Html->tag('h2', 'Packaged Item');
	echo 'This product contains the following items:';
	$this->Table->reset();
	foreach ($catalogItem['CatalogItemPackageChild'] as $k => $catalogItemPackage) {
		if ($catalogItemPackage['CatalogItemChild']['hidden']) {
			$url = null;
			$title = $catalogItemPackage['CatalogItemChild']['title'];
		} else {
			$url = $this->CatalogItem->url($catalogItemPackage['CatalogItemChild']);
			$title = $this->Html->link($catalogItemPackage['CatalogItemChild']['title'], $url);
		}
		
		if (!empty($loggedUserTypes['staff']) && !empty($catalogItemChildOptions[$catalogItemPackage['CatalogItemChild']['id']])) {
			$childId = $catalogItemPackage['CatalogItemChild']['id'];
			$title .= $this->Html->div('fullFormWidth', $this->element('catalog_items/product_option_input', array(
				'productOptions' => $catalogItemChildOptions[$childId],
				'model' => 'OrderProduct.PackageChild.' . $childId,
			)));
		}
		$this->Table->cells(array(
			array(
				$this->CatalogItem->thumb($catalogItemPackage['CatalogItemChild'], array('dir' => 'thumb', 'url' => $url)),
				null,
				null,
				null,
				array('width' => 40)
			),
			array($title, 'Item'),
			array(
				number_format($catalogItemPackage['quantity']),
				'Quantity',
				null,
				null,
				array('width' => 40)
			)
		), true);
	}
	echo $this->Table->table();
}
echo $this->Grid->colContinue('1/4', null, array('class' => 'orderCart'));

echo $this->CatalogItem->price($catalogItem['CatalogItem']);

$notes = array();
if ($catalogItem['CatalogItem']['min_quantity'] > 1) {
	$notes[] = 'Minimum order of ' . number_format($catalogItem['CatalogItem']['min_quantity']);
}
if ($catalogItem['CatalogItem']['quantity_per_pack'] > 1) {
	$notes[] = 'This is a pack of ' . number_format($catalogItem['CatalogItem']['quantity_per_pack']);
}
if ($catalogItem['CatalogItem']['stock'] < 10) {
	$notes[] = 'Limited stock';
}

if (!empty($notes)) {
	echo $this->Html->tag('h3', 'Notes');
	echo $this->Layout->menu($notes);
}

if ($catalogItem['CatalogItem']['stock'] <= 0) {
	echo $this->Html->tag('h3', 'Out of Stock');
	echo $this->Html->tag('p', 'Sorry, this item is temporarily out of stock. Please check back soon for inventory updates!');
} else {
	echo $this->Layout->fieldset('Add to Cart');
	echo $this->Form->inputs(array(
		'fieldset' => false,
		'Order.id' => array(
			'type' => 'hidden'
		),
		'OrderProduct.product_id' => array(
			'type' => 'hidden', 
			'value' => $catalogItem['CatalogItem']['id']
		),
		'Order.user_id' => array(
			'type' => 'hidden', 
			'default' => $loggedUserId
		),
	));

	echo $this->element('catalog_items/product_option_input', array(
		'blank' => true,
		'label' => false,
	));
	echo $this->Form->input('OrderProduct.quantity', array(
		'default' => !empty($catalogItem['CatalogItem']['min_quantity']) ? $catalogItem['CatalogItem']['min_quantity'] : 1,
		'class' => 'quantity',
	));
	echo $this->FormLayout->submit('Add to Cart');
	echo "</fieldset>\n";
}

echo $this->Grid->colClose(true);

echo $this->Form->end();
echo "</div>\n";	//catalogItem

if (!empty($loggedUserTypes['staff'])) {
	echo "<hr/>\n";
	echo $this->Layout->adminMenu(array('view', 'edit'), array('url' => array(
		'action' => 'view',
		$catalogItem['CatalogItem']['id'],
		'staff' => true
	)));
}