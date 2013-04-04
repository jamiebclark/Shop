<?php
$this->Table->reset();
$dir = 'thumb';
foreach ($catalogItems as $catalogItem) {
	$url = array('action' => 'view', $catalogItem['CatalogItem']['id']);
	$active = $catalogItem['CatalogItem']['active'];
	$this->Table->cells(array(
		array($this->CatalogItem->thumb($catalogItem['CatalogItem'], compact('url', 'dir')), null, null, null, array('width' => 80)),
		array($this->CatalogItem->link($catalogItem['CatalogItem'], compact('url'))),
		array($this->DisplayText->positiveNumber($catalogItem['CatalogItem']['stock']), 'In Stock'),
		array($this->Layout->actionMenu(array('view', 'edit', 'active', 'delete'), compact('url', 'active')), 'Actions')
	), true);
}
echo $this->Table->table(array('paginate' => true));	