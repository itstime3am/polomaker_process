<?php
	$_index = isset($index)?$index:0;
	$_strDtColumns = '';
	$_strDtDisplay = '';
	//$_strSelectElem = '';
	if (isset($dataview_fields)) {
		if (is_array($dataview_fields)) {
			$_arr = array();
			$_count = 1;
			foreach ($dataview_fields as $_id => $_obj) {
				if (array_key_exists("form_edit", $_obj)) {
					$_lbl = (array_key_exists("label", $_obj["form_edit"]))?$_obj["form_edit"]["label"]:'';
					$_strDtColumns .= '["' . $_id . '","' . $_lbl . '"],';
				} else if (array_key_exists("list_item", $_obj)) {
					$_lbl = (array_key_exists("label", $_obj["list_item"]))?$_obj["list_item"]["label"]:'';
					$_strDtColumns .= '["' . $_id . '","' . $_lbl . '"],';
				}
				if (array_key_exists("list_item", $_obj)) {
					$_str1 = '';
					$_title = (array_key_exists("label", $_obj["list_item"]))?$_obj["list_item"]["label"]:'';
					$_str1 .= '{"sTitle":"' . $_title . '", "mData":"' . $_id . '"';
					if (array_key_exists('width', $_obj["list_item"])) $_str1 .= ',"sWidth":"' . $_obj["list_item"]['width'] . '"';
					if (array_key_exists('class', $_obj["list_item"])) {
						$_class = strtolower($_obj["list_item"]['class']);
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

					$_str = '';
					if (array_key_exists("selectable", $_obj["list_item"]) && ((boolean)($_obj["list_item"]["selectable"]) == TRUE)) {
						$_str .= '<li><input type="checkbox" id="li_col_XXXX" ';
						if (array_key_exists('default', $_obj["list_item"]) && ((boolean)($_obj["list_item"]["default"]) == FALSE)) $_str .= 'checked';
						$_str .= ' />' . $_title . '</li>';							
					}

					if (array_key_exists("order", $_obj["list_item"])) {
						$_arr[$_obj["list_item"]["order"]] = array($_str, $_str1);
					} else {
						$_arr[90 + $_count] = array($_str, $_str1);
						$_count++;
					}
/*
					if (array_key_exists("selectable", $_obj["list_item"])) {
						if ((boolean)($_obj["list_item"]["selectable"])) {
							$_str = '';
							$_str1 = '';
							$_title = (array_key_exists("label", $_obj["list_item"]))?$_obj["list_item"]["label"]:'';
							$_str .= '<li><input type="checkbox" id="li_col_XXXX" ';
							$_str1 .= '{"sTitle":"' . $_title . '", "mData":"' . $_id . '"';
							if (array_key_exists('width', $_obj["list_item"])) {
								$_str1 .= ',"sWidth":"' . $_obj["list_item"]['width'] . '"';
							}
							if (array_key_exists('class', $_obj["list_item"])) {
								$_str1 .= ',"sClass":"' . $_obj["list_item"]['class'] . '"';
							}
							$_visible = FALSE;
							if (array_key_exists('default', $_obj["list_item"])) {
								if (((boolean)($_obj["list_item"]["default"]))) {
									$_visible = TRUE;
									$_str .= 'checked';
								}
							}
							if (! $_visible) $_str1 .= ',"bVisible":false';
							$_str .= ' />' . $_title . '</li>';
							$_str1 .= '},';
							
							if (array_key_exists("order", $_obj["list_item"])) {
								$_arr[$_obj["list_item"]["order"]] = array($_str, $_str1);
							} else {
								$_arr[90 + $_count] = array($_str, $_str1);
								$_count++;
							}
						}
					}
*/
				}
			}
			ksort($_arr);
			$_i = 0;
			foreach ($_arr as $_elems) {
				//if ($_elems[0] != '') $_strSelectElem .= str_replace("XXXX", $_i, $_elems[0]);
				$_strDtDisplay .= $_elems[1];
				$_i ++;
			}

			if (strlen($_strDtDisplay) > 0) {
				$_strDtColumns = substr($_strDtColumns, 0, -1);

				if (isset($custom_columns) && (trim($custom_columns) != '')) {
					$_strDtDisplay .= $custom_columns;
					if (substr(trim($custom_columns), -1, 1) != ',') {
						$_strDtDisplay .= ',';
					}
				}

				if (!(isset($list_viewable) && ($list_viewable == FALSE))) $_strDtDisplay .= '{"sTitle":"เรียกดู", "sWidth":"50","sClass":"center","mData":function() { return \'<img class="tblButton" command="view" src="./public/images/b_view.png" alt="view" title="\' + MSG_ICON_TITLE_VIEW + \'" />\';}, "bSortable": false},';
				if (!(isset($list_editable) && ($list_editable == FALSE))) $_strDtDisplay .= '{"sTitle":"แก้ไข", "sWidth":"50","sClass":"center","mData":function() { return \'<img class="tblButton" command="edit" src="./public/images/b_edit.png" alt="edit" title="\' + MSG_ICON_TITLE_EDIT + \'" />\';}, "bSortable": false},';
				if (!(isset($list_deleteable) && ($list_deleteable == FALSE))) $_strDtDisplay .= '{"sTitle":"ลบ", "sWidth":"50","sClass":"center","mData":function() { return \'<img class="tblButton" command="delete" src="./public/images/b_delete.png" alt="delete" title="\' + MSG_ICON_TITLE_DELETE + \'" />\';}, "bSortable": false}';

				if (substr(trim($_strDtDisplay), -1, 1) == ',') $_strDtDisplay = substr(trim($_strDtDisplay), 0, -1);				
				if (substr(trim($_strDtColumns), -1, 1) == ',') $_strDtColumns = substr(trim($_strDtColumns), 0, -1);								
			}
			//$_strSelectElem = '<ul>' . $_strSelectElem . '</ul>';
		}
	}
	echo <<<EOT
	<script language='javascript'>
		if (! _Sublist_arrDtColumns) _Sublist_arrDtColumns = [];
		if (! _Sublist_aoColumns) _Sublist_aoColumns = [];

		_Sublist_arrDtColumns[$_index] = [$_strDtColumns];
		_Sublist_aoColumns[$_index] = [$_strDtDisplay];
	</script>
EOT;
?>
<div id="divSubListInfo" class="cls-div-info" index="<?php echo isset($index)?$index:0; ?>" ><?php echo isset($info)?$info:''?></div>
<div id="divSubList" class="cls-div-list cls-div-sub-list" controller="<?php echo isset($crud_controller)?$crud_controller:$this->uri->rsegment(1); ?>" index="<?php echo isset($index)?$index:0; ?>" master_cols="<?php echo isset($master_cols)?$master_cols:''; ?>" map_cols="<?php echo isset($map_cols)?$map_cols:''; ?>" >
	<table id="tblSubList" class="cls-tbl-list cls-tbl-sub-list"></table>
</div>
<div id="divSublistFormEditDialog" class="cls-div-form-edit-dialog" index="<?php echo isset($index)?$index:0; ?>" title="">
<?php
	if (isset($edit_template)) echo $edit_template
?>
</div>
