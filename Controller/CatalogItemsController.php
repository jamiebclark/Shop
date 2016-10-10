<?php
class CatalogItemsController extends ShopAppController {
	public $name = 'CatalogItems';
	public $components = [
		'Shop.ShoppingCart', 
		'Layout.Table',
		'FormData.RemoveBlankData' => [
			'Shop.CatalogItemOption' => ['title'],
			'Shop.CatalogItemPackage' => [
				'or' => ['package_id', 'qty'],
			]
		],
	];
	
	public $helpers = [
		'Shop.CatalogItem', 
		'Layout.Crumbs' => [
			'controllerCrumbs' => [
				['Online Store', ['action' => 'index']]
			],
		],
		//'Layout.DisplayText'
	];
	
	//var $uses = ['CatalogItem', 'OrderCatalogItem'];

	var $rootCategoryId = 1;
	
	/*
	public function beforeFilter() {
		parent::beforeFilter();
		if (!$this->LoggedUserTypes->check('admin')) {
			$this->offline = [
				'title' => 'Store Offline',
				'content' => 'While we get the site up and running, the store is temporarily down. Check back soon!',
			];
		}	
	}
	*/
	
	public function admin_test_send($invoiceId = null) {
		$msg = 'No Invoice ID detected';
		if (!empty($invoiceId)) {
			$invoice = $this->CatalogItem->Product->OrderProduct->Order->Invoice->find('first', [
				'conditions' => ['Invoice.id' => $invoiceId]
			]);
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
		$this->Flash->alert($msg);
	}
	
	public function index($categoryId = null) {
		$categoryId = $this->_getCatalogItemCategoryId($categoryId, false);
		
		// Catalog Layout
		$sessionName = 'CatalogItem.CatalogLayout';
		$catalogLayout = ['layout' => 'thumb', 'per_page' => 24];
		if ($this->Session->check($sessionName) && is_array($sessionDefault = $this->Session->read($sessionName))) {
			$catalogLayout = array_merge($catalogLayout, $sessionDefault);
		}
		if (!empty($this->request->data['CatalogItem'])) {
			$data = array_intersect_key($this->request->data['CatalogItem'], $catalogLayout);
			$catalogLayout = array_merge($catalogLayout, $data);
		}
		$this->Session->write($sessionName, $catalogLayout);

		// Catalog Layout Form
		$layouts = ['thumb' => 'Thumbnails', 'list' => 'List'];
		$perPages = [8, 12, 24];
		$perPages = array_combine($perPages, $perPages);
		$this->set(compact( 'layouts', 'perPages'));
		
		// Data
		$this->paginate = $this->_findOptions([
			'public' => 1,
			'limit' => $catalogLayout['per_page']
		], $categoryId);
		
		$catalogItems = $this->paginate('CatalogItem');
		$this->set(compact('catalogItems', 'catalogLayout'));
		$this->set('title_for_layout', 'Online Store Catalog');

		$this->_setCatalogItemCategories($categoryId);
	}
	
	public function view ($id = null) {
		$catalogItem = $this->FormData->findModel($id, null, [
			'contain' => [
				'CatalogItemImage',
				'CatalogItemOption' => ['ProductOptionChoice'],
				'CatalogItemPackageChild' => [
					'CatalogItemChild' => ['CatalogItemOption' => ['ProductOptionChoice']]
				],
			]
		]);
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
						'conditions' => [
							'CatalogItemChild.id = CatalogItemPackageChild.catalog_item_child_id'
						]
					)
				)
			)
		));
		
		if (!empty($catalogItem['CatalogItemPackageChild'])) {
			$catalogItemChildOptions = [];
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
	
	public function admin_index($categoryId = null) {
		//$this->CatalogItem->updateAllStock();
		$categoryId = $this->_getCatalogItemCategoryId($categoryId, true);
		
		$options = [];
		if (empty($categoryId)) {
			$options = ['conditions' => ['CatalogItem.active' => 1]];
		}
		$options = $this->_findOptions($options, $categoryId, true);
		$options['order'] = [
			'CatalogItem.active' => 'DESC',
			'CatalogItem.title' => 'ASC',
		];
		
		$this->paginate = $options;

		$catalogItems = $this->paginate();
		$this->set(compact('catalogItems'));
		$this->_setCatalogItemCategories($categoryId, true);
	}
	
	public function admin_inactive($categoryId = null) {
		$categoryId = $this->_getCatalogItemCategoryId($categoryId, true);

		$options = $this->_findOptions(['conditions' => ['CatalogItem.active' => 0]], $categoryId, true);
		$this->paginate = $options;
		
		$catalogItems = $this->paginate();
		$this->set(compact('catalogItems'));
	}

	public function admin_add() {
		$this->FormData->addData(['default' => ['CatalogItem' => ['active' => 1]]]);
	}
	
	public function admin_edit($id = null) {

		if (!empty($this->request->data)) {
			$data =& $this->request->data;
			if (!empty($data['CatalogItemImage'])) {
				foreach ($data['CatalogItemImage'] as $k => $catalogItemImage) {
					if (empty($catalogItemImage['filename']) && empty($catalogItemImage['add_file']['tmp_name'])) {
						if (!empty($catalogItemImage['id'])) {
							$this->CatalogItem->CatalogItemImage->delete($catalogItemImage['id']);
						}
						unset($data['CatalogItemImage'][$k]);
					}
				}
			}
		}

		$result = $this->FormData->editData($id, null, [
			'contain' => [
				'CatalogItemPackageChild',
				'CatalogItemCategory',
				'CatalogItemImage', 
				'ShippingRule',
				'CatalogItemOption' => ['ProductOptionChoice'],
			]
		], null, ['deep' => true]);
	}

	public function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	public function admin_view($id = null) {
		$catalogItem = $this->FormData->findModel($id, null, [
			'contain' => [
				'CatalogItemPackageChild' => [
					'CatalogItemChild'
				],
				'CatalogItemOption' => ['ProductOptionChoice'],
				'ShippingRule',
				'CatalogItemCategory',
			]
		]);
		$catalogItemImages = $this->CatalogItem->CatalogItemImage->find('all', [
			'conditions' => [
				'CatalogItemImage.catalog_item_id' => $id,
			]
		]);

		$productInventoryAdjustments = $this->CatalogItem->Product->ProductInventoryAdjustment->find('all', [
			'fields' => '*',
			'link' => ['Shop.Product' => ['Shop.CatalogItem']],
			'conditions' => ['CatalogItem.id' => $id],
			'limit' => 5,
		]);
		/*
		$catalogItem = $this->CatalogItem->findById($id);
		$catalogItem = $this->CatalogItem->postContain($catalogItem, [
			'CatalogItemPackageChild' => [
				'link' => [
					'Shop.CatalogItemChild' => [
						'conditions' => [
							'CatalogItemChild.id = CatalogItemPackageChild.catalog_item_child_id'
						]
					]
				]
			],
			'CatalogItemOption' => ['ProductOptionChoice'],
			'CatalogItemImage', 
			'ShippingRule',
			'CatalogItemCategory',
		]);
		*/
		$catalogItemCategories = $this->CatalogItem->findCategories($id);
		
		$this->set(compact('catalogItem', 'catalogItemImages', 'catalogItemCategories', 'productInventoryAdjustments'));
	}
	
	public function admin_packages($id = null) {
		$this->FormData->editData($id, null, [
			'contain' => [
				'Product',
				'CatalogItemPackageChild' => ['CatalogItem']
			]
		]);



		$this->set('catalogItems', $this->CatalogItem->selectList());
	}
	
	public function admin_shipping_rules($id = null) {
		$this->FormData->editData($id, null, ['contain' => ['ShippingRule']]);
		//$this->set('catalogItems', $this->CatalogItem->selectList());
	}

	public function admin_totals() {
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
			'link' => [
				'Shop.Product' => [
					'Shop.OrderProduct' => [
						'Shop.Order' => ['Shop.Invoice']
					]
				]
			],
			'conditions' => ['Invoice.paid <>' => null],
			'group' => [
				'order_year',
				'order_month',
				'Product.id',
			],
			'order' => [
				'order_year DESC',
				'order_month',
				'CatalogItem.title',
			],
		);
		$result = $this->CatalogItem->find('all', $options);
		//debug($result);
		$totals = [];
		$totalsOptions = [];
		
		$catalogItems = [];
		$products = [];
		
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
	public function admin_copy() {
		$PDO = new PDO('mysql:host=65.60.39.82;db=webdb;charset=utf8;', 'souper_remote', '1Fv5y4cc');
		$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$srcDb = 'souper_bowl'; //'webdb';
		$dstDb = 'souper_bowl_shop'; //'shop';
		$srcDb = 'webdb';
		$dstDb = 'shop';

		
		$tables = [
			'product_categories' => 'catalog_item_categories',
			'product_categories_products' => [
				'table' => 'catalog_item_categories_catalog_items',
				'fields' => [
					'product_id' => 'catalog_item_id',
					'product_category_id' => 'catalog_item_category_id',
				]
			],
			//'catalog_item_category_links',
			'product_images' => [
				'table' => 'catalog_item_images',
				'fields' => ['product_id' => 'catalog_item_id'],
			],
			'product_options' => [
				'table' => 'catalog_item_options',
				'fields' => ['product_id' => 'catalog_item_id'],
			],
			'product_packages' => [
				'table' => 'catalog_item_packages',
				'fields' => ['product_parent_id' => 'catalog_item_parent_id', 'product_child_id' => 'catalog_item_child_id'],
			],
			'product_option_choices' => ['fields' => ['product_option_id' => 'catalog_item_option_id']],
			'products' => 'catalog_items',

			'product_handlings' => 'handling_methods',
			'invoices',
			'invoice_payment_methods',
			'shop_orders' => [
				'table' => 'orders',
				'fields' => [
					'shop_order_product_count' => false,
					//'shop_order_shipping_id'//
					'shop_order_shipping_method_id' => 'shipping_method_id',
					'cancelled' => 'canceled',
					
				]
			],
			'product_handlings_shop_orders' => [
				'table' => 'orders_handling_methods',
				'fields' => [
					'product_handling_id' => 'handling_method_id',
					'shop_order_id' => 'order_id',
				],
			],
			'product_promos_shop_orders' => [
				'table' => 'orders_promo_codes',
				'fields' => [
					'product_promo_id' => 'promo_code_id',
					'shop_order_id' => 'order_id',
				],
			],
			'shop_order_products' => [
				'table' => 'order_products',
				'fields' => [
					'shop_order_id' => 'order_id',	//TODO: Update this?
					'product_id' => 'catalog_item_id',
					'product_option_choice_id_1' => false,
					'product_option_choice_id_2' => false,
					'product_option_choice_id_3' => false,
					'product_option_choice_id_4' => false,
				],
			],
			'product_shipping_rules_shop_order_products' => [
				'table' => 'order_products_shipping_rules',
				'fields' => [
					'product_shipping_rule_id' => 'shipping_rule_id',
					'shop_order_product_id' => 'order_product_id',
				]
			],
			'paypal_payments',
			//'products',
			//'product_inventories',
			'product_inventory_adjustments' => ['fields' => ['product_inventory_id' => 'product_id']],
			'product_promos' => 'promo_codes',
			'shop_order_shipping_classes' => 'shipping_classes',
			'shop_order_shipping_methods' => 'shipping_methods',
			'product_shipping_rules' => [
				'table' => 'shipping_rules',
				'fields' => [
					'product_id' => 'catalog_item_id',
					'shop_order_class_id' => 'order_class_id',
				]
			],
		];
		$log = [];
		$queries = [];
		foreach ($tables as $srcTable => $config) {
			if (is_numeric($srcTable)) {
				$srcTable = $config;
				$config = [];
			}
			if (!is_array($config)) {
				$config = ['table' => $config];
			}
			$config = array_merge([
				'table' => $srcTable,
				'fields' => [],
			], $config);
			
			extract($config);
			
			$dst = "`$dstDb`.`$table`";
			$src = "`$srcDb`.`$srcTable`";
			$showQuery = "SHOW COLUMNS FROM $src";
			if (!($M = $PDO->query($showQuery))) {
				$log[] = "Source table $src not found";
				continue;
			}
			$srcFields = $dstFields = [];
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
	public function ajax_product_option_select($id = null, $key = null) {
		$productOptions = $this->Product->ProductOption->find('all', [
			'fields' => '*',
			'link' => ['Product'],
			'postContain' => ['ProductOptionChoice'],
			'conditions' => [
				'Product.id' => $id,
			]
		]);
		debug($productOptions);
		$this->set(compact('productOptions', 'key'));
	}
	*/
	public function _setFormElements() {
		$this->set('catalogItemCategories', $this->CatalogItem->CatalogItemCategory->selectList());
	}

	public function _findOptions($options = [], $categoryId = null, $admin = false) {
		if ($categoryId = $this->_getCatalogItemCategoryId($categoryId, $admin)) {
			$options = array_merge(
				$this->CatalogItem->CatalogItemCategory->findCatalogItemsOptions($categoryId, true, false, $admin), 
				$options
			);
		}
		return $options;
	}
	
	public function _getCatalogItemCategoryId($categoryId = null, $admin = false) {
		if (empty($categoryId) && !empty($this->request->params['named']['category'])) {
			$categoryId = $this->request->params['named']['category'];
		}

		$categoryId = $this->CatalogItem->CatalogItemCategory->checkScope($categoryId, $this->rootCategoryId);
		if (!$categoryId) {
			return $this->redirectMsg(['action' => 'index'], 'Category not found', false);
		}

		return $categoryId;
	}
	
	public function _setCatalogItemCategories($categoryId = null, $admin = false) {
		$categoryId = $this->_getCatalogItemCategoryId($categoryId, $admin);
		//Loads Category
		$catalogItemCategory = $this->CatalogItem->CatalogItemCategory->read(null, $categoryId);

		//Category List
		if ($admin) {
			$catalogItemCategories = $this->CatalogItem->CatalogItemCategory->find('all', [
				'conditions' => ['CatalogItemCategory.parent_id' => $categoryId]
			]);
		} else {
			$catalogItemCategories = $this->CatalogItem->CatalogItemCategory->findActiveCategories($categoryId);
		}
		
		//Category Path
		$catalogItemCategoryPath = $this->CatalogItem->CatalogItemCategory->getPath($categoryId, $this->rootCategoryId);
		
		if (!empty($catalogItemCategoryPath)) {
			$crumbs = [];
			foreach ($catalogItemCategoryPath as $row) {
				$row = $row['CatalogItemCategory'];
				if ($row['id'] == $this->rootCategoryId) {
					continue;
				}
				$crumbs[] = [$row['title'], [$row['id']]];
			}
			$this->helpers['Layout.Crumbs']['actionCrumbs'] = $crumbs;
		}
		
		/*
		$catalogItemCategories = $this->CatalogItem->CatalogItemCategory->findChildren($categoryId, false, [
			'CatalogItemCategory.active' => 1
		]);
		*/
		return $this->set(compact('catalogItemCategory', 'catalogItemCategories', 'catalogItemCategoryPath'));
	}
}