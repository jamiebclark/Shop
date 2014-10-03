<?php
$count = 0;
$default = array(
	'current' => false,
	'perRow' => 3,
	'limit' => null,
	'dir' => 'thumb',
);
extract(array_merge($default, compact(array_keys($default))));

$spanClass = 'col-sm-' . floor(12 / $perRow);
?>
<div class="thumbnails row">
	<?php foreach ($catalogItemImages as $catalogItemImage):
		if (!empty($catalogItemImage['CatalogItemImage'])) {
			$catalogItemImage = $catalogItemImage['CatalogItemImage'];
		}
		$thumbnailClass = 'thumbnail';
		if ($current == $catalogItemImage['id']) {
			$thumbnailClass .= ' thumbnail-current';
		}
		$url = array(
			'controller' => 'catalog_item_images',
			'action' => 'view',
			$catalogItemImage['id'],
		);
		?>
		<div class="<?php echo $spanClass; ?>">
			<div class="<?php echo $thumbnailClass; ?>">
				<?php echo 	$this->CatalogItem->thumb($catalogItemImage, compact('dir','url') + array(
					'class' =>  '',
				)); ?>
			</div>
		</div>
		<?php if (!empty($limit) && ++$count >= $limit) {
			break;
		} ?>
	<?php endforeach; ?>
</div>
