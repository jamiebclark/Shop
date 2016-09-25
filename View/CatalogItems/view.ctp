<?php
$this->Html->css('Shop.catalog_item_view', null, ['inline' => false]);
?>

<?php if (empty($catalogItem['CatalogItem']['active'])): ?>
	<div class="alert alert-warning">
		<h2 class="alert-title">Inactive Product</h2>
		<p class="lead">Sorry, this product isn't currently active in the store. It's not currently available for purchase.</p>
	</div>
<?php endif; ?>

<div class="catalogitem-view">
<?php echo $this->Form->create('OrderProduct', ['url' => ['action' => 'add']]); ?>
	<div class="row">
		<div class="col-sm-offset-3 col-sm-9">
			<div class="catalogitem-view-heading">
				<h2><?php echo $catalogItem['CatalogItem']['title'];?></h2>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-3">
			<div class="catalogitem-images"><?php
				echo $this->CatalogItem->thumb(
					$catalogItem['CatalogItem'], [
						'div' => false, 
						'dir' => 'thumb',
						'class' => 'catalogitem-images-display',
						'url' => [
							'controller' => 'catalog_item_images',
							'action' => 'index',
							$catalogItem['CatalogItem']['id'],
						],
						'id' => 'image-load-target',
					]
				); ?>
				<?php if (count($catalogItem['CatalogItemImage']) > 1): ?>
					<?php echo $this->element('catalog_item_images/thumb_list', [
						'div' => 'catalogitem-images-thumbnails',
						'thumbnailClass' => 'catalogitem-images-thumbnail',
						'limit' => 12,
						'catalogItemImages' => $catalogItem['CatalogItemImage'],
						'imageClass' => 'image-load',
						'imageLoadTarget' => '#image-load-target',
					]); ?>
				<?php endif; ?>
			</div>
		</div>
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
							<div class="panel-heading"><span class="panel-title">Packaged Item</span></div>
							<div class="panel-body">This product contains the following items:</div>
							<?php
							echo $this->element('catalog_item_packages/child_table', [
								'result' => $catalogItem['CatalogItemPackageChild'],
							]);
							?>
						</div>
					</div>
					<?php endif; ?>
					<?php if (!empty($catalogItemCategories)): ?>
						<div class="panel panel-default">
							<div class="panel-heading"><span class="panel-title">Categories</span></div>
							<div class="panel-body">
								<?php echo $this->CatalogItem->categories($catalogItemCategories); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-sm-4 add-cart">
					<div class="panel panel-default">
						<div class="panel-body">
							<?php if (empty($catalogItem['CatalogItem']['active'])): ?>
								<h3 class="heading-topper">Unavailable</h3>
								<p>This product isn't currently available in the store</p>
							<?php elseif (!$this->CatalogItem->hasStock($catalogItem['CatalogItem'])): ?>
								<h3 class="heading-topper">Out of stock</h3>
								<p>Sorry, this item is temporarily out of stock. Please check back soon for inventory updates</p><?php 
							else: ?>
								<?php
								echo $this->CatalogItem->price($catalogItem['CatalogItem']);
								echo $this->CatalogItem->notes($catalogItem['CatalogItem']);

								echo $this->Form->hidden('Order.id');
								echo $this->Form->hidden('Product.catalog_item_id', ['value' => $catalogItem['CatalogItem']['id']]);
								if (!empty($loggedUserId)) {
									echo $this->Form->hidden('Order.user_id', ['default' => $loggedUserId]);
								}

								echo $this->element('catalog_item_options/input', [
									'prefix' => 'Product.',
									'catalogItemOptions' => $catalogItem['CatalogItemOption']
								]);

								$default = 1;
								if (!empty($catalogItem['CatalogItem']['min_quantity'])) {
									$default = $catalogItem['CatalogItem']['min_quantity'];
								}
								echo $this->Form->input('OrderProduct.quantity', compact('default') + [
									'class' => 'form-control quantity',
								]);
								echo $this->Form->submit('Add to Cart', [
									'class' => 'btn btn-primary',
								]);
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
	echo $this->CatalogItem->adminMenu(['view', 'edit'], $catalogItem['CatalogItem'], ['urlAdd' => ['admin' => true]]);
}
?>

<?php $this->Html->scriptStart(['inline' => false]); ?>
(function($) {
	$.fn.imageLoad = function() {
		return this.each(function() {
			var $image = $(this),
				target = $image.data('image-load-target'),
				$target = $(target),
				src = $image.data('image-load-src');
			$image.mouseover(function() {
				$target.attr('src', src);
			});
		});
	};

	$(document).ready(function() {
		$('.image-load').imageLoad();
	});

})(jQuery);
<?php $this->Html->scriptEnd(); ?>