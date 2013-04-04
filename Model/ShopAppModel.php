<?php
class ShopAppModel extends AppModel {
	var $actsAs = array(
		'Containable',
		'Shop.Linkable',
		'Shop.PostContain',
	);

	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$aliasFields = array('order', 'virtualFields');
		foreach ($aliasFields as $field) {
			if (isset($this->{$field})) {
				if (!is_array($this->{$field})) {
					$this->{$field} = array($this->{$field});
				}
				foreach ($this->{$field} as $key => $val) {
					unset($this->{$field}[$key]);
					$key = str_replace('$ALIAS', $this->alias, $key);
					$val = str_replace('$ALIAS', $this->alias, $val);
					$this->{$field}[$key] = $val;
				}
			}
		}
	}
	
	function &getData() {
		$data = null;
		if (isset($this->request->data)) {
			if (isset($this->request->data[$this->alias])) {
				$data =& $this->request->data[$this->alias];
			} else {
				$data =& $this->request->data;
			}
		}
		return $data;
	}
}