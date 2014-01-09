<?php
foreach ($productInventoryAdjustments as $adjustment):
	$this->Table->cells(array(
		array(
			$this->Calendar->niceShort($adjustment['ProductInventoryAdjustment']['available']),
			'Date',
		), array(
			$this->CatalogItem->link($adjustment['CatalogItem']),
			'Catalog Item',
		), array(
			$this->Product->link($adjustment['Product'], array('subTitle' => true)),
			'Product',
		), array(
			number_format($adjustment['ProductInventoryAdjustment']['quantity']),
			'Amount',
		), array(
			$adjustment['ProductInventoryAdjustment']['title'],
			'Title',
		), array(
			$this->ModelView->actionMenu(array('edit', 'delete'), $adjustment['ProductInventoryAdjustment']),
			'Actions',
		)
	), true);
endforeach; 
echo $this->Table->output(array(
	'paginate' => true,
));