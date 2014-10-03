<?php
$this->Html->addCrumb('Catalog', array('controller' => 'catalog_items', 'action' => 'index'));
if (empty($catalogItem) && !empty($catalogItemImage['CatalogItem'])) {
	$catalogItem = array('CatalogItem' => $catalogItemImage['CatalogItem']);
}

if (!empty($catalogItem)) {
	$this->Html->addCrumb($catalogItem['CatalogItem']['title'], array('controller' => 'catalog_images', 'action' => 'view', $catalogItem['CatalogItem']['id']));
}

if (!empty($catalogItemImage)) {
	$this->Html->addCrumb('Images', array('action' => 'index', $catalogItemImage['CatalogItem']['id']));
	$this->Html->addCrumb('Image');
} else {
	$this->Html->addCrumb('Images');
}
