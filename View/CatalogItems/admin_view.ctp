<?php 
$this->Html->script('Layout.element_input_list', ['inline' => false]);


echo $this->Layout->defaultHeader($catalogItem['CatalogItem']['id'], null, [
	'title' => $catalogItem['CatalogItem']['title']
]); ?>
<div class="row">
	<div class="col-sm-8">
		<div class="panel panel-default">
			<div class="panel-heading"><span class="panel-title">Short Description</span></div>
			<div class="panel-body">
				<?php echo $this->DisplayText->text($catalogItem['CatalogItem']['short_description'], ['class' => 'well']); ?>
			</div>

			<div class="panel-heading"><span class="panel-title">Description</span></div>
			<div class="panel-body">
				<?php echo $this->DisplayText->text($catalogItem['CatalogItem']['description'], ['class' => 'well']); ?>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading"><span class="panel-title">About</span></div><?php
			echo $this->Layout->infoResultTable($catalogItem['CatalogItem'], [
					'price' => ['format' => 'cash'],
					'sale' => ['format' => 'cash', 'notBlank', 'class' => 'sale'],
					'stock' => [
						'label' => 'Currently In Stock',
						'format' => 'number',
						'url' => ['controller' => 'products', 'action' => 'index', $catalogItem['CatalogItem']['id']]
					],
					'unlimited' => [
						'format' => 'yesno',
						'label' => 'Unlimited Inventory',
					],			
					'min_quantity' => ['label' => 'Minimum Quantity per Order'],
					'quantity_per_pack',
					'created' => ['format' => 'date'],
					'modified' => ['format' => 'date', 'label' => 'Last Modified'],
					'active' => ['format' => 'yesno'],
				]
			); ?>
		</div>

		<?php
		$this->Table->reset();
		foreach ($catalogItem['ShippingRule'] as $shippingRule) {
			$range = !empty($shippingRule['min_quantity']) ? $shippingRule['min_quantity'] : '...';
			$range .= ' - ' . (!empty($shippingRule['max_quantity']) ? $shippingRule['max_quantity'] : '...');
			$this->Table->cells(array(
				['If:', '&nbsp;'],
				[$range, 'Quantity Range'],
				['Then Add:', '&nbsp;'],
				array($this->DisplayText->cash($shippingRule['amt']), 'Flat Rate'),
				array($this->DisplayText->cash($shippingRule['per_item']), 'Per-Item'),
				array(round($shippingRule['pct'] * 100) . '%', 'Percent of Sub-Total')
			), true);
		}
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo $this->Layout->actionMenu(['edit'], [
					'url' => ['action' => 'shipping_rules', $catalogItem['CatalogItem']['id']],
					'class' => 'pull-right'
				]); ?>
				<span class="panel-title">Shipping Rules</span>
			</div>
			<?php echo $this->Table->output(); ?>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo $this->Layout->actionMenu(['add'], [
					'url' => ['controller' => 'product_inventory_adjustments', 'action' => 'add'],
					'class' => 'pull-right'
				]);?>
				<span class="panel-title">Inventory History</span>
			</div>
			<?php echo $this->element('product_inventory_adjustments/table'); ?>
		</div>

	</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<?php echo $this->CatalogItem->thumb($catalogItem['CatalogItem'], [
				'dir' => 'mid', 
				'class' => false,
			]);?>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				Production Options
				<?php echo $this->Html->link('<i class="fa fa-plus"></i>', [
						'controller' => 'catalog_item_options',
						'action' => 'add',
						$catalogItem['CatalogItem']['id'],
					], [
						'escape' => false, 
						'class' => 'btn btn-default pull-right ajax-modal',
						'data-modal-title' => 'Add option',
					]
				); ?>
			</div>
			<div class="panel-body">
				<?php foreach ($catalogItem['CatalogItemOption'] as $catalogItemOption): ?>			
					<div class="panel panel-default">
						<div class="panel-heading">
							<?php echo $catalogItemOption['title']; ?>
							<div class="pull-right btn-group">
								<?php echo $this->Html->link(
									'<i class="fa fa-edit"></i>', [
										'controller' => 'catalog_item_options',
										'action' => 'edit',
										$catalogItemOption['id']
									], [
										'class' => 'btn btn-default ajax-modal',
										'data-modal-title' => 'Edit Option',
										'escape' => false,
									]
								); ?>
								<?php echo $this->Html->link(
									'<i class="fa fa-times"></i>', [
										'controller' => 'catalog_item_options',
										'action' => 'delete',
										$catalogItemOption['id']
									], [
										'class' => 'btn btn-danger',
										'escape' => false,
									], 
									'Delete this option and all of it\'s associated choices?'
								); ?>
							</div>
						</div>
						<ul class="list-group">
						<?php foreach ($catalogItemOption['ProductOptionChoice'] as $productOptionChoice): ?>
							<li class="list-group-item"><?php echo $productOptionChoice['title']; ?></li>
						<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading"><span class="panel-title">Categories</span></div>
			<div class="panel-body">
				<?php echo $this->CatalogItem->categories($catalogItemCategories); ?>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo $this->Html->link('<i class="fa fa-plus"></i>',
					['controller' => 'catalog_item_packages', 'action' => 'add', $catalogItem['CatalogItem']['id']],
					['escape' => false, 'class' => 'pull-right btn btn-default ajax-modal', 'data-modal-title' => 'Add Package']
				); ?>
				<span class="panel-title">Packages</span>
			</div>
			<?php if (!empty($catalogItem['CatalogItemPackageChild'])): ?>
				<ul class="list-group">
				<?php foreach ($catalogItem['CatalogItemPackageChild'] as $catalogItemPackageChild):
					$url = ['action' => 'view', $catalogItemPackageChild['CatalogItemChild']['id']];
					?>
					<li class="list-group-item">
						<div class="pull-right">
							<?php echo $this->ModelView->actionMenu(['edit', 'delete'], [
									'url' => [
										'controller' => 'catalog_item_packages', 
										'action' => 'view', 
										$catalogItemPackageChild['id']
									]
								]); ?>
						</div>
						<?php echo $this->Html->link(
								$catalogItemPackageChild['CatalogItemChild']['title'], 
								$url
							);
						?>
						<span class="label label-default">
							<?php echo number_format($catalogItemPackageChild['quantity']); ?>
						</span>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>				
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo $this->Html->link('<i class="fa fa-plus"></i>', [
						'controller' => 'catalog_item_images',
						'action' => 'add',
						$catalogItem['CatalogItem']['id']
					], [
						'class' => 'pull-right btn btn-default ajax-modal', 
						'escape' => false,
						'data-modal-title' => 'Add Image',
					]
				); ?>
				<span class="panel-title">Photos
			</span></div>
			<?php
				echo $this->CatalogItemImage->mediaList($catalogItem['CatalogItemImage'], [
					'dir' => 'thumb',
					'size' => 'thumb',
					'title' => false,
					'link' => true,
					'actionMenu' => ['edit', 'delete', 'move_up', 'move_down'],
				]);
			?>
		</div>
	</div>
</div>