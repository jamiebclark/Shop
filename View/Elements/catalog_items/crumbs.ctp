<?php
if (empty($this->request->params['prefix'])) {
	echo $this->element('orders/pre_crumb');
}

$this->Asset->css('products');
$this->Asset->js('product');

$this->Crumbs->baseCrumbs(array(array('Online Store', array('controller' => 'hubs', 'action' => 'view', 'shop'))));

$model = Inflector::classify($this->request->params['controller']);
$varName = InflectorPlus::varNameSingular($model);

if ($model != 'Product') {
	if (!empty($this->viewVars[$model])) {
		$product = $this->viewVars[$model];
	} else if (!empty($this->viewVars[$varName]['Product'])) {
		$product = $this->viewVars[$varName]['Product'];
	}
	if (!empty($product)) {
		$this->Crumbs->setParent('Product', $product, array('controllerVar' => 1));
	}
}
