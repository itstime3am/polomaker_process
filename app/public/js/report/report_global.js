
$.editable.addInputType('datepicker', {
	element: function(settings, original) {
		var input = $('<input />');
		input.datepicker({
			dateFormat: 'dd/mm/yy',
			onSelect: function(dateText, inst) {
				$(this).parents("form").submit();
				inst.submit = true;
			},
			onClose: function(dateText, inst) {
				if ('submit' in inst) {
					delete inst['submit'];
				} else {
					$(this).parents("form").submit();
				}
			}
		});
		$(this).append(input);
		
		settings.onblur = function (value, settings) {
			if ($('input', this).datepicker( "widget" ).is(":visible")) {
				return false;
			} else {
				return value;
			}
		};

		return (input);
	},
	plugin: function(settings, original) {
		var form = this;
		$("input", this).filter(":text").datepicker({
			onSelect: function(dateText) { $(this).hide(); $(form).trigger("submit"); }
		});
	}
});

$.editable.addInputType('datetimepicker', {
	element: function(settings, original) {
		var input = $('<input />');
		input.datetimepicker({
			showOn: "both"
			,changeYear: true
			,changeMonth: true
			,buttonImage: "public/images/select_day.png"
			,buttonImageOnly: true
			,dateFormat: 'dd/mm/yy'
			,timeFormat: 'HH:mm'
			,timeInput: true
			,maxDate: new Date()
			,onClose: function(dateText, inst) {
				if ('submit' in inst) {
					delete inst['submit'];
				} else {
					$(this).parents("form").submit();
				}
			}
		});
		$(this).append(input);
		
		settings.onblur = function (value, settings) {
			if ($('input', this).datetimepicker( "widget" ).is(":visible")) {
				return false;
			} else {
				return value;
			}
		};
		return (input);
	},
	plugin: function(settings, original) {
		var form = this;
		$("input", this).filter(":text").datetimepicker({
			onSelect: function(dateText) { $(this).hide(); $(form).trigger("submit"); }
		});
	}
});

var _objChangedData = {};
var _OBJ_CHANGED_DATA = {};
var _objDataTableFixedColumns;
$(window).load(function() {
	//++ Warning leaving page without save
	$(window).on('beforeunload', function() {
		if (_fnCheckDataChanged()) return MSG_CONFIRM_LEAVE_PAGE_WITHOUT_SAVE;
	});
	//-- Warning leaving page without save

	//++ Add special datatable properties and tools
	if (_aoColumns) {
		for (_i=0;_i<_aoColumns.length;_i++) {
			delete _aoColumns[_i].sTitle;
		}
	}
	/*
	if (_tableToolButtons) {
		_tableToolButtons.push({"text": "&nbsp;","className": "DTTT_button_space"});
		_tableToolButtons.push({
			"text": "บันทึกข้อมูล"
			, "className": "DTTT_button_commit_page DTTT_button_disabled"
			, "fnClick": function ( nButton, oConfig, oFlash ) {
				if (! _fnCheckDataChanged()) return false;
				if ($('td.edit form').length > 0) return false; //Editing
				_doCommitPage();
			}
		});
	}
	*/
	//-- Add special datatable properties and tools
	
	var _fnDefault_doPopulateTable = doPopulateTable;
	var _fnDefault_doSearch = doSearch;
	var _fnDefault__doResize = _doResize;
	
	doSearch = function (blnChangeSearchCriteria, blnCheckChange, fncCallBack) {
		var _blnCheckChange = (typeof blnCheckChange == 'boolean')? blnCheckChange : true;
		if (_blnCheckChange && _fnCheckDataChanged()) {
			if ( ! confirm(MSG_CONFIRM_LEAVE_PAGE_WITHOUT_SAVE)) return false;
		}
		_fnDefault_doSearch.apply(this, [blnChangeSearchCriteria, fncCallBack]);
		$("#dialog-modal").dialog( "open" );
	};
	
	_doResize = function () {
		_objDataTable.fnAdjustColumnSizing();
	};

	// ++ first set disabled 
	$(".search-param.set-disabled").each(function () {
		_setEnableElem(this, false);
	});
	// -- first set disabled 
});

if (_ALLOW_EDIT === false) {
	_doSetDataTablePagePlugins = function() {
		//do nothing;
	};
} else {
	_doSetDataTablePagePlugins = function () {
		if (typeof _objDataTable != 'undefined') {
			//fire everytime change layout		
			if (($('#tblSearchResult tbody tr td.edit').length > 0) && (typeof $($('#tblSearchResult tbody tr td.edit').get(0)).attr('title') == 'undefined')) {
				//$('#tblSearchResult tbody tr').off("DOMSubtreeModified", 'td.edit');
				$('div.DTFC_LeftBodyLiner table tbody tr td.edit').off( "mouseenter mouseleave" );
				//_doSetEditableColumns();
				//_objDataTable.fnAdjustColumnSizing();
			} else {
				if (($('div.DTFC_LeftBodyLiner table thead') != 'undefined') && ($('div.DTFC_LeftBodyLiner table thead').css('visibility') != 'hidden')) {
					if (typeof _objDataTableFixedColumns != 'undefined') _objDataTableFixedColumns.fnRedrawLayout();//fnUpdate();
					$('div.DTFC_LeftBodyLiner table tbody tr td.edit').on('click', function () { 
						$('div.DTFC_LeftBodyWrapper').css('display', 'none');
					});

					$('#tblSearchResult thead').css('visibility', 'hidden');//dont user display:none because sort header and tbody will miss align
					$('div.DTFC_LeftBodyLiner table thead').css('visibility', 'hidden');//.css('display', 'none');
					
					$('div.DTFC_LeftWrapper table').css('background-color', 'white');
					$('div.DTFC_LeftBodyLiner table').css('background-color', 'white');

					//++ Adjust and set background to prevent transparent when scroll
					_fcWrapper = $('.DTFC_LeftWrapper');
					_fcBody = $('.DTFC_LeftBodyLiner');
					_w = _fcWrapper.width() || 0;
					if (_w > 0) _fcWrapper.width(_w + 10);
					_w = _fcBody.width() || 0;
					if (_w > 0) _fcBody.width(_w + 10);
					//-- Adjust and set background to prevent transparent when scroll
				}
			}
		}
	};
}

function _fnCheckDataChanged() {
	var nButton = $('.DTTT_button_commit_page');
	if ($(nButton).hasClass('DTTT_button_disabled') || (JSON.stringify(_OBJ_CHANGED_DATA) == '{}') || ($('td.data-edit-changed').length <= 0)) {
		_OBJ_CHANGED_DATA = {};
		$(nButton).addClass('DTTT_button_disabled');
		return false;
	} else {
		$(nButton).removeClass('DTTT_button_disabled');
		return true;
	}
}

function _doUpdateSummaryValue(arrData) {
	var _arrSumList = {};
	$('span.cls-summary-value').each(function() {
		$(this).html('');
		_data = $(this).attr('field') || '';
		_arrSumList[_data] = 0;
	});
	if (arrData.length > 0) {
		for (_i=0;_i<arrData.length;_i++) {
			_obj = arrData[_i];
			for (_x in _arrSumList) {
				if ((_obj[_x]) && (! isNaN(_obj[_x]))) _arrSumList[_x] += parseFloat(_obj[_x]);
			}
		}
		$('span.cls-summary-value').each(function() {
			var _data = $(this).attr('field') || '';
			var _val = _arrSumList[_data];
			if (typeof _val == 'number') {
				$(this).html(formatNumber(_val, 2));
			} else {
				$(this).html('Err');
			}
		});
	}
}
