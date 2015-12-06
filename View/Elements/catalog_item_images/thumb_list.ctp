<?php
$count = 0;
$default = [
	'current' => false,
	'perRow' => 3,
	'limit' => null,
	'dir' => 'thumb',
	'div' => '',
	'spanClass' => '',
	'thumbnailClass' => '',
	'imageClass' => '',
	'imageLoadTarget' => null,
	'imageLoadSize' => 'thumb',
];
extract(array_merge($default, compact(array_keys($default))));

$div = trim($div . ' thumbnails row');
$spanClass = trim($spanClass . ' col-sm-' . floor(12 / $perRow));
$thumbnailClass = trim($thumbnailClass . ' thumbnail ' . $spanClass);

?>
<div class="<?php echo $div; ?>">
	<?php foreach ($catalogItemImages as $catalogItemImage):
		if (!empty($catalogItemImage['CatalogItemImage'])) {
			$catalogItemImage = $catalogItemImage['CatalogItemImage'];
		}
		$tmpThumbnailClass = $thumbnailClass;
		if ($current == $catalogItemImage['id']) {
			$tmpThumbnailClass .= ' thumbnail-current';
		}
		$url = [
			'controller' => 'catalog_item_images',
			'action' => 'view',
			$catalogItemImage['id'],
		];

		$imageOptions = compact('dir','url') + [
			'class' => $imageClass,
		];

		if (!empty($imageLoadSize) && !empty($imageLoadTarget)) {
			$imageOptions['data-image-load-src'] = $this->CatalogItem->imageSrc($catalogItemImage, ['dir' => $imageLoadSize]);
			$imageOptions['data-image-load-target'] = $imageLoadTarget;
		}

		?>
		<div class="<?php echo $tmpThumbnailClass; ?>">
			<?php echo 	$this->CatalogItem->thumb($catalogItemImage, $imageOptions); ?>
		</div>

		<?php if (!empty($limit) && ++$count >= $limit) {
			break;
		} ?>
	<?php endforeach; ?>
</div>
