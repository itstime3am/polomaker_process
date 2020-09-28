<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Ctrl_order extends MY_Ctrl_crud {
	function __construct() {
		parent::__construct();
	}
	function _getTypeID() {
		switch (strtolower($this->modelName)) {
			case 'mdl_order_polo':
				return 1;
				break;
			case 'mdl_order_tshirt':
				return 2;
				break;
			case 'mdl_order_premade_polo':
				return 3;
				break;
			case 'mdl_order_premade_tshirt':
				return 4;
				break;
			case 'mdl_order_cap':
				return 5;
				break;
			case 'mdl_order_jacket':
				return 6;
				break;
			case 'mdl_order_premade_cap':
				return 7;
				break;
			case 'mdl_order_premade_jacket':
				return 8;
				break;
		}
	}
	public function commit_payment_log() {
		$_blnSuccess = FALSE;
		$_strError = "Unknown error";
		$_strMessage = "";
		
		$_arrData = $this->__getAjaxPostParams();
		if (isset($_arrData) && ($_arrData != FALSE)) {
			$_arrData['type_id'] = $this->_getTypeID();
			try {
				$this->load->model('Mdl_order_payment_log', 'm');
				$_strMessage = $this->m->commit($_arrData, FALSE);
				$_strError = $this->m->error_message;
			} catch (Exception $e) {
				$_strError = $e->getMessage();
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
	public function delete_payment_log() {
		$_blnSuccess = FALSE;
		$_strError = "Unknown error";
		$_strMessage = "";
		
		$_arrData = $this->__getAjaxPostParams();
		if (isset($_arrData) && ($_arrData != FALSE) && ($_arrData['rowid'] > 0)) {
			$_arrData['cancel_date'] = new DateTime();
			$_arrData['cancel_by'] = $this->session->userdata('user_id');
			$_arrData['is_cancel'] = 1;
			try {
				$this->load->model('Mdl_order_payment_log', 'm');
				$_strMessage = $this->m->commit($_arrData, FALSE);
				$_strError = $this->m->error_message;
			} catch (Exception $e) {
				$_strError = $e->getMessage();
			}
		} else {
			$_strError = 'Invalid parameter ( "rowid" )';
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
	public function approve_payment_log() {
		$_blnSuccess = FALSE;
		$_strError = "Unknown error";
		$_strMessage = "";
		
		$_arrData = $this->__getAjaxPostParams();
		if (isset($_arrData) && ($_arrData != FALSE)) {
			$_arrData['approve_date'] = new DateTime();
			$_arrData['approve_by'] = $this->session->userdata('user_id');
			try {
				$this->load->model('Mdl_order_payment_log', 'm');
				$_strMessage = $this->m->commit($_arrData, FALSE);
				$_strError = $this->m->error_message;
			} catch (Exception $e) {
				$_strError = $e->getMessage();
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