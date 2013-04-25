<?php
class CatalogItemImage extends ShopAppModel {
	var $name = 'CatalogItemImage';
	var $actsAs = array(
		'Uploadable.ImageUploadable' => array(
			'plugin' => 'Shop',
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
	
	private $setThumbnail = false;
	
	function beforeSave() {
		$data =& $this->getData();
		if (isset($data) && empty($data['id']) && empty($data['add_file']['tmp_name'])) {
			unset($data['add_file']);
		}
		if (!empty($data['set_thumbnail'])) {
			$this->setThumbnail = true;
		}
		
		return parent::beforeSave();
	}
	
	function afterSave($created) {
		if ($this->setThumbnail) {
			$result = $this->read('catalog_item_id', $this->id);
			$this->setCatalogItemThumb($result[$this->alias]['catalog_item_id']);
		}
	}
	
	/**
	 * Sets the thumbnail for the CatalogItem as the first returned image in the set
	 *
	 **/
	function setCatalogItemThumb($catalogItemId = null) {
		$options = array(
			'group' => 'catalog_item_id'
		);
		if (!empty($catalogItemId)) {
			$options['catalog_item_id'] = $catalogItemId;
		}
		
		$results = $this->find('all', $options);
		$data = array();
		foreach ($results as $result) {
			$data[] = array(
				'id' => $result[$this->alias]['catalog_item_id'],
				'filename' => $result[$this->alias]['filename'],
			);
		}
		$this->CatalogItem->create();
		$this->CatalogItem->saveAll($data);
	}
}