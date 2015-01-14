<?php
class CatalogItemsController extends ShopAppController {
	var $name = 'CatalogItems';
	var $components = array('Shop.ShoppingCart', 'Layout.Table');
	var $helpers = array(
		'Shop.CatalogItem', 
		'Layout.Crumbs' => array(
			'controllerCrumbs' => array(
				array('Online Store', array('action' => 'index'))
			),
		),
		//'Layout.DisplayText'
	);
	
	//var $uses = array('CatalogItem', 'OrderCatalogItem');

	var $rootCategoryId = 1;
	
	/*
	function beforeFilter() {
		parent::beforeFilter();
		if (!$this->LoggedUserTypes->check('admin')) {
			$this->offline = array(
				'title' => 'Store Offline',
				'content' => 'While we get the site up and running, the store is temporarily down. Check back soon!',
			);
		}	
	}
	*/
	
	function admin_test_send($invoiceId = null) {
		$msg = 'No Invoice ID detected';
		if (!empty($invoiceId)) {
			$invoice = $this->CatalogItem->Product->OrderProduct->Order->Invoice->find('first', array(
				'conditions' => array('Invoice.id' => $invoiceId)
			));
			App::uses('InvoiceEmail', 'Shop.Network/Email');
			$msg = 'No company emails';
			if (defined('COMPANY_ADMIN_EMAILS')) {
				$InvoiceEmail = new InvoiceEmail();
				$msg = 'Created InvoiceEmail Object';
				if ($InvoiceEmail->sendAdminPaid($invoice)) {
					$msg = 'Sent notification email to admins: ' . COMPANY_ADMIN_EMAILS;
				} else {
					$msg = 'Error sending notification email';
				}
			} else {
				$msg = 'No Admin Emails set, so nothing is being sent';
			}
		}
		$this->Session->setFlash($msg);
	}
	
	function index($categoryId = null) {
		$categoryId = $this->_getCatalogItemCategoryId($categoryId, false);
		
		// Catalog Layout
		$sessionName = 'CatalogItem.CatalogLayout';
		$catalogLayout = array('layout' => 'thumb', 'per_page' => 24);
		if ($this->Session->check($sessionName) && is_array($sessionDefault = $this->Session->read($sessionName))) {
			$catalogLayout = array_merge($catalogLayout, $sessionDefault);
		}
		if (!empty($this->request->data['CatalogItem'])) {
			$data = array_intersect_key($this->request->data['CatalogItem'], $catalogLayout);
			$catalogLayout = array_merge($catalogLayout, $data);
		}
		$this->Session->write($sessionName, $catalogLayout);

		// Catalog Layout Form
		$layouts = array('thumb' => 'Thumbnails', 'list' => 'List');
		$perPages = array(8, 12, 24);
		$perPages = array_combine($perPages, $perPages);
		$this->set(compact( 'layouts', 'perPages'));
		
		// Data
		$this->paginate = $this->_findOptions(array(
			'public' => 1,
			'limit' => $catalogLayout['per_page']
		), $categoryId);
		
		$catalogItems = $this->paginate('CatalogItem');
		$this->set(compact('catalogItems', 'catalogLayout'));
		$this->set('title_for_layout', 'Online Store Catalog');

		$this->_setCatalogItemCategories($categoryId);
	}
	
	function view ($id = null) {
		$catalogItem = $this->FormData->findModel($id, null, array(
			'contain' => array(
				'CatalogItemImage',
				'CatalogItemOption' => array('ProductOptionChoice'),
				'CatalogItemPackageChild' => array(
					'CatalogItemChild' => array('CatalogItemOption' => array('ProductOptionChoice'))
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
	
	function admin_index($categoryId = null) {
		//$this->CatalogItem->updateAllStock();
		$categoryId = $this->_getCatalogItemCategoryId($categoryId, true);
		
		$options = array();
		if (empty($categoryId)) {
			$options = array('conditions' => array('CatalogItem.active' => 1));
		}
		$options = $this->_findOptions($options, $categoryId, true);
		$options['order'] = array(
			'CatalogItem.active' => 'DESC',
			'CatalogItem.title' => 'ASC',
		);
		
		$this->paginate = $options;

		$catalogItems = $this->paginate();
		$this->set(compact('catalogItems'));
		$this->_setCatalogItemCategories($categoryId, true);
	}
	
	function admin_inactive($categoryId = null) {
		$categoryId = $this->_getCatalogItemCategoryId($categoryId, true);

		$options = $this->_findOptions(array('conditions' => array('CatalogItem.active' => 0)), $categoryId, true);
		$this->paginate = $options;
		
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
		$productInventoryAdjustments = $this->CatalogItem->Product->ProductInventoryAdjustment->find('all', array(
			'fields' => '*',
			'link' => array('Shop.Product' => array('Shop.CatalogItem')),
			'conditions' => array('CatalogItem.id' => $id),
			'limit' => 5,
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
		
		$this->set(compact('catalogItem', 'catalogItemCategories', 'productInventoryAdjustments'));
	}
	
	function admin_packages($id = null) {
		$this->FormData->editData($id, null, array(
			'contain' => array(
				'Product',
				'CatalogItemPackageChild' => array('CatalogItem')
			)
		));



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
	
	// Temporary function to copy souper bowl of caring old information to new system
	function admin_copy() {
		$PDO = new PDO('mysql:host=65.60.39.82;db=webdb;charset=utf8;', 'souper_remote', '1Fv5y4cc');
		$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$srcDb = 'souper_bowl'; //'webdb';
		$dstDb = 'souper_bowl_shop'; //'shop';
		$srcDb = 'webdb';
		$dstDb = 'shop';

		
		$tables = array(
			'product_categories' => 'catalog_item_categories',
			'product_categories_products' => array(
				'table' => 'catalog_item_categories_catalog_items',
				'fields' => array(
					'product_id' => 'catalog_item_id',
					'product_category_id' => 'catalog_item_category_id',
				)
			),
			//'catalog_item_category_links',
			'product_images' => array(
				'table' => 'catalog_item_images',
				'fields' => array('product_id' => 'catalog_item_id'),
			),
			'product_options' => array(
				'table' => 'catalog_item_options',
				'fields' => array('product_id' => 'catalog_item_id'),
			),
			'product_packages' => array(
				'table' => 'catalog_item_packages',
				'fields' => array('product_parent_id' => 'catalog_item_parent_id', 'product_child_id' => 'catalog_item_child_id'),
			),
			'product_option_choices' => array('fields' => array('product_option_id' => 'catalog_item_option_id')),
			'products' => 'catalog_items',

			'product_handlings' => 'handling_methods',
			'invoices',
			'invoice_payment_methods',
			'shop_orders' => array(
				'table' => 'orders',
				'fields' => array(
					'shop_order_product_count' => false,
					//'shop_order_shipping_id'//
					'shop_order_shipping_method_id' => 'shipping_method_id',
					'cancelled' => 'canceled',
					
				)
			),
			'product_handlings_shop_orders' => array(
				'table' => 'orders_handling_methods',
				'fields' => array(
					'product_handling_id' => 'handling_method_id',
					'shop_order_id' => 'order_id',
				),
			),
			'product_promos_shop_orders' => array(
				'table' => 'orders_promo_codes',
				'fields' => array(
					'product_promo_id' => 'promo_code_id',
					'shop_order_id' => 'order_id',
				),
			),
			'shop_order_products' => array(
				'table' => 'order_products',
				'fields' => array(
					'shop_order_id' => 'order_id',	//TODO: Update this?
					'product_id' => 'catalog_item_id',
					'product_option_choice_id_1' => false,
					'product_option_choice_id_2' => false,
					'product_option_choice_id_3' => false,
					'product_option_choice_id_4' => false,
				),
			),
			'product_shipping_rules_shop_order_products' => array(
				'table' => 'order_products_shipping_rules',
				'fields' => array(
					'product_shipping_rule_id' => 'shipping_rule_id',
					'shop_order_product_id' => 'order_product_id',
				)
			),
			'paypal_payments',
			//'products',
			//'product_inventories',
			'product_inventory_adjustments' => array('fields' => array('product_inventory_id' => 'product_id')),
			'product_promos' => 'promo_codes',
			'shop_order_shipping_classes' => 'shipping_classes',
			'shop_order_shipping_methods' => 'shipping_methods',
			'product_shipping_rules' => array(
				'table' => 'shipping_rules',
				'fields' => array(
					'product_id' => 'catalog_item_id',
					'shop_order_class_id' => 'order_class_id',
				)
			),
		);
		$log = array();
		$queries = array();
		foreach ($tables as $srcTable => $config) {
			if (is_numeric($srcTable)) {
				$srcTable = $config;
				$config = array();
			}
			if (!is_array($config)) {
				$config = array('table' => $config);
			}
			$config = array_merge(array(
				'table' => $srcTable,
				'fields' => array(),
			), $config);
			
			extract($config);
			
			$dst = "`$dstDb`.`$table`";
			$src = "`$srcDb`.`$srcTable`";
			$showQuery = "SHOW COLUMNS FROM $src";
			if (!($M = $PDO->query($showQuery))) {
				$log[] = "Source table $src not found";
				continue;
			}
			$srcFields = $dstFields = array();
			while($row = $M->fetch()) {
				$field = $row['Field'];
				if (isset($fields[$field]) && $fields[$field] === false) {
					continue;
				}
				$srcFields[] = $field;
				$dstFields[] = !empty($fields[$field]) ? $fields[$field] : $field;
			}
			$dstFields = '`' . implode('`, `', $dstFields) . '`';
			$srcFields = '`' . implode('`, `', $srcFields) . '`';
			try {
				$PDO->query("SELECT $srcFields FROM $src LIMIT 1");
			} catch (PDOException $e) {
				$log[] = "Source table $src not found";
				$log[] = $e->getMessage();
				continue;
			}
			try {
				$PDO->query("SELECT $dstFields FROM $dst LIMIT 1");
			} catch (PDOException $e) {
				$log[] = "Destination table $dst not found";
				$log[] = $e->getMessage();
				continue;
			}
			
			$q = "REPLACE INTO $dst ($dstFields) SELECT $srcFields FROM $src";
			$queries[$table] = $q;
		}
		debug(compact('queries'));
		foreach ($queries as $table => $q) {
			try {
				$PDO->query($q);
				if ($table == 'catalog_items') {
					$catalogItems = $this->CatalogItem->find('list');
					foreach ($catalogItems as $id => $title) {
						$this->CatalogItem->createProducts($id);
					}
				}
			} catch (PDOException $e) {
				$log[] = $e->getMessage();
				break;
			}
		}
		// Adjust Product IDs
		$PDO->query("UPDATE $dstDb.order_products AS OrderProduct
			JOIN $srcDb.shop_order_products AS ShopOrderProduct ON ShopOrderProduct.id = OrderProduct.id
			JOIN $dstDb.products AS Product ON Product.catalog_item_id = ShopOrderProduct.product_id AND ((
					(Product.product_option_choice_id_1 = ShopOrderProduct.product_option_choice_id_1) OR
					(Product.product_option_choice_id_1 IS NULL AND ShopOrderProduct.product_option_choice_id_1 IS NULL)
				) AND (
					(Product.product_option_choice_id_2 = ShopOrderProduct.product_option_choice_id_2) OR
					(Product.product_option_choice_id_2 IS NULL AND ShopOrderProduct.product_option_choice_id_2 IS NULL)
				) AND (
					(Product.product_option_choice_id_3 = ShopOrderProduct.product_option_choice_id_3) OR
					(Product.product_option_choice_id_3 IS NULL AND ShopOrderProduct.product_option_choice_id_3 IS NULL)
				) AND (
					(Product.product_option_choice_id_4 = ShopOrderProduct.product_option_choice_id_4) OR
					(Product.product_option_choice_id_4 IS NULL AND ShopOrderProduct.product_option_choice_id_4 IS NULL)
				)
			)
			SET OrderProduct.product_id = Product.id");

		$PDO->query("UPDATE $dstDb.product_inventory_adjustments AS InventoryAdjustment
			JOIN $srcDb.product_inventory_adjustments AS OldInventoryAdjustment ON InventoryAdjustment.id = OldInventoryAdjustment.id
			JOIN $srcDb.product_inventories AS ProductInventory ON ProductInventory.id = OldInventoryAdjustment.product_inventory_id
			JOIN $dstDb.products AS Product ON Product.catalog_item_id = ProductInventory.product_id AND ((
					(Product.product_option_choice_id_1 = ProductInventory.product_option_choice_id_1) OR
					(Product.product_option_choice_id_1 IS NULL AND ProductInventory.product_option_choice_id_1 IS NULL)
				) AND (
					(Product.product_option_choice_id_2 = ProductInventory.product_option_choice_id_2) OR
					(Product.product_option_choice_id_2 IS NULL AND ProductInventory.product_option_choice_id_2 IS NULL)
				) AND (
					(Product.product_option_choice_id_3 = ProductInventory.product_option_choice_id_3) OR
					(Product.product_option_choice_id_3 IS NULL AND ProductInventory.product_option_choice_id_3 IS NULL)
				) AND (
					(Product.product_option_choice_id_4 = ProductInventory.product_option_choice_id_4) OR
					(Product.product_option_choice_id_4 IS NULL AND ProductInventory.product_option_choice_id_4 IS NULL)
				)
			)
			SET InventoryAdjustment.product_id = Product.id, InventoryAdjustment.title = Product.title");
		$products = $this->CatalogItem->Product->find('list');
		foreach ($products as $productId => $productTitle) {
			$this->CatalogItem->Product->updateStock($productId);
		}
		debug(compact('log'));
		exit();
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
			'link' => array(
				'Shop.CatalogItemPackageParent' => array(
				'class' => 'Shop.CatalogItemPackage',
				'conditions' => array(
					'CatalogItemPackageParent.catalog_item_parent_id = CatalogItem.id',
				)
			)),
			'conditions' => array('CatalogItemPackageParent.id' => null),
			'order' => array('CatalogItem.active DESC', 'CatalogItem.title'),
		));
		$this->set(compact('packageChildren'));
	}

	function _findOptions($options = array(), $categoryId = null, $admin = false) {
		if ($categoryId = $this->_getCatalogItemCategoryId($categoryId, $admin)) {
			$options = array_merge(
				$this->CatalogItem->CatalogItemCategory->findCatalogItemsOptions($categoryId, true, false, $admin), 
				$options
			);
		}
		return $options;
	}
	
	function _getCatalogItemCategoryId($categoryId = null, $admin = false) {
		if (empty($categoryId) && !empty($this->request->params['named']['category'])) {
			$categoryId = $this->request->params['named']['category'];
		}

		$categoryId = $this->CatalogItem->CatalogItemCategory->checkScope($categoryId, $this->rootCategoryId);
		if (!$categoryId) {
			return $this->redirectMsg(array('action' => 'index'), 'Category not found', false);
		}

		return $categoryId;
	}
	
	function _setCatalogItemCategories($categoryId = null, $admin = false) {
		$categoryId = $this->_getCatalogItemCategoryId($categoryId, $admin);
		//Loads Category
		$catalogItemCategory = $this->CatalogItem->CatalogItemCategory->read(null, $categoryId);

		//Category List
		if ($admin) {
			$catalogItemCategories = $this->CatalogItem->CatalogItemCategory->find('all', array(
				'conditions' => array('CatalogItemCategory.parent_id' => $categoryId)
			));
		} else {
			$catalogItemCategories = $this->CatalogItem->CatalogItemCategory->findActiveCategories($categoryId);
		}
		
		//Category Path
		$catalogItemCategoryPath = $this->CatalogItem->CatalogItemCategory->getPath($categoryId, $this->rootCategoryId);
		
		if (!empty($catalogItemCategoryPath)) {
			$crumbs = array();
			foreach ($catalogItemCategoryPath as $row) {
				$row = $row['CatalogItemCategory'];
				if ($row['id'] == $this->rootCategoryId) {
					continue;
				}
				$crumbs[] = array($row['title'], array($row['id']));
			}
			$this->helpers['Layout.Crumbs']['actionCrumbs'] = $crumbs;
		}
		
		/*
		$catalogItemCategories = $this->CatalogItem->CatalogItemCategory->findChildren($categoryId, false, array(
			'CatalogItemCategory.active' => 1
		));
		*/
		return $this->set(compact('catalogItemCategory', 'catalogItemCategories', 'catalogItemCategoryPath'));
	}
}