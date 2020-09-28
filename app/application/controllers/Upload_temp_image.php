<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_temp_image extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url', 'file', 'upload_helper'));
		hlpr_doTempUploadPathCleanUp();
	}
	public function index() {
		$_arrFiles = array();
		$_arrError = array();
		if (isset($_FILES['image']) && ($_FILES['image']['name']) && ($_FILES['image']['name'] != "")) {
			$_upload_path_url = _url_temp_upload_path();
			$_uploaddir_path = _file_temp_upload_path();

			//$_dat = new DateTime();
			$_uplCnfg = array(
				'upload_path' => $_uploaddir_path
				, 'file_name' => gmdate('YmdHis') //$_dat->format('YmdHis')
				, 'allowed_types' => 'jpg|jpeg|png|gif'
				, 'max_size' => 5000
				, 'max_width' => 4000
				, 'max_height' => 4000
				, 'overwrite' => FALSE
			);
			//$this->load->library('upload', $_uplCnfg);
			$this->load->library('upload');
			$this->upload->initialize($_uplCnfg);
			if ( ! $this->upload->do_upload('image')) {
				$_arrError[] = $this->upload->display_errors();
			} else {
				try {
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
						$_arrError[] = $this->image_lib->display_errors();
					}
				} catch (Exception $e) {
					$_arrError[] = $this->image_lib->display_errors();
				}
			}
		}
		if (count($_arrError) > 0) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(array('error' => join("<br>\n, ", $_arrError))));
		} else if (count($_arrFiles) <= 0) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(array('error' => "No file uploaded")));
		} else {
			/*
			this has to be the only data returned or you will get an error.
			if you don't give this a json array it will give you a Empty file upload result error
			it you set this without the if(IS_AJAX)...else... you get ERROR:TRUE (my experience anyway)
			 so that this will still work if javascript is not enabled
			*/
			if (IS_AJAX) echo json_encode(array("files" => $_arrFiles));
		}
	}
}