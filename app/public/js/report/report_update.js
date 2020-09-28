	$(function() {
		doPopulateTable = function (arrData, blnChangeSearchCriteria) {
			var _blnChangeSearchCriteria = typeof blnChangeSearchCriteria != undefined?blnChangeSearchCriteria:true;
			_objChangedData = {};
			if (_objDataTable) _objDataTable.fnDestroy(true);
			$('#divDisplayQueryResult').html(_TMPL_TBL_SEARCH);			
			_objDataTable = $('#tblSearchResult').dataTable(
				{
					"bJQueryUI": true,
					"sPaginationType": "full_numbers",
					"aaData": arrData,
					"aaSorting":[],
					"sScrollY": "85%",
					"sScrollX": "100%",
					"sScrollXInner": "500%",
					"aLengthMenu": [[10, 20, 35, 50, -1], [10, 20, 35, 50, "all"]],
					"iDisplayLength": 10,
					"bStateSave": true,
					"fnStateLoadParams": function (oSettings, oaData) {
						if (_blnChangeSearchCriteria) { //Destroy state saving if requery
							_blnChangeSearchCriteria = false;
							return false;
						}
					},
					"aoColumns": _aoColumns,
					"sDom": "<'row-fluid'<'span6'T><'span6'lf>r>t<'row-fluid'<'span6'i><'span6'p>><'clear'><'span6'T>",
					"oTableTools": {
						"aButtons": (_tableToolButtons != undefined)?_tableToolButtons:[],
						"sSwfPath": "public/js/jquery/dataTable/TableTools/2.1.5/swf/copy_csv_xls_pdf.swf"
					},
					"bScrollCollapse": true,
					"fnInitComplete": function(oSettings, json) {
						oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();	
						_objDataTableFixedColumns = new $.fn.dataTable.FixedColumns( this, {
							"iLeftColumns": 5
						});
						setTimeout(_doResize, 1000);
					},
					"fnDrawCallback": function () { //fnInfoCallback
						setTimeout(_doSetDataTablePagePlugins, 200);
					}
				}
			);
			_doUpdateSummaryValue(arrData);
		};
	});
	
	if (_ALLOW_EDIT === false) {
		_doSetEditableColumns = function() {
			//do nothing;
		};
	} else {
		_doSetEditableColumns = function() {
			$('#tblSearchResult tbody tr td.order_status_id').editable(
				function (value, settings) { 
					var _val;
					if (_doPrepareChangedData('order_status_id', value, this)) {
						_val = (settings.data[value]);
					} else {
						_val = this.revert;
					}
					//++ Set value and class to fixed column 
					var _otr = $($(this).parents('tr')[0]);
					var _trIndex = _otr.index();
					var _tdIndex = $(this).index();
					if ((_trIndex > -1) && (_tdIndex > -1) && ($('div.DTFC_LeftBodyLiner table tbody tr').length > _trIndex)) {
						var _fcTr = $($('div.DTFC_LeftBodyLiner table tbody tr').get(_trIndex));
						var _fcCol = $($('td', _fcTr).get(_tdIndex));
					}
					if (_fcCol) {
						_fcCol.html(_val);
						_fcCol.removeClass('data-edit-changed');
						if ($(this).hasClass('data-edit-changed')) _fcCol.addClass('data-edit-changed');
					}
					//-- Set value and class to fixed column 
					return _val;
				}, { type: 'select', data: {'0':'', '1':'WIP', '2':'รอส่ง', '3':'เครดิต', '4':'CLOSED'}, onblur: 'submit', tooltip: 'Click to edit...',
						callback: function() {
							$('div.DTFC_LeftBodyWrapper').css('display', '');
						}
					}
			);
	
			$('#tblSearchResult tbody tr td.screen_status_id').editable(
				function (value, settings) { 
					if (_doPrepareChangedData('screen_status_id', value, this)) return (settings.data[value]);
					return this.revert;
				}, { type: 'select', data: {'0':'', '1':'รอแบบลูกค้า', '2':'รอขึ้นแบบ', '3':'รอลูกค้าโอเค', '4':'OK', '5':'OK แบบเก่า'}, onblur: 'submit', tooltip: 'Click to edit...' }
			);

			$('#tblSearchResult tbody tr td.weave_status_id').editable(
				function (value, settings) { 
					if (_doPrepareChangedData('weave_status_id', value, this)) return (settings.data[value]);
					return this.revert;
				}, { type: 'select', data: {'0':'', '1':'รอแบบลูกค้า', '2':'รอขึ้นแบบ', '3':'รอลูกค้าโอเค', '4':'OK', '5':'OK แบบเก่า'}, onblur: 'submit', tooltip: 'Click to edit...' }
			);

			$('#tblSearchResult tbody tr td.deposit_payment').editable(
				function (value, settings) { 
					if (_doPrepareChangedData('deposit_payment', value, this)) {
						if (isNaN(value)) {
							return value;
						} else {
							return formatNumber(parseFloat(value), 2);
						}
					}
					return this.revert;
				}, { type: 'text', onblur: 'submit', data: function (value, settings) { return value.replace(/,/gi, ''); }, tooltip: 'Click to edit...' }
			);
			$('#tblSearchResult tbody tr td.deposit_route_id').editable(
				function (value, settings) { 
					if (_doPrepareChangedData('deposit_route_id', value, this)) return (settings.data[value]);
					return this.revert;
				}, { type: 'select', data: _objDepositRouteOptions, onblur: 'submit', tooltip: 'Click to edit...' }
			);
			$('#tblSearchResult tbody tr td.deposit_date').editable(
				function (value, settings) { 
					if (_doPrepareChangedData('deposit_date', value, this)) return value;
					return this.revert;
				}, { type: 'datepicker', onblur: 'submit', tooltip: 'Click to edit...' }
			);
		};
	}
	
	function _doPrepareChangedData(data_field, value, objSource) {
		var _tr = $(objSource).parents('tr')[0];
		var _datatable = $(objSource).parents('.cls-tbl-list')[0];
		$(objSource).removeClass('data-edit-changed');
		if ((_tr) && (_datatable)) {
			var _aData = $(_datatable).dataTable().fnGetData(_tr);
			if (('order_rowid' in _aData) && ('order_type_id' in _aData) && (data_field) && (value)) {
				var _value = value;
				var _order_rowid = _aData['order_rowid'].toString();
				var _type_id = _aData['order_type_id'].toString();
				var _str_index = _type_id + '-' + _order_rowid;
				var _rowid = _aData['order_status_rowid'] || -1;
				var _net_amount = _aData['total_price_sum_net'] || '';
				var _vat = _aData['total_price_sum_vat'] || '';
				var _total_amount = _aData['total_price_sum'] || '';
				_net_amount = _net_amount.replace(/\,/g, '');
				_vat = _vat.replace(/\,/g, '');
				_total_amount = _total_amount.replace(/\,/g, '');
				
				//Clear this property in case edit and clear value
				if (_str_index in _objChangedData) {
					if (data_field in _objChangedData[_str_index]) delete _objChangedData[_str_index][data_field];
				}
				switch (data_field) {
					case 'order_status_id':
					case 'screen_status_id':
					case 'weave_status_id':
					case 'deposit_route_id':
						if ((_aData[data_field] || 0) == value) {
							_fnCheckDataChanged(); //run to check and reset state if no others data changed
							return false;
						}
						break;
					case 'deposit_payment':
						$('.ul-vldr-error-msg #li_' + data_field + '__typeDouble').remove();
						$(objSource).removeClass('input-invalid').removeProp('invalid-msg');
						_value = value.replace(/\,/g, '');
						if (isNaN(_value)) {
							var _strErrMsg = MSG_VLDR_INVALID_DATATYPE.replace(/v_XX_1/g, '( ' + data_field + ': double )') + ' ';
							$(objSource).addClass('input-invalid').prop('invalid-msg', _strErrMsg);
							$('.ul-vldr-error-msg').append('<li id="li_' + data_field + '__typeDouble">' + _strErrMsg + '</li>');
							return true;
						} else {
							if (parseFloat(_aData[data_field].toString().replace(/\,/g, '')).toFixed(2) == parseFloat(_value).toFixed(2)) {
								_fnCheckDataChanged(); //run to check and reset state if no others data changed
								return false;
							}
						}
						break;
				}
				if (!(_str_index in _objChangedData)) _objChangedData[_str_index] = {"rowid":_rowid, "order_type_id": _type_id, "order_rowid": _order_rowid, "net_amount": _net_amount, "vat": _vat, "total_amount": _total_amount};
				_objChangedData[_str_index][data_field] = _value;
				
				$(objSource).addClass('data-edit-changed');
				$('.DTTT_button_commit_page').removeClass('DTTT_button_disabled');
				return true;
			}
		}
		return false;
	}
	
	function _doCommitPage() {
//console.log('COMMIT');
		var _str = JSON.stringify(_objChangedData);
//console.log(_str);
		if (( ! _objChangedData) || (_str == '{}')) {
			alert(MSG_ALERT_COMMIT_NO_CHANGE);
			return false;
		}

		$("#dialog-modal").html("<p>" + MSG_DLG_HTML_COMMIT + "</p>");
		$("#dialog-modal").dialog('option', 'title', MSG_DLG_TITLE_COMMIT);
		$("#dialog-modal").dialog( "open" );
		$.ajax({
			type:"POST",
			url:"./report_update/commit",
			contentType: "application/json;charset=utf-8",
			dataType:"json",
			data: _str,
			success: function(data, textStatus, jqXHR) {
				if (data.success == false) {
					alert(MSG_ALERT_COMMIT_FAILED.replace(/v_XX_1/g, data.error));
					$("#dialog-modal").dialog( "close" );
				} else {
					doSearch(false, false);
					alert(MSG_ALERT_COMMIT_SUCCESS.replace(/v_XX_1/g, ''));
					$("#dialog-modal").dialog( "close" );
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				doDisplayInfo(textStatus + ' : ' + errorThrown, "ErrorMessage");
				$("#dialog-modal").dialog( "close" );
			},
			statusCode: {
				404: function() {
					doDisplayInfo("Page not found", "ErrorMessage");
					$("#dialog-modal").dialog( "close" );
				}
			}
		});
	}
