<?php
App::uses('ShopAppModel', 'Shop.Model');
class CatalogItemImage extends ShopAppModel {
	public $name = 'CatalogItemImage';
	public $actsAs = [
		'Shop.BlankDelete' => ['id', 'add_image'],
		'Uploadable.ImageUploadable' => [
			'plugin' => 'Shop',
			'upload_var' => 'add_image',
			//'bypass_is_uploaded' => true,
			'upload_dir' => 'img/catalog_item_images/',
			'update' => ['filename'],
			'dirs' => [
				'thumb' => ['setSoft' => [160, 160]],
				'mid' => ['setSoft' => [320, 320]],
				'' => ['max' => [600, 400]]
			]
		],
		//'Shop.BlankDelete' => ['title'],
		'Layout.Removable',
		'Shop.FieldOrder' => [
			'orderField' => 'order',
			'subKeyFields' => ['catalog_item_id'],
		]
	];
	public $order = ['CatalogItemImage.catalog_item_id', 'CatalogItemImage.order'];
	public $belongsTo = ['Shop.CatalogItem'];
	
	public $validate = [
		'catalog_item_id' => [
			'rule' => 'notBlank',
			'message' => 'Please select a product',
		]
	];
	
	public function afterSave($created, $options = []) {
		$id = $this->id;
		$result = $this->read(null, $id);
		if (!empty($result[$this->alias]['thumb'])) {
			$this->setThumbnail($this->id);
		}
		$result = $this->read(null, $id);
		return parent::afterSave($created);
	}

/**
 *	Sets the current image as the CatalogItem's default thumnail image
 *
 **/
	public function setThumbnail($id) {
		$result = $this->read(null, $id);
		$result = $result[$this->alias];
		$this->CatalogItem->save([
			'id' => $result['catalog_item_id'],
			'filename' => $result['filename'],
		], ['callbacks' => false, 'validate' => false]);
		return $this->updateAll(array(
			$this->escapeField('thumb') => 0,
		), array(
			$this->escapeField('catalog_item_id') => $result['catalog_item_id'],
			$this->escapeField($this->primaryKey) . ' <>' => $id
		));
	}
}