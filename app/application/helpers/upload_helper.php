<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

	function _url_upload_path() {
		$_return = base_url() . UPLOAD_PATH;
		$_return = str_replace('\\', '/', $_return);
		if (substr($_return, -1) != "/") $_return .= '/';
		return $_return;
	}
	
	function _file_upload_path() {
		$_return = FCPATH . UPLOAD_PATH;
		if (DIRECTORY_SEPARATOR  == "/") {
			$_return = str_replace('\\', '/', $_return);
			if (substr($_return, -1) != "/") $_return .= '/';			
		} else {
			$_return = str_replace('/', '\\', $_return);
			if (substr($_return, -1) != "\\") $_return .= "\\";
		}
		if(!is_dir($_return)) mkdir($_return, 0755, true);
		return $_return;
	}
	
	function _url_temp_upload_path() {
		$_return = base_url() . TEMP_UPLOAD_PATH;
		$_return = str_replace('\\', '/', $_return);
		if (substr($_return, -1) != "/") $_return .= '/';
		return $_return;
	}
	
	function _file_temp_upload_path() {
		//$_return = realpath(APPPATH) . DIRECTORY_SEPARATOR . TEMP_UPLOAD_PATH;
		$_return = FCPATH . TEMP_UPLOAD_PATH;
		if (DIRECTORY_SEPARATOR  == "/") {
			$_return = str_replace('\\', '/', $_return);
			if (substr($_return, -1) != "/") $_return .= '/';			
		} else {
			$_return = str_replace('/', '\\', $_return);
			if (substr($_return, -1) != "\\") $_return .= "\\";
		}
		if(!is_dir($_return)) mkdir($_return, 0755, true);
		return $_return;
	}
	
	function hlpr_doTempUploadPathCleanUp() {
		$_uploaddir_path = _file_temp_upload_path();
		foreach (glob($_uploaddir_path."*") as $_file) {
			/*** if file is 24 hours (86400 seconds) old then delete it ***/
			if (filemtime($_file) < time() - 86400) {
				unlink($_file);
			}
		}
	}
