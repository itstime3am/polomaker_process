String.prototype.trim=function(){return this.replace(/^\s\s*/, '').replace(/\s\s*$/, '');};
String.prototype.escapeHtml = function () {
	var map = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;'
	};
	return this.replace(/[&<>"']/g, function(m) { return map[m]; });
};
String.prototype.escapeQuotes = function () {
	var map = {
		'"': '&quot;',
		"'": '&#039;'
	};
	return this.replace(/["']/g, function(m) { return map[m]; });
};
function escapeQuotes(str) {
	var map = {
		'"': '&quot;',
		"'": '&#039;'
	};
	if (typeof str == "string") {
		return str.replace(/["']/g, function(m) { return map[m]; });
	} else {
		return "";
	}
}
String.prototype.escapePostgresQuoteIdent = function () {
	return this.replace(/\"\"/g, '\\\"');
};
function escapePostgresQuoteIdent(str) {
	if (typeof str == "string") {
		return str.replace(/\"\"/g, '\\\"');
	} else {
		return "";
	}
}
/*++ Array utils */
if(!Array.remove){
	Array.prototype.remove = function(from, to) {
		var rest = this.slice((to || from) + 1 || this.length);
		this.length = from < 0 ? this.length + from : from;
		return this.push.apply(this, rest);
	};
}
if(!Array.sum){
	Array.prototype.sum = function(selector) {
		if (typeof selector !== 'function') {
			selector = function(item) {
				return item;
			}
		}
		var sum = 0;
		for (var i = 0; i < this.length; i++) {
			sum += parseFloat(selector(this[i]));
		}
		return sum;
	};
}
if(!Array.indexOf){
	Array.prototype.indexOf = function(obj){
		for(var i=0; i<this.length; i++){
			if(this[i]==obj){
				return i;
			}
		}
		return -1;
	};
}
if (!Array.removeByValue) {
	Array.prototype.removeByValue = function() {
		var what, a = arguments, L = a.length, ax;
		while (L && this.length) {
			what = a[--L];
			while ((ax = this.indexOf(what)) !== -1) {
				this.splice(ax, 1);
			}
		}
		return this;
	};
}
if (!Array.intersection) {
	Array.prototype.intersection = function() {
		var _args = Array.prototype.slice.call(arguments);
		// if no array is passed then return empty array
		if (_args.length == 0) return [];
		
		var _arrSet = _args.slice();
		_arrSet.unshift(this);
		// for optimisation lets find the smallest array
		var _imin = 0;
		for(var _i = 1;_i<_arrSet.length;_i++)
			if (_arrSet[_i].length < _arrSet[_imin].length) _imin = _i;
		var _smallest = _arrSet.slice(_imin)[0];

		return _smallest.reduce(function (a, e) {
			for (var _i = 0;_i <_arrSet.length;_i++) if (_arrSet[_i].indexOf(e) == -1) return a;
			a.push(e);
			return a;
		}, []);
	}
}
/*-- Array utils */

function getAbsoluteURL(url) {
	var _div = document.createElement('div');
	_div.innerHTML = "<a></a>";
	_div.firstChild.href = url; // Ensures that the href is properly escaped
	_div.innerHTML = _div.innerHTML; // Run the current innerHTML back through the parser
	return _div.firstChild.href;
}

function _doSortArrObjByPropVal(arrObj, strPropName, blnDesc, strDataType) {
	var _strName = strPropName || false;
	if (! _strName) return false;
	if ((typeof arrObj != 'object') || (typeof arrObj.sort != 'function')) return false;
	var _blnDesc = blnDesc || false;
	var _dType = (strDataType || 'int').toString().trim().toLowerCase();
	var _compare = function(a, b) {
		var _valA = ((_strName in a) ? a[_strName] : -1);
		var _valB = ((_strName in b) ? b[_strName] : -1);
		if ((_dType == 'int')) {
			if (! isNaN(_valA)) _valA = parseInt(_valA);
			if (! isNaN(_valB)) _valB = parseInt(_valB);
		} else if ((_dType == 'dbl')) {
			if (! isNaN(_valA)) _valA = parseFloat(_valA);
			if (! isNaN(_valB)) _valB = parseFloat(_valB);
		}
		if ((_valA < 0) && (_valB < 0)) {
			return 0;			
		} else if (_valA < 0) {
			return 1;
		} else if (_valB < 0) {
			return -1;
		} else {
			if (_blnDesc == false) {
				if (_valA < _valB) return -1;
				if (_valA > _valB) return 1;
			} else {
				if (_valA < _valB) return 1;
				if (_valA > _valB) return -1;
			}
		}
		return 0;
	};
	arrObj.sort(_compare);
}

function _doFocusNext(strSelector, src) {
	var _elem = _toJQObj(src);
	var _selecter = strSelector || '';
	if ((_elem.length <= 0) || (_selecter.trim() == '')) return false;
	var _elemNext = $(_selecter + ':gt(' + ($(_selecter).index(_elem)) + '):visible');
	if (_elemNext.length > 0) _elemNext.each(function() {
		if (isEnable(this)) {
			$(this).focus();
			return false;
		}
	});
}

function formatNumber(nStr, digit, blnComma) {
	if (digit === undefined) digit = 2;
	if (blnComma === undefined) blnComma = true;
	fl = parseFloat(nStr);
	pow = Math.pow(10, digit);
	fl = Math.round(fl * pow) / pow;
	fx = fl.toFixed(digit);
	nStr = fx + '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? x[1] : '';
	if ((x2.length) < digit) {
		for (i=(digit - (x2.length));i<digit;i++) {
			x2 = x2 + '0';
		}
	}
	if (digit > 0) x2 = '.' + x2;

	if (blnComma) {
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
	}
	return x1 + x2;
}
/*
function _findObjectInList(objList, objKey, objValue) {
	var _objList = objList || [];
	var _objKey = objKey || '';
	var _objVal = objValue || '';
	for (var _x in _objList) {
		if ((typeof _objList[_x] == null) || (typeof _objList[_x] == 'function') || (typeof _objList[_x] == 'undefined')) {
			continue;
		} else if ((typeof _objKey == 'object') && (_objVal == '')) {
			var _ea = _objList[_x];
			var _blnInvalid = false;
			for (var _y in _objKey) {
				if ((! _blnInvalid) && (! ((_y in _ea) && (_ea[_y] == _objKey[_y])))) _blnInvalid = true;
			}
			if (! _blnInvalid) return _ea;
		} else if (((_objKey.toLowerCase() == '__index') || (_objKey.toLowerCase() == '__key')) && (_isInt(_x))) {
			if ((_objList[_x] == _objVal)) return _objList[_x];
		} else if ((_objKey in _objList[_x]) && (_objList[_x][_objKey] == _objVal)) {
			return _objList[_x];
		}
	}
}
*/
function _findFirstObjectInList(objList, objKey, objValue) {
	var _objList = objList || [];
	var _objKey = objKey || '';
	var _objVal = objValue || '';
	for (var _x in _objList) {
		if ((typeof _objList[_x] == null) || (typeof _objList[_x] == 'function') || (typeof _objList[_x] == 'undefined')) {
			continue;
		} else if ((typeof _objKey == 'object') && (_objVal == '')) {
			var _ea = _objList[_x];
			var _blnInvalid = false;
			for (var _y in _objKey) {
				if ((! _blnInvalid) && (! ((_y in _ea) && (_ea[_y] == _objKey[_y])))) _blnInvalid = true;
			}
			if (! _blnInvalid) return _ea;
		} else if (((_objKey.toLowerCase() == '__index') || (_objKey.toLowerCase() == '__key')) && (_isInt(_x))) {
			if ((_objList[_x] == _objVal)) return _objList[_x];
		} else if ((_objKey in _objList[_x]) && (_objList[_x][_objKey] == _objVal)) {
			return _objList[_x];
		}
	}
}

function _listObjectInList(objList, objKey, objValue) {
	var _objList = objList || [];
	var _objKey = objKey || '';
	var _objVal = objValue || '';
	var _returnList = [];
	for (var _x in _objList) {
		if ((typeof _objList[_x] == null) || (typeof _objList[_x] == 'function') || (typeof _objList[_x] == 'undefined')) {
			continue;
		} else if ((typeof _objKey == 'object') && (_objVal == '')) {
			var _ea = _objList[_x];
			var _blnInvalid = false;
			for (var _y in _objKey) {
				if ((! _blnInvalid) && (! ((_y in _ea) && (_ea[_y] == _objKey[_y])))) _blnInvalid = true;
			}
			if (! _blnInvalid) _returnList.push(_ea);
		} else if (((_objKey.toLowerCase() == '__index') || (_objKey.toLowerCase() == '__key')) && (_isInt(_x))) {
			if ((_objList[_x] == _objVal)) _returnList.push(_objList[_x]);
		} else if ((_objKey in _objList[_x]) && (_objList[_x][_objKey] == _objVal)) {
			return _returnList.push(_objList[_x]);
		}
	}
	if (_returnList.length <= 0) {
		return false;
	} else {
		return _returnList;
	}
}

function _removeObjectFromList(objList, objKey, objValue) {
	var _objList = objList || [];
	var _objKey = objKey || '';
	var _objVal = objValue || '';
	for (var _x in _objList) {
		if ((typeof _objList[_x] == 'function')) {
			continue;
		} else if ((typeof _objKey == 'object')) {
			var _ea = _objList[_x];
			var _blnInvalid = false;
			for (var _y in _objKey) {
				if ((! _blnInvalid) && (! ((_y in _ea) && (_ea[_y] == _objKey[_y])))) _blnInvalid = true;
			}
			if (! _blnInvalid) {
				if (Array.isArray(objList)) { //array
					objList.splice(_x, 1);
				} else { //object
					delete objList[_x];
				}
			}
		} else if (((_objKey.toLowerCase() == '__index') || (_objKey.toLowerCase() == '__key')) && (_isInt(_x)) && (_objList[_x] == _objVal)) {
			if (Array.isArray(objList)) { //array
				objList.splice(_x, 1);
			} else { //object
				delete objList[_x];
			}
		} else if ((_objKey in _objList[_x]) && (_objList[_x][_objKey] == _objVal)) {
			if (Array.isArray(objList)) { //array
				objList.splice(_x, 1);
			} else { //object
				delete objList[_x];
			}
		}
	}
	return true;
}

/*++ DateTime related utils */
Date.prototype.strCurrentDate = function () { 
    return ((this.getDate() < 10)?"0":"") + this.getDate() +"/"+(((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"/"+ this.getFullYear();
}
Date.prototype.strCurrentTime = function () {
     return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes();
	 // +":"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds()
}
function _isValidTimeString(str){
	var _str = str || '';
	var _val = new String(_str.trim());
	if (_val.length != 5) {
		return false;
	} else {
		var _hour = _val.substring(0,2);
		var _min = _val.substring(3,5);
		if (_val.substring(2,3) !== ":") return false;
		if (isNaN(_hour)) return false;
		if (isNaN(_min)) return false;
		if (parseInt(_hour) < 24){
			if (parseInt(_min) < 60){
				return true;
			} else return false;
		} else return false;
	}
}
function datParseTime(strTime, dat) {
	if (!dat) dat = new Date();
	var _time = strTime.match(/(\d+)(?::(\d\d))?\s*(p?)/i);
	if (!_time) return NaN;
	var _hours = parseInt(_time[1], 10);
	if (_time[3]) {
		_hours += (_hours < 12 && _time[3]) ? 12 : 0;
	}
	dat.setHours(_hours);
	dat.setMinutes(parseInt(_time[2], 10) || 0);
	dat.setSeconds(0, 0);
	return dat;
}

function datGetDate(strDateTimeVal) {
	var str, strTime;
	if (strDateTimeVal.trim().indexOf(' ') > 6) {
		var _arr = strDateTimeVal.trim().split(" ");
		if (_arr.length > 0) str = _arr[0];
		if (_arr.length > 1) strTime = _arr[1];
	} else {
		str = strDateTimeVal.trim();
	}
	if (str.trim().length < 8) return false;
	var parts = [];
	var _retDt;
	if (str.indexOf('/') > -1) parts = str.split("/");
	if (str.indexOf('-') > -1) parts = str.split("-");
	if ((parts.length == 3) && (parts[0].trim().length == 4)) {
		_retDt = new Date(str);
	} else if ((str.length == 8) && (! isNaN(str))) {
		parts[0] = str.substr(0, 4);
		parts[1] = str.substr(4, 2);
		parts[2] = str.substr(6, 2);
		_retDt = new Date(parseInt(parts[0], 10), (parseInt(parts[1], 10) - 1), parseInt(parts[2], 10));
	}
	if (Object.prototype.toString.call(_retDt) != '[object Date]') return false;
	
	if (strTime) _retDt = datParseTime(strTime, _retDt);
	return _retDt;
}

/*-- DateTime related utils */

/*
 * Date Format 1.2.3
 * (c) 2007-2009 Steven Levithan <stevenlevithan.com>
 * MIT license
 *
 * Includes enhancements by Scott Trenda <scott.trenda.net>
 * and Kris Kowal <cixar.com/~kris.kowal/>
 *
 * Accepts a date, a mask, or a date and a mask.
 * Returns a formatted version of the given date.
 * The date defaults to the current date/time.
 * The mask defaults to dateFormat.masks.default.
 */

var dateFormat = function () {
	var	token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
		timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
		timezoneClip = /[^-+\dA-Z]/g,
		pad = function (val, len) {
			val = String(val);
			len = len || 2;
			while (val.length < len) val = "0" + val;
			return val;
		};

	// Regexes and supporting functions are cached through closure
	return function (date, mask, utc, dispFormat) {
		var dF = dateFormat;
		/* ++ buff added */
		var _dispFormat = dispFormat || '';
		if ((_dispFormat != '') && (_dispFormat in dateFormat.dispFormat)) {
			dF.i18n = dateFormat.dispFormat[_dispFormat];
		}
		/* -- buff added */

		// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
		if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
			mask = date;
			date = undefined;
		}

		// Passing date through Date applies Date.parse, if necessary
		date = date ? new Date(date) : new Date;
		if (isNaN(date)) throw SyntaxError("invalid date");

		mask = String(dF.masks[mask] || mask || dF.masks["default"]);

		// Allow setting the utc argument via the mask
		if (mask.slice(0, 4) == "UTC:") {
			mask = mask.slice(4);
			utc = true;
		}

		var	_ = utc ? "getUTC" : "get",
			d = date[_ + "Date"](),
			D = date[_ + "Day"](),
			m = date[_ + "Month"](),
			y = date[_ + "FullYear"](),
			H = date[_ + "Hours"](),
			M = date[_ + "Minutes"](),
			s = date[_ + "Seconds"](),
			L = date[_ + "Milliseconds"](),
			o = utc ? 0 : date.getTimezoneOffset(),
			flags = {
				d:    d,
				dd:   pad(d),
				ddd:  dF.i18n.dayNames[D],
				dddd: dF.i18n.dayNames[D + 7],
				m:    m + 1,
				mm:   pad(m + 1),
				mmm:  dF.i18n.monthNames[m],
				mmmm: dF.i18n.monthNames[m + 12],
				yy:   String(y).slice(2),
				yyyy: y,
				h:    H % 12 || 12,
				hh:   pad(H % 12 || 12),
				H:    H,
				HH:   pad(H),
				M:    M,
				MM:   pad(M),
				s:    s,
				ss:   pad(s),
				l:    pad(L, 3),
				L:    pad(L > 99 ? Math.round(L / 10) : L),
				t:    H < 12 ? "a"  : "p",
				tt:   H < 12 ? "am" : "pm",
				T:    H < 12 ? "A"  : "P",
				TT:   H < 12 ? "AM" : "PM",
				Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
				o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
				S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
			};

		return mask.replace(token, function ($0) {
			return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
		});
	};
}();

// Some common format strings
dateFormat.masks = {
	"default":      "ddd mmm dd yyyy HH:MM:ss",
	shortDate:      "m/d/yy",
	mediumDate:     "mmm d, yyyy",
	longDate:       "mmmm d, yyyy",
	fullDate:       "dddd, mmmm d, yyyy",
	shortTime:      "h:MM TT",
	mediumTime:     "h:MM:ss TT",
	longTime:       "h:MM:ss TT Z",
	isoDate:        "yyyy-mm-dd",
	isoTime:        "HH:MM:ss",
	isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
	isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
	dayNames: [
		"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
		"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
	],
	monthNames: [
		"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
		"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
	]
};
dateFormat.dispFormat = {
	th:{
		dayNames: [
			"อา.", "จ.", "อ.", "พ.", "พฤ.", "ศ.", "ส.",
			"อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัส", "ศุกร์", "เสาร์"
		],
		monthNames: [
			"ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.",
			"มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
		]
	}
};
// For convenience...
Date.prototype.format = function (mask, utc, dispFormat) {
	return dateFormat(this, mask, utc, dispFormat);
};

function strGetDisplayNumber(objValue, blnBahtSign, intDigit) {
	if (! objValue) objValue = 0;
	if (isNaN(objValue)) return 'NaN';
	_value = parseFloat(objValue);
	_intDigit = intDigit || 0;
	_blnBahtSign = blnBahtSign || false;
	if (_blnBahtSign) {
		return '฿' + formatNumber(_value, _intDigit);
	} else {
		return formatNumber(_value, _intDigit);
	}
}

function strConvertDataToTableObject() {
	var jsonReturn = [];
	var _cols = _objDataTable.fnSettings().aoColumns;
	var _data = _objDataTable.fnGetData();
	var _each;
	//_data = _objDataTable.fnGetData();//TableTools.fnGetMasters()[0].fnGetTableData( oConfig );
	for (i=0;i<_data.length;i++) {
		_each = _data[i];
		_new = {};
		for (j=0;j<_cols.length;j++) {
			_col = _cols[j];
			if ((_col.bVisible) && (typeof _col.mData !== 'function')) _new[j + '_' + _col.sTitle] = _each[_col.mData];
		}
		jsonReturn.push(_new);
	}
	if (jsonReturn.length > 0) {
		var _strHead = '';
		$.each(jsonReturn[0], function (k, v) {
			_strHead += "<th>"+k+"</th>";
		});
		_strHead = '<tr>' + _strHead + '</tr>';
		var _strBody = '';
		$.each(jsonReturn, 
			function () {
				var _row = "";
				$.each(this, function (k , v) {
					_row += "<td>"+v+"</td>";
				});
				_strBody += "<tr>"+_row+"</tr>";                 
			}
		);
		var _strHTMLTable = '<table id="tblExportExcel">' + _strHead + _strBody + '</table>';
		return _strHTMLTable;
	} else {
		return '';
	}
}

function doExportExcel( nButton, oConfig, oFlash ) {
	var uri = 'data:application/vnd.ms-excel;base64,'
		, template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
		, base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
		, format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
	//data = $('#tblSearchResult').dataTable().fnGetData();
	if (_objDataTable) {
		var ctx = {worksheet: name || 'export', table: strConvertDataToTableObject()}
		window.location.href = uri + base64(format(template, ctx));
	}
}

function strConvertInvoiceDate(str) {
	var parts = str.split("/");
	if (parts.length < 3) return '';
	return parseInt(parts[2], 10) + '-' + pad(parseInt(parts[1], 10), 2, '0') + '-' +  pad(parseInt(parts[0], 10), 2, '0');
}

function pad(n, width, z) {
	z = z || '0';
	n = n + '';
	return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function _isInt(n) {
	return parseFloat(n) == parseInt(n, 10) && !isNaN(n);
}

function getData(obj) {
	return _getElemData(obj);
}
function getLabel(obj) {
	return _getElemLabel(obj);
}
function getValue(obj, defReturn) {
	return _getElemValue(obj, defReturn);
}
function setValue(obj, arrData, blnStrictDataType) {
	return _setElemValue(obj, arrData, blnStrictDataType);
}
function clearValue(obj) {
	_clearElemValue(obj);
}
function isEnable(obj) {
	return _enableElem(obj);
}
function _getElemData(elem) {
	var _elem = _toJQObj(elem);
	if (_elem.length <= 0) return;
	var _data = (_elem.attr('data'))?_elem.attr('data'):'';
	if (_data == '') {
		if (_elem.parents('.data-container').length > 0) {
			_data = $(_elem.parents('.data-container').get(0)).attr('data') || '';
		}
	}
	if (_data == '') {
		var _tag = _elem.get(0).tagName.toLowerCase();
		var _type = _elem.get(0).type;
		if (_tag == 'input' && (_type == 'radio' || _type == 'checkbox')) _data = _elem.attr('name') || '';
	}
	if (_data == '') {
		if ((_elem.attr('id')) && (_elem.attr('id').length > 4)) _data = _elem.attr('id').substr(4);
	}
	return _data;
}
function _getElemLabel(elem) {
	var _elem = _toJQObj(elem);
	if (_elem.length <= 0) return;
	var _label = (_elem.find('label').length > 0)?$(_elem.find('label').get(0)).html():'';
	if (_label == '') {
		if (_elem.parents('.data-container').length > 0) {
			if ($(_elem.parents('.data-container').get(0)).find('label').length > 0) {
				_label = $($(_elem.parents('.data-container').get(0)).find('label').get(0)).html()
			}
		}
	}
	_label = _label.trim();
	if ((_label != '') && (_label.substr(-1) == ':')) _label = _label.substr(0, (_label.length-1)).trim();
	
	return _label;
}

function _setElemValue(elem, data, blnStrictDataType) {
	var _data = (typeof data == 'undefined')?0:data;
	var _blnChangeType = true;
	if ((typeof blnStrictDataType != 'undefined') && (blnStrictDataType == false)) _blnChangeType = false;
	var _value;
	var _elem = _getJQUserInputElement(elem);
	if (_elem.length == 0) return false;
	var _tag = _elem.get(0).tagName.toLowerCase();
	var _type = _elem.get(0).type;
	if (_tag && (typeof _data != 'undefined')) {
		if ((_tag == 'input') && (_type == 'file')) {
			var _fileName, _pathUrl;
			if (typeof _data == 'object') {
				var _dataField = getData(_elem);
				if (_dataField in _data) _data = _data[_dataField];
				if (typeof _data["name"] == 'string') _fileName = _data["name"];
				if (typeof _data["url"] == 'string') _pathUrl = _data["url"];
			} else {
				_fileName = _data;
				_pathUrl = '/app/uploads/' + _data;
			}
			var _prnt = $(_elem.parents('div.display-upload').get(0));
			var _hdn = $($('input[type="hidden"].fmg-value', _prnt).get(0));
			var _isChange = false;
			if ((_hdn.length > 0)) {
				var _oldVal = _hdn.val() || false;
				if ((_oldVal) && (_oldVal != _fileName)) _isChange = true;
				_hdn.val(_fileName);
			}
			if ((_prnt.length > 0) && (_pathUrl) && (typeof _pathUrl == 'string')) {
				var _oldBg = _prnt.css('background-image') || false;
				var _bg = 'url(' + _pathUrl + ')';
				if ((_oldBg) && (_oldBg != _bg)) _isChange = true;
				_prnt.attr('aria-label', 'loading image...').css('background-image', _bg);
			}
			if (_isChange) {
				if ((_prnt.length > 0) && (! _prnt.hasClass('clsCtrl-valueChanged'))) _prnt.addClass('clsCtrl-valueChanged');
				if ((! _elem.hasClass('clsCtrl-valueChanged'))) _elem.addClass('clsCtrl-valueChanged');
				if ((_hdn.length > 0) && (! _hdn.hasClass('clsCtrl-valueChanged'))) _hdn.addClass('clsCtrl-valueChanged');
			}
		} else {
			if ((_data) && (typeof _data == 'object')) { //case pass array of values
				var _dataField = (_getElemData(_elem) || '').replace('[]', '');
				if (_dataField in _data) {
					_value = _data[_dataField];
				} else {
					_value = _data;
				}
				// ++ case display_data separate from actual data (or case not allow all select value control sel
				if (_elem.attr("display_data")) {
					var _disp = _elem.attr("display_data");
					var _dispTxt = (_disp in _data) ? (_data[_disp] || '').toString().trim() : '';
					if  (_dispTxt.length > 0) {
						if (_elem.data("ui-combobox")) {
							_elem.combobox("setDisplayOnly", _dispTxt);
						} else {
							_elem.text(_dispTxt);
						}
					}
				}
			} else {
				_value = _data;
			}
			if (_elem.hasClass('hasDatepicker') || _elem.hasClass('hasdatepicker')) {
				//if ('timepicker' in _elem.data('datepicker').settings) //datetimepicker
				//var _strFormat = _elem.datepicker('option', 'dateFormat');					
				if ((typeof _value == 'string')) {
					//if (_value.trim() == '') return false;
					//_value = $.datepicker.parseDate(_elem.datepicker("option", "dateFormat"), _value);
					_value = datGetDate(_value);
				}
				if (_value instanceof Date) _elem.datepicker('setDate', _value);
			}
			if ((typeof _value == 'object')) return false;
			
			if (_elem.is('.input-integer, .input-double, .input-number') && (typeof _value == 'string')) _value = _cleanNumericValue(_value);
			if (_blnChangeType === true) {
				if (_elem.is('.input-double, .input-number')) {
					if (! isNaN(_value)) {
						_value = parseFloat(_value);
					} else {
						doSetVldrError(_elem, '', 'SetInvalidDataType', 'Invalid data type on _SetElemValue');
						return false;
					}
				} else if (_elem.is('.input-integer')) {
					if (! isNaN(_value)) {
						_value = parseInt(_value);				
					} else {
						doSetVldrError(_elem, '', 'SetInvalidDataType', 'Invalid data type on _SetElemValue');
						return false;
					}
				}
			}
			if (_elem.is('.input-format-number') && (_value != '') && (! isNaN(_value))) _value = formatNumber(_value);
			if (_elem.is('.input-format-integer') && (_value != '') && (! isNaN(_value))) _value = formatNumber(_value, 0);
			if ((_tag == 'input' && (_type == 'text' || _type == 'hidden')) || (_tag == 'textarea')) {
				_elem.val(_value);
			} else if (_tag == 'select') {
				if (_elem.data("ui-combobox")) {
					_elem.combobox('setValue', _value);
				} else if (_elem.data("ui-groupingcombobox")) {
					_elem.groupingcombobox('setValue', _value);
				} else if (_elem.data('ech-multiselect')) {
					_elem.val(_value);
				} else {
					_elem.val(_value);
				}
			} else if (_tag == 'input' && _type == 'checkbox') {
				var _elemData = _getElemData(_elem) || '';
				if ((_value == 1) || (_value == 't') || (_value == true)) {
					_elem.prop('checked', true);
				} else if (_elemData.indexOf('[]') >= 0) {
					var _elemVal = _elem.attr('value') || '';
					if ((_value.toString().split(',').indexOf(_elemVal) >= 0)) _elem.prop('checked', true);
				} else {
					_elem.prop('checked', false);
				}
			} else if (_tag == 'input' && _type == 'radio') {
				var _name = (_elem.attr('name') || '').trim();
				if (_name != '') {
					$('input[type="radio"][name="' + _name + '"]').prop('checked', false);
					$('input[type="radio"][name="' + _name + '"]').filter(function () { return (_value == $(this).val()) }).prop("checked", true);
				} else {
					if (_value == _elem.val()) {
						_elem.prop('checked', true);
					} else {
						_elem.prop('checked', false);
					}
				}
			} else if ((_tag == 'span') || (_tag == 'div')) {
				_elem.html(_value);
			}
		}
	}
}

function _clearElemValue(elem) {
	_elem = _getJQUserInputElement(elem);
	if (_elem.length == 0) return false;
	var _tag = _elem.get(0).tagName.toLowerCase();
	var _type = _elem.get(0).type;
	if ((_tag == 'input' && (_type == 'text' || _type == 'hidden')) || (_tag == 'textarea')) {
		_elem.val('');
	} else if (_tag == 'select') {
		if (_elem.data("ui-combobox")) {
			_elem.combobox("clearValue");
		} else if (_elem.data("ui-groupingcombobox")) {
			_elem.groupingcombobox("clearValue");
		} else if (_elem.data('ech-multiselect')) {
			_elem.multiselect("uncheckAll");
		} else {
			_elem.val('');
		}
	} else if (_tag == 'input' && (_type == 'checkbox' || _type == 'radio')) {
		_elem.prop('checked', false);
	} else if ((_tag == 'input') && (_type == 'file')) {
		_elem.val('');
		_elem.removeClass('clsCtrl-deactivate').removeClass('clsCtrl-valueChanged').removeAttr('ctrl_changeValue');
		var _prnt = $(_elem.parents('div.display-upload').get(0));
		var _remove = $($('input[type="checkbox"].fmg-no-image', _prnt).get(0));
		var _hdn = $($('input[type="hidden"].fmg-value', _prnt).get(0));
		if (_prnt.length > 0) _prnt.css('background-image', '').removeAttr('aria-label').removeAttr('old_bg').removeClass('clsCtrl-deactivate').removeClass('clsCtrl-valueChanged').removeAttr('ctrl_changeValue');
		if (_remove.length > 0) _clearElemValue(_remove);
		if (_hdn.length > 0) _hdn.val('').removeClass('clsCtrl-deactivate').removeClass('clsCtrl-valueChanged').removeAttr('ctrl_changeValue');
	} else {
		if ($.isFunction(_elem.html)) _elem.html('');
	}
}

function _cleanNumericValue(val) {
	if ((typeof val == 'undefined') || (val == null)) return;
	var _val = val;
	_val = _val.toString().trim();
	var _blnMinus = (_val.match(/^\(.+\)$/) || false) !== false;
	_val = _val.replace(/[a-zA-Z_,\( \)]/g, '');
	if ((_val.length <= 0) || isNaN(_val)) {
		return '';
	} else {
		return (_blnMinus)?(parseFloat(_val) * -1):parseFloat(_val);
	}
}

function _enableElem(elem, blnEnabled) {
	if (typeof blnEnabled != 'undefined') {
		return _setEnableElem(elem, blnEnabled);
	} else {
		var _elem = _getJQUserInputElement(elem);
		var _tag = _elem.get(0).tagName.toLowerCase();
		var _type = _elem.get(0).type;
		if ((_tag == 'input' && (_type == 'text' || _type == 'hidden')) || (_tag == 'textarea')) {
			if (_elem.hasClass('hasDatepicker')) {
				return (! _elem.datepicker("option", "disabled"));
			} else {
				var _attr = _elem.attr('readonly') || false;
				return ( ! ((_attr == true) || (_attr == 'readonly')));
			}
		} else if (_tag == 'select') {
			if (_elem.data("ui-combobox")) {
				return (! _elem.is('.ui-combobox-input-disabled'));
			} else if (_elem.data("ui-groupingcombobox")) {
				return (! _elem.is('.ui-combobox-input-disabled'));
			} else {
				var _attr = _elem.attr('disabled') || false;
				return ( ! ((_attr == true) || (_attr == 'disabled')));
			}
		} else if (_tag == 'input' && (_type == 'checkbox' || _type == 'radio' || _type == 'file')) {
			var _attr = _elem.attr('disabled') || false;
			return ( ! ((_attr == true) || (_attr == 'disabled')));
		}
	}
}

function _setEnableElem(elem, blnEnabled) {
    var _bln = blnEnabled || false;
    var _elem = _getJQUserInputElement(elem);
	if (_elem.length <= 0) return false;
    var _tag = _elem.get(0).tagName.toLowerCase();
    var _type = _elem.get(0).type;
    if ($(elem).data("ui-button")) {
		$(elem).button('option', 'disabled', (! _bln));
	} else if ((_tag == 'input' && (_type == 'text' || _type == 'hidden')) || (_tag == 'textarea')) {
        if (_elem.hasClass('hasDatepicker')) {
            _elem.datepicker("option", "disabled", (!_bln));
        } else {
			if (_bln == true) {
				_elem.removeAttr("readonly");
			} else {
				_elem.attr("readonly", "readonly");
			}
        }
    } else if (_tag == 'select') {
		if (_elem.data("ui-combobox")) {
			_elem.combobox('enable', _bln);
		} else if (_elem.data("ui-groupingcombobox")) {
            _elem.groupingcombobox('enable', _bln);
        } else {
			if (_bln == true) {
				_elem.removeAttr("disabled");
			} else {
				_elem.attr("disabled", "disabled");
			}
        }
	} else if (_tag == 'input' && (_type == 'checkbox' || _type == 'radio')) {
		if (_bln == true) {
			_elem.removeAttr("disabled");
		} else {
			_elem.attr("disabled", "disabled");
		}
	} else if ((_tag == 'input') && (_type == 'file')) {
		var _prnt = $(_elem.parents('div.display-upload').get(0));
		if (_bln == true) {
			_elem.removeAttr("disabled");
			if ((_prnt.length > 0)) {
				_prnt.removeAttr("disabled");
				//var _oldBg = _prnt.attr('old_bg') || 'none';
				//if (_oldBg != 'none') _prnt.css('background-image', _oldBg).removeAttr('old_bg');
			}
		} else {
			_elem.attr("disabled", "disabled");
			if ((_prnt.length > 0)) {
				_prnt.attr("disabled", "disabled");
				//var _bg = _prnt.css('background-image') || 'none';
				//if (_bg != 'none') _prnt.css('background-image', '').attr('old_bg', _bg);
			}
		}
	}
	return _bln;
}

function doSelectDisplayFields() {
	$("#divSelectableFields").dialog( "open" );
}

function _visibleButtonColumns(blnVis) {
	_bln = (blnVis || false);
	_colLength = 0;
	if (_aoColumns) _colLength = _aoColumns.length;
	if ((_objDataTable) && (_colLength > 3)) {
		_objDataTable.fnSetColumnVis(_colLength - 3, _bln);		
		_objDataTable.fnSetColumnVis(_colLength - 2, _bln);		
		_objDataTable.fnSetColumnVis(_colLength - 1, _bln);		
	}
	return false;
}

function doClearDisplayInfo(index) {
	var _indx = index || 0;
	if (_indx == -1) {
		$(".cls-div-info").html("");
	} else {
		$(".cls-div-info[index=" + _indx + "]").html("");
	}
}

function doClearDisplayError(index, strErrCategory) {
	var _index = index || -1;
	var _strSelector = (strErrCategory)?'#li_' + strErrCategory:'li';
	var _strIndexFiler = (_index == -1)?'':'[index="' + _index + '"]';
	$(_strSelector, $('.ul-vldr-error-msg' + _strIndexFiler)).remove();
}

function doDisplayInfo(msg, title, index) {
	var _indx = index || 0;
	var _strFilter = (_indx > -1)?'[index=' + _indx + ']':'';
	$(".cls-div-info" + _strFilter).html(title + ': ' + msg);
	$(".cls-div-info" + _strFilter).show(500).fadeIn(500).fadeOut(200).fadeIn(500).fadeOut(200).fadeIn(500);
}

function doDisplayError(strErrCategory, strErrorMessage, blnDoAlert, index) {
	var _index = index || -1;
	var _blnDoAlert = true;
	if (typeof blnDoAlert == 'boolean') _blnDoAlert = blnDoAlert;
	var _strErrMsg = strErrorMessage || 'Unknown Error!';
	var _strID = 'li_' + strErrCategory;
	var _strIndexFiler = (_index == -1)?'':'[index="' + _index + '"]';
	$('.ul-vldr-error-msg' + _strIndexFiler).append('<li id="' + _strID + '">' + _strErrMsg + '</li>');
	if (_blnDoAlert) alert(_strErrMsg);
}

function _doDisplayFormError(form) {
	_strErrList = '';
	$( form ).find(".input-invalid").each(
		function () {
			_strErrList += "\r\n   - " + $(this).prop('id') + ': ' + $(this).prop('error-msg')
		}
	);
	if (_strErrList != '') {
		alert(MSG_ALERT_FORM_INVALID + _strErrList);
	}
}

function _doClearForm(form) {			
	var _index = $(form).attr('index') || 0;
	doClearVldrError(form);
	doClearDisplayInfo(_index);
	_doSetEnableFormUserInput(form, true);
	var _div = $( form ).parents('.cls-div-form-edit-dialog')[0];
	if ($( _div ).find(".cls-div-sub-list").length > 0) {
		$( _div ).find('.cls-div-sub-list').attr('main-search', '');
		if ($.isFunction(clearSubList)) clearSubList();
	}
	doClearUserInput(form);

	//++ Clear data value for special link
	if (typeof _objEditPanelData == 'object') {
		for (_key in _objEditPanelData) {
			$(form).find('[href*="{' + _key + '}"]').each(function() { $(this).addClass('link-disabled'); });
			_objEditPanelData[_key] = "";
		}
	}
	$('.eventInsert-hide', form).removeClass('hidden');
	$('.eventView-hide', form).removeClass('hidden');
	$('.eventEdit-hide', form).removeClass('hidden');
	$('.eventInsert-invis', form).removeClass('invisible');
	$('.eventView-invis', form).removeClass('invisible');
	$('.eventEdit-invis', form).removeClass('invisible');

	$(form).find("#btnFormSubmit").css('display', '');
	$(form).find("#btnFormReset").css('display', '');
}
function _doSetValueFormUserInput(form, objData, blnClearValue) {
	if (blnClearValue || false) _doClearForm(form);
	
	$(form).find(".user-input").each(
		function () {
			_setElemValue(this, objData);
		}
	);
}

function _getElemValue(elem, defReturn) {
	var _elem = _getJQUserInputElement(elem);
	var _defRet = defReturn;
	var _valRet;
	if (_elem.length <= 0) return _defRet;
	_tag = _elem.get(0).tagName.toLowerCase();
	_type = _elem.get(0).type;
	if ((_elem) && (_tag == 'input' && (_type == 'text' || _type == 'hidden')) || (_tag == 'textarea') || (_tag == 'select')) {
		if (_elem.data('ech-multiselect')) {
			var _arrRet = [];
			if (_elem.is('.input-integer, .input-double, .input-number')) {
				var _arrVal = _elem.val();
				for (var _i in _arrVal) {
					_dummy = _cleanNumericValue(_arrVal[_i]);
					if (_dummy) _arrRet.push(_dummy);
				}
			} else {
				_arrRet = _elem.val() || false;
			}
			if ((_arrRet) && (_arrRet.length > 0)) return _arrRet.join(',');
		} else if (_elem.is('.input-integer, .input-double, .input-number')) {
			_valRet = _cleanNumericValue(_elem.val());
			//if ((_valRet) && (_elem.val() != _valRet)) _elem.val(_valRet); //change to refined numeric data // removed after implement class .format-number
		} else {
			_valRet = _elem.val();
		}
	} else if (_tag == 'input' && (_type == 'checkbox')) {
		if (_elem.is(':checked') == true) {
			_valRet = (_elem.val() && (_elem.val().toLowerCase() != 'on') && (_elem.val().toString().toLowerCase() != 'true')) ? _elem.val() : '1';
		}
	} else if (_tag == 'input' && (_type == 'radio')) {
		var _elem = $('input[type="radio"][name="' + _elem.attr('name') + '"]:checked');
		if (_elem.length > 0) {
			_valRet = _elem.val();
		}
    } else if ((_tag == 'span') || (_tag == 'div')) {
		if (_elem.is('.input-integer, .input-double, .input-number')) {
			_valRet = _cleanNumericValue(_elem.html());
		} else {
			_valRet = _elem.html();
		}
	} else if ((_tag == 'input') && (_type == 'file') && (_elem.parents('span.spn-image-select').length > 0)) {
		var _prnt = $(_elem.parents('div.display-upload').get(0));
		var _hdn = $($('input[type="hidden"].fmg-value', _prnt).get(0));
		if (_prnt.is('.clsCtrl-deactivate')) {
			_valRet = 'remove';
		} else if (! _elem.is('.clsCtrl-valueChanged')) {
			_valRet = 'unchange';
		} else {
			if (_hdn.length > 0) _valRet = _hdn.val();
		}
	} else {
		_valRet = _elem.val();
	}
	if ((_valRet == null) || (_valRet.toString().trim() == '')) {
		return _defRet;
	} else {
		return _valRet;		
	}
}

function _setEnableElem(elem, blnEnabled) {
	_elem = $(elem);
	_bln = blnEnabled || false;
	_tag = _elem[0].tagName.toLowerCase();
	_type = _elem[0].type;
	if ((_tag == 'input' && (_type == 'text' || _type == 'hidden')) || (_tag == 'textarea')) {
		if (_elem.hasClass('hasDatepicker')) {
			_elem.datepicker( "option", "disabled", (! _bln));
		} else {
			_elem.attr("readonly", (! _bln));
		}
	} else if (_tag == 'select') {
		if (_elem.data("ui-combobox")) {
			_elem.combobox('enable', _bln);
		} else {
			_elem.attr("disabled", (! _bln));
		}
	} else if (_tag == 'input' && (_type == 'checkbox' || _type == 'radio' || _type == 'file')) {
		_elem.attr("disabled", (! _bln));
	}
}
function _doSetEnableFormUserInput(form, blnEnabled) {
	var _bln = blnEnabled || false;
	$(form).find(".user-input:not(.set-disabled)").each(
		function () {
			_setEnableElem(this, _bln);
		}
	);
	if (_bln) {
		$(form).find("#btnFormSubmit:not(.hidden):not(.invisible)").css('display', '');
		$(form).find("#btnFormReset:not(.hidden):not(.invisible)").css('display', '');
	} else {
		$(form).find("#btnFormSubmit").css('display', 'none');
		$(form).find("#btnFormReset").css('display', 'none');
	}
}

function blnDataChanged(form) {
	if (! _currEditData) return true;
	var _isChanged = false;
	var _form = form || $('form');
	$('.user-input:not(".no-validate")', _form).each(
		function () {
			var _elem = _getJQUserInputElement(this);
			if (_elem.is('readonly') || _elem.is('disabled')) return true;
			var _value = _getElemValue(_elem);
			var _dataField = _getElemData(_elem);
			if ((typeof _currEditData != 'undefined') && (_dataField != '') && (_dataField in _currEditData)) {
				var _oldVal = _currEditData[_dataField];
//console.debug(_dataField + " : " + (_currEditData[_dataField] || 0) + ' == ' + (_value || 0));
				if (_elem.hasClass('hasDatepicker')) {
					var _format = _elem.datepicker("option", "dateFormat"), _old = '', _curr = '';
					_curr = (_elem.val() || '').trim(); //_elem.datepicker( "getDate" ); //getValue(_elem, '');//
					if (Object.prototype.toString.call(_oldVal) === '[object Date]') {
						_old = $.datepicker.formatDate(_format, _oldVal);
					} else if ((typeof _oldVal == 'string') && (_oldVal != '')) {
						var _dt = datGetDate(_oldVal);
						_old = $.datepicker.formatDate(_format, _dt);
						var _timeFormat = (_elem.datepicker('option', 'timeFormat') || '').trim();
						if ((_timeFormat != '') && (typeof $.datepicker.formatTime == 'function')) {
							_old += ' ' + $.datepicker.formatTime(_timeFormat, {hour: _dt.getHours(), minute: _dt.getMinutes(), second: _dt.getSeconds()});
						}
					}
					if ((_old != _curr)) { //&& (! ((_old.length > _curr.length) && (_old.indexOf(_curr) >= 0)))
						_isChanged = true;
						return false;
					}
				} else {
					if (_elem.is('.input-integer, .input-double, .input-number')) _oldVal = _cleanNumericValue(_oldVal);
					if (this.type == 'checkbox') {
						switch (_oldVal.toLowerCase()) {
							case "t":
							case "1":
							case "true":
								_oldVal = 1;
								break;
							default:
								_oldVal = 0;
								break;
						}
					}
					if ((_value || 0) != (_oldVal || 0)) {
						_isChanged = true;
						return false;
					}					
				}
			} else {
				if ((_value || false) != false) {
					_isChanged = true;
					return false;
				}
			}
		}
	);
//console.debug('changed = ' + _isChanged);
	return _isChanged;
}

function _cleanNumericValue(val) {
	var _val = val || 0;
	_val = _val.toString().trim();
	if (_val.length > 0) _val = _val.replace(/,/g, '');
	return parseFloat(_val);
}

function _toJQObj(obj) {
	if (typeof obj == 'string') {
		if ($('[data="' + obj + '"]').length > 0) {
			return $('[data="' + obj + '"]');
		} else if ($('#' + obj).length > 0) {
			return $('#' + obj);
		} else {
			return;
		}
	} else if (obj instanceof jQuery) {
		return obj
	} else {
		return $(obj);
	}
}

function _getJQUserInputElement(elem) {
	var _elem = _toJQObj(elem);
//	if (_elem.get(0).tagName.toLowerCase() == 'select') {
//		return _elem;
//	} else {
	if (_elem.children(':not(label,option)').length > 0) _elem = $(_elem.children(':not(label,option)').get(0));
	return _elem;
//	}
}

function jqObjDataContainer(obj) {
	var _elem = _toJQObj(obj);
	if (_elem) {
		if (_elem.hasClass('data-container')) {
			return _elem;
		} else if (_elem.parents('.data-container').length > 0) {
			return $(_elem.parents('.data-container').get(0));
		} else if ($('.data-container', _elem).length > 0) {
			return $($('.data-container', _elem).get(0));
		}
	}
}

function doVldrInput(allow, obj, type){
	if(allow){
		$(obj).keypress(function(e) {
				var a = [];
				var k = e.which;
				
				for (i = 48; i < 58; i++){
					a.push(i);
				}

				if(type == 'number'){
					if (!(a.indexOf(k)>=0)){
						e.preventDefault();
						return e;
					}
				}else if(type == 'text'){
					if ((a.indexOf(k)>=0)){
						e.preventDefault();
						return e;
					}
				}
		});
	}else{
		$(obj).unbind('keypress');
	}
}

function doSetVldrError(obj, data_field, type, msg, index) {
    var _elem = _toJQObj(obj);
	if (_elem) {
		if ((typeof index == 'undefined') || (index == '')) index = (_elem.parents('[index]').length > 0)?$(_elem.parents('[index]').get(0)).attr('index'):0;
		var _elemDispErr = false;
		if (index) {
			_elemDispErr = $('.ul-vldr-error-msg[index="' + index + '"]');
			if (_elemDispErr.length <= 0) _elemDispErr = _elem.parents('.ul-vldr-error-msg');
			if (_elemDispErr.length <= 0) _elemDispErr = $('.ul-vldr-error-msg');
			if (_elemDispErr.length > 0) {
				_elemDispErr = $(_elemDispErr[0]);
			} else {
				_elemDispErr = false;
			}
		}
		var _dataField = data_field || _getElemData(obj);
		var _strErrMsg = ((_elem.attr('invalid-msg') && (_elem.attr('invalid-msg') != msg))?_elem.attr('invalid-msg') + ', ':'') + msg;
		_elem.addClass('input-invalid').attr('invalid-msg', _strErrMsg).attr('title', _strErrMsg);
		if (_elemDispErr) _elemDispErr.append('<li id="li_' + _dataField + '__' + type + '">' + _strErrMsg + '</li>');
	}
}

function blnValidateElem(elem) {
	var _elem = _toJQObj(elem);
	doClearVldrErrorElement(_elem);

	if (_elem.is('.input-required') && ( ! blnValidateElem_TypeRequired(_elem))) return false;
	if (_elem.is('.input-integer') && ( ! blnValidateElem_TypeInt(_elem))) return false;
	if (_elem.is('.input-double') && ( ! blnValidateElem_TypeDouble(_elem))) return false;
	
    return true;
}

function blnValidateElem_TypeRequired(elem) {
	if (elem instanceof jQuery) elem = elem.get(0);
	if (('tagName' in elem) && ('type' in  elem)) {
		var _elem = $(elem);
		_dataField = _getElemData(_elem);
		_type = elem.type;
		_tag = elem.tagName.toLowerCase();
		if ((_tag == 'input' && (_type == "text" || _type == "hidden")) || (_tag == 'select')) {
			$('#li_' + _dataField + '__required', $('.ul-vldr-error-msg')).remove();
			if ((_elem.val() || '').trim() == "") {
				var _strErrMsg = MSG_VLDR_INVALID_REQUIRED.replace(/v_XX_1/g, '( ' + _dataField + ' )') + ' ';
				doSetVldrError(_elem, _dataField, "required", _strErrMsg);
				return false;
			}
		}
	}
	return true;
}

function blnValidateElem_TypeInt(elem) {
	if (elem instanceof jQuery) elem = elem.get(0);
	if (('tagName' in elem) && ('type' in  elem)) {
		var _elem = $(elem);
		var _val = _elem.val().toString().replace(/,/g, '').trim();
		_dataField = _getElemData(_elem);
		_type = elem.type;
		_tag = elem.tagName.toLowerCase();
		if ((_tag == 'input' && (_type == "text" || _type == "hidden")) || (_tag == 'select')) {
			$('#li_' + _dataField + '__typeInt', $('.ul-vldr-error-msg')).remove();
			if ((_val !== '') && (! _isInt(_val))) {
				var _strErrMsg = MSG_VLDR_INVALID_DATATYPE.replace(/v_XX_1/g, '( ' + _dataField + ': integer )') + ' ';
				doSetVldrError(_elem, _dataField, "typeInt", _strErrMsg);
				return false;
			}
		}
	}
	return true;
}

function blnValidateElem_TypeDouble(elem) {
	if (elem instanceof jQuery) elem = elem.get(0);
	var _index = ($(elem).parents('form').length > 0)?$($(elem).parents('form').get(0)).attr('index'):'';
	var _strIndexFilter = (_index)?'[index="' + _index + '"]':'';
	if (('tagName' in elem) && ('type' in  elem)) {
		var _elem = $(elem);
		var _val = _elem.val().toString().replace(/,/g, '').trim();
		_dataField = _getElemData(_elem);
		_type = elem.type;
		_tag = elem.tagName.toLowerCase();
		if ((_tag == 'input' && (_type == "text" || _type == "hidden")) || (_tag == 'select')) {
			$('#li_' + _dataField + '__typeDouble', $('.ul-vldr-error-msg')).remove();
			if (isNaN(_val)) {
				var _strErrMsg = MSG_VLDR_INVALID_DATATYPE.replace(/v_XX_1/g, '( ' + _dataField + ': double )') + ' ';
				doSetVldrError(_elem, _dataField, "typeDouble", _strErrMsg);
				return false;
			}
        }
    }
    return true;
}

function blnValidateContainer(blnFullTest, container, strJqSelector) {
	var _blnFullLoop = blnFullTest || false;
    var _container = container || $('body');
	var _strJqSelector = strJqSelector || '';
    var _blnIsValid = true;
    doClearVldrError(_container);
    $(_strJqSelector + '.input-required', _container).filter(__fnc_filterNotNestedHiddenClass).each(
        function () {
            if (blnValidateElem_TypeRequired(this) == false) {
				if (_blnIsValid) _blnIsValid = false;
				if (! _blnFullLoop) return false;
			}
        }
    );
	if (!(_blnFullLoop || _blnIsValid)) return false;
	
    $(_strJqSelector + '.input-integer', _container).filter(__fnc_filterNotNestedHiddenClass).each(
        function () {
            if (blnValidateElem_TypeInt(this) == false) {
				if (_blnIsValid) _blnIsValid = false;
				if (! _blnFullLoop) return false;
			}
        }
    );
	if (!(_blnFullLoop || _blnIsValid)) return false;

    $(_strJqSelector + '.input-double', _container).filter(__fnc_filterNotNestedHiddenClass).each(
        function () {
            if (blnValidateElem_TypeDouble(this) == false) {
				if (_blnIsValid) _blnIsValid = false;
				if (! _blnFullLoop) return false;
			}
        }
    );
    return _blnIsValid;
}

function blnValidateFormValue(form) {
	var _blnValid = true;
	doClearVldrError(form);
	$( form ).find(".input-required").each(function() {
		_this = $( this );
		if (getValue(_this, null) === null) {
			_this.addClass('input-invalid');
			_this.prop('error-msg', MSG_FORM_INVALID_NO_VALUE_INPUT);
			_blnValid = false;			
		}
	});
	//if ( ! _blnValid ) return _blnValid;
	$( form ).find(".input-integer").each(function() {
		_this = $(this);
		_val = getValue(_this, '');
		if ((_val !== '') && (! _isInt(_cleanNumericValue(_val)))) {
			_this.addClass('input-invalid');
			_this.prop('error-msg', MSG_FORM_INVALID_INTEGER_INPUT);
			_blnValid = false;
		}
	});
	//if ( ! _blnValid ) return _blnValid;
	$( form ).find(".input-number").each(function() {
		_this = $(this);
		_val = getValue(_this, '');
		if ((_val !== '') && (isNaN(_cleanNumericValue(_val)))) {
			_this.addClass('input-invalid');
			_this.prop('error-msg', MSG_FORM_INVALID_INTEGER_INPUT);
			_blnValid = false;
		}
	});
	//if ( ! _blnValid ) return _blnValid;
	_doDisplayFormError(form);
	
	return _blnValid;
}

function doClearVldrErrorElement(elem) {
	var _elem = $(elem);
	$('.ul-vldr-error-msg li').each(function() {
		if (this.id.indexOf('li_' + _getElemData(_elem) + '__') >= 0) {
			$(this).remove();
		}
	});
	_elem.removeClass('input-invalid');
	_elem.removeProp('invalid-msg');
	_elem.removeAttr('title');
}

function doClearVldrError(container) {
    var _container = container || $('body');
    $('.ul-vldr-error-msg').empty();
    $('.input-invalid', _container).each(
		function () {
		    doClearVldrErrorElement(this);
		}
	);
}

function _doFetchDataByJQSelector(arrData, container, selectText) {
	var _arrData = arrData || [];
    $(selectText, container).each(
        function () {
			_setElemValue(this, _arrData, false);
        }
    );
}

function doFetchDataUserInput(arrData, container) {
    var _container = container || $('form');
    var _arrData = arrData || [];
    if (_arrData == []) {
        return false;
    } else {
        _doFetchDataByJQSelector(_arrData, _container, '.user-input');
    }
}

function doFetchDataContainer(arrData, container) {
    var _container = container || $('form');
    var _arrData = arrData || [];
    if (_arrData == []) {
        return false;
    } else {
        _doFetchDataByJQSelector(_arrData, _container, '.data-container');
    }
}

function _clearByJQSelector(container, selectText) {
    $(selectText, container).each(
        function () {
			_clearElemValue(this);
        }
    );
}
function doClearUserInput(container) {
    var _container = container || $('form');
    doClearVldrError(_container);
    _clearByJQSelector(_container, '.user-input:not(".data-constant")');
}
function doClearDataContainer(container) {
    var _container = container || $('form');
    doClearVldrError(_container);
    _clearByJQSelector(_container, '.data-container:not(".data-constant")');
}

function jqObjDataContainer(obj) {
	var _elem = _toJQObj(obj);
	if (_elem) {
		if (_elem.hasClass('data-container')) {
			return _elem;
		} else if (_elem.parents('.data-container').length > 0) {
			return $(_elem.parents('.data-container').get(0));
		} else if ($('.data-container', _elem).length > 0) {
			return $($('.data-container', _elem).get(0));
		} else {
			return _elem;
		}
	}
}

function __performInitTemplateControls(jqContainer, dialogOptions) {
	_container = jqContainer || $('form');
	_options = dialogOptions || undefined;
	if (_container && (_container.length > 0)) {
		_container.each(function () {
			for (var _i=0;_i<this.elements.length;_i++){
				var _el = this.elements[_i];
//console.log(_elem.name+"="+_elem.value);
				var _elem = _toJQObj(_el);
				var _id = _elem.attr('id') || '';
				if (_id.length > 4) {
					var _pref = _id.substr(0, 4).toLowerCase();
					switch (_pref) {
						case 'dpk-':
							if (_elem.is(':ui-datepicker')) _elem.datepicker("destroy");
							_elem.datepicker({
								showOn: "both",
								buttonText: 'เลือกวันที่',
								changeYear: true,
								changeMonth: true,
								buttonImage: "public/images/select_day.png",
								buttonImageOnly: true,
								dateFormat: 'dd/mm/yy'
							});
							var _def_val = _elem.attr('def_val') || '';
							if (_def_val.length >= 8) _elem.datepicker("setDate", new Date(_def_val));
							break;
						case 'sel-':
							if (_elem.is(':ui-combobox')) _elem.combobox("destroy");
							_elem.combobox();
							break;
					}
				}
			}
			for (var _i=0;_i<this.getElementsByTagName('a').length;_i++){
				var _aHref = this.getElementsByTagName('a')[_i];
				var _elem = _toJQObj(_aHref);
				var _id = _elem.attr('id') || '';
				if (_id.length > 4) {
					var _pref = _id.substr(0, 4).toLowerCase();
					if (_pref == 'btn-') {
						if (_elem.is(':ui-button')) _elem.button("destroy");
						var _icon = _elem.attr('icon').trim() || '';
						_elem.button();
						if (_icon !== '') _elem.button("option", "icons", {primary: _icon});
						_elem.on('click', 
							function (evnt) {
								var _func = $(this).attr('function').trim() || '';
								var _strParams = $(this).attr('params') || '';
								var _params = _strParams.split(',');
								if (_func !== '') executeFunctionByName(_func, window, _params); 
							}
						);					
					}
				}
			}
			//first set disabled 
			$(".user-input.set-disabled", _container).each(function () {
				_setEnableElem(this, false);
			});
		});
	}
}

function doPostToURL(path, params, method, target) {
	var _target = target || 'new';
	var _method = method || "post"; // Set method to post by default, if not specified.

	var _frm = document.createElement("form");
	_frm.setAttribute("method", _method);
	_frm.setAttribute("action", path);
	if (_target == 'new') _frm.setAttribute('target', '_blank');

	var _field = document.createElement("input");
	_field.setAttribute("type", "hidden");
	_field.setAttribute("name", 'data');
	_field.setAttribute("value", JSON.stringify(params));

	_frm.appendChild(_field);
	$(document.body).append(_frm);
	_frm.submit();
	_frm.remove();
}


function _checkAccessImg(url, fnc_Callback_onSuccess, fnc_Callback_onError, fnc_Callback_onDone) {
	var _url = url || '';
	if (_url.trim().length <= 0) {
		if (typeof fnc_Callback_onError == 'function') fnc_Callback_onError.apply(this, ['Empty url path']);
		if (typeof fnc_Callback_onDone == 'function') fnc_Callback_onDone.apply(this);
	} else {
		var _el = document.createElement('img');
		var _timStart = new Date();
		var _fnc_idleChecker = function() {
			if (document.body.contains(_el)) {
//				alert('Timeout!!!');
				document.body.removeChild(_el);
				if (typeof fnc_Callback_onError == 'function') fnc_Callback_onError.apply(this, ['"' + this.src + '" failed, timeout']);
				if (typeof fnc_Callback_onDone == 'function') fnc_Callback_onDone.apply(this);
			}
		};
		_el.id = "lfchkr" + new Date().getTime() + Math.floor(Math.random() * 1000000);
		_el.onerror = function (ev) {
//			alert(this.id + ' failed to load');
			if ( ! document.body.contains(_el)) return false;
			document.body.removeChild(_el);
			if (typeof fnc_Callback_onError == 'function') fnc_Callback_onError.apply(this, ['"' + this.src + '" unaccessable']);
			if (typeof fnc_Callback_onDone == 'function') fnc_Callback_onDone.apply(this);
		};
		_el.onload = function (ev) {
//			alert(this.id + ' success');
			if ( ! document.body.contains(_el)) return false;
			document.body.removeChild(_el);
			if (typeof fnc_Callback_onSuccess == 'function') fnc_Callback_onSuccess.apply(this);
			if (typeof fnc_Callback_onDone == 'function') fnc_Callback_onDone.apply(this);
		};
		// + "?" + new Date().getTime() + Math.floor(Math.random() * 1000000); //case want to check if cached as well
		_el.src = _url;
		_el.setAttribute("style", "display:none;");
		document.body.appendChild(_el);
		setTimeout(_fnc_idleChecker, 5000); //5 seconds to check else return timeout
	}
}

function _checkAccessImgList(arrUrl, fnc_Callback_onSuccess, fnc_Callback_onError, fnc_Callback_onDone) {
	var _arrUrl = (typeof arrUrl == 'object') && (arrUrl.length > 0) ? arrUrl : '';
	if (typeof _arrUrl == 'string') {
		_checkAccessImg(_arrUrl, fnc_Callback_onSuccess, fnc_Callback_onError, fnc_Callback_onDone);
		return false;
	}
	var _blDone = false;
	var _arrLoadCheckList = [];
	var _arrObjErrorList = false;
	var _fncCheckLoadCompleted = function () {
		if (_blDone) return false;
		if (_arrLoadCheckList.length <= 0) {
			if (_arrObjErrorList) {
				if (typeof fnc_Callback_onError == 'function') fnc_Callback_onError.apply(this, [_arrObjErrorList]);
				if (typeof fnc_Callback_onDone == 'function') fnc_Callback_onDone.apply(this);
			} else {
				if (typeof fnc_Callback_onSuccess == 'function') fnc_Callback_onSuccess.apply(this);
				if (typeof fnc_Callback_onDone == 'function') fnc_Callback_onDone.apply(this);
			}
			_blDone = true;
		}
	};
	for (var _i=0;_i<_arrUrl.length;_i++) {
		var _strUrl = ((typeof _arrUrl[_i] == 'string') && (_arrUrl[_i].trim().length > 0)) ? _arrUrl[_i].trim() : '';
		if (_strUrl != '') {
			_arrLoadCheckList.push(_strUrl);
			_checkAccessImg(_strUrl
				, function() { _arrLoadCheckList.removeByValue(_strUrl) }
				, function(errMsg) { _arrObjErrorList.append({"url": _strUrl, "msg": (errMsg || 'unknown')}); }
				, _fncCheckLoadCompleted);
		}
	}
}
/*
function __objGetDataFromContainer(cntnr) {
	var _cntnr = cntnr || $('.cls-frm-edit');
	var _objReturn = {};
	$(".user-input:not(.no-commit), .data-container[data]:not(.no-commit)", _cntnr).each(function () {
			var _name = _getElemData(this);
			var _val = _getElemValue(this, undefined);
			if (typeof _val != 'undefined') _objReturn[_name] = _val;
		});
	return _objReturn;
};
*/
function __objGetDataFromContainer(cntnr) {
	var _cntnr = cntnr || $('.cls-frm-edit');
	var _objReturn = {};
	$(cntnr).find(".user-input:not(.no-commit), .data-container[data]:not(.no-commit)").each(function () {
		var _data = (_getElemData(this) || '').trim();
		var _val = _getElemValue(this, undefined);
		if ((typeof _data == 'string') && (typeof _val != 'undefined')) {
			if (_data.indexOf('[]') >= 0) {
				_data = _data.replace('[]', '');
				if (_data in _objReturn) {
					_objReturn[_data] += _val + ',';
				} else {
					_objReturn[_data] = ',' + _val + ',';						
				}
			} else {
				_objReturn[_data] = _val;
			}
		}
	});
	return _objReturn;
}

function _getModalDialog(title, message, optContainer) {
	var _title = title || MSG_DLG_TITLE_QUERY;
	var _msg = message || MSG_DLG_HTML_QUERY;
	var _cntr = _toJQObj(optContainer);
	if (_cntr.length <= 0) _cntr = $('body');
	
	$("#dialog-modal").each(function() {
		if ($(this).is(':ui-dialog')) $(this).dialog("close");
		$(this).remove();
	});

	var _divDlg = $("#dialog-modal");
	if (_divDlg.length < 1) {
		_divDlg = $('<div id="dialog-modal">').appendTo(_cntr);
	} else if (_divDlg.length == 1) {
		_divDlg.detach().appendTo(_cntr);
	}

	if (! _divDlg.is(':ui-dialog')) {
		_divDlg.dialog({
			height:100,
			width:400,
			resizable:false,
			modal:true,
			closeOnEscape:false,
			autoOpen:false
		});
	}
	_divDlg.html("<p>" + _msg + "</p>");
	_divDlg.dialog("option", "title", _title);

	// Inferno:: case already open modal just take over and reopen if it forced close by any procedures and take over its handler
	if (_divDlg.dialog("isOpen")) {
		_divDlg.on('close', function() {
			$(this).off('close');
			_divDlg = $('<div id="dialog-modal">')
				.appendTo('body')
				.dialog({
					height:100,
					width:400,
					resizable:false,
					modal:true,
					closeOnEscape:false,
					autoOpen:true
				})
				.dialog("moveToTop");
		});
	}
	return _divDlg;
}

function _getOpenWaitModalDialog(title, message, optContainer) {
	var _dlg = _getModalDialog(title, message, optContainer) || false;
	if (_dlg && (_dlg.is(':ui-dialog'))) {
		if (! _dlg.dialog("isOpen")) _dlg.dialog("open");
		_dlg.dialog("moveToTop");
		return _dlg;
	} else {
		return false;
	}
}

function _closeWaitModalDialog(dlg) {
	var _dlg = dlg || false;
	if (_dlg && (_dlg.is(':ui-dialog')) && (_dlg.dialog("isOpen"))) {
		_dlg.dialog("close");
	}
}

function _forceCloseAllWaitModalDialog() {
	$('#dialog-modal').each(function() {
		var _dlg = $(this);
		if (_dlg && (_dlg.is(':ui-dialog')) && (_dlg.dialog("isOpen"))) _dlg.dialog("close");
	});
}

function _doDisplayToastMessage(msg, optIntBlinkLoop, optIsGetObjPresist) {
	var _msg = (msg || '').toString().trim();
	if (_msg.length > 0) {
		_doClearToastMessage();
		var _intLoop = (optIntBlinkLoop || 1) - 1;
		var _isPresist = optIsGetObjPresist || false;
		var _intDelayHide = (_intLoop > 0) ? 500 : 1000;
		var _win = $(window);
		var _divMsg = $('<div>').html(_msg).addClass('cls-div-toast-message').css('position', 'fixed').css('z-index', 100000).appendTo('body');
		_divMsg.css("top", ((_win.height() - _divMsg.outerHeight()) / 10) + _win.scrollTop() + "px");
		_divMsg.css("left", ((_win.width() - _divMsg.outerWidth()) / 2) + _win.scrollLeft() + "px");
		_divMsg.css('font-size','17px')
		if(msg.indexOf("สำเร็จ") > 0){
			_divMsg.css('color','green');
		}else if(msg.indexOf("ล้มเหลว") > 0){
			_divMsg.css('color', 'red');
		}
		_divMsg.show().fadeOut(5000);	
		// for (var _l = 0;_l < _intLoop;_l++) {
		// 	_divMsg.fadeOut(_intDelayHide).fadeIn(1000);
		// }
		if (_isPresist) {
			return _divMsg;
		} else {
			_divMsg.fadeOut(5000, function() { _divMsg.remove(); });
		}
	}
}
function _doClearToastMessage() {
	$('div.cls-div-toast-message', 'body').remove();
}
function _joomlaIframeWrapper_resizeToFit() {
	if ((window.parent) && (window.parent.frames.length > 0)) {
		window.parent.frames[0].frameElement.style.height = (window.parent.innerHeight - 120) + 'px'; //current window height - template header / footer // window.parent.document.body.scrollHeight
		//page real height //(window.document.body.scrollHeight + 50) + 'px'; //$(window.document).height()
	}
}