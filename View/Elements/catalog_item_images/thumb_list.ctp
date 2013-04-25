<?php
$count = 0;
$default = array(
	'perRow' => 3,
	'limit' => null,
	'dir' => 'thumb',
);
extract(array_merge($default, compact(array_keys($default))));

$spanClass = 'span' . floor(12 / $perRow);
?>
<div class="row-fluid">
	<ul class="thumbnails">
	<?php
	foreach ($catalogItemImages as $catalogItemImage):
		if (!empty($catalogItemImage['CatalogItemImage'])) {
			$catalogItemImage = $catalogItemImage['CatalogItemImage'];
		}
		$url = array(
			'controller' => 'catalog_item_images',
			'action' => 'view',
			$catalogItemImage['id'],
		);
		echo $this->Html->tag('li', 
			$this->CatalogItem->thumb($catalogItemImage, compact('dir','url')+array('class'=>'thumbnail')),
			array('class' => $spanClass)
		);
		if (!empty($limit) && ++$count >= $limit) {
			break;
		}
	endforeach;
	?>
	</ul>
</div>
