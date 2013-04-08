<?php
/**
 * Automatcially syncs fields between model and the Invoice model
 *
 **/
class InvoiceSyncBehavior extends ModelBehavior {
	var $settings = array();
	
	function setup(Model $Model, $syncFields = array()) {
		if (empty($Model->belongsTo['Invoice'])) {
			throw new Exception("Cannot use InvoiceSync Behavior without having {$Model->alias} belongTo Invoice");
		}
		if (!isset($this->settings[$Model->alias]['fields'])) {
			$this->settings[$Model->alias]['fields'] = array();
		}
		$this->settings[$Model->alias]['fields'] = array_merge(
			$this->settings[$Model->alias]['fields'],
			(array) $syncFields
		);
		
		$model = $Model->alias;
		if (!empty($Model->plugin)) {
			$model = "{$Model->plugin}.$model";
		}
		$Model->Invoice->bindModel(array('hasOne' => array($model)));
	}
	
	function afterSave(Model $Model, $created) {
		$this->copyModelToInvoice($Model, $Model->id);
		return parent::afterSave($Model, $created);
	}
	
	/**
	 * Finds all fields stored in settings and saves them to Invoice
	 *
	 * @param AppModel $Model Model from where to find the source
	 * @param int $id Model ID
	 * @return bool Save is successful
	 **/
	function copyModelToInvoice($Model, $id) {
		$result = $Model->find('first', array(
			'contain' => array('Invoice'),
			'conditions' => array($Model->alias . '.id' => $id)
		));
		$data = array($Model->alias => compact('id'), 'Invoice' => array(
			'item_name' => $Model->alias,
			'item_number' => $id,
		));
		if (!empty($result['Invoice']['id'])) {
			$data['Invoice']['id'] = $result['Invoice']['id'];
		}				
		if (!empty($this->settings[$Model->alias]['fields'])) {
			foreach ($this->settings[$Model->alias]['fields'] as $modelField => $invoiceField) {
				if (is_numeric($modelField)) {
					$modelField = $invoiceField;
				}
				$data['Invoice'][$invoiceField] = $result[$Model->alias][$modelField];
			}
		}
		return $Model->Invoice->saveAll($data, array('callbacks' => false));
	}
	
	/**
	 * Finds all fields stored in settings and saves them to Model from Invoice
	 *
	 * @param AppModel $Model Model where Invoice fields will be saved
	 * @param int $id Invoice id
	 * @return bool/null Save is successful, null if no fields are present
	 **/
	function copyInvoiceToModel($Model, $invoiceId) {
		if (!empty($this->settings[$Model->alias]['fields'])) {
			$result = $Model->Invoice->find('first', array(
				'contain' => array($Model->alias),
				'conditions' => array('Invoice.id' => $invoiceId)
			));
			if (empty($result[$Model->alias])) {
				return null;
			}
			$data = array($Model->alias => array(), 'Invoice' => array('id' => $invoiceId));
			foreach ($this->settings[$Model->alias]['fields'] as $modelField => $invoiceField) {
				if (is_numeric($modelField)) {
					$modelField = $invoiceField;
				}
				$result[$Model->alias][$modelField] = $data['Invoice'][$invoiceField];
			}
			return $Model->saveAll($data, array('callbacks' => false));
		}
		return null;
	}
}