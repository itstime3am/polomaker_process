	$(function() {
		doPopulateTable = function (arrData, blnChangeSearchCriteria) {
			var _blnChangeSearchCriteria = typeof blnChangeSearchCriteria != undefined?blnChangeSearchCriteria:true;

			_objChangedData = {};
			if (_objDataTable) _objDataTable.fnDestroy(true);

			//$('#divDisplayQueryResult').html('<table id="tblSearchResult" class="cls-tbl-list"><thead><tr><th rowspan="2">สถานะ</th><th rowspan="2">เลขที่ใบสั่งตัด</th><th rowspan="2">เซลส์</th><th rowspan="2">ชื่อลูกค้า</th><th rowspan="2">บริษัท</th><th rowspan="2">กลุ่มสินค้า</th><th rowspan="2">ประเภทสินค้า</th><th rowspan="2">ชนิดผ้า</th><th rowspan="2">วันที่ส่งจริง</th><th rowspan="2">จำนวนตัว</th><th colspan="3" class="ui-state-default">ปักแยกตามร้าน</th><th colspan="3" class="ui-state-default">สกรีนแยกตามร้าน</th><th colspan="5" class="ui-state-default">ราคาเฉลี่ยต่อตัว</th><th rowspan="2">ราคารวม</th><th rowspan="2">VAT</th><th rowspan="2">รวมทั้งสิ้น</th><th colspan="3" class="ui-state-default">มัดจำ</th><th colspan="4" class="ui-state-default">งวดสุดท้าย</th><th rowspan="2">ตรวจสอบการชำระเงิน</th><th rowspan="2">หมายเหตุ</th></tr><tr><th>ปักโรงงาน</th><th>ปักพี่แดง</th><th>ปักร็อค</th><th>สกรีน DTG</th><th>สกรีนปุ้ย</th><th>สกรีนพี่นี</th><th>เสื้อรวม</th><th>สกรีนรวม</th><th>ปักรวม</th><th>อื่นๆรวม</th><th>ราคารวม</th><th>จำนวนเงิน</th><th>ช่องทางการชำระเงิน</th><th>วันที่ชำระเงิน</th><th>จำนวนเงิน</th><th>ช่องทางการชำระเงิน</th><th>วันที่ชำระเงิน</th><th>WHT จำนวน</th></tr></thead><tbody></tbody></table>');
			$('#divDisplayQueryResult').html(_TMPL_TBL_SEARCH);			
						
			_objDataTable = $('#tblSearchResult').dataTable(
				{
					"bJQueryUI": true,
					"sPaginationType": "full_numbers",
					"aaData": arrData,
					"aaSorting":[],
					"sScrollY": "85%",
					"sScrollX": "100%",
					"sScrollXInner": "400%",
					"aLengthMenu": [[10, 20, 35, 50, -1], [10, 20, 35, 50, "all"]],
					"iDisplayLength": 10,
					"bStateSave": true,
					"fnStateLoadParams": function (oSettings, oaData) {
						if (_blnChangeSearchCriteria) { //Destroy state saving if requery
							_blnChangeSearchCriteria = false;
							return false;
						}
					},
					"aoColumns": _aoColumns,
					"sDom": "<'row-fluid'<'span6'T><'span6'lf>r>t<'row-fluid'<'span6'i><'span6'p>><'clear'><'span6'T>",
					"oTableTools": {
						"aButtons": (_tableToolButtons != undefined)?_tableToolButtons:[],
						"sSwfPath": "public/js/jquery/dataTable/TableTools/2.1.5/swf/copy_csv_xls_pdf.swf"
					},
					"bScrollCollapse": true,
					"fnInitComplete": function(oSettings, json) {
						oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();	
						_objDataTableFixedColumns = new $.fn.dataTable.FixedColumns( this, {
							"iLeftColumns": 4
						});
						setTimeout(_doResize, 1000);
					},
					"fnDrawCallback": function () { //fnInfoCallback
						setTimeout(_doSetDataTablePagePlugins, 200);
					}
				}
			);
			_doUpdateSummaryValue(arrData);			
		};
	});

	if (_ALLOW_EDIT === false) {
		_doSetEditableColumns = function() {
			//do nothing;
		};
	} else {
		_doSetEditableColumns = function() {
			$('#tblSearchResult tbody tr td.payment_status_id').editable(
				function (value, settings) { 
					if (_prepareChangedData('payment_status_id', value, this)) return (settings.data[value]);
					return this.revert;
				}, { type: 'select', data: {'0':'', '1':'รอสรุปผล', '2':'เครดิต 30 วัน', '3':'ตรวจสอบแล้ว'}, onblur: 'submit', tooltip: 'Click to edit...' }
			);

			$('#tblSearchResult tbody tr td.account_remark').editable(
				function (value, settings) { 
					if (_prepareChangedData('account_remark', value, this)) return value;
					return this.revert;
				}, { type: 'text', onblur: 'submit', tooltip: 'Click to edit...' }
			);
		};
	}
	
	function _prepareChangedData(data_field, value, objSource) {
		var _tr = $(objSource).parents('tr')[0];
		var _datatable = $(objSource).parents('.cls-tbl-list')[0];
		$(objSource).removeClass('data-edit-changed');
		if ((_tr) && (_datatable)) {
			var _aData = $(_datatable).dataTable().fnGetData(_tr);
			if (('order_rowid' in _aData) && ('order_type_id' in _aData) && (data_field) && (value)) {
				var _value = value;
				var _order_rowid = _aData['order_rowid'].toString();
				var _type_id = _aData['order_type_id'].toString();
				var _str_index = _type_id + '-' + _order_rowid;
				var _rowid = _aData['order_status_rowid'] || -1;
				var _net_amount = _aData['total_price_sum_net'] || '';
				var _vat = _aData['total_price_sum_vat'] || '';
				var _total_amount = _aData['total_price_sum'] || '';
				_net_amount = _net_amount.replace(/\,/g, '');
				_vat = _vat.replace(/\,/g, '');
				_total_amount = _total_amount.replace(/\,/g, '');
				
				//Clear this property in case edit and clear value
				if (_str_index in _objChangedData) {
					if (data_field in _objChangedData[_str_index]) delete _objChangedData[_str_index][data_field];
				}
				switch (data_field) {
					case 'payment_status_id':
						if ((_aData[data_field] || 0) == value) {
							_fnCheckDataChanged(); //run to check and reset state if no others data changed
							return false;
						}
						break;
				}
				if (!(_str_index in _objChangedData)) _objChangedData[_str_index] = {"rowid":_rowid, "order_type_id": _type_id, "order_rowid": _order_rowid, "net_amount": _net_amount, "vat": _vat, "total_amount": _total_amount};
				_objChangedData[_str_index][data_field] = _value;
				
				$(objSource).addClass('data-edit-changed');
				$('.DTTT_button_commit_page').removeClass('DTTT_button_disabled');
				return true;
			}
		}
		return false;
	}
	
	function _doCommitPage() {
//console.log('COMMIT');
		var _str = JSON.stringify(_objChangedData);
//console.log(_str);
		if (( ! _objChangedData) || (_str == '{}')) {
			alert(MSG_ALERT_COMMIT_NO_CHANGE);
			return false;
		}

		$("#dialog-modal").html("<p>" + MSG_DLG_HTML_COMMIT + "</p>");
		$("#dialog-modal").dialog('option', 'title', MSG_DLG_TITLE_COMMIT);
		$("#dialog-modal").dialog( "open" );
		$.ajax({
			type:"POST",
			url:"./report_account/commit",
			contentType: "application/json;charset=utf-8",
			dataType:"json",
			data: _str,
			success: function(data, textStatus, jqXHR) {
				if (data.success == false) {
					alert(MSG_ALERT_COMMIT_FAILED.replace(/v_XX_1/g, data.error));
					$("#dialog-modal").dialog( "close" );
				} else {
					doSearch(false, false);
					alert(MSG_ALERT_COMMIT_SUCCESS.replace(/v_XX_1/g, ''));
					$("#dialog-modal").dialog( "close" );
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				doDisplayInfo(textStatus + ' : ' + errorThrown, "ErrorMessage");
				$("#dialog-modal").dialog( "close" );
			},
			statusCode: {
				404: function() {
					doDisplayInfo("Page not found", "ErrorMessage");
					$("#dialog-modal").dialog( "close" );
				}
			}
		});
	}
