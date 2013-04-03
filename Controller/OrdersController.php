<?php
class OrdersController extends ShopAppController {
	var $name = 'Orders';
	
	var $components = array(		//'FindFilter', 		'Shop.ShoppingCart'	);	
	var $helpers = array(
		'Shop.Invoice',
		'Shop.CatalogItem', 
		'Shop.PaypalForm',
	);
	
	var $paginate = array(
		'fields' => '*',
		'link' => array('Shop.Invoice'),
	);
		/*
	function beforeFilter() {
		parent::beforeFilter();
		$this->FindFilter->filter = array(
			'shipped' => array('options' => array('' => ' -- Either -- ', 1 => 'Shipped', 0 => 'Not Shipped')),
			'paid' => array('options' => array('' => ' -- Either -- ', 1 => 'Paid', 0 => 'Not Paid')),
			'cancelled' => array('type' => 'checkbox', 'default' => 0),
			'email' => array('type' => 'text', 'label' => 'Email Address'),
			'name' => array('type' => 'text'),
		);
	}
	*/	
	function view($id = null) {
		$saveData = array(
			'success' => array(
				'redirect' => array('action' => 'view', 'ID'),
				'message' => 'Updated cart',
			),
			'fail' => array(
				'message' => 'There was an error updating your cart',
			)
		);
		$saveOptions = array();
		$model = null;
		if (isset($this->request->data['checkout'])) {
			$saveData['success']['redirect'] = array('action' => 'checkout', 'ID');
		} else if (isset($this->request->data['update'])) {
			$saveOptions = array(
				//'validate' => false,
			);
			$id = $this->request->data['Order']['id'];
			if ($this->Order->OrderProduct->saveAll($this->request->data['OrderProduct'], array(
			//	'validate' => false, 
			//	'callbacks' => false
			))) {
				$this->_redirectMsg(array('action' => 'view', $id), 'Updated Cart');
			} else {
				debug($this->Order->OrderProduct->validationErrors);
				$this->Session->setFlash('Could not update cart');
			}
			unset($this->request->data);
		}

		$order = $this->_findOrder($id);
		if ($this->FormData->saveData($model, $saveData, $saveOptions) === null) {
			$this->request->data = $order;
		}
	}
	
	function _beforeSaveData($data, $saveOptions) {
	
	}
	
	function edit($id = null) {
		$this->FormData->editData($id);
		$this->redirect(array('action' => 'view', $id));
	}
	
	function print_invoice($id = null) {
		Configure::write('debug', 0);
		$this->header = false;
		$this->_findOrder($id);
	}
	
	function shipping($id = null) {
		if (!empty($this->request->data['Order']['same_billing'])) {
			$cols = array('first_name', 'last_name', 'addline1', 'addline2', 'city', 'state', 'zip', 'country');
			foreach ($cols as $col) {
				$this->request->data['Invoice'][$col] = !empty($this->request->data['Order'][$col]) ? $this->request->data['Order'][$col] : null;
			}
		}

		$order = $this->_findOrder($id);
		$saveData = array(
			'success' => array(
				'messages' => 'Successfully updated shipping information for your Order',
				'redirect' => array('action' => 'checkout', 'ID')
			)
		);
		
		if ($this->_saveData(null, $saveData) === null) {
			$this->request->data = $order;
		}
		
		$this->set('states', $this->Order->State->selectList());
		$this->set('countries', $this->Order->Country->selectList());
	}
	
	function checkout($id = null) {
		$this->_saveData();
		$order = $this->_findOrder($id);

		//Before displaying checkout screen, checks if order is complete
		if (empty($order['Order']['addline1']) || empty($order['Order']['invoice_id'])) {
			//Shipping information has not been entered yet
			$this->redirect(array('action' => 'shipping', $id));
		} else if (!empty($order['Invoice']['paid'])) {
			//Order has been paid already
			$this->redirect(array('action' => 'view', $id));
		}
	}
	
	function staff_index() {
		if (!empty($this->request->data['Order']['id'])) {
			$order = $this->Order->findById($this->request->data['Order']['id']);
			if (!empty($order)) {
				$this->redirect(array('action' => 'view', $this->request->data['Order']['id']));
			} else {
				$this->_redirectMsg(true, 'Could not find Order #' . $this->request->data['Order']['id']);
			}
		}
		$this->paginate = $this->_findFilter($this->paginate);
		$orders = $this->paginate();
		$this->set(compact('orders'));
	}
	
	function staff_filter() {
		$this->render('/FindFilters/filter');
	}
	
	function staff_view($id = null) {
		if ($this->_saveData() === null) {
			$this->request->data = $this->FormData->findModel($id);
		} else {
			$id = $this->request->data['Order']['id'];
			$this->FormData->findModel($id);
		}
		$this->Order->query('UPDATE webdb.order_products SET sub_total = price * quantity');

		$this->set('products', $this->Order->OrderProduct->Product->selectList());
		$this->set('orderShippingMethods', $this->Order->ShippingMethod->selectList());
		$this->set('invoicePaymentMethods', $this->Order->Invoice->InvoicePaymentMethod->selectList());

	}
	
	function _setFindModelOptions($options = array()) {
		return array_merge(array(
			'fields' => '*',
			'link' => array('Invoice'),
			'postContain' => array(
				'OrderProduct' => array(
					'link' => array('Product'),
				)
			),
		), $options);
	}
	
	function staff_edit($id = null) {
		$this->FormData->editData($id);
		/*
		null, array(), array('validate' => false)) === null) {
			$this->request->data = $this->Order->find('first', array(
				'fields' => '*',
				'link' => array('Invoice'),
				'conditions' => array(
					'Order.id' => $id
				),
				'postContain' => array(
					'OrderProduct',
					'OrdersProductHandling',
				)
			));
		}
		*/
		$this->set('products', $this->Order->OrderProduct->Product->selectList());
		$this->set('states', $this->Order->State->selectList());
		$this->set('countries', $this->Order->Country->selectList());
		$this->set('invoicePaymentMethods', $this->Order->Invoice->InvoicePaymentMethod->selectList());
		
		/*$productOptionsResult = $this->Order->OrderProduct->Product->ProductOption->find('all', array(
			'fields' => '*',
			'link' => array('Product' => array('OrderProduct' => array('Order'))),
			'postContain' => array('ProductOptionChoice'),
			'conditions' => array(
				'Order.id' => $id,
			),
			'group' => 'Product.id'
		));
		$productOptions = array();
		foreach ($productOptionsResult as $productOption) {
			$productOptions[$productOption['Product']['id']][] = array(
				'ProductOption' => $productOption['ProductOption'],
				'ProductOptionChoice' => $productOption['ProductOptionChoice']
			);
		}*/
		$productOptions = $this->Order->findProductOptions($id);
		$this->set(compact('productOptions'));
		$this->set('orderShippingMethods', $this->Order->OrderShippingMethod->selectList());
	}
	
	function staff_add() {
		$this->FormData->addData();
	}
	
	function staff_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	function staff_total() {
		$orders = $this->Order->find('all', array(
			'fields' => array(
				'SUBSTRING(Invoice.paid, 1, 10) AS paid_day',
				'SUM(Order.total) AS total',
				'IF(MONTH(Invoice.paid) BETWEEN 1 AND 2, YEAR(Invoice.paid), YEAR(Invoice.paid) + 1) AS year',
			),
			'link' => array('Invoice'),
			'conditions' => array(),
			'group' => 'paid_day',
			'order' => 'Invoice.paid DESC',
		));
		$totals = array();
		$stats = array(
			'min' => 0,
			'max' => 0,
			'min_day' => null,
			'max_day' => null,
		);
		foreach ($orders as $order) {
			$year = $order[0]['year'];
			$total = $order[0]['total'];
			$day = $order[0]['paid_day'];
			
			if (empty($year)) {
				continue;
			}
			
			list($y, $m, $d) = explode('-', $day);
			$dayKey = (2 - ($year - $y)) * 10000 + (100 * $m) + $d;
			if (empty($stats['min_day']) || $dayKey < $stats['min_day']) {
				$stats['min_day'] = $dayKey;
			} else if (empty($stats['max_day']) || $dayKey > $stats['max_day']) {
				$stats['max_day'] = $dayKey;
			}
			if ($total > $stats['max']) {
				$stats['max'] = $total;
			} else if ($total < $stats['min']) {
				$stats['min'] = $total;
			}
			if (empty($totals[$year])) {
				$totals[$year] = array(
					'day' => array(),
					'total' => 0,
				);
			}
			$totals[$year]['day'][$day] = $total;
			$totals[$year]['total'] += $total;
		}
		$this->set(compact('totals', 'stats'));
	}
	
	function _findFilter($options = array()) {
		if (isset($this->findFilterVal['shipped'])) {
			if (!empty($this->findFilterVal['shipped'])) {
				$options['conditions']['Order.shipped <>'] = null;
			} else {
				$options['conditions']['Order.shipped'] = null;
			}
		}
		if (isset($this->findFilterVal['paid'])) {
			if (!empty($this->findFilterVal['paid'])) {
				$options['conditions']['Invoice.paid <>'] = null;
			} else {
				$options['conditions']['Invoice.paid'] = null;
			}
		}
		if (isset($this->findFilterVal['cancelled'])) {
			$options['conditions']['Order.cancelled'] = $this->findFilterVal['cancelled'];
		}
		if (isset($this->findFilterVal['email'])) {
			$options['conditions']['Invoice.email LIKE'] = trim($this->findFilterVal['email']);
		}
		if (isset($this->findFilterVal['name'])) {
			$options['conditions'][]['OR'] = array(
				'Order.first_name LIKE' => trim($this->findFilterVal['name']) . '%',
				'Order.last_name LIKE' => trim($this->findFilterVal['name']) . '%',
				'CONCAT(Order.first_name," ",Order.last_name) LIKE' => trim($this->findFilterVal['name']) . '%',
			);
		}
		return $options;
	}
	
	function _findOrder($id = null) {
		if (empty($id)) {
			if (!empty($this->request->data['Order']['id'])) {
				$id = $this->request->data['Order']['id'];
			} else {
				$id = $this->ShoppingCart->getCart();
			}
		}
		$order = $this->Order->findOrder($id);
		
		if (empty($order)) {
			$this->_redirectMsg(array(
				'controller' => 'products',
				'action' => 'index',
			), 'Order not found');
		} else {
			$this->ShoppingCart->setCart($id);
			$this->set(compact('order'));
			return $order;
		}
	}

	
	function admin_copy() {
		set_time_limit(120);
		
		//Products
		mysql_query('TRUNCATE TABLE webdb.products');
		$this->Order->query('REPLACE INTO webdb.products 
			(id, title, quantity_per_pack, short_description, description, price, sale, min_quantity, active, hidden, `order`, created, modified) 
		SELECT 
			id,
			name, 
			items_per_pack,
			short_description, 
			long_description, 
			price, 
			sale, 
			IF (min_quantity > 0, min_quantity, 1), 
			active, 
			hidden, 
			product_order, 
			date_added, 
			date_added
		FROM store.products');
		$products = $this->Order->OrderProduct->Product->find('list');
		
		//Product Options
		mysql_query('TRUNCATE webdb.product_options');
		mysql_query('TRUNCATE webdb.product_option_choices');
		$result = mysql_query('SELECT 
				C.product_option_id, 
				C.product_option_choice_id, 
				O.* 
			FROM store.opt1 AS O 
				LEFT JOIN 
				(SELECT C.product_option_id, C.id AS product_option_choice_id, C.title, O.product_id
				FROM webdb.product_options AS O 
				JOIN webdb.product_option_choices AS C ON C.product_option_id = O.id
				) AS C ON C.title = O.name AND C.product_id = O.product_id
			GROUP BY product_id, O.name
			ORDER BY product_id, size_order');
		debug(mysql_error());
		$data = array();
		while ($row = mysql_fetch_assoc($result)) {
			if (empty($row['product_id'])) {
				continue;
			}
			if (empty($data[$row['product_id']])) {
				$data[$row['product_id']] = array(
					'ProductOption' => array(
						'id' => $row['product_option_id'],
						'product_id' => $row['product_id'],
						'title' => 'Size',
						'index' => 1,
					),
					'ProductOptionChoice' => array()
				);
			}
			$data[$row['product_id']]['ProductOptionChoice'][] = array(
				'id' => $row['product_option_choice_id'],
				'title' => $row['name']
			);
		}
		
		foreach ($data as $row) {
			$this->Order->OrderProduct->Product->ProductOption->saveAll($row);
		}
		

		//Product Categories
		$this->Order->query('REPLACE INTO webdb.product_categories 
			(id, lft, rght, title)
		SELECT id, lft, rgt, title FROM store.categories');
		mysql_query('TRUNCATE TABLE webdb.product_categories_products');
		$this->Order->query('REPLACE INTO webdb.product_categories_products (id, product_id, product_category_id)
		SELECT PCP.id, PC.product_id, PC.cat_id FROM store.product_categories AS PC LEFT JOIN
			webdb.product_categories_products AS PCP ON PCP.product_id = PC.product_id AND PCP.product_category_id = PC.cat_id');
			
		//Product Handling
		$this->Order->query('REPLACE INTO webdb.product_handlings (id, title, pct, amt, active)
		SELECT id, title, pct, amt, active FROM store.handling');
		
		//Product Handling Shop Orders
		$this->Order->query('REPLACE INTO webdb.product_handlings_orders (id, product_handling_id, order_id, title, amt, pct)
		SELECT id, handling_id, order_id, title, amt, pct FROM store.order_handling');
		
		//Product Images
		$this->Order->OrderProduct->Product->ProductImage->deleteAll(array(1));
		$images = $this->Order->OrderProduct->Product->ProductImage->find('all');
		$imageIds = array();
		foreach ($images as $image) {
			$imageIds[$image['ProductImage']['product_id'] . '-' . $image['ProductImage']['order']] = $image['ProductImage']['id'];
		}
		$dir = '/home/souper/public_html/images/shop/';
		$handle = opendir($dir);
		$data = array();
		$fileMax = 50;
		$fileCount = 0;
		while(false !== ($file = readdir($handle))) {
			if (is_file($dir . $file)) {
				$fileName = substr($file, 0, strpos($file, '.'));
				list($productId, $count) = explode('_', $fileName) + array(null, 1);
				if (is_numeric($productId) && isset($products[$productId])) {
					$key = $productId . '-' . $count;
					$data[] = array(
						'id' => !empty($imageIds[$key]) ? $imageIds[$key] : null,
						'product_id' => $productId,
						'order' => $count,
						'add_file' => array(
							'tmp_name' => $dir . $file,
							'name' => $file,
							'errors' => 0
						)
					);
				}
			}
			if ($fileCount++ > $fileMax) {
				break;
			}
		}
		if (!empty($data)) {
			debug('Adding ' . count($data) . ' Images');
			$this->Order->OrderProduct->Product->ProductImage->saveAll($data);
		}
		
		//Product Inventories
		mysql_query('TRUNCATE TABLE webdb.product_inventories');
		mysql_query('TRUNCATE TABLE webdb.product_inventory_adjustments');
		
		$result = mysql_query('SELECT
			I.*, C.product_option_choice_id
			FROM store.inventory AS I
			LEFT JOIN (SELECT C.product_option_id, C.id AS product_option_choice_id, C.title, O.product_id
				FROM webdb.product_options AS O 
				JOIN webdb.product_option_choices AS C ON C.product_option_id = O.id
			) AS C ON I.product_id = C.product_id AND I.opt1 = C.title
			JOIN webdb.products AS P ON I.product_id = P.id');
		debug(mysql_error());
		$inventoryData = array();
		$inventoryAdjustData = array();
		
		$count = 0;
		$inventoryCount = 0;
		while($row = mysql_fetch_assoc($result)) {
			$key = $row['product_id'] . '-' . $row['product_option_choice_id'];
			if (empty($inventoryData[$key])) {
				$inventoryCount++;
				$inventoryData[$key] = array(
					'id' => $inventoryCount,
					'product_id' => $row['product_id'],
					'product_option_choice_id_1' => $row['product_option_choice_id'],
				) + array('quantity' => 0);
			}
			$inventoryData[$key]['quantity'] += $row['amt'];
			$inventoryAdjustData[] = array(
				'id' => $count,
				'product_inventory_id' => $inventoryData[$key]['id'],
				'quantity' => $row['amt'],
				'available' => $row['date_available'],
			);
			$count++;
		}
		$inventoryData = array_values($inventoryData);
		$this->Order->OrderProduct->Product->ProductInventory->saveAll($inventoryData);
		$this->Order->OrderProduct->Product->ProductInventory->ProductInventoryAdjustment->saveAll($inventoryAdjustData);
		
		//Product Promos
		$this->Order->query('REPLACE INTO webdb.product_promos (id, code, title, amt, pct, active, started, stopped)
		SELECT PN.id, P.code, P.title, P.amt, P.pct, P.active, null, P.expire FROM
			store.promos AS P LEFT JOIN webdb.product_promos AS PN ON PN.code = P.code');
		
		//Product Promo Shop Orders
		$this->Order->query('REPLACE INTO webdb.product_promos_orders (id, product_promo_id, order_id, code, title, amt, pct)
		SELECT OP.id, P.id, OP.order_id, OP.code, OP.title, OP.amt, OP.pct FROM store.order_promos AS OP 
			LEFT JOIN webdb.product_promos AS P ON P.code = OP.code');
		debug(mysql_error());

		//Product Shipping Rules Shop Order
		
		
		
		//Shipping Methods
		mysql_query('REPLACE INTO webdb.order_shipping_methods (id, title, url) 
			SELECT id, label, url FROM store.ship_methods');
		debug(mysql_error());
		
		//Shop Orders
		$result = mysql_query('SELECT 
				O.*, I.id AS invoice_id, P.id AS paypal_id, COUNT(OI.id) AS order_product_count
			FROM store.orders AS O 
			LEFT JOIN webdb.paypal_payments AS P ON P.txn_id = O.pay_confirm_id
			LEFT JOIN store.order_items AS OI ON OI.order_id = O.id
			LEFT JOIN webdb.orders AS ONEW ON ONEW.id = O.id
			LEFT JOIN webdb.invoices AS I ON I.paypal_payment_id = P.id OR I.id = ONEW.invoice_id
			GROUP BY O.id
		');//2981, 114623
		debug(mysql_error());
		$data = array();
		$count = 0;
		while($row = mysql_fetch_assoc($result)) {
			$data[$count] = array(
				'Order' => array(
					'id' => $row['id'],
					'ordered' => $row['date_ordered'],
					'created' => $row['date_ordered'],
					'shipped' => $row['date_shipped'],
					'shipping_cost' => $row['shipping_cost'],
					'order_shipping_method_id' => $row['ship_method'],
					'tracking' => $row['tracking_id'],
					'first_name' => $row['first_name'],
					'last_name' => $row['last_name'],
					'location_name' => $row['oname'],
					'addline1' => $row['addline1'],
					'addline2' => $row['addline2'],
					'city' => $row['city'],
					'state' => $row['state'],
					'zip' => $row['zip'],
					'country' => 'US',
					'email' => $row['email'],
					'phone' => $row['phone'],
					'note' => $row['comment'],
					'invoice_id' => $row['invoice_id'],
					'archived' => !empty($row['date_paid']) || !empty($row['date_shipped']),
					'order_product_count' => $row['order_product_count'],
				),
				'Invoice' => array(
					'id' => $row['invoice_id'],
					'paid' => $row['date_paid'],
					'paypal_payment_id' => $row['paypal_id'],
					'first_name' => $row['first_name'],
					'last_name' => $row['last_name'],
					'location_name' => $row['oname'],
					'addline1' => $row['addline1'],
					'addline2' => $row['addline2'],
					'city' => $row['city'],
					'state' => $row['state'],
					'zip' => $row['zip'],
					'country' => 'US',
					'email' => $row['email'],
					'phone' => $row['phone'],
				)
			);
			
			$count++;
		}
		foreach ($data as $row) {
			$this->Order->saveAll($row);
		}
		//Shop Order Products
		$result = mysql_query('SELECT
			OI.*, C.product_option_choice_id, I.id AS inventory_id, Invoice.paid, C.title AS choice_title
			FROM store.order_items AS OI
			LEFT JOIN (SELECT C.product_option_id, C.id AS product_option_choice_id, C.title, O.product_id
				FROM webdb.product_options AS O 
				JOIN webdb.product_option_choices AS C ON C.product_option_id = O.id
			) AS C ON OI.product_id = C.product_id AND OI.opt1 = C.title
			LEFT JOIN webdb.product_inventories AS I ON 
				I.product_id = OI.product_id AND 
				IF (C.product_option_choice_id IS NOT NULL, I.product_option_choice_id_1 = C.product_option_choice_id, 1)
			LEFT JOIN webdb.orders AS SO ON SO.id = OI.order_id
			LEFT JOIN webdb.invoices AS Invoice ON Invoice.id = SO.invoice_id
			');
		debug(mysql_error());
		$data = array();
		$inventoryData = array();
		while($row = mysql_fetch_assoc($result)) {
			$title = $row['name'];
			if (!empty($row['choice_title'])) {
				$title .= ': ' . $row['choice_title'];
			}
			$data[] = array(
				'id' => $row['id'],
				'order_id' => $row['order_id'],
				'product_id' => $row['product_id'],
				'parent_product_id' => $row['parent_id'],
				'product_option_choice_id_1' => $row['product_option_choice_id'],
				'title' => $title,
				'quantity' => $row['amt'],
				'price' => $row['price'],
				'shipping' => $row['shipping'],
				'cost' => $row['cost'],
			);
			if (!empty($row['paid']) && !empty($row['inventory_id'])) {
				if (!isset($inventoryData[$row['inventory_id']])) {
					$inventoryData[$row['inventory_id']] = 0;
				}
				$inventoryData[$row['inventory_id']] += $row['amt'];
			}
		}
		$this->Order->OrderProduct->saveAll($data);
		if (!empty($inventoryData)) {
			foreach ($inventoryData as $inventoryId => $adjustAmt) {
				$this->Order->OrderProduct->Product->ProductInventory->updateAll(array(
					'ProductInventory.quantity' => 'ProductInventory.quantity - ' . $adjustAmt
				), array(
					'ProductInventory.id' => $inventoryId
				));
			}
		}
		
		//Shop Order Shipping Class
		
		//Product Shipping
		mysql_query('TRUNCATE webdb.product_shipping_rules');
		$result = mysql_query('SELECT id, shipping, ship_per FROM store.products');
		$data = array();
		while ($row = mysql_fetch_assoc($result)) {
			if (!empty($row['ship_per'])) {
				$product_id = $row['id'];
				preg_match_all('/[\s]*([\d]*)\-([\d]*)\=([\d]*);/', $row['ship_per'], $matches);
				foreach ($matches[0] as $k => $matchString) {
					$min_quantity = $matches[1][$k];
					$max_quantity = $matches[2][$k];
					$amt = $matches[3][$k];
					if (round($amt) == 0) {
						continue;
					}
					$data[] = compact('product_id', 'min_quantity', 'max_quantity', 'amt');
				}
			}
			if (round($row['shipping']) > 0) {
				$data[] = array(
					'product_id' => $row['id'],
					'per_item' => $row['shipping'],
				);
			}
		}
		if (!$this->Order->OrderProduct->Product->ProductShippingRule->saveAll($data)) {
			debug($this->Order->OrderProduct->Product->ProductShippingRule->validationErrors);
		}
		
		//Product Packages
		mysql_query('REPLACE INTO webdb.product_package_products (product_parent_id, product_child_id, quantity)
		SELECT parent_id, product_id, amt FROM store.packages');
		
		//Totals updates
		mysql_query('UPDATE webdb.order_products SET sub_total = price * quantity');
		mysql_query('UPDATE webdb.orders AS O 
			JOIN (SELECT order_id, SUM(sub_total) AS sub_total, SUM(shipping) AS shipping FROM order_products GROUP BY order_id) AS P 
				ON P.order_id = O.id 
			SET O.sub_total = P.sub_total, O.shipping = P.shipping, O.total = O.sub_total + O.shipping + O.handling - O.promo_discount');
			
		//Final Inventory Adjustment
		mysql_query('UPDATE webdb.product_inventories AS I
			JOIN (SELECT 
				SUM(OrderProduct.quantity) AS quantity,
				OrderProduct.product_id,
				OrderProduct.product_option_choice_id_1,
				OrderProduct.product_option_choice_id_2,
				OrderProduct.product_option_choice_id_3,
				OrderProduct.product_option_choice_id_4

			FROM `orders` AS Order 
				JOIN order_products AS OrderProduct ON OrderProduct.order_id = Order.id
				JOIN invoices AS Invoice ON Order.invoice_id = Invoice.id
			WHERE Invoice.paid IS NOT NULL OR Order.shipped IS NOT NULL
			GROUP BY 
				OrderProduct.product_id,
				OrderProduct.product_option_choice_id_1,
				OrderProduct.product_option_choice_id_2,
				OrderProduct.product_option_choice_id_3,
				OrderProduct.product_option_choice_id_4
		) AS I2 ON 
			I.product_id = I2.product_id AND
			((I.product_option_choice_id_1 IS NULL AND I2.product_option_choice_id_1 IS NULL) OR I.product_option_choice_id_1 = I2.product_option_choice_id_1) AND
			((I.product_option_choice_id_2 IS NULL AND I2.product_option_choice_id_2 IS NULL) OR I.product_option_choice_id_2 = I2.product_option_choice_id_2) AND
			((I.product_option_choice_id_3 IS NULL AND I2.product_option_choice_id_3 IS NULL) OR I.product_option_choice_id_3 = I2.product_option_choice_id_3) AND
			((I.product_option_choice_id_4 IS NULL AND I2.product_option_choice_id_4 IS NULL) OR I.product_option_choice_id_4 = I2.product_option_choice_id_4)
		SET I.quantity = I.quantity - I2.quantity
		');
		//UPDATE webdb.orders AS O JOIN (
	}
}
