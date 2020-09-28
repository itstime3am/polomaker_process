if (typeof _Sublist_arrDtColumns == 'undefined') {
	var _Sublist_arrDtColumns = [];
	var _Sublist_aoColumns = [];
	var _arrSublistDataTable = [];

	var _masterLink = [];
	function populateSublist(blnEditable, objLink, opt_fncCallback) {
		if (objLink) _masterLink = objLink;
		$(".cls-div-sub-list").each(function() {
			var _controller = $(this).attr('controller');
			var _datastring = $(this).attr('main-search');
			var _index = $(this).attr('index');
			var _divEditDlg = $('.cls-div-form-edit-dialog[index=' + _index + ']').get(0);
			var _this = this;
			doClearDisplayInfo(_index);
			$.ajax({
				type: "POST", 
				url: "./" + _controller + "/json_search",
				dataType: "json",
				data: _datastring,
				success: function(data, textStatus, jqXHR) {
					if (data.success == false) {
						doDisplayInfo(MSG_ALERT_QUERY_FAILED.replace(/v_XX_1/g, data.error), 'Search', _index);
					} else {
						data = data.data;
						var _arrayData = [];
						if (data.length > 0) {
							for (var i = 0; i < data.length; i++) {
								_arrayData[i] = {'client_temp_id':i};
								for (j=0;j<_Sublist_arrDtColumns[_index].length;j++) {
									_arrayData[i][_Sublist_arrDtColumns[_index][j][0]] = (data[i][_Sublist_arrDtColumns[_index][j][0]] == null)?'':data[i][_Sublist_arrDtColumns[_index][j][0]];
								}
							}
						}
						_populateSublistDataTable(_index, _arrayData, blnEditable);
					}
					if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
					$('.cls-div-sub-list[index="' + _index + '"]').trigger("DataTableLoaded", [_index, _arrSublistDataTable[_index], _masterLink]);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					doDisplayInfo(MSG_ALERT_QUERY_FAILED.replace(/v_XX_1/g, textStatus + ' ( ' + errorThrown + ' )'), "ErrorMessage", _index);
					if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
				},
				statusCode: {
					404: function() {
						doDisplayInfo("Page not found", "ErrorMessage", _index);
						if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
					}
				}
			});
		});
	}
	
	function _populateSublistDataTable(index, arrData, blnEditable) {
		var _index = index || -1;
		var _arrayData = arrData || [];
		var _divDisplay = $('.cls-div-sub-list[index="' + _index + '"]');
		var _divEditDlg = $('#divSublistFormEditDialog[index="' + _index + '"]');
		if (typeof blnEditable != 'boolean') blnEditable = true;
		//if (_arrSublistDataTable.length > 0) {
		//	if (_index in _arrSublistDataTable) if ($.isFunction(_arrSublistDataTable[_index].fnDestroy)) _arrSublistDataTable[_index].fnDestroy();
		//}
		_divDisplay.html('<table id="tblSubList' + _index + '" class="cls-tbl-list"></table>');
		var _arrColumns = _Sublist_aoColumns[_index].slice(0);
		var _arrButtons;
		if (_DT_VERSION.substring(0, 4) == '1.9.') {
			_arrButtons = [
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
			];
		} else {
			_arrButtons = ["copyHtml5", "print", "excelHtml5"];
		}
		
		if (blnEditable) {
			if (_DT_VERSION.substring(0, 4) == '1.9.') {
				_arrButtons.push({
								"sExtends": "text"
								, "sButtonText": ""
								, "sButtonClass": "DTTT_button_space"
							});
				_arrButtons.push({
								"sExtends": "text",
								"sButtonText": "เพิ่ม",
								"sButtonClass": "DTTT_button_add_row",
								"fnClick": function ( nButton, oConfig, oFlash ) {
									if (! $(nButton).is('.DTTT_button_disabled')) doInsert(_divEditDlg);
								}
							});
			} else {
				_arrButtons.push({"text": "&nbsp;","className": "DTTT_button_space"});
				_arrButtons.push({
					"text": "เพิ่ม","className": "DTTT_button_add_row"
					,"action": function ( nButton, oConfig, oFlash ) {
						if (! $(nButton).is('.DTTT_button_disabled')) doInsert(_divEditDlg);
					}
				});
			}
		}
		if (_DT_VERSION.substring(0, 4) == '1.9.') {
			_arrSublistDataTable[_index] = $('#tblSubList' + _index).dataTable($.extend(true, {}, _DT_BASE_OPTIONS, {
				"aaData": _arrayData
				, "aoColumns": _arrColumns
				, "oTableTools": {
					"aButtons": (_arrButtons != undefined)?_arrButtons:[],
					"sSwfPath": "public/js/jquery/dataTable/TableTools/2.1.5/swf/copy_csv_xls_pdf.swf"
				}
			}));
			oSettings = _arrSublistDataTable[_index].fnSettings();
			oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
			setTimeout(function() { _arrSublistDataTable[_index].fnAdjustColumnSizing(true); }, 1200);
		} else {
			var _newOpt = $.extend(true, {}, _DT_BASE_OPTIONS, {"data": _arrayData, "columns": _arrColumns, buttons: (_arrButtons != undefined)?_arrButtons:[]});
			_arrSublistDataTable[_index] = $('#tblSubList' + _index).dataTable(_newOpt);
		}
		_arrSublistDataTable[_index].fnDraw();
		if (blnEditable) {
			$('td img[command="edit"]', _divDisplay).css('display', '');
			$('td img[command="delete"]', _divDisplay).css('display', '');
			_divDisplay.find('.DTTT_button_space').css('display', '');
			_divDisplay.find('.DTTT_button_add_row').css('display', '');			
		} else {
			$('td img[command="edit"]', _divDisplay).css('display', 'none');
			$('td img[command="delete"]', _divDisplay).css('display', 'none');
			_divDisplay.find('.DTTT_button_space').css('display', 'none');
			_divDisplay.find('.DTTT_button_add_row').css('display', 'none');			
		}
		_divDisplay.trigger('load_done');
	}
	function clearSubList() {
		$(".cls-div-sub-list").each(function() {
			var _index = $(this).attr('index');
			if (_index in _arrSublistDataTable) {
				_arrSublistDataTable[_index].fnDestroy(true);
			}
			$(this).html('<table id="tblSubList' + _index + '" class="cls-tbl-list"></table>');
		});
		_arrSublistDataTable.length = 0;
	}
}