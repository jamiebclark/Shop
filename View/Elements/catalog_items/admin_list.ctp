<?php
$this->Table->reset();
$dir = 'thumb';
foreach ($products as $productInfo) {
	$url = array('action' => 'view', $productInfo['Product']['id']);
	$active = $productInfo['Product']['active'];
	$this->Table->cells(array(
		array($this->Product->thumb($productInfo['Product'], compact('url', 'dir')), null, null, null, array('width' => 80)),
		array($this->Product->link($productInfo['Product'], compact('url'))),
		array($this->DisplayText->positiveNumber($productInfo['Product']['stock']), 'In Stock'),
		array($this->Layout->actionMenu(array('view', 'edit', 'active', 'delete'), compact('url', 'active')), 'Actions')
	), true);
}
echo $this->Table->table(array('paginate'));
?>
	