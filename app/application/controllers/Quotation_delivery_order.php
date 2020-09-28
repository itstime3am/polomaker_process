<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quotation_delivery_order extends MY_Ctrl_crud { //MY_Controller
	function __construct() {
		parent::__construct();
		$this->modelName = 'Mdl_quotation_detail';
		$this->_quotation_rowid = -1;
	}

	public function index($quotation_rowid = -1) {
		if ($quotation_rowid <= 0) die('Invalid parameter "quotation_rowid" => ' . $quotation_rowid);

		$this->_quotation_rowid = (int) $quotation_rowid;
		$this->session->set_userdata('quotation_rowid', $this->_quotation_rowid);
		
		$this->add_css(array(
			'public/css/jquery/ui/1.11.4/cupertino/jquery-ui.min.css',
			'public/css/jquery/dataTable/1.10.11/dataTables.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/buttons-1.1.2/buttons.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/colreorder-1.3.1/colReorder.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/fixedcolumns-3.2.1/fixedColumns.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/fixedheader-3.1.1/fixedHeader.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/responsive-2.0.2/responsive.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/scroller-1.4.1/scroller.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/select-1.1.2/select.jqueryui.min.css',
			'public/css/jquery/fileupload/fileupload.css',
			'public/css/_public/_list.css',
			'public/css/_public/_template_main.css'
			,'public/css/order/form.css'
			,'public/css/order/_detail_premade.css'
			,'public/css/quotation/form.css'
			,'public/css/quotation_delivery_order/form.css'
		));
		$this->add_js(array(
			'public/js/jquery/1.11.0/jquery.js',
			'public/js/jquery/ui/1.10.4/jquery-ui.min.js',
			'public/js/jquery/ui/1.10.3/jquery-ui-autocomplete-combobox.js',
			'public/js/jquery/dataTable/1.10.11/jquery.dataTables.min.js',
			'public/js/jquery/dataTable/1.10.11/dataTables.jqueryui.min.js',			
			'public/js/jquery/dataTable/extensions/jszip-2.5.0/jszip.min.js',
			'public/js/jquery/dataTable/extensions/pdfmake-0.1.18/pdfmake.min.js',
			'public/js/jquery/dataTable/extensions/pdfmake-0.1.18/vfs_fonts.js',
			'public/js/jquery/dataTable/extensions/buttons-1.1.2/dataTables.buttons.min.js',
			'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.jqueryui.min.js',
			'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.colVis.min.js',
			//'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.flash.min.js',
			'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.html5.min.js',
			'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.print.min.js',
			'public/js/jquery/dataTable/extensions/colreorder-1.3.1/dataTables.colReorder.min.js',
			'public/js/jquery/dataTable/extensions/fixedcolumns-3.2.1/dataTables.fixedColumns.min.js',
			'public/js/jquery/dataTable/extensions/fixedheader-3.1.1/dataTables.fixedHeader.min.js',
			'public/js/jquery/dataTable/extensions/responsive-2.0.2/dataTables.responsive.min.js',
			'public/js/jquery/dataTable/extensions/responsive-2.0.2/responsive.jqueryui.min.js',
			'public/js/jquery/dataTable/extensions/scroller-1.4.1/dataTables.scroller.min.js',
			'public/js/jquery/dataTable/extensions/select-1.1.2/dataTables.select.min.js',
			'public/js/jquery/dataTable/extensions/type-detection/moment_2.8.4.min.js',
			'public/js/jquery/dataTable/extensions/type-detection/datetime-moment.js',
			'public/js/jquery/dataTable/extensions/type-detection/numeric-comma.js',
			'public/js/jquery/fileupload/load-image.min.js',
			'public/js/jquery/fileupload/canvas-to-blob.min.js',
			'public/js/jquery/fileupload/jquery.iframe-transport.js',
			'public/js/jquery/fileupload/jquery.fileupload.js',
			'public/js/jquery/fileupload/jquery.fileupload-process.js',
			'public/js/jquery/fileupload/jquery.fileupload-image.js',
			'public/js/jquery/fileupload/jquery.form.js',
			'public/js/_public/_list.js',
			'public/js/_public/_search_panel.js',
			'public/js/_public/_fmg_controller.js'
			,array(<<<SCRPT
		$.fn.dataTable.moment( 'YYYY/MM/DD', moment.locale('en') );
		$.fn.dataTable.moment( 'DD/MM/YYYY', moment.locale('en') );
		$.fn.dataTable.moment( 'DD MM YYYY', moment.locale('en') );

SCRPT
				, 'custom')
		));
		
		$this->load->model('Mdl_quotation', 'm');
		$_data = $this->m->get_detail_report($quotation_rowid);
		
		$_layout = array(
				array('qo_number', 'customer_rowid', 'branch_jug_id')
				, array('start_date', 'day_limit', 'payment_condition') //'return <div class="table-title frm-edit-row-title">วันที่สิ้นสุด</div><div class="table-value frm-edit-row-value"><span id="spn-disp_end_date" class="user-input"></span></div>'
				, array('', '', 'return <div class="div-sum-title">ราคารวมสุทธิ</div><div class="div-sum-value"><span id="spn-disp_sum_net" class="user-input input-double spn-sum-value">'.$_data['disp_sum_net'].'</span>บาท</div>')
				, array('', 'percent_discount', 'return <div class="div-sum-title">ส่วนลด</div><div class="div-sum-value"><span id="spn-disp_sum_discount" class="user-input input-double spn-sum-value">'.$_data['disp_sum_discount'].'</span>บาท</div>')
				, array('', 'is_vat', 'return <div class="div-sum-title">VAT</div><div class="div-sum-value"><span id="spn-disp_sum_vat" class="user-input spn-sum-value">'.$_data['disp_sum_vat'].'</span>บาท</div>')
				//, array('', 'return ' . $_strSelVAT, 'return <div class="div-sum-title">VAT</div><div class="div-sum-value"><span id="spn-disp_sum_vat" class="user-input input-double spn-sum-value"></span>บาท</div>')
				, array('', '', 'return <div class="div-sum-title">รวม</div><div class="div-sum-value"><span id="spn-disp_sum_amount" class="user-input spn-sum-value">'.$_data['disp_sum_amount'].'</span>บาท</div>')
				, array('remark')
				, array('')
			);
		$_arrCtrl = array();
		$_arrCtrl["qo_number"] = array("label"=>"เลขที่", "name"=>"qo_number", "type"=>"spn", "class"=>"set-disabled data-constant", "value"=>$_data['qo_number']);
		$_arrCtrl["customer_rowid"] = array("label"=>"ลูกค้า", "name"=>"customer_rowid", "type"=>"spn", "class"=>"set-disabled data-constant", "value"=>$_data['customer_name']);
		$_arrCtrl["branch_jug_id"] = array("label"=>"สาขา", "name"=>"branch_jug_id", "type"=>"spn", "class"=>"set-disabled data-constant", "value"=>$_data['branch']);
		$_arrCtrl["start_date"] = array("label"=>"วันที่เริ่มต้น", "name"=>"start_date", "type"=>"spn", "class"=>"set-disabled data-constant", "value"=>$_data['disp_start_date']);
		$_arrCtrl["day_limit"] = array("label"=>"กำหนดยื่นราคา", "name"=>"day_limit", "type"=>"spn", "class"=>"set-disabled data-constant", "value"=>$_data['disp_day_limit']);
		$_arrCtrl["payment_condition"] = array("label"=>"เงื่อนไขการชำระเงิน", "name"=>"payment_condition", "type"=>"spn", "class"=>"set-disabled data-constant", "value"=>$_data['payment_condition']);
		//$_arrCtrl["is_disp_notice"] = array("label"=>"แสดง", "name"=>"is_disp_notice", "type"=>"txt", "class"=>"set-disabled data-constant", "value"=>$_data['is_disp_notice']);
		$_arrCtrl["percent_discount"] = array("label"=>"ส่วนลด ( % )", "name"=>"percent_discount", "type"=>"spn", "class"=>"set-disabled data-constant", "value"=>$_data['disp_percent_discount']);
		$_arrCtrl["is_vat"] = array("label"=>"VAT", "name"=>"is_vat", "type"=>"spn", "class"=>"set-disabled data-constant", "value"=>$_data['disp_is_vat']);
		$_arrCtrl["remark"] = array("label"=>"บันทึกเพิ่มเติม", "name"=>"remark", "type"=>"div", "class"=>"set-disabled data-constant", "value"=>$_data['remark']);
		
		$this->load->model('mdl_master_table', 'mt');
		$_listDetailTitle = $this->mt->list_all('quotation_detail_title', 'sort_index');
		$this->_selOptions["deposit_route"] = $this->mt->list_where('order_payment_route', 'is_cancel=0 AND is_deposit=1', 'sort_index', 'm_');
		$this->_selOptions["close_route"] = $this->mt->list_where('order_payment_route', 'is_cancel=0 AND is_close=1', 'sort_index', 'm_');
		$this->load->helper('order_detail_helper');
		$_frmView = $this->load->view('quotation/detail', array(
				'index' => 1
				, 'crud_controller' => 'quotation_detail'
				, 'listDetailTitle' => $_listDetailTitle
				/*, 'polo_panel' => hlpr_get_OrderPolo_ViewParams()
				, 'tshirt_panel' => hlpr_get_OrderTshirt_ViewParams()
				, 'cap_panel' => hlpr_get_OrderCap_ViewParams()
				, 'jacket_panel' => hlpr_get_OrderJacket_ViewParams()
				, 'premade_polo_panel' => hlpr_get_OrderPremadePolo_ViewParams()
				, 'premade_tshirt_panel' => hlpr_get_OrderPremadeTshirt_ViewParams()
				, 'premade_cap_panel' => hlpr_get_OrderPremadeCap_ViewParams()
				, 'premade_jacket_panel' => hlpr_get_OrderPremadeJacket_ViewParams()*/
			), TRUE);

		//Get Default auto prepare controls (followed by model)
		$this->_prepareControlsDefault();
		$this->_setController("disp_title", "เลขที่ใบนำส่ง/เลขใบสั่งผลิต", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>1));
		$this->_setController("description", "คำอธิบาย", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"left","order"=>2));
		$this->_setController("disp_status", "สถานะ", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>3));
		$this->_setController("original_qty", "จำนวนตั้งต้น", NULL, array("selectable"=>TRUE,"default"=>FALSE,"class"=>"right","order"=>6));
		//$this->_setController("left_qty", "จำนวนคงเหลือ", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"right","order"=>7));
		$this->_setController("order_type", "", NULL);
		$this->_setController("type_id", "", NULL);
		$this->_setController("order_rowid", "", NULL);
		$this->_setController("order_detail_rowid", "", NULL);
		$this->_setController("deliver_rowid", "", NULL);
		$this->_setController("left_qty", "", NULL);
		$this->_setController("original_qty", "", NULL);
		$this->_setController("total_deliver_qty", "", NULL);
		$this->_setController("deliverable", "", NULL);
		$this->_setController("disp_status", "", NULL);
		$this->_setController("is_vat", "", NULL);
		$this->_setController("is_tax_inv_req", "", NULL);
		$this->_setController("customer_rowid", "", NULL);
		$this->_setController("customer", "", NULL);
		$this->_setController("company", "", NULL);
		$this->_setController("arr_full_address", "", NULL);
		$this->_setController("total_price_each", "", NULL);
		$this->_setController("avg_deposit_payment", "", NULL);
		$this->_setController("avg_close_payment", "", NULL);
		$this->_setController("disp_deposit_date", "", NULL);
		$this->_setController("disp_close_date", "", NULL);
		$this->_setController("str_deposit_date", "", NULL);
		$this->_setController("str_close_date", "", NULL);
		$this->_setController("deposit_route", "", NULL);
		$this->_setController("close_route", "", NULL);
		
		$_detailHTML = $this->load->view('_public/_list', array(
			'index' => 1
			,'list_viewable' => FALSE
			,'list_insertable' => FALSE
			,'list_editable' => FALSE
			,'list_deleteable' => FALSE
			,'dataview_fields' => $this->_arrDataViewFields
			,'custom_columns' => array(
				array(
					"column" => <<<CCLMS
{"sTitle": "จำนวนส่ง", "sClass":"center", "mData": 'client_temp_id', "mRender": function(data,type,full) { return fnc__DDT_Row_RenderDeliverDetailQtyControl(data, type, full); }, "bSortable": false}
CCLMS
					, "order" => 8
				)
			)
			//,'edit_template' => $_frmView
		), TRUE);
/*
{ "sTitle": "เลือก", "sClass":"center select-checkbox", "bSortable": false }		
*/
			$_mainHTML = $this->load->view(
			'_public/_form',
			array(
				'index' => 1
				, 'controls' => $_arrCtrl
				, 'layout' => $_layout
				, '_IS_NO_BUTTONS' => TRUE
				, 'sublist' => $_detailHTML
			), TRUE);
		$_frmDeliver = $this->add_view(
				'delivery/form', 
				array(
					'index' => 2
					, 'arrDepositRoute' => $this->_selOptions["deposit_route"]
					, 'arrCloseRoute' => $this->_selOptions["close_route"]
				),
				TRUE
			);
		$_mainHTML .= <<<INSORD
	<br>
	<div id="divPrepareInsertDeliver" class="cls-div-form-edit-dialog" style="display:none;">
		{$_frmDeliver}
	</div>
INSORD;
		$pass['contents'] = $_mainHTML;
		$pass['title'] = "ใบเสนอราคา:: ใบนำส่งสินค้า";
		
		$this->add_js('public/js/quotation_delivery_order/form.js');
		$this->add_js('public/js/quotation/detail.js');
		//$this->add_js("_DT_BASE_OPTIONS['select'] = { style: 'multi' };", 'custom');

		return $this->add_view_with_script_header('_public/_template_single', $pass);
	}
	
	public function json_search() {
		//$this->_serviceCheckRight('view');
		if ($this->_quotation_rowid <= 0) $this->_quotation_rowid = $this->session->userdata('quotation_rowid');
		if ($this->_quotation_rowid <= 0) die('Invalid parameter "quotation_rowid" => ' . $quotation_rowid);
		
		$blnSuccess = FALSE;
		$strError = 'Unknown Error';
		$this->load->model('Mdl_quotation_detail', 'm');
		$arrResult = $this->m->search_for_deliver(array("quotation_rowid"=>$this->_quotation_rowid));
		$strError = $this->m->error_message;
		if ($strError == '') {
			$blnSuccess = TRUE;
			if (!is_array($arrResult)) {
				$arrResult = array();
			}
		}
		$json = json_encode(
			array(
				'success' => $blnSuccess,
				'error' => $strError,
				'data' => $arrResult
			)
		);
		header('content-type: application/json; charset=utf-8');
		echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$json.")":$json;
	}

	public function create_link_produce_order() {
		$_blnSuccess = FALSE;
		$_strError = "Unknown error";
		$_strMessage = "";
		
		$_arrData = $this->__getAjaxPostParams();
		if (isset($_arrData) && ($_arrData != FALSE)) {
			if (! (isset($_arrData['quotation_detail_rowid']) && ($_arrData['quotation_detail_rowid'] > 0))) {
				$_strError = 'Quotation detail rowid is required.';
			} else {
				try {
					$this->load->model($this->modelName, 'm');
					$_strMessage = $this->m->fncQuotationCreateLinkProduceOrder($_arrData);
					$_strError = $this->m->error_message;
				} catch (Exception $e) {
					$_strError = $e->getMessage();
				}
			}
		}
		if (empty($_strError)) {
			$_blnSuccess = TRUE;
		} else {
			$_blnSuccess = FALSE;
		}
		$json = json_encode(
			array(
				'success' => $_blnSuccess,
				'error' => $_strError,
				'message' => $_strMessage
			)
		);
		header('content-type: application/json; charset=utf-8');
		echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$json.")": $json;
	}
}