<?php
$controller = $this->request->params['controller'];
$categoryController = $controller == 'catalog_item_categories';

if (!empty($catalogItemCategoryPath)) {
	$crumbs = array();
	foreach ($catalogItemCategoryPath as $count => $catalogItemCategory) {
		$url = compact('controller');
		$title = $catalogItemCategory['CatalogItemCategory']['title'];
		if ($categoryController) {
			$url += array('action'=>'view', $catalogItemCategory['CatalogItemCategory']['id']);
		} else {
			$url += array('action'=>'index', 'category'=>$catalogItemCategory['CatalogItemCategory']['id']);
		}
		$crumbs[] = array($title, $url);
	}
	if ($categoryController) {
		$this->Crumbs->actionCrumbs($crumbs);
	} else {
		$this->Crumbs->userSetCrumbs($crumbs);
	}
}
