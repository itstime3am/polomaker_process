<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Access_control {
	var $_MSG_ACCESS_NOT_ALLOWED = 'v_XX_1 ไม่มีสิทธิ์ใช้งานหน้านี้';
	var $_MSG_FUNCTION_NOT_ALLOWED = 'v_XX_1 ไม่มีสิทธิ์ใช้งานฟังก์ชันนี้';
	function __construct() {
		$this->_user_id = -1;
		$this->_user_name = '';
		$this->_user_display = '';
		$this->_email = '';
		$this->_ACR = array();
        log_message('Debug', '"Access Control" class is loaded.');
    }
	
	function _arrListACR($userid) {
		$_arrReturn = array();
		if ( ! empty($userid) ) {
			$_db = JFactory::getDbo();
			$_viewlevels = implode(",", array_unique(JAccess::getAuthorisedViewLevels($userid)));
			if (strlen(trim($_viewlevels)) == 0) $_viewlevels = '0';
//echo $_viewlevels;exit;
			$_sql = $_db->getQuery(true);
			$_sql
				->select($_db->quoteName('v.title'))
				->from($_db->quoteName('#__viewlevels', 'v'))
				/*
				->join('INNER', $_db->quoteName('#__user_usergroup_map', 'ugm') . " ON INSTR(CONCAT(',', SUBSTR(v.rules, 2, LENGTH(v.rules) - 2), ','), CONCAT(',', ugm.group_id, ',')) > 0 ")
				->where('ugm.user_id = ' . $_db->quote($userid))
				//->where('(v.title LIKE \'\\_\\_%\' OR LOWER(v.title) = ' . $_db->quote('-' . $acname) . ' OR LOWER(v.title) LIKE ' . $_db->quote('-' . $acname . '%', false) . ')')
				*/
				->where("v.id IN ($_viewlevels) AND v.title LIKE '-%'")
				->order('ordering ASC')
			;
			$_db->setQuery($_sql);
			$_result = $_db->loadObjectList();
			if ($_result) foreach($_result as $_row) $_arrReturn[] = $_row->title;
		}
		return $_arrReturn;
	}
	
	function _loadPageAccessRight($userid = -1) {
		$_CI =& get_instance();
		if (count($this->_ACR) == 0) {
			$_user_id = ($userid > 0) ? (int) $userid : $this->_user_id;
			$_arr = $this->_arrListACR($_user_id);
			if ($_arr) {
				/* format is "-[name]__[access_right]" */
				foreach($_arr as $acr) {
					$_acr = strtolower($acr);
					$_parts = explode('__', $_acr);
					if (is_array($_parts) && (count($_parts) > 0)) {
						$_group = substr($_parts[0], 1);
						$_medthod = '_ALLOW';
						if (count($_parts) > 1) {
							$_medthod = $_parts[1];
						}
						if ( ! array_key_exists($_group, $this->_ACR)) $this->_ACR[$_group] = array();
						$this->_ACR[$_group][] = $_medthod;
					}
				}
			}
		}
	}

	function blnCheckRight($name = '', $group = NULL) {
		$_group = ( ! empty($group) ) ? strtolower($group) : '';
		if (array_key_exists($_group, $this->_ACR)) {
//print_r($this->_ACR[$_group]);exit;
			if (in_array('_ALLOW', $this->_ACR[$_group])) {
				if (empty($name) || in_array(strtolower($name), $this->_ACR[$_group])) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}
}