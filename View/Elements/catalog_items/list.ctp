<?php
if (!isset($sort)) {
	$sort = true;
}
if ($sort) {
	echo $this->Layout->tableSortMenu(array(
		array('Title', 'CatalogItem.title'),
		array('Lowest Price', 'CatalogItem.price', 'ASC'),
	));
}

$this->Table->reset();
foreach ($catalogItems as $catalogItem) {
	$url = $this->CatalogItem->url($catalogItem['CatalogItem']);
	$catalogItemCell = $this->Html->tag('h2', $this->CatalogItem->link($catalogItem['CatalogItem']));
	if (!empty($catalogItem['CatalogItem']['short_description'])) {
		$catalogItemCell .= $this->DisplayText->text($catalogItem['CatalogItem']['short_description']);
	}
	$dir = 'thumb';
	$this->Table->cells(array(
		array(
			$this->CatalogItem->thumb($catalogItem['CatalogItem'], compact('url', 'dir')),
			$sort ? 'CatalogItem' : null,
			$sort ? 'CatalogItem.title' : null,
			null,
			array('width' => 80)
		),
		array($catalogItemCell),
		array(
			$this->CatalogItem->price($catalogItem['CatalogItem']), 
			$sort ? 'Price' : null, 
			$sort ? 'CatalogItem.price' : null,
		),
	), true);
}

echo $this->Html->div('catalogItemsList',
	$this->Table->table(array('paginate' => true))
);
