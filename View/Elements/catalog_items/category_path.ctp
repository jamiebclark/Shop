<?php
if (!empty($catalogItemCategoryPath)) {
	foreach ($catalogItemCategoryPath as $count => $catalogItemCategory) {
		$this->Crumbs->add(
			$catalogItemCategory['CatalogItemCategory']['title'],
			array('action' => 'index', 'category' => $catalogItemCategory['CatalogItemCategory']['id'])
		);
	}
}
