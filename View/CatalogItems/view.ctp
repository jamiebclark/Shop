<style type="text/css">
.catalogitem-image-thumb-list img {
	display: block;
	float: left;
	width: 33%;
}
.catalogitem-images .main {
	width: 100%;
}
.add-cart .catalogitem-price {
	line-height: 80px;
}
.add-cart .catalogitem-price .cash {
	font-size: 40px;
}
</style>
<div class="catalogitem-view row">
<?php echo $this->Form->create('OrderProduct', array('action' => 'add')); ?>
	<div class="col-sm-3 catalogitem-images"><?php
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
	
	<div class="col-sm-6">
		<h1><?php echo $catalogItem['CatalogItem']['title'];?></h1>
		<?php echo $this->DisplayText->text($catalogItem['CatalogItem']['description']);?>

		<?php if (!empty($catalogItem['CatalogItemPackageChild'])): ?>
		<div class="package">
			<h4>Packaged Item</h4>
			This product contains the following items:
			<?php
			echo $this->element('catalog_item_packages/child_table', array(
				'result' => $catalogItem['CatalogItemPackageChild'],
			));
			?>
		</div>
		<?php endif; ?>
		<?php if (!empty($catalogItemCategories)): ?>
			<h4>Categories</h4>
			<?php echo $this->CatalogItem->categories($catalogItemCategories); ?>
		<?php endif; ?>
	</div>
	<div class="col-sm-3 add-cart">
		<?php 
		if (!$this->CatalogItem->hasStock($catalogItem['CatalogItem'])): ?>
			<h3>Out of stock</h3>
			<p>Sorry, this item is temporarily out of stock. Please check back soon for inventory updates</p><?php 
		else: ?>
			<?php
			echo $this->CatalogItem->price($catalogItem['CatalogItem']);
			echo $this->CatalogItem->notes($catalogItem['CatalogItem']);

			echo $this->Form->inputs(array(
				'fieldset' => false,
				'Order.id' => array('type' => 'hidden'),
				'Product.catalog_item_id' => array(
					'type' => 'hidden', 
					'value' => $catalogItem['CatalogItem']['id']
				),
				'Order.user_id' => array(
					'type' => 'hidden', 
					'default' => $loggedUserId
				),
			));

			echo $this->element('catalog_item_options/input', array(
				'prefix' => 'Product.',
				'catalogItemOptions' => $catalogItem['CatalogItemOption']
			));

			$default = 1;
			if (!empty($catalogItem['CatalogItem']['min_quantity'])) {
				$default = $catalogItem['CatalogItem']['min_quantity'];
			}
			echo $this->Form->input('OrderProduct.quantity', compact('default') + array(
				'class' => 'form-control quantity',
			));
			echo $this->Form->submit('Add to Cart', array(
				'class' => 'btn btn-primary',
			));
		endif;?>
	</div>
	<?php echo $this->Form->end(); ?>
</div>
<?php
if (!empty($isShopAdmin)) {
	echo $this->CatalogItem->adminMenu(array('view', 'edit'), $catalogItem['CatalogItem'], array('urlAdd' => array('admin' => true)));
}