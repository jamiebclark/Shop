<?php
//Configure::write('debug', 0);
App::import('Lib', 'Shop.Param');
App::import('Lib', 'Shop.InflectorPlus');

require_once '../Config/core.php';

class ShopAppController extends AppController {
	public $components = array(
		'Shop.Constants',
		'FormData.FormData' => array('plugin' => 'Shop'),
		'Layout.FormLayout',
		'Session',
		'Flash',
	);
	
	public $helpers = array(
		'CakeAssets.Asset',
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

/**
 * Works with the Permission Component
 * TODO: Remove this dependency
 **/
	public $permissions = [
		'prefix' => ['admin' => ['type' => ['staff']]],
	];
	
	public function beforeRender() {
		parent::beforeRender();
		// Checks one last time that global settings have been set
		if ($this->layout != 'setup' && !empty($this->request->params['prefix'])) {
			$this->layout = 'admin';
		}
	}

	public function redirectHome() {
		return $this->redirect(array('controller' => 'catalog_items', 'action' => 'index'));
	}
	
	public function redirectMsg($redirect = true, $msg = null, $success = null) {
		if (!empty($msg)) {
			$element = 'alert';
			if ($success === false) {
				$element = 'danger';
			} else if ($success == true) {
				$element = 'success';
			}
			$this->Flash->set(__($msg), compact('element'));
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