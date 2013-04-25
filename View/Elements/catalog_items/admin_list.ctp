<?php
$this->Table->reset();
$dir = 'thumb';
foreach ($catalogItems as $catalogItem) {
	$url = array('action' => 'view', $catalogItem['CatalogItem']['id']);
	$active = $catalogItem['CatalogItem']['active'];
	
	$this->Table->checkbox($catalogItem['CatalogItem']['id']);
	$this->Table->cells(array(
		array(
			$this->CatalogItem->thumb($catalogItem['CatalogItem'], compact('url', 'dir')), 
			array('width' => 80)
		), array($this->CatalogItem->link($catalogItem['CatalogItem'], compact('url')),
			'Catalog Item'
		), array($this->CatalogItem->inventory($catalogItem['CatalogItem']), 'Stock'),
		array($this->Layout->actionMenu(array('view', 'edit', 'active', 'delete'), compact('url', 'active')), 'Actions')
	), array('class' => $active ? null : 'inactive'));
}
echo $this->Table->output(array(
	'paginate' => true,
	'withChecked' => array('delete', 'active', 'inactive'),
));	