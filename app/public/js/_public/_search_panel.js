var _blnDataChanged = false;
var _objDataTable;
//var _arrayQueriedData;
var _currentDataString; //use in re-query after change data
var _onload_time;
$(function() {
	$("#btnSearch").button().click(function() {
		doSearch(true);
	});
	$("#btnReset").button();
	$("#dialog-modal").html("<p>" + MSG_DLG_HTML_QUERY + "</p>");
	$("#dialog-modal").dialog({
		height:100,
		width:400,
		resizable:false,
		modal:true,
		closeOnEscape:false,
		title: MSG_DLG_TITLE_QUERY,
		autoOpen:false
	});
	if (_autoSearch_OnLoad != false) {
		doSearch(true);
	} else {
		doPopulateTable([], true);
	}
});

function doSearch(blnChangeSearchCriteria, opt_fncCallBack) {	  
	var _index = $('#divDisplayQueryResult').attr('index') || 0;
	//if (_aoColumns.length == 0) return;
	
	var _blnChangeSearchCriteria = typeof blnChangeSearchCriteria != undefined?blnChangeSearchCriteria:true;
	doClearDisplayInfo(_index);
	doClearDisplayError(_index);
	//if (_objDataTable) _objDataTable.fnClearTable();
	var _dlgMdl = _getOpenWaitModalDialog();
	if (_blnChangeSearchCriteria) {
		$("#dialog-modal").dialog( "open" );
		_currentDataString = "";			
		var _update = {};
		$(".search-param").each(
			function () {
				var _tag = this.tagName.toLowerCase();
				var _type = this.type;
				var _name;
				var _val =_getElemValue(this);
				if (_tag == 'input' && ((_type == 'radio') || ((_type == 'checkbox') && ($(this).hasClass('cls-toggle-label'))))) {
					_name = $(this).attr('name');
					if (_val === false) _val = undefined;
				}
				if ((typeof _name == 'undefined') || (_name == '')) _name = $(this).attr('id').substr(4);
				if (_name.indexOf('[]') >= 0) {
					_name = _name.replace(/\[\]/g, '');
					if (_name in _update) {
						if (_val) _update[_name] += ',' + _val;
					} else {
						if (_val) _update[_name] = _val;
					}
				} else {
					if (_val) _update[_name] = _val;
				}
			}
		);
		for (key in _update) {
			_currentDataString = _currentDataString + key + '=' + _update[key] + '&';
		}
		if (_currentDataString.length > 0) _currentDataString = _currentDataString.substr(0, _currentDataString.length - 1);
	}
	$.ajax({
		type: "POST", 
		url: "./" + CONTROLLER_NAME + "/json_search",
		//contentType: "application/json;charset=utf-8",
		dataType: "json",
		data: _currentDataString,
		success: function(data, textStatus, jqXHR) {
			if (data.success == false) {
				var _msg = MSG_ALERT_QUERY_FAILED.replace(/v_XX_1/g, data.error);
				doDisplayError('frmSearchFailed', _msg, true, _index);
				if (typeof opt_fncCallBack == 'function') opt_fncCallBack.apply(this, [false]);
			} else {
				data = data.data;
				var _arrayData = [];
				if (data.length > 0) {
					for (var i = 0; i < data.length; i++) {
						_arrayData[i] = {'client_temp_id':i};
						for (j=0;j<_arrDtColumns.length;j++) {
							_arrayData[i][_arrDtColumns[j][0]] = (data[i][_arrDtColumns[j][0]] == null)?'':data[i][_arrDtColumns[j][0]];
						}
					}
				} else {
					doDisplayInfo(MSG_ALERT_QUERY_NO_DATA_FOUND, 'Info', _index);
				}
				if (typeof opt_fncCallBack == 'function') {
					doPopulateTable(_arrayData, _blnChangeSearchCriteria, function() {
						opt_fncCallBack.apply(this, arguments);
					});
				} else {
					doPopulateTable(_arrayData, _blnChangeSearchCriteria);
				}
			}
			$("#dialog-modal").dialog( "close" );	
			
			setTimeout(
				function() 
				{
					_onload_time = new Date().toLocaleString('th-TH',{hour12:false});
					// console.log(_onload_time)
				},0);			

		},
		error: function(jqXHR, textStatus, errorThrown) {
			$("#dialog-modal").dialog( "close" );
			doDisplayInfo(MSG_ALERT_QUERY_FAILED.replace(/v_XX_1/g, textStatus + ' ( ' + errorThrown + ' )'), "ErrorMessage", _index);
			if (typeof opt_fncCallBack == 'function') opt_fncCallBack.apply(this, arguments);
		},
		statusCode: {
			404: function() {
				$("#dialog-modal").dialog( "close" );
				doDisplayInfo("Page not found", "ErrorMessage", _index);
				if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
			}
		}
	});
	return false;
}

function datePostFormat (localStringDate){
	var year = parseInt(localStringDate.substring(6,11));

	if(year > new Date().getFullYear()){
		localStringDate = localStringDate.replace(year, year -= 543);
	}
	
	return localStringDate;
}