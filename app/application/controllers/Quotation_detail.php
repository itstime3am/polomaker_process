<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quotation_detail extends MY_Ctrl_crud {
	function __construct() {
		parent::__construct();
		$this->modelName = 'Mdl_quotation_detail';
	}
	
	function view_draft_pdf($rowid) {
		$this->load->model($this->modelName, 'mt');
		$pass['data'] = $this->mt->fnc_get_order_detail_from_json($rowid);
		if ($pass['data'] == FALSE) {
			echo "Error get report data: " . $this->mt->error_message;
			return;
		} else {
			$file_name = '';
			$html = '';
			
			mb_internal_encoding("UTF-8");
			$this->load->helper('exp_pdf_helper');
			$this->load->helper('upload_helper');

			$now = new DateTime();
			$strNow = $now->format('YmdHis');
			$file_name = 'SMPL001_' . $strNow . '.pdf';
			
			$pass['code'] = 'FM-SA-01-001 REV.00';
			$pass['is_show_price'] = TRUE;
			$_type_id = (int) $pass['data']['type_id'];
			switch ($_type_id) {
				case 1:
					$pass['title'] = 'SAMPLE-ใบงานสั่งตัดเสื้อโปโล';
					$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_order_detail', $pass, TRUE);
					$pass['others_price_panel'] = $this->load->view('order/pdf/section/_pdf_others_price', $pass, TRUE);
					$pass['size_quan_section'] = $this->load->view('order/pdf/section/_pdf_size_quan', $pass, TRUE);
					$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
					$pass['images_section'] = $this->load->view('order/pdf/section/_pdf_sample_images', $pass, TRUE);
					$html = $this->load->view('order/pdf/pdf_1', $pass, TRUE);
					break;
				case 2:
					$pass['title'] = 'SAMPLE-ใบงานสั่งตัดเสื้อยืด';
					$pass['is_tshirt'] = TRUE;
					$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_order_detail', $pass, TRUE);
					$pass['others_price_panel'] = $this->load->view('order/pdf/section/_pdf_others_price', $pass, TRUE);
					$pass['size_quan_section'] = $this->load->view('order/pdf/section/_pdf_size_quan', $pass, TRUE);
					$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
					$pass['images_section'] = $this->load->view('order/pdf/section/_pdf_sample_images', $pass, TRUE);
					$html = $this->load->view('order/pdf/pdf_1', $pass, TRUE);
					break;
				case 3:
					$pass['title'] = 'SAMPLE-ใบงานเสื้อโปโลสำเร็จรูป';
					$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_premade_order_detail', $pass, TRUE);
					$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
					$pass['images_section'] = $this->load->view('order/pdf/section/_pdf_sample_images', $pass, TRUE);
					$html = $this->load->view('order/pdf/premade_pdf', $pass, TRUE);
					break;
				case 4:
					$pass['title'] = 'SAMPLE-ใบงานเสื้อยืดสำเร็จรูป';
					$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_premade_order_detail', $pass, TRUE);
					$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
					$pass['images_section'] = $this->load->view('order/pdf/section/_pdf_sample_images', $pass, TRUE);
					$html = $this->load->view('order/pdf/premade_pdf', $pass, TRUE);
					break;
/*				case 5:
					$pass['title'] = 'SAMPLE-ใบงานสั่งตัดหมวก';
					$pass['head_section'] = $this->load->view('order/pdf/section/_pdf_order_detail_cap', $pass, TRUE);
					$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
					$html = $this->load->view('order/pdf/pdf_cap', $pass, TRUE);
					break;
				case 6:
					$pass['title'] = 'SAMPLE-ใบงานสั่งตัดเสื้อแจ็คเก็ต';
					$pass['is_jacket'] = TRUE;
					$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_order_detail', $pass, TRUE);
					$pass['others_price_panel'] = $this->load->view('order/pdf/section/_pdf_others_price', $pass, TRUE);
					$pass['size_quan_section'] = $this->load->view('order/pdf/section/_pdf_size_quan', $pass, TRUE);
					$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
					$pass['images_section'] = $this->load->view('order/pdf/section/_pdf_sample_images', $pass, TRUE);
					$html = $this->load->view('order/pdf/pdf_1', $pass, TRUE);
					break;
*/
				case 7:
					$pass['title'] = 'SAMPLE-ใบงานหมวกสำเร็จรูป';
					$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_premade_order_detail_cap', $pass, TRUE);
					$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
					$pass['images_section'] = $this->load->view('order/pdf/section/_pdf_sample_images', $pass, TRUE);
					$html = $this->load->view('order/pdf/premade_pdf', $pass, TRUE);
					break;
				case 8:
					$pass['title'] = 'SAMPLE-ใบงานเสื้อแจ็คเก็ตสำเร็จรูป';
					$pass['is_jacket'] = TRUE;
					$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_premade_order_detail', $pass, TRUE);
					$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
					$pass['images_section'] = $this->load->view('order/pdf/section/_pdf_sample_images', $pass, TRUE);
					$html = $this->load->view('order/pdf/premade_pdf', $pass, TRUE);
					break;
				case 5:
				case 6:
				case 9:
				case 10:
				case 11:
					if ($_type_id == 5) {
						$pass['title'] = 'SAMPLE-ใบงานสั่งตัดหมวก';
					} else if ($_type_id == 6) {
						$pass['title'] = 'SAMPLE-ใบงานสั่งตัดเสื้อแจ็คเก็ต';
					} else if ($_type_id == 9) {
						$pass['title'] = 'SAMPLE-ใบงานสั่งตัดกระเป๋าผ้า';
					} else if ($_type_id == 10) {
						$pass['title'] = 'SAMPLE-ใบงานสั่งตัดผ้ากันเปื้อน';
					} else if ($_type_id == 11) {
						$pass['title'] = 'SAMPLE-ใบงานสั่งตัดเสื้อคนงาน';
					}
					$pass['detail_section'] = $this->load->view('order/pdf/section/_pdf_order_detail_others', $pass, TRUE);
					$pass['others_price_panel'] = $this->load->view('order/pdf/section/_pdf_others_price', $pass, TRUE);
					$pass['size_quan_section'] = $this->load->view('order/pdf/section/_pdf_size_quan', $pass, TRUE);
					$pass['screen_section'] = $this->load->view('order/pdf/section/_pdf_screen', $pass, TRUE);
					$pass['images_section'] = $this->load->view('order/pdf/section/_pdf_sample_images', $pass, TRUE);
					$html = $this->load->view('order/pdf/pdf_other', $pass, TRUE);
					break;
			}
//echo $html;exit;
			/*
			$this->load->library('mpdf8');
			$this->mpdf8->exportMPDF_withWaterMark($html, $file_name, 'SAMPLE');
			*/
			$this->load->library('pdf');
			$this->pdf->exportMPDF_withTextWaterMark($html, 'SAMPLE', $file_name);
		}
	}

	function commit() {
		$_blnSuccess = FALSE;
		$_strError = '';
		$_strMessage = '';
		$json_input_data = json_decode(trim(file_get_contents('php://input')), true); //get json
		$_arrData = (isset($json_input_data))?$json_input_data:$this->input->post(); //or post data submit
		if (isset($_arrData) && ($_arrData != FALSE)) {
			try {
				$this->load->model($this->modelName, 'm');
				$this->db->trans_begin();
				
				$_arr = $_arrData['json_images'];
				if (is_array($_arr)) {
					//++ Manage upload files
					$this->load->helper('upload_helper');
					$_tmpPath = _file_temp_upload_path();
					$_upPath = _file_upload_path();
					for ($_i=1;$_i<10;$_i++) {
						$_key = 'file_image' . $_i;
						$_oldKey = 'old_file_image' . $_i;
						if (array_key_exists($_key, $_arr) && ($_arr[$_key] != '') && ($_arr[$_key] != 'unchange')) {
							$_oldFile = $_tmpPath . $_arr[$_key];
							$_ext = pathinfo($_oldFile, PATHINFO_EXTENSION);
							$_newFileName = gmdate('YmdHis') . '-' . $_i . '-pl.' . $_ext; // $_now->format('YmdHis') . '-1-pl.' . $_ext;
							$_newFile = $_upPath . $_newFileName;
							if (file_exists($_oldFile)) {
								if (rename($_oldFile, $_newFile)) {
									$_arrData['json_images'][$_key] = $_newFileName;
								} else {
									$_arrData['json_images'][$_key] = '';
								}
							}
						}
						//++ Manage old image files
						if (array_key_exists($_oldKey, $_arr) && ($_arr[$_oldKey] != '') && ($_arr[$_oldKey] != $_arr[$_key])) {
							$_to_delete = $_upPath . trim($_arr[$_oldKey]);
							if (file_exists($_to_delete)) {
								unlink($_to_delete);
							}
						}
					}
					//-- Manage upload files
				}
				
				$_aff_rows = $this->m->commit($_arrData);
				$_strError = $this->m->error_message;
			} catch (Exception $e) {
				$_blnSuccess = FALSE;
				$_strError = $e->getMessage();
			}

			if (($this->db->trans_status() === FALSE) || ($_strError != "")) {
				$_strError .= "::DB Transaction rollback";
				$this->db->trans_rollback();
			} else {
				$_blnSuccess = TRUE;
				$_strMessage = $_aff_rows;
				$this->db->trans_complete();			
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
}