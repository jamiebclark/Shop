<?php
class CatalogItemImage extends ShopAppModel {
	var $name = 'CatalogItemImage';
	var $actsAs = array(
		'Shop.BlankDelete' => array('id', 'add_file'),
		'Uploadable.ImageUploadable' => array(
			'plugin' => 'Shop',
			//'bypass_is_uploaded' => true,
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
		//'Shop.BlankDelete' => array('title'),
		'Layout.Removable',
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
		$result = $this->read(null, $this->id);
		if (!empty($result[$this->alias]['thumb'])) {
			$this->setThumbnail($this->id);
		}
		return parent::afterSave($created);
	}

/**
 *	Sets the current image as the CatalogItem's default thumnail image
 *
 **/
	function setThumbnail($id) {
		$result = $this->read(null, $id);
		$result = $result[$this->alias];
		$this->CatalogItem->save(array(
			'id' => $result['catalog_item_id'],
			'filename' => $result['filename'],
		), array('callbacks' => false, 'validate' => false));
		return $this->updateAll(array(
			$this->escapeField('thumb') => 0,
		), array(
			$this->escapeField('catalog_item_id') => $result['catalog_item_id'],
			$this->escapeField($this->primaryKey) . ' <>' => $id
		));
	}
}