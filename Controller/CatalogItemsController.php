<?php
class CatalogItemsController extends ShopAppController {
	var $name = 'CatalogItems';
	var $components = array('Shop.ShoppingCart');
	var $helpers = array(
		'Shop.CatalogItem', 
		//'Layout.DisplayText'
	);
	
	//var $uses = array('CatalogItem', 'OrderCatalogItem');
	
	/*
	function beforeFilter() {
		parent::beforeFilter();
		if (!$this->_loggedUserType('staff')) {
			$this->offline = array(
				'title' => 'Store Offline',
				'content' => 'While we get the site up and running, the store is temporarily down. Check back soon!',
			);
		}	
	}
	*/
	
	function index($categoryId = null) {
		//Filters Category
		if (empty($categoryId)) {
			if (empty($this->findFilterVal)) {
				$this->findFilterVal = array();
			}
			$categoryId = Param::keyCheck($this->findFilterVal, 'category', false, 1);	//Defaults to Category ID 1
		} else if (!is_numeric($categoryId)) {
			$categoryId = $this->CatalogItem->CatalogItemCategory->find('list', array(
				'fields' => array('CatalogItemCategory.id', 'CatalogItemCategory.id'),
				'conditions' => array('CatalogItemCategory.slug LIKE' => $categoryId),
				'userType' => $this->_loggedUserTypes(),
			));
			$categoryId = array_pop($categoryId);
		}
		
		//Loads Category
		$catalogItemCategory = $this->CatalogItem->CatalogItemCategory->read(null, $categoryId);
		$catalogItemCategoryLeft = $catalogItemCategory['CatalogItemCategory']['lft'];
		$catalogItemCategoryRight = $catalogItemCategory['CatalogItemCategory']['rght'];
		
		//Category List
		$catalogItemCategories = $this->CatalogItem->CatalogItemCategory->findActiveCategories($categoryId);
		
		//Category Path
		$catalogItemCategoryPath = $this->CatalogItem->CatalogItemCategory->getPath($categoryId);
		
		/*
		$catalogItemCategories = $this->CatalogItem->CatalogItemCategory->findChildren($categoryId, false, array(
			'CatalogItemCategory.active' => 1
		));
		*/
		
		$this->paginate = array(
			'link' => array(
				'CatalogItemCategory',
			),
			'conditions' => array(
				'CatalogItem.active' => 1,
				'CatalogItem.hidden' => 0,
				'CatalogItemCategory.lft BETWEEN ? AND ?' => array($catalogItemCategoryLeft, $catalogItemCategoryRight),
			),
			'group' => 'CatalogItem.id',
		);
		$catalogItems = $this->paginate();
		$this->set(compact('catalogItems', 'catalogItemCategory', 'catalogItemCategories', 'catalogItemCategoryPath'));
	}
	
	function view($id = null) {
		//Temporary - Remove later
		$this->CatalogItem->createProducts($id);
		$this->CatalogItem->updateProductTitles($id);
		//
		
		$catalogItem = $this->FormData->findModel($id, null, array(
			'contain' => array(
				'CatalogItemPackageChild',
				'CatalogItemImage',
			)
		));
		$catalogItemOptions = $this->CatalogItem->CatalogItemOption->findCatalogItemList($id);
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
		$this->request->data['Order']['id'] = $this->ShoppingCart->getCart();
		$this->set(compact('catalogItem', 'catalogItemOptions', 'catalogItemChildOptions'));
	}
	
	function staff_index() {
		$this->paginate = array('conditions' => array('CatalogItem.active' => 1));
		$catalogItems = $this->paginate();
		$this->set(compact('catalogItems'));
	}
	
	function staff_inactive() {
		$this->paginate = array('conditions' => array('CatalogItem.active' => 0));
		$catalogItems = $this->paginate();
		$this->set(compact('catalogItems'));
	}

	function staff_add() {
		$this->FormData->addData(array('default' => array('CatalogItem' => array('active' => 1))));
	}
	
	function staff_edit($id = null) {
		$this->FormData->editData($id, null, array('contain' => 'CatalogItemCategory'));
	}

	function staff_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	function staff_view($id = null) {
		$catalogItem = $this->CatalogItem->findById($id);
		$catalogItem = $this->CatalogItem->postContain($catalogItem, array(
			'CatalogItemPackageChild' => array(
				'link' => array(
					'CatalogItemChild' => array(
						'class' => 'Shop.CatalogItem',
						'conditions' => array(
							'CatalogItemChild.id = CatalogItemPackageChild.catalog_item_child_id'
						)
					)
				)
			),
			'CatalogItemImage', 
			'ShippingRule',
			'CatalogItemCategory',
		));
		$this->set(compact('catalogItem'));
	}
	
	function staff_packages($id = null) {
		if ($this->_saveData() === null) {
			$this->request->data = $this->CatalogItem->find('first', array(
				'postContain' => array(
					'CatalogItemPackageChild' => array(
						'link' => array(
							'CatalogItemChild' => array(
								'conditions' => 'CatalogItemChild.id = CatalogItemPackageChild.catalog_item_child_id'
							)
						)
					)
				),
				'conditions' => array(
					'CatalogItem.id' => $id
				)
			));
		}
		$this->set('catalogItems', $this->CatalogItem->selectList());
	}
	
	function staff_shipping_rules($id = null) {
		$this->FormData->editData($id, null, array('contain' => array('ShippingRule')));
		//$this->set('catalogItems', $this->CatalogItem->selectList());
	}

	function staff_totals() {
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
	}
}
