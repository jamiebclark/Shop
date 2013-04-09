<?php
$this->Crumbs->title('Image');
$this->Crumbs->setParent('CatalogItem', $catalogItemImage['CatalogItem']);

echo $this->CatalogItem->thumb($catalogItemImage['CatalogItemImage'], array(
	'class' => 'catalog-item-image-view'
));
if (count($catalogItemImages) > 1) {
	echo $this->element('catalog_item_images/thumb_list');
}

if (!empty($loggedUserTypes['admin'])) {
	echo $this->Layout->adminMenu(array('view', 'edit', 'add', 'delete'), array(
		'url' => array(
			'action' => 'view', 
			$catalogItemImage['CatalogItemImage']['id'],
			'admin' => true
		)
	));
}