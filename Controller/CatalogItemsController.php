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
		if (empty($id) && !empty($this->request->query['prodID'])) {
			$id = $this->request->query['prodID'];
		}
		
		$catalogItem = $this->FormData->findModel($id);
		
		if (empty($catalogItem)) {
			$this->_redirectMsg(true, 'CatalogItem not found');
		}
		$catalogItemOptions = $this->CatalogItem->CatalogItemOption->findCatalogItemOptions($id);
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
		$this->request->data['Order']['id'] = $this->ShoppingCart->getCart();
		$this->set(compact('catalogItem', 'catalogItemOptions', 'catalogItemChildOptions'));
	}
	
	function staff_index() {
		$this->paginate = array(
			'conditions' => array(
				'CatalogItem.active' => 1
			)
		);
		$catalogItems = $this->paginate();
		$this->set(compact('catalogItems'));
	}
	
	function staff_inactive() {
		$this->paginate = array(
			'conditions' => array(
				'CatalogItem.active' => 0
			)
		);
		$catalogItems = $this->paginate();
		$this->set(compact('catalogItems'));
	}

	function staff_add() {
		$this->_saveData();
		$this->set('catalogItemCategories', $this->CatalogItem->CatalogItemCategory->selectList());
	}
	
	function staff_edit($id = null) {
		if (!$this->_saveData()) {
			$this->request->data = $this->CatalogItem->find('first', array(
				'postContain' => array('CatalogItemCategory'),
				'conditions' => array(
					'CatalogItem.id' => $id
				)
			));
		}
		$this->set('catalogItemCategories', $this->CatalogItem->CatalogItemCategory->selectList());
	}
	
	function staff_delete($id = null) {
		$this->_deleteData($id);
	}
	
	function staff_view($id = null) {
		$catalogItem = $this->CatalogItem->findById($id);
		$catalogItem = $this->CatalogItem->postContain($catalogItem, array(
			'CatalogItemPackageChild' => array(
				'link' => array(
					'CatalogItemChild' => array(
						'class' => 'CatalogItem',
						'conditions' => array(
							'CatalogItemChild.id = CatalogItemPackageChild.catalog_item_child_id'
						)
					)
				)
			),
			'CatalogItemImage', 
			'CatalogItemShippingRule',
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
		if (!$this->_saveData()) {
			$this->request->data = $this->CatalogItem->find('first', array(
				'contain' => array('CatalogItemShippingRule'),
				'conditions' => array(
					'CatalogItem.id' => $id
				)
			));
		}
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
				'Product.title',
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
		$result = $this->Product->find('all', $options);
		//debug($result);
		$totals = array();
		$totalsOptions = array();
		
		$calendarItems = array();
		$products = array();
		
		foreach ($result as $row) {
			$year = $row[0]['order_year'];
			$month = $row[0]['order_month'];
			$count = $row[0]['catalog_item_count'];
			
			$calendarItem = $row['CalendarItem'];
			$calendarItemId = $calendarItem['id'];

			$productId = $row['Product']['id'];
			
			$calendarItems[$calendarItemId] = $calendarItem['title'];
			$products[$calendarItem][$productId] = $row['Product']['title'];
			
			if (empty($totals['total'][$calendarItemId])) {
				$totals['total'][$calendarItemId] = 0;
			}
			if (empty($totals['year'][$year][$calendarItemId])) {
				$totals['year'][$year][$calendarItemId] = 0;
			}
			if (empty($totals['month'][$year][$month][$calendarItemId])) {
				$totals['month'][$year][$month][$calendarItemId] = 0;
			}
			
			$totals['total'][$calendarItemId] += $count;
			$totals['year'][$year][$calendarItemId] += $count;
			$totals['month'][$year][$month][$calendarItemId] += $count;
			
			if ($productOptionId != $productId) {
				if (empty($totalsOptions['total'][$calendarItemId][$productId])) {
					$totalsOptions['total'][$calendarItemId][$productId] = 0;
				}
				if (empty($totalsOptions['year'][$year][[$calendarItemId][$productId])) {
					$totalsOptions['year'][$year][$calendarItemId][$productId] = 0;
				}
				if (empty($totalsOptions['month'][$year][$month][$calendarItemId][$productId])) {
					$totalsOptions['month'][$year][$month][$calendarItemId][$productId] = 0;
				}
				$totalsOptions['total'][$calendarItemId][$productId] += $count;
				$totalsOptions['year'][$year][$calendarItemId][$productId] += $count;
				$totalsOptions['month'][$year][$month][$calendarItemId][$productId] += $count;
			}

		}
		$this->set(compact('totals', 'totalsOptions', 'calendarItems', 'products', 'monthShift'));
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
}
