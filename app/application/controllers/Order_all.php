<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_all extends MY_Ctrl_crud {
	function __construct() {
		parent::__construct();
		$this->modelName = 'Mdl_order_all';
		$this->_CUSTOMER_ROWID = -1;
		$this->_START_SCRIPT = '';
	}
	public function index() {
		$_strStartScript = '';
		if ($this->_START_SCRIPT != '') {
			$_strStartScript = $this->_START_SCRIPT;
		}
		$this->add_css(array(
			'public/css/jquery/ui/1.11.4/cupertino/jquery-ui.min.css',
			'public/css/jquery/dataTable/1.10.11/dataTables.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/buttons-1.1.2/buttons.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/fixedcolumns-3.2.1/fixedColumns.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/colreorder-1.3.1/colReorder.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/fixedheader-3.1.1/fixedHeader.jqueryui.min.css'/*,
			'public/css/jquery/dataTable/extensions/responsive-2.0.2/responsive.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/scroller-1.4.1/scroller.jqueryui.min.css',
			'public/css/jquery/dataTable/extensions/select-1.1.2/select.jqueryui.min.css',*/
		));
		$this->add_js(array(
			'public/js/jquery/1.11.0/jquery.js',
			'public/js/jquery/ui/1.10.4/jquery-ui.min.js',
			'public/js/jquery/dataTable/1.10.11/jquery.dataTables.min.js',
			'public/js/jquery/dataTable/1.10.11/dataTables.jqueryui.min.js',
			'public/js/jquery/dataTable/extensions/buttons-1.1.2/dataTables.buttons.min.js',
			'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.jqueryui.min.js',
			'public/js/jquery/dataTable/extensions/jszip-2.5.0/jszip.min.js',
			'public/js/jquery/dataTable/extensions/pdfmake-0.1.18/pdfmake.min.js',
			'public/js/jquery/dataTable/extensions/pdfmake-0.1.18/vfs_fonts.js',
			'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.html5.min.js',
			'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.print.min.js',
			'public/js/jquery/dataTable/extensions/buttons-1.1.2/buttons.colVis.min.js',
			'public/js/jquery/dataTable/extensions/fixedcolumns-3.2.1/dataTables.fixedColumns.min.js',
			'public/js/jquery/dataTable/extensions/fixedheader-3.1.1/dataTables.fixedHeader.min.js',
			'public/js/jquery/dataTable/extensions/colreorder-1.3.1/dataTables.colReorder.min.js',
			//'public/js/jquery/dataTable/extensions/responsive-2.0.2/dataTables.responsive.min.js',
			//'public/js/jquery/dataTable/extensions/responsive-2.0.2/responsive.jqueryui.min.js',
			//'public/js/jquery/dataTable/extensions/scroller-1.4.1/dataTables.scroller.min.js',
			//'public/js/jquery/dataTable/extensions/select-1.1.2/dataTables.select.min.js',
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
			'public/js/jquery/ui/1.10.3/jquery-ui-autocomplete-combobox.js',
			'public/js/jsUtilities.js',
			'public/js/jsGlobalConstants.js'
		));

		$_editFormParams['title'] = 'รายการสั่งผลิตสินค้า: ทุกประเภท';
		
		$this->_setController("rowid", "", array('type'=>'hdn'));
		$this->_setController("type_id", "", array('type'=>'hdn'));
		$this->_setController("order_rowid", "", array('type'=>'hdn'));
		$this->_setController("avail_process_status", "", array('type'=>'hdn'));
		
		//-- this one useualy been set in 	_prepareControlsDefault but here we remove those function to save unnecessary procedure
		$this->_setController("disp_order_type", "ประเภท", array(), array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>0));
		$this->_setController("job_number", "เลขที่งาน", array(), array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>1));
		$this->_setController("customer", "ลูกค้า", array(), array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>2));
		$this->_setController("disp_order_date", "วันที่สั่งงาน", array(), array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>3));
		$this->_setController("disp_due_date", "กำหนดส่ง", array(), array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>4));
		$this->_setController("disp_deliver_date", "วันที่ส่งลูกค้า", array(), array("selectable"=>TRUE,"default"=>FALSE,"class"=>"center","order"=>5));
		$this->_setController("disp_vat_type", "VAT", array(), array("selectable"=>TRUE,"default"=>FALSE,"class"=>"center","order"=>6));
		$this->_setController("total_price_sum", "ยอดรวม(บาท)", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"default_number","order"=>7));
		//-- set special attributes	
		
		$pass['left_panel'] = $this->__getLeftPanel();
		
		$_editFormParams['index'] = 2;
		
		$_custom_columns = array();
		/*
		$_custom_columns[] = array(
				"column" => '{"sTitle":"ขำระมัดจำ", "sClass":"cls-payment-dlg right","sWidth":"80px","mData":"rowid","mRender": function(data,type,full) { return \'<span class="cls-spn-payment">\' + formatNumber(full.total_deposit_payment) + \'</span><img class="tblButton" command="cmd_open_deposit_dialog" src="public/images/b_view.png" title="รายการชำระเงินมัดจำ" />\';}, "bSortable": true}'
				, "order" => 7
			);
		*/
		$this->_setController("total_left_amount", "คงเหลือ(บาท)", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"default_number","order"=>8));
		$this->_setController("process_status", "สถานะ", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>12));
		
		if ($this->_blnCheckRight('edit')) $_custom_columns[] = array(
				"column" => '{"sTitle":"แก้ไขสถานะ", "sClass": "center","mData":"rowid","mRender": function(data,type,full) { return fnc__DDT_Row_RenderOP(data, type, full); }, "bSortable": false}' 
				, "order" => 14
			);
		if ($this->_blnCheckRight('export_pdf')) $_custom_columns[] = array(
				"column" => '{"sTitle":"เอกสาร", "sClass": "center","mData":"rowid","mRender": function(data,type,full) { return fnc__DDT_Row_RenderPDF(data, type, full); }, "bSortable": false}'
				, "order" => 15
			);
		if ($this->_blnCheckRight('view')) $_custom_columns[] = array(
				"column" => '{"sTitle":"เรียกดู", "sClass": "center","mData":"rowid","mRender": function(data,type,full) { return fnc__DDT_Row_RenderView(data, type, full); } , "bSortable": false}' 
				, "order" => 16
			);
		if ($this->_blnCheckRight('edit')) $_custom_columns[] = array(
				"column" => '{"sTitle":"แก้ไข", "sClass": "center","mData":"rowid","mRender": function(data,type,full) { return fnc__DDT_Row_RenderEdit(data, type, full); } , "bSortable": false}' 
				, "order" => 17
			);
		
		$pass['work_panel'] = $this->add_view('_public/_list', 
			array(
				'custom_columns' => $_custom_columns,
				'dataview_fields' => $this->_arrDataViewFields,
				'list_viewable' => FALSE,
				'list_editable' => FALSE,
				'list_deleteable' => FALSE
			), TRUE
		);

		$this->add_js('public/js/order/order_all.js');
		if ($_strStartScript != '') $this->add_js($_strStartScript, 'custom');
		$pass['title'] = "ใบสั่งผลิตสินค้า";
		$pass["autosearch"] = FALSE;
		$this->_DISABLE_ON_LOAD_SEARCH = True;
		$this->add_view_with_script_header('_public/_template_main', $pass);
	}

	function __getLeftPanel() {
		//$_arrCompanySearch = $this->c->list_select_company();
		//if (is_array($_arrCompanySearch)) array_unshift($_arrCompanySearch, array('rowid'=>'', 'company'=>''));
		$_to = new DateTime();
		$_frm = date_sub(new DateTime(), new DateInterval('P3D'));
		
		return $this->add_view('_public/_search_panel', array(
			'controls' => array(
				array(
					"type" => "txt",
					"label" => "เลขที่ใบงาน",
					"name" => "job_number"
				),
				array(
					"type" => "aac"
					, "label" => "ลูกค้า"
					, "name" => "customer_rowid"
					, "url" => "./customer/json_search_acc"
					, "min_length" => 3
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
					if ((_match) && (_match.length > 1)) {
						setValue($('#txt-company', $(this).parents('form').get(0)), _match[1]);
						ui.item.value = _aac_text.substring(0, (_aac_text.length - _match[1].length - 3));
					}
				}
OSL
				),
				array(
					"type" => "txt"
					, "label" => "บริษัท"
					, "name" => "company"
				),
				array(
					"type" => "dpk",
					"label" => "จากวันที่",
					"name" => "date_from"
					//,"value" => $_frm->format('d/m/Y')
				),
				array(
					"type" => "dpk",
					"label" => "ถึงวันที่",
					"name" => "date_to",
					"value" => $_to->format('d/m/Y')
				),
				array(
					"type" => "chk",
					"label" => "แสดงเฉพาะ active",
					"name" => "is_active_status",
					"value" => TRUE
				),
				array(
					"type" => "info",
					"value" => "&nbsp;"
				),
				array(
					"type" => "info",
					"value" => "* จำกัดจำนวนแสดงผลไว้ที่ 3,000 เพื่อประสิทธิภาพในการทำงานของโปรแกรม"
				)
			),
			'layout' => array(),
			'search_onload' => (($this->_CUSTOMER_ROWID <= 0) && ($this->_START_SCRIPT == ''))
		), TRUE);
	}

	function get_pdf($pdf_index, $rowid) {
		$this->load->model($this->modelName, 'm');
		$pass['data'] = $this->m->get_detail_report($rowid);
		if ($pass['data'] == FALSE) {
			echo "Error get report data: " . $this->m->error_message;
			return;
		} else {
			$file_name = '';
			$html = '';
			
			mb_internal_encoding("UTF-8");
			$this->load->helper('exp_pdf_helper');
			$this->load->helper('upload_helper');

			if ($pass['data'] !== FALSE) {
				$now = new DateTime();
				$strNow = $now->format('YmdHis');
				switch ($pdf_index) {
					case "1":
						$file_name = 'F-P001_' . $strNow . '.pdf';
						$pass['title'] = 'ใบสั่งซื้อ เสื้อสั่งตัดโปโล';
						$pass['code'] = 'FM-SA-01-001 REV.00';
						$pass['is_show_price'] = TRUE;
						$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_order_detail', $pass, TRUE);
						$pass['others_price_panel'] = $this->load->view('order/pdf/section/_pdf_others_price', $pass, TRUE);
						$pass['size_quan_section'] = $this->load->view('order/pdf/section/_pdf_size_quan', $pass, TRUE);
						$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
						$pass['images_section'] = $this->load->view('order/pdf/section/_pdf_sample_images', $pass, TRUE);
						$html = $this->load->view('order/pdf/pdf_1', $pass, TRUE);
						break;
					case "2":
						$file_name = 'F-P002_' . $strNow . '.pdf';
						$pass['title'] = 'ใบสั่งตัด เสื้อโปโล';
						$pass['code'] = 'FM-SA-01-002 REV.00';
						$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_order_detail', $pass, TRUE);
						$pass['size_quan_section'] = $this->load->view('order/pdf/section/_pdf_size_quan', $pass, TRUE);
						$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
						$pass['images_section'] = $this->load->view('order/pdf/section/_pdf_sample_images', $pass, TRUE);
						$html = $this->load->view('order/pdf/pdf_2', $pass, TRUE);
						break;
					case "3":
						$file_name = 'F-P003_' . $strNow . '.pdf';
						$pass['title'] = 'ใบจ่ายงานเย็บ สั่งตัดเสื้อโปโล';
						$pass['code'] = 'FM-SA-01-003 REV.00';
						$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_order_detail', $pass, TRUE);
						$html = $this->load->view('order/pdf/pdf_3', $pass, TRUE);
						break;
					case "4":
						$file_name = 'F-P004_' . $strNow . '.pdf';
						$pass['title'] = 'ใบจ่ายงานเย็บแขน เสื้อโปโล';
						$pass['code'] = 'FM-SA-01-004 REV.00';
						$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_order_detail', $pass, TRUE);
						$html = $this->load->view('order/pdf/pdf_4', $pass, TRUE);
						break;
					case "5":
						$file_name = 'F-P005_' . $strNow . '.pdf';
						$pass['title'] = 'ใบจ่ายเย็บ เสื้อโปโล';
						$pass['code'] = 'FM-SA-01-005 REV.00';
						$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_order_detail', $pass, TRUE);
						$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
						$html = $this->load->view('order/pdf/pdf_5', $pass, TRUE);
						break;
					case "6":
						$file_name = 'F-P006_' . $strNow . '.pdf';
						$pass['title'] = 'ใบงานข้อมูล สั่งตัดเสื้อโปโล';
						$pass['code'] = 'FM-SA-01-006 REV.00';
						$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_order_detail', $pass, TRUE);
						$pass['size_quan_section'] = $this->load->view('order/pdf/section/_pdf_size_quan', $pass, TRUE);
						$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
						$pass['images_section'] = $this->load->view('order/pdf/section/_pdf_sample_images', $pass, TRUE);
						$html = $this->load->view('order/pdf/pdf_1', $pass, TRUE);
						break;
				}
//echo $html;exit;
				$this->load->library('pdf');
				$this->pdf->exportMPDF($html, $file_name);
			}
		}
	}

	function change_status_by_id() {
		$blnSuccess = FALSE;
		$strError = '';
		$this->load->model($this->modelName, 'm');
		$json_input_data = json_decode(trim(file_get_contents('php://input')), true); //get json
		$_arrData = (isset($json_input_data))?$json_input_data:$this->input->post(); //or post data submit
		if (isset($_arrData) && ($_arrData != FALSE)) {
			if (! isset($_arrData['rowid'])) $strError .= '"rowid" not found,';
			if (! isset($_arrData['ps_rowid'])) $strError .= '"ps_rowid" not found,';
			$_remark = FALSE;
			if (isset($_arrData['status_remark']) && (!(empty($_arrData['status_remark'])))) $_remark = $_arrData['status_remark'];
			if ($strError == '') {
				$this->m->change_status_by_id($_arrData['rowid'], $_arrData['ps_rowid'], $_remark);
				$strError = $this->m->error_message;
			}
		} else {
			$strError = 'Invalid parameters passed ( None )';
		}
		if ($strError == '') {
			$blnSuccess = TRUE;
		}
		$json = json_encode(
			array(
				'success' => $blnSuccess,
				'error' => $strError
			)
		);
		header('content-type: application/json; charset=utf-8');
		echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$json.")":$json;
	}

}
