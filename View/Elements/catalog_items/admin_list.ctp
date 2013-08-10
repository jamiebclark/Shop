<?php
$this->Table->reset();
$dir = 'thumb';
foreach ($catalogItems as $catalogItem) {
	$url = array('action' => 'view', $catalogItem['CatalogItem']['id']);
	$active = $catalogItem['CatalogItem']['active'];
	
	$this->Table->checkbox($catalogItem['CatalogItem']['id']);
	
	$this->Table->cells(array(
		array(
			$this->CatalogItem->media($catalogItem['CatalogItem'], array('dir' => 'thumb')), 
			'Catalog Item',
			
		), 
		array($this->CatalogItem->inventory($catalogItem['CatalogItem']), 'Stock'),
		array($this->Layout->boolOutput($active), 'Active', 'active'),		
		array($this->CatalogItem->actionMenu(array('view', 'edit', 'active', 'delete'), compact('url', 'active')), 'Actions')
	), array('class' => $active ? null : 'inactive'));
}
echo $this->Table->output(array(
	'class' => 'catalogitem-admin-list',
	'paginate' => true,
	'withChecked' => array('delete', 'active', 'inactive'),
));	