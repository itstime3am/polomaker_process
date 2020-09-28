var _DIV_PAYMENT_DLG, _objDP, _objAFDP;
$(function() {
	if (! (_objDP && _objAFDP)) {
		_objDP = new _obj_payment_panel('divDepositPayment');
		_objAFDP = new _obj_payment_panel('divAfterDepositPayment');

		_DIV_PAYMENT_DLG = $('div#div_payment_dialog').dialog({
			height: 400
			, width: 960
			, show: {effect:"puff",duration: 1000}
			, hide: {effect:"fade",duration:1000}
			, resizable: true
			, modal: true
			, closeOnEscape: true
			, autoOpen: false
			, beforeClose: function(event, ui) {
				if (_objDP.__isEditingRow() || _objAFDP.__isEditingRow()) {
					if (confirm('พบรายการเปลี่ยนแปลงที่ยังไม่ได้บันทึก, เลือก OK เพื่อกลับไปตรวจสอบ หรือ CANCEL เพื่อปิดฟอร์มโดยยกเลิกการเปลี่ยนแปลงล่าสุด')) {
						return false
					}
				}
			}
			, buttons: {
				'Close': function() {
					$(this).dialog('close');
				}
			}
		});

		$("div.cls-tab-container ul").find("a").each(function() {
			var href = $( this ).attr( "href" );
			if ( href.indexOf( "#" ) == 0 ) {
				var newHref = window.location.protocol + '//' + window.location.hostname + window.location.pathname + href;
				$(this).attr("href", newHref);
			}
		});
		
		$('#div_payment_tabs').tabs(); //{ active: 0 }
	}
});

function _openPaymentListDialog(objData) {
	var _objData = $.isPlainObject(objData) ? objData : {};
	$('table.cls-tbl-payment', _DIV_PAYMENT_DLG).each(function() {
		var _objPT, _tbl, _obj = false;
		if ($(this).is('.cls-tbl-deposit')) {
			_objPT = _objDP;
			//_tbl = _objDP._tblPayment;
			if (('deposit' in _objData) && ($.isPlainObject(_objData['deposit']))) {
				_obj = _objData['deposit'];
				$('#div_payment_tabs').tabs( 'enable', 0 );
				if (('is_enable' in _obj) && (_obj['is_enable'] == false)) {
					$('#div_payment_tabs').tabs( "option", "disabled", [0]); //.tabs({ disable: 0 });
					$('#div_payment_tabs').tabs( "option", "active", 1 );
				}
			}
		} else if ($(this).is('.cls-tbl-after-deposit')) {
			_objPT = _objAFDP;
			//_tbl = _objAFDP._tblPayment;
			if (('after_deposit' in _objData) && ($.isPlainObject(_objData['after_deposit']))) {
				_obj = _objData['after_deposit'];
				$('#div_payment_tabs').tabs( 'enable', 1 );
				if (('is_enable' in _obj) && (_obj['is_enable'] == false)) {
					$('#div_payment_tabs').tabs( "option", "disabled", [1]); //.tabs({ disable: 1 });
					$('#div_payment_tabs').tabs( "option", "active", 0 );
				}
			}
		}
		_tbl = _objPT._tblPayment;
		if ((_tbl.length < 1) || (! _obj)) return false;

		/*
		var _tmp_is_editable = (_tbl.attr('editable') == 'editable');
		var _is_editable = (((('is_editable' in _obj)) ? (_obj['is_editable']) : true) && _tmp_is_editable);
		var _tmp_is_approveable = (_tbl.attr('approveable') == 'approveable');
		var _is_approveable = (((('is_approveable' in _obj)) ? (_obj['is_approveable']) : false) && _tmp_is_approveable)
		*/
		var _is_editable = (_tbl.attr('editable') === 'false') ? false : ((('is_editable' in _obj)) ? ((_obj['is_editable']) && (_tbl.attr('editable') != 'undefined')) : false);
		var _is_approveable = (_tbl.attr('approveable') === 'false') ? false : ((('is_approveable' in _obj)) ? ((_obj['is_approveable']) && (_tbl.attr('approveable') != 'undefined')) : false);

		if (! _is_editable) _objPT._trEditPanel.addClass('hidden');

		var _const = $(_tbl).attr('_CONST') || '{}';
		if (typeof _const == 'string') _const = JSON.parse(_const);
		if (('constant' in _obj)) {
			_cst = _obj['constant'];
			if (typeof _cst == 'string') _cst = JSON.parse(_cst);
			_const = $.extend(true, _const, _cst);
		}
		$(_tbl).removeAttr('_CONST');
		$(_tbl).attr('_CONST', JSON.stringify(_const));

		$('tbody', _tbl).empty();
		if (('arr_payment_list' in _obj) && ($.isArray(_obj['arr_payment_list']))) {
			var _arr_payment_list = _obj['arr_payment_list'];
			for(var _i=0;_i < _arr_payment_list.length;_i++) {
				var _row = _arr_payment_list[_i];
				_objPT._insertDetailRow(
					{
						"rowid": _row['rowid']
						, "payment_datetime": _row["payment_datetime"]
						, "payment_route_rowid": _row["payment_route_rowid"]
						, "payment_route": _row["payment_route"]
						, "amount": _row["amount"]
						, "description": _row["description"]
						, "is_approve": _row["is_approve"]
						, "is_cancel": _row["is_cancel"]
					}
					, _is_editable
					, _is_approveable
				);
			}
		}
		
		var _objTA = _objPT.__objGetTotalAmount() || false;
		if (_objTA) {
			$('.cls-grand-total .cls-spn-count', _objPT._srcDivContainer).html(formatNumber(_objTA.total.count, 0));
			$('.cls-grand-total .cls-spn-amount', _objPT._srcDivContainer).html(formatNumber(_objTA.total.amount));
			$('.cls-approved-total .cls-spn-count', _objPT._srcDivContainer).html(formatNumber(_objTA.approved.count, 0));
			$('.cls-approved-total .cls-spn-amount', _objPT._srcDivContainer).html(formatNumber(_objTA.approved.amount));
			$('.cls-pending-total .cls-spn-count', _objPT._srcDivContainer).html(formatNumber(_objTA.waiting.count, 0));
			$('.cls-pending-total .cls-spn-amount', _objPT._srcDivContainer).html(formatNumber(_objTA.waiting.amount));
			if ($(this).is('.cls-tbl-deposit')) {
				_apprv_dep = _objTA.approved.amount;
				_pending_dep = _objTA.waiting.amount;
			} else if ($(this).is('.cls-tbl-after-deposit')) {
				_apprv_payment = _objTA.approved.amount;
				_pending_payment = _objTA.waiting.amount;
			}
		}
	});

	var _status_rowid = (("status_rowid" in _objData) && (! isNaN(_objData['status_rowid']))) ? parseFloat(_objData['status_rowid']) : -1;
	var _grand_total = (("grand_total" in _objData) && (! isNaN(_objData['grand_total']))) ? parseFloat(_objData['grand_total']) : 'n/a';
	var _deposit_amount = (("deposit_amount" in _objData) && (! isNaN(_objData['deposit_amount']))) ? parseFloat(_objData['deposit_amount']) : 'n/a';
	var _strTitle = '';

	if (_status_rowid < 70) {
		if (! isNaN(_deposit_amount)) _strTitle += 'ยอดมัดจำที่ต้องชำระ ' + formatNumber(_deposit_amount) + ' บาท';
		var _dblSum = _apprv_dep + _pending_dep;
		if (! isNaN(_dblSum)) _strTitle += ' - ยอดมัดจำบันทึกแล้ว ' + formatNumber(_dblSum) + ' บาท ';
		var _dblLeft = _deposit_amount - _dblSum;
		if (! isNaN(_dblLeft)) {
			if (_dblLeft > 0) {
				_strTitle += ' [[ คงเหลือยอดมัดจำค้างชำระ ' + formatNumber(_dblLeft) + ' บาท ]]';
			} else {
				_strTitle += ' [[ ไม่มียอดมัดจำค้างชำระ ]]';
			}
		}
	} else if (_status_rowid > 0) {
		if (! isNaN(_grand_total)) _strTitle += 'ยอดรวมที่ต้องชำระ ' + formatNumber(parseFloat(_grand_total)) + ' บาท';
		var _dblSum = _apprv_dep + _pending_dep + _apprv_payment + _pending_payment;
		if (! isNaN(_dblSum)) _strTitle += ' - ยอดที่บันทึกแล้ว ' + formatNumber(_dblSum) + ' บาท ';
		var _dblLeft = _grand_total - _dblSum;
		if (! isNaN(_dblLeft)) {
			if (_dblLeft > 0) {
				_strTitle += ' [[ คงเหลือยอดค้างชำระ ' + formatNumber(_dblLeft) + ' บาท ]]';
			} else {
				_strTitle += ' [[ ไม่มียอดค้างชำระ ]]';
			}
		}
	}
	_DIV_PAYMENT_DLG.dialog('option', 'title', _strTitle);

	if (_DIV_PAYMENT_DLG.dialog('isOpen')) {
		_DIV_PAYMENT_DLG.focus();
	} else {
		_DIV_PAYMENT_DLG.dialog("open").focus();
	}
}