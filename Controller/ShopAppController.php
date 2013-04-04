<?php
Configure::write('debug', 2);
App::import('Lib', 'Shop.Param');
App::import('Lib', 'Shop.InflectorPlus');
class ShopAppController extends AppController {
	var $components = array(
		'FormData.FormData' => array('plugin' => 'Shop'),
		'Session',
	);
	
	var $helpers = array(
		'Layout.Asset',
		'Layout.Crumbs',
		'Layout.Calendar', 
		'Layout.DisplayText',
		'Layout.FormLayout',
		'Layout.Grid',
		'Layout.Iconic',
		'Layout.Layout',
		//'Layout.DateBuild', 
		'Layout.Table',
	);
	
	function beforeRender() {
		parent::beforeRender();
		$loggedUserId = 1;
		$isShopAdmin = true;
		$this->set(compact('loggedUserId', 'isShopAdmin'));
	}
	
	function _redirectMsg($redirect = true, $msg = null) {
		if (!empty($msg)) {
			$this->Session->setFlash($msg);
		}
		if ($redirect !== false) {
			if ($redirect === true) {
				$redirect = $this->referer();
			}
			return $this->redirect($redirect);
		}
		return null;
	}
}