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
<div class="catalogitem-view">
<?php echo $this->Form->create('OrderProduct', array('action' => 'add')); ?>
	<div class="row">
		<div class="col-sm-offset-3 col-sm-9">
			<div class="catalogitem-view-heading">
				<h2><?php echo $catalogItem['CatalogItem']['title'];?></h2>
			</div>
		</div>
	</div>
	<div class="row">
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
		<div class="col-sm-9">
			<div class="row">
				<div class="col-sm-8">
					<?php if (!empty($catalogItem['CatalogItem']['description'])): ?>
						<div class="panel panel-default">
							<div class="panel-body">
								<?php echo $this->DisplayText->text($catalogItem['CatalogItem']['description']);?>
							</div>
						</div>
					<?php endif; ?>

					<?php if (!empty($catalogItem['CatalogItemPackageChild'])): ?>
					<div class="package">
						<div class="panel panel-default">
							<div class="panel-heading">Packaged Item</div>
							<div class="panel-body">This product contains the following items:</div>
							<?php
							echo $this->element('catalog_item_packages/child_table', array(
								'result' => $catalogItem['CatalogItemPackageChild'],
							));
							?>
						</div>
					</div>
					<?php endif; ?>
					<?php if (!empty($catalogItemCategories)): ?>
						<div class="panel panel-default">
							<div class="panel-heading">Categories</div>
							<div class="panel-body">
								<?php echo $this->CatalogItem->categories($catalogItemCategories); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-sm-4 add-cart">
					<div class="panel panel-default">
						<div class="panel-body">
							<?php 
							if (!$this->CatalogItem->hasStock($catalogItem['CatalogItem'])): ?>
								<h3>Out of stock</h3>
								<p>Sorry, this item is temporarily out of stock. Please check back soon for inventory updates</p><?php 
							else: ?>
								<?php
								echo $this->CatalogItem->price($catalogItem['CatalogItem']);
								echo $this->CatalogItem->notes($catalogItem['CatalogItem']);

								echo $this->Form->hidden('Order.id');
								echo $this->Form->hidden('Product.catalog_item_id', ['value' => $catalogItem['CatalogItem']['id']]);
								echo $this->Form->hidden('Order.user_id', ['default' => $loggedUserId]);

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
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo $this->Form->end(); ?>
</div>
<?php
if (!empty($isShopAdmin)) {
	echo $this->CatalogItem->adminMenu(array('view', 'edit'), $catalogItem['CatalogItem'], array('urlAdd' => array('admin' => true)));
}