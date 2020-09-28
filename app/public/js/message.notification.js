//var _curMsgObj = {};
var _fnc__autoloadUserMessageList = function() {
	var _mainMenu = $('ul.nav.menu', window.parent.document);
	if (_mainMenu.length <= 0) return false;

	var _url = 'notify/json_list_message_notification';
	$.ajax({
		type: "POST", url: _url, contentType: "application/json;charset=utf-8"
		, success: function(data, textStatus, jqXHR) {
			if (typeof data == 'string') data = JSON.parse(data);
			if ((data) && ('success' in data) && (data.success)) {
				var _error = ('error' in data) ? data.error : 0;
				if (_error < 0) {
					switch (_error) {
						case '-7':
						case -7:
							alert('Session expired:: force reload page to re-login.');
							window.location.reload();
							break;
					}
					return false;
				}
				
				var _data = ('data' in data)? data.data: [];
				$('.user-msg-notify-header-notice', window.parent.document).hide();
				$('div.user-msg-notify-alert-container', window.parent.document).empty().hide();
				$('.user-msg-notify-menu', window.parent.document).removeClass('user-msg-notify-menu');

				__fncPopupWebNotify(_data);
				for (var _i in _data) {
					var _ea = _data[_i];
					if (typeof(_ea) != 'function') {
						var _notifyCode = (_ea["code"] || '').trim().toLowerCase();
						var _items = parseInt(_ea["items_count"] || 0);
						if ((_notifyCode != '')) {
							__fncUpdateMsgNotifyIcon(_notifyCode, _ea);
						}
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

function ___fncDisplayWebNotify(objToNotify) {
	if ($.isEmptyObject(objToNotify)) return false;
	if (Notification.permission != "granted") {
		Notification.requestPermission(function (_perm) {
			if (_perm === "granted") ___fncDisplayWebNotify(objToNotify);
		});
		return false;
	} else {
		for (var _title in objToNotify) {
			var _obj = objToNotify[_title];
			if (! ((typeof _obj == 'object') && ("body" in _obj))) continue;

			var _mainWindow = window.parent || window;
			var _currUrl = _mainWindow.location.href;
			var _asgnUrl = (("url" in _obj) && (_obj["url"] != false)) ? (_obj["url"]).trim() : false;
			if ((_asgnUrl) && (getAbsoluteURL(_currUrl) == getAbsoluteURL(_asgnUrl))) {
				alert(_obj["body"]);
				location.reload();
				_mainWindow.focus();
			} else {
				var _objParams = {"body": _obj["body"], "requireInteraction": true};
				if (("icon" in _obj) && (_obj["icon"] != false)) _objParams["icon"] = _obj["icon"];
				var _objNotif = new Notification(_title, _objParams);
				//_objNotif.onclose = function() { alert(':('); };
				_objNotif.onclick = function() {
					_mainWindow.location.href = _asgnUrl;
					_mainWindow.focus();
					//setTimeout(_objNotif.close.bind(_objNotif), 30000); 
				};
			}
		}
	}
}
function __fncPopupWebNotify(objData) {
	var _arrObj = objData || false;
	if (! ($.isArray(_arrObj) && ("Notification" in window))) return false; //alert("Your browser doesn't support notfication API");

	var _objNotifyItems = {};
	var _lastStamp = ((typeof(sessionStorage) != "undefined") && (sessionStorage.getItem("nofity_message_timestamp"))) ? parseInt(sessionStorage.getItem("nofity_message_timestamp")) : -1;
	for (var _i in _arrObj) {
		var _obj = _arrObj[_i];
		if (! $.isPlainObject(_obj)) continue;
		
		var _thisStamp = ('latest_update_timestamp' in _obj) ? parseInt(_obj['latest_update_timestamp']) : 0
		if (_thisStamp <= _lastStamp) continue;
		
		if (typeof(sessionStorage) != "undefined") sessionStorage.setItem('nofity_message_timestamp', _thisStamp);
		var _notifyCode = ('code' in _obj) ? (_obj["code"] || '').trim().toLowerCase() : false;
		var _msgType = ('msg_type' in _obj) ? (_obj["msg_type"] || '').trim().toLowerCase() : false;
		var _title = ('title' in _obj) ? (_obj["title"] || '').trim() : false;
		var _desc = ('description' in _obj) ? (_obj["description"] || '').trim() : false;
		var _items = parseInt(_obj["items_count"] || 0);
		var _title = false, _strBody = false, _icon = false, _url = false;
		switch (_notifyCode) {
			case "quotation":
				_title = 'ใบเสนอราคา';
				_icon = "public/images/icons/32/window_new.png";
				switch (_msgType) {
					case "status":
						_strBody = 'รอการปรับสถานะ "' + _desc + '" จำนวน ' + _items + ' ใบงาน';
						_url = 'quotation';
						break;
					case "action":
						_strBody = _desc + ' จำนวน ' + _items + ' ใบงาน';
						_url = 'quotation';
						break;
				}
				break;
		}
		if (_strBody) {
			if (! (_title in _objNotifyItems)) {
				_objNotifyItems[_title] = {"body": _strBody, "icon": _icon, "url": _url};
			} else {
				_objNotifyItems[_title]["body"] += "\n" + _strBody;
			}
		}
	}
	if (! $.isEmptyObject(_objNotifyItems)) ___fncDisplayWebNotify(_objNotifyItems);		
}
function __fncUpdateMsgNotifyIcon(notifyCode, notifyObj) {
	var _code = (notifyCode || '').trim();
	var _obj = notifyObj || false;
	if ((_code.length <= 0) || (! _obj)) return false; 

	var _mainMenu = $('ul.nav.menu', window.parent.document);
	if (_mainMenu.length <= 0) return false;

	var _cntr = $('li[data-id][data-level] > a[class="notify--' + _code + '"]', _mainMenu);
	if (_cntr.length <= 0) return false;

	var _items = parseInt(_obj["items_count"] || 0);
	_cntr.addClass('user-msg-notify-menu');
	var _div = $('div.user-msg-notify-alert-container', _cntr);
	if (_div.length > 1) _div.remove();
	if (_div.length == 0) {
		_div = $('<div>')
			.addClass('user-msg-notify-alert-container')
			.on('click', function() {
				
			})
			.appendTo(_cntr)
	}
	_div.html(_items).show();
	if (! _cntr.is(':visible')) {
		var _visRoot = (_cntr.parents(':visible').length > 0) ? $(_cntr.parents(':visible')[0]) : false;
		if (_visRoot) {
			var _hdrNtf = $('div.user-msg-notify-header-notice', _visRoot);
			if (_hdrNtf.length == 0) {
				_hdrNtf = $('<div class="user-msg-notify-header-notice">').appendTo(_visRoot);
			} else {
				_hdrNtf.show();
			}
		}
	}
}

var __autoReloadMsg = new __clsAutoReloadSearch();
$( document ).ready(function() {
	//++ inject CSS style to parent
	if (window.parent) {
		$('head', window.parent.document).append('<style type="text/css">\n@keyframes blinker {35% {opacity:0.3;} 0%, 70%, 100% {opacity:1;}}\ndiv.user-msg-notify-header-notice {display:block;position:absolute;top:.5em;right:-.5em;height:16px;width:16px;background:transparent url("../app/public/images/warning.png") no-repeat;animation:blinker 2s linear infinite;}\n.user-msg-notify-menu {animation: blinker 2s linear infinite;}\ndiv.user-msg-notify-alert-container {width:2.5em;height:2.5em;cursor:pointer;position:absolute;right:-20px;top:-5px;display:block;background-color:red;color:yellow;font-weight:bold;line-height:2.4em;font-size:10px;text-align:center;vertical-align:middle;border:1px dashed red;border-radius:2em;opacity:0.8;z-index:1000;animation:blinker 2s linear infinite;}\n</style>');
	}
	$(window).on('beforeunload', function() {
		if ((typeof __autoReloadMsg == 'object') && (typeof __autoReloadMsg.isActive == 'function') && (__autoReloadMsg.isActive())) {
			__autoReloadMsg.stop();
		}
	});
	var _mainMenu = $('ul.nav.menu', window.parent.document);
	if (_mainMenu.length > 0) {
		$('li > a[href]', _mainMenu).each(function() {
			var _a = $(this);
			var _li = _a.parent('li');
			var _ctrl = false;
			var _href = (_a.attr('href') || '').trim();
			if (_href.indexOf('/') >= 0) {
				var _arr = _href.split('/');
				_ctrl = (_arr.pop() || '').trim();
				while ((_ctrl == '') && (_arr.length > 0)) {
					_ctrl = (_arr.pop() || '').trim();
				}
			} else {
				_ctrl = _href;
			}
			if (_ctrl.trim() == '') return true;
			
			if (_li.length > 0) {
				_li.attr('data-id', _ctrl).attr('data-level', _li.parents('ul').length - 1)
					.prepend($('<a>').addClass("notify--" + _ctrl));
			}
		});
	}

	_fnc__autoloadUserMessageList();
	__autoReloadMsg.start(_fnc__autoloadUserMessageList, 50000); //default 50 seconds interval	
});