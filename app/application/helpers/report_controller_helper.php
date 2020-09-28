<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
function hlpr_arrListClosePaymentRoute() {
	return array(
				"0"=> ""
				, "1"=>"เงินสด(หน้าร้าน)"
				, "2"=>"เงินสด(โบ้เบ้)"
				, "3"=>"Messenger"
				, "4"=>"KBANK"
				, "5"=>"KTB"
				, "14"=>"KBANK POLO"
				, "6"=>"SCB"
				, "7"=>"SCB POLO"
				, "8"=>"รอโอน"
				, "9"=>"CREDIT"
				, "10"=>"Cheque"
			);
}
function hlpr_arrListDepositRoute() {
	return array(
				"0"=>""
				, "1"=>"เงินสด(หน้าร้าน)"
				, "4"=>"KBank"
				, "14"=>"KBank POLO"
				, "5"=>"KTB"
				, "6"=>"SCB"
				, "7"=>"SCB POLO"
				, "8"=>"รอโอน"
				, "9"=>"CREDIT"
				, "10"=>"Cheque"
				, "11"=>"ชำระ 100% วันจัดส่ง"
				, "12"=>"บัตรเครดิต"
			);
}

// ++ Issues 20160210 ++ 
function hlpr_arrListClosePaymentRoute() {
	return array(
				"0"=> ""
				, "1"=>"เงินสด(หน้าร้าน)"
				, "21"=>"เงินสด(ฝ่ายจัดส่ง)"
				, "3"=>"Messenger"
				, "15"=>"KBANK สุนิสา"
				, "16"=>"KTB สุนิสา"
				, "17"=>"SCB สุนิสา"
				, "18"=>"SCB สุรศักดิ์"
				, "7"=>"SCB POLO"
				, "19"=>"SCB POLO PLUS"
				, "20"=>"SCB POLO TALAD"
				, "14"=>"KBANK POLO"
				, "10"=>"Cheque"
				, "9"=>"CREDIT"
				, "8"=>"รอโอน"
				, "12"=>"บัตรเครดิต"
			);
}

function hlpr_arrListDepositRoute() {
	return array(
				"0"=>""
				, "1"=>"เงินสด(หน้าร้าน)"
				, "15"=>"KBANK สุนิสา"
				, "16"=>"KTB สุนิสา"
				, "17"=>"SCB สุนิสา"
				, "18"=>"SCB สุรศักดิ์"
				, "7"=>"SCB POLO"
				, "19"=>"SCB POLO PLUS"
				, "20"=>"SCB POLO TALAD"
				, "14"=>"KBANK POLO"
				, "10"=>"Cheque"
				, "9"=>"CREDIT"
				, "8"=>"รอโอน"
				, "12"=>"บัตรเครดิต"
				, "11"=>"ชำระ 100% วันส่งของ"
			);
}
/* -- Issues 20160210 -- */

function hlpr_htmlSummaryPanel($arrControls, $arrLayout) {
	if (! (is_array($arrControls) && is_array($arrLayout))) return FALSE;
	$_arr = array();

	foreach ($arrControls as $_id=>$_obj) {
		$_lbl = '';
		if (array_key_exists("form_edit", $_obj)) {
			$_lbl = (array_key_exists("label", $_obj["form_edit"]))?$_obj["form_edit"]["label"]:'';
		} else if (array_key_exists("list_item", $_obj)) {
			$_lbl = (array_key_exists("label", $_obj["list_item"]))?$_obj["list_item"]["label"]:'';
		}
		$_arr[$_id] = $_lbl;
	}
	$_return = <<<HTML

	<div id="div_summary_panel" class="cls-div-summary" >
		<table id="tbl_summary_panel" class="rounded-corner">
			<thead>
				<tr>
					<th class="rounded-top-left" style="height:24px;width:10%;"></th>
					<th>สรุปรวม</th>
					<th class="rounded-top-right" style="width:10%;"></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="2" class="rounded-foot-left"></td>
					<td class="rounded-foot-right">&nbsp;</td>
				</tr>
			</tfoot>
			<tbody>

HTML;
	foreach ($arrLayout as $_grp => $_item) {
		if (is_array($_item)) {
			$_row = _getSummaryRow($_grp, $_item, $_arr);
			$_return .= <<<ROW
				<tr>
					<td colspan="3">
$_row
					</td>
				</tr>

ROW;
		}
	}
	
	$_return .= <<<END

			</tbody>
		</table>
	</div>

END;
	return $_return;

}

function _getSummaryRow($groupKey, $items, $arrControls, $intSubLevel = 0) {
	$_return = '';
	if (is_array($items)) {
		$_row = "";
		$_width = 0;
		if (count($items) > 0) $_width = floor(100 / (count($items) * 5));
		foreach ($items as $_grp => $_item) {
			if ( ! is_array($_item)) {
				if ((strlen($_item) > 7) && (substr($_item, 0, 7) == 'return ')) {
					$_row .= substr($_item, 7);
				} else {
					$_row .= _getControlSet($_item, $arrControls, $_width);
				}
			} else {
				$_row .= _getSummaryRow($_grp, $_item, $arrControls, ($intSubLevel + 1));
			}
		}
		
		if (! is_numeric($groupKey)) {
			$_group_title = '';
			if (strlen($groupKey) > 0) {
				$_group_title = <<<GTL
						<span class="group-title">$groupKey</span>

GTL;
			}
			$_row = <<<GRP
					<div class="frm-edit-row-group">
$_group_title
$_row
					</div>

GRP;
		}
		$_return = <<<ROW
						<div class="frm-edit-row">
$_row
						</div>

ROW;
		
	}
	return $_return;	
}

function _getControlSet($item, $arrControls, $width_ratio = 20) {
	$_return = '';
	if (array_key_exists($item, $arrControls)) {
		$_title = $arrControls[$item];

		if ($_title != '') {
			$_twidth = ($width_ratio * 3); 
			$_vwidth = ($width_ratio * 2);
			$_return = <<<EACH
								<div class="frm-edit-row-title" style="width:$_twidth%" >$_title :</div>
								<div class="frm-edit-row-value" style="width:$_vwidth%"><span field="$item" class="cls-summary-value"></span></div>

EACH;

		} else {
			$_vwidth = ($width_ratio * 5);
			$_return = <<<EACH
								<div class="frm-edit-row-singleset" style="width:$_vwidth%"><span field="$item" class="cls-summary-value"></span></div>

EACH;
		}					
	} else {
		$_vwidth = ($width_ratio * 5);
		$_return = <<<EACH
								<div class="frm-edit-row-singleset" style="width:$_vwidth%"></div>

EACH;
	}
	return $_return;
}

function hlpr_arrSearchControlsParams($type = -1, $product_type_id = 0) {
	$CI =& get_instance();
	$CI->load->model('Mdl_customer', 'c');
/*
	$_arrCustomer = $CI->c->list_select();
	if (is_array($_arrCustomer)) {
		array_unshift($_arrCustomer, array('rowid'=>'', 'company'=>'', 'display_name'=>''));
	}
*/
	$_arrCompanySearch = $CI->c->list_select_company();
	if (is_array($_arrCompanySearch)) array_unshift($_arrCompanySearch, array('rowid'=>'', 'company'=>''));
	$CI->load->model('mdl_master_table', 'mt');
	$_arrFabric = $CI->mt->list_all('fabric', 'is_polo DESC');
	array_unshift($_arrFabric, array('rowid'=>'', 'name'=>''));
	$_arrJoomlaUsers = $CI->mt->list_joomla_users();
	array_unshift($_arrJoomlaUsers, array('id'=>'', 'name'=>''));
	$_arrDepPayRoute = $CI->mt->list_where('order_payment_route', "is_cancel = 0 AND is_deposit = 1", 'sort_index', 'm_');
	array_unshift($_arrDepPayRoute, array('rowid'=>'', 'name'=>''));
	$_arrClsPayRoute = $CI->mt->list_where('order_payment_route', "is_cancel = 0 AND is_close = 1", 'sort_index', 'm_');
	array_unshift($_arrClsPayRoute, array('rowid'=>'', 'name'=>''));
	
	$_to = new DateTime();
	$_frm = date_sub(new DateTime(), new DateInterval('P3D'));

	$_ctrlProductType = array(
			"type" => "sel",
			"label" => "ประเภท",
			"name" => "product_type_id",
			"sel_options" => array(
				array('id'=>'', 'name'=>''),
				array('id'=>1, 'name'=>'เสื้อโปโล'),
				array('id'=>2, 'name'=>'เสื้อยืด'),
				array('id'=>5, 'name'=>'หมวก'),
				array('id'=>6, 'name'=>'เสื้อแจ๊คเก็ต')
			),
			"sel_val" => "id",
			"sel_text" => "name"
		);
	
	if ((! empty($product_type_id)) && ($product_type_id > 0)) {
/*
		array_push($_arrControls, array(
				"type" => "hdn",
				"label" => "hidden",
				"name" => "product_type_id",
				"value" => $product_type_id
			));
*/
			$_ctrlProductType["class"] = "set-disabled";
			$_ctrlProductType["value"] = $product_type_id;
	}

	$_arrControls = array(
				array(
					"type" => "sel",
					"label" => "กลุ่มสินค้า",
					"name" => "category_id",
					"sel_options" => array(
						array('id'=>'', 'name'=>''),
						array('id'=>1, 'name'=>'สั่งตัด'),
						array('id'=>2, 'name'=>'สำเร็จรูป')
					),
					"sel_val" => "id",
					"sel_text" => "name"
				),
				$_ctrlProductType
				, array(
					"type" => "sel",
					"label" => "สถานะ",
					"name" => "order_status",
					"sel_options" => array(
						array('id'=>'', 'name'=>''),
						array('id'=>0, 'name'=>'None'),
						array('id'=>1, 'name'=>'WIP'),
						array('id'=>2, 'name'=>'รอส่ง'),
						array('id'=>3, 'name'=>'เครดิต'),
						array('id'=>4, 'name'=>'CLOSED')
					),
					"sel_val" => "id",
					"sel_text" => "name"
				),
/*
				array(
					"type" => "sel"
					, "label" => "ชนิดผ้า"
					, "name" => "fabric_rowid"
					, "sel_options" => $_arrFabric
				),
*/
				array(
					"type" => "txt",
					"label" => "เลขที่ใบงาน",
					"name" => "job_number"
				),
/* ++ Change to aac to reduce load
				array(
					"type" => "sel",
					"label" => "ลูกค้า",
					"name" => "customer_rowid",
					"sel_options" => $this->_selOptions['customer'],
					"sel_val" => "rowid",
					"sel_text" => "display_name"
				),
*/
				array(
					"type" => "aac"
					, "label" => "ลูกค้า"
					, "name" => "customer_rowid"
					, "url" => "./customer/json_search"
					, "min_length" => 2
					, "sel_val" => "rowid"
					, "sel_text" => "display_name_company"
					, "on_select" => <<<OSL
				var _aac_text = '';
				if (ui.item) {
					_aac_text = ui.item.value || '';
					_aac_text = _aac_text.toString().trim();
				}
				if (_aac_text != '') {
					var _match = /\[(.+)\]/.exec(_aac_text);
					if ((_match) && (_match.length > 0)) $('#sel-company', $(this).parents('form').get(0)).combobox('setValue', _match[1]);
				}

OSL
				),
				array(
					"type" => "sel"
					, "label" => "บริษัท"
					, "name" => "company"
					, "sel_options" => $_arrCompanySearch
					, "sel_val" => "company"
					, "sel_text" => "company"
					, "on_changed" => <<<OCH
function(str, event, ui) {
	var _str = str || '';
	_str = _str.toString().trim();
	if (_str != '') {
		$('#tblSearchPanel #aac-customer_rowid').autocomplete('search', _str);
	}
}

OCH
				),
				array(
					"type" => "sel",
					"label" => "เซลส์",
					"name" => "create_user_id",
					"sel_options" => $_arrJoomlaUsers,
					"sel_val" => "id",
					"sel_text" => "name"
				),
				array(
					"type" => "rdo"
					, "name" => "date_type"
					, "sel_options" => array(
							array("rowid"=>"1", "name"=>"วันที่เปิดใบงาน")
							, array("rowid"=>"2", "name"=>"วันที่ส่งจริง")
						)
					, "sel_val" => "rowid"
					, "sel_text" => "name"
					, "value" => 1
				),
				array(
					"type" => "dpk",
					"label" => "จากวันที่",
					"name" => "date_from",
					"value" => $_frm->format('d/m/Y')
				),
				array(
					"type" => "dpk",
					"label" => "ถึงวันที่",
					"name" => "date_to",
					"value" => $_to->format('d/m/Y')
				)
				/*,array(
					"type" => "info",
					"value" => "* จำกัดจำนวนแสดงผลไว้ที่ 100 เพื่อประสิทธิภาพในการทำงานของโปรแกรม"
				)*/
		);
	
	$_arrLayout = array(
			array('product_type_id')
			, array('order_status')
			, array('category_id')
			//, array('fabric_rowid')
			, array('job_number')
			, array('customer_rowid')
			, array('company')
			, array('create_user_id')
			, array("เงื่อนไขวันที่" => array(
				array('date_type')
				, array('date_from')
				, array('date_to')
			))
		);

	if ($type == 1) {
/*
		$_arrDummy = hlpr_arrListDepositRoute();
		$_arrList = array(array("rowid"=>"-1", "name"=>""));
		foreach ($_arrDummy as $_key=>$_val) {
			if ($_key == 0) $_val = "- ว่าง -";
			array_push($_arrList, array("rowid"=>$_key, "name"=>$_val));
		}
*/
		array_push($_arrControls, array(
					"type" => "sel",
					"label" => "มัดจำ",
					"name" => "deposit_route_id",
					"sel_options" => $_arrDepPayRoute
				));
		//array_push($_arrLayout, array('deposit_route_id'));
		$_arrLayout['ช่องทางชำระเงิน'] = array(array('deposit_route_id'));
	} else {
/*
		$_arrDummy = hlpr_arrListClosePaymentRoute();
		$_arrList = array(array("rowid"=>"-1", "name"=>""));
		foreach ($_arrDummy as $_key=>$_val) {
			if ($_key == 0) $_val = "- ว่าง -";
			array_push($_arrList, array("rowid"=>$_key, "name"=>$_val));
		}
*/
		array_push($_arrControls, array(
					"type" => "sel",
					"label" => "งวดสุดท้าย",
					"name" => "close_payment_route_id",
					"sel_options" => $_arrClsPayRoute
				));
		//array_push($_arrLayout, array('close_payment_route_id'));
		$_arrLayout['ช่องทางชำระเงิน'] = array(array('close_payment_route_id'));
	}
	return array(
			'controls' => $_arrControls
			, 'layout' => $_arrLayout
		);
}

/* End of file report_controller_helper.php */ 
/* Location: ./application/helpers/report_controller_helper.php */ 