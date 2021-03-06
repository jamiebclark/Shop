<?php
echo $this->Layout->defaultHeader();
$this->Table->reset();
foreach ($catalogItemImages as $catalogItemImage) {
	$url = $this->ModelView->modelUrl($catalogItemImage['CatalogItemImage']);
	$this->Table->checkbox($catalogItemImage['CatalogItemImage']['id']);
	$this->Table->cells(array(
		array(
			$this->CatalogItem->thumb($catalogItemImage['CatalogItem'], array(
				'dir' => 'thumb', 'url' => $url
			)), 'Image', array('width' => 40)
		), array(
			$this->Html->link($catalogItemImage['CatalogItem']['title'], 
				array(
					'controller' => 'catalog_items', 
					'action' => 'view', 
					$catalogItemImage['CatalogItem']['id']
				), array('class' => 'secondary')),
			'Catalog Item',
		), array(
			$this->ModelView->actionMenu(array('view', 'edit', 'delete', 'move_up', 'move_down'), $catalogItemImage['CatalogItemImage']),
			'Actions',
		)
	), true);
}
echo $this->Table->output(array(
	'paginate' => true,
	'withChecked' => array('delete')
));