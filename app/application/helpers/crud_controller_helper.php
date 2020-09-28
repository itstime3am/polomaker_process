<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

function hlpr_prepareMasterTableSelectOptions($_arrSelList) {
	$_arrSelOptions = array();
	$CI = get_instance();
	$CI->load->model('mdl_master_table', 'mt');
	foreach ($_arrSelList as $_index=>$_item) {
		$_order = 'rowid';
		$_text = 'name';
		$_pref = 'pm_m_';
		$_table = FALSE;
		$_key = FALSE;
		$_arr = FALSE;
		$_blnNoFeedRow = FALSE;
		if (isset($_index) && (!empty($_index)) && (!is_numeric($_index))) {
			$_key = (string) $_index;
			$_table = (string) $_index;
		}
		if (isset($_item) && is_array($_item)) {
			if (array_key_exists('table_name', $_item)) {
				$_table = $_item['table_name'];
				$_pref = ""; //if not set table_prefix then pass "" for no prefix
			}
			if (! $_table) continue;
			if (array_key_exists('table_prefix', $_item)) $_pref = $_item['table_prefix'];
			if (array_key_exists('order_by', $_item)) $_order = $_item['order_by'];
			if (array_key_exists('text', $_item)) $_text = $_item['text'];
			if (array_key_exists('where', $_item)) {
				$_arr = $CI->mt->list_where($_table, $_item['where'], $_order, $_pref);				
			} else {
				$_arr = $CI->mt->list_all($_table, $_order, $_pref);
			}
			if (array_key_exists('no_feed_row', $_item) && ($_item['no_feed_row'])) $_blnNoFeedRow = TRUE;
		} else if (is_string($_item)) {
			$_key = $_item;
			$_arr = $CI->mt->list_all($_item);
		}
		if ((! empty($_key)) && is_array($_arr)) {
			if (($_blnNoFeedRow == FALSE) && (count($_arr) > 0)) {
				array_unshift($_arr, array('rowid'=>'', $_text=>''));
			}
			$_arrSelOptions[$_key] = $_arr;
		}
	}
	return $_arrSelOptions;
}

function hlpr_prepareControlsDefault($modelName, $selOptions = array()) {
	$CI = null;
	if (!$CI) $CI = get_instance();
	$CI->load->model($modelName);
	$_arrDataViewFields = array();
	foreach ($CI->$modelName->_AUTO_FIELDS as $_name => $_value) {
		$_arrDataViewFields[$_name] = array("form_edit" => array("name" => $_name, "type" => "hdn"));
	}
	foreach ($CI->$modelName->_FIELDS as $_name => $_value) {
		$_arr = array();
		$_label = '';
		if (($_name == 'create_by') || ($_name == 'create_date') || ($_name == 'update_by') || ($_name == 'update_date')) continue;
		$_label = $_name;
		$_arr['type'] = 'txt';
		if (strlen($_name) > 6) {
			if (substr($_name, -5) == 'rowid') {
				$_label = substr($_name, 0, -6);
				$_arr['type'] = 'sel';
				$_arr['sel_text'] = 'name';
				$_arr['sel_val'] = 'rowid';
				if (array_key_exists($_label, $selOptions)) $_arr['sel_options'] = $selOptions[$_label];
				$_arr['hidden_name'] = $_label . '_disp';
			}
		}
		$_arr['label'] = str_replace('_', ' ', ucwords($_label));
		$_arr['name'] = $_name;
		$_arrDataViewFields[$_name] = array("form_edit" => $_arr);
	}
	return $_arrDataViewFields;
}

function hlpr_setController(&$destArray, $strDataColumn, $strLabel, $arrEditAttributes = array(), $arrGridAttributes = array()) {
	if (! is_array($destArray)) return FALSE;
	
	if ((! array_key_exists($strDataColumn, $destArray)) || (! is_array($destArray[$strDataColumn]))) {
		$destArray[$strDataColumn] = array("form_edit" => array('name'=>$strDataColumn, 'label'=>$strLabel));
	} else if ((! array_key_exists('form_edit', $destArray[$strDataColumn])) || (! is_array($destArray[$strDataColumn]['form_edit']))) {
		$destArray[$strDataColumn]['form_edit'] = array('name'=>$strDataColumn, 'label'=>$strLabel);
	} else {
		if (! array_key_exists('name', $destArray[$strDataColumn]['form_edit'])) $destArray[$strDataColumn]['form_edit']['name'] = $strDataColumn;
		$destArray[$strDataColumn]['form_edit']['label'] = $strLabel;
	}
	if (is_array($arrEditAttributes)) {
		foreach ($arrEditAttributes as $_key => $_itm) {
			$destArray[$strDataColumn]["form_edit"][$_key] = $_itm;
		}
	}

	if (is_array($arrGridAttributes) && count($arrGridAttributes) > 0) {
		if (! array_key_exists("list_item", $destArray[$strDataColumn])) {
			$destArray[$strDataColumn]["list_item"] = array("label"=>$strLabel);
		} else {
			$destArray[$strDataColumn]['list_item']["label"] = $strLabel;
		}
		foreach ($arrGridAttributes as $_key => $_itm) {
			$destArray[$strDataColumn]["list_item"][$_key] = $_itm;
		}
	}
}

function hlpr_getDisplayLabel($srcArray, $strDataColumn) {
	if (array_key_exists($strDataColumn, $srcArray)) {
		if (array_key_exists("form_edit", $srcArray[$strDataColumn])) {
			if (array_key_exists("label", $srcArray[$strDataColumn]["form_edit"])) return $srcArray[$strDataColumn]["form_edit"]["label"];
		} else if (array_key_exists("list_item", $srcArray[$strDataColumn])) {
			if (array_key_exists("label", $srcArray[$strDataColumn]["list_item"])) return $srcArray[$strDataColumn]["list_item"]["label"];
		}
	}
	return str_replace('_', ' ', ucwords($strDataColumn));
}

function hlpr_arrGetEditControls($srcArray, $arr = array()) {
	if (count($arr) == 0) $arr = $srcArray;
	$_arrReturn = array();
	foreach ($arr as $_key => $_obj) {
		if (array_key_exists("form_edit", $_obj)) {
			//at least default from model will always have "type"
			if (array_key_exists("type", $_obj["form_edit"])) $_arrReturn[$_key] = $_obj["form_edit"];
		}
	}
	return $_arrReturn;
}
	function hlpr_getDataTableToolOptions($strEditDialogSelector, $viewType, $params, $pageOptions, &$onloadScript = NULL) {
		//if (isset($pageOptions['type']) && (strtolower($pageOptions['type']) == 'top_main') && isset($pageOptions['jqDataTable']) && ($pageOptions['jqDataTable'] == '1.10.11')) {
			return __strGetDTTableTools_1_10($strEditDialogSelector, $viewType, $params);
		/*} else {
			return __strGetDTTableToolsBefore_1_10($strEditDialogSelector, $viewType, $params);
		}*/
	}

	function __strGetDTTableToolsBefore_1_10($strEditDialogSelector, $viewType, $params) {
		$_str = '';
		if (($viewType == 3) && (!(isset($params['list_select_columns']) && ($params['list_select_columns'] == FALSE)))) {
			$_str .= <<<TBLT
		{
			"sExtends": "text",
			"sButtonText": "เลือกแสดงข้อมูล",
			"sButtonClass": "cls_button_select",
			"fnClick": function ( nButton, oConfig, oFlash ) {
				doSelectDisplayFields();
			}
		},
TBLT;
		}
		if (!(isset($params['list_button_copy']) && ($params['list_button_copy'] == FALSE))) {
			$_str .= <<<TBLT
		{
			"sExtends": "copy",
			"sButtonText": "คัดลอก",
			"bShowAll": true,
			"bHeader": true,
			"bFooter": false,
			"mColumns": "visible",
			"fnCellRender": function ( sValue, iColumn, nTr, iDataIndex ) {
				if (sValue.length > 4) if (sValue.substr(0, 4) == "<img") return '';
				return sValue;
			}
		},
TBLT;
		}
		if (!(isset($params['list_button_print']) && ($params['list_button_print'] == FALSE))) {
			$_str .= <<<TBLT
		{
			"sExtends": "print",
			"sButtonText": "พิมพ์",
			"bShowAll": true,
			"bHeader": true,
			"bFooter": false,
			//"sInfo": "Please press escape when done",
			"mColumns": "visible",
			"fnClick": function (nButton, oConfig, oFlash) {
				_blnLeft = false;
				if ($('#left_panel').css('display') !== 'none') {
					_blnLeft = true;
					if (typeof doToggleLeftPanel == 'function') doToggleLeftPanel();
				}
				if (typeof _visibleButtonColumns == 'function') _visibleButtonColumns(false);
				$(window).keyup(function() {
					if (typeof _visibleButtonColumns == 'function') _visibleButtonColumns(true);
					if (_blnLeft && (typeof doToggleLeftPanel == 'function')) doToggleLeftPanel();
				});
				this.fnPrint( true, oConfig );
			}
		},
TBLT;
		}
		if (($viewType == 3) && (!(isset($params['list_button_excel']) && ($params['list_button_excel'] == FALSE)))) {
			$_str .= <<<TBLT
		{
			"sExtends": "text",
			"sButtonText": "Excel",
			"sButtonClass": "DTTT_button_xls",
			"bShowAll": true,
			"bHeader": true,
			"bFooter": false,
			"mColumns": "visible",
			"fnClick": function ( nButton, oConfig, oFlash ) {
				if (! $(nButton).is('.DTTT_button_disabled')) doExportExcel( nButton, oConfig, oFlash );
			}
		},

TBLT;
		}
		if (!(isset($params['list_insertable'])) && (isset($params['list_addable']))) $params['list_insertable'] = $params['list_addable'];
		if (!(isset($params['list_insertable']) && ($params['list_insertable'] == FALSE))) {
			$_str .= <<<TBLT
		{
			"sExtends": "text"
			, "sButtonText": ""
			, "sButtonClass": "DTTT_button_space"
			
		},
		{
			"sExtends": "text",
			"sButtonText": "เพิ่ม",
			"sButtonClass": "DTTT_button_add_row",
			"fnClick": function ( nButton, oConfig, oFlash ) {
				if (! $(nButton).is('.DTTT_button_disabled')) doInsert($('$strEditDialogSelector'));
			}
		}
TBLT;
		}
		return $_str;
	}

	function __strGetDTTableTools_1_10($strEditDialogSelector, $viewType, $params) {
		$_str = '"pageLength", {"text": "&nbsp;","className": "DTTT_button_space"}';
		if (($viewType == 3) && (!(isset($params['list_select_columns']) && ($params['list_select_columns'] == FALSE)))) {
			$_str .= <<<TBLT

		, {"extend": "colvis", "text": "เลือกแสดงข้อมูล", "className": "cls_button_select", "columns": function ( idx, data, node ) {
				var _colDef = _objDataTable.DataTable().init().aoColumns[idx];
				return ((_objDataTable.DataTable().column(idx).dataSrc() == "client_temp_id") || (('sNoToggle' in _colDef) && (_colDef['sNoToggle'] == true))) ? false : true;
			}
		}

TBLT;
		}
		if (!(isset($params['list_button_copy']) && ($params['list_button_copy'] == FALSE))) {
			$_str .= <<<TBLT
		, {"extend": "copy","text": "คัดลอก", "exportOptions": {"columns": ":visible:not(.no-export)"}}

TBLT;
		}
		if (!(isset($params['list_button_print']) && ($params['list_button_print'] == FALSE))) {
			$_str .= <<<TBLT
		, {"extend": "print","text": "พิมพ์", "exportOptions": {"columns": ":visible:not(.no-export)"}}

TBLT;
		}
		if (($viewType == 3) && (!(isset($params['list_button_excel']) && ($params['list_button_excel'] == FALSE)))) {
			$_str .= <<<TBLT
		, {"extend": "excel","text": "Excel","className": "DTTT_button_xls", "exportOptions": {"columns": ":visible:not(.no-export)"}}

TBLT;
		}
		if (!(isset($params['list_insertable'])) && (isset($params['list_addable']))) $params['list_insertable'] = $params['list_addable'];
		if (!(isset($params['list_insertable']) && ($params['list_insertable'] == FALSE))) {
			$_str .= <<<TBLT
		, {"text": "&nbsp;","className": "DTTT_button_space"}
		, {
			"text": "เพิ่ม","className": "DTTT_button_add_row"
			,"action": function ( nButton, oConfig, oFlash ) {
				if (! $(nButton).is('.DTTT_button_disabled')) doInsert($('$strEditDialogSelector'));
			}
		}
TBLT;
		}
		return $_str;
	}

/*++ View Helper*/
function _getLayoutItemDisplay($layout, &$arrControls, &$arrEditPanelDataKey) {
	$_return = '';
	foreach ($layout as $_grp => $_item) {
		$_rows = '';
		if (is_array($_item)) {
			$_rows .= _getRow($_item, $arrControls, $arrEditPanelDataKey);
		}
		$_return .= '<tr><td colspan="3" class="td-align-center">';
		if ((! is_numeric($_grp)) && (strlen($_grp) > 0)) {
			$_return .= '<div class="frm-edit-row-group">';
			$_return .= '<span class="group-title">' . $_grp . '</span>';
			$_return .= $_rows;
			$_return .= '</div>';
		} else {
			$_return .= $_rows;
		}
		$_return .= '</td></tr>';
	}
	return $_return;
}

function _getRow($arrItems, &$arrControls, &$arrEditPanelDataKey, $intSubLevel = 0) {
	$_row = '';
	$_each = '';
	$_group = FALSE;
	$_width = 0;
	if (is_array($arrItems)) {
		if (count($arrItems) > 0) $_width = floor(100 / (count($arrItems) * 5));
		foreach ($arrItems as $_grp => $_item) {
			if ( ! is_array($_item)) {
				$_title = '';
				$_elem = '';
				$_type = '';
				if ((strlen($_item) > 7) && (substr($_item, 0, 7) == 'return ')) {
					$_elem = substr($_item, 7);
					if (preg_match_all('/\{([^\}]+)\}/', $_elem, $_matches)) {
						foreach($_matches[1] as $_col) {
							if ( ! (isset($arrEditPanelDataKey) && is_array($arrEditPanelDataKey))) {
								$arrEditPanelDataKey = array();
							}
							array_push($arrEditPanelDataKey, $_col);
						}
					}
				} else if (array_key_exists($_item, $arrControls)) {
					$_title = trim($arrControls[$_item][0]);
					$_elem = $arrControls[$_item][1];
					if (count($arrControls[$_item]) > 2) $_type = $arrControls[$_item][2];
					unset($arrControls[$_item]);
				}
				if (($_title == 'hidden') || ($_type == 'info')) {
					$_each = $_elem;
				} else if ($_title != '') {
					if ($_type == 'chk') {
						$_each = '<div class="table-value cls-row-value frm-edit-row-value value-checkbox" style="margin-left:' . $_width . '%;width:' . $_width . '%;">' . $_elem . '</div>';
						$_each .= '<div class="table-title frm-edit-row-title title-checkbox" style="margin-left:10px;min-width:' . ($_width * 3) . '%" >' . $_title . '</div>';
					} else {
						$_each = '<div class="table-title frm-edit-row-title" style="width:' . ($_width * 2) . '%" >' . $_title . '</div>';
						$_each .= '<div class="table-value cls-row-value frm-edit-row-value" style="width:' . ($_width * 3) . '%" >' . $_elem . '</div>';
					}
				} else {
					$_each = '<div class="cls-row-value" style="width:' . ($_width * 5) . '%;" >' . $_elem . '</div>';
				}
			} else {
				$_subLevel = $intSubLevel + 1;
				$_group = TRUE;
				$_each = _getRow($_item, $arrControls, $arrEditPanelDataKey, $_subLevel);
			}
			if ((! is_numeric($_grp)) && (strlen($_grp) > 0)) {
				$_row .= '<div class="frm-edit-row-group cls-row-group">';
				$_row .= '<span class="group-title cls-group-title">' . $_grp . '</span>';
				$_row .= $_each;
				$_row .= '</div>';
			} else {
				$_row .= $_each;
			}
		}
		if ($_group == FALSE) {
			$_row = '<div class="frm-edit-row cls-row" >' . $_row . '</div>';
		}
	}
	return $_row;
}

function _isJQDTTT_HTML5() {
	$CI = get_instance();
	return ($CI->__isExistsJs('dataTable/1.10.11', NULL, FALSE) > 0);
}
/*-- View Helper*/

/* End of file crud_controller_helper.php */ 
/* Location: ./application/helpers/crud_controller_helper.php */ 