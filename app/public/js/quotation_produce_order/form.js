var _autoSearch_OnLoad = false;
$(function() {
	var _chldCount = $("#tabs ul").find("a").length;
	var _currIndex = 0;
	// ++ Inferno:: ** prevent error neverending reload loop
	$("div.cls-tab-container ul").find("a").each(function() {
		var href = $( this ).attr( "href" );
		if ( href.indexOf( "#" ) == 0 ) {
			var newHref = window.location.protocol + '//' + window.location.hostname + window.location.pathname + href;
			$(this).attr("href", newHref);
		}
	});
	// -- Inferno:: ** prevent error neverending reload loop
    $("#tabs").tabs({
		beforeActivate: function( event, ui ) {
			if (! _evntCheckBeforeChangeTab(ui.oldPanel)) {
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
		}
		, activate: function(event, ui) {
			$($.fn.dataTable.fnTables(true)).DataTable().columns.adjust(); //.responsive.recalc()
		}
	})
	.show();
	
    $("#divDetailTabs").tabs();
	
	$('#divUserInputDialog').dialog({
		height: 200
		, width: 780
		, show: {effect:"puff", duration: 1000}
		, hide: {effect:"fade", duration:1000}
		, resizable: false
		, modal: true
		, closeOnEscape: true
		, autoOpen: false
		, beforeClose: function(event, ui) {
			doClearVldrError($(this));
			$("#txt-create_order_date").datepicker('option', 'maxDate', null);
			$("#txt-create_due_date").datepicker('option', 'minDate', null);
			$("#txt-create_due_date").datepicker('option', 'maxDate', null);
			$("#txt-create_deliver_date").datepicker('option', 'minDate', null);
			clearValue($('.user-input[name="supplier_rowid"]'));
			
			$('.data-container', this).each(function() {
				clearValue($(this));
			});
		}
		, buttons: {
			'Commit': function() {
				var _data = __objGetDataFromContainer($('#divUserInputDialog'));

				doClearVldrErrorElement($('#txt-create_order_date'));
				if (! (('create_order_date' in _data) && (_data['create_order_date']))) {
					doSetVldrError($('#txt-create_order_date'), 'order_date', 'required', '"วันที่สั่งงาน" is required field.', 3);
				} else {
					if (confirm('ยืนยันการสร้างใบสั่งผลิตจากข้อมูลรายละเอียดใบเสนอราคาที่เลือก')) {
						$.ajax({
							type:"POST",
							url:"./quotation_produce_order/create_link_produce_order",
							contentType:"application/json;charset=utf-8",
							dataType:"json",
							data: JSON.stringify(_data),
							success: function(data, textStatus, jqXHR) {
								if (data.success == false) {
									doDisplayError('การสร้างใบสั่งงานผลิตล้มเหลว!', MSG_ALERT_COMMIT_FAILED.replace(/v_XX_1/g, data.error), true, 3);
								} else {
									var _json = JSON.parse(data.message);
									_doDisplayToastMessage('สร้างใบสั่งงานผลิตสำเร็จ ( job_number: "' + _json["job_number"] + '")', 2, false);
									if (typeof doSearch == 'function') doSearch(false);
								}
								_forceCloseAllWaitModalDialog();
							}
							, error: function(jqXHR, textStatus, errorThrown) {
								doDisplayError(textStatus, errorThrown, false, 3);
							}
							, statusCode: {
								404: function() {
									doDisplayError("Not Found!", "Page not found", false, 3);
								}
							}
						});
						$(this).dialog('close');
					}
				}
				return false;
			}
			, 'Cancel': function() {
				$(this).dialog('close');
			}
		}
	});

	$('#txt-create_order_date, #txt-create_due_date, #txt-create_deliver_date').datepicker({
			showOn: "both",
			buttonImage: "public/images/select_day.png",
			buttonImageOnly: true,
			dateFormat: 'dd/mm/yy'
		});
	$('#txt-create_order_date').on('change', function() {
		doClearVldrErrorElement($(this));
		$("#txt-create_due_date").datepicker('option', 'minDate', null);
		$("#txt-create_deliver_date").datepicker('option', 'minDate', null);

		var _ord_date = $('#txt-create_order_date').datepicker('getDate');
		var _due_date = $('#txt-create_due_date').datepicker('getDate');
		var _dlv_date = $('#txt-create_deliver_date').datepicker('getDate');
		
		if ((_ord_date !== null) && (_ord_date instanceof Date)) {
			if ((_due_date !== null) && (_due_date instanceof Date)) {
				if ((_ord_date > _due_date)) {
					doSetVldrError($(this), '', 'InvalidOrderDate', 'Order_Date must lesser than Due_Date', 3);
					return false
				}
			}
			if ((_dlv_date !== null) && (_dlv_date instanceof Date)) {
				if ((_ord_date > _due_date)) {
					doSetVldrError($(this), '', 'InvalidOrderDate', 'Order_Date must lesser than Deliver_Date', 3);
					return false
				}
			}
			$("#txt-create_due_date").datepicker('option', 'minDate', _ord_date);
			$("#txt-create_deliver_date").datepicker('option', 'minDate', _ord_date);		
		} else {
			$(this).datepicker('setDate', null);
		}
	});

	$('#txt-create_due_date').on('change', function() {
		doClearVldrErrorElement($(this));
		$("#txt-create_order_date").datepicker('option', 'maxDate', null);
		$("#txt-create_deliver_date").datepicker('option', 'minDate', null);

		var _ord_date = $('#txt-create_order_date').datepicker('getDate');
		var _due_date = $('#txt-create_due_date').datepicker('getDate');
		var _dlv_date = $('#txt-create_deliver_date').datepicker('getDate');
		if ((_due_date !== null) && (_due_date instanceof Date)) {
			if ((_ord_date !== null) && (_ord_date instanceof Date)) {
				if ((_ord_date > _due_date)) {
					doSetVldrError($(this), '', 'InvalidDueDate', 'Due_Date must greater than Order_Date', 3);
					return false
				}
			}
			if ((_dlv_date !== null) && (_dlv_date instanceof Date)) {
				if ((_due_date > _dlv_date)) {
					doSetVldrError($(this), '', 'InvalidDueDate', 'Due_Date must lesser than Deliver_Date', 3);
					return false
				}
			}
			$("#txt-create_order_date").datepicker('option', 'maxDate', _due_date);
			$("#txt-create_deliver_date").datepicker('option', 'minDate', _due_date);
		} else {
			$(this).datepicker('setDate', null);
		}
	});
	
	$('#txt-create_deliver_date').on('change', function() {
		doClearVldrErrorElement($(this));
		$("#txt-create_order_date").datepicker('option', 'maxDate', null);
		$("#txt-create_due_date").datepicker('option', 'maxDate', null);

		var _ord_date = $('#txt-create_order_date').datepicker('getDate');
		var _due_date = $('#txt-create_due_date').datepicker('getDate');
		var _dlv_date = $('#txt-create_deliver_date').datepicker('getDate');		
		if ((_dlv_date !== null) && (_dlv_date instanceof Date)) {
			if ((_ord_date !== null) && (_ord_date instanceof Date)) {
				if ((_ord_date > _dlv_date)) {
					doSetVldrError($(this), '', 'InvalidDeliverDate', 'Deliver_Date must greater than Order_Date', 3);
					return false
				}
			}
			if ((_due_date !== null) && (_due_date instanceof Date)) {
				if ((_due_date > _dlv_date)) {
					doSetVldrError($(this), '', 'InvalidDeliverDate', 'Deliver_Date must greater than Due_Date', 3);
					return false
				}
			}
			$("#txt-create_order_date").datepicker('option', 'maxDate', _dlv_date);
			$("#txt-create_due_date").datepicker('option', 'maxDate', _dlv_date);
		} else {
			$(this).datepicker('setDate', null);
		}
	});

	var _fncTemplate_doClearForm = _doClearForm;
	var _fncTemplate_doInsert = doInsert;
	var _fncTemplate_doView = doView;

	_doClearForm = function (_frm) {
		_fncTemplate_doClearForm.apply(this, arguments);
		var _index = $(_frm).attr('index') || 0;
		if (_index == 0) {
			$('#tabs').tabs({ active: 0 });
			$("#tabs").tabs( "disable" , 1 );
		} else {
			//++ premade details size
			$('#tbl_detail_list tbody tr:not("#edit_panel1")', _frm).remove();
			$('#tbl_detail_size tbody tr:not("#edit_panel2")', _frm).remove();
			$('.total-value').html(' -- ');
			$('.total-price').html(' -- ');
			doClearDetailsEditPanel();
			_blnDetailChanged = false;
			//-- premade details size

			//++ set size cat and hide another table
			$(".tbl_size_cat", _frm).css('display', 'none');
			$('.total-value').html(' -- ');
			$('.total-price').html(' -- ');
			//-- set size cat and hide another table
			
			//remove others price items
			$('#tbl_op_list tbody tr:not("#op_edit_panel")', _frm).remove();
			opClearEditPanel();
			_blnOpChanged = false;

			//remove screen items
			$('#tbl_sc_list tbody tr:not("#sc_edit_panel")', _frm).remove();
			scClearEditPanel();
			_blnScChanged = false;

			$('.cls-is-expired.hidden', _frm).removeClass('hidden');
			$('.cls-is-expired.has-value', _frm).removeClass('has-value');

			$('#divDetailTabs').tabs({ active: 0 });
			$('#tabMnuDetail').addClass('hidden');
			$('#tabMnuOthers').addClass('hidden');
			$('.cls-detail-panel').each(function() { $(this).addClass('hidden'); });
			$('.cls-others-panel').each(function() { $(this).addClass('hidden'); });

			$("div.display-upload", _frm).css('background-image', '');
		}
	};

	doView = function(dataRowObj, divEditDlg) {
		_fncTemplate_doView.apply(this, arguments);
		var _index = $('#frm_edit', divEditDlg).attr('index') || 0;
		if (_index == 0) {
			if (('rowid' in dataRowObj) && (dataRowObj['rowid'] > 0)) $("#tabs").tabs( "enable" , 1 );
			
			var _qo_status = parseInt(dataRowObj['status_rowid'] || 0);
			__fncCheckVisibleElementsByStatus(_qo_status, divEditDlg);

			__fncPopulateSpecialControls(dataRowObj, divEditDlg);
			__fncManageExpired(false);
		}
		$('.eventView-hide').addClass('hidden');
	};

	$('#sel-title_rowid').combobox({
		select: function() {
			var _sel = $('option:selected', this);
			if (_sel.length <= 0) return false;

			var _order_type_id = _sel.attr('order_type_id') || -1;
			//$('#hdn-title').val(_sel.text());
			
			$('#tabMnuDetail').addClass('hidden');
			$('#tabMnuOthers').addClass('hidden');
			$('.cls-detail-panel').each(function() { $(this).addClass('hidden'); });
			switch (parseInt(_order_type_id)) {
				case 1:
					$('#tabMnuDetail').removeClass('hidden');
					$('#tabMnuOthers').removeClass('hidden');
					$('#divPoloDetailPanel').removeClass('hidden');
					$('#divPoloOthersPanel').removeClass('hidden');
					break;
				case 2:
					$('#tabMnuDetail').removeClass('hidden');
					$('#divTshirtDetailPanel').removeClass('hidden');
					break;
				case 3:
					$('#tabMnuDetail').removeClass('hidden');
					$('#divCapDetailPanel').removeClass('hidden');
					break;
				case 4:
					$('#tabMnuDetail').removeClass('hidden');
					$('#divJacketDetailPanel').removeClass('hidden');
					break;
			}
		}
	});
	
	$('#divPanelHandler').remove();
	if (typeof doSearch == 'function') doSearch(false);	
});

function _doUpdateDepositValues(dblPercent, dblActual) {
	var _dblP = dblPercent || -1;
	var _dblA = dblActual || -1;
	var _dblAmount = _cleanNumericValue(getValue($('#divMain #spn-sum_amount'), 0));	
	if (_dblAmount <= 0) return false;
	
	if (_dblP > 0) {
		_dblA = ((_dblP / 100) * _dblAmount);
		setValue($('#divMain #txt-deposit_amount'), formatNumber(_dblA));
	} else if (_dblA > 0) {
		_dblP = ((_dblA * 100) / _dblAmount);
		setValue($('#divMain #txt-deposit_percent'), formatNumber(_dblP));
	}
}

function __fncCheckVisibleElementsByStatus(qo_status, divEditDlg) {
	$('[from_qs]', divEditDlg).each(function() {
		$(this).removeClass('hidden');
	});
	$('[to_qs]', divEditDlg).each(function() {
		$(this).removeClass('hidden');
	});
	
	$('[from_qs]', divEditDlg).each(function() {
		if (parseInt($(this).attr('from_qs') || 0) > qo_status) {
			$(this).addClass('hidden');
		}
	});
	$('[to_qs]', divEditDlg).each(function() {
		if (parseInt($(this).attr('to_qs') || 0) < qo_status) {
			$(this).addClass('hidden');
		}
	});
}

function __fncPopulateSpecialControls(dataRowObj, divEditDlg) {
	var _arr = dataRowObj['json_details'];
	if ((typeof _arr == 'string') && (_arr.trim().length > 0)) _arr = JSON.parse(_arr);
	if ($('div#div_premade_detail_panel').filter(__fnc_filterNotNestedHiddenClass).length > 0) {
		_premadeOrderFetchDetail(_arr);
	} else {
		var _div = $('div#ord_cap_quan_container').filter(__fnc_filterNotNestedHiddenClass);
		if (_div.length > 0) {
			var _qty = 0;
			var _price = 0;
			if ('order_qty' in _arr) _qty = parseInt(_arr['order_qty'] || 0);
			if ('order_price_each' in _arr) _price = parseFloat(_arr['order_price_each'] || 0);
			
			$('div.total-price', _div[0]).html(formatNumber(_qty * _price));
		}		
	}
	_doSetValueFormUserInput($('#divDetail', divEditDlg), _arr);	
	var _size_cat = ('size_category' in _arr) ? _arr["size_category"] : -1;
	
	_arr = dataRowObj['json_images'];
	if (typeof _arr == 'string') _arr = JSON.parse(_arr);
	var _arrImgs = {};
	for (var _key in _arr) {
		var _val = _arr[_key] || false;
		if ((typeof _key == 'string') && (typeof _val == 'string') && (_val != 'unchange')) _arrImgs[_key] = {"url": '../app/uploads/' + _val, "name": _val};
	}
	_doSetValueFormUserInput($('#divImages', divEditDlg), _arrImgs);

	if (('json_others' in dataRowObj)) {
		if (typeof dataRowObj['json_others'] == 'string') dataRowObj['json_others'] = JSON.parse(dataRowObj['json_others']);
		
		//++ size quan
		if (_size_cat > 0) dataRowObj['json_others']['size_category'] = _size_cat;
		_sqFetchData(dataRowObj['json_others']);
		
		//++ Screen
		_tbl = $('table#tbl_sc_list', divEditDlg).filter(__fnc_filterNotNestedHiddenClass);
		if (_tbl.length > 0) _tbl = _tbl[0];
		$('tbody tr:not("#sc_edit_panel")', _tbl).remove();
		if ('screen' in dataRowObj['json_others']) {
			_arr = dataRowObj['json_others']["screen"];
			if (typeof _arr == 'string') _arr = JSON.parse(_arr);
			for (_i=0;_i<_arr.length;_i++) {
				var _row = _arr[_i];
				_scInsertDetailRow(_tbl, _row);
			}
		}
		//++ Others price
		_tbl = $('table#tbl_op_list', divEditDlg).filter(__fnc_filterNotNestedHiddenClass);
		if (_tbl.length > 0) _tbl = _tbl[0];
		$('tbody tr:not("#op_edit_panel")', _tbl).remove();
		if ('others_price' in dataRowObj['json_others']) {
			_arr = dataRowObj['json_others']["others_price"];
			if (typeof _arr == 'string') _arr = JSON.parse(_arr);
			for (_i=0;_i<_arr.length;_i++) {
				var _row = _arr[_i];
				_opInsertDetailRow(_tbl, _row);
			}
		}
	}
	_doUpdateDetailValues();
}

var _current_row;
var _blnDetailsChange = false;
function _doUpdateTotalValue(index) {
	if ((index >= 0)) { 
		var _totalNet = 0;
		var _totalDiscount = 0;
		var _totalVAT = 0;
		var _totalValue = 0;
		var _arr = _arrSublistDataTable[index].fnGetData() || [];
		for (_i=0;_i<_arr.length;_i++) {
			if (('amount' in _arr[_i]) && (_arr[_i]['amount'] != '') && (! isNaN(_arr[_i]['amount']))) {
				_totalNet += parseFloat(_arr[_i]['amount']);
			}
		}
		var _percent = $('#txt-percent_discount').val() || 0;
		if ((! isNaN(_percent)) && (_percent > 0) && (_percent < 100)) {
			_totalDiscount = (_totalNet * _percent / 100) * -1;
		} else {
			$('#txt-percent_discount').val('');
		}

		var _is_vat = getValue($('#sel-is_vat')) || 0;
		if (_is_vat == 1) {
			_totalVAT = Math.round(_totalNet * 7) / 100;
		} else if (_is_vat == 2) {
			_totalVAT = Math.round(_totalNet * 7) / 107;
			_totalNet = _totalNet - _totalVAT;
		} else {
			_totalVAT = 0;
		}
		_totalValue = _totalNet + _totalDiscount + _totalVAT;
		$('#divMain #spn-sum_net').html(formatNumber(_totalNet, 2));
		$('#divMain #spn-sum_discount').html(formatNumber(_totalDiscount, 2));
		$('#divMain #spn-sum_vat').html(formatNumber(_totalVAT, 2));
		$('#divMain #spn-sum_amount').html(formatNumber(_totalValue, 2));

		//$('#divMain #spn-disp_deposit_payment').html(formatNumber(_totalValue, 2));
		//$('#divMain #spn-disp_left_amount').html(formatNumber(_totalValue, 2));
	}
}

function fnc__DDT_Row_RenderProduceOrderControl(data, type, full) {
	var _order_type_id = ('order_type_id' in full) ? parseInt(full['order_type_id']) : -1;
	if (_order_type_id <= 0) return false;

	var _type_id = ('type_id' in full) ? parseInt(full['type_id']) : -1;
	var _order_rowid = ('order_rowid' in full) ? parseInt(full['order_rowid']) : -1;
	var _order_job_number = ('order_job_number' in full) ? full['order_job_number'] : 'n/a';
	var _elPanel = $('<div>');
	var _div = $('<div>').attr('qd_rowid', full['rowid']).attr('qo_rowid', full['quotation_rowid']).addClass("cls-quotation-row-control-panel").appendTo(_elPanel);
	if (_order_rowid > 0) {
		var _href = '';
		switch (_type_id) {
			case 1:
				_href = './order_polo/pass_command/1/' + _order_rowid + '/' + _order_job_number;
				break;
			case 2:
				_href = './order_tshirt/pass_command/1/' + _order_rowid + '/' + _order_job_number;
				break;
			case 3:
				_href = './order_premade_polo/pass_command/1/' + _order_rowid + '/' + _order_job_number;
				break;
			case 4:
				_href = './order_premade_tshirt/pass_command/1/' + _order_rowid + '/' + _order_job_number;
				break;
			case 5:
				_href = './order_cap/pass_command/1/' + _order_rowid + '/' + _order_job_number;
				break;
			case 6:
				_href = './order_jacket/pass_command/1/' + _order_rowid + '/' + _order_job_number;
				break;
			case 7:
				_href = './order_premade_cap/pass_command/1/' + _order_rowid + '/' + _order_job_number;
				break;
			case 8:
				_href = './order_premade_jacket/pass_command/1/' + _order_rowid + '/' + _order_job_number;
				break;
		}
		_div.append($('<a href="' + _href + '"><span class="cls-link_produce_order" command="link_produce_order" title="ใบงานผลิต">' + _order_job_number + '</span></a>'));		
	} else {
		_div.append($('<img class="list-row-button" command="produce" src="./public/images/forms.png" alt="create produce order" title="สร้างใบงานผลิต">'));
	}
	return _elPanel.html();	
}

function customCommand(command, aData, tr, divEditDlg) {
	var _cmd = command.toLowerCase();
	if ((_cmd == 'produce') && ('rowid' in aData)) {
		var _rowid = aData['rowid'] || false;
		if (! _rowid) return false;
		
		var _seq = aData['seq'] || _rowid;
		var _qo_number = aData['qo_number'] || aData['quotation_rowid'];
		if (confirm('สร้างใบงานสั่งผลิต ตามรายละเอียดลำดับที่ #' + _seq + ', ของใบเสนอราคาเลฃที่ "' + _qo_number + '", กรุณากด "ยืนยัน" เพื่อทำรายการ')) {
			var _dlg = $('#divUserInputDialog');
			setValue($('#hdn-create_quotation_detail_rowid', _dlg), _rowid);
			setValue($('#txt-create_order_date', _dlg), new Date());
			$('#txt-create_order_date', _dlg).trigger('change');
			_dlg.dialog("open");
		}
	}
}

function __fncManageExpired(blnIsCheckValue) {
	var _blnIsCheckValue = blnIsCheckValue || false;
	if (! _blnIsCheckValue) {
		$('.cls-is-expired').addClass('hidden');
	} else {
		$('input.cls-is-expired').each(function() {
			var _elem = $(this);
			if (_elem.length <= 0) return true;
			if (getValue(_elem, false)) _elem.addClass('has-value');
		});
		$('option.cls-is-expired').each(function() {
			var _opt = $(this);
			if (_opt.length <= 0) return true;

			_opt.attr('title', "- OBSOLETED -");
			if (_opt.html().indexOf("- obsoleted -") < 0) _opt.html(_opt.html() + ' - obsoleted -');
			var _cat_id = _opt.val();
			var _tbl = $('table.tbl_size_cat#cat_id_' + _cat_id);
			if (_tbl.length <= 0) return true;
			
			if ((! _opt.is(':selected')) && ($('input.cls-is-expired.has-value', _tbl).length <= 0)) {
				_tbl.addClass('hidden');
				_opt.addClass('hidden');				
			} else {
				_tbl.addClass('has-value');
			}			
		});
		// cut
		$('table.tbl_size_cat:not(.has-value)').each(function() {
			var _tbl = $(this);
			$('tr th.cls-col-size-txt.cls-is-expired', _tbl).each(function() {
				var _indx = $(this).index();
				var _tdPrc = $('tr td.cls-col-size-price.cls-is-expired:eq(' + _indx + ')', _tbl);
				var _tdQty = $('tr td.cls-col-size-qty.cls-is-expired:eq(' + _indx + ')', _tbl)
				if (
					(! $('input.sp-price', _tdPrc).hasClass('has-value')) 
					&& (! $('input.sq-qty', _tdQty).hasClass('has-value'))
				) {
					$(this).addClass('hidden');
					$('tr th.cls-col-size-chest.cls-is-expired:eq(' + _indx + ')', _tbl).addClass('hidden');
					_tdPrc.addClass('hidden');
					_tdQty.addClass('hidden');
				}
			});
		});
	}
}