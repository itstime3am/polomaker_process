<form id="frmSearch" controller="<?php echo $this->uri->rsegment(1); ?>" id="frmSearch" action="post">
	<table id="tblSearchPanel" class="rounded-corner">
	<thead>
		<tr>
			<th class="rounded-top-left" style="height:24px;"></th>
			<th></th>
			<th class="rounded-top-right"></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="2" class="rounded-foot-left"></td>
			<td class="rounded-foot-right">&nbsp;</td>
		</tr>
	</tfoot>
	<tbody>
<?php
	$_arrControls = array();
	$_startScript = '';
	if (isset($controls)) {
		foreach ($controls as $_ctrl) {
//var_dump($_ctrl);
		if (! is_array($_ctrl)) continue;
			$_type = array_key_exists('type', $_ctrl)?$_ctrl['type']:'txt';
			$_label = array_key_exists('label', $_ctrl)?$_ctrl['label']:'';
			$_name = array_key_exists('name', $_ctrl)?$_ctrl['name']:'';
			$_val = array_key_exists('value', $_ctrl)?$_ctrl['value']:'';
			$_class = "search-param";
			$_class .= array_key_exists('add_class', $_ctrl)?' ' . $_ctrl['add_class']:'';
			$_class .= array_key_exists('class', $_ctrl)?' ' . $_ctrl['class']:'';
			$_input_elem = '';
			switch ($_type) {
				case 'txt':
					$_input_elem = '<input type="text" id="' . $_type . '-' . $_name . '" value="' . $_val . '" class="' . $_class . '" />';
					break;
				case 'aac': //Ajax auto complete
					$_url = './' . $_name . '/json_search';
					$_sel_val = 'rowid';
					$_sel_text = 'name';
					$_min_length = 3;
					$_disp_text = $_name;
					$_on_select = <<<OSL

			, select: function(event, ui) {

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
				$('#tblSearchPanel #hdn-$_name').val(_aac_hdn_val);

OCH;
					if (array_key_exists('url', $_ctrl)) $_url = $_ctrl['url'];
					if (array_key_exists('sel_val', $_ctrl)) $_sel_val = $_ctrl['sel_val'];
					if (array_key_exists('sel_text', $_ctrl)) $_sel_text = $_ctrl['sel_text'];
					if (array_key_exists('min_length', $_ctrl) && is_int($_ctrl['min_length'])) $_min_length = (int) $_ctrl['min_length'];
					if (array_key_exists('disp_text', $_ctrl) && (! empty($_ctrl['disp_text']))) $_disp_text = $_ctrl['disp_text'];
					$_input_elem = '<input type="text" id="' . $_type . '-' . $_disp_text . '" value="' . $_val . '" class="ajax-autocomplete ' . $_class . '" />';
					$_input_elem .= '<input type="hidden" id="hdn-' . $_name . '" class="' . $_class . '" />';
					if (array_key_exists('on_select', $_ctrl)) $_on_select .= $_ctrl['on_select'];
					if (array_key_exists('on_change', $_ctrl)) $_on_change .= $_ctrl['on_change'];
					if (array_key_exists('on_changed', $_ctrl)) $_on_change .= $_ctrl['on_changed'];
					$_on_select .= "\n}";
					$_on_change .= "\n}";

					$_jqSel = '#tblSearchPanel #aac-' . $_disp_text;
					
					$_startScript .= <<<AAC

		$('$_jqSel').autocomplete({
			delay: 3,
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
							response(_resp);
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
				case 'dpk': //date picker
					$_input_elem = '<input type="text" id="txt-' . $_name . '" value="' . $_val . '" class="' . $_class . '" />';
					$_startScript .= <<<EOT
		$('#tblSearchPanel #txt-$_name').datepicker({
			showOn: "both",
			buttonImage: "public/images/select_day.png",
			buttonImageOnly: true,
			dateFormat: 'dd/mm/yy'
		});

EOT;
					break;
				case 'sel':
					$_input_elem = '<select id="' . $_type . '-' . $_name . '" class="' . $_class . '" />';
					$_sel_val = 'rowid';
					$_sel_text = 'name';
					if (array_key_exists('sel_options', $_ctrl)) {
						if (array_key_exists('sel_val', $_ctrl)) $_sel_val = $_ctrl['sel_val'];
						if (array_key_exists('sel_text', $_ctrl)) $_sel_text = $_ctrl['sel_text'];
						if (is_array($_ctrl['sel_options'])) {
							foreach ($_ctrl['sel_options'] as $_opt) {
								if (is_array($_opt)) {
									$_input_elem .= '<option value="' . $_opt[$_sel_val] . '" ';
									if (($_val != '') && ($_opt[$_sel_val] == $_val)) $_input_elem .= 'selected ';
									$_input_elem .= '>' . $_opt[$_sel_text] . '</option>';
								}
							}
						}
					}
					$_input_elem .= '</select>';
					$_options = '';
					$_on_change = '';
					if (array_key_exists('on_changed', $_ctrl)) $_on_change = "changed:" . $_ctrl['on_changed'];
					if (array_key_exists('on_change', $_ctrl)) $_on_change = "changed:" . $_ctrl['on_change'];
					if (strlen(trim($_on_change)) > 0) $_options = "{" . $_on_change . "}";
					$_startScript .= "\t\t$('#tblSearchPanel #" . $_type . '-' . $_name . "').combobox(" . $_options . ");\n";
					break;
				case 'txa':
					$_input_elem = '<textarea id="' . $_type . '-' . $_name . '" class="' . $_class . '" >' . $_val . '</textarea>';
					break;
				case 'chk':
					$_input_elem = '<input type="checkbox" id="'. $_type . '-' . $_name .'" ';
					if (!empty($_val)) $_input_elem .= 'checked';
					$_input_elem .= ' class="' . $_class . '" />';
					break;
				case 'hdn':
					$_input_elem = '<input type="hidden" id="' . $_type . '-' . $_name . '" value="' . $_val . '" class="' . $_class . '" />';
					break;
				case 'chk':
					$_input_elem = '<input type="checkbox" id="'. $_type . '-' . $_name .'" ';
					if ($_val !== '') $_input_elem .= 'checked';
					$_input_elem .= ' class="search-param" />';
					break;
				case 'rdo':
					if (array_key_exists('sel_options', $_ctrl) && array_key_exists('sel_val', $_ctrl) && array_key_exists('sel_text', $_ctrl)) {
						if (is_array($_ctrl['sel_options'])) {
							foreach ($_ctrl['sel_options'] as $_opt) {
								if ($_opt[$_ctrl['sel_text']] != '') {
									$_id = $_type . '-' . $_name . '_' . $_opt[$_ctrl['sel_val']];
									$_input_elem .= '<input type="radio" name="' . $_name . '" id="' . $_id . '" value="' . $_opt[$_ctrl['sel_val']] . '" ';
									if (($_val != '') && ($_opt[$_ctrl['sel_val']] == $_val)) $_input_elem .= 'checked ';
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
				case 'info':
					$_input_elem = '<tr><td colspan="3" style="text-align:center;"><span class="search-info">' . $_val . '</span></td></tr>';
					break;
			}
			$_arrControls[$_name] = array($_label, $_input_elem, $_type);
/*
			if ($_type == 'hdn' || $_type == 'info') {
				echo $_input_elem;
			} else {
				echo <<<EOT
		<tr>
			<td></td>
			<td class="table-title">$_label :</td>
			<td>
				$_input_elem
			</td>
			<td></td>
		</tr>
EOT;
			}
*/
		}
		$_display = '';
		$_arrEditPanelDataKey = array();
		if (isset($layout) && is_array($layout)) {
			$_display .= _getLayoutItemDisplay($layout, $_arrControls, $_arrEditPanelDataKey);
		}
		foreach ($_arrControls as $_item) { //left over from layout (if passed, otherwise generate all)
			if (($_item[0] == 'hidden') || ($_item[2] == 'info')) {
				$_display .= $_item[1];
			} elseif ($_item[2] == 'chk') {
							$_display .= <<<EOT
		<tr>
			<td class="table-value table-value-checkbox">{$_item[1]}</td>
			<td class="table-title table-title-checkbox" colspan="2">{$_item[0]}</td>
		</tr>
EOT;
			} else {
				$_display .= <<<EOT
		<tr>
			<td class="table-title">{$_item[0]} :</td>
			<td class="table-value" colspan="2">{$_item[1]}</td>
		</tr>
EOT;
			}
		}
		echo $_display;
	}
?>
		<tr>
			<td colspan="3" style="height:20px;"></td>
		</tr>
		<tr>
			<td colspan="3" style="text-align:center;"><input type="button" id="btnSearch" value="ค้นหา" class="clsFormButton" /> <input type="reset" id="btnReset" value="ล้าง" class="clsFormButton" /></td>
		</tr>
	</tbody>
	</table>
</form>
<br style="clear:both" />
<div id="dialog-modal" title="">
</div>
<script language="javascript">
<?php
	if (isset($search_onload) && ($search_onload == FALSE)) {
		echo "var _autoSearch_OnLoad = false;\n";
	} else {
		echo "var _autoSearch_OnLoad = true;\n";
	}
?>
	$(function() {
<?php
	echo $_startScript;
	echo "\n";
	if (isset($add_script)) {
		echo "\t\t" . $add_script . "\n";
	}
?>
	});
</script>