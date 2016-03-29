<?php
$default = [
	'form' => false,
	'links' => false,
	'images' => true,
	'shipping' => false,
	'small' => false,
	'condensed' => false,
	'delete' => null,
	'title' => 'Cart',
	'titleUrl' => true,
];
extract(array_merge($default, compact(array_keys($default))));

$continueShoppingUrl = ['controller' => 'catalog_items', 'action' => 'index'];

$emptyCart = empty($order['OrderProduct']);
//Order-dependent settings
if ($order['Order']['archived']) {
	$archived = true;
	$form = false;
	$links = false;
} else {
	$archived = false;
}
$tableOptions = ['blank' => 'Cart is empty'];
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
			$titleUrl = ['controller' => 'orders', 'action' => 'view', $order['Order']['id']];
		}
		$title = $this->Html->link($title, $titleUrl);
	}
}

?>
<div class="<?php echo $wrapClass;?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			<span class="panel-title"><?php echo $title; ?></span>
		</div><?php
		if ($form) {
			echo $this->Form->create('Order', ['url' => ['action' => 'edit']]);
			echo $this->Form->hidden('id', ['value' => $order['Order']['id']]);
		}
		$this->Table->reset();
		if (!$emptyCart):
			foreach ($order['OrderProduct'] as $k => $orderProduct) {
				$rowOptions = ['class' => 'order-product'];
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
						['dir' => 'thumb', 'class' => 'media-object']);
					if (!empty($url)) {
						$thumb = $this->Html->link($thumb, $url, ['class' => 'pull-left', 'escape' => false]);
					} else {
						$thumb = $this->Html->tag('span', $thumb, ['class' => 'pull-left']);
					}
				}
				$cell = $this->Html->div('media', $thumb . $this->Html->div('media-body', $title));

				$this->Table->cell($cell, 'Product', ['class' => 'product']);
				
				//Price
				if (!$condensed) {
					$price = $hasParent ? '&nbsp;' : $this->CatalogItem->cash($orderProduct['price']);
					$this->Table->cell($price, 'Price', ['class' => 'price']);
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
						$deleteUrl = ['controller' => 'order_products', 'action' => 'delete', $orderProduct['id']];
						$qtyOptions = [
							'label' => false, 
							'div' => false, 
						];
						if (!empty($delete) || (!$condensed && $links)) {
							$qtyOptions['appendButton'] = $this->Html->link(
								$this->Iconic->icon('x'),
								$deleteUrl,
								[
									'escape' => false, 
									'class' => 'btn btn-default',
									'title' => 'Remove this item from your cart',
								]
							);
						}
						$cell = $this->Form->input($prefix . 'quantity', $qtyOptions);
					}
					$this->Table->cell(
						$cell,
						$this->Form->button('Update', ['type' => 'submit', 'name' => 'update', 'div' => false]),
						['class' => 'quantity']
					);
				} else {
					$this->Table->cell(
						number_format($orderProduct['quantity']), 
						!$condensed ? 'Quantity' : 'Qty',
						['class' => 'quantity']
					);
				}
				
				if ($shipping) {
					$this->Table->cell(
						$this->DisplayText->cash($orderProduct['shipping']),
						'Shipping',
						['class' => 'quantity']
					);
				}

				/*
				//DELETE Link
				if (!empty($delete) || (!$condensed && $links)) {
					if (!$hasParent) {
						$deleteLink = $this->ModelView->actionMenu(['delete'], [
							'url' => ['controller' => 'order_products', $orderProduct['id']],
						]);
					} else {
						$deleteLink = '&nbsp;';
					}
					$this->Table->cell($deleteLink, 'Remove', ['class' => 'delete']);
				}
				*/
				
				$this->Table->cell(
					$hasParent ? '&nbsp;' : $this->DisplayText->cash($orderProduct['sub_total']),
					'Total', 
					['class' => 'row-total']
				);

				$this->Table->rowEnd($rowOptions);
			}
			
			//Totals Rows
			$colspan = $this->Table->columnCount - 1;
			$totals = [];
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
				['class' => 'final']
			);
			foreach ($totals as $rowCount => $totalRow):
				list($label, $value, $options) = $totalRow + [null, null, []];
				$trOptions = ['class' => 'cart-total'];
				if ($class = Param::keyCheck($options, 'class')) {
					$trOptions = $this->Html->addClass($trOptions, $class);
				}
				if ($rowCount == 0) {
					$trOptions = $this->Html->addClass($trOptions, 'top-total'); 
				}
				$this->Table->cells([
					[$label, ['class' => 'total-label', 'colspan' => $colspan]], 
					[$value, ['class' => 'row-total']]
				], $trOptions);
			endforeach; ?>
			<?php echo $this->Table->output($tableOptions); ?>
		<?php else: ?>
			<div class="jumbotron">
				<h1>Your cart is empty!</h1>
				<?php 
					echo $this->Html->link(
						'Add some stuff to it!', 
						$continueShoppingUrl,
						['class' => 'btn btn-lg']
					);
				?>
			</div>
		<?php endif; ?>
		<?php if ($form && !$emptyCart): ?>
			<div class="clearfix">
				<div class="promo-code">
					<?php 
					echo $this->FormLayout->input('add_promo_code', array(
						'label' => false,
						'value' => '',
						'placeholder' => 'Add a Promo Code',
						'appendButton' => $this->FormLayout->submit('Add', ['div' => false])
					));
					if (!empty($this->request->data['PromoCode'])): ?>
						<dl>
							<dt>Using Promos:</dt>
							<dd>
								<?php foreach ($this->request->data['PromoCode'] as $promoCode): ?>
									<em><?php echo $promoCode['code']; ?></em> 
								<?php endforeach; ?>
							</dd>
						</dl>
					<?php endif; ?>
				</div>
			</div>

			<div class="panel-footer clearfix">
				<?php
				echo $this->FormLayout->buttons([
					'Checkout <i class="fa fa-arrow-right"></i>' => [
						'class' => 'btn btn-lg btn-primary pull-right',
						'name' => 'checkout',
					],
					'<i class="fa fa-arrow-left"></i> Continue Shopping' => [
						'url' => $continueShoppingUrl,
						'class' => 'btn',
						'escape' => false,
					]
				]); ?>
			</div>
			<?php 
			echo $this->Form->end();
		endif;
		//	debug($this->request->data);
		?>
	</div>
</div>