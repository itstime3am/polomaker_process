<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Ctrl_crud extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->modelName = '';
		$this->load->helper('crud_controller_helper');
		$this->_selOptions = array();
		$this->_arrDataViewFields = array();
		$this->_arrControlLayout = array();
	}

	public function add_view($path, $params = array(), $return = FALSE, $with_script_header = FALSE) {
		if ($path == '_public/_list') {
			if (! $this->_blnCheckRight('view')) $params['list_viewable'] = FALSE;
			if (! $this->_blnCheckRight('insert')) $params['list_insertable'] = FALSE;
			if (! $this->_blnCheckRight('edit')) $params['list_editable'] = FALSE;
			if (! $this->_blnCheckRight('delete')) {
				$params['list_deleteable'] = FALSE;
				$params['list_cancelable'] = FALSE;
			}
		}
		return parent::add_view($path, $params, $return, $with_script_header);
	}

	public function json_search() {
		//$this->_serviceCheckRight('view');
		$blnSuccess = FALSE;
		$strError = 'Unknown Error';
		$this->load->model($this->modelName, 'm');
		$json_input_data = json_decode(trim(file_get_contents('php://input')), true); //get json
		$_arrData = (isset($json_input_data))?$json_input_data:$this->input->post(); //or post data submit
		if (isset($_arrData) && ($_arrData != FALSE)) {
			$arrResult = $this->m->search($_arrData);
		} else {
			$arrResult = $this->m->search();
		}
		$strError = $this->m->error_message;
		if ($strError == '') {
			$blnSuccess = TRUE;
			if (!is_array($arrResult)) {
				$arrResult = array();
			}
		}
		$json = json_encode(
			array(
				'success' => $blnSuccess,
				'error' => $strError,
				'data' => $arrResult
			)
		);
		header('content-type: application/json; charset=utf-8');
		echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$json.")":$json;
	}
	
	public function commit() {
		$this->_serviceCheckRight(($this->_blnCheckRight('insert') || $this->_blnCheckRight('edit')));
		
		$_blnSuccess = FALSE;
		$_strError = '';
		$_strMessage = '';
		$json_input_data = json_decode(trim(file_get_contents('php://input')), true); //get json
		$_arrData = (isset($json_input_data))?$json_input_data:$this->input->post(); //or post data submit
		if (isset($_arrData) && ($_arrData != FALSE)) {
			$this->load->model($this->modelName, 'm');			
			$_aff_rows = $this->m->commit($_arrData);
			$_strError = $this->m->error_message;

			if ($_strError == '') {
				$_blnSuccess = TRUE;
				$_strMessage = $_aff_rows;
			} else {
				$_blnSuccess = FALSE;
			}
		}
		
		$json = json_encode(
			array(
				'success' => $_blnSuccess,
				'error' => $_strError,
				'message' => $_strMessage
			)
		);
		header('content-type: application/json; charset=utf-8');
		echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$json.")": $json;
	}
	
	public function delete() {
		$this->_serviceCheckRight('delete');
		
		$_blnSuccess = FALSE;
		$_strError = '';
		$_strMessage = '';
		if ($this->input->post()) { // check if form has been submitted
			$this->load->model($this->modelName, 'm');
			
			$_rowid = $this->input->post('rowid');
			$_aff_rows = $this->m->delete($_rowid);
			$_strError = $this->m->error_message;

			if ($_strError == '') {
				$_blnSuccess = TRUE;
				$_strMessage = $_aff_rows;
			} else {
				$_blnSuccess = FALSE;
			}
		}
		
		$json = json_encode(
			array(
				'success' => $_blnSuccess,
				'error' => $_strError,
				'message' => $_strMessage
			)
		);
		header('content-type: application/json; charset=utf-8');
		echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$json.")": $json;
	}

	public function cancel() {
		$this->_serviceCheckRight(($this->_blnCheckRight('edit') || $this->_blnCheckRight('delete')));

		$_blnSuccess = FALSE;
		$_strError = '';
		$_strMessage = '';
		$json_input_data = json_decode(trim(file_get_contents('php://input')), true); //get json
		$_arrData = (isset($json_input_data))?$json_input_data:$this->input->post(); //or post data submit
		$_rowid = (isset($_arrData['rowid'])) ? $_arrData['rowid'] : -1;
		if ($_rowid > 0) {
			$this->load->model($this->modelName, 'm');			
			$_aff_rows = $this->m->cancel($_rowid);
			$_strError = $this->m->error_message;

			if ($_strError == '') {
				$_blnSuccess = TRUE;
				$_strMessage = $_aff_rows;
			} else {
				$_blnSuccess = FALSE;
			}
		} else {
			$_strError = 'Invalid rowid ( '. $_rowid .' )';
		}
		
		$json = json_encode(
			array(
				'success' => $_blnSuccess,
				'error' => $_strError,
				'message' => $_strMessage
			)
		);
		header('content-type: application/json; charset=utf-8');
		echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$json.")": $json;
	}

	function _prepareSelectOptions($_arrSelList) {	
		return hlpr_prepareMasterTableSelectOptions($_arrSelList);
	}

	function _prepareControlsDefault() {
		$this->_arrDataViewFields = hlpr_prepareControlsDefault($this->modelName, $this->_selOptions);
		return $this->_arrDataViewFields;
	}
	
	function _setController($strDataColumn, $strLabel, $arrEditAttributes = array(), $arrGridAttributes = array()) {
		hlpr_setController($this->_arrDataViewFields, $strDataColumn, $strLabel, $arrEditAttributes, $arrGridAttributes);
	}
	
	function _getDisplayLabel($strDataColumn) {
		return hlpr_getDisplayLabel($this->_arrDataViewFields, $strDataColumn);
	}
	
	function _arrGetEditControls($arr = array()) {
		return hlpr_arrGetEditControls($this->_arrDataViewFields, $arr);
	}
}