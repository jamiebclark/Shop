<?php
echo $this->element('catalog_item_categories/path');
if (!empty($catalogItemCategory['CatalogItemCategory']['parent_id'])) {
	echo $this->Html->tag('h5', $this->Html->link(
		'Up one level', 
		array('action' => 'view', $catalogItemCategory['CatalogItemCategory']['parent_id'])
	));
}

echo $this->Layout->defaultHeader($catalogItemCategory['CatalogItemCategory']['id'], array(), array(
	'title' => $catalogItemCategory['CatalogItemCategory']['title'],
));
?>
<h3>Child Categories</h3>
<div class="well">
	<ul>
	<?php foreach ($children as $child): ?>
		<li><?php echo $this->Html->link($child['CatalogItemCategory']['title'], array(
			'action' => 'view', $child['CatalogItemCategory']['id'],
		));?></li>
	<?php endforeach; ?>
	</ul>
</div>

<h3>Catalog Items in Category</h3>
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