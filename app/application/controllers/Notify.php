<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notify extends MY_Controller {

	function __construct() {
		parent::__construct();
	}

	function _explode_trim($delimiter, $str) { 
		if ( is_string($delimiter) ) { 
			$str = trim(preg_replace('|\\s*(?:' . preg_quote($delimiter) . ')\\s*|', $delimiter, $str)); 
			return explode($delimiter, $str); 
		} 
		return $str; 
	}

	function _datFromPost($strDateText) {
		$datDummy = new DateTime();
		if (strlen($strDateText) >= 8) {
			$arr = $this->_explode_trim("/", $strDateText);
			if (count($arr) == 3) {
				If ($arr[2] >= 2500) {
					$arr[2] -= 543;
				}
				if (checkdate($arr[1], $arr[0], $arr[2])) {
					$datDummy->setDate($arr[2], $arr[1], $arr[0]);
					return $datDummy;
				}
			}
		}
		return FALSE;
	}

	public function json_list_message_notification() {		
		$this->load->model('mdl_master_table', 'mst');
		$_arrResult = $this->mst->list_message_notification();
		$_strError = $this->mst->error_message;
		$_blnSuccess = (trim($_strError) == '');
		if ($_arrResult == -7) {
			$_strError = '-7';
		} elseif (! is_array($_arrResult)) $_arrResult = array();

		$_json = json_encode(
			array(
				'success' => $_blnSuccess,
				'error' => $_strError,
				'data' => $_arrResult
			)
		);
		echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$_json.")" : $_json;
	}
	public function json_list_order_notification() {		
		echo "Welcome, " . $this->_AC->_user_display . " ( " . $this->_AC->_user_name . " ) !!";
	}
}