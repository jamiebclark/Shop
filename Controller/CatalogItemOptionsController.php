<?php
class CatalogItemOptionsController extends ShopAppController {
	public $name = 'CatalogItemOptions';
	public $components = ['FormData.Crud'];

	public function admin_add($catalogItemId = null) {
		$this->Crud->create([
			'default' => [
				'CatalogItemOption' => [
					'catalog_item_id' => $catalogItemId,
				]
			]
		]);
	}

	public function admin_edit($id = null) {
		$this->Crud->update($id, [
			'query' => [
				'contain' => ['ProductOptionChoice'],
			]
		]);
	}

	public function admin_view($id = null) {
		$result = $this->Crud->read($id);
		$this->redirect([
			'controller' => 'catalog_items', 
			'action' => 'view', 
			$result['CatalogItemOption']['catalog_item_id']
		]);
	}

	public function admin_delete($id = null) {
		$this->Crud->delete($id);
	}
}