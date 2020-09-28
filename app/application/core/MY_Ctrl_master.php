<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Ctrl_master extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->modelName = '';
		$this->_model_fncSearch = 'search';
		$this->_model_fncSearch__arrAppendArgs = array();
		$this->_model_fncCommit = 'commit';
		$this->_model_fncCancel = 'cancel';
		$this->_model_fncDelete = 'delete';
		$this->_defaultBaseIndex = 0; //default base index used in jQuery select to identify correct elements and objects 
		$this->_pageOptions = array("type"=>"left_main");
		$this->_selOptions = array();
		$this->_arrDataViewFields = array();

		$this->_editPanelWidth = 900; //default edit panel width
		$this->_editPanelHeight = 600; //default edit panel height
		
		$this->_searchDlgMoreOptions = array();
		$this->_listDlgMoreOptions = array();
		$this->_editDlgMoreOptions = array();

		$this->load->helper('crud_controller_helper');
	}
	function __prepareEditForm() { //for main panel
		$this->_prepareControlsDefault();
		//$this->_setController("name", "name", NULL, array("selectable"=>TRUE,"default"=>TRUE,"order"=>0));
	}
	function __getEditForm($intFormIndex = -1) { // for main panel
		$_intFrmIndx = ($intFormIndex >= 0)?$intFormIndex:($this->_defaultBaseIndex + 2);
		$this->__prepareEditForm();
		$_arrParams = array_merge(
			array(
				'index' => $_intFrmIndx
				,'crud_controller' => $this->page_name
				,'controls' => $this->_arrGetEditControls()
			)
			, $this->_editDlgMoreOptions
		);
		$_html = $this->add_view('_public/_form', $_arrParams, TRUE);
		return $_html;
	}
	function __arrSearchControls() { // default for left/top search panel
		$_strMdlName = $this->modelName;
		$this->load->model($_strMdlName);
		$_arrControls = array();
		$_intCount = 0;
		foreach ($this->$_strMdlName->_FIELDS AS $_key=>$_val) {
			if (((strpos($_key, 'is_') === FALSE) && (strpos($_key, '_rowid') === FALSE) && (strpos($_key, '_date') === FALSE) 
				&& (strpos($_key, '_datetime') === FALSE) && (strpos($_key, '_timestamp') === FALSE)) 
				&& ($_intCount < 3)) {
				array_push($_arrControls, array(
					"name" => $_key, "type" => "txt", "label" => $this->_getDisplayLabel($_key), "placeHolder"=> $this->_getDisplayLabel($_key)
				));
				$_intCount += 1;
			}
		}
		return $_arrControls;
	}
	function _getMainPanel() { //override
		$_editForm = $this->__getEditForm(($this->_defaultBaseIndex + 2));
		$_arrParams = array_merge(
			array(
				'index' => ($this->_defaultBaseIndex + 1)
				,'dataview_fields' => $this->_arrDataViewFields
				,'edit_template' => $_editForm
			)
			, $this->_listDlgMoreOptions
		);
		
		if (isset($this->_pageOptions['display_list_title']) && ($this->_pageOptions['display_list_title'] == True) && (strlen($this->_pageTitle) > 0)) {
			$_arrParams['div_title'] = $this->_pageTitle;
		}
		$_html = $this->add_view(
				'_public/_list'
				, $_arrParams
				, TRUE
			);
		return $_html;
	}
	function _getLeftPanel() { //override
		$_arrControls = $this->__arrSearchControls();
		$_arrParams = array_merge(
			array(
				"controls" => $_arrControls
			)
			, $this->_searchDlgMoreOptions
		);
		if (is_array($_arrControls) && (count($_arrControls) > 0)) {
			return $this->add_view(
				"_public/_search_panel"
				, $_arrParams
				, TRUE
			);
		} else {
			return '';
		}
	}
	function _getTopPanel() { //overwritable
		$_arrSrcControls = $this->__arrSearchControls();
		$_arrControls = array();
		if (is_array($_arrSrcControls) && (count($_arrSrcControls) > 0)) {
			$_arrLayoutHeadRow = array();
			$_arrLayoutCtrlRow = array();
			$_ctrlHiddens = array();
			foreach ($_arrSrcControls as $_ctrl) {
				$_eaH = FALSE;
				if (isset($_ctrl['name']) && (! empty($_ctrl['name']))) {
					$_new = array_merge(array(), $_ctrl);
					if (isset($_ctrl['label']) && (! empty($_ctrl['label']))) {
						if (
							(! (array_key_exists('placeHolder', $_ctrl) || array_key_exists('place_holder', $_ctrl))) 
							|| (empty($_ctrl['placeHolder']) && empty($_ctrl['place_holder']))
						) {
							$_new['placeHolder'] = $_ctrl['label'];
						}
						$_eaH = 'return <div class="top_search_title">' . (isset($_ctrl['label']) ? $_ctrl['label'] : '') . '</div>';
					} else if (($_ctrl['type'] != 'hdn')) {
						$_eaH = 'return <div class="top_search_title"></div>';
					}
					$_new['label'] = '';
					array_push($_arrControls, $_new);

					if ($_eaH !== FALSE) {
						array_push($_arrLayoutHeadRow, $_eaH);
						array_push($_arrLayoutCtrlRow, $_ctrl['name']);
					}
				}
			}
		}
		if (count($_arrControls) > 0) {
			return $this->add_view(
				'_public/_search_panel_top'
				,array(
					'controls' => $_arrControls
					, 'layout' => array($_arrLayoutHeadRow, $_arrLayoutCtrlRow)
					, 'end_script' => <<<ENDSCR
		var _chldCount = $('#tblSearchPanel tr:first-child .cls-row .cls-row-value').length || 1;
		if (_chldCount > 1) $('#tblSearchPanel .cls-row .cls-row-value').css('width', Math.floor(100 / _chldCount) + '%');
ENDSCR
				), TRUE
			);			
		} else {
			return '';
		}
	}
	public function index() {
		$this->_add_css(
			array(
				'public/css/jquery/ui/1.11.4/cupertino/jquery-ui.min.css'
				,'public/css/jquery/dataTable/1.10.11/dataTables.jqueryui.min.css'
				,'public/css/jquery/dataTable/extensions/buttons-1.1.2/buttons.jqueryui.min.css'
				,'public/css/jquery/dataTable/extensions/colreorder-1.3.1/colReorder.jqueryui.min.css'
				//,'public/css/jquery/dataTable/extensions/fixedcolumns-3.2.1/fixedColumns.jqueryui.min.css'
				//,'public/css/jquery/dataTable/extensions/fixedheader-3.1.1/fixedHeader.jqueryui.min.css'
				//,'public/css/jquery/dataTable/extensions/fixedheader-3.1.3/fixedHeader.dataTables.min.css'
				,'public/css/jquery/dataTable/extensions/responsive-2.0.2/responsive.jqueryui.min.css'
				,'public/css/jquery/dataTable/extensions/scroller-1.4.1/scroller.jqueryui.min.css'
				,'public/css/jquery/dataTable/extensions/select-1.1.2/select.jqueryui.min.css'
			)
			, 'file'
			, 0
		);
		$this->_add_js(
			array(
				'public/js/jquery/2.2.0/jquery-2.2.0.min.js'
				// ++ Inferno changed to 1.10.4 because dialog position not work
				,'public/js/jquery/ui/1.11.4/jquery-ui.min.js'
				//,'public/js/jquery/ui/1.11.4/jquery-ui.min.js'
				// -- Inferno changed to 1.10.4 because dialog position not work
				,'public/js/jquery/dataTable/extensions/jszip-2.5.0/jszip.min.js'
				,'public/js/jquery/dataTable/extensions/pdfmake-0.1.18/pdfmake.min.js'
				,'public/js/jquery/dataTable/extensions/pdfmake-0.1.18/vfs_fonts.js'
				,'public/js/jquery/dataTable/1.10.11/jquery.dataTables.min.js'
				,'public/js/jquery/dataTable/1.10.11/dataTables.jqueryui.min.js'
				,'public/js/jquery/dataTable/extensions/buttons-1.1.2/dataTables.buttons.min.js'
				,'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.jqueryui.min.js'
				,'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.colVis.min.js'
				,'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.flash.min.js'
				,'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.html5.min.js'
				,'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.print.min.js'
				,'public/js/jquery/dataTable/extensions/colreorder-1.3.1/dataTables.colReorder.min.js'
				//,'public/js/jquery/dataTable/extensions/fixedcolumns-3.2.1/dataTables.fixedColumns.min.js'
				//,'public/js/jquery/dataTable/extensions/fixedheader-3.1.1/dataTables.fixedHeader.min.js'
				//,'public/js/jquery/dataTable/extensions/fixedheader-3.1.3/dataTables.fixedHeader.min.js'
				,'public/js/jquery/dataTable/extensions/responsive-2.0.2/dataTables.responsive.min.js'
				,'public/js/jquery/dataTable/extensions/responsive-2.0.2/responsive.jqueryui.min.js'
				,'public/js/jquery/dataTable/extensions/scroller-1.4.1/dataTables.scroller.min.js'
				,'public/js/jquery/dataTable/extensions/select-1.1.2/dataTables.select.min.js'
				,'public/js/jquery/dataTable/extensions/type-detection/moment_2.8.4.min.js'
				,'public/js/jquery/dataTable/extensions/type-detection/datetime-moment.js'
				,'public/js/jquery/dataTable/extensions/type-detection/numeric-comma.js'
				,'public/js/jsGlobalConstants.js'
				,'public/js/jsUtilities.js'
				,'public/js/jsGlobal.js'
			)
			, 'file'
			, 0
		);
		$this->_onload_scripts[1] .= <<<JQS
		$.fn.dataTable.moment( 'YYYY/MM/DD', moment.locale('en') );
		$.fn.dataTable.moment( 'DD/MM/YYYY', moment.locale('en') );
		$.fn.dataTable.moment( 'DD MM YYYY', moment.locale('en') );

JQS;

		$pass_main['title'] = $this->_pageTitle;
		$pass_main['work_panel'] = $this->_getMainPanel();
		$pass_main['left_panel'] = $this->_getLeftPanel();
		$_result = $this->add_view_with_script_header('_public/_template_main', $pass_main, TRUE);
		echo $_result;
	}
	public function json_search() {
		$this->_serviceCheckRight('view');
		$blnSuccess = FALSE;
		$arrResult = array();
		$strError = 'Unknown Error';
		$_arrData = $this->__getAjaxPostParams();
		if (! is_array($_arrData)) $_arrData = array();
		if (!empty($this->_model_fncSearch__arrAppendArgs)) $_arrData = array_merge($_arrData, $this->_model_fncSearch__arrAppendArgs);
		$this->load->model($this->modelName, 'm');
		if (! empty($_arrData)) {
			$arrResult = call_user_func(array($this->m, $this->_model_fncSearch), $_arrData); //$this->m->search($_arrData);
		} else {
			$arrResult = call_user_func(array($this->m, $this->_model_fncSearch)); //$this->m->search();
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
			//, JSON_NUMERIC_CHECK 
		);
		header('content-type: application/json; charset=utf-8');
		echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$json.")":$json;
	}
	function commit() {
		$this->_serviceCheckRight(($this->_blnCheckRight('insert') || $this->_blnCheckRight('edit')));
		$_blnSuccess = FALSE;
		$_strError = '';
		$_strMessage = '';
		$_arrData = $this->__getAjaxPostParams();
		if ($_arrData != FALSE) {
			$this->load->model($this->modelName, 'm');
			$_aff_rows = call_user_func(array($this->m, $this->_model_fncCommit), $_arrData); //$this->m->commit($_arrData);
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
	function cancel() {
		$this->_serviceCheckRight('cancel');
		$_blnSuccess = FALSE;
		$_strError = '';
		$_strMessage = '';
		$_arrData = $this->__getAjaxPostParams();
		if ($_arrData != FALSE) {
			$this->load->model($this->modelName, 'm');
			$_rowid = $_arrData['rowid'];
			$_aff_rows = call_user_func(array($this->m, $this->_model_fncCancel), $_rowid); //$this->m->cancel($_rowid);
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
	function delete() {
		$this->_serviceCheckRight('delete');
		$_blnSuccess = FALSE;
		$_strError = '';
		$_strMessage = '';
		$_arrData = $this->__getAjaxPostParams();
		if ($_arrData != FALSE) {
			$this->load->model($this->modelName, 'm');
			$_rowid = $_arrData['rowid'];
			$_aff_rows = call_user_func(array($this->m, $this->_model_fncDelete), $_rowid); //$this->m->delete($_rowid);
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
	function _prepareSelectOptions($_arrSelList) {	
		$this->_selOptions = hlpr_prepareMasterTableSelectOptions($_arrSelList, $this->_selOptions);
		return $this->_selOptions;
	}
	function _prepareControlsDefault() {
		$this->_arrDataViewFields = hlpr_prepareControlsDefault($this->modelName, $this->_selOptions);
	}
	function _setController($strDataColumn, $strLabel, $arrEditAttributes = NULL, $arrGridAttributes = NULL) {
		hlpr_setController($this->_arrDataViewFields, $strDataColumn, $strLabel, $arrEditAttributes, $arrGridAttributes);
	}
	function _getDisplayLabel($strDataColumn) {
		return hlpr_getDisplayLabel($this->_arrDataViewFields, $strDataColumn);
	}
	function _arrGetEditControls($arr = array()) {
		return hlpr_arrGetEditControls($this->_arrDataViewFields, $arr);
	}
	function _getPostObjValue($strName, $arrObjSource, $default = NULL) {
		if (isset($arrObjSource[$strName]) && (strlen(trim($arrObjSource[$strName])) > 0)) {
			return $this->db->escape_str($arrObjSource[$strName]);
		} else {
			return $this->db->escape_str($default);
		}
	}
}
/*
	function json_list_order_notification() {
		$this->load->model('mdl_master_table', 'mst');
		$_arrResult = $this->mst->list_order_notification();
		$_strError = $this->mst->error_message;
		$_blnSuccess = (trim($_strError) == '');
		if (! is_array($_arrResult)) $_arrResult = array();

		$_json = json_encode(
			array(
				'success' => $_blnSuccess,
				'error' => $_strError,
				'data' => $_arrResult
			)
		);
		echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$_json.")" : $_json;
	}
	function json_list_message_notification() {
		$this->load->model('mdl_master_table', 'mst');
		$_arrResult = $this->mst->list_message_notification();
		$_strError = $this->mst->error_message;
		$_blnSuccess = (trim($_strError) == '');
		if (! is_array($_arrResult)) $_arrResult = array();

		$_json = json_encode(
			array(
				'success' => $_blnSuccess,
				'error' => $_strError,
				'data' => $_arrResult
			)
		);
		header('content-type: application/json; charset=utf-8');
		echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$_json.")" : $_json;
	}
	function jsonGetProcessAccessControlObj($process_type) {
		$_procType = trim(strtolower($process_type));		
		$_arrReturn = array();
		$_ACR = FALSE;
		
		if (isset($this->_AC->_ACR)) $_ACR = $this->_AC->_ACR;
		if ((strlen($_procType) > 0) && ($_ACR)) {
			$_arrOrder = (isset($_ACR[$_procType . '_order']) && is_array($_ACR[$_procType . '_order'])) ? $_ACR[$_procType . '_order'] : array();
			$_arrProcs = (isset($_ACR[$_procType . '_process']) && is_array($_ACR[$_procType . '_process'])) ? $_ACR[$_procType . '_process'] : array();

			$_arrReturn["_object"] = array("order" => $_arrOrder, "process" => $_arrProcs);
			if (in_array("edit", $_arrOrder)) {
				$_arrReturn["edit"] = 1;
			} elseif (in_array("edit", $_arrProcs)) {
				$_arrReturn["edit"] = 2;
			}
			if (in_array("delete", $_arrOrder)) {
				$_arrReturn["delete"] = 1;
			}
		}
		
		return json_encode($_arrReturn);
	}
*/
//---------------------------------------------------
/*
	function __checkAcessibleOPDep__OLD() {
		$this->_access_op_dep_rowid = FALSE;
		$this->load->model('Mdl_M_operation_department', 'od');
		
		$_listResult = $this->od->_listUserCutOperationDepartments();
		if (is_array($_listResult) && (count($_listResult) > 0)) $this->_ALLOW_DEP_ROWS = $_listResult;

		$_sess_op_dep = $this->session->userdata('access_op_dep');
		if (($_sess_op_dep !== FALSE) && (trim($_sess_op_dep) != '')) {
			$this->_access_op_dep_rowid = $_sess_op_dep;
			$this->_model_fncSearch__arrAppendArgs['_list_op_dep_rowid'] = '(' . $this->_access_op_dep_rowid . ')';
		} else {
			$this->_access_op_dep_rowid = $this->od->getListOpDepRowid();
			if (trim($this->_access_op_dep_rowid) != '') {
				$this->session->set_userdata('access_op_dep', $this->_access_op_dep_rowid);
				$this->_model_fncSearch__arrAppendArgs['_list_op_dep_rowid'] = '(' . $this->_access_op_dep_rowid . ')';
			}
		}
	}
	function __getDepPageParameters__OLD($strCode = NULL, $blnForceSingle = TRUE, $availableDep = NULL, $exceptionDep = NULL) {
		if ($blnForceSingle !== FALSE) $blnForceSingle = TRUE;
		
		$this->_SELECT_DEP_ROW = FALSE;
		$this->_OPERATOR_DEP_CODE = '';
		$this->_access_op_dep_rowid = '';
		$this->session->unset_userdata('access_op_dep');
		if (strlen(trim($strCode)) > 0) {
			$this->_OPERATOR_DEP_CODE = trim(strtoupper($strCode));
			if (is_array($this->_ALLOW_DEP_ROWS)) foreach ($this->_ALLOW_DEP_ROWS as $_row) {
				if (isset($_row['code']) && (trim(strtoupper($_row['code'])) == $this->_OPERATOR_DEP_CODE)) {
					$this->_SELECT_DEP_ROW = $_row;
					break;
				}
			}
		} else {
			$_arrAvailDep = array();
			$_arrExceptDep = array();
			if (is_array($availableDep)) {
				foreach($availableDep as $_ea) {
					if (is_string($_ea)) $_arrAvailDep[] = trim(strtoupper($_ea));
					// else if (is_numeric($_ea)) {$_arrAvailDep[] = intval($_ea);}
				}
			} else if (is_string($availableDep)) {
				$_arrAvailDep[] = trim(strtoupper($availableDep));
			} else if (is_array($exceptionDep)) {
				foreach($exceptionDep as $_ea) {
					if (is_string($_ea)) $_arrExceptDep[] = trim(strtoupper($_ea));
					// else if (is_numeric($_ea)) {$_arrExceptDep[] = intval($_ea);}
				}
			} else if (is_string($exceptionDep)) {
				$_arrExceptDep[] = trim(strtoupper($exceptionDep));
			}
			if (is_array($this->_ALLOW_DEP_ROWS)) {
				foreach ($this->_ALLOW_DEP_ROWS as $_row) {
					if (isset($_row['code']) && (! empty($_row['code']))) {
						if (
							((count($_arrAvailDep) == 0) && (count($_arrExceptDep) == 0))
							|| ((count($_arrAvailDep) > 0) && (in_array(trim(strtoupper($_row['code'])), $_arrAvailDep)))
							|| ((count($_arrExceptDep) > 0) && ( ! in_array(trim(strtoupper($_row['code'])), $_arrExceptDep)))
						) {
							if (($blnForceSingle === TRUE)) {
								$this->_SELECT_DEP_ROW = $_row;
								$this->_OPERATOR_DEP_CODE = trim(strtoupper($_row['code']));
								break;
							} else {
								$this->_access_op_dep_rowid .= $_row['rowid'] . ',';
							}
						}
					}
				}
			}
		}
		if (is_array($this->_SELECT_DEP_ROW)) {
			$this->_pageTitle .= " :: " . $this->_SELECT_DEP_ROW['name_th']; //$this->_OPERATOR_DEP_CODE;
			$this->_access_op_dep_rowid = $this->_SELECT_DEP_ROW['rowid'] . ',';
		} else {
			// if force single but none dep selected
			if (($blnForceSingle === TRUE)) die(str_replace('v_XX_1', ('"' . $this->session->userdata('user_name') . '"'), $this->_AC->_MSG_ACCESS_NOT_ALLOWED));
		}
		if (trim($this->_access_op_dep_rowid) != '') {
			$this->_access_op_dep_rowid = substr($this->_access_op_dep_rowid, 0, -1);
			$this->_model_fncSearch__arrAppendArgs['_list_op_dep_rowid'] = '(' . $this->_access_op_dep_rowid . ')';
			$this->session->set_userdata('access_op_dep', $this->_access_op_dep_rowid);
		}
	}
*/