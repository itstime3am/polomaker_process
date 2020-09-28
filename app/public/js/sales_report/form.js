	//++ Browser detection//
	var browser = (function (pub) {
		var matched, browserObj;
		uaMatch = function(ua) {
			ua = ua.toLowerCase();
			//This fixes an ie7 bug that causes crashes from incorrect version identification
			if(/*@cc_on/*@if(@_jscript_version<=5.6)1@else@*/0/*@end@*/) {
				ua = "msie 6.0";
			}
			var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
				/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
				/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
				/(msie) ([\w.]+)/.exec( ua ) ||
				ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
				[];
	 
			return {
				browserObj: match[ 1 ] || "",
				version: match[ 2 ] || "0"
			};
		};
	 
		matched = uaMatch(navigator.userAgent);
		browserObj = {};
	 
		if ( matched.browserObj ) {
			browserObj[ matched.browserObj ] = true;
			browserObj.version = matched.version;
		}
		// Chrome is Webkit, but Webkit is also Safari.
		if (browserObj.chrome) {
			browserObj.webkit = true;
		} else if (browserObj.webkit) {
			browserObj.safari = true;
		}
	 
		pub = browserObj;   
		return pub;
	}(browser || {}));
	//-- Browser detection//
	var _placeholders = [];
	var _base_options;
	if (browser.msie) {
        _base_options = {
			canvas: true,
            lines: {
                show: true,
                lineWidth: 1
            },
            points: { show: false },
			bars: {
				show: true,
				barWidth: 2,
				align: "center",
				fill: true,
				fillColor: { colors: [ { opacity: 0.8 }, { opacity: 0.1 } ] },
				horizontal: false
			},
			crosshair: {
				mode: "x",
				color: 'lightgray',
				lineWidth: 1
			},
            grid: { hoverable: true, clickable: true, labelMargin: 5, borderWidth:2, autoHighlight: true, mouseActiveRadius: 10 },
            xaxis: { mode: "time", timeformat: "%d/%m/%y<br>%H:%M:%S" },
            legend: {
                show: false,
                labelBoxBorderColor: null,
                noColumns: 3,
                backgroundColor: null,
                backgroundOpacity: 0.5,
                container: $('#' + this._divLegendID)
            }
        };
    } else {
/*
        _base_options = {
			canvas: true,
            lines: { show: true, lineWidth: 1 },
            points: {
                show: true,
                radius: 0.5,
                symbol: "circle"
            },
			bars: {
				show: true,
				barWidth: 2,
				align: "center",
				fill: true,
				fillColor: { colors: [ { opacity: 0.8 }, { opacity: 0.1 } ] },
				horizontal: false
			},
			crosshair: {
				mode: "x",
				color: 'lightgray',
				lineWidth: 1
			},
            grid: { hoverable: true, clickable: true },
            xaxis: { mode: "time", timeformat: "%d/%m/%y\n%H:%M:%S" },
			legend: {
				show: false,
                labelBoxBorderColor: null,
                noColumns: 3,
                backgroundColor: null,
                backgroundOpacity: 0.5,
				//Needed to position legend in canvas ( used in jquery.flot.legendoncanvas.js )
                position: "ne", // position of default legend container within plot
                margin: 5 // distance from grid edge to default legend container within plot
				//Needed to position legend in canvas ( used in jquery.flot.legendoncanvas.js )
                //container: $('#' + this._divLegendID) //Open this is need html legend display (remove javascript jquery.flot.legendoncanvas.js from page)
			}
        };
*/
		_base_options = {
			canvas: true
			,series: {
				bars: {
					show: true
					,align: "center"
					,barWidth: 0.5
					,fill: true
				}
			}
			//,colors: ['red', 'transparent']
			,xaxes: [
				{
					min:0
					,max:1
					,font: {size:12, color:'#000000', weight:'bold'}
					,reserveSpace: true
					,labelWidth: 10
					,labelHeight: 14
					,tickLength: 0
					,ticks:[]
				},
				{
					min:0
					,max:1
					,font: {size:12, color:'blue'}
					,reserveSpace: true
					,labelWidth: 10
					,labelHeight: 14
					,tickColor: 'transparent'
					,ticks:[]
				}
			]
			,yaxis: {
				min:0
				,font: {size:9, color:'#000000'}
				,labelWidth: 80
				,reserveSpace: true
				,tickFormatter: function (v, axis) {
					return formatNumber(v);
				}
			}
			,legend: {
				show: false
				,noColumns: 1
				,labelBoxBorderColor: null //"#000000",
				,backgroundOpacity: 0.5
				,position: "ne"
				,margin: 5 // distance from grid edge to default legend container within plot
			}
			,grid: {
				hoverable: false
				,borderWidth: 2
				,backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
			}
		};
    }
	
	$(function () {
		var _fncTemplate_doSearch = doSearch;
		var _fncTemplate_doPopulateTable = doPopulateTable;
			
		doSearch = function(blnChangeSearchCriteria) {
			var _blnChangeSearchCriteria = (typeof blnChangeSearchCriteria != undefined)?blnChangeSearchCriteria:true;
			doClearDisplayInfo();
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
				_currentDataString = JSON.stringify(_update);
			}
			var _format = 'dd/mm/yy';//$('#frmSearch #txt-date_from').datepicker('option', "dateFormat");
			var _strdatefrom = $('#frmSearch #txt-date_from').val();
			var _strdateto = $('#frmSearch #txt-date_to').val();
			var _datDateFrom = $.datepicker.parseDate(_format, _strdatefrom);
			var _datDateTo = $.datepicker.parseDate(_format, _strdateto);
			var _strPeriodRange = ': จากวัน ' + _datDateFrom.format('dddd ที่ dd mmmm yyyy', false, 'th') + ' ถึงวันที่ ' +  _datDateTo.format('dddd ที่ dd mmmm yyyy', false, 'th');
			$('.spn-period-range').html('');
			$('.spn-sum-val').html('0');
			$.ajax({
				type:"POST",
				url:"./sales_report/json_sales_report",
				contentType:"application/json;charset=utf-8",
				dataType:"json",
				data:_currentDataString,
				success: function(data, textStatus, jqXHR) {
					if (data.success == false) {
						alert(MSG_ALERT_QUERY_FAILED.replace(/v_XX_1/g, data.error));
						$("#dialog-modal").dialog( "close" );
					} else {
						$('.spn-period-range').html(_strPeriodRange);
						doDisplayGraph(data.data);				
						$("#dialog-modal").dialog( "close" );
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					doDisplayInfo(textStatus + ' : ' + errorThrown, "ErrorMessage");
					if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
					$("#dialog-modal").dialog( "close" );
				},
				statusCode: {
					404: function() {
						doDisplayInfo("Page not found", "ErrorMessage");
						if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
						$("#dialog-modal").dialog( "close" );
					}
				}
			});
		};

		$('#txt-date_from')
			.on("change", _evntOnDateChange)
			.datepicker('destroy')
			.datepicker({
				showOn: "both",
				buttonImage: "public/images/select_day.png",
				buttonImageOnly: true,
				dateFormat: 'dd/mm/yy',
				onSelect: function() { $(this).change(); }
			});
		$('#txt-date_to')
			.on("change", _evntOnDateChange)
			.datepicker('destroy')
			.datepicker({
				showOn: "both",
				buttonImage: "public/images/select_day.png",
				buttonImageOnly: true,
				dateFormat: 'dd/mm/yy',
				onSelect: function() { $(this).change(); }
			});

		$('#divPanelHandler').click(function() {
			doToggleLeftPanel();
		});

		_evntOnDateChange();
		doDisplayGraph([]);
	});

	function _evntOnDateChange() {
		if ($('#txt-date_from').datepicker("getDate") > $('#txt-date_to').datepicker("getDate")) return false;
		
		$("#dialog-modal").dialog( "open" );
		var _strFrom = getValue($('#txt-date_from'));
		var _strTo = getValue($('#txt-date_to'));
		$.ajax({
			type:"POST",
			url:"./sales_report/json_list_available_sales",
			contentType:"application/json;charset=utf-8",
			dataType:"json",
			data: JSON.stringify({"branch_id": _BRANCH_ID, "date_from": _strFrom, "date_to": _strTo}),
			success: function(data, textStatus, jqXHR) {
				var _div = $('#div_user_select').empty();
				if (data.success == false) {
					alert(MSG_ALERT_QUERY_FAILED.replace(/v_XX_1/g, data.error));
					$("#dialog-modal").dialog( "close" );
				} else {
					_div.html('ไม่พบข้อมูลในช่วงเวลาที่เลือก');
					if ($.isArray(data.data)) {
						if (data.data.length > 0) {
							_div.html('');
							for (var _i=0;_i < data.data.length;_i++) {
								var _row = data.data[_i];
								_div.append('<input type="checkbox" class="search-param cls-toggle-label" id="' + _row['id'] + '" checked name="user_list[]" value="' + _row['id'] + '" /><label class="cls-toggle-label" for="' + _row['id'] + '" >' + _row['name'] + '</label>');
							}
						}
					}
					$("#dialog-modal").dialog( "close" );
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				doDisplayInfo(textStatus + ' : ' + errorThrown, "ErrorMessage");
				if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
				$("#dialog-modal").dialog( "close" );
			},
			statusCode: {
				404: function() {
					doDisplayInfo("Page not found", "ErrorMessage");
					if (typeof opt_fncCallback == 'function') opt_fncCallback.apply(this, arguments);
					$("#dialog-modal").dialog( "close" );
				}
			}
		});		
	}
		
	doPopulateTable = function() {
		//do nothing, no table this page
	};

	function doDisplayGraph(objData) {
		var _ticks = ('ticks' in objData)?objData['ticks']:[];
		for (var _i=0;_i<3;_i++) {
			var _placeholder = $('#div_place_holder_' + (_i + 1));
			var _spnSum = $('#spn_sum_' + (_i + 1));
			
			var _str = 'plot' + (_i + 1);
			var _data = (_str in objData)?objData[_str]:[];
			if (_data.length <= 0) {
				_placeholders[_i] = $.plot(_placeholder, [{data:[]}], _base_options);
				continue;
			}
			var _sum = _data.sum();
			if (_i == 0) {
				_spnSum.html(formatNumber(_sum, 0));
			} else {
				_spnSum.html(formatNumber(_sum, 2));				
			}
			var _min = _ticks[0][0] - 0.5;
			var _max = _ticks[_ticks.length - 1][0] + 0.5;
			var _tickAxes2 = [];
			var _dataSet = [];
			for (var _j=0;_j<_ticks.length;_j++) {
				_idx = _ticks[_j][0];
				_tickAxes2.push([_idx, formatNumber(_data[_j])]);
				_dataSet.push({label:_ticks[_j][1], data:[[_idx, _data[_j]]]});
				_dataSet.push({data:[[_idx, null]], xaxis: 2});
			}
			var _xaxes = [
				{
					min: _min
					,max: _max
					,ticks: _ticks
				},
				{
					min: _min
					,max: _max
					,ticks: _tickAxes2
				}
			];
			var _option = $.extend(true, {}, _base_options, {xaxes: _xaxes});
			_placeholders[_i] = $.plot(_placeholder, _dataSet, _option);
		}
	}

	var _leftPanelHide = 0;
	var _leftPanelShow = 290;
	function doToggleLeftPanel() {
		if ($('#left_panel').css('display') !== 'none') {
			$('#left_panel').css('display', 'none');
			$('#work_panel').css('margin-left', (_leftPanelHide+10) + 'px');
			$('#divPanelHandler').css('left', _leftPanelHide + 'px');
		} else {
			$('#left_panel').css('display', 'block');
			$('#work_panel').css('margin-left', (_leftPanelShow+10) + 'px');
			$('#divPanelHandler').css('left', _leftPanelShow + 'px');
		}
		if (_placeholders.length > 0) {
			for (_x in _placeholders) {
				if (typeof _placeholders[_x] == 'object') {
					_placeholders[_x].resize();
					_placeholders[_x].setupGrid();
					_placeholders[_x].draw();
				}
			}
		}
	}
/*	test value	
		$.plot($('#div_place_holder_1'), 
			[
				{label:'ทดสอบ1', data:[[1, 23]]},
				{data:[[1, null]], xaxis:2},
				{label:'ทดสอบ2', data:[[2, 26]]},
				{data:[[2, null]], xaxis:2},
				{label:'ทดสอบ3', data:[[3, 12]]},
				{data:[[3, null]], xaxis:2}
			], 
			{
				canvas: true,
				series: {
					bars: {
						show: true,
						align: "center",
						barWidth: 0.5,
						fill: true
					}
				},
				//colors: ['red', 'transparent'],
				xaxes: [
					{
						min: 0.5,
						max: 3.5,
						font: {size:12, color:'#000000', weight:'bold'},
						labelWidth: 10,
						tickLength: 0,
						ticks: [[1, 'test'],[2, 'test2'],[3, 'test2']]						
					},
					{
						min: 0.5,
						max: 3.5,
						font: {size:12, color:'blue'},
						labelWidth: 10,
						tickColor: 'transparent',
						ticks: [[1, '3455'],[2, '1235'],[3, 'test2']]						
					}
				],
				yaxis: {
					axisLabelUseCanvas: true,
					font: {size:9, color:'#000000'},
					labelWidth: 80,
					tickFormatter: function (v, axis) {
						return formatNumber(v);
					}
				},
				legend: {
					show: false,
					noColumns: 1,
					labelBoxBorderColor: null, //"#000000",
					backgroundOpacity: 0.5,
					position: "ne", 
					margin: 5 // distance from grid edge to default legend container within plot
				},
				grid: {
					hoverable: true,
					borderWidth: 2,
					backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
				}
			}
		);
*/		
