<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_temp_image extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url', 'file', 'upload_helper'));
		$this->load->model('Mdl_screen_process');
		$this->load->model('Mdl_weave_process');
		hlpr_doTempUploadPathCleanUp();
	}
	public function index() {
		$_arrFiles = array();
		$_arrError = '';
		$blnSuccess = FALSE;
		$strError='';
		$old_path ='';
		$new_path = '';
		// if(isset($_POST)){
		// echo print_r($_POST);exit;
		// }
		if (isset($_FILES['image']) && ($_FILES['image']['name']) && ($_FILES['image']['name'] != "")) {
			$_upload_path_url = _url_upload_path();
			$_uploaddir_path = _file_upload_path();
			if(isset($_POST['type'])){ $_uploaddir_path .= "manu_" . $_POST['type'];};
			if(isset($_POST['file_name']) && $_POST['file_name'] != ""){
				$__file_name = $_POST['file_name'];
				$arrdata = '';
				if( $_POST['type'] == "screen" && $_POST['timestamp']){
					if($this->Mdl_screen_process->_checkUpdateTime($_POST['ps_rowid'], $_POST['timestamp'])){
						$arrdata = $this->Mdl_screen_process->is_file_exits($_POST['ps_rowid'], $__file_name );
					}else{
						$strError = "refresh";
					}
					
				}else{
					if($this->Mdl_weave_process->_checkUpdateTime($_POST['ps_rowid'], $_POST['timestamp'])){
						$arrdata = $this->Mdl_weave_process->is_file_exits($_POST['ps_rowid'], $__file_name );
					}else{
						$strError = "refresh";
					}		
				}

				if($arrdata > 0){
					$old_path = $_uploaddir_path.'/'.$__file_name;
					$index = (int)substr($__file_name,strpos($__file_name,'(')+1,(strpos($__file_name,'(')-5)-strpos($__file_name,'('));
					$__file_name = gmdate('YmdHis')."-".$_POST['ps_rowid']."-".$_POST['job_number']."-".$_POST['ps_seq']."(".($index+1).")";
				}
			}else{
				if( $_POST['type'] == "screen" && $_POST['timestamp']){
					if($this->Mdl_screen_process->_checkUpdateTime($_POST['ps_rowid'], $_POST['timestamp'])){
						if(isset($_POST['ps_rowid']) && isset($_POST['ps_seq'])){
							$__file_name = gmdate('YmdHis')."-".$_POST['ps_rowid']."-".$_POST['job_number']."-".$_POST['ps_seq']."(1)";
						}
					}else{
						$strError = "refresh";
					}
					
				}else{
					if($this->Mdl_weave_process->_checkUpdateTime($_POST['ps_rowid'], $_POST['timestamp'])){
						if(isset($_POST['ps_rowid']) && isset($_POST['ps_seq'])){
							$__file_name = gmdate('YmdHis')."-".$_POST['ps_rowid']."-".$_POST['job_number']."-".$_POST['ps_seq']."(1)";
						}
					}else{
						$strError = "refresh";
					}		
				}
			}
			if($strError == ''){

				//$_dat = new DateTime();
				$_uplCnfg = array(
					'upload_path' => $_uploaddir_path
					, 'file_name' => $__file_name //$_dat->format('YmdHis')
					, 'allowed_types' => 'jpg|jpeg|png|gif'
					, 'max_size' => 5000
					, 'max_width' => 4000
					, 'max_height' => 4000
					, 'overwrite' => TRUE
				);
				//$this->load->library('upload', $_uplCnfg);
				$this->load->library('upload');
				$this->upload->initialize($_uplCnfg);
				if ( ! $this->upload->do_upload('image')) {
					$_arrError = $this->upload->display_errors();
				} else {
					try {
						// echo $old_path;exit;
						if($old_path != ''){
							unlink($old_path);
						}

						$data = $this->upload->data();				
						//set the data for the json array
						$info = new StdClass;
						$info->id = $this->input->post('element_id');
						$info->name = $data['file_name'];
						$info->size = $data['file_size'] * 1024;
						$info->type = $data['file_type'];
						$info->url = $_upload_path_url . $data['file_name'];
						/* 
						// ++ I set this to original file since I did not create thumbs. 
						// ++ change to thumbnail directory if you do = $_upload_path_url .'/thumbs' .$data['file_name']
						$info->thumbnailUrl = $_upload_path_url . 'thumbs/' . $data['file_name'];
						$info->deleteUrl = base_url() . 'uploads/deleteImage/' . $data['file_name'];
						$info->deleteType = 'DELETE';
						*/
						$info->error = null;
						$config = array(
							'image_library' => 'gd2'
							, 'source_image' => $data['full_path']
							, 'maintain_ratio' => TRUE
							, 'quality' => '90%'
						);
						/* ++ Remove these config to replace with resized image 
						$config['new_image'] = $_uploaddir_path . 'new/' . $data['file_name'];
						$config['create_thumb'] = TRUE;
						// -- Remove these config to replace with resized image */
						$this->load->library('image_lib', $config);
						$_result = $this->image_lib->resize();
						if ($_result !== FALSE) {
							$_arrFiles[] = $info;
						} else {
							$_arrError = $this->image_lib->display_errors();
						}
					} catch (Exception $e) {
						$_arrError = $this->image_lib->display_errors();
					}
				}
			}
		}

		if ($_arrError != '') {
			// $this->output
			// 	->set_content_type('application/json')
			// 	->set_output(json_encode(array('error' => join("<br>\n, ", $_arrError))));
			$json = json_encode(
				array(
					'success' => false,
					'error' => $_arrError,
				)
			);
			header('content-type: application/json; charset=utf-8');
			echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$json.")":$json;
		} 
		else if (!isset($_FILES['image'])) {
			$json = json_encode(
				array(
					'success' => false,
					'error' => 'No file uploaded',
					)
			);
			header('content-type: application/json; charset=utf-8');
			echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$json.")":$json;
			// $this->output
			// 	->set_content_type('application/json')
			// 	->set_output(json_encode(array('error' => "No file uploaded")));
		}else {
			/*
			this has to be the only data returned or you will get an error.
			if you don't give this a json array it will give you a Empty file upload result error
			it you set this without the if(IS_AJAX)...else... you get ERROR:TRUE (my experience anyway)
			 so that this will still work if javascript is not enabled
			*/
			if($strError == ''){
				$blnSuccess = true;
			}

			$json = json_encode(
				array(
					'success' => $blnSuccess,
					'error' => $strError,
					'files' => $_arrFiles)
				);

			header('content-type: application/json; charset=utf-8');
			echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$json.")":$json;
		}
	}
}