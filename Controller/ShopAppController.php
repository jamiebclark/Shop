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