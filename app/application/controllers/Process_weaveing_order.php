<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Process_weaveing_order extends MY_Ctrl_crud {
	function __construct() {
		parent::__construct();
		$this->modelName = 'Mdl_weave_process';
	}

	public function index() {

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
			'public/css/jquery/ui/timepicker/1.6.1/jquery-ui-timepicker-addon.min.css',
			'public/css/jquery/fileupload/fileupload.css',
			//'public/css/order/form.css',
			//'public/css/order/_detail_premade.css',
			'public/css/quotation/form.css',
			array('a.DTTT_button_commit_page span { background: url(public/images/ok-grey.png) no-repeat bottom right;display: inline-block;height: 24px;line-height: 24px;padding-right: 30px; }', 'custom'),
			array('a.DTTT_button_commit_page:hover span { background: url(public/images/ok-green.png) no-repeat center right; }', 'custom')
		));

		$_allowEdit = "true";
		$_manu_type = "weave";
		$this->add_js(array(
			array("var _ALLOW_EDIT = " . $_allowEdit . ";", 'custom_init'),
			array("var _MANU_TYPE = '" . $_manu_type . "';", 'custom_init'),
			'public/js/jquery/1.11.0/jquery.js',
			'public/js/jquery/ui/1.10.4/jquery-ui.min.js',
			'public/js/jquery/ui/1.10.3/jquery-ui-autocomplete-combobox.js',
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
			'public/js/jquery/editable/1.7.1/jquery.editable.min.js',
			'public/js/jquery/ui/timepicker/1.6.1/jquery-ui-timepicker-addon.min.js',
			'public/js/jquery/ui/timepicker/1.6.1/jquery-ui-sliderAccess.js',
			'public/js/jquery/fileupload/load-image.min.js',
			'public/js/jquery/fileupload/canvas-to-blob.min.js',
			'public/js/jquery/fileupload/jquery.iframe-transport.js',
			'public/js/jquery/fileupload/jquery.fileupload.js',
			'public/js/jquery/fileupload/jquery.fileupload-process.js',
			'public/js/jquery/fileupload/jquery.fileupload-image.js',
			'public/js/jquery/fileupload/jquery.form.js',
			'public/js/_public/_fmg_controller.js', 
			'public/js/jsGlobal.js', 
			'public/js/jsUtilities.js', 
			'public/js/jsGlobalConstants.js'
			, array(<<<SCRPT
		$.fn.dataTable.moment( 'YYYY/MM/DD', moment.locale('en') );
		$.fn.dataTable.moment( 'DD/MM/YYYY', moment.locale('en') );
		$.fn.dataTable.moment( 'DD MM YYYY', moment.locale('en') );

SCRPT
			, 'custom')
		));
		
		$this->load->model('mdl_master_table', 'mt');
		$this->load->model($this->modelName, 'm');
		
		//Get Default auto prepare controls (followed by model)
		$this->_prepareControlsDefault();

		//++ set special attributes		
		$this->_setController("job_number", "เลขที่งาน", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>2));
		$this->_setController("start_ps_date", "วันที่เริ่มการผลิต", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>3));
		$this->_setController("customer", "ลูกค้า", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>4));
		$this->_setController("disp_order", "ประเภทสินค้า", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>9));
		$this->_setController("pattern", "แบบเสื้อ", NULL, array("selectable"=>TRUE,"default"=>TRUE,"order"=>10));
		$this->_setController("position", "ตำแหน่ง", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center position","order"=>11));
		$this->_setController("disp_weave_type", "ประเภทงาน", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"edit center weave_type","order"=>14));
		// $this->_setController("detail", "รายละเอียด", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>9));
		$this->_setController("width", "กว้าง", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"edit center width","width"=>"60","order"=>15));
		$this->_setController("height", "สูง", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"edit center height","width"=>"60","order"=>16));
		$this->_setController("fabric", "ชนิดผ้า", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>17));
		$this->_setController("qty", "จำนวน", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>18));
		$this->_setController("eg_date", "วันที่ส่งแบบ", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>19));
		$this->_setController("approve_date", "วันที่ Sale Approve", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>20));
		$this->_setController("fabric_date", "วันที่รับผ้า", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"center","order"=>21));
		$this->_setController("block_emp", "ช่างตีบล็อค", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"edit center block_emp","order"=>22));
		$this->_setController("block_number", "เลขที่บล็อค", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"edit center block_number","order"=>23));
		$this->_setController("stitch_number", "ฝีเข็ม", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"edit center stitch_number","order"=>24));
		$this->_setController("color_silk_qty", "จำนวนสีไหม", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"edit center color_silk_qty","order"=>25));
		$this->_setController("prod_cost", "ต้นทุน", NULL, array("selectable"=>TRUE,"default"=>TRUE,"class"=>"edit center default_number prod_cost","order"=>26));
		//-- set special attribute

		/*++ dummy field, use it value to show span on panel (just add to array keep value) */
		$this->_setController("order_s_rowid", "", NULL);
		$this->_setController("order_rowid", "", NULL);
		$this->_setController("seq", "", NULL);
		$this->_setController("rowid", "", NULL);
		$this->_setController("disp_status", "", NULL);
		$this->_setController("disp_fabric_status", "", NULL);
		$this->_setController("prod_id", "", NULL);
		$this->_setController("img", "", NULL);
		$this->_setController("eg_remark", "", NULL);
		$this->_setController("status_remark", "", NULL);
		$this->_setController("order_remark", "", NULL);
		$this->_setController("status_rowid", "", NULL);
		$this->_setController("arr_avail_status", "", NULL);
		$this->_setController("arr_avail_action", "", array());
		$this->_setController("arr_avail_fabric", "", array());
		/*-- dummy field, use it value to show span on panel (just add to array keep value) */

		$_custom_columns = array( 
			array(
				"column" => <<<CCLMS
{ "sTitle":"note","width":"180","sClass":"center order_remark","mData":"rowid","mRender":function(data,type,full) { return fnc__DDT_Row_RenderOrderReamerk(data, type, full); } , "bSortable": false}
CCLMS
				, "order" => 1),
			array(
				"column" => <<<CCLMS
{ "sTitle":"สถานะ","width":"100","sClass":"center","mData":'rowid',"mRender":function(data,type,full) { return fnc__DDT_Row_RenderStatus(data, type, full); }, "bSortable": true }
CCLMS
				, "order" => 5
			),
			array(
				"column" => <<<CCLMS
{ "sTitle":"แก้ไขสถานะ","width":"180","sClass":"center","mData":'rowid',"mRender":function(data,type,full) { return fnc__DDT_Row_RenderAvailStatus(data, type, full); }, "bSortable": false }
CCLMS
			, "order" => 6),
			array(
				"column" => <<<CCLMS
{ "sTitle":"สถานะผ้า","width":"100","sClass":"center","mData":'rowid',"mRender":function(data,type,full) { return fnc__DDT_Row_RenderFabricStatus(data, type, full); }, "bSortable": true }
CCLMS
				, "order" => 7
			),
			array(
				"column" => <<<CCLMS
{ "sTitle":"แก้ไขสถานะผ้า","width":"100","sClass":"center","mData":'rowid',"mRender":function(data,type,full) { return fnc__DDT_Row_RenderAvailFabricStatus(data, type, full); }, "bSortable": true }
CCLMS
				, "order" => 8
			),
			array(
				"column" => <<<CCLMS
{ "sTitle":"อัพโหลดรูป","width":"180","sClass":"center edit img","mData":"rowid","mRender":function(data,type,full) { return fnc__DDT_Row_RenderEdit(data, type, full); } , "bSortable": false}
CCLMS
				, "order" => 12)
			);

		$pass['left_panel'] = $this->add_view('_public/_search_panel', $this->_arrSearchParams(), TRUE);

		$this->load->helper('order_detail_helper');
		$template = array(
			'index' => 0,
			'list_viewable' => FALSE,
			'list_insertable' => $this->_blnCheckRight('insert', 'quotation'),
			'list_editable' => FALSE,
			'list_deleteable' => FALSE,
			'dataview_fields' => $this->_arrDataViewFields
			,'custom_columns' => $_custom_columns
			//, 'jqDataTable' => '1.10.11'
		);		
		$template['edit_template'] = $this->_getEditTemplate();
		$pass['work_panel'] = $this->add_view('_public/_list', $template, TRUE);
		$pass['work_panel'] .= <<<SCR
	<script language="javascript">
	if (_tableToolButtons) {
		_tableToolButtons.push({"text": "&nbsp;","className": "DTTT_button_space"});
		_tableToolButtons.push({
			"text": "บันทึกข้อมูล"
			, "className": "DTTT_button_commit_page DTTT_button_disabled"
			, "action": function () {__doCommitChangeMultiDataTable(_dataToUpdateColumn,'')}
		});
	}
	</script>
SCR;

		$pass['title'] = "งานปัก";
		
		$this->add_js('public/js/screening_weaveing_process/form.js');
		// $this->add_js('public/js/quotation/detail.js');
		//$this->add_js('public/js/quotation/payment.js');
				
		$qo_status = $this->mt->list_where('manu_weave_status', 'is_cancel=0', NULL, 'm_');
		$this->add_js("var _ARR_QO_STATUS = " . json_encode($qo_status) . ";", 'custom');

		$qo_status = $this->mt->list_where('manu_weave_type', 'is_cancel=0', NULL, 'm_');
		$this->add_js("var _ARR_SCREEN_TYPE = " . json_encode($qo_status) . ";", 'custom');

		$qo_status = $this->mt->list_where('fabric_status', 'is_cancel=0', NULL, 'm_');
		$this->add_js("var _ARR_FABRIC_STATUS = " . json_encode($qo_status) . ";", 'custom');

		$this->_DISABLE_ON_LOAD_SEARCH = True;
		$this->add_view_with_script_header('_public/_template_main', $pass);
	}

	function _getEditTemplate() {
		return <<<TMP
	<div id="div_edit_dialog">
		<span class="cls-label" style="font-weight:bold;"></span>
		<div class="edit-file-wrapper" style="display:flex;justify-content: center;">
			<textarea id="txa-edit_column" style="width:96%;display:none" class="user-input" rows="3" placeholder=""></textarea>
			<select id="sel-edit_column" style="width:50%;display:none" class="user-input" rows="3" placeholder=""></select>
		</div>
		
		<div class="file-upload-wrapper" style="display:none;justify-content:center;">
        <div class="frm-edit-row" style="display:flex;align-items:center;margin-top:20px;flex-direction:column;">
            <div role="img" class="display-upload disp-upload-main" id="div_disp_upload_view"></div><br>
			<!-- <span style="text-align: center;margin-bottom: 5px;">123</span> -->
			<textarea id="txa-eg_remark" style="width:50%;display:none;" class="user-input" rows="3" placeholder="หมายเหตุ"></textarea>
			<button class="btn-input-file-upload" style="width: 20%;display:none; margin-top: 5px;">Upload File</button>
			<a id="btn-download-img" style="display:none; margin-top: 5px;"><button>Download</button></a>
            <form action="upload_temp_image.php" id="frm-upload-file"  method="post" enctype="multipart/form-data">
			<input type="file" name="image" class="input-file-upload" style="display:none; margin-top: 10px;">
			<input type="text" name="file_name" class="input-text" style="display:none; margin-top: 10px;">
			<button type="submit" id="btn-submit" style="display:none;"></button>
            </form>
        </div>
    	</div>
	</div>
TMP;

	}

	function commit() {
		$_blnSuccess = FALSE;
		$_strError = '';
		$_strMessage = '';
		$json_input_data = json_decode(trim(file_get_contents('php://input')), true); //get json
		$_arr = (isset($json_input_data))?$json_input_data:$this->input->post(); //or post data submit
		if (isset($_arr) && ($_arr != FALSE)) {
			$this->load->model($this->modelName, 'm');
			
			if (empty($_arr['sale_rowid'])) $_arr['sale_rowid'] = $this->session->userdata('user_id');
			if (array_key_exists('start_date', $_arr) && (! empty($_arr['start_date']))) {
				$_datValue = $this->m->_datFromPost($_arr['start_date']);
				if ($_datValue instanceof DateTime) $_arr['start_date'] = $_datValue;
			}
			//$this->db->trans_begin();
			
			$_aff_rows = $this->m->commit($_arr);
			$_strError = $this->m->error_message;
			$_rowid = 0;
			if (array_key_exists('rowid', $_arr) && (trim($_arr['rowid']) > '0')) {
				$_rowid = $_arr['rowid'];
			} else {
				$_rowid = $this->m->last_insert_id;
				// ++ update revision to 1, start trigger job for revision runnig by editting
				if ($_strError == '') {
					$this->db->reset_query();

					$this->db->set('revision', 1);
					$this->db->set('update_by', (int) $this->session->userdata('user_id'));
					$this->db->set('update_date', 'now()', FALSE);
					$this->db->where('rowid', $_rowid);
					$this->db->update('pm_t_quotation');
					$_strError .= $this->db->error()['message'];
				}
			}
			if ($_rowid <= 0) $_strError .= 'Invalid rowid';

			if ($_strError == "") {
				$_blnSuccess = TRUE;
				$_strMessage = $_aff_rows;
				//$this->db->trans_complete();			
			}
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

	function _arrSearchParams() {
		$_to = new DateTime();
		$_frm = date_sub(new DateTime(), new DateInterval('P3D'));
		return array(
			'controls' => array(
				array(
					"type" => "txt",
					"label" => $this->_getDisplayLabel('เลขที่งาน'),
					"name" => "job_number"
				),
				array(
					"type" => "dpk"
					,"label" => "จากวันที่"
					,"name" => "date_from"
					// ,"value" => $_frm->format('d/m/Y')
					,"value" => '18/11/2020'
				),
				array(
					"type" => "dpk"
					,"label" => "ถึงวันที่"
					,"name" => "date_to"
					//,"value" => $_to->format('d/m/Y')
				),
				array(
					"type" => "chk",
					"label" => "แสดงเฉพาะมีการตอบกลับจากSale",
					"name" => "is_reply_status",
					"value" => FALSE
				),
				array(
					"type" => "info",
					"value" => "&nbsp;"
				)
			),
			'search_onload' => TRUE
		);		
	}
	
	function get_pdf($quotation_rowid) {
		$this->load->model($this->modelName, 'm');
		$pass['data'] = $this->m->get_detail_report($quotation_rowid);
		if ($pass['data'] == FALSE) {
			echo "Error get report data: " . $this->m->error_message;
			return;
		} else {
			$_status_rowid = (isset($pass['data']['status_rowid'])) ? $pass['data']['status_rowid'] : 10;
			$html = '';
			mb_internal_encoding("UTF-8");
			$this->load->helper('exp_pdf_helper');
			
			$now = new DateTime();
			$strNow = $now->format('YmdHis');
			$file_name = 'quotation_' . $strNow . '.pdf';
			
			$this->load->library('mpdf8');
			$pass['title'] = 'ใบเสนอราคา';

			$html = $this->load->view('quotation/pdf/quotation', $pass, TRUE);
//echo $html;exit;
			if ($_status_rowid >= 40) {
				$this->mpdf8->exportMPDF_Template($html, 'quotation', $file_name);
			} else {
				$this->mpdf8->exportMPDF_Template_withWaterMark($html, 'quotation', $file_name);
			}
		}
	}

	function json_get_qonumber() {
		$_blnSuccess = FALSE;
		$_strError = '';
		$_strResult = '';
		$_arr = $this->__getAjaxPostParams();
		if (is_array($_arr)) {
			if (array_key_exists('start_date', $_arr) && (strlen($_arr['start_date']) == 10)) {
				$this->load->model($this->modelName, 'm');
				$_date = $this->m->_datFromPost($_arr['start_date']);
				$_str_date = '';
				if ($_date instanceof DateTime) $_str_date = $_date->format('Y/m/d');
				$_strResult = $this->m->getNextQONumber($_str_date);
				$_strError = $this->m->error_message;
				if ($_strError == '') {
					$_blnSuccess = TRUE;
				}

			}
		}
		$json = json_encode(
			array(
				'success' => $_blnSuccess,
				'error' => $_strError,
				'qo_number' => $_strResult
			)
		);
		header('content-type: application/json; charset=utf-8');
		echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$json.")":$json;
	}

	function update_data_by_id() {
		$blnSuccess = FALSE;
		$strError = '';
		$this->load->model($this->modelName, 'm');
		$json_input_data = json_decode(trim(file_get_contents('php://input')), true); //get json
		$_arrData = (isset($json_input_data))?$json_input_data:$this->input->post(); //or post data submit
		// echo count($_arrData);exit;
		// echo print_r($_arrData);exit;
		if (isset($_arrData) && ($_arrData != FALSE)) {
			$_remark = FALSE;
			// if (isset($_arrData['status_remark']) && (!(empty($_arrData['status_remark'])))) $_remark = $_arrData['status_remark'];
			if ($strError == '') {
				if($this->m->update_data_by_id($_arrData)){
					$strError = $this->m->error_message;
				}else{
					$strError = "refresh";
				}
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
	
	function change_status_by_id() {
		$blnSuccess = FALSE;
		$strError = '';
		$this->load->model($this->modelName, 'm');
		$json_input_data = json_decode(trim(file_get_contents('php://input')), true); //get json
		$_arrData = (isset($json_input_data))?$json_input_data:$this->input->post(); //or post data submit
		// print_r($_arrData);exit;
		if (isset($_arrData) && ($_arrData != FALSE)) {
			if (! isset($_arrData['rowid'])) $strError .= '"rowid" not found,';
			if (! isset($_arrData['status_rowid'])) $strError .= '"status_rowid" not found,';
			$_remark = FALSE;
			if (isset($_arrData['status_remark']) && (!(empty($_arrData['status_remark'])))) $_remark = $_arrData['status_remark'];
			if ($strError == '') {
				//create new
				if (isset($_arrData['order_rowid']) && isset($_arrData['order_s_rowid']) && isset($_arrData['seq'])){
					if($this->m->change_status_by_id($_arrData['rowid'], $_arrData['status_rowid'], $_remark, $_arrData['order_rowid'], $_arrData['order_s_rowid'], $_arrData['seq'], $_arrData['job_number'], $_arrData['timestamp'])){
						$strError = $this->m->error_message;
					}else{
						$strError = "refresh";
					}
				}else{
				//update by id
					if($this->m->change_status_by_id($_arrData['rowid'], $_arrData['status_rowid'],  $_remark, '', '', '','',$_arrData['timestamp'])){
						$strError = $this->m->error_message;
					}else{
						$strError = "refresh";
					}
				}
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

	function change_status_fabric_by_id() {
		$blnSuccess = FALSE;
		$strError = '';
		$this->load->model($this->modelName, 'm');
		$json_input_data = json_decode(trim(file_get_contents('php://input')), true); //get json
		$_arrData = (isset($json_input_data))?$json_input_data:$this->input->post(); //or post data submit
		// print_r($_arrData);exit;
		if (isset($_arrData) && ($_arrData != FALSE)) {
			if (! isset($_arrData['rowid'])) $strError .= '"rowid" not found,';
			if (! isset($_arrData['status_rowid'])) $strError .= '"status_rowid" not found,';
			$_remark = FALSE;
			if (isset($_arrData['status_remark']) && (!(empty($_arrData['status_remark'])))) $_remark = $_arrData['status_remark'];
			if ($strError == '') {
				//create new
				if (isset($_arrData['rowid']) && isset($_arrData['status_rowid'])){
					if($this->m->change_status_fabric_by_id($_arrData['rowid'], $_arrData['status_rowid'], $_arrData['timestamp'])){
						$strError = $this->m->error_message;
					}else{
						$strError = "refresh";
					}
				}
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

	function change_status_by_text() {
		$blnSuccess = FALSE;
		$strError = 'Unknown Error';
		$this->load->model($this->modelName, 'm');
		$json_input_data = json_decode(trim(file_get_contents('php://input')), true); //get json
		$_arrData = (isset($json_input_data))?$json_input_data:$this->input->post(); //or post data submit
		if (isset($_arrData) && ($_arrData != FALSE)) {
			$arrResult = $this->m->change_status_by_code($_arrData);
			$strError = $this->m->error_message;
		} else {
			$strError = 'Invalid parameters passed ( None )';
		}
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
	
	function insert_delivery_order() {
		$blnSuccess = FALSE;
		$strError = 'Unknown Error';
		$this->load->model($this->modelName, 'm');
		$json_input_data = json_decode(trim(file_get_contents('php://input')), true); //get json
		$_arrData = (isset($json_input_data))?$json_input_data:$this->input->post(); //or post data submit
		if (isset($_arrData) && ($_arrData != FALSE)) {
			$arrResult = $this->m->insert_delivery_order($_arrData);
			$strError = $this->m->error_message;
		} else {
			$strError = 'Invalid parameters passed ( None )';
		}
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
}