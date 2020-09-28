<div id="divInfo" class="cls-div-info" index="<?php echo isset($index)?$index:0; ?>" ></div>
<ul class="ul-vldr-error-msg" index="<?php echo isset($index)?$index:0; ?>" ></ul>
<form id="frm_edit" controller="<?php echo isset($crud_controller)?$crud_controller:$this->uri->rsegment(1); ?>" class="cls-frm-edit" index="<?php echo isset($index)?$index:0; ?>">
	<table id="tbl_edit" class="rounded-corner cls-tbl-edit" autofocus >
	<!-- to prevent focus on first input that cause problem when have scroll bar (back to top after blur lower elements) -->
	<thead>
		<tr>
			<th class="rounded-top-left" style="width:30%"></th>
			<th style="width:30%">&nbsp;</th>
			<th class="rounded-top-right" style="width:40%"></th>
		</tr>
	</thead>
	<!-- tfoot>
		<tr>
		<td colspan="2" class="rounded-foot-left"></td>
		<td class="rounded-foot-right">&nbsp;</td>
		</tr>
	</tfoot -->
	<tbody>
<?php
	$_arrControls = array();
	$_startScript = '';
	$_strFormSelector = ".cls-frm-edit[index=" . (isset($index)?$index:0) . "]";
	if (isset($controls)) {
		if (! is_array($controls)) return;
		//++ Prepare controls elements 
		foreach ($controls as $_ctrl) {
			if (! is_array($_ctrl)) continue;
			$_type = array_key_exists('type', $_ctrl)?$_ctrl['type']:'txt';
			$_label = array_key_exists('label', $_ctrl)?$_ctrl['label']:'';
			$_name = array_key_exists('name', $_ctrl)?$_ctrl['name']:'';
			$_val = array_key_exists('value', $_ctrl)?$_ctrl['value']:'';
			$_class = 'user-input';
			$_class .= array_key_exists('add_class', $_ctrl)?' ' . $_ctrl['add_class']:'';
			$_class .= array_key_exists('class', $_ctrl)?' ' . $_ctrl['class']:'';
			$_maxlength = '';
			$_size = '';
			$_specStyle = '';
			if (array_key_exists('readonly', $_ctrl)) $_specStyle = 'readonly';
			if (array_key_exists('maxlength', $_ctrl)) {
				$_set_length = intval($_ctrl['maxlength']);
				if ($_set_length > 0) {
					$_maxlength = ' maxlength="' . $_set_length . '"';
					if (($_type == 'txt') || ($_type == 'sel')) {
						$_size = ' size="' . $_set_length . '"';
					}
				}
			}
			if (array_key_exists('add_attr', $_ctrl)) $_specStyle .= ' ' . $_ctrl['add_attr'];
			$_input_elem = '';
			switch ($_type) {
				case 'txt':
					$_input_elem = '<input type="text" id="' . $_type . '-' . $_name . '" value="' . $_val . '" class="' . $_class . '"' . $_maxlength . $_size . $_specStyle . ' />';
					break;
				case 'dpk': //date picker
					$_input_elem = '<input type="text" id="txt-' . $_name . '" value="' . $_val . '" class="' . $_class . '"' . $_maxlength . $_size . $_specStyle . ' />';
					$_startScript .= <<<EOT
		$('#frm_edit #txt-$_name').datepicker({
			showOn: "both",
			buttonImage: "public/images/select_day.png",
			buttonImageOnly: true,
			dateFormat: 'dd/mm/yy'
		});
EOT;
					break;
				case 'sel':
					$_input_elem = '<select id="' . $_type . '-' . $_name . '" class="' . $_class . '"' . $_maxlength . $_size . $_specStyle . ' />';
					$_arrAttr = array();
					if (array_key_exists('sel_attr', $_ctrl) && is_array($_ctrl['sel_attr'])) $_arrAttr = $_ctrl['sel_attr'];
					if (array_key_exists('sel_options', $_ctrl) && array_key_exists('sel_val', $_ctrl) && array_key_exists('sel_text', $_ctrl)) {
						if (is_array($_ctrl['sel_options'])) {
							foreach ($_ctrl['sel_options'] as $_opt) {
								$_input_elem .= '<option value="' . $_opt[$_ctrl['sel_val']] . '" ';
								if (($_val != '') && ($_opt[$_ctrl['sel_val']] == $_val)) $_input_elem .= 'selected ';
								foreach ($_arrAttr as $_key=>$_val) {
									if (array_key_exists($_val, $_opt)) $_input_elem .= $_key . '="' . $_opt[$_val] . '" ';
								}
								$_input_elem .= '>' . $_opt[$_ctrl['sel_text']] . '</option>';
							}
						}
					}
					$_input_elem .= '</select>';
					$_jqSel = $_strFormSelector . " #" . $_type . '-' . $_name;
					$_startScript .= <<<EOS
		$('$_jqSel').combobox({changed: function(str, ev, ui) {

EOS;
					if (array_key_exists('hidden_name', $_ctrl)) {
						$_input_elem .= '<input type="hidden" id="hdn-' . $_ctrl['hidden_name'] . '" class="user-input" />';
						$_startScript .= "\t\t$('" . $_strFormSelector . " #hdn-" . $_ctrl['hidden_name'] . "').val(str);\n";
					}
					if (array_key_exists('on_change', $_ctrl)) {
						$_startScript .= $_ctrl['on_change'];
					}
					if (array_key_exists('on_changed', $_ctrl)) {
						$_startScript .= $_ctrl['on_changed'];
					}
		$_startScript .= <<<EOS
			return false;
		}});
EOS;
					break;
				case 'aac': //Ajax auto complete
					$_url = './' . $_name . '/json_search';
					$_sel_val = 'rowid';
					$_sel_text = 'name';
					$_min_length = 3;
					$_disp_text = $_name;
					$_on_select = <<<OSL

			, select: function(event, ui) {
				var _aac_text = '';
				var _aac_hdn_val = '';
				if (ui.item) {
					_aac_text = ui.item.value || '';
					_aac_hdn_val = ui.item.hdn_value;
					_aac_text = _aac_text.toString().trim();
					_aac_hdn_val = _aac_hdn_val.toString().trim();
				}
				$('#frm_edit #hdn-$_name').val(_aac_hdn_val);

OSL;
					$_on_change = <<<OCH

			, change: function(event, ui) {
				var _aac_text = '';
				var _aac_hdn_val = '';
				if (ui.item) {
					_aac_text = ui.item.value || '';
					_aac_hdn_val = ui.item.hdn_value;
					_aac_text = _aac_text.toString().trim();
					_aac_hdn_val = _aac_hdn_val.toString().trim();
				}
				$('#frm_edit #hdn-$_name').val(_aac_hdn_val);

OCH;
					if (array_key_exists('url', $_ctrl)) $_url = $_ctrl['url'];
					if (array_key_exists('sel_val', $_ctrl)) $_sel_val = $_ctrl['sel_val'];
					if (array_key_exists('sel_text', $_ctrl)) $_sel_text = $_ctrl['sel_text'];
					if (array_key_exists('min_length', $_ctrl) && is_int($_ctrl['min_length'])) $_min_length = (int) $_ctrl['min_length'];
					if (array_key_exists('disp_text', $_ctrl) && (! empty($_ctrl['disp_text']))) $_disp_text = $_ctrl['disp_text'];
					$_input_elem = '<input type="text" id="' . $_type . '-' . $_disp_text . '" value="' . $_val . '" class="ajax-autocomplete ' . $_class . '" />';
					$_input_elem .= '<input type="hidden" id="hdn-' . $_name . '" class="' . $_class . '" />';
					if (array_key_exists('on_select', $_ctrl)) $_on_select .= $_ctrl['on_select'];
					$_on_select .= "\n}";
					if (array_key_exists('on_change', $_ctrl)) $_on_change .= $_ctrl['on_change'];
					if (array_key_exists('on_changed', $_ctrl)) $_on_change .= $_ctrl['on_changed'];
					$_on_change .= "\n}";

					$_jqSel = '#frm_edit #aac-' . $_disp_text;
					$_startScript .= <<<AAC

		$('$_jqSel').autocomplete({
			delay: 500,
			source: function( request, response ) {
				$.ajax({
					dataType: "json"
					, type : 'POST'
					, data:{
						"$_sel_text": request.term
					}
					, url: '$_url'
					, success: function(data) {
						$('$_jqSel').removeClass('ui-autocomplete-loading');
						if ((data.success == true) && (data.data.length > 0)) {
							var data = data.data;
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
							var _resp = $.map( data, function(item) {
									_text = item.$_sel_text;
									if ( !request.term || matcher.test(_text) ) return {
											label: _text.replace(
												new RegExp(
													"(?![^&;]+;)(?!<[^<>]*)(" +
													$.ui.autocomplete.escapeRegex(request.term) +
													")(?![^<>]*>)(?![^&;]+;)", "gi"
												), "<strong>$1</strong>" ),
											value: _text,
											hdn_value: item.$_sel_val
										};
								})
							if (_resp.length > 0) _resp.unshift({label:"&nbsp;", value:"", hdn_value:""});
							response(_resp.slice(0, 300));
						}
					}
					, error: function(data) {
						$('$_jqSel').removeClass('ui-autocomplete-loading');  
					}
				});
			}
			, minLength: $_min_length
$_on_select
$_on_change
		});

AAC;
/*
			, open: function() {
			}
			, close: function() {
			}
			, focus:function(event,ui) {
			}
*/
					break;
				case 'txa':
					$_rows = (array_key_exists('rows', $_ctrl) && is_numeric($_ctrl['rows'])) ? intval($_ctrl['rows']) : 3;
					$_input_elem = '<textarea id="' . $_type . '-' . $_name . '" class="' . $_class . '" rows="' . $_rows . '"' . $_maxlength . $_size . $_specStyle . '>' . $_val . '</textarea>';
					break;
				case 'hdn':
					$_label = 'hidden';
					$_input_elem = '<input type="hidden" id="' . $_type . '-' . $_name . '" value="' . $_val . '" class="' . $_class . '" ' . $_specStyle . ' />';
					break;
				case 'chk':
					$_input_elem = '<input type="checkbox" id="'. $_type . '-' . $_name .'"';
					switch (strtolower(trim($_val))) {
						case '':
							break;
						case '1':
						case 'true':
						case 'on':
							$_input_elem .= ' checked="checked"';
							break;
						default:
							$_input_elem .= ' value="' . trim($_val) . '"';
							break;
					}
					$_input_elem .= ' class="' . $_class . '" ' . $_specStyle . ' />';
					break;
				case 'rdo':
					if (array_key_exists('sel_options', $_ctrl) && array_key_exists('sel_val', $_ctrl) && array_key_exists('sel_text', $_ctrl)) {
						if (is_array($_ctrl['sel_options'])) {
							foreach ($_ctrl['sel_options'] as $_opt) {
								if ($_opt[$_ctrl['sel_text']] != '') {
									$_id = $_type . '-' . $_name . '_' . $_opt[$_ctrl['sel_val']];
									$_input_elem .= '<input type="radio" name="' . $_name . '" id="' . $_id . '" value="' . $_opt[$_ctrl['sel_val']] . '" ';
									if (($_val != '') && ($_opt[$_ctrl['sel_val']] == $_val)) $_input_elem .= 'checked="checked" ';
									$_input_elem .= 'name="' . $_type . '-' . $_name . '" class="' . $_class . '" >';
									$_input_elem .= '<label class="cls-radio-label" for="' . $_id . '" >' . $_opt[$_ctrl['sel_text']] . '</label>';
								}
							}
						}
					}
					break;
				case 'spn':
					$_input_elem = '<span id="' . $_type . '-' . $_name . '" class="' . $_class . ' no-validate" ' . $_maxlength . $_size . $_specStyle . '>' . trim($_val) . '</span>';
					break;
				case 'div':
					$_html = trim($_val);
					if (array_key_exists('html', $_ctrl)) $_html = $_ctrl['html'];
					$_input_elem = '<div id="' . $_type . '-' . $_name . '" class="' . $_class . ' no-validate" ' . $_maxlength . $_size . $_specStyle . '>' . $_html . '</div>';
					break;
			}
			if (strpos($_class, 'set-disabled')) {
				$_dummyId = '';
				if (($_type == 'dpk') || ($_type == 'mpk')) {
					$_dummyId = 'txt-' . $_name;
				} else {
					$_dummyId = $_type . '-' . $_name;
				}
				$_startScript .= "\t\t_setEnableElem($('#frm_edit #" . $_dummyId . "'), false);\n";
			}
			$_arrControls[$_name] = array($_label, $_input_elem, $_type);
		}

		$_display = '';
		$_arrEditPanelDataKey = array();
		if (isset($layout) && is_array($layout)) {
			$_display .= _getLayoutItemDisplay($layout, $_arrControls, $_arrEditPanelDataKey);
		}
		foreach ($_arrControls as $_item) { //left over from layout (if passed, otherwise generate all)
			if ($_item[0] == 'hidden') {
				$_display .= $_item[1];
			} elseif ($_item[2] == 'chk') {
							$_display .= <<<EOT
		<tr>
			<td class="table-value table-value-checkbox">
				$_item[1]
			</td>
			<td class="table-title table-title-checkbox">$_item[0] :</td>
			<td></td>
		</tr>
EOT;
			} else {
				$_display .= <<<EOT
		<tr>
			<td class="table-title">$_item[0] :</td>
			<td class="table-value">
				$_item[1]
			</td>
			<td></td>
		</tr>
EOT;
			}
		}
		echo $_display;

		$_strArrEditPanelData = '';
		foreach($_arrEditPanelDataKey as $_key) {
			$_strArrEditPanelData .= '"' . $_key . '":"",';
		}
		if (strlen($_strArrEditPanelData) > 0) $_strArrEditPanelData = substr($_strArrEditPanelData, 0, -1);

/*
		//-- Prepare controls elements 
		$_display = '<tr><td colspan="3" class="td-align-center"><div class="form-edit-elem-container">';
		if (isset($layout)) {
			if (is_array($layout)) {
				$_display .= _getLayoutItemDisplay($layout, $_arrControls);
			}
		}
		$_display .= '</div></td></tr>';
		echo $_display;
		
		foreach ($_arrControls as $_item) { //left over from layout
			if ($_item[0] == 'hidden') {
				echo $_item[1];
			} elseif ($_item[2] == 'chk') {
				echo <<<EOT
		<tr>
			<td class="table-value table-value-checkbox">
				$_item[1]
			</td>
			<td class="table-title table-title-checkbox">$_item[0] :</td>
			<td></td>
		</tr>
EOT;
			} else {
				echo <<<EOT
		<tr>
			<td class="table-title">$_item[0] :</td>
			<td class="table-value">
				$_item[1]
			</td>
			<td></td>
		</tr>
EOT;
			}
		}
*/
	}

?>
<?php if (! (isset($_IS_NO_BUTTONS) && ($_IS_NO_BUTTONS == True))): ?>
		<tr>
			<td colspan="3" class="td-align-center">
				<input type="button" id="btnFormSubmit" class="cls-btn-form-submit" value="บันทึก"/><input type="button" id="btnFormReset" class="cls-btn-form-reset" value="ค่าเริ่มต้น" /><input type="button" id="btnFormCancel"  class="cls-btn-form-cancel"value="ยกเลิก"/>
			</td>
		</tr>
<?php endif; ?>
	</tbody>
	<tfoot>
		<tr>
		<td colspan="2" class="rounded-foot-left"></td>
		<td class="rounded-foot-right">&nbsp;</td>
		</tr>
	</tfoot>
	</table>
</form>
<br style="clear:both" />
<div id="sublist"><?php echo isset($sublist)?$sublist:''; ?></div>
<script language="javascript">
<?php
	if (strlen($_strArrEditPanelData) > 0) {
		echo "\tvar _objEditPanelData = {" . $_strArrEditPanelData . "};\n";
	}
?>
	$(function() {
<?php
	echo $_startScript;
?>
	});
</script>