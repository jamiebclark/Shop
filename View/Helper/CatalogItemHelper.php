<?php
class CatalogItemHelper extends AppHelper {
	var $name = 'CatalogItem';
	var $helpers = array(		'Html', 		'Form', 		'Photo', 	);
	
	var $thumbDir = 'catalog_item_images/';
	
	function thumb($CatalogItem, $options = array()) {
		$options['thumbDir'] = $this->thumbDir;
		if ($url = Param::keyValCheck($options, 'url')) {
			if ($url === true) {
				$url = $this->url($CatalogItem);
			}
			$options['url'] = $url;
		}		return "THUMBNAIL";
		//return $this->Photo->thumb($CatalogItem, $options);
	}
	
	
	function link ($CatalogItem, $options = array(), $onClick = null) {
		$options = array_merge(array(
			'class' => '',
			'escape' => true,
		), $options);
		$options['class'] .= ' catalog-item';
		$url = Param::keyCheck($options, 'url', false, $this->url($CatalogItem));
		
		return $this->Html->link($CatalogItem['title'], $url, $onClick);
	}
	
	function url($CatalogItem) {
		return array(
			'controller' => 'catalog_items', 
			'action' => 'view', 
			$CatalogItem['id'],
			Inflector::slug($CatalogItem['title'])
		);
	}
	
	function inventory($qty = 0) {
		 return $this->Html->tag(
			'font', 
			number_format($qty), 
			array(
				'class' => ($qty > 0 ? 'positive' : 'negative') . ' inventory'
			)
		);
	}
	
	function price($CatalogItem) {
		$output = '';
		if ($CatalogItem['sale'] > 0) {
			$output .= $this->Html->tag('font',
				$this->cash($CatalogItem['sale']),
				array('class' => 'salePrice')
			);
			$output .= ' ';
			$output .= $this->Html->tag('font',
				$this->cash($CatalogItem['price']),
				array('class' => 'saleOldPrice')
			);
		} else {
			$output .= $this->Html->tag('font',
				$this->cash($CatalogItem['price']),
				array('class' => 'price')
			);
		}
		return $this->Html->tag('span', $output, array('class' => 'catalogItemPrice'));
	}		function cash($num) {		return '$' . number_format($num, $num == round($num) ? 0 : 2);	}
}