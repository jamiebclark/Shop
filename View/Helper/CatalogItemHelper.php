<?php
define('SHOP_ROOT', APP . 'Plugin' . DS . 'Shop' . DS);
define('SHOP_WWW_ROOT', SHOP_ROOT . 'webroot' . DS);

class CatalogItemHelper extends AppHelper {
	var $name = 'CatalogItem';
	var $helpers = array(		'Html', 		'Form', 		'Photo', 	);
	
	var $thumbDir = 'catalog_item_images/';
	
	function thumb($catalogItem, $options = array()) {
		$src = $this->thumbDir;
		if (!empty($options['dir'])) {
			$options = $this->addClass($options, $options['dir']);
			$src .= $options['dir'] . '/';
			unset($options['dir']);
		}
		if (!empty($options['url']) && $options['url'] === true) {
			$options['url'] = $this->url($catalogItem);
		}
		$src .= $catalogItem['filename'];
		$file = str_replace(array('/','\\'), DS, SHOP_WWW_ROOT . 'img' . DS . $src);
		if (is_file($file)) {
			return $this->Html->image('Shop.' . $src, $options);
		} else {
			return null;
		}
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
	
	function notes($catalogItem) {
		$notes = array();
		if ($catalogItem['min_quantity'] > 1) {
			$notes[] = 'Minimum order of ' . number_format($catalogItem['min_quantity']);
		}
		if ($catalogItem['quantity_per_pack'] > 1) {
			$notes[] = 'This is a pack of ' . number_format($catalogItem['quantity_per_pack']);
		}
		if ($catalogItem['stock'] < 10) {
			$notes[] = 'Limited stock';
		}
		if (empty($notes)) {
			return '';
		} else {
			return $this->Html->div('catalog-item-notes', 
				'<ul><li>'.implode('</li><li>', $notes).'</li></ul>'
			);
		}
	}
	
	function getInventoryClass($qty, $unlimited = false) {
		$warning = 10;
		if ($qty <= 0 && !$unlimited) {
			$class = 'error';
		} else {
			$class = ($unlimited || $qty > $warning) ? 'success' : 'warning';
		}
		return $class;
	}
	
	function inventory($qty = 0, $unlimited = false) {
		$out = number_format($qty);
		if ($unlimited) {
			$out = 'Unlimited';
			$qty = 1;
		}
		$class = $this->getInventoryClass($qty, $unlimited);
		if ($class == 'error') {
			$class = 'important';
		}
		$class = 'label label-' . $class;
		return $this->Html->tag('span', $out, compact('class'));
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