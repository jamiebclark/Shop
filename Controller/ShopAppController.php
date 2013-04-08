<?php
//Configure::write('debug', 0);
App::import('Lib', 'Shop.Param');
App::import('Lib', 'Shop.InflectorPlus');

require_once '../Config/core.php';

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
		
		if (!empty($this->request->params['prefix'])) {
			$this->layout = 'admin';
		}
	}
	
	function _redirectMsg($redirect = true, $msg = null) {
		if (!empty($msg)) {
			$this->Session->setFlash($msg);
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