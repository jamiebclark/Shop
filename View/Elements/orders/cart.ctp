<?php
$default = array(
	'form' => false,
	'links' => false,
	'images' => true,
	'shipping' => false,
	'small' => false,
	'condensed' => false,
	'delete' => null,
	'title' => 'Cart',
	'titleUrl' => true,
);
extract(array_merge($default, compact(array_keys($default))));

$emptyCart = empty($order['OrderProduct']);
//Order-dependent settings
if ($order['Order']['archived']) {
	$archived = true;
	$form = false;
	$links = false;
} else {
	$archived = false;
}
$tableOptions = array('blank' => 'Cart is empty');
$wrapClass = 'order-cart';
if ($condensed) {
	$wrapClass .= ' condensed';
}
if ($form) {
	$wrapClass .= ' order-form';
}

if ($title) {
	if ($titleUrl) {
		if ($titleUrl === true) {
			$titleUrl = array('controller' => 'orders', 'action' => 'view', $order['Order']['id']);
		}
		$title = $this->Html->link($title, $titleUrl);
	}
}

<<<<<<< HEAD
if ($form) {
	echo $this->Form->create('Order', array('action' => 'edit'));
	echo $this->Form->hidden('id', array('value' => $order['Order']['id']));
}
$this->Table->reset();
if (!$emptyCart):
	foreach ($order['OrderProduct'] as $k => $orderProduct):
		if (empty($orderProduct['Product']['CatalogItem'])) {
			continue;
		}
		
		$rowOptions = array('class' => 'order-product');
		$catalogItem = $orderProduct['Product']['CatalogItem'];
		$prefix = 'OrderProduct.' . $k . '.';
		if ($hasParent = !empty($orderProduct['parent_id'])) {
			$rowOptions = $this->Html->addClass($rowOptions, 'child-order-product');
		}
		
		$productHidden = $catalogItem['hidden'];
		$productActive = $catalogItem['active'];
		
		//One last post-fix from moving the old system over
		$orderProduct['title'] = html_entity_decode($orderProduct['title']);
		
		//Product
		$url = null;
		if (!$hasParent && !$productHidden && $productActive && $links) {
			$url = $this->CatalogItem->modelUrl($catalogItem);
		}
		
		if ($hasParent && $condensed) {
			continue;
		}
		
		$cell = $thumb = '';
		$title = !empty($url) ? $this->Html->link($orderProduct['title'], $url) : $orderProduct['title'];
		$mediaOptions = compact('title', 'url');
		if ($images) {
			$thumb = $this->CatalogItem->thumb($catalogItem, 
				array('dir' => 'thumb', 'class' => 'media-object'));
			if (!empty($url)) {
				$thumb = $this->Html->link($thumb, $url, array('class' => 'pull-left', 'escape' => false));
			} else {
				$thumb = $this->Html->tag('span', $thumb, array('class' => 'pull-left'));
			}
=======
?>
<div class="<?php echo $wrapClass;?>">
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo $title; ?></div><?php
		if ($form) {
			echo $this->Form->create('Order', array('action' => 'edit'));
			echo $this->Form->hidden('id', array('value' => $order['Order']['id']));
>>>>>>> 8d2b9ace26644135d86e336e52568999b1972ed1
		}
		$this->Table->reset();
		if (!$emptyCart):
			foreach ($order['OrderProduct'] as $k => $orderProduct) {
				$rowOptions = array('class' => 'order-product');
				$catalogItem = $orderProduct['Product']['CatalogItem'];
				$prefix = 'OrderProduct.' . $k . '.';
				if ($hasParent = !empty($orderProduct['parent_id'])) {
					$rowOptions = $this->Html->addClass($rowOptions, 'child-order-product');
				}
				
				$productHidden = $catalogItem['hidden'];
				$productActive = $catalogItem['active'];
				
				//One last post-fix from moving the old system over
				$orderProduct['title'] = html_entity_decode($orderProduct['title']);
				
				//Product
				$url = null;
				if (!$hasParent && !$productHidden && $productActive && $links) {
					$url = $this->CatalogItem->modelUrl($catalogItem);
				}
				
				if ($hasParent && $condensed) {
					continue;
				}
				
				$cell = $thumb = '';
				$title = !empty($url) ? $this->Html->link($orderProduct['title'], $url) : $orderProduct['title'];
				$mediaOptions = compact('title', 'url');
				if ($images) {
					$thumb = $this->CatalogItem->thumb($catalogItem, 
						array('dir' => 'thumb', 'class' => 'media-object'));
					if (!empty($url)) {
						$thumb = $this->Html->link($thumb, $url, array('class' => 'pull-left', 'escape' => false));
					} else {
						$thumb = $this->Html->tag('span', $thumb, array('class' => 'pull-left'));
					}
				}
				$cell = $this->Html->div('media', $thumb . $this->Html->div('media-body', $title));

				$this->Table->cell($cell, 'Product', array('class' => 'product'));
				
				//Price
				if (!$condensed) {
					$price = $hasParent ? '&nbsp;' : $this->CatalogItem->cash($orderProduct['price']);
					$this->Table->cell($price, 'Price', array('class' => 'price'));
				}
				
				//Quantity
				if ($form) {
					if ($hasParent) {
						$cell = number_format($orderProduct['quantity']);
					} else {
						echo $this->Form->hidden($prefix . 'id');
						echo $this->Form->hidden($prefix . 'product_id');
						echo $this->Form->hidden($prefix . 'parent_id');
						echo $this->Form->hidden($prefix . 'package_quantity');
						$deleteUrl = array('controller' => 'order_products', 'action' => 'delete', $orderProduct['id']);
						$qtyOptions = array(
							'label' => false, 
							'div' => false, 
						);
						if (!empty($delete) || (!$condensed && $links)) {
							$qtyOptions['appendButton'] = $this->Html->link(
								$this->Iconic->icon('x'),
								$deleteUrl,
								array(
									'escape' => false, 
									'class' => 'btn btn-default',
									'title' => 'Remove this item from your cart',
								)
							);
						}
						$cell = $this->Form->input($prefix . 'quantity', $qtyOptions);
					}
					$this->Table->cell(
						$cell,
						$this->Form->submit('Update', array('name' => 'update', 'div' => false)),
						array('class' => 'quantity')
					);
				} else {
					$this->Table->cell(
						number_format($orderProduct['quantity']), 
						!$condensed ? 'Quantity' : 'Qty',
						array('class' => 'quantity')
					);
				}
				
				if ($shipping) {
					$this->Table->cell(
						$this->DisplayText->cash($orderProduct['shipping']),
						'Shipping',
						array('class' => 'quantity')
					);
				}

				/*
				//DELETE Link
				if (!empty($delete) || (!$condensed && $links)) {
					if (!$hasParent) {
						$deleteLink = $this->ModelView->actionMenu(array('delete'), array(
							'url' => array('controller' => 'order_products', $orderProduct['id']),
						));
					} else {
						$deleteLink = '&nbsp;';
					}
					$this->Table->cell($deleteLink, 'Remove', array('class' => 'delete'));
				}
				*/
				
				$this->Table->cell(
					$hasParent ? '&nbsp;' : $this->DisplayText->cash($orderProduct['sub_total']),
					'Total', 
					array('class' => 'row-total')
				);

				$this->Table->rowEnd($rowOptions);
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
<<<<<<< HEAD
			$this->Table->cell($deleteLink, 'Remove', array('class' => 'delete'));
		}
		*/
		
		$this->Table->cell(
			$hasParent ? '&nbsp;' : $this->DisplayText->cash($orderProduct['sub_total']),
			'Total', 
			array('class' => 'row-total')
		);

		$this->Table->rowEnd($rowOptions);
	endforeach;
	
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
			array($label, array('class' => 'total-label', 'colspan' => $colspan)), 
			array($value, array('class' => 'row-total'))
		), $trOptions);
	}
	echo $this->Table->output($tableOptions);
else: ?>
	<div class="jumbotron">
		<h1>Your cart is empty!</h1>
		<?php 
			echo $this->Html->link(
				'Add some stuff to it!', 
				array('controller' => 'catalog_items', 'action' => 'index'),
				array('class' => 'btn btn-lg')
=======
			//Handling
			$totals[] = array(
				'Handling',
				$this->DisplayText->cash($order['Order']['handling'], false)
>>>>>>> 8d2b9ace26644135d86e336e52568999b1972ed1
			);
			//Total
			$totals[] = array(
				'Total',
				$this->DisplayText->cash($order['Order']['total'], false),
				array('class' => 'final')
			);
			foreach ($totals as $rowCount => $totalRow):
				list($label, $value, $options) = $totalRow + array(null, null, array());
				$trOptions = array('class' => 'cart-total');
				if ($class = Param::keyCheck($options, 'class')) {
					$trOptions = $this->Html->addClass($trOptions, $class);
				}
				if ($rowCount == 0) {
					$trOptions = $this->Html->addClass($trOptions, 'top-total'); 
				}
				$this->Table->cells(array(
					array($label, array('class' => 'total-label', 'colspan' => $colspan)), 
					array($value, array('class' => 'row-total'))
				), $trOptions);
			endforeach; ?>
			<?php echo $this->Table->output($tableOptions); ?>
		<?php else: ?>
			<div class="jumbotron">
				<h1>Your cart is empty!</h1>
				<?php 
					echo $this->Html->link(
						'Add some stuff to it!', 
						array('controller' => 'catalog_items', 'action' => 'index'),
						array('class' => 'btn btn-lg')
					);
				?>
			</div>
		<?php endif;

		if ($form && !$emptyCart):	?>
			<div class="clearfix">
				<div class="promo-code">
					<?php 
					echo $this->FormLayout->input('PromoCode.0.code', array(
						'label' => false,
						'value' => '',
						'placeholder' => 'Add a Promo Code',
						'appendButton' => $this->FormLayout->submit('Add', array('div' => false))
					));
					if (!empty($this->request->data['Order']['PromoCode'])): ?>
						<div class="promo-code-list">
							Currently Using: <span><?php
								foreach ($this->request->data['Order']['PromoCode'] as $promoCode):
									echo $promoCode['code'] . ' ';
								endforeach;
							?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="panel-footer clearfix">
				<?php
				echo $this->FormLayout->buttons(array(
					'Checkout' => array(
						'class' => 'btn btn-lg btn-primary pull-right',
						'name' => 'checkout',
					),
					'Continue Shopping' => array(
						'url' => array('controller' => 'catalog_items', 'action' => 'index'),
						'class' => 'btn',
					)
				)); ?>
			</div>
			<?php 
			echo $this->Form->end();
		endif;
		//	debug($this->request->data);
		?>
	</div>
</div>