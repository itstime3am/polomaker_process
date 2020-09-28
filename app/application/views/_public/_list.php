<?php
//print_r($this->_ci_cached_vars);exit;
//print_r($_ACR);exit;
	$_strDtColumns = '';
	$_strDtDisplay = '';
	$_strSelectElem = '';
	if (isset($dataview_fields)) {
		if (is_array($dataview_fields)) {
			$_arr = array();
			$_count = 1;
			foreach ($dataview_fields as $_id=>$_obj) {
				$_lbl = '';
				if (array_key_exists("form_edit", $_obj)) {
					$_lbl = (array_key_exists("label", $_obj["form_edit"]))?$_obj["form_edit"]["label"]:'';
				} else if (array_key_exists("list_item", $_obj)) {
					$_lbl = (array_key_exists("label", $_obj["list_item"]))?$_obj["list_item"]["label"]:'';
				}
				$_strDtColumns .= '["' . $_id . '","' . $_lbl . '"],';

				if (array_key_exists("list_item", $_obj)) {
					$_str1 = '';
					$_title = (array_key_exists("label", $_obj["list_item"]))?$_obj["list_item"]["label"]:'';
					$_class = '';
					$_str = '';
					$_str1 .= '{"sTitle":"' . $_title . '", "mData":"' . $_id . '"';
					if (array_key_exists('width', $_obj["list_item"])) $_str1 .= ',"sWidth":"' . $_obj["list_item"]['width'] . '"';
					if (array_key_exists("selectable", $_obj["list_item"]) && ((boolean)($_obj["list_item"]["selectable"]) == TRUE)) {
						$_class = 'cls-selectable ';
						$_str .= '<li><input type="checkbox" id="li_col_XXXX" ';
						if (array_key_exists('default', $_obj["list_item"]) && ((boolean)($_obj["list_item"]["default"]) == FALSE)) $_str .= 'checked';
						$_str .= ' />' . $_title . '</li>';
					} else {
						$_str1 .= ',"sNoToggle": true';
					}
					if (array_key_exists('class', $_obj["list_item"])) {
						$_class .= strtolower($_obj["list_item"]['class']);
						if (strpos($_class, 'default_number') !== FALSE) {
							$_str1 .= ',"mRender": function(data, type, full) { if ($.isFunction(_fncDTmRenderFormat)) { return _fncDTmRenderFormat(data, type, full, "default_number"); } else { return data } }';
							$_class = trim(str_replace('default_number', 'right', $_class));
						}
						if ((strpos($_class, 'default_int') !== FALSE) || (strpos($_class, 'default_integer') !== FALSE)) {
							$_str1 .= ',"mRender": function(data, type, full) { if ($.isFunction(_fncDTmRenderFormat)) { return _fncDTmRenderFormat(data, type, full, "default_int"); } else { return data } }';
							$_class = trim(str_replace('default_int', 'right', $_class));
							$_class = trim(str_replace('default_integer', 'right', $_class));
						}
					}
					$_str1 .= ',"sClass":"' . $_class . '"';
					if (array_key_exists('default', $_obj["list_item"]) && ((boolean)($_obj["list_item"]["default"]) == FALSE)) $_str1 .= ',"bVisible":false';
					$_str1 .= '},';
					
					if (array_key_exists("order", $_obj["list_item"])) {
						$_arr[$_obj["list_item"]["order"]] = array($_str, $_str1);
					} else {
						$_arr[90 + $_count] = array($_str, $_str1);
						$_count++;
					}
				}
			}
			if (isset($custom_columns)) {
				if (is_array($custom_columns)) {
					foreach ($custom_columns as $_col) {
						if (is_array($_col)) {
							$_str1 = $_col["column"];
							if (substr(trim($_str1), -1, 1) != ',') $_str1 = trim($_str1) . ',';
							if (array_key_exists("order", $_col) && (! array_key_exists($_col["order"], $_arr))) {
								$_arr[$_col["order"]] = array('', $_str1);
							} else {
								$_arr[90 + $_count] = array('', $_str1);
								$_count++;
							}
						} else {
							$_arr[90 + $_count] = array('', $_col);
							$_count++;
						}
					}
				} else { //string
					if ((trim($custom_columns) != '')) {
						if (substr(trim($custom_columns), -1, 1) != ',') $custom_columns = trim($custom_columns) . ',';
						$_arr[90 + $_count] = array('', $custom_columns);
					}
				}
			}
			ksort($_arr);
			$_i = 0;
			foreach ($_arr as $_elems) {
				$_strSelectElem .= str_replace("XXXX", $_i, $_elems[0]);
				$_strDtDisplay .= $_elems[1];
				$_i ++;
			}
			if (strlen($_strDtColumns) > 0) $_strDtColumns = substr($_strDtColumns, 0, -1);
			if (strlen($_strDtDisplay) > 0) {
				if (!(isset($list_viewable) && ($list_viewable == FALSE))) $_strDtDisplay .= '{"sTitle":"เรียกดู", "sWidth":"50","sClass":"center no-export","mData":function() { return \'<img class="list-row-button" command="view" src="./public/images/b_view.png" alt="view" title="\' + MSG_ICON_TITLE_VIEW + \'" />\';}, "bSortable": false},';
				if (!(isset($list_editable) && ($list_editable == FALSE))) $_strDtDisplay .= '{"sTitle":"แก้ไข", "sWidth":"50","sClass":"center no-export","mData":function() { return \'<img class="list-row-button" command="edit" src="./public/images/b_edit.png" alt="edit" title="\' + MSG_ICON_TITLE_EDIT + \'" />\';}, "bSortable": false},';
				if (!(isset($list_deleteable) && ($list_deleteable == FALSE))) $_strDtDisplay .= '{"sTitle":"ลบ", "sWidth":"50","sClass":"center no-export","mData":function() { return \'<img class="list-row-button" command="delete" src="./public/images/b_delete.png" alt="delete" title="\' + MSG_ICON_TITLE_DELETE + \'" />\';}, "bSortable": false}';
				if ((isset($list_cancelable)) && ($list_cancelable == TRUE)) $_strDtDisplay .= '{"sTitle":"ยกเลิก", "sWidth":"50","sClass":"center no-export","mData":function() { return \'<img class="list-row-button" command="cancel" src="./public/images/hide-show.png" alt="cancel" title="\' + MSG_ICON_TITLE_CANCEL + \'" />\';}, "bSortable": false}';

				if (substr(trim($_strDtDisplay), -1, 1) == ',') $_strDtDisplay = substr(trim($_strDtDisplay), 0, -1);
				if (substr(trim($_strDtColumns), -1, 1) == ',') $_strDtColumns = substr(trim($_strDtColumns), 0, -1);		
			}
			$_strSelectElem = '<ul>' . $_strSelectElem . '</ul>';
		}
	}
	$_strTableToolButtons = '';
	if ((isset($jqDataTable) && ($jqDataTable >= '1.10')) || _isJQDTTT_HTML5()) {
			$_strTableToolButtons .= <<<TBLT
						"pageLength"
TBLT;
	}
	if (!(isset($list_select_columns) && ($list_select_columns == FALSE))) {
		if ((isset($jqDataTable) && ($jqDataTable >= '1.10')) || _isJQDTTT_HTML5()) {
			$_strTableToolButtons .= <<<TBLT
						, {
							"extend": "colvis", "text": "เลือกแสดงข้อมูล", "className": "cls_button_select"
							, "columnText": function ( dt, idx, title ) {
								return (idx + 1) + ': ' + title;
							}
							, "columns": function ( idx, data, node ) {
								var _colDef = _objDataTable.DataTable().init().aoColumns[idx];
								return ((_objDataTable.DataTable().column(idx).dataSrc() == "client_temp_id") || (('sNoToggle' in _colDef) && (_colDef['sNoToggle'] == true))) ? false : true;
							}
							, "showAll": true
							, "showNone": true
						}
						, {"text": "&nbsp;","className": "DTTT_button_space"}
TBLT;
		} else {
			$_strTableToolButtons .= <<<TBLT
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
	}
	if ((isset($jqDataTable) && ($jqDataTable >= '1.10')) || _isJQDTTT_HTML5()) {
		$_strTableToolButtons .= <<<TBLT
						, {"extend": "copy","text": "คัดลอก", "className": "DTTT_button_copy", "exportOptions": {"columns":  ":visible:not(.no-export)"}}
						, {"extend": "print", "text": "พิมพ์", "className": "DTTT_button_print", "exportOptions": {"columns": ":visible:not(.no-export)"}}
						, {"extend": "excelHtml5","text": "Excel", "className": "DTTT_button_xls", "exportOptions": {"columns": ":visible:not(.no-export)"}}
						, {"extend": "csvHtml5","text": "CSV", "className": "DTTT_button_csv", "exportOptions": {"columns": ":visible:not(.no-export)"}}
						, {"extend": "pdfHtml5","text": "PDF", "className": "DTTT_button_pdf", "exportOptions": {"columns": ":visible:not(.no-export)"}}
TBLT;
	} else {
		$_strTableToolButtons .= <<<TBLT
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
						}
						, {
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
									doToggleLeftPanel();
								}
								_visibleButtonColumns(false);
								$(window).keyup(function() {
									_visibleButtonColumns(true);
									if (_blnLeft) doToggleLeftPanel();
								});
								this.fnPrint( true, oConfig );
							}
						}
						, {
							"sExtends": "text",
							"sButtonText": "Excel",
							"sButtonClass": "DTTT_button_xls",
							"bShowAll": true,
							"bHeader": true,
							"bFooter": false,
							"mColumns": "visible",
							"fnClick": function ( nButton, oConfig, oFlash ) {
								doExportExcel( nButton, oConfig, oFlash );
							}
						}
TBLT;
	}
	if (!(isset($list_insertable) && ($list_insertable == FALSE))) {
		if ((isset($jqDataTable) && ($jqDataTable >= '1.10')) || _isJQDTTT_HTML5()) {
			$_strTableToolButtons .= <<<TBLT
						, {"text": "&nbsp;","className": "DTTT_button_space"}
						, {
							"text": "เพิ่ม","className": "DTTT_button_add_row"
							,"action": function ( nButton, oConfig, oFlash ) {
								if (! $(nButton).is('.DTTT_button_disabled')) doInsert($("#divFormEditDialog"));
							}
						}
TBLT;
		} else {
			$_strTableToolButtons .= <<<TBLT
						, {
							"sExtends": "text"
							, "sButtonText": ""
							, "sButtonClass": "DTTT_button_space"
							
						}
						, {
							"sExtends": "text",
							"sButtonText": "เพิ่ม",
							"sButtonClass": "DTTT_button_add_row",
							"fnClick": function ( nButton, oConfig, oFlash ) {
								doInsert($("#divFormEditDialog"));
							}
						}
TBLT;
		}
	}
	echo <<<EOT
	<script language='javascript'>
		var _arrDtColumns = [$_strDtColumns];
		var _aoColumns = [$_strDtDisplay];
		var _tableToolButtons = [$_strTableToolButtons];
	</script>
EOT;
?>
<div id="divPanelHandler"><img src="./public/images/hide-show.png" title="ซ่อน/แสดง แผงควบคุม"></div>
<div id="divDisplayQueryResult" class="cls-div-list" index="<?php echo isset($index)?$index:0; ?>" >
	<table id="tblSearchResult" class="cls-tbl-list"></table>
</div>
<div id="divFormEditDialog" class="cls-div-form-edit-dialog" style="display:none;" index="<?php echo isset($index)?$index:0; ?>">
<?php
	if (isset($edit_template)) echo $edit_template
?>
</div>
<div id="divSelectableFields" class="cls-div-select-list-fields" index="<?php echo isset($index)?$index:0; ?>" >
<?php
	echo $_strSelectElem;
?>
</div>