<?php
//$this->viewVars['pre_crumbs'] = 'This is a test';
echo $this->element('products/crumbs', array(
	'crumbs' => array(
		'Your Order',
	)
));

echo $this->Html->tag('h1', 'Your Cart', array('class' => 'topTitle'));
echo $this->element('orders/cart', array(
	'form' => true,
	'links' => true,
	'photos' => true,
));

echo $this->Grid->col('1/2', $this->element('orders/shipping_status', array('blank' => false)));
echo $this->Grid->colContinue('1/2', $this->element('orders/payment_status', array('blank' => false)));
echo $this->Grid->colClose(true);

/*
echo $this->Html->div('orderForm');
echo $this->Form->create('Order');
echo $this->Form->hidden('id');
$this->Table->reset();
if (!empty($order['OrderProduct'])) {
	foreach ($order['OrderProduct'] as $k => $orderProduct) {
		$prefix = 'OrderProduct.' . $k . '.';
		$hasParent = !empty($orderProduct['ParentProduct']['id']);
		if (!$hasParent) {
			$cell = $this->Html->link(
				$this->Html->image('btn/20x20/black_trans/delete.png'),
				array(
					'controller' => 'order_products',
					'action' => 'delete',
					$orderProduct['id'],
				), array(
					'escape' => false,
				)
			);
		} else {
			$cell = '&nbsp;';
		}
		$this->Table->cell($cell, 'Remove', null, null, array('width' => 40));
		echo $this->Form->hidden($prefix . '.id');
		echo $this->Form->hidden($prefix . '.product_id');
		for ($i = 1; $i <= 4; $i++) {
			echo $this->Form->hidden($prefix . '.product_option_choice_id_' . $i);
		}
		$this->Table->cell(
			$this->Form->input($prefix . 'quantity', array(
				'label' => false,
				'div' => 'quantity',
			)),
			$this->FormLayout->submit('Update', array('name' => 'update')), 
			null, 
			null, 
			array(
				'width' => 40
			)
		);

		if (!$hasParent && !$orderProduct['Product']['hidden'] && $orderProduct['Product']['active']) {
			$url = $this->Product->url($orderProduct['Product']);
		} else {
			$url = null;
		}
		$cell = $this->Product->thumb($orderProduct['Product'], array(
			'url' => $url, 
			'dir' => 'thumb',
			'tag' => 'font',
		));
		$cell .= !empty($url) ? $this->Html->link($orderProduct['title'], $url) : $orderProduct['title'];
		$this->Table->cells(array(
			array($cell, 'Product'), 
			array(
				$this->DisplayText->cash($orderProduct['price']), 
				'Price'
			), 
			array(
				$this->DisplayText->cash($orderProduct['sub_total']),
				'Total', null, null, array(
					'class' => 'totalColumn'
				)
			)
		), true);
	}
	
	//Totals Rows
	$colspan = 4;
	$totals = array();
	//Promo Codes
	if ($order['Order']['promo_discount'] != 0) {
		$totals[] = array(
			'Discount', 
			$this->DisplayText->cash($order['Order']['promo_discount'])
		);
	}
	//Sub-Total
	$totals[] = array(
		'Sub-Total',
		$this->DisplayText->cash($order['Order']['sub_total'])
	);
	//Shipping
	$totals[] = array(
		'Shipping',
		$this->DisplayText->cash($order['Order']['shipping'])
	);
	//Handling
	$totals[] = array(
		'Handling',
		$this->DisplayText->cash($order['Order']['handling'])
	);
	//Total
	$totals[] = array(
		'Total',
		$this->DisplayText->cash($order['Order']['total']),
		array('class' => 'finalTotal')
	);
	foreach ($totals as $rowCount => $totalRow) {
		list($label, $value, $options) = $totalRow + array(null, null, array());
		$class = Param::keyCheck($options, 'class', false, '');
		if ($rowCount == 0) {
			$class .= ' topRow';
		}
		$this->Table->cells(array(
			array($label,
				null, null, null, array(
					'class' => 'cartTotal label ' . $class,
					'colspan' => $colspan,
				)
			), array(
				$value,
				null, null, null, array(
					'class' => 'cartTotal value totalColumn ' . $class
				)
			)
		), true);
	}
	echo $this->Table->table(array(
		'blank' => 'Cart is empty'
	));
} else {
	echo $this->Html->div('pageMessage',
		'Your cart is empty. ' . $this->Html->link('Add some stuff to it!', array('controller' => 'products', 'action' => 'index'))
	);
}
echo $this->FormLayout->buttons(array(
	$this->Html->link($this->Html->image('icn/16x16/bullet_back.png') . ' Continue Shopping', array(
			'controller' => 'products',
			'action' => 'index'
		), array(
			'escape' => false
		)
	),
	array('Checkout', array(
			'name' => 'checkout',
			'class' => 'submit',
			'img' => 'icn/16x16/cart_go.png',
		)
	)
), array('class' => 'buttons checkoutButtons'));

echo $this->Form->end();

echo "</div>\n";
*/
?>