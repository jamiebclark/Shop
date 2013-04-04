<?php
if (empty($class)) {
	$class = '';
}
$class .= ' catalog-item-image-thumb-list clearfix';
$count = 0;
if (empty($dir)) {
	$dir = 'thumb';
}
?>
<div class="<?php echo $class;?>">
<?php
foreach ($catalogItemImages as $catalogItemImage):
	if (!empty($catalogItemImage['CatalogItemImage'])) {
		$catalogItemImage = $catalogItemImage['CatalogItemImage'];
	}
	$url = array(
		'controller' => 'product_images',
		'action' => 'view',
		$catalogItemImage['id'],
	);
	echo $this->CatalogItem->thumb($catalogItemImage, compact('dir', 'url'));
	if (!empty($limit) && ++$count >= $limit) {
		break;
	}
endforeach;
?></div>
