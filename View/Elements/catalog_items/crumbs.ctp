<?php
if (empty($this->request->params['prefix'])) {
	echo $this->element('orders/pre_crumb');
}

$this->Asset->css('style', array('plugin' => 'Shop'));
$this->Asset->js('script', array('plugin' => 'Shop'));

$this->Crumbs->baseCrumbs(array(array('Online Store', array('controller' => 'hubs', 'action' => 'view', 'shop'))));

$model = Inflector::classify($this->request->params['controller']);
$varName = InflectorPlus::varNameSingular($model);

if ($model != 'CatalogItem') {
	if (!empty($this->viewVars[$model])) {
		$catalogItem = $this->viewVars[$model];
	} else if (!empty($this->viewVars[$varName]['CatalogItem'])) {
		$catalogItem = $this->viewVars[$varName]['CatalogItem'];
	}
	if (!empty($catalogItem)) {
		$this->Crumbs->setParent('CatalogItem', $catalogItem, array('controllerVar' => 1));
	}
}
