<?php
//Configure::write('debug', 0);
App::import('Lib', 'Shop.Param');
App::import('Lib', 'Shop.InflectorPlus');

require_once '../Config/core.php';

class ShopAppController extends AppController {
	var $components = array(
		'FormData.FormData' => array('plugin' => 'Shop'),
		'Layout.FormLayout',
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
	
	function beforeFilter() {
		//Makes sure everything is loaded
		$vars = array();
		if (!defined('SHOP_VARS_LOADED')) {
			$vars['noBootstrap'] = true;
		}
		if (!empty($vars)) {
			$this->set($vars);
			$this->helpers = array('Layout.Layout');
			$this->layout = 'setup';
			$this->render('Shop./CatalogItems/setup');
		}
		parent::beforeFilter();		
	}
	
	function beforeRender() {
		parent::beforeRender();
		
		//Set Variables
		$loggedUserId = 1;
		$isShopAdmin = true;
		$this->set(compact('loggedUserId', 'isShopAdmin'));
		
		if ($this->layout != 'setup' && !empty($this->request->params['prefix'])) {
			$this->layout = 'admin';
		}
	}
	
	function _redirectHome() {
		return $this->redirect(array('controller' => 'catalog_items', 'action' => 'index'));
	}
	
	function _redirectMsg($redirect = true, $msg = null, $success = null) {
		if (!empty($msg)) {
			$type = 'info';
			if ($success === false) {
				$type = 'error';
			} else if ($success == true) {
				$type = 'success';
			}
			$this->Session->setFlash(__($msg), 'default', array(
				'class' => 'alert alert-' . $type
			));
		}
		if ($redirect !== false) {
			if ($redirect === true) {
				$redirect = $this->referer();
			}
			if (headers_sent($file, $line)) {
				throw new Exception("Cannot redirect. Headers were already sent in $file on line $line");
			}
			return $this->redirect($redirect);
		}
		return null;
	}
}