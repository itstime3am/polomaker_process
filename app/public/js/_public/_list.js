	var _currEditTr, _currEditData;
	var _DT_VERSION = $.fn.dataTable.version;
	var _DT_BASE_OPTIONS;

	$(function() {
		$('#divPanelHandler').click(function() {
			doToggleLeftPanel();
		});
		$(".cls-div-form-edit-dialog").dialog({
			height:600,
			width:'90%',
			show: {effect:"puff",duration: 1000},
			hide: {effect:"fade",duration:1000},
			resizable:true,
			modal:true,
			closeOnEscape:false,
			autoOpen:false,
			open: function () {
				var _frm = $(this).find(".cls-frm-edit").get(0);
				if (typeof _doCommitUserControlsChanged == 'function') _doCommitUserControlsChanged(_frm);
				$(_frm).trigger('edit_form_loaded');
			}
		});
		$("#divSelectableFields").dialog({
			height:150,
			width:800,
			show: {effect:"flip",duration:1000},
			hide: {effect:"fade",duration: 1000},
			title: MSG_DLG_TITLE_SELECT_FIELDS,
			resizable:false,
			modal:true,
			closeOnEscape:true,
			autoOpen:false,
			open: function () {
				if (!_objDataTable) return false;
				var _bVis = false;
				for (i = 0;i < _objDataTable.fnSettings().aoColumns.length;i++) {
					_bVis = _objDataTable.fnSettings().aoColumns[i].bVisible;
					$( "#li_col_" + i ).prop("checked", _bVis);
				}
			},
			buttons: {
				'Apply': function() {
					if (! _objDataTable) return false;
					$( "#divSelectableFields ul li input[type='checkbox']" ).each(function () {
						_idx = $( this ).prop('id').substr(7) || 0;
						_objDataTable.fnSetColumnVis(_idx, $(this).prop('checked'));
					});
					$(this).dialog('close');
				},
				'Cancel': function() {
					$(this).dialog('close');
				}
			}
		});
		
		$('#frm_edit').on('click', '[command=link]', function() {
			if ($(this).hasClass('link-disabled')) return false;
			var _target = $(this).attr('target') || 'new';
			var _href = $(this).attr('href') || '';
			if (_href != '') {
				if (typeof _objEditPanelData == 'object') {
					var _match;
					var _Regexp = /\{([^\}]+)\}/g;
					var _newHref = _href;
					_match = _Regexp.exec(_href);
					while (_match != null) {
						if (_match[1] in _objEditPanelData) _newHref = _newHref.replace(_match[0], _objEditPanelData[_match[1]]);
						_match = _Regexp.exec(_href);
					}
					_href = _newHref;
				}
				if (_target == 'new') {
					window.open(_href);
				} else if (_target == 'self') {
					window.location.href = _href;
				}
			}
		});
		$('.cls-div-list').on('click', "table tbody td img",
			function () {
				_doCommandClick(this);
			}
		);
		$(".cls-btn-form-submit").button().on('click', 
			function() {
				var _frm = $(this).parents('form').get(0);
				if (blnDataChanged(_frm)) {
					if (blnValidateContainer(false, _frm, '.user-input')) {
						doSubmit(_frm);
					}
				} else {
					alert(MSG_ALERT_COMMIT_NO_CHANGE);
				}
			}
		);
		$(".cls-btn-form-cancel").button().on('click', 
			function () {
				var _frm = $(this).parents('form').get(0);
				var _divDialog = $(this).parents(".cls-div-form-edit-dialog").get(0);
				if ($(_frm).find(".cls-btn-form-submit").css('display') == 'none') { //View
					$(_divDialog).dialog( "close" );
				} else {
					if (blnDataChanged(_frm)) {
						if (confirm(MSG_CONFIRM_CANCEL_EDITED_PANEL)) $(_divDialog).dialog( "close" );
					} else {
						$(_divDialog).dialog( "close" );
					}
				}
			}
		);
		$(".cls-btn-form-reset").button().on('click', 
			function () {
				var _frm = $(this).parents('form').get(0);
				if (blnDataChanged(_frm)) {
					var _divDialog = $(this).parents(".cls-div-form-edit-dialog").get(0);
					if (confirm(MSG_CONFIRM_CANCEL_EDITED_PANEL)) doEdit("", "", _divDialog);
				} else {
					alert(MSG_ALERT_COMMIT_NO_CHANGE);
				}
			}
		);
	});
	
	function _doCommandClick(img) {
		var _command = $( img ).attr('command');
		var _tr = $( img ).parents('tr')[0];
		var _index = $($( img ).parents('.cls-div-list')[0]).attr("index");
		var _divEditDlg = $(".cls-div-form-edit-dialog[index=" + _index + "]");
		var _datatable = $( img ).parents('.cls-tbl-list')[0];
		var _aData = $(_datatable).dataTable().fnGetData(_tr);
		if (_aData) {
			switch (_command) {
				case 'link':
					var _target = $(img).attr('target') || 'new';
					var _href = $(img).attr('href') || '';
					if (_href !== '') {
						if (_target == 'new') {
							window.open(_href);
						} else if (_target == 'self') {
							window.location.href = _href;
						}
					}
					break;
				case 'view':
					doView(_aData, _divEditDlg);
					break;
				case 'edit':
					doEdit(_aData, _tr, _divEditDlg);
					break;
				case 'delete':
					if (confirm(MSG_CONFIRM_DELETE_ROW.replace('v_XX_1', ''))) doDelete(_aData, _tr, _divEditDlg);
					break;
				case 'cancel':
					if (confirm(MSG_CONFIRM_CANCEL_ROW.replace('v_XX_1', ''))) doCancel(_aData, _tr, _divEditDlg);
					break;
				default:
					if ((_command in window) && ($.isFunction(eval(_command)))) {
						var _strParams = $( img ).attr('params') || '';
						if (_strParams.trim().length > 0) {
							_strParams = _strParams.trim();
							if (_strParams.substr(0, 1) != '[') _strParams = '[' + _strParams;
							if (_strParams.substr(-1) != ']') _strParams = _strParams + ']';
						}
						var _params = eval(_strParams) || [];
						eval(_command).apply(img, _params);
					} else if (('customCommand' in window) && ($.isFunction(customCommand))) {
						if ($.isFunction(customCommand)) customCommand.apply(this, [_command, _aData, _tr, _divEditDlg]);
					}
					break;
			}
		}
	}
	
	function doInsert(divEditDlg) {
		_currEditTr = undefined;
		_currEditData = undefined;
		var _frm = $(divEditDlg).find(".cls-frm-edit").get(0);
		if (!$(divEditDlg).dialog( "isOpen" )) $(divEditDlg).dialog('option', 'title', MSG_DLG_TITLE_INSERT_FORM.replace(/v_XX_1/g, '')).dialog( "open" );
		_doClearForm(_frm);
	}
	function _doCommitUserControlsChanged(form) {
		_currEditData = {};
		$(form).find(".user-input:not(.no-commit), .data-container[data]:not(.no-commit)").each(function () {
			var _data = (_getElemData(this) || '').trim();
			var _val = _getElemValue(this, false);
			if (_val) {
				if (_data.indexOf('[]') >= 0) {
					_data = _data.replace('[]', '');
					if (_data in _currEditData) {
						_currEditData[_data] += _val + ',';
					} else {
						_currEditData[_data] = ',' + _val + ',';						
					}
				} else {
					if ((_data) && (_val)) _currEditData[_data] = _val;
				}
			}
		});
		return $.extend(true, {}, _currEditData);
	}
	function doView(dataRowObj, divEditDlg) {
		var _frm = $(divEditDlg).find(".cls-frm-edit").get(0);
		_doClearForm(_frm);
		if ((dataRowObj) && (_frm)) {
			if ($(divEditDlg).find('.cls-div-sub-list').length > 0) {
				$(divEditDlg).find('.cls-div-sub-list').each(function(ind, obj) {
					var _ea = $(this);
					var _arrSearchParams = {};
					var _dummy1 = _ea.attr('master_cols');
					var _dummy2 = _ea.attr('map_cols');
					if ((_dummy1.trim().length > 0) && (_dummy2.trim().length > 0)) {
						var _arr1 = _dummy1.split(',');
						var _arr2 = _dummy2.split(',');
						if ((_arr1.length > 0) && (_arr2.length > 0)) {
							for (var _i=0;_i<_arr2.length;_i++) {
								if ((_arr1.length > _i) && (_arr2.length > _i) && (_arr1[_i].trim().length > 0)) {
									var _str = _arr1[_i].trim();
									if (_str in dataRowObj) _arrSearchParams[_arr2[_i].trim()] = dataRowObj[_str];
								}
							}
						}
						var _strSearch = JSON.stringify(_arrSearchParams);
						$('.cls-div-sub-list').attr('main-search', _strSearch);
						if (typeof populateSublist == 'function') populateSublist(false, _arrSearchParams);
					}
				});
			}
			_doSetEnableFormUserInput(_frm, false);
			_doSetValueFormUserInput(_frm, dataRowObj);

			//++ prepare data value for special link
			if (typeof _objEditPanelData == 'object') {
				for (_key in _objEditPanelData) {
					if (_key in dataRowObj) {
						$(_frm).find('[href*="{' + _key + '}"]').each(function() { $(this).removeClass('link-disabled'); });
						_objEditPanelData[_key] = dataRowObj[_key];
					}
				}
			}
			$('.eventView-hide', _frm).addClass('hidden');
			$('.eventView-invis', _frm).addClass('invisible');

			//-- prepare data value for special link
			$(_frm).find(".cls-btn-form-submit").css('display', 'none');
			$(_frm).find(".cls-btn-form-reset").css('display', 'none');
			if (!$(divEditDlg).dialog( "isOpen" )) $(divEditDlg).dialog('option', 'title', MSG_DLG_TITLE_VIEW_FORM.replace(/v_XX_1/g, ' ( rowid ' + dataRowObj['rowid'] + ' )')).dialog( "open" );
		}
	}

	function doEdit(dataRowObj, trObj, divEditDlg) {
		var _frm = $(divEditDlg).find(".cls-frm-edit").get(0);
		_doClearForm(_frm);
		if (dataRowObj && _frm) {
			_currEditData = undefined;
			_currEditTr = trObj;
			_currEditData = dataRowObj;
		}
		if (_currEditData && _frm) {
			if ($(divEditDlg).find('.cls-div-sub-list').length > 0) {
				$(divEditDlg).find('.cls-div-sub-list').each(function(ind, obj) {
					var _ea = $(this);
					var _arrSearchParams = {};
					var _dummy1 = _ea.attr('master_cols');
					var _dummy2 = _ea.attr('map_cols');
					if ((_dummy1.trim().length > 0) && (_dummy2.trim().length > 0)) {
						var _arr1 = _dummy1.split(',');
						var _arr2 = _dummy2.split(',');
						if ((_arr1.length > 0) && (_arr2.length > 0)) {
							for (var _i=0;_i<_arr2.length;_i++) {
								if ((_arr1.length > _i) && (_arr2.length > _i) && (_arr1[_i].trim().length > 0)) {
									var _str = _arr1[_i].trim();
									if (_str in dataRowObj) _arrSearchParams[_arr2[_i].trim()] = dataRowObj[_str];
								}
							}
						}
						var _strSearch = JSON.stringify(_arrSearchParams);
						_ea.attr('main-search', _strSearch);
						if (typeof populateSublist == 'function') populateSublist(true, _arrSearchParams);
					}
				});
			}
			_doSetEnableFormUserInput(_frm, true);
			_doSetValueFormUserInput(_frm, dataRowObj);

			//++ prepare data value for special link
			if (typeof _objEditPanelData == 'object') {
				for (_key in _objEditPanelData) {
					if (_key in dataRowObj) {
						$(_frm).find('[href*="{' + _key + '}"]').each(function() { $(this).removeClass('link-disabled'); });
						_objEditPanelData[_key] = dataRowObj[_key];
					}
				}
			}

			$('.eventEdit-hide', _frm).addClass('hidden');
			$('.eventEdit-invis', _frm).addClass('invisible');

			if (!$(divEditDlg).dialog( "isOpen" )) $(divEditDlg).dialog('option', 'title', MSG_DLG_TITLE_EDIT_FORM.replace(/v_XX_1/g, ' ( rowid ' + _currEditData['rowid'] + ' )')).dialog( "open" );
		}
	}
	function doDelete(dataRowObj, trObj, divEditDlg, opt_fncCallback) {
		$("#dialog-modal").html("<p>" + MSG_DLG_HTML_DELETE + "</p>");
		$("#dialog-modal").dialog('option', 'title', MSG_DLG_TITLE_DELETE);
		$("#dialog-modal").dialog( "open" );
		var _frm = $(divEditDlg).find(".cls-frm-edit").get(0);
		var _index = $(divEditDlg).attr('index');
		$.ajax({
			type:"POST",
			url:"./" + $(_frm).attr("controller") + "/delete",
			data: 'rowid=' + dataRowObj['rowid'],
			success: function(data, textStatus, jqXHR) {
				if (data.success == false) {
					alert(MSG_ALERT_DELETE_FAILED.replace(/v_XX_1/g, data.error));
					$("#dialog-modal").dialog( "close" );
				} else {
					if (trObj) {
						_datatable = $( trObj ).parents('.cls-tbl-list')[0];
						$(_datatable).dataTable().fnDeleteRow(trObj);					
					}
					alert(MSG_ALERT_DELETE_SUCCESS.replace(/v_XX_1/g, ''));
					$("#dialog-modal").dialog( "close" );
				}
				if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				doDisplayInfo(textStatus + ' : ' + errorThrown, "ErrorMessage", _index);
				$("#dialog-modal").dialog( "close" );
				if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
			},
			statusCode: {
				404: function() {
					doDisplayInfo("Page not found", "ErrorMessage", _index);
					$("#dialog-modal").dialog( "close" );
					if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
				}
			}
		});
	}
	function doCancel(dataRowObj, trObj, divEditDlg, opt_fncCallback) {
		$("#dialog-modal").html("<p>" + MSG_DLG_HTML_CANCEL + "</p>");
		$("#dialog-modal").dialog('option', 'title', MSG_DLG_TITLE_CANCEL);
		$("#dialog-modal").dialog( "open" );
		var _frm = $(divEditDlg).find(".cls-frm-edit").get(0);
		var _index = $(divEditDlg).attr('index');
		$.ajax({
			type:"POST",
			url:"./" + $(_frm).attr("controller") + "/cancal",
			data: 'rowid=' + dataRowObj['rowid'],
			success: function(data, textStatus, jqXHR) {
				if (data.success == false) {
					alert(MSG_ALERT_CANCEL_FAILED.replace(/v_XX_1/g, data.error));
					$("#dialog-modal").dialog("close");
				} else {
					if (trObj) {
						_datatable = $(trObj).parents('.cls-tbl-list')[0];
						$(_datatable).dataTable().fnDeleteRow(trObj);					
					}
					alert(MSG_ALERT_CANCEL_SUCCESS.replace(/v_XX_1/g, ''));
					$("#dialog-modal").dialog("close");
				}
				if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				doDisplayInfo(textStatus + ': ' + errorThrown, "ErrorMessage", _index);
				$("#dialog-modal").dialog("close");
				if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
			},
			statusCode: {
				404: function() {
					doDisplayInfo("Page not found", "ErrorMessage", _index);
					$("#dialog-modal").dialog( "close" );
					if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
				}
			}
		});
	}
	function doSubmit(form, opt_fncCallback) {
		_index = $(form).attr('index') || 0;
		if (blnDataChanged(form) == false) {
			alert(MSG_ALERT_COMMIT_NO_CHANGE);
			return false;
		}

		$("#dialog-modal").html("<p>" + MSG_DLG_HTML_COMMIT + "</p>");
		$("#dialog-modal").dialog('option', 'title', MSG_DLG_TITLE_COMMIT);
		$("#dialog-modal").dialog( "open" );

		var _update = {};
		if (_currEditData !== undefined) _update = $.extend(true, {}, _currEditData);

		_doCommitUserControlsChanged(form);

		if (_currEditData !== undefined) _update = $.extend(true, _update, _currEditData);

		//++add master link (foreign key) if master details style
		if (typeof _masterLink != 'undefined') {
			for (_key in _masterLink) {
				if (_key in _update) {
					_update[_key] = _masterLink[_key];
				}
			}
		}
		//--add master link (foreign key) if master details style
		_str = JSON.stringify(_update);
		$.ajax({
			type:"POST",
			url:"./" + $(form).attr("controller") + "/commit",
			contentType:"application/json;charset=utf-8",
			dataType:"json",
			data:_str,
			success: function(data, textStatus, jqXHR) {
				if (data.success == false) {
					alert(MSG_ALERT_COMMIT_FAILED.replace(/v_XX_1/g, data.error));
					$("#dialog-modal").dialog( "close" );
				} else {
					_currEditTr = undefined;
					_currEditData = undefined;
					_doClearForm(form);
					var _divDialog = $(form).parents(".cls-div-form-edit-dialog").get(0);
					if (typeof _divDialog != 'undefined') {
						if ($(_divDialog).attr("id").indexOf("Sublist") >= 0) {
							if (typeof populateSublist == 'function') populateSublist(true, null, opt_fncCallback);
						} else {
							if (typeof doSearch == 'function') doSearch(false, opt_fncCallback);
						}
						if ($(_divDialog).dialog( "isOpen" )) $(_divDialog).dialog( "close" );
					}
					alert(MSG_ALERT_COMMIT_SUCCESS.replace(/v_XX_1/g, ''));
					$("#dialog-modal").dialog( "close" );
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				doDisplayInfo(textStatus + ' : ' + errorThrown, "ErrorMessage", _index);
				if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
				$("#dialog-modal").dialog( "close" );
			},
			statusCode: {
				404: function() {
					doDisplayInfo("Page not found", "ErrorMessage", _index);
					if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
					$("#dialog-modal").dialog( "close" );
				}
			}
		});
	}

	var _leftPanelHide = 0;
	var _leftPanelShow = 290;
	function doToggleLeftPanel(blnDisplay) {
		var _blnToDisplay = (typeof(blnDisplay) === 'boolean') ? blnDisplay : ($('#left_panel').css('display') == 'none');
		if (_blnToDisplay) {
			$('#left_panel').css('display', 'block');
			$('#work_panel').css('margin-left', (_leftPanelShow+10) + 'px');
			$('#divPanelHandler').css('left', _leftPanelShow + 'px');
		} else {
			$('#left_panel').css('display', 'none');
			$('#work_panel').css('margin-left', (_leftPanelHide+10) + 'px');
			$('#divPanelHandler').css('left', _leftPanelHide + 'px');
		}
		if (_objDataTable) _objDataTable.fnDraw();
	}
	
	if (_DT_VERSION.substring(0, 4) == '1.9.') {
		_DT_BASE_OPTIONS = {
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bDeferRender": true,
			"bAutoWidth": false,
			"sDom": "<'row-fluid'<'span6'T><'span6'lf>r>t<'row-fluid'<'span6'i><'span6'p>><'clear'><'span6'T>",
			"aaSorting":[],
			"sScrollY": "85%",
			"sScrollX": "95%",
			"sScrollXInner": "100%",
			"bScrollCollapse": true, 
			"aLengthMenu": [[15, 25, 35, 50, -1], [15, 25, 35, 50, "all"]],
			"iDisplayLength": 15,
			"bStateSave": true,
			"fnStateLoadParams": function (oSettings, oaData) {
				if ((typeof _blnChangeSearchCriteria == 'boolean') && (_blnChangeSearchCriteria)) { //Destroy state saving if requery
					_blnChangeSearchCriteria = false;
					return false;
				}
			}
			/*
			"aoColumns": _aoColumns,
			"aaData": arrData,
			"oTableTools": {
				"aButtons": (_tableToolButtons != undefined)?_tableToolButtons:[],
				"sSwfPath": "public/js/jquery/dataTable/TableTools/2.1.5/swf/copy_csv_xls_pdf.swf"
			}
			*/
		 };
	} else {
		_DT_BASE_OPTIONS = {
			"jQueryUI": true
			,"deferRender": true
			,"autoWidth": false
			,"processing": true
			,"info": true
			,"searching": true
			,"searchDelay": 500
			,"ordering": true
			,"order": []
			,"scrollCollapse": true
			,"paging": true
			,"pagingType": "full_numbers"
			,"lengthMenu": [[15, 25, 35, 50, -1], [15, 25, 35, 50, "all"]]
			//,"pageLength": -1
			,"language": {"url": "public/js/jquery/dataTable/dataTables.thai.lang"}
			,"dom": '<"ui-toolbar ui-widget-header ui-helper-clearfix ui-corner-tl ui-corner-tr"Bf>t<"ui-toolbar ui-widget-header ui-helper-clearfix"ip><"ui-toolbar ui-widget-header ui-helper-clearfix ui-corner-bl ui-corner-br"B>'
			,"fixedHeader": true
			,"colReorder": true
			,"stateSave": true
			//,buttons: (_tableToolButtons != undefined)?_tableToolButtons:[] //['copy', 'excel', 'pdf']
		};
	}

	function doPopulateTable(arrData, blnChangeSearchCriteria, opt_fncCallback) {
		var _blnChangeSearchCriteria = (typeof blnChangeSearchCriteria != undefined) ? blnChangeSearchCriteria : true;
		//if ((typeof _objDataTable == 'object') && ('fnDestroy' in _objDataTable) && (typeof _objDataTable.fnDestroy == 'function')) _objDataTable.fnDestroy();
		$('#divDisplayQueryResult').html('<table id="tblSearchResult" class="cls-tbl-list"></table>');
		if (typeof fnOnDataTableBeforeCreate == 'function') fnOnDataTableBeforeCreate.apply(this);
		if (_DT_VERSION.substring(0, 4) == '1.9.') {
			_objDataTable = $('#tblSearchResult').dataTable($.extend(true, {}, _DT_BASE_OPTIONS, {
				"aaData": arrData
				, "aoColumns": _aoColumns
				, "oTableTools": {
					"aButtons": (_tableToolButtons != undefined)?_tableToolButtons:[],
					"sSwfPath": "public/js/jquery/dataTable/TableTools/2.1.5/swf/copy_csv_xls_pdf.swf"
				}
			}));
			oSettings = _objDataTable.fnSettings();
			oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
			setTimeout(_doResize, 1200);
		} else {
			var _newOpt = $.extend(true, {}, _DT_BASE_OPTIONS, {
				"data": arrData
				, "columns": _aoColumns
				, "buttons": (_tableToolButtons != undefined) ? _tableToolButtons : []
				,"stateLoadParams": function (oSettings, oaData) {
					if (_blnChangeSearchCriteria) { //Destroy state saving if requery
						_blnChangeSearchCriteria = false;
						return false;
					}
				}
			});
			_objDataTable = $('#tblSearchResult').dataTable(_newOpt);
		}
		if (typeof fnOnDataTableAfterCreate == 'function') fnOnDataTableAfterCreate.apply(this);
		_objDataTable.fnDraw();
		if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this);
		$('#divDisplayQueryResult').trigger('load_done');
	}

	function _doResize() {
		_objDataTable.fnAdjustColumnSizing(false);
	}

	function doSelectDisplayFields() {
		$("#divSelectableFields").dialog( "open" );
	}

	function _fncDTmRenderFormat(data, type, full, format) {
		if (data) {
			var _fmt = format || 'default_number';
			switch (_fmt) {
				case 'default_int':
					var _data = data.toString().replace(/\,/g, '');
					if (isNaN(_data)) {
						return data;
					} else {
						return formatNumber(parseInt(_data), 0);
					}
					break;
				case 'default_number':
					var _data = data.toString().replace(/\,/g, '');
					if (isNaN(_data)) {
						return data;
					} else {
						return formatNumber(parseFloat(_data), 2);
					}
					break;
			}
		}
		return data;
	}