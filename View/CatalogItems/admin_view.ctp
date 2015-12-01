<?php echo $this->Layout->defaultHeader($catalogItem['CatalogItem']['id'], null, [
	'title' => $catalogItem['CatalogItem']['title']
]); ?>
<div class="row">
	<div class="col-sm-8">
		<div class="panel panel-default">
			<div class="panel-heading">Short Description</div>
			<div class="panel-body">
				<?php echo $this->DisplayText->text($catalogItem['CatalogItem']['short_description'], ['class' => 'well']); ?>
			</div>

			<div class="panel-heading">Description</div>
			<div class="panel-body">
				<?php echo $this->DisplayText->text($catalogItem['CatalogItem']['description'], ['class' => 'well']); ?>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">About</div><?php
			echo $this->Layout->infoResultTable($catalogItem['CatalogItem'], [
					'price' => ['format' => 'cash'],
					'sale' => ['format' => 'cash', 'notEmpty', 'class' => 'sale'],
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
				Shipping Rules
			</div>
			<?php echo $this->Table->output(); ?>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo $this->Layout->actionMenu(['add'], [
					'url' => ['controller' => 'product_inventory_adjustments', 'action' => 'add'],
					'class' => 'pull-right'
				]);?>
				Inventory History
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
		<?php foreach ($catalogItem['CatalogItemOption'] as $catalogItemOption): ?>			
			<div class="panel-heading"><?php echo $catalogItemOption['title']; ?></div>
			<ul class="list-group">
			<?php foreach ($catalogItemOption['ProductOptionChoice'] as $productOptionChoice): ?>
				<li class="list-group-item"><?php echo $productOptionChoice['title']; ?></li>
			<?php endforeach; ?>
			</ul>
		<?php endforeach; ?>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">Categories</div>
			<div class="panel-body">
				<?php echo $this->CatalogItem->categories($catalogItemCategories); ?>
			</div>
		</div>

		<?php
		$this->Table->reset();
		if (!empty($catalogItem['CatalogItemPackageChild'])):
			foreach ($catalogItem['CatalogItemPackageChild'] as $catalogItemPackageChild):
				$url = ['action' => 'view', $catalogItemPackageChild['CatalogItemChild']['id']];
				$this->Table->cells(array(
					array(
						$this->CatalogItem->thumb(
							$catalogItemPackageChild['CatalogItemChild'], 
							['dir' => 'thumb', 'url' => $url]
						)
					), array(
						$this->Html->link(
							$catalogItemPackageChild['CatalogItemChild']['title'], 
							$url
						), 'CatalogItem'
					), array(
						$this->Html->link(
							number_format($catalogItemPackageChild['quantity']), 
							$url
						), 'Quantity')
				), true);
			endforeach;
		endif;
		?>	
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo $this->Html->link('Packages',
					['action' => 'packages', $catalogItem['CatalogItem']['id']]
				); ?>
			</div>
			<?php echo $this->Table->output(); ?>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo $this->Layout->actionMenu(['index'], [
					'url' => [
						'controller' => 'catalog_item_images',
						'action' => 'index',
						$catalogItem['CatalogItem']['id']
					],
					'class' => 'pull-right'
				]); ?>
				Photos
			</div>
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