<?php
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
?>
<div class="order-cart <?php echo ($form ? 'order-form' : '');?>">
<?php
if ($form) {
	echo $this->Form->create('Order', array('action' => 'edit'));
	echo $this->Form->hidden('id', array('value' => $order['Order']['id']));
}
$this->Table->reset();
if (!empty($order['OrderProduct'])):
	foreach ($order['OrderProduct'] as $k => $orderProduct) {
		$catalogItem = $orderProduct['Product']['CatalogItem'];
		$prefix = 'OrderProduct.' . $k . '.';
		$hasParent = !empty($orderProduct['ParentProduct']['id']);
		
		$productHidden = $catalogItem['hidden'];
		$productActive = $catalogItem['active'];
		
		//One last post-fix from moving the old system over
		$orderProduct['title'] = html_entity_decode($orderProduct['title']);
		
		
		//Product
		if (!$hasParent && !$productHidden && $productActive && $links) {
			$url = $this->CatalogItem->url($catalogItem);
		} else {
			$url = null;
		}
		$cell = $thumb = '';
		$title = !empty($url) ? $this->Html->link($orderProduct['title'], $url) : $orderProduct['title'];
		$mediaOptions = compact('title', 'url');
		if ($images) {
			$thumb = $this->CatalogItem->thumb($catalogItem, 
				array('dir' => 'thumb', 'class' => 'media-object'));
			if (!empty($url)) {
				$thumb = $this->Html->link($thumb, $url, array('class' => 'pull-left', 'escape' => false));
			}
		}
		$cell = $this->Html->div('media', $thumb . $this->Html->div('media-body', $title));
		debug($mediaOptions);
		$this->Table->cell(
			$cell,
			'Product', 
			array('class' => $hasParent ? 'hasParent' : null)
		);
		
		//Price
		$price = $hasParent ? '&nbsp;' : $this->DisplayText->cash($orderProduct['price']);
		$this->Table->cell($price, 'Price', null, null, array('class' => 'price'));
		
		//Quantity
		if ($form) {
			echo $this->Form->hidden($prefix . 'id');
			echo $this->Form->hidden($prefix . 'product_id');
			$fieldName = $prefix . 'quantity';
			if ($hasParent) {
				$cell = $this->Form->hidden($fieldName) . number_format($orderProduct['quantity']);
			} else {
				$cell = $this->Form->input($fieldName, array(
					'label' => false,
					'div' => false,
					'class' => 'qty',
					'append' => $this->Iconic->icon('x', array('url' => array(
						'controller' => 'order_products',
						'action' => 'delete',
						$orderProduct['id'],
					)))
				));
			}
			$this->Table->cell(
				$cell,
				$this->Form->submit('Update', array('name' => 'update', 'div' => false)), 
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

		$this->Table->cell(
			$hasParent ? '&nbsp;' : $this->DisplayText->cash($orderProduct['sub_total']),
			'Total', null, null, array(
				'class' => 'row-total'
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
		array('class' => 'final')
	);
	foreach ($totals as $rowCount => $totalRow) {
		list($label, $value, $options) = $totalRow + array(null, null, array());
		$trOptions = array('class' => 'cart-total');
		if ($class = Param::keyCheck($options, 'class')) {
			$trOptions = $this->Html->addClass($trOptions, $class);
		}
		if ($rowCount == 0) {
			$trOptions = $this->Html->addClass($trOptions, 'top-total'); 
		}
		$this->Table->cells(array(
			array($label,
				null, null, null, array(
					'class' => 'total-label',
					'colspan' => $colspan,
				)
			), array(
				$value,
				null, null, null, array(
					'class' => 'row-total',
				)
			)
		), $trOptions);
	}
	echo $this->Table->output(array('blank' => 'Cart is empty'));
else: ?>
	<span class="message">
	Your cart is empty. <?php echo $this->Html->link('Add some stuff to it!', array('controller' => 'catalog_items', 'action' => 'index'));?>
	</span>
<?php
endif;

if ($form):
	echo $this->FormLayout->buttons(array(
		'Checkout' => array(
			'class' => 'btn btn-primary',
			'name' => 'checkout',
		),
		'Continue Shopping' => array(
			'url' => array('controller' => 'catalog_items', 'action' => 'index'),
			'class' => 'btn',
		)
	));
	?>
	<fieldset><legend>Promotional Codes</legend>
	<?php 
	echo $this->Form->input('PromoCode.0.code', array(
		'label' => 'Promotional Code',
		'value' => '',
		'div' => 'input text promoInput',
		'after' => $this->FormLayout->submit('Submit', array('div' => false))
	));
	if (!empty($this->request->data['Order']['PromoCode'])): ?>
		<div class="promo-code-list">
			Currently Using: <span><?php
		foreach ($this->request->data['Order']['PromoCode'] as $promoCode) {
			echo $promoCode['code'] . ' ';
		}?></span>
		</div>
	<?php endif; ?>
	</fieldset><?php
	echo $this->Form->end();
endif;
?>
</div>