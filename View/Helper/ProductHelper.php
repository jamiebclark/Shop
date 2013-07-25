<?php
App::uses('ModelViewHelper', 'Layout.View/Helper');
class ProductHelper extends ModelViewHelper {
	var $name = 'Product';
	var $modelPlugin = 'Shop';
	
	function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$ModelView = $this;
		$this->setAutoAction('add', array(
			'function' => function($id, $options = array()) use ($ModelView)  {
				return array(
					'Add',
					array('controller' => 'product_inventory_adjustments', 'action' => 'add', $id),
					array('icon' => 'add'),
				);
			}
		));
	}			
}