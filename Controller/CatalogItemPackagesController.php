<?php
class CatalogItemPackagesController extends ShopAppController {
	public $name = 'CatalogItemPackages';
	public $components = ['FormData.Crud'];

	public function admin_add($catalogItemId) {
		$this->Crud->create([
			'default' => [
				'CatalogItemPackage' => [
					'catalog_item_parent_id' => $catalogItemId,
				]
			],
		]);
	}

	public function admin_view($id) {
		$result = $this->Crud->read($id);
		$this->redirect(['controller' => 'catalog_items', 'action' => 'view', $result['CatalogItemPackage']['catalog_item_parent_id']]);
	}

	public function admin_edit($id = null) {
		$this->Crud->update($id);
	}

	public function admin_delete($id = null) {
		$this->Crud->delete($id);
	}

	public function _setFormElements() {
		$CatalogItem = ClassRegistry::init('Shop.CatalogItem');
		$packageChildren = ['' => ' -- Package Content -- '] + $CatalogItem->find('list', [
			'link' => [
				'Shop.CatalogItemPackageParent' => [
				'class' => 'Shop.CatalogItemPackage',
				'conditions' => [
					'CatalogItemPackageParent.catalog_item_parent_id = CatalogItem.id',
				]
			]],
			'conditions' => ['CatalogItemPackageParent.id' => null],
			'order' => ['CatalogItem.active DESC', 'CatalogItem.title'],
		]);
		$this->set(compact('packageChildren'));

	}
}