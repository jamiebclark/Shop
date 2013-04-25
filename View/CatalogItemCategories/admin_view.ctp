<?php
echo $this->element('catalog_item_categories/path');
echo $this->Layout->defaultHeader($catalogItemCategory['CatalogItemCategory']['id']);
?>
<h2>Child Categories</h2>
<div class="well">
	<ul>
	<?php foreach ($children as $child): ?>
		<li><?php echo $this->Html->link($child['CatalogItemCategory']['title'], array(
			'action' => 'view', $child['CatalogItemCategory']['id'],
		));?></li>
	<?php endforeach; ?>
	</ul>
</div>

<h2>Catalog Items in Category</h2>
<div class="well">
	<?php 
	foreach ($catalogItems as $k => $catalogItem):
		if ($k) {
			echo ', ';
		}
		$direct = $catalogItem['CatalogItemCategory']['parent_id'] == $catalogItemCategory['CatalogItemCategory']['id'];
		echo $this->CatalogItem->link($catalogItem['CatalogItem'], array(
			'class' => $direct ? 'direct' : null
		));
	endforeach;
	?>
</div>