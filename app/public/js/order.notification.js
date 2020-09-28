var _currNotifyObj = {};
var _fnc__autoloadUserNotificationList = function() {
	if (typeof CONTROLLER_NAME == 'undefined') return false;
	
	var _mainMenu = $('ul.nav.navbar-nav.level0', window.parent.document);
	if (_mainMenu.length <= 0) return false;

	var _cntr = $('li[data-id][data-level] > a[class^="notify--"]', _mainMenu);
	if (_cntr.length <= 0) return false;
	
	var _url = CONTROLLER_NAME + '/json_list_order_notification';
	$.ajax({
		type: "POST", url: _url, contentType: "application/json;charset=utf-8"
		, success: function(data, textStatus, jqXHR) {
			if (typeof data == 'string') data = JSON.parse(data);
			if ((data) && ('success' in data) && (data.success)) {
				var _data = ('data' in data) ? data.data : [];
				$('.user-notification-header-notice', window.parent.document).hide();
				$('div.user-notification-alert-container', window.parent.document).empty().hide();
				$('.user-notification-menu', window.parent.document).removeClass('user-notification-menu');
				if (JSON.stringify(_currNotifyObj) != JSON.stringify(_data)) {
					_currNotifyObj = _data;
				}
				for (var _i in _data) {
					var _ea = _data[_i];
					if (typeof(_ea) != 'function') {
						var _notifyCode = (_ea["code"] || '').trim().toLowerCase();
						if ((_notifyCode != '')) __fncUpdateNotifyIcon(_notifyCode, _ea);
					}
				}
			} else {
				console.error('User notification failed:: Invalid return result');
				console.error(data);
			}
		}
		, error: function(jqXHR, textStatus, errorThrown) {
			console.error('User notification request failed:: ' + textStatus + ' ( ' + errorThrown + ' )');
		}
		, statusCode: {
			404: function() {
				console.error("User notification request failed:: Page not found");
			}
		}
	});
};
function __fncUpdateNotifyIcon(notifyCode, notifyObj) {
	var _code = (notifyCode || '').trim();
	var _obj = notifyObj || false;
	if ((_code.length <= 0) || (! _obj)) return false; 
	
	var _mainMenu = $('ul.nav.navbar-nav.level0', window.parent.document);
	if (_mainMenu.length <= 0) return false;

	var _cntr = $('li[data-id][data-level] > a[class="notify--' + _code + '"]', _mainMenu);
	if (_cntr.length <= 0) return false;
	
	var _depCode = (_obj["dep_code"] || '').trim().toLowerCase();
	var _depNameTh = (_obj["dep_name_th"] || '').trim();
	var _procCode = (_obj["field"] || '').trim().toLowerCase();
	var _notifyCode = (_obj["code"] || '').trim().toLowerCase();

	var _ipr = (("ipr" in _obj) && (_isInt(_obj["ipr"]))) ? parseInt(_obj["ipr"] || 0) : 0;
	var _rjt = (("rjt" in _obj) && (_isInt(_obj["rjt"]))) ? parseInt(_obj["rjt"] || 0) : 0;
	var _cmp = (("cmp" in _obj) && (_isInt(_obj["cmp"]))) ? parseInt(_obj["cmp"] || 0) : 0;
	if ((_ipr + _rjt) > 0) {
		_cntr.addClass('user-notification-menu');
		var _div = $('div.user-notification-alert-container', _cntr);
		if (_div.length > 1) _div.remove();
		if (_div.length == 0) {
			_div = $('<div>')
				.addClass('user-notification-alert-container')
				.on('click', function() {
					
				})
				.appendTo(_cntr)
			;
		}
		var _disp = (_ipr || 0) + (_rjt || 0);
		var _total = (_ipr || 0) + (_rjt || 0) + (_cmp || 0)
		_div.html(_disp+'/'+_total).show();

		if (! _cntr.is(':visible')) {
			var _visRoot = (_cntr.parents(':visible').length > 0) ? $(_cntr.parents(':visible')[0]) : false;
			if (_visRoot) {
				var _hdrNtf = $('div.user-notification-header-notice', _visRoot);
				if (_hdrNtf.length == 0) {
					_hdrNtf = $('<div class="user-notification-header-notice">').appendTo(_visRoot);
				} else {
					_hdrNtf.show();
				}
			}
		}
	}
}

var __autoReloadNotify = new __clsAutoReloadSearch();
$( document ).ready(function() {
	//++ inject CSS style to parent
	if (window.parent) {
		$('head', window.parent.document).append('<style type="text/css">\n/*@keyframes blinker {35% {opacity:0.3;} 0%, 70%, 100% {opacity:1;}}*/\ndiv.user-notification-header-notice {display:block;position:absolute;top:.5em;right:-.5em;height:16px;width:16px;background:transparent url("../app/public/images/warning.png") no-repeat;animation:blinker 2s linear infinite;}\n.user-notification-menu {animation: blinker 2s linear infinite;}\ndiv.user-notification-alert-container {width:2.5em;height:2.5em;cursor:pointer;position:relative;float:right;margin-top:-.5em;margin-right:-2em;display:block;background-color:yellow;color:red;font-weight:bold;line-height:2.4em;font-size:10px;text-align:center;vertical-align:middle;border:1px dashed red;border-radius:2em;opacity:0.8;z-index:1000;animation:blinker 2s linear infinite;}\n</style>');
	}
	$(window).on('beforeunload', function() {
		if ((typeof __autoReloadNotify == 'object') && (typeof __autoReloadNotify.isActive == 'function') && (__autoReloadNotify.isActive())) {
			__autoReloadNotify.stop();
		}
	});

	_fnc__autoloadUserNotificationList();
	__autoReloadNotify.start(_fnc__autoloadUserNotificationList, 30000); //default 30 seconds interval	
});
