<?php
$this->Asset->css('products');
$this->Asset->js('product');

//Settings
if (empty($form)) {
	$form = false;
}
if (empty($links)) {
	$links = false;
}
if (!isset($images)) {
	$images = true;
}

//Order-dependent settings
if ($order['Order']['archived']) {
	$archived = true;
	$form = false;
	$links = false;
} else {
	$archived = false;
}

echo $this->Html->div('orderCart ' . ($form ? 'orderForm' : ''));

if ($form) {
	echo $this->Form->create('Order');
	echo $this->Form->hidden('id');
}

$this->Table->reset();
if (!empty($order['OrderProduct'])) {
	foreach ($order['OrderProduct'] as $k => $orderProduct) {
		$prefix = 'OrderProduct.' . $k . '.';
		$hasParent = !empty($orderProduct['ParentProduct']['id']);
		
		$productHidden = $orderProduct['Product']['hidden'];
		$productActive = $orderProduct['Product']['active'];
		
		//One last post-fix from moving the old system over
		$orderProduct['title'] = html_entity_decode($orderProduct['title']);
		
		
		//Product
		if (!$hasParent && !$productHidden && $productActive && $links) {
			$url = $this->Product->url($orderProduct['Product']);
		} else {
			$url = null;
		}
		$cell = '';
		if ($images) {
			$cell .= $this->Product->thumb($orderProduct['Product'], array(
				'url' => $url, 
				'dir' => 'thumb',
				'tag' => 'font',
			));
		}
		$cell .= !empty($url) ? $this->Html->link($orderProduct['title'], $url) : $orderProduct['title'];
		$this->Table->cell($cell, 'Product', null, null, array('class' => $hasParent ? 'hasParent' : null));
		
		//Price
		$price = $hasParent ? '&nbsp;' : $this->DisplayText->cash($orderProduct['price']);
		$this->Table->cell($price, 'Price', null, null, array('class' => 'price'));
		
		//Quantity
		if ($form) {
			echo $this->Form->hidden($prefix . 'id');
			echo $this->Form->hidden($prefix . 'product_id');
			for ($i = 1; $i <= 4; $i++) {
				echo $this->Form->hidden($prefix . 'product_option_choice_id_' . $i);
			}
			
			$fieldName = $prefix . 'quantity';
			if ($hasParent) {
				$cell = $this->Form->hidden($fieldName) . number_format($orderProduct['quantity']);
			} else {
				$cell = $this->Form->input($fieldName, array(
					'label' => false,
					'div' => 'quantity',
				));
			}
			$this->Table->cell(
				$cell,
				$this->FormLayout->submit('Update', array('name' => 'update', 'img' => false, 'div' => false)), 
				null, 
				null, 
				array(
					'width' => 40,
					'class' => $hasParent ? 'quantity' : null
				)
			);
		} else {
			$this->Table->cell(
				number_format($orderProduct['quantity']), 
				'Quantity',
				null,
				null,
				array('class' => 'quantity')
			);
		}

		//Delete Option
		if (!$hasParent && !$archived && $form) {
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

		}  else {
			$cell = '&nbsp;';
		}
		$this->Table->cell($cell, '&nbsp;', null, null, array('width' => 40));
		
		$this->Table->cell(
			$hasParent ? '&nbsp;' : $this->DisplayText->cash($orderProduct['sub_total']),
			'Total', null, null, array(
				'class' => 'totalColumn'
			)
		);
		$this->Table->rowEnd();
	}
	
	//Totals Rows
	$colspan = $this->Table->columnCount - 1;
	$totals = array();
		//Sub-Total
	$totals[] = array(
		'Sub-Total',
		$this->DisplayText->cash($order['Order']['sub_total'], false)
	);
	//Shipping
	$totals[] = array(
		'Shipping',
		$this->DisplayText->cash($order['Order']['shipping'], false)
	);
	//Promo Codes
	if ($order['Order']['promo_discount'] != 0) {
		$totals[] = array(
			'Discount', 
			$this->DisplayText->cash($order['Order']['promo_discount'], false)
		);
	}
	//Handling
	$totals[] = array(
		'Handling',
		$this->DisplayText->cash($order['Order']['handling'], false)
	);
	//Total
	$totals[] = array(
		'Total',
		$this->DisplayText->cash($order['Order']['total'], false),
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
if ($form) {
	echo $this->element('orders/shipping_cutoff_msg');

	echo $this->FormLayout->buttons(array(
		'Continue Shopping' => array(
			'class' => 'prev',
			'url' => array(
				'controller' => 'products',
				'action' => 'index'
			),
			'align' => 'left',
		),
		'Checkout' => array(
			'name' => 'checkout',
			'class' => 'next',
			'img' => 'icn/16x16/cart_go.png',
			'disabled' => $order['Order']['total'] <= 0,
			'align' => 'right',
		)
	), array(
		'secondary' => false,
	));

	echo $this->Layout->fieldset('Promotional Codes', null, array('class' => 'promos'));
	echo $this->Form->input('ProductPromosOrder.0.code', array(
		'label' => 'Promotional Code',
		'value' => '',
		'div' => 'input text promoInput',
		'after' => $this->FormLayout->submit('Submit', array('div' => false))
	));
	if (!empty($this->request->data['ProductPromosOrder'])) {
		echo $this->Html->div('promoCodeList');
		echo 'Currently Using: ';
		echo $this->Html->tag('span');
		foreach ($this->request->data['ProductPromosOrder'] as $productPromosOrder) {
			echo $productPromosOrder['code'] . ' ';
		}
		echo "</span>\n";
		echo "</div>\n";
	}
	echo "</fieldset>\n";
	echo $this->Form->end();
}
echo "</div>\n";
?>