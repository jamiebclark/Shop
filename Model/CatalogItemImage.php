<?php
class CatalogItemImage extends ShopAppModel {
	var $name = 'CatalogItemImage';
	var $actsAs = array(
		'Uploadable.ImageUploadable' => array(
			'bypass_is_uploaded' => true,
			'upload_dir' => 'img/catalog_item_images/',
			'update' => array('filename'),
			'dirs' => array(
				'thumb' => array(
					'setSoft' => array(160, 160),
				),
				'mid' => array(
					'setSoft' => array(320, 320),
				),
				'' => array(
					'max' => array(600, 400)
				)
			)
		),
		'Shop.FieldOrder' => array(
			'orderField' => 'order',
			'subKeyFields' => array('catalog_item_id'),
		)
	);
	var $order = array('CatalogItemImage.catalog_item_id', 'CatalogItemImage.order');
	var $belongsTo = array('Shop.CatalogItem');
	
	var $validate = array(
		'catalog_item_id' => array(
			'rule' => 'notEmpty',
			'message' => 'Please select a product',
		)
	);
	
	function afterSave($created) {
		$result = $this->read('catalog_item_id', $this->id);
		$this->setCatalogItemThumb($result[$this->alias]['catalog_item_id']);
	}
	
	/**
	 * Sets the thumbnail for the CatalogItem as the first returned image in the set
	 *
	 **/
	function setCatalogItemThumb($productId = null) {
		$options = array(
			'group' => 'catalog_item_id'
		);
		if (!empty($productId)) {
			$options['catalog_item_id'] = $productId;
		}
		
		$results = $this->find('all', $options);
		$data = array();
		foreach ($results as $result) {
			$data[] = array(
				'id' => $result[$this->alias]['catalog_item_id'],
				'filename' => $result[$this->alias]['filename'],
			);
		}
		$this->CatalogItem->saveAll($data);
	}
}