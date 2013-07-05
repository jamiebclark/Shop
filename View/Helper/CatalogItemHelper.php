<?php
define('SHOP_ROOT', APP . 'Plugin' . DS . 'Shop' . DS);
define('SHOP_WWW_ROOT', SHOP_ROOT . 'webroot' . DS);
App::uses('ModelViewHelper', 'Layout.View/Helper');
class CatalogItemHelper extends ModelViewHelper {
	var $name = 'CatalogItem';
	var $helpers = array(		'Html', 		'Form', 		'Photo', 	);
	
	var $thumbDir = 'catalog_item_images/';
	
	/*
	function media($catalogItem, $options = array()) {
		$options = array_merge(array(
			'url' => $this->url($catalogItem),
			'dir' => 'thumb',
		), $options);
		$thumbOptions = $this->addClass($options, 'media-object');
		$thumb = $this->Html->div('pull-left', $this->thumb($catalogItem, $thumbOptions));
		$title = $catalogItem['title'];
		if (!empty($options['url'])) {
			$title = $this->Html->link($title, $options['url']);
		}
		$body = $this->Html->tag('h2', $title, array('class' => 'media-title'));
		return $this->Html->div('catalog-item media', $thumb . $this->Html->div('media-body', $body));
	}
	*/
	
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
		$filename = 'Shop.' . $src;
		if (is_file($file)) {
			return isset($options['filename']) ? $filename : $this->Html->image($filename, $options);
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
		if (is_array($qty)) {
			$result = $qty;
			$qty = $result['stock'];
			$unlimited = $result['unlimited'];
		}
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
	
	function price($catalogItem) {
		$out = '';
		if ($catalogItem['sale'] > 0) {
			$out .= $this->cash($catalogItem['sale'], array('class' => 'sale'));
			$out .= ' ';
			$out .= $this->cash($catalogItem['price'], array('class' => 'old'));
		} else {
			$out .= $this->cash($catalogItem['price']);
		}
		return $this->Html->tag('span', $out, array('class' => 'catalog-item-price'));
	}		function cash($num, $options = array()) {
		$options = array_merge(array('tag' => 'font'), $options);
		extract($this->addClass($options, 'cash'));
		$out = '$' . number_format($num, $num == round($num) ? 0 : 2);
		if (!empty($tag)) {
			$out = $this->Html->tag($tag, $out, compact('class', 'style'));
		}
		return $out;	}
}