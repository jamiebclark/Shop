<?php echo $this->Layout->defaultHeader($catalogItem['CatalogItem']['id']); ?>
<div class="row">
<div class="span8">
<?php
echo $this->Layout->infoResultTable($catalogItem['CatalogItem'], array(
		'title',
		'short_description',
		'description',
		'price' => array('format' => 'cash'),
		'sale' => array('format' => 'cash', 'notEmpty', 'class' => 'sale'),
		'stock' => array(
			'label' => 'Currently In Stock',
			'format' => 'number',
			'url' => array('controller' => 'products', 'action' => 'index', $catalogItem['CatalogItem']['id'])
		),
		'unlimited' => array(
			'format' => 'yesno',
			'label' => 'Unlimited Inventory',
		),			
		'min_quantity' => array('label' => 'Minimum Quantity per Order'),
		'quantity_per_pack',
		'created' => array('format' => 'date'),
		'modified' => array('format' => 'date', 'label' => 'Last Modified'),
		'active' => array('format' => 'yesno'),
	)
);

$url = array(
	'action' => 'shipping_rules',
	$catalogItem['CatalogItem']['id']
);
echo $this->Layout->headingActionMenu('Shipping Rules', array('edit' => $url), compact('url'));

$this->Table->reset();
foreach ($catalogItem['ShippingRule'] as $shippingRule) {
	$range = !empty($shippingRule['min_quantity']) ? $shippingRule['min_quantity'] : '...';
	$range .= ' - ' . (!empty($shippingRule['max_quantity']) ? $shippingRule['max_quantity'] : '...');
	$this->Table->cells(array(
		array('If:', '&nbsp;'),
		array($range, 'Quantity Range'),
		array('Then Add:', '&nbsp;'),
		array($this->DisplayText->cash($shippingRule['amt']), 'Flat Rate'),
		array($this->DisplayText->cash($shippingRule['per_item']), 'Per-Item'),
		array(round($shippingRule['pct'] * 100) . '%', 'Percent of Sub-Total')
	), true);
}
echo $this->Table->output();
?>
</div>
<div class="span4">
	<div class="content-box">
	<?php foreach ($catalogItem['CatalogItemOption'] as $catalogItemOption): ?>			
		<h4><?php echo $catalogItemOption['title']; ?></h4>
		<ul>
		<?php foreach ($catalogItemOption['ProductOptionChoice'] as $productOptionChoice): ?>
			<li><?php echo $productOptionChoice['title']; ?></li>
		<?php endforeach; ?>
		</ul>
	<?php endforeach; ?>
	</div>
	<div class="content-box">
		<h3>Categories</h3>
		<?php echo $this->CatalogItem->categories($catalogItemCategories); ?>
		<?php $url = array('action' => 'packages', $catalogItem['CatalogItem']['id']); ?>
		<h3><?php echo $this->Html->link('Packages', $url);?></h3>
		<?php
		$this->Table->reset();
		if (!empty($catalogItem['CatalogItemPackageChild'])) {
			foreach ($catalogItem['CatalogItemPackageChild'] as $CatalogItemPackageChild) {
				$this->Table->cells(array(
					array(
						$this->CatalogItem->thumb(
							$CatalogItemPackageChild['CatalogItemChild'], 
							array('dir' => 'thumb', 'url' => $url)
						)
					), array(
						$this->Html->link(
							$CatalogItemPackageChild['CatalogItemChild']['title'], 
							$url
						), 'CatalogItem'
					), array(
						$this->Html->link(
							number_format($CatalogItemPackageChild['quantity']), 
							$url
						), 'Quantity')
				), true);
			}
			echo $this->Table->output();
		}
		?>
	</div>
	<div class="content-box">
		<?php echo $this->Layout->headingActionMenu(
			'Photos', 
			array('index'), 
			array('url' => array(
				'controller' => 'catalog_item_images',
				'action' => 'index',
				$catalogItem['CatalogItem']['id']
			))
		);
		$this->Table->reset();
		foreach ($catalogItem['CatalogItemImage'] as $catalogItemImage) {
			$url = array('controller' => 'catalog_item_images', 'action' => 'view', $catalogItemImage['id']);
			$this->Table->cells(array(
				array($this->CatalogItem->thumb(
					$catalogItemImage, 
					array('dir' => 'thumb', 'url' => $url)
				)), 
				array($this->CatalogItem->actionMenu(array('view', 'edit', 'delete', 'move_up', 'move_down'), compact('url'))),
			), true);
		}
		echo $this->Table->output(array('class' => 'orderCatalogItemsForm'));
	?>
	</div>
</div>
</div>