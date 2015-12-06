<?php
App::uses('ModelViewHelper', 'Layout.View/Helper');
class CatalogItemImageHelper extends ModelViewHelper {
	public $name = 'CatalogItemImage';
	
	protected $modelPlugin = 'Shop';
	protected $thumbDir = 'catalog_item_images/';
	protected $defaultDir = 'thumb';
	protected $defaultMediaDir = 'thumb';

}