<?php
class CatalogItemsController extends ShopAppController {
	var $name = 'CatalogItems';
	var $components = array('Shop.ShoppingCart', 'Layout.Table');
	var $helpers = array(
		'Shop.CatalogItem', 
		//'Layout.DisplayText'
	);
	
	//var $uses = array('CatalogItem', 'OrderCatalogItem');
	
	/*
	function beforeFilter() {
		parent::beforeFilter();
		if (!$this->_loggedUserType('admin')) {
			$this->offline = array(
				'title' => 'Store Offline',
				'content' => 'While we get the site up and running, the store is temporarily down. Check back soon!',
			);
		}	
	}
	*/
	
	function _getCategoryId($categoryId, $rootCategoryId) {
		$categoryId = $this->CatalogItem->CatalogItemCategory->checkScope($categoryId, $rootCategoryId);
		if (!$categoryId) {
			return $this->_redirectMsg(array('action' => 'index'), 'Category not found', false);
		}
		return $categoryId;			
	}
	
	function index($categoryId = null) {
		$rootCategoryId = 1;
		
		//Filters Category
		$categoryId = $this->_getCategoryId($categoryId, $rootCategoryId);
		
		//Loads Category
		$catalogItemCategory = $this->CatalogItem->CatalogItemCategory->read(null, $categoryId);

		//Category List
		$catalogItemCategories = $this->CatalogItem->CatalogItemCategory->findActiveCategories($categoryId);
		
		//Category Path
		$catalogItemCategoryPath = $this->CatalogItem->CatalogItemCategory->getPath($categoryId, $rootCategoryId);
		
		/*
		$catalogItemCategories = $this->CatalogItem->CatalogItemCategory->findChildren($categoryId, false, array(
			'CatalogItemCategory.active' => 1
		));
		*/
		
		$this->paginate = $this->CatalogItem->CatalogItemCategory->findCatalogItemsOptions($categoryId);
		$catalogItems = $this->paginate();
		$this->set(compact('catalogItems', 'catalogItemCategory', 'catalogItemCategories', 'catalogItemCategoryPath'));
	}
	
	function view ($id = null) {
		$catalogItem = $this->FormData->findModel($id, null, array(
			'contain' => array(
				'CatalogItemImage',
				'CatalogItemOption' => array('ProductOptionChoice'),
				'CatalogItemPackageChild' => array(
					'CatalogItemChild' => array(
						'CatalogItemOption' => array('ProductOptionChoice'),
					)
				),
			)
		));
		//TODO: Temporary - Remove later
		$this->CatalogItem->createProducts($id);
		$this->CatalogItem->updateProductTitles($id);
		//

		//$catalogItemOptions = $this->CatalogItem->CatalogItemOption->findCatalogItemList($id);
		
		/*
		$catalogItem = $this->CatalogItem->postContain($catalogItem, array(
			'CatalogItemImage', 
			'CatalogItemPackageChild' => array(
				'link' => array(
					'CatalogItemChild' => array (
						'table' => 'catalog_items',
						'conditions' => array(
							'CatalogItemChild.id = CatalogItemPackageChild.catalog_item_child_id'
						)
					)
				)
			)
		));
		
		if (!empty($catalogItem['CatalogItemPackageChild'])) {
			$catalogItemChildOptions = array();
			foreach ($catalogItem['CatalogItemPackageChild'] as $catalogItemPackage) {
				$childId = $catalogItemPackage['CatalogItemChild']['id'];
				$catalogItemChildOptions[$childId] = $this->CatalogItem->CatalogItemOption->findCatalogItemOptions($childId);
			}
		}
		*/
		$catalogItemCategories = $this->CatalogItem->findCategories($id);
		$this->request->data['Order']['id'] = $this->ShoppingCart->getCart();
		$this->set(compact('catalogItem', 'catalogItemOptions', 'catalogItemChildOptions', 'catalogItemCategories'));
	}
	
	function admin_index() {
		$this->CatalogItem->updateAllStock();
		
		$this->paginate = array('conditions' => array('CatalogItem.active' => 1));
		$catalogItems = $this->paginate();
		$this->set(compact('catalogItems'));
	}
	
	function admin_inactive() {
		$this->paginate = array('conditions' => array('CatalogItem.active' => 0));
		$catalogItems = $this->paginate();
		$this->set(compact('catalogItems'));
	}

	function admin_add() {
		$this->FormData->addData(array('default' => array('CatalogItem' => array('active' => 1))));
	}
	
	function admin_edit($id = null) {
		$result = $this->FormData->editData($id, null, array(
			'contain' => array(
				'CatalogItemPackageChild',
				'CatalogItemCategory',
				'CatalogItemImage', 
				'ShippingRule',
				'CatalogItemOption' => array('ProductOptionChoice'),
			)
		), null, array('deep' => true));
	}

	function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	function admin_view($id = null) {
		$catalogItem = $this->FormData->findModel($id, null, array(
			'contain' => array(
				'CatalogItemPackageChild' => array(
					'CatalogItemChild'
				),
				'CatalogItemOption' => array('ProductOptionChoice'),
				'CatalogItemImage', 
				'ShippingRule',
				'CatalogItemCategory',
			)
		));
		/*
		$catalogItem = $this->CatalogItem->findById($id);
		$catalogItem = $this->CatalogItem->postContain($catalogItem, array(
			'CatalogItemPackageChild' => array(
				'link' => array(
					'Shop.CatalogItemChild' => array(
						'conditions' => array(
							'CatalogItemChild.id = CatalogItemPackageChild.catalog_item_child_id'
						)
					)
				)
			),
			'CatalogItemOption' => array('ProductOptionChoice'),
			'CatalogItemImage', 
			'ShippingRule',
			'CatalogItemCategory',
		));
		*/
		$catalogItemCategories = $this->CatalogItem->findCategories($id);
		
		$this->set(compact('catalogItem', 'catalogItemCategories'));
	}
	
	function admin_packages($id = null) {
		if ($this->_saveData() === null) {
			$this->request->data = $this->CatalogItem->find('first', array(
				'postContain' => array(
					'CatalogItemPackageChild' => array(
						'link' => array(
							'Shop.CatalogItemChild' => array(
								'conditions' => 'CatalogItemChild.id = CatalogItemPackageChild.catalog_item_child_id'
							)
						)
					)
				),
				'conditions' => array('CatalogItem.id' => $id)
			));
		}
		$this->set('catalogItems', $this->CatalogItem->selectList());
	}
	
	function admin_shipping_rules($id = null) {
		$this->FormData->editData($id, null, array('contain' => array('ShippingRule')));
		//$this->set('catalogItems', $this->CatalogItem->selectList());
	}

	function admin_totals() {
		$monthShift = 2;
		
		if (!empty($monthShift)) {
			$orderYearField = 'IF(MONTH(Invoice.paid) > 2 , YEAR(Invoice.paid) + 1, YEAR(Invoice.paid)) AS order_year';
		} else {
			$orderYearField = 'Invoice.paid';
		}
		
		$options = array(
			'fields' => array(
				'CatalogItem.*',
				'Product.id',
				'Product.sub_title',
				'COUNT(CatalogItem.id) AS catalog_item_count',
				'MONTH(Invoice.paid) AS order_month',
				$orderYearField,
			),
			'link' => array(
				'Shop.Product' => array(
					'Shop.OrderProduct' => array(
						'Shop.Order' => array('Shop.Invoice')
					)
				)
			),
			'conditions' => array('Invoice.paid <>' => null),
			'group' => array(
				'order_year',
				'order_month',
				'Product.id',
			),
			'order' => array(
				'order_year DESC',
				'order_month',
				'CatalogItem.title',
			),
		);
		$result = $this->CatalogItem->find('all', $options);
		//debug($result);
		$totals = array();
		$totalsOptions = array();
		
		$catalogItems = array();
		$products = array();
		
		foreach ($result as $row) {
			$year = $row[0]['order_year'];
			$month = $row[0]['order_month'];
			$count = $row[0]['catalog_item_count'];
			
			$catalogItem = $row['CatalogItem'];
			$catalogItemId = $catalogItem['id'];

			$productId = $row['Product']['id'];
			
			$catalogItems[$catalogItemId] = $catalogItem;
			$products[$catalogItemId][$productId] = $row['Product']['sub_title'];
			
			if (empty($totals['total'][$catalogItemId])) {
				$totals['total'][$catalogItemId] = 0;
			}
			if (empty($totals['year'][$year][$catalogItemId])) {
				$totals['year'][$year][$catalogItemId] = 0;
			}
			if (empty($totals['month'][$year][$month][$catalogItemId])) {
				$totals['month'][$year][$month][$catalogItemId] = 0;
			}
			
			$totals['total'][$catalogItemId] += $count;
			$totals['year'][$year][$catalogItemId] += $count;
			$totals['month'][$year][$month][$catalogItemId] += $count;
			
			if (empty($totalsOptions['total'][$catalogItemId][$productId])) {
				$totalsOptions['total'][$catalogItemId][$productId] = 0;
			}
			if (empty($totalsOptions['year'][$year][$catalogItemId][$productId])) {
				$totalsOptions['year'][$year][$catalogItemId][$productId] = 0;
			}
			if (empty($totalsOptions['month'][$year][$month][$catalogItemId][$productId])) {
				$totalsOptions['month'][$year][$month][$catalogItemId][$productId] = 0;
			}
			$totalsOptions['total'][$catalogItemId][$productId] += $count;
			$totalsOptions['year'][$year][$catalogItemId][$productId] += $count;
			$totalsOptions['month'][$year][$month][$catalogItemId][$productId] += $count;

		}
		$this->set(compact('totals', 'totalsOptions', 'catalogItems', 'products', 'monthShift'));
	}
	/*
	function ajax_product_option_select($id = null, $key = null) {
		$productOptions = $this->Product->ProductOption->find('all', array(
			'fields' => '*',
			'link' => array('Product'),
			'postContain' => array('ProductOptionChoice'),
			'conditions' => array(
				'Product.id' => $id,
			)
		));
		debug($productOptions);
		$this->set(compact('productOptions', 'key'));
	}
	*/
	function _setFormElements() {
		$this->set('catalogItemCategories', $this->CatalogItem->CatalogItemCategory->selectList());
		$packageChildren = array('' => ' -- Package Content -- ') + $this->CatalogItem->find('list', array(
			'link' => array('Shop.CatalogItemPackageParent' => array(
				'table' => 'catalog_item_packages',
				'conditions' => array(
					'CatalogItemPackageParent.catalog_item_parent_id = CatalogItem.id',
				)
			)),
			'conditions' => array('CatalogItemPackageParent.id' => null),
			'order' => array('CatalogItem.active DESC', 'CatalogItem.title'),
		));
		$this->set(compact('packageChildren'));
	}
}
