<?php
/**
 * Behavior that automatcially syncs fields between model and the Invoice model
 *
 **/
App::uses('InflectorPlus', 'Layout.Lib');
class InvoiceSyncBehavior extends ModelBehavior {
	public $settings = [];
	
	// Stores invoice deleting info between beforeDelete and afterDelete
	private $_deleteInvoiceId;
/**
 * Initiate behavior for the model using specific settings.
 *
 * Available settings:
 *
 * - fields: (array) The fields to sync between the model and Invoice, using the format:
 *   	modelField => invoiceField
 * - title: (string) The human-formatted title of the model, used for the Invoice view
 *
 * @param Model $Model Model using the behavior
 * @param array $settings Settings to override for model.
 * @return void
 **/
	public function setup(Model $Model, $settings = []) {
		$default = array(
			'title' => InflectorPlus::humanize($Model->alias),
			'fields' => [],
		);
		if (empty($Model->belongsTo['Invoice'])) {
			throw new Exception("Cannot use InvoiceSync Behavior without having {$Model->alias} belongTo Invoice");
		}
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $default;
		}
		$this->settings[$Model->alias] = array_merge(
			$this->settings[$Model->alias], 
			(array) $settings
		);
		
		$Invoice = ClassRegistry::init('Shop.Invoice');
		$Invoice->bindModel(array('hasOne' => array($Model->alias => array('className' => $this->getModelName($Model)))), false);
		if (empty($Invoice->syncedModels)) {
			$Invoice->syncedModels = [];
		}
		$Invoice->syncedModels[$Model->alias] = $Model->alias;
	}
	
	public function afterSave(Model $Model, $created, $options = []) {
		//Prevents infinite looping while still keeping Model's afterSave functions
		if (empty($Model->_syncingWithInvoice)) {
			$this->copyModelToInvoice($Model, $Model->id);
		}
		return parent::afterSave($Model, $created, $options);
	}
	
	public function beforeDelete(Model $Model, $cascade = true) {
		$result = $Model->find('first', array(
			'contain' => ['Invoice'],
			'conditions' => array($Model->escapeField($Model->primaryKey) => $Model->id)
		));
		
		// If invoice hasn't been paid or if it's empty, delete the invoice too
		if (!empty($result['Invoice']['id']) && (empty($result['Invoice']['paid']) || empty($result['Invoice']['amt']))) {
			$this->_deleteInvoiceId = $result['Invoice']['id'];
		}
		return parent::beforeDelete($Model, $cascade);
	}

	public function afterDelete(Model $Model) {
		if (!empty($this->_deleteInvoiceId)) {
			$Model->Invoice->deleteAll(['Invoice.id' => $this->_deleteInvoiceId], true, false);
			$this->_deleteInvoiceId = null;
		}
		return parent::afterDelete($Model);
	}
	
/**
 * Finds all fields stored in settings and saves them to Invoice
 *
 * @param Model $Model Model from where to find the source
 * @param int $id Model ID
 * @return bool Save is successful
 **/
	public function copyModelToInvoice($Model, $id, $fields = null) {
		//Prevents infinite looping while still keeping Model's afterSave functions
		if (!empty($Model->_syncingWithInvoice) || !empty($Model->Invoice->_syncingWithModel)) {
			return null;
		}
	
		$Model->Invoice->_syncingWithModel = true;

		$settings =& $this->settings[$Model->alias];
		$invoiceSchema = $Model->Invoice->schema();
		
		if (empty($fields)) {
			$fields = $settings['fields'];
		}

		$result = $Model->find('first', array(
			'fields' => '*', 
			'link' => ['Shop.Invoice'],
			'conditions' => array($Model->escapeField() => $id)
		));

		$data = array($Model->alias => compact('id'), 'Invoice' => array(
			'model' => $this->getModelName($Model),
			'model_title' => $settings['title'],
			'model_id' => $id,
		));
		if (!empty($result['Invoice']['id'])) {
			$data['Invoice']['id'] = $result['Invoice']['id'];
		}
		
		if (!empty($fields)) {
			foreach ($fields as $modelField => $invoiceField) {
				if (is_numeric($modelField)) {
					$modelField = $invoiceField;
				}
				if (!empty($result[$Model->alias][$modelField])) {
					$value = $result[$Model->alias][$modelField];
				} else if (!empty($invoiceSchema[$invoiceField]['null'])) {
					$value = null;
				} else if (in_array($invoiceSchema[$invoiceField]['type'], ['integer', 'float'])) {
					$value = 0;
				} else {
					$value = '';
				}
				$data['Invoice'][$invoiceField] = $value;
			}
		}
		$Model->Invoice->create();
		$success = $Model->Invoice->saveAll($data, ['callbacks' => false, 'validate' => false]);
		$Model->Invoice->_syncingWithModel = null;
		return $success;
	}
	
/**
 * Finds all fields stored in settings and saves them to Model from Invoice
 *
 * @param Model $Model Model where Invoice fields will be saved
 * @param int $id Invoice id
 * @return bool/null Save is successful, null if no fields are present
 **/
	public function copyInvoiceToModel($Model, $invoiceId, $fields = null) {
		$alias = $Model->alias;

		//Prevents infinite looping
		if (!empty($Model->_syncingWithInvoice) || !empty($Model->Invoice->_syncingWithModel)) {
			return null;
		}
		$Model->_syncingWithInvoice = true;
		
		$success = null;
		$settings =& $this->settings[$alias];
		if (empty($fields)) {
			$fields = $settings['fields'];
		}
		if (!empty($fields)) {
			$result = $Model->Invoice->find('first', [
				'contain' => [$alias],
				'conditions' => ['Invoice.id' => $invoiceId]
			]);
			if (empty($result[$alias])) {
				$success = null;
			} else {
				$data = [
					$alias => [$Model->primaryKey => $result[$alias][$Model->primaryKey]], 
					'Invoice' => ['id' => $invoiceId]
				];
				foreach ($fields as $modelField => $invoiceField) {
					if (is_numeric($modelField)) {
						$modelField = $invoiceField;
					}
					$data[$alias][$modelField] = $result['Invoice'][$invoiceField];
				}
				$success = $Model->saveAll($data, ['callbacks' => false, 'validate' => false]);
				if (method_exists($Model, 'afterCopyInvoiceToModel')) {
					$Model->afterCopyInvoiceToModel($result[$alias][$Model->primaryKey], $invoiceId);
				}
			}
		}
		$Model->_syncingWithInvoice = null;
		return $success;
	}
	
	public function afterCopyInvoiceToModel($Model, $id, $invoiceId) {
		return true;
	}
	
/**
 * Finds model name and detects presence of plugin
 * 
 * @param Model $model
 * @return string Model alias with plugin if found
 **/
	private function getModelName(Model $Model) {
		$alias = $Model->alias;
		if (!empty($Model->plugin)) {
			$alias = "{$Model->plugin}.$alias";
		}
		return $alias;
	}
}