$(document).ready(function () {
	var _editDlg = $(".cls-frm-edit").parents(".cls-div-form-edit-dialog");
	if (_editDlg.length > 0) {
		_editDlg.each(function() {
			$(this).on("dialogopen", function(event, ui) {
				var _btnSubmit = $("#frm_edit").find("#btnFormSubmit");
				if ((_btnSubmit.length > 0) && (_btnSubmit.is(':visible'))) __global._blnSetLock('ACT_EditDlg', '-\nแบบฟอร์มแก้ไขข้อมูลกำลังทำงาน อาจมีข้อมูลที่ยังไม่ได้บันทึก\n    - กรุณาตรวจสอบก่อนทำการยืนยันการออก', true);
				return false;
			});
			$(this).on("dialogclose", function(event, ui) {
				if (__global._objGetLock('ACT_EditDlg') || false) __global._blnUnLock('ACT_EditDlg');
				return false;
			});
		});
	}
    //validation on change
    $(document).on('change', '.input-integer:not(.no-validate)', function (ev) {
		doClearVldrErrorElement(this);
		blnValidateElem_TypeInt(this);
	});
    $(document).on('change', '.input-double:not(.no-validate)', function (ev) {
		doClearVldrErrorElement(this);
		blnValidateElem_TypeDouble(this);
	});
	//first set disabled 
	$(".user-input.set-disabled").each(function () {
		_setEnableElem(this, false);
	});
	//clear invalid validator on changed
	$(document).on('change', '.user-input.input-invalid', function() {
		doClearVldrErrorElement(this);
		blnValidateElem(this);
	});
	$(document).on('click', 'label.cls-radio-label[for]', function() {
		//var _prnt = $(this).parents()[0];
		//var _dst = $('#' + $(this).attr('for'), _prnt);
		var _dst = $(this).prev();
		if (_dst.length > 0) _dst.trigger('click');
		return false;
	});
	//resize windows update display screen
	_joomlaIframeWrapper_resizeToFit();
	$(window).on('resize', function() { _joomlaIframeWrapper_resizeToFit() });
});

__fnc_filterNotNestedHiddenClass = function(index) {
	return ((! $(this).hasClass('hidden')) && ($(this).parents('.hidden').length == 0));
};

function _joomlaIframeWrapper_resizeToFit() {
	if ((window.parent) && (window.parent.frames.length > 0)) {
		window.parent.frames[0].frameElement.style.height = (window.parent.innerHeight - 220) + 'px'; //current window height - template header / footer // window.parent.document.body.scrollHeight
		//page real height //(window.document.body.scrollHeight + 50) + 'px'; //$(window.document).height()
	}
}

var __global = new __clsGlobal();
$(function() {
	$(window).on('beforeunload', function(event){
		var _arr = __global._arrListCurrentLockMessage_Strict();
		if (_arr.length > 0) {
			var _msg = _arr.join('\n    - ');
			if (_msg.length > 0) _msg = '    - ' + _msg + '\n\n';
			_msg = 'PAGE_LOCKED: [ CAUTION ] Strict mode!\n\nLeaving page may cause some unsaved data loss.\nPlease recheck lock messages.\n\n' + _msg;
			event.returnValue = _msg;
			return _msg;
		} else {
			_arr = __global._arrListCurrentLockMessage_Normal();
			if (_arr.length > 0) {
				var _msg = _arr.join('\n    - ');
				if (_msg.length > 0) _msg = '    - ' + _msg + '\n\n';
				_msg = 'PAGE_LOCKED: Normal mode.\n\n' + _msg;
				event.returnValue = _msg;
				return _msg;
			}
		}
	});
});

function __clsGlobal() {
	this.__arrPAGE_LOCKS = [];
	
	this._blnSetLock = function (key, message, blnStrict) {
		var _key = ((typeof key == 'string') && (key.trim() != ''))?key.trim():false;
		var _msg = (typeof message == 'string')?message.trim():'';
		var _bln_strick = (typeof blnStrict == 'boolean')?blnStrict:false;
		if (_key === false) return false;
		var _exist = this._objGetLock(_key);
		if (!_exist) {
			this.__arrPAGE_LOCKS.push({'key': _key, 'message': _msg, 'strict': _bln_strick, 'lock': true});
		} else {
			if (_exist['message'] != _msg) {
				if (_exist['message'] != '') _exist['message'] += ', ';
				_exist['message'] += _msg;
			}
			_exist['lock'] = (_exist['lock'] || true);
			_exist['strict'] = (_exist['strict'] || _bln_strick);
		}
		return true;
	};
	this._blnUnLock = function (key) {
		var _key = ((typeof key == 'string') && (key.trim() != ''))?key.trim():false;
		if (_key === false) return false;
		return _removeObjectFromList(this.__arrPAGE_LOCKS, 'key', _key);
	};
	this._objGetLock = function (key) {
		var _key = ((typeof key == 'string') && (key.trim() != ''))?key.trim():false;
		if (_key === false) return false;
		return _listObjectInList(this.__arrPAGE_LOCKS, 'key', _key) || false;
	};
	this._blnSetLock_Normal = function (key, message) {
		return this._blnSetLock(key, message);
	};
	this._blnSetLock_Strict = function (key, message) {
		return this._blnSetLock(key, message, true);
	};	
	this.__arrGetLockObjectList = function (objSpecCond) {
		var _obj_special_cond = (typeof objSpecCond == 'object')?objSpecCond:{};
		var _arrLockedObj = [];
		for (var _i=0;_i<this.__arrPAGE_LOCKS.length;_i++) {
			var _ea = this.__arrPAGE_LOCKS[_i];
			if (('lock' in _ea) && (_ea['lock'] == true)) {
				if (('strict' in _obj_special_cond) && (typeof _obj_special_cond['strict'] == 'boolean')) {
					if (_obj_special_cond['strict'] == _ea['strict']) _arrLockedObj.push($.extend({}, _ea));
				} else {
					_arrLockedObj.push($.extend({}, _ea));
				}		
			}
		}
		return _arrLockedObj;
	};
	this._isPageLocked = function() {
		var _arr = this.__arrGetLockObjectList();
		return (_arr.length > 0);
	}
	this._isPageStrictLocked = function() {
		var _arr = this.__arrGetLockObjectList({'strict':true});
		return (_arr.length > 0);
	};
	this._arrListCurrentLockKey = function() {
		var _arr_list_key = [];
		var _arr = this.__arrGetLockObjectList();
		for (var _i=0;_i<_arr.length;_i++) {
			if (_arr[_i]['key']) _arr_list_key.push(_arr[_i]['key']);
		}
		return _arr_list_key;
	};
	this._arrListCurrentLockKey_Normal = function() {
		var _arr_list_key = [];
		var _arr = this.__arrGetLockObjectList({'strict':false});
		for (var _i=0;_i<_arr.length;_i++) {
			if (_arr[_i]['key']) _arr_list_key.push(_arr[_i]['key']);
		}
		return _arr_list_key;
	};
	this._arrListCurrentLockKey_Strict = function() {
		var _arr_list_key = [];
		var _arr = this.__arrGetLockObjectList({'strict':true});
		for (var _i=0;_i<_arr.length;_i++) {
			if (_arr[_i]['key']) _arr_list_key.push(_arr[_i]['key']);
		}
		return _arr_list_key;
	};
	this._arrListCurrentLockMessage = function() {
		var _arr_list_msg = [];
		var _arr = this.__arrGetLockObjectList();
		for (var _i=0;_i<_arr.length;_i++) {
			if (_arr[_i]['key']) {
				var _str = _arr[_i]['key'];
				if (_arr[_i]['message']) _str += ': ' + _arr[_i]['message'];
				_arr_list_msg.push(_str);
			}
		}
		return _arr_list_msg;
	};
	this._arrListCurrentLockMessage_Normal = function() {
		var _arr_list_msg = [];
		var _arr = this.__arrGetLockObjectList({'strict':false});
		for (var _i=0;_i<_arr.length;_i++) {
			if (_arr[_i]['key']) {
				var _str = _arr[_i]['key'];
				if (_arr[_i]['message']) _str += ': ' + _arr[_i]['message'];
				_arr_list_msg.push(_str);
			}
		}
		return _arr_list_msg;
	};
	this._arrListCurrentLockMessage_Strict = function() {
		var _arr_list_msg = [];
		var _arr = this.__arrGetLockObjectList({'strict':true});
		for (var _i=0;_i<_arr.length;_i++) {
			if (_arr[_i]['key']) {
				var _str = _arr[_i]['key'];
				if (_arr[_i]['message']) _str += ': ' + _arr[_i]['message'];
				_arr_list_msg.push(_str);
			}
		}
		return _arr_list_msg;
	};
}

if (typeof __clsAutoReloadSearch == 'undefined') {
	var _DEFAULT_RELOAD_INTERVAL = 30;
	function __clsAutoReloadSearch() {
		this._reloadHandler = false;
		this._reloadInterval = 60000;
		this._reloadFunction = false;
		this._canceling = false;
		this._isDebug = false;
		var _self = this;
		this.start = function(fncCall, interval, isDebug) {
			if ((! isNaN(interval)) && (parseInt(interval) > 0)) {
				_self._reloadInterval =  parseInt(interval);
			}		
			if ((typeof fncCall == 'function')) {
				_self._reloadFunction = fncCall;
			}
			_self._isDebug = (typeof isDebug != 'undefined');
			if (_self._isDebug) _self._doLog('autoReload - Starting');
			if ((typeof _self._reloadFunction != 'function') && (typeof doSearch == 'function')) {
				if (_self._isDebug) _self._doLog('autoReload - destinate function not defined, use default "doSearch".');
				_self._reloadFunction = function() {
					if (($('.ui-dialog').length > 0) && ($('.ui-dialog').is(':visible'))) return false;
					if (_currentDataString.length > 0) doSearch.call(_self, false);
					return false;
				};
			}
			if (typeof _self._reloadFunction != 'function') {
				if (_self._isDebug) _self._doLog('autoReload - invalid destinate function or no function defined.');
				return false;
			}
			if (_self._reloadInterval <= 0) {
				if (_self._isDebug) _self._doLog('autoReload - invalid interval ( ' + _self._reloadInterval + ' ), use default ( ' + _DEFAULT_RELOAD_INTERVAL + 'ms. ).');
				_self._reloadInterval = _DEFAULT_RELOAD_INTERVAL;
			}
			_self._count = 0;
			_self._reloadHandler = setInterval(function() {
					_self._count++;
					if (_self._canceling == false) {
						if (_self._isDebug) _self._doLog('autoReload - triggering#' + _self._count);
						_self._reloadFunction.call();
					} else {
						_self._reloadHandler = false;
						_self._canceling = true;
						if (_self._isDebug) _self._doLog('autoReload - Cleared');
					}
				}, _self._reloadInterval);
			if (_self._isDebug) _self._doLog('autoReload - started with ' + _self._reloadInterval + 'ms. interval.');
		};
		this.stop = function() {
			if (_self._isDebug) _self._doLog('autoReload - Stopping');
			if (_self._reloadHandler) clearInterval(_self._reloadHandler);
			_self._canceling = true;
			_self._reloadHandler = false;
		};
		this.isActive = function() {
			if (_self._reloadHandler !== false) {
				return true;
			} else {
				return false;
			}
		};
		this._doLog = function (str) {
			var _str = (str) ? str.toString() : '-NONE-';
			console.log((new Date()).format('H:MM:ss:L') + ' : ' + _str);
			return false;
		};
	}
}