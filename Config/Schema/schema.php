<?php 
class ShopSchema extends CakeSchema {

	public $connection = 'shop';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $catalog_item_categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'lft' => array('type' => 'integer', 'null' => false, 'default' => null),
		'rght' => array('type' => 'integer', 'null' => false, 'default' => null),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'parent_id' => array('column' => 'parent_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $catalog_item_categories_catalog_items = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'catalog_item_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'catalog_item_category_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'product_id' => array('column' => array('catalog_item_id', 'catalog_item_category_id'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $catalog_item_images = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'catalog_item_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'filename' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'order' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'product_id' => array('column' => 'catalog_item_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $catalog_item_options = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'catalog_item_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'index' => array('type' => 'integer', 'null' => true, 'default' => null),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'product_id' => array('column' => array('catalog_item_id', 'index'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $catalog_item_packages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'catalog_item_parent_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'catalog_item_child_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'quantity' => array('type' => 'integer', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'product_parent_id' => array('column' => array('catalog_item_parent_id', 'catalog_item_child_id'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $catalog_items = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'short_description' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'description' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'price' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'sale' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'cost' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'min_quantity' => array('type' => 'integer', 'null' => true, 'default' => '1', 'length' => 4),
		'quantity_per_pack' => array('type' => 'integer', 'null' => true, 'default' => '1'),
		'stock' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 6),
		'filename' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'hidden' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'unlimited' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'order' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $handling_methods = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'pct' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'amt' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $invoice_payment_methods = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 127, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $invoices = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'item_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'item_number' => array('type' => 'integer', 'null' => false, 'default' => null),
		'model' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'key' => 'index', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'model_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'model_title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'description' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'invoice_payment_method_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'first_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'last_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'addline1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'addline2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'city' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'state' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'zip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'country' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 25, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'amt' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'recur' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'paid' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'paypal_payment_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'unique'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'paypal_payment_id' => array('column' => 'paypal_payment_id', 'unique' => 1),
			'invoice_payment_method_id' => array('column' => 'invoice_payment_method_id', 'unique' => 0),
			'model' => array('column' => array('model', 'model_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $order_products = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'order_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'product_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'catalog_item_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'parent_product_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'product_inventory_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'quantity' => array('type' => 'integer', 'null' => false, 'default' => null),
		'package_quantity' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4),
		'price' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'shipping' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'sub_total' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'total' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'cost' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'archived' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'parent_product_id' => array('column' => 'parent_product_id', 'unique' => 0),
			'shop_order_id' => array('column' => 'order_id', 'unique' => 0),
			'product_id' => array('column' => 'catalog_item_id', 'unique' => 0),
			'parent_shop_order_product_id' => array('column' => 'parent_id', 'unique' => 0),
			'product_inventory_id' => array('column' => 'product_inventory_id', 'unique' => 0),
			'product_id_2' => array('column' => 'product_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $order_products_shipping_rules = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'shipping_rule_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'order_product_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'amt' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'pct' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'product_shipping_rule_id' => array('column' => array('shipping_rule_id', 'order_product_id'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $orders = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'invoice_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'unique'),
		'paid' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'sub_total' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'shipping' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'handling' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'auto_price' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'auto_shipping' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'auto_handling' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'promo_discount' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'total' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'shop_order_product_count' => array('type' => 'integer', 'null' => false, 'default' => null),
		'shipped' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'shop_order_shipping_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'shipping_method_id' => array('type' => 'boolean', 'null' => false, 'default' => null, 'key' => 'index'),
		'shipping_cost' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'tracking' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'last_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'location_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'addline1' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'addline2' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'city' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'state' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'zip' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'country' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'phone' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'same_billing' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'canceled' => array('type' => 'boolean', 'null' => false, 'default' => null),
		'archived' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'note' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'invoice_id' => array('column' => 'invoice_id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'shop_order_shipping_method_id' => array('column' => 'shipping_method_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $orders_handling_methods = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'handling_method_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'order_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'amt' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'pct' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'handling_method_id' => array('column' => array('handling_method_id', 'order_id'), 'unique' => 1),
			'handling_method_id_2' => array('column' => 'handling_method_id', 'unique' => 0),
			'order_id' => array('column' => 'order_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $orders_promo_codes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'promo_code_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'order_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 25, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'amt' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'pct' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'product_promo_id' => array('column' => array('promo_code_id', 'order_id'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $paypal_payments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'invoice' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'item_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'item_number' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'ptype' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'payment_status' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'pending_reason' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'reason_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 127, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'payment_date' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'payment_fee' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'payment_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 8, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'mc_gross' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'mc_currency' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'mc_fee' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'mc_handling' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'mc_shipping' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'memo' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'txn_id' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'unique', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'receiver_email' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'first_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'last_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'business' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 127, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'address_street' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'address_city' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'address_state' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'address_zip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'address_country' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'payer_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 13, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'payer_email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 127, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'payer_business_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 127, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'auth_amount' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'auth_exp' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'auth_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 19),
		'auth_status' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'amount' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'amount_per_cycle' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'initial_payment_amount' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'next_payment_date' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'outstanding_balance' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'payment_cycle' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'period_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'product_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'product_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'profile_status' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'recurring_payment_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'rp_invoice_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 127, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'time_created' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 28, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'txn_id' => array('column' => 'txn_id', 'unique' => 1),
			'invoice' => array('column' => 'invoice', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $product_inventories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'product_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'quantity' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 6),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'product_id_2' => array('column' => 'product_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $product_inventory_adjustments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'product_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'quantity' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 6),
		'available' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'product_id' => array('column' => 'product_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $product_option_choices = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'catalog_item_option_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'product_option_id' => array('column' => 'catalog_item_option_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $products = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'catalog_item_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'sub_title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 256, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'product_option_choice_id_1' => array('type' => 'integer', 'null' => true, 'default' => null),
		'product_option_choice_id_2' => array('type' => 'integer', 'null' => true, 'default' => null),
		'product_option_choice_id_3' => array('type' => 'integer', 'null' => true, 'default' => null),
		'product_option_choice_id_4' => array('type' => 'integer', 'null' => true, 'default' => null),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'stock' => array('type' => 'integer', 'null' => false, 'default' => null),
		'price' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'sale' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'cost' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '10,2'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'catalog_product_id' => array('column' => array('catalog_item_id', 'product_option_choice_id_1', 'product_option_choice_id_2', 'product_option_choice_id_3', 'product_option_choice_id_4'), 'unique' => 1),
			'catalog_product_id_2' => array('column' => 'catalog_item_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $promo_codes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'amt' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '10,2'),
		'pct' => array('type' => 'float', 'null' => false, 'default' => null, 'length' => '2,2'),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'started' => array('type' => 'date', 'null' => true, 'default' => null),
		'stopped' => array('type' => 'date', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $shipping_classes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $shipping_methods = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'url' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	public $shipping_rules = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'catalog_item_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'order_class_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'min_quantity' => array('type' => 'integer', 'null' => true, 'default' => null),
		'max_quantity' => array('type' => 'integer', 'null' => true, 'default' => null),
		'pct' => array('type' => 'float', 'null' => true, 'default' => '0.00', 'length' => '10,2'),
		'per_item' => array('type' => 'float', 'null' => true, 'default' => '0.00', 'length' => '10,2'),
		'amt' => array('type' => 'float', 'null' => true, 'default' => '0.00', 'length' => '10,2'),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'product_id' => array('column' => array('catalog_item_id', 'order_class_id', 'active'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
}
