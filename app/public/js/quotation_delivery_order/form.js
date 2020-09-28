var _autoSearch_OnLoad = false;
$(function() {
/*
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
*/
	_tableToolButtons.push(
		{"text": "&nbsp;","className": "DTTT_button_space"}
		, {
			"text": "ออกใบนำส่งสินค้า","className": "DTTT_button_add_row"
			,"action": function ( nButton, oConfig, oFlash ) {
				if (! $(nButton).is('.DTTT_button_disabled')) _fncDoPrepareInsertDeliveryOrder();
			}
		}
	);
	
	_DT_BASE_OPTIONS["rowCallback"] = function(row, data) {
		var _order_type = ('order_type' in data) ? parseInt(data['order_type']) : 0;
		if (_order_type == 0) {
			$(row).addClass('cls-tr-delivery');
		}
	};

	$('#divPrepareInsertDeliver').dialog({
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
	
	$('#divPanelHandler').remove();
	if (typeof doSearch == 'function') doSearch(false);	
});
/*
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
*/
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

function fnc__DDT_Row_RenderDeliverDetailQtyControl(data, type, full) {
	var _order_type = ('order_type' in full) ? parseInt(full['order_type']) : 0;
	var _elPanel = $('<div>');
	var _div = $('<div>').appendTo(_elPanel);
	if (_order_type == 0) {
		var _deliver_rowid = ('deliver_rowid' in full) ? parseInt(full['deliver_rowid']) : -1;
		var _total_deliver_qty = ('total_deliver_qty' in full) ? parseInt(full['total_deliver_qty']) : -1;
		if (_deliver_rowid > 0) {
			var _href = './delivery/pass_command/1/' + _deliver_rowid;
			_div.append($('<span>' + _total_deliver_qty + '<span><a href="' + _href + '" class="cls-link-view-delivery"><img src="public/images/icons/16/link.png" class="cls-img-disp-details" title="เรียกดูใบนำส่งสินค้า"></a>'));
		} else {
			_div.append($('<span>n/a</span>'));
		}
	} else {
		var _left_qty = ('left_qty' in full) ? parseInt(full['left_qty']) : 0;
		var _org_qty = ('original_qty' in full) ? parseInt(full['original_qty']) : 0;
		var _deliverable = ('deliverable' in full) ? parseInt(full['deliverable']) : -1;
		if (_deliverable > 0) {
			if (_left_qty > 0) {
				_div.append($('<input type="text" class="cls-txt-deliver-qty cls-user-input input-integer" left_qty="' + _left_qty + '" data="deliver_qty" title="เลือกเพื่อนำส่ง">'))
					.append($('<span>&nbsp;/&nbsp;' + _left_qty + '</span>'));
			} else {
				_div.append($('<span class="cls-empty-qty-detail">0 / ' + formatNumber(_org_qty, 0) + '</span>'));
			}
		} else {
			_div.append($('<span>อยู่ระหว่างขั้นตอนการผลิต</span>'));
		}
	}
	return _elPanel.html();	
}

function _fncDoPrepareInsertDeliveryOrder() {
	var _arr_obj = [];
	doClearVldrError();
	doClearDisplayError(1, "NO_DELIVER_QTY");
	var _blnError = false;
	$('.cls-txt-deliver-qty').each(function() {
		var _elQty = $(this);
		var _qty = _elQty.val() || 0;
		if (_qty <= 0) return true;
		var _left_qty = parseInt(_elQty.attr('left_qty')) || 0;
		if (_qty > _left_qty) {
			doSetVldrError(_elQty, 'deliver_qty', 'InvalidAssignQuantity', 'Assigned qty. greater than remaining qty.', 1);
			_blnError = true;
		} else {
			var _rowData = _objDataTable.fnGetData(_elQty.parents('tr')[0]);
			_arr_obj.push($.extend({}, _rowData, { "deliver_qty": _qty, "left_qty": _left_qty }));
		}
	});
	if ((_blnError)) {
		return false;
	} else if ((_arr_obj.length <= 0)) {
		doDisplayError("NO_DELIVER_QTY", "กรุณาเลือกรายละเอียดจำนวนนำส่ง อย่างน้อย 1 รายการ", true, 1);
		return false;
	}
	
	var _dlg = $('#divPrepareInsertDeliver');
	_setEnableElem($('#acc-job_number', _dlg), false);
	$('#txt-deliver_date', _dlg).datepicker("setDate", new Date());
	$('#tbl_detail tbody tr:not(#edit_panel)', _dlg).remove();

	var _customer_rowid = -1, _customer = '', _company = '', _tel = '', _arr_full_address = '';
	var _is_vat = 0, _is_tax_inv_req = false;
	var _total_deposit_payment = 0, _total_close_payment = 0;
	for (var _i=0;_i<_arr_obj.length;_i++) {
		var _ea = _arr_obj[_i];
		var _deliver_qty = ('deliver_qty' in _ea) ? parseInt(_ea['deliver_qty']) : -1;
		if (_deliver_qty <= 0) continue;

		_tel = ('tel' in _ea) ? _ea['tel'] : '';
		_arr_full_address = ('arr_full_address' in _ea) ? _ea['arr_full_address'] : '';
		var _left_qty = ('left_qty' in _ea) ? parseInt(_ea['left_qty']) : -1;
		_is_vat = ('is_vat' in _ea) ? parseInt(_ea['is_vat']) : 0;
		if ((_is_tax_inv_req == false) && ('is_tax_inv_req' in _ea) && (_ea['is_tax_inv_req'] > 0)) _is_tax_inv_req = true;
		var _type_id = ('type_id' in _ea) ? parseInt(_ea['type_id']) : -1;
		if (_type_id > 0) _setElemValue($('#chk-product_deliver_' + _type_id, _dlg), _type_id);
		var _order_rowid = ('order_rowid' in _ea) ? parseInt(_ea['order_rowid']) : -1;
		var _order_detail_rowid = ('order_detail_rowid' in _ea) ? parseInt(_ea['order_detail_rowid']) : -1;
		var _disp_title = ('disp_title' in _ea) ? _ea['disp_title'] : '';
		var _description = ('description' in _ea) ? _ea['description'] : '';
		var _job_number = '', _type = '', _category = '';
		if (_disp_title.length > 0) {
			var _arr = _disp_title.split(':');
			if (_arr.length > 1) _job_number = _arr[1].trim();
		}
		if (_description.length > 0) {
			var _arr = _description.split(' ');
			if (_arr.length > 1) {
				_type = _arr[0].trim();
				_category = _arr[1].trim();
			}
		}

		if ($('#divDispSelectedJobNumber .cls-selected-jobnumber[type_id="' + _type_id + '"][order_rowid="' + _order_rowid + '"]').length == 0) {
			_customer_rowid = ('customer_rowid' in _ea) ? parseInt(_ea['customer_rowid']) : -1;
			_customer = ('customer' in _ea) ? _ea['customer'] : '';
			_company = ('company' in _ea) ? _ea['company'] : '';
			//if (typeof _arr_full_address == 'string') _arr_full_address = JSON.parse(_arr_full_address);
			var _div = $('<div>').addClass('cls-selected-jobnumber')
				.attr('rowid', _order_detail_rowid)
				.attr('type_id', _type_id)
				.attr('order_rowid', _order_rowid)
				.attr('job_number', _job_number)
				.attr('category', _category)
				.attr('type', _type)
				.attr('customer', _customer)
				.attr('company', _company)
				.html(_job_number)
				//.append('<div class="cls-remove-selected-jobnumber eventView-hide"></div>')
				.appendTo($('#divDispSelectedJobNumber'))
			;
		}

		var _total_price_each = ('total_price_each' in _ea) ? parseFloat(_ea['total_price_each']) : 0;
		var _avg_deposit_payment = ('avg_deposit_payment' in _ea) ? parseFloat(_ea['avg_deposit_payment']) : 0;
		var _avg_close_payment = ('avg_close_payment' in _ea) ? parseFloat(_ea['avg_close_payment']) : 0;
		var _str = '<tr rowid="-1" type_id="' + _type_id + '" order_rowid="' + _order_rowid + '" order_detail_rowid="' + _order_detail_rowid + '" left_qty="' + _left_qty + '" avg_deposit_payment="' + _avg_deposit_payment + '" avg_close_payment="' + _avg_close_payment + '" >';
		_str += '<td>' + _deliver_qty + '</td>';
		_str += '<td>' + _description + '</td>';
		_str += '<td>' + formatNumber(_total_price_each, 2) + '</td>';
		_str += '<td>' + formatNumber(_total_price_each * _deliver_qty, 2) + '</td>';
		_str += '<td class="control-button"><img src="public/images/edit.png" class="ctrl-edit" title="Edit"><img src="public/images/b_delete.png" class="ctrl-delete" title="Delete"></td></tr>';

		$('#tbl_detail tbody').append(_str);

		_total_deposit_payment += (_avg_deposit_payment * _deliver_qty);
		_total_close_payment += (_avg_close_payment * _deliver_qty);
	}
	$('#tbl_detail tbody tr#edit_panel').detach().appendTo($('#tbl_detail tbody')); //.hide()
	$('#tbl_detail').triggerHandler('evntRowChanged');

	_setElemValue($('#aac-customer_display', _dlg), _customer);
	_setEnableElem($('#aac-customer_display', _dlg), false);
	if (_company.trim().length > 0) _customer = _customer + ' [' + _company + ']';
	_evntCustomerSelected('set', {
		'item': {
			"value": _customer
			, "rowid": _customer_rowid
			, "customer_name": _customer
			, "company": _company
			, "tel": _tel
			, "addresses": _arr_full_address
		}
	});

	_setElemValue($('#sel-is_vat', _dlg), _is_vat);
	_setEnableElem($('#sel-is_vat', _dlg), false);
	if (_is_tax_inv_req) _setElemValue($('#chk-attachment_1', _dlg), true);

	var _str_deposit_date = ('str_deposit_date' in _ea) ? _ea['str_deposit_date'] : '';
	var _str_close_date = ('str_close_date' in _ea) ? _ea['str_close_date'] : '';
	var _deposit_route = ('deposit_route' in _ea) ? _ea['deposit_route'] : '';
	var _close_route = ('close_route' in _ea) ? _ea['close_route'] : '';
	if (_str_deposit_date != '') _setElemValue($('#txt-deposit_date', _dlg), _str_deposit_date);
	if (_deposit_route != '') _setElemValue($('#sel-deposit_route', _dlg), _deposit_route);
	_setElemValue($('#txt-deposit_amount', _dlg), _total_deposit_payment);
	if (_str_close_date != '') _setElemValue($('#txt-payment_date', _dlg), _str_close_date);
	if (_close_route != '') _setElemValue($('#sel-payment_route', _dlg), _close_route);
	_setElemValue($('#txt-payment_amount', _dlg), _total_close_payment);
	
	_setEnableElem($('#txt-deposit_date', _dlg), false);
	_setEnableElem($('#sel-deposit_route', _dlg), false);
	_setEnableElem($('#txt-deposit_amount', _dlg), false);
	_setEnableElem($('#txt-payment_date', _dlg), false);
	_setEnableElem($('#sel-payment_route', _dlg), false);
	_setEnableElem($('#txt-payment_amount', _dlg), false);
	$('#btnFormReset').hide();

	_doRecalAmount();
	_dlg.dialog( 'open' ).focus();
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