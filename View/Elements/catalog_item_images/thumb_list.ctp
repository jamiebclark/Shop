<?php
if (empty($class)) {
	$class = '';
}
$class .= ' productImageThumbList clearfix';
?>

<div class="<?php echo $class;?>">
<?php
$count = 0;
if (empty($dir)) {
	$dir = 'thumb';
}
foreach ($productImages as $productImage) {
	if (!empty($productImage['ProductImage'])) {
		$productImage = $productImage['ProductImage'];
	}
	$url = array(
		'controller' => 'product_images',
		'action' => 'view',
		$productImage['id'],
	);
	
	echo $this->CatalogItem->thumb($productImage, compact('dir', 'url'));
	$count++;
	if (!empty($limit) && $count >= $limit) {
		break;
	}
}
?>
</div>
