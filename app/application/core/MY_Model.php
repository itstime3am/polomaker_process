<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Form Validation Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Extended Model
 * @author		Inferno
 * @link		
 */
 
class MY_Model extends CI_Model {
	var $_TABLE_NAME = '';
	var $_AUTO_FIELDS = array();
	var $_FIELDS = array();
	var $_DATE_FIELDS = array();
	var $_DATETIME_FIELDS = array();
	var $_JSON_FIELDS = array();
	var $_SPEC_FIELDS = array('json' => array(), 'int_array' => array());
	var $last_insert_id = 0;
	var $error_message = '';
	var $error_number = 0;

    function __construct()
    {
        parent::__construct();
    }
	
	function list_all() {
		$_result = $this->db->from($this->_TABLE_NAME)->get()->result_array();
		$this->error_message = $this->db->error()['message'];
		$this->error_number = $this->db->error()['code'];

		if (is_array($_result)) {
			$_items = array();
			$_arrObj = new ArrayObject($this->_FIELDS);
			foreach ($_result as $_obj) {
				$_row = $_arrObj->getArrayCopy();
				$_row = array_merge($_row, $_obj);
				$_items[] = $_row;
			}
			return $_items;
		} else {
			return FALSE;
		}
	}

    function get_by_id($RowID)
    {
		$_result = $this->db->get_where($this->_TABLE_NAME, array('rowid' => $RowID))->row();
		$this->error_message = $this->db->error()['message'];
		$this->error_number = $this->db->error()['code'];
		if ($_result == FALSE) { 
			return FALSE;
		} else {
			$_item = array();
			foreach ($_result as $_key => $_value) {
				$_item[$_key] = $_value;
			}
		}
		return $_item;
	}

	function __getSpecialFieldSet($key = NULL) {
		if (empty($key)) return FALSE;
		$_strGetType = '';
		
		if (in_array($key, $this->_JSON_FIELDS)) {
			$_strGetType = 'json';
		} else {
			if (is_array($this->_SPEC_FIELDS)) {
				foreach ($this->_SPEC_FIELDS as $_type => $_arr) {
					if (is_array($_arr) && (in_array($key, $_arr)) && (is_string($_type))) {
						$_strGetType = $_type;
						break;
					}
				}
			}
		}
		if (! empty($_strGetType)) {
			return $_strGetType;
		} else {
			return FALSE;
		}
	}

	function __blnPrepareDBSet($_arrParams = NULL) {
		$_isCompareTmplNeeded = FALSE;
		$blnHasData = FALSE;
		if ((isset($_arrParams) && is_array($_arrParams))) {
			$_isCompareTmplNeeded = TRUE;
		} else {
			$_arrParams = $this->_FIELDS;
			$_isCompareTmplNeeded = FALSE;
		}
		foreach ($_arrParams as $key=>$value) {
			if (($_isCompareTmplNeeded == FALSE) || (array_key_exists($key, $this->_FIELDS))) {
				if (! is_null($value)) {
					if (is_string($value)) $value = trim($value);
					if (empty($value) && ($value != '0')) {
						$this->db->set($key, NULL);
					} else {
						$blnHasData = TRUE;
						if (in_array($key, $this->_DATE_FIELDS)) {
							if ($value instanceof DateTime) {
								$dat_value = $value;
							} else {
								$dat_value = $this->_datFromPost($value);
							}
							if ($dat_value instanceof DateTime) $this->db->set($key, "TO_DATE('" . $dat_value->format('Ymd')."', 'YYYYMMDD')::DATE", FALSE);
						} elseif (in_array($key, $this->_DATETIME_FIELDS)) {
							if ($value instanceof DateTime) {
								$dat_value = $value;
							} else {
								$dat_value = $this->_datFromPost($value);
							}
							if ($dat_value instanceof DateTime) $this->db->set($key, "TO_TIMESTAMP('". $dat_value->format('Ymd H:i:s')."', 'YYYYMMDD HH24:MI:SS')::TIMESTAMP WITHOUT TIME ZONE", FALSE);
						} elseif ($value instanceof DateTime) {
							$this->db->set($key, "TO_TIMESTAMP('" . $value->format('Ymd H:i:s') . "', 'YYYYMMDD HH24:MI:SS')::TIMESTAMP WITHOUT TIME ZONE", FALSE);
						} elseif (in_array($key, $this->_JSON_FIELDS)) {
							if (is_array($value)) {
								$value = json_encode($value);
							} else if (! is_string($value)) {
								$value = trim((string) $value);
							}
							if (strlen(trim($value)) > 0) $this->db->set($key, "'" . $value . "'::JSON", FALSE);
						} else {
							$_specType = $this->__getSpecialFieldSet($key);
							switch(strtolower($_specType)) {
								case 'json':
									if (is_array($value)) {
										$value = json_encode($value);
									} else if (! is_string($value)) {
										$value = trim((string) $value);
									}
									if (strlen(trim($value)) > 0) $this->db->set($key, "'" . $value . "'::JSON", FALSE);
									break;
								case 'int_array':
									if (is_array($value)) {
										$value = trim(implode(',', $value));
									} else if (! is_string($value)) {
										$value = trim((string) $value);
									}
									if (strlen(trim($value)) > 0) $this->db->set($key, 'ARRAY[' . $value . ']::INT[]', FALSE);
									break;
								case 'numeric_array':
									if (is_array($value)) {
										$value = trim(implode(',', $value));
									} else if (! is_string($value)) {
										$value = trim((string) $value);
									}
									if (strlen(trim($value)) > 0) $this->db->set($key, 'ARRAY[' . $value . ']::NUMERIC[]', FALSE);
									break;
								case 'multi_dimension_numeric_array':
									if (is_string($value)) {
										$value = trim((string) $value);
									} else {
										$value = json_encode($value);
									}
									if (strlen(trim($value)) > 0) $this->db->set($key, 'ARRAY[' . $value . ']::NUMERIC[]', FALSE);
									break;
								default:
									if (strlen(trim($value)) > 0) $this->db->set($key, $value);
									break;
							}
						}
					}
				}
			}
		}
		return $blnHasData;
	}
	
	function commit($arrObj, $blnCommitNullValue = TRUE) {
		foreach ($arrObj as $key=>$value) {
			if (array_key_exists($key, $this->_FIELDS)) {
				$this->_FIELDS[$key] = $value;				
			}
		}
		$_blnIsInsert = TRUE;
		if (array_key_exists('rowid', $arrObj) && (trim($arrObj['rowid']) > 0)) $_blnIsInsert = FALSE;
		
		if ($_blnIsInsert) {
			foreach ($this->_AUTO_FIELDS as $key=>$value) {
				$this->_AUTO_FIELDS[$key] = null;
			}
			return $this->insert();
		} else {
			return $this->update(array('rowid' => $arrObj['rowid']), $blnCommitNullValue);
		}
	}
	
	function insert() {
		$CI = & get_instance();
		$_create_by = FALSE;
		$_create_date = FALSE;
		if (array_key_exists('create_by', $this->_FIELDS)) {
			if (empty($this->_FIELDS['create_by']) || ($this->_FIELDS['create_by'] < 1)) {
				$this->db->set('create_by', $CI->session->userdata('user_id'));
			} else {
				$this->db->set('create_by', $this->_FIELDS['create_by']);
			}
			unset($this->_FIELDS['create_by']);
		}
		if (array_key_exists('create_date', $this->_FIELDS)) {
			if (empty($this->_FIELDS['create_date'])) {
				$this->db->set('create_date', 'CURRENT_TIMESTAMP', FALSE);
			} else {
				$this->db->set('create_date', $this->_FIELDS['create_date']);
			}
			unset($this->_FIELDS['create_date']);
		}
		$blnHasData = $this->__blnPrepareDBSet($this->_FIELDS);
		if ($blnHasData) {
			try {
				$this->db->insert($this->_TABLE_NAME);
				$this->error_message = $this->db->error()['message'];
				$this->error_number = $this->db->error()['code'];
				$this->last_insert_id = $this->db->insert_id();
				return $this->last_insert_id;
			} catch (Exception $e) {
				$this->error_message = $e->getMessage();
				$this->error_number = $e->getCode();
				$this->last_insert_id = -1;
				return FALSE;
			}
		} else {
			$this->error_message = 'No value';
			$this->error_number = 701;
			return FALSE;
		}
	}
	
	function update($arrWhere, $blnUpdateNullValue = FALSE) {
		$CI = & get_instance();
		$blnHasData = $this->__blnPrepareDBSet($this->_FIELDS);
		if ($blnHasData) {
			if (array_key_exists('update_by', $this->_FIELDS)) {
				if (empty($this->_FIELDS['update_by']) || ($this->_FIELDS['update_by'] < 1)) {
					$this->db->set('update_by', $CI->session->userdata('user_id'));
				} else {
					$this->db->set('update_by', $this->_FIELDS['update_by']);
				}
			}
			if (array_key_exists('update_date', $this->_FIELDS)) {
				if (empty($this->_FIELDS['update_date'])) {
					$this->db->set('update_date', 'CURRENT_TIMESTAMP', FALSE);
				} else {
					$this->db->set('update_date', $this->_FIELDS['update_date']);
				}
			}
			try {
				$this->last_insert_id = -1;

				$this->db->where($arrWhere);
				$this->db->update($this->_TABLE_NAME);

				$this->error_message = $this->db->error()['message'];
				$this->error_number = $this->db->error()['code'];

				return $this->db->affected_rows();
			} catch (Exception $e) {
				$this->error_message = $e->getMessage();
				$this->error_number = $e->getCode();
				return FALSE;
			}				
		} else {
			$this->error_message = 'No values to update';
			$this->error_number = 801;

			return FALSE;
		}
	}

	function delete($RowID)
	{
		$this->db->delete($this->_TABLE_NAME, array('rowid' => $RowID));
		$this->error_message = $this->db->error()['message'];
		$this->error_number = $this->db->error()['code'];
		return $this->db->affected_rows();
	}
	
	function cancel($RowID) {
		$this->db->set('is_cancel', 1);
		$this->db->set('update_by', $this->_AC->_user_id);
		$this->db->set('update_date', 'now()', FALSE);
		$this->db->where('rowid', $RowID);
		$this->db->update($this->_TABLE_NAME);
		$this->error_message = $this->db->error()['message'];
		$this->error_number = $this->db->error()['code'];
		return $this->db->affected_rows();
	}

    //escapes and adds single quotes
    //to each value of an array
    function safe_escape(&$data)
    {
        if (is_array($data))
		{
			foreach($data as $node)
			{
				$node = $this->db->escape($node);
			}
		} 
		return $data;
	}

	function SP_execute($sp, $data = '')
	{
		//$CI = get_instance();
		//$CI->firephp->log($data);
		$_return;
		if ($data !== '')
		{
			$params = '';
			if (is_array($data))
			{
				for ($i=0;$i<count($data);$i++)
				{
					$params .= '?, ';
				}
				$params = substr($params, 0, -2);
			} 
			else
			{
				$params = '?';
			}
			//$CI->firephp->log($params);
			$_return = $this->db->query('select * from '.$sp.' ('.$params.')', $this->safe_escape($data));
		}
		else
		{
			$_return = $this->db->query('select * from '.$sp.'()');		
		}
		$this->error_message = $this->db->error()['message'];
		$this->error_number = $this->db->error()['code'];
		return $_return;
	}
	
	function arr_execute($sql, $params = NULL)
	{
		$result;
		if (isset($params) && is_array($params)) {
			$result = $this->db->query($sql, $params)->result();			
		} else {
			$result = $this->db->query($sql)->result();
		}
		$return = FALSE;
		if ($result) {
			foreach($result as $row) {
				$rowTemp = array();
				foreach ($row as $key => $val) {
					$rowTemp[$key] = $val;
				}
				$return[] = $rowTemp;
			}
		}
		$this->error_message = $this->db->error()['message'];
		$this->error_number = $this->db->error()['code'];
		return $return;
	}
	
	function arr_SP_execute($sp, $data = '')
	{
		$result = $this->SP_execute($sp, $data)->result();
		$return = false;
		if ($result) {
			foreach($result as $row) {
				$rowTemp = array();
				foreach ($row as $key => $val) {
					$rowTemp[$key] = $val;
				}
				$return[] = $rowTemp;
			}
		}
		$this->error_message = $this->db->error()['message'];
		$this->error_number = $this->db->error()['code'];
		return $return;
	}

	function _strConvertDisplayDateFormat($dtDate = null) 
	{
		if ($dtDate == null) {
			//$dtDate = new DateTime();
			return '';
		} else {
			return $dtDate->format('Y/m/d');
		}
	}
	
	function _explode_trim($delimiter, $str) { 
		if ( is_string($delimiter) ) { 
			$str = trim(preg_replace('|\\s*(?:' . preg_quote($delimiter) . ')\\s*|', $delimiter, $str)); 
			return explode($delimiter, $str); 
		} 
		return $str; 
	}

	function _datFromPost($strDateText, $date_delimeter = "/", $datetime_seperater = ' ', $time_delimeter = ':') { //default ui format = dd/mm/yyyy
		$_strDate = '';
		$_strTime = '';
		if (strpos($strDateText, $datetime_seperater) > 0) {
			$_arr = $this->_explode_trim($datetime_seperater, $strDateText);
			if (count($_arr) >= 2) {
				$_strDate = $_arr[0];
				$_strTime = $_arr[1];
			}
		} else {
			$_strDate = $strDateText;
		}
		$datDummy = new DateTime();
		if (strpos($_strDate, $date_delimeter) > 0) {
			$arr = $this->_explode_trim($date_delimeter, $_strDate);
			if (count($arr) == 2) { //only MM/YYYY
				$arr[2] = $arr[1];
				$arr[1] = $arr[0];
				$arr[0] = 1;
			}
			if ($arr[2] >= 2500) {
				$arr[2] -= 543;
			}
			if (checkdate($arr[1], $arr[0], $arr[2])) {
				$datDummy->setDate($arr[2], $arr[1], $arr[0]);
			}
			if (strpos($_strTime, $time_delimeter) > 0) {
				$_arr = $this->_explode_trim($time_delimeter, $_strTime);
				if (count($_arr) == 2) {
					$datDummy->setTime($_arr[0], $_arr[1]);
				} elseif (count($_arr) > 2) {
					$datDummy->setTime($_arr[0], $_arr[1], $_arr[2]);
				}
			}
			return $datDummy;
		}
		return '';
	}
	
	function _getSearchConditionSQL($arrParams, $arrSpec = array()) {
		$retSql = ' ';
		$_arrEaSpec = FALSE;
		foreach ($arrParams as $_name=>$_value) {
			$_arrEaSpec = FALSE;
			if (array_key_exists($_name, $arrSpec) && is_array($arrSpec[$_name])) $_arrEaSpec = $arrSpec[$_name];
			$_dbcol = ($_arrEaSpec && (array_key_exists('dbcol', $_arrEaSpec)))?$_arrEaSpec['dbcol']:'t.'.$_name;
			$_val = ($_arrEaSpec && (array_key_exists('val', $_arrEaSpec)))?$_arrEaSpec['val']:$_value;
			$_oper = ($_arrEaSpec && (array_key_exists('operand', $_arrEaSpec)))?$_arrEaSpec['operand']:'=';
			$_type = 'txt';
			if ($_arrEaSpec && (array_key_exists('type', $_arrEaSpec))) {
				$_type = $_arrEaSpec['type'];
			} else {
				if (substr($_name, -6) == '_rowid') {
					$_type = 'int';
				} elseif (substr($_name, -5) == '_date') {
					$_type = 'dat';
				}
			}
			switch (strtolower($_type)) {
				case 'txt':
					if ($_oper == '=') {
						$retSql .= sprintf("AND %s LIKE CONCAT('%%', '%s', '%%') ", $_dbcol, $this->db->escape_like_str($_val));
					} else {
						$retSql .= sprintf("AND %s %s '%s' ", $_dbcol, $_oper, $this->db->escape_str($_val));
					}
					break;
				case 'int':
				case 'dbl':
					if (is_numeric($_val)) $retSql .= sprintf("AND %s %s %d ", $_dbcol, $_oper, $_val);
					break;
				case 'dat':
					$_dat = $this->_datFromPost($_val);
					if (($_dat !== '') && ($_dat instanceof Datetime)) $retSql .= sprintf("AND %s %s TO_DATE('%s', 'YYYYMMDD') ", $_dbcol, $_oper, $_dat->format('Ymd'));
					break;
				case 'raw':
					$retSql .= sprintf("AND %s %s %s ", $_dbcol, $_oper, $_val);
					break;
			}
		}
//echo $retSql . "\n<br>";
		return $retSql;
	}
	
	function _getCheckAccessRight($userColumn = '', $groupname = '') {
		$CI = & get_instance();
		$_user_col = (!empty($userColumn))?$userColumn:'t.create_by';
		$_user_id = $CI->_AC->_user_id;
		$_user_id = $this->db->escape((int) $_user_id);
		$_retSql = ' ';
		if ($CI->_blnCheckRight('list_all', $groupname) == FALSE) {
			if ($CI->_blnCheckRight('list_branch', $groupname) == TRUE) {
				$_retSql .= <<<BRA

AND $_user_col IN (
	SELECT DISTINCT dest_user
	FROM pm_v_user_group_by_branch
	WHERE source_user = $_user_id
)

BRA;
			} else {
				$_retSql .= "AND $_user_col = $_user_id ";			
			}
		}
		return $_retSql;
	}
	
	function _getCheckAccessRight__jug_id($userColumn = '', $column_jug_id = '', $groupname = '') {
		include( APPPATH.'config/database.php' );
		$CI = & get_instance();
		$_user_col = (!empty($userColumn))?$userColumn:'create_by';
		$_user_id = $this->db->escape((int) $CI->_AC->_user_id);
		$_column_jug_id = (!empty($column_jug_id)) ? $column_jug_id : -1;
		
		$_user_id = $this->db->escape((int) $CI->_AC->_user_id );
		$_retSql = ' ';
		if ($CI->_blnCheckRight('list_all', $groupname) == FALSE) {
			if ($CI->_blnCheckRight('list_branch', $groupname) == TRUE) {
				$_retSql .= <<<BRA

AND {$_column_jug_id} IN (
	SELECT DISTINCT group_id
	FROM {$db['joomla']['database']}.{$db['joomla']['dbprefix']}user_usergroup_map ugm
	WHERE ugm.user_id = $_user_id
)

BRA;
			} else {
				$_retSql .= "AND $_user_col = $_user_id ";			
			}
		}
		return $_retSql;
	}
}