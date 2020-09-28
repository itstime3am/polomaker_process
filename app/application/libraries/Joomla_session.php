<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once ( JPATH_BASE.DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE.DS.'includes'.DS.'framework.php' );

class Joomla_session {
	function __construct() {
		JFactory::getApplication('site')->initialise();
		$this->_session = JFactory::getSession();
        log_message('Debug', '"Joomla Session" class is loaded.');
    }
	function _set_userdata($key, $value) {
		if (is_string($key) && isset($value)) {
			$this->_session->set($key, $value, 'userdata');
		}
	}
	function _set_userdata__array($arrData) {
		if (is_array($arrData)) {
			foreach ($arrData AS $_key => $_val) {
				$this->_set_userdata($_key, $_val);
			}
		}
	}
	function set_userdata($data = NULL, $value = NULL) {
		if (! $data) {
			$this->clear_userdata();
		} else if (is_array($data)) {
			$this->_set_userdata__array($data);
		} else if ((is_string($data)) && isset($value)) {
			$this->_set_userdata($data, $value);
		}
	}

	function clear_userdata() {
		$this->_session->destroy();
		$this->_session->start();
	}

	function _remove_userdata__array($arrKeys) {
		if (is_array($arrKeys)) {
			for ($_i=0;$_i<count($arrKeys);$_i++) {
				$this->_remove_userdata($arrKeys[$_i]);
			}
		}
	}

	function _remove_userdata($key) {
		if (is_string($key) && (array_key_exists($key, $this->_arrUserData))) {
			unset($this->_arrUserData[$key]);
			$this->_session->clear($key, 'userdata');
		}
	}

	function remove_userdata($keys = FALSE) {
		if ($keys) {
			if (is_array($keys)) {
				$this->_remove_userdata__array($keys);
			} else if (is_string($keys)) {
				$this->_remove_userdata($keys);
			}
		}
	}

	function userdata($key, $default_value = NULL) {
		if ($this->_session->has($key, 'userdata')) {
			return $this->_session->get($key, $default_value, 'userdata');
		} else {
			return $default_value;
		}
	}
}