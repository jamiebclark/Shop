<style type="text/css">
.catalog-item-image-thumb-list img {
	display: block;
	float: left;
	width: 33%;
}
.catalog-item-images .main {
	width: 100%;
}
</style>
<div class="catalog-item-view row">
	<div class="span3 catalog-item-images"><?php
	echo $this->CatalogItem->thumb(
		$catalogItem['CatalogItem'], array(
			'div' => false, 
			'dir' => 'thumb',
			'class' => 'main',
			'url' => array(
				'controller' => 'catalog_item_images',
				'action' => 'index',
				$catalogItem['CatalogItem']['id'],
			)
		)
	);
	if (count($catalogItem['CatalogItemImage']) > 1) {
		echo $this->element('catalog_item_images/thumb_list', array(
			'limit' => 3,
			'catalogItemImages' => $catalogItem['CatalogItemImage'],
		));
	}
	?></div>
	
	<div class="span9">
		<h1><?php echo $catalogItem['CatalogItem']['title'];?></h1>
		<?php echo $this->DisplayText->text($catalogItem['CatalogItem']['description']);?>

		<?php if (!empty($catalogItem['CatalogItemPackageChild'])): ?>
		<div class="package">
			<h2>Packaged Item</h2>
			This product contains the following items:
			<?php
			foreach ($catalogItem['CatalogItemPackageChild'] as $k => $catalogItemPackage):
				$url = null;
				$title = $catalogItemPackage['CatalogItemChild']['title'];
				if (!$catalogItemPackage['CatalogItemChild']['hidden']) {
					$url = $this->CatalogItem->url($catalogItemPackage['CatalogItemChild']);
					$title = $this->Html->link($title, $url);
				}
				$thumb = $this->CatalogItem->thumb($catalogItemPackage['CatalogItemChild'], array(
					'dir' => 'thumb', 
					'url' => $url,
					'class' => 'img',
				)); 
				?>
				<div class="media">
					<div class="img"><?php echo $thumb;?></div>
					<div class="rght qty"><?php 
						echo number_format($catalogItemPackage['quantity']);
					?></div>
					<div class="bd"><?php echo $title;?></div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		<div class="add-cart"><?php 
			echo $this->CatalogItem->price($catalogItem['CatalogItem']);
			echo $this->CatalogItem->notes($catalogItem['CatalogItem']);
			if ($catalogItem['CatalogItem']['stock'] <= 0): ?>
				<h3>Out of stock</h3>
				<p>Sorry, this item is temporarily out of stock. Please check back soon for inventory updates</p>
			<?php else:
				echo $this->Form->create('OrderProduct', array('action' => 'add')); ?>
				<fieldset><legend>Add to Cart</legend><?php
				echo $this->Form->inputs(array(
					'fieldset' => false,
					'Order.id' => array(
						'type' => 'hidden'
					),
					'OrderProduct.product_id' => array(
						'type' => 'hidden', 
						'value' => $catalogItem['CatalogItem']['id']
					),
					'Order.user_id' => array(
						'type' => 'hidden', 
						'default' => $loggedUserId
					),
				));
				echo $this->element('catalog_items/product_option_input', array(
					'blank' => true,
					'label' => false,
				));
				echo $this->Form->input('OrderProduct.quantity', array(
					'default' => !empty($catalogItem['CatalogItem']['min_quantity']) ? $catalogItem['CatalogItem']['min_quantity'] : 1,
					'class' => 'quantity',
				));
				echo $this->FormLayout->submit('Add to Cart');
				?>
				</fieldset>
				<?php
				echo $this->Form->end();
			endif;?>
		</div>
	</div>
</div>
<?php
if (!empty($isShopAdmin)) {
	echo $this->Layout->adminMenu(array('view', 'edit'), array('url' => array(
		'action' => 'view',
		$catalogItem['CatalogItem']['id'],
		'staff' => true
	)));
}