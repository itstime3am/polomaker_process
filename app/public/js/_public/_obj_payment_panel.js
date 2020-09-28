function _obj_payment_panel(strDivID) {
	this._srcDivContainer = $('#' + strDivID);
	this._tblPayment = $('table.cls-tbl-payment', this._srcDivContainer);
	this._trEditPanel = $('tr.tr-edit-panel', this._tblPayment);
	this._CURRENT_ROW = false;
	this._index = this._srcDivContainer.attr('index') || 1;
	var self = this;

	$('tr.tr-edit-panel .cls-payment-route', this._tblPayment).combobox();

	$('tr.tr-edit-panel td .cls-payment-datetime', this._tblPayment).datetimepicker({
		showOn: "both"
		,changeYear: true
		,changeMonth: true
		,buttonImage: "public/images/select_day.png"
		,buttonImageOnly: true
		,dateFormat: 'dd/mm/yy'
		,timeFormat: 'HH:mm'
		,timeInput: true
		,maxDate: new Date()
	});

	$('tr.tr-edit-panel td .cls-payment-amount', this._tblPayment).on('change', function() {
		doClearVldrErrorElement(this)
		return blnValidateElem_TypeDouble(this);
	});

	$(this._tblPayment).on('click', '.cls-btn-submit', function() {
		var _tbl = self._tblPayment;

		var _url = $(_tbl).attr('_COMMIT_URL') || '';
		if (_url.trim() == '') {
			alert('Error:: form action url not found');
			return false;
		}

		var _const = $(_tbl).attr('_CONST') || {};
		if (typeof _const == 'string') _const = JSON.parse(_const);

		if (! blnValidateContainer(true, self._trEditPanel, '.user-input')) return false;

		var _isUpdate = false;
		var _rowid = -1;
		if ((self._CURRENT_ROW) && (self._CURRENT_ROW.attr('rowid') > 0)) { //(self._CURRENT_ROW.css('display') == 'none')
			_rowid = parseInt(self._CURRENT_ROW.attr('rowid'));
			_isUpdate = true;
		}
		var _selRoute = $('.cls-payment-route option:selected', self._trEditPanel);
		var _dblAmount = _cleanNumericValue(getValue($('.cls-payment-amount', self._trEditPanel), null));
		var _strAmount = (_dblAmount) ? formatNumber(_dblAmount, 2) : '';

		var _modal = _getOpenWaitModalDialog(MSG_DLG_HTML_COMMIT, MSG_DLG_TITLE_COMMIT);
		var _jsonData = $.extend({}, _const, {
				"payment_datetime": getValue($('.cls-payment-datetime', self._trEditPanel), null)
				, "payment_route_rowid": _selRoute.val()
				, "payment_route": _selRoute.text()
				, "amount": _dblAmount
				, "description": getValue($('.cls-payment-description', self._trEditPanel), null)
			});
		if (_rowid > 0) {
			_jsonData['rowid'] = _rowid;
		}
		$.ajax({
			type:"POST",
			url: _url,
			contentType:"application/json;charset=utf-8",
			dataType:"json",
			data: JSON.stringify(_jsonData),
			success: function(data, textStatus, jqXHR) {
				if (data.success == false) {
					doDisplayError('Commit Failed!', MSG_ALERT_COMMIT_FAILED.replace(/v_XX_1/g, data.error), true, self._index);
				} else {
					if (_isUpdate) {
						_arr = self._CURRENT_ROW.children();
						if (_arr.length > 3) {
							$(_arr[0]).html(_jsonData["payment_datetime"]);
							$(_arr[1]).html(_jsonData["payment_route"]);
							$(_arr[1]).attr("payment_route_rowid", _jsonData["payment_route_rowid"]);
							$(_arr[2]).html(formatNumber(_jsonData["amount"]));
							$(_arr[2]).attr("amount", _jsonData["amount"]);
							$(_arr[3]).html(_jsonData["description"] || '');
						}
						_doDisplayToastMessage('ทำการแก้ไขรายการชำระเงินเรียบร้อยแล้ว', 2, false);
					} else {
						var _new_rowid = ((('message' in data) && (_isInt(data.message))) ? parseInt(data.message) : -1);
						if (_new_rowid > 0) {
							self._insertDetailRow({
								"rowid": _new_rowid
								, "payment_datetime": _jsonData["payment_datetime"]
								, "payment_route_rowid": _jsonData["payment_route_rowid"]
								, "payment_route": _jsonData["payment_route"]
								, "amount": _jsonData["amount"]
								, "description": (_jsonData["description"] || '')
							});
							_doDisplayToastMessage('เพิ่มรายการชำระเงินใหม่สำเร็จ', 2, false);
						}
					}
					self.__fncReloadDataSource();
					$('.cls-btn-cancel', self._trEditPanel).trigger('click');
				}
				_forceCloseAllWaitModalDialog();
			}
			, error: function(jqXHR, textStatus, errorThrown) {
				doDisplayError(textStatus, errorThrown, false, self._index);
				_forceCloseAllWaitModalDialog();
			}
			, statusCode: {
				404: function() {
					doDisplayError("Not Found!", "Page not found", false, self._index);
					_forceCloseAllWaitModalDialog();
				}
			}
		});
		return false;
	});

	$(this._tblPayment).on('click', '.cls-btn-cancel', function() {
		var _tbl = self._tblPayment;
		
		if (self._CURRENT_ROW) self._CURRENT_ROW.css('display', '');
		self.__fncClearEditPanel();
		return false;
	});

	$(this._tblPayment).on('click', '.ctrl-edit', function() {
		var _tbl = self._tblPayment;
		
		if (self._CURRENT_ROW) $('.cls-btn-cancel', this._trEditPanel).trigger('click');

		self._CURRENT_ROW = $($(this).parents('tr').get(0));

		self.__fncPopulateEditPanel(self._CURRENT_ROW);
		return false;
	});

	$(this._tblPayment).on('click', '.ctrl-delete', function() {
		var _tr = $($(this).parents('tr')[0]);
		var _tbl = self._tblPayment;
		if ((_tr.length < 1)) return false;
		
		var _url = $(_tbl).attr('_DELETE_URL') || '';
		if (_url.trim() == '') {
			alert('Error:: form delete url not found');
			return false;
		}
		
		var _rowid = $(_tr).attr('rowid') || 0;
		if ((_rowid < 1)) {
			alert('Error:: "rowid" not found!');
			return false;
		}
		if (confirm(MSG_CONFIRM_DELETE_ROW.replace(/\(*\s+v_XX_1\s+\)*/, ''))) {				
			var _modal = _getOpenWaitModalDialog(MSG_DLG_HTML_DELETE, MSG_DLG_TITLE_DELETE);
			$.ajax({
				type:"POST",
				url: _url,
				contentType:"application/json;charset=utf-8",
				dataType:"json",
				data: JSON.stringify({ "rowid": _rowid }),
				success: function(data, textStatus, jqXHR) {
					if (data.success == false) {
						doDisplayError('Delete Failed!', MSG_ALERT_COMMIT_FAILED.replace(/v_XX_1/g, data.error), true, 3);
					} else {
						_tr.remove();
						_doDisplayToastMessage('ลบข้อมูลสำเร็จ', 2, false);
					}
					self.__fncReloadDataSource();
					_forceCloseAllWaitModalDialog();
				}
				, error: function(jqXHR, textStatus, errorThrown) {
					doDisplayError(textStatus, errorThrown, false, self._index);
					_forceCloseAllWaitModalDialog();
				}
				, statusCode: {
					404: function() {
						doDisplayError("Not Found!", "Page not found", false, self._index);
						_forceCloseAllWaitModalDialog();
					}
				}
			});
		}
		return false;
	});
	
	$(this._tblPayment).on('click', '.ctrl-approve', function() {
		self._fncDoUpdateApprovalStatus(this, 1);
		return false;
	});
	$(this._tblPayment).on('click', '.ctrl-reject', function() {
		self._fncDoUpdateApprovalStatus(this, -1);
		return false;
	});
	$(this._tblPayment).on('click', '.ctrl-undo', function() {
		self._fncDoUpdateApprovalStatus(this, 0);
		return false;
	});


	this._fncDoUpdateApprovalStatus = function(img, intApproveStatus) {
		var _tr = $($(img).parents('tr')[0]);
		if (_tr.length < 1) return false;

		var _tbl = self._tblPayment;
		var _url = $(_tbl).attr('_APPROVE_URL') || '';
		if (_url.trim() == '') {
			alert('Error:: form delete url not found');
			return false;
		}
		
		var _rowid = $(_tr).attr('rowid') || 0;
		if ((_rowid < 1)) {
			alert('Error:: "rowid" not found!');
			return false;
		}

		doClearVldrError($('div#div_payment_dialog'));
		var _apprStatus = intApproveStatus || 0;
		var _strConf, _strNtf;
		if (_apprStatus == 1) {
			_strConf = 'รับรองความถูกต้อง ของข้อมูลการชำระเงินเลขที่ #' + _rowid + ', กรุณากดยืนยัน';
			_strNtf = 'การรับรองความถูกต้อง';
		} else if (_apprStatus == -1) {
			_strConf = 'แจ้งเตือนเพื่อตรวจสอบข้อมูล ของข้อมูลการชำระเงินเลขที่ #' + _rowid + ', กรุณากดยืนยัน';
			_strNtf = 'การแจ้งเตือนผู้รับผิดชอบตรวจสอบข้อมูล';
		} else if (_apprStatus == 0) {
			_strConf = 'ยกเลิกการรับรองผลการตรวจสอบเอกส่าร ของข้อมูลการชำระเงินเลขที่ #' + _rowid + ', กรุณากดยืนยัน';
			_strNtf = 'การยกเลิกการรับรองผลการตรวจสอบเอกสาร';
		} else {
			return false;
		}
		if (confirm(_strConf)) {
			$.ajax({
				type:"POST",
				url: _url,
				contentType:"application/json;charset=utf-8",
				dataType:"json",
				data: JSON.stringify({ "rowid": _rowid, "is_approve": _apprStatus }),
				success: function(data, textStatus, jqXHR) {
					if (data.success == false) {
						doDisplayError(_strNtf + ' ล้มเหลว!', MSG_ALERT_COMMIT_FAILED.replace(/v_XX_1/g, data.error), true, self._index);
					} else {
						self.__fncGetApprovalControllers(_apprStatus, $('td.control-button', _tr));

						self.__fncReloadDataSource();
						_doDisplayToastMessage(_strNtf + 'สำเร็จ', 2, false);
					}
					_forceCloseAllWaitModalDialog();
				}
				, error: function(jqXHR, textStatus, errorThrown) {
					doDisplayError(textStatus, errorThrown, false, self._index);
					_forceCloseAllWaitModalDialog();
				}
				, statusCode: {
					404: function() {
						doDisplayError("Not Found!", "Page not found", false, self._index);
						_forceCloseAllWaitModalDialog();
					}
				}
			});
		}
	};

	this.__fncGetApprovalControllers = function(is_approve, container) {
		if (typeof is_approve == 'undefined') return false;	
		var _is_approve = is_approve;
		
		var _cntr = container || false;
		var _tbl = self._tblPayment;
		var _is_editable = (_tbl.attr('editable') == 'editable');
		var _is_approveable = (_tbl.attr('approveable') == 'approveable');

		var _strBtns = '';
		if (_is_approve > 0) {
			_strBtns += '<span class="cls-payment-approved">ยืนยันแล้ว</span>';
		} else if (_is_approve < 0) {
			_strBtns += '<span class="cls-payment-rejected">ตรวจสอบ</span>';		
		}
		if (_is_editable == true) {
			if (_is_approve <= 0) {
				_strBtns += '<img src="public/images/edit.png" class="cls-edit-ctrl ctrl-edit" title="แก้ไข" /><img src="public/images/b_delete.png" class="cls-edit-ctrl ctrl-delete" title="ยกเลิก" />';
			}
		}
		if (_is_approveable == true) {
			if (_is_approve == 0) {
				_strBtns += '<img src="public/images/icons/16/file-complete.png" class="cls-approve-ctrl ctrl-approve" title="ยืนยันเอกสาร" /><img src="public/images/icons/16/file-reject.png" class="cls-reject-ctrl ctrl-reject" title="กรุณาตรวจสอบข้อมูล" />';
			} else {
				_strBtns += '<img src="public/images/icons/16/undo.png" class="cls-undo-ctrl ctrl-undo" title="ยกเลิกสถานะ" />';
			}
		}
		if (_cntr) {
			$(_cntr).empty().html(_strBtns);
		} else {
			return _strBtns
		}
	};
	
	this._insertDetailRow = function(objNew, is_editable, is_approveable) {
		var _tbl = self._tblPayment;
		if ((! _tbl) || (_tbl.length < 1)) return false;
		
		var _rowid = (('rowid' in objNew) ? objNew['rowid'] : -1);
		if (_rowid < 1) return false;

		var _is_editable = true;
		if ((typeof is_editable == 'boolean')) {
			_is_editable = is_editable;

			_tbl.removeAttr('editable');
			if (_is_editable) _tbl.attr('editable', 'editable');
		} else {
			_is_editable = (_tbl.attr("editable") == 'editable');
		}
		var _is_approveable = false;
		if ((is_approveable)) {
			_is_approveable = (is_approveable || false);
			
			_tbl.removeAttr('approveable');
			if (_is_approveable) _tbl.attr('approveable', 'approveable');
		} else {
			_is_approveable = (_tbl.attr("approveable") == 'approveable');
		}
		
		var _strBtns = '', _payment_route = false, _str;
		var _payment_route_rowid = ('payment_route_rowid' in objNew) ? objNew.payment_route_rowid : -1;
		var _is_approve = (objNew.is_approve || 0);
		if (('payment_route' in objNew)) {
			_payment_route = objNew.payment_route;
		} else if ((_payment_route_rowid > 0)) {
			var _elmSel = $('.cls-payment-route', this._trEditPanel).get(0);
			var _opt = $('option', _elmSel).filter(function() { return ($(this).val() == _payment_route_rowid); });
			if (_opt.length > 0) _payment_route = _opt.text();
		}
		_str = '<tr rowid="' + _rowid + '" is_approve="' + _is_approve + '"><td>' + objNew.payment_datetime + '</td>';
		_str += '<td payment_route_rowid="' + _payment_route_rowid + '">' + _payment_route + '</td>';
		_str += '<td amount="' + objNew.amount + '">' + formatNumber(objNew.amount) + '</td>';
		_str += '<td>' + (((objNew.description + '').trim() != 'null') ? objNew.description.trim() : '')  + '</td>';

		_str += '<td class="control-button" from_qs="40"></td></tr>';
		
		var _tr = $(_str).appendTo($('tbody', _tbl));
		self.__fncGetApprovalControllers(_is_approve, $('td.control-button', _tr));
	};

	this.__fncPopulateEditPanel = function(current_row) {
		self._CURRENT_ROW = current_row || self._CURRENT_ROW;
		if (self._CURRENT_ROW) $(self._CURRENT_ROW).css('display', 'none');

		var _tbl = $(self._CURRENT_ROW).parents('table#tbl_payment')[0];

		_arrItems = $(self._CURRENT_ROW).children();
		setValue($('.cls-payment-datetime', this._trEditPanel), $(_arrItems[0]).html(), false);
		setValue($('.cls-payment-route', this._trEditPanel), $(_arrItems[1]).attr('payment_route_rowid'), false);
		_setElemValue($('.cls-payment-amount', this._trEditPanel), _cleanNumericValue($(_arrItems[2]).html()), true);
		_setElemValue($('.cls-payment-description', this._trEditPanel), $(_arrItems[3]).html(), false);

		$('.cls-btn-submit', this._trEditPanel).prop('title', 'Edit');
		$('.cls-btn-submit', this._trEditPanel).prop('act', 'update');
		$('.cls-btn-cancel', this._trEditPanel).prop('title', 'Cancel');
		$('.cls-btn-cancel', this._trEditPanel).prop('act', 'cancel');
		
		$(this._trEditPanel).detach().insertAfter(self._CURRENT_ROW).show();
	};

	this.__fncClearEditPanel = function() {
		var _tbl = self._tblPayment.filter(__fnc_filterNotNestedHiddenClass);

		doClearUserInput(this._trEditPanel);

		$('.cls-btn-submit', this._trEditPanel).prop('title', 'Insert');
		$('.cls-btn-submit', this._trEditPanel).prop('act', 'insert');
		$('.cls-btn-cancel', this._trEditPanel).prop('title', 'Reset');
		$('.cls-btn-cancel', this._trEditPanel).prop('act', 'reset');

		self._CURRENT_ROW = false;
		$(this._trEditPanel).detach().appendTo($('tfoot', _tbl));
	};

	this.__isEditingRow = function() {
		var _tbl = self._tblPayment.filter(__fnc_filterNotNestedHiddenClass);

		if (this._trEditPanel.hasClass('hidden')) return false;

		if (((getValue($('.cls-payment-datetime', this._trEditPanel), '').trim() != '') || (getValue($('.cls-payment-route', this._trEditPanel), 0) > 0) 
			|| (getValue($('.cls-payment-amount', this._trEditPanel), 0) > 0))) {
			return true;
		} else {
			return false;
		}
	};

	this.__fncReloadDataSource = function() {
		if ($('.cls-frm-edit[index=0]').is(':visible')) {
			var _elmHdnSrc = $('.cls-frm-edit[index=0] #hdn-arr_payment_log');
			var _arrCurrList = [];
			$('tbody tr:not(.tr-edit-panel)', self._tblPayment).each(function() {
				var _tr = $(this);
				var _arr = _tr.children();
				var _eaRow = {
					"rowid": (_tr.attr('rowid') || -1)
					, "payment_datetime": ($(_arr[0]).html() || '')
					, "payment_route_rowid": ($(_arr[1]).attr('payment_route_rowid') || 0)
					, "payment_route": ($(_arr[1]).html() || '')
					, "amount": ($(_arr[2]).attr('amount') || 0)
					, "description": ($(_arr[3]).html() || '')
					, "is_approve": (_tr.attr('is_approve') || 0)
				};
				_arrCurrList.push(_eaRow);
			});
			setValue(_elmHdnSrc, JSON.stringify(_arrCurrList), false);
		} else {
			if (typeof doSearch == 'function') doSearch(false);
		}
	};

	this.__objGetTotalAmount = function() {
		var _dblTotal = 0, _dblApproved = 0, _dblWait = 0, _intTotal = 0, _intApproved = 0, _intWait = 0;
		$('tbody tr:not(.tr-edit-panel)', self._tblPayment).each(function() {
			var _tr = $(this);
			var _arr = _tr.children();
			var _amount = parseFloat(($(_arr[2]).attr('amount') || 0));
			_dblTotal += _amount;
			_intTotal ++;
			var _is_appr = (_tr.attr('is_approve') || 0);
			if (_is_appr > 0) {
				_dblApproved += _amount;
				_intApproved ++;
			} else {
				_dblWait += _amount;
				_intWait ++;
			}
		});
		return {
			"total": { "count": _intTotal, "amount": _dblTotal }
			, "approved": { "count": _intApproved, "amount": _dblApproved }
			, "waiting": { "count": _intWait, "amount": _dblWait }
		};
	};
	
	return this;
}