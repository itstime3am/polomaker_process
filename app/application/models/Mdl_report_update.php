<?php
class Mdl_report_update extends MY_Model {
	function __construct()
	{
		parent::__construct();
		if (! isset($this->db)) {
			$this->db = $this->load->database('default', TRUE);
		}
		$this->_TABLE_NAME = 'pm_t_order_status';
		$this->_AUTO_FIELDS = array(
			'rowid' => ''
		);
		$this->_FIELDS = array(
			'order_type_id' => '',
			'order_rowid' => '',
			'order_status_id' => '',
			'is_screen' => '',
			'screen_status_id' => '',
			'is_weave' => '',
			'weave_status_id' => '',
			'net_amount' => '',
			'vat' => '',
			'total_amount' => '',
			'deposit_payment' => '',
			'deposit_route_id' => '',
			'deposit_date' => '',
			'status_deliver_date' => '',
			'close_payment_route_id' => '',
			'close_payment_date' => '',
			'close_payment_amount' => '',
			'close_payment_wht' => '',
			'payment_status_id' => '',
			'deliver_remark' => '',
			'account_remark' => '',
			'status_update_by' => NULL,
			'status_update_date' => NULL,
			'deliver_by' => NULL,
			'deliver_date' => NULL,
			'account_by' => 0,
			'account_date' => NULL
		);
	}
	/*
FORMAT(r.sum_qty, 2) AS sum_qty, FORMAT(r.sum_item_price, 2) AS sum_item_price, FORMAT(r.ea_item_price, 2) AS ea_item_price, FORMAT(r.ea_screen_price, 2) AS ea_screen_price, FORMAT(r.ea_weave_price, 2) AS ea_weave_price, FORMAT(r.sum_screen_price, 2) AS sum_screen_price, FORMAT(r.sum_weave_price, 2) AS sum_weave_price, FORMAT(r.sum_other_price, 2) AS sum_other_price, FORMAT(r.ea_other_price, 2) AS ea_other_price,

, FORMAT(r.total_price_sum - r.deposit_payment, 2) AS disp_close_payment_amount
	*/
	
	function search($arrObj = array()) {
		include ( APPPATH.'config/database'.EXT );
		$_sql = <<<QUERY
SELECT t.*, COALESCE(t.close_payment_amount, t.total_price_sum - t.deposit_payment) AS close_payment_amount
, COALESCE(uc.name, '-') AS sales_name 
FROM pm_v_order_report_status t 
	LEFT OUTER JOIN {$db['joomla']['database']}.{$db['joomla']['dbprefix']}users uc ON t.create_by = uc.id 
WHERE True  
QUERY;
		$_arrSpecWhere = array(
			'order_status' => array('dbcol'=>'t.order_status_id', 'type'=>'int'),
			'job_number' => array('dbcol'=>'t.job_number', 'type'=>'txt'),
			'customer_rowid' => array('dbcol'=>'t.customer_rowid'),
			'create_user_id' => array('dbcol'=>'uc.id', 'type'=>'int')
		);
		if (array_key_exists('category_id', $arrObj)) {
			$_cat_id = $arrObj['category_id'];
			if (is_numeric($_cat_id)) $_arrSpecWhere['category_id'] = array('dbcol'=>'t.category_type_id','type'=>'int');
		}
		if (array_key_exists('product_type_id', $arrObj)) {
			$_type_id = $arrObj['product_type_id'];
			if (is_numeric($_type_id)) {
				if ($_type_id <= 2) { // if 1,2 query both
					$_arrSpecWhere['product_type_id'] = array('dbcol'=>'t.order_type_id','type'=>'raw','operand'=>'IN','val'=>"(1,2,3,4)");
				} else {
					$_arrSpecWhere['product_type_id'] = array('dbcol'=>'t.order_type_id','type'=>'raw','operand'=>'IN','val'=>"(" . $_type_id . "," . ($_type_id + 2) . ")");
				}
			}
		}
		$_condition_date_field = 't.raw_order_date';
		if (array_key_exists('date_type', $arrObj) && is_numeric($arrObj['date_type'])) {
			$_date_type = (int) $arrObj['date_type'];
			if ($_date_type == 2) {
				$_condition_date_field = 't.raw_deposit_date';
			}
			unset($arrObj['date_type']);
		}
		if (array_key_exists('date_from', $arrObj)) {
			$datDateFrom = $this->_datFromPost($arrObj['date_from']);
			if ($datDateFrom !== '') {
				$_arrSpecWhere['date_from'] = array('dbcol'=>$_condition_date_field, 'operand'=>'>=', 'val'=>$datDateFrom->format('Y-m-d'));
			} else {
				unset($arrObj['date_from']);
			}
		}
		if (array_key_exists('date_to', $arrObj)) {
			$datDateTo = $this->_datFromPost($arrObj['date_to']);
			if ($datDateTo !== '') {
				$_arrSpecWhere['date_to'] = array('dbcol'=>$_condition_date_field, 'operand'=>'<=', 'val'=>$datDateTo->format('Y-m-d'));
			} else {
				unset($arrObj['date_to']);
			}
		}
		if (array_key_exists('deposit_route_id', $arrObj)) {
			if ($arrObj['deposit_route_id'] >= 0) {
				$_arrSpecWhere['deposit_route_id'] = array(
					"type"=>"raw", 
					"dbcol"=>"COALESCE(t.deposit_route_id, 0)", 
					"val"=> $this->db->escape((int) trim($arrObj['deposit_route_id']))
				);
			} else {
				unset($arrObj['deposit_route_id']);
			}
		}
		if (array_key_exists('close_payment_route_id', $arrObj)) {
			if ($arrObj['close_payment_route_id'] >= 0) {
				$_arrSpecWhere['close_payment_route_id'] = array(
					"type"=>"raw", 
					"dbcol"=>"COALESCE(t.close_payment_route_id, 0)", 
					"val"=> $this->db->escape((int) trim($arrObj['close_payment_route_id']))
				);
			} else {
				unset($arrObj['close_payment_route_id']);
			}
		}
		$_sql .= $this->_getSearchConditionSQL($arrObj, $_arrSpecWhere);
		$_sql .= $this->_getCheckAccessRight("t.create_by", "report_update");
		$_sql .= "ORDER BY t.job_number, t.raw_order_date, t.raw_due_date ";	
//		$_sql .= "LIMIT 100";	
//echo $_sql;exit;
		return $this->arr_execute($_sql);
	}
	
	function commit($arrObj, $intUpdateType) {
//var_dump($arrObj);exit;
		$this->success_rows = 0;
		$_BASE_FIELDS = array();
		foreach ($this->_FIELDS as $key=>$value) {
			array_push($_BASE_FIELDS, $key);
		}
		$this->db->trans_begin();
		foreach ($arrObj as $index=>$obj) {
			unset($this->_FIELDS);
			$this->_FIELDS = array();
			foreach ($obj as $_k=>$_v) {
				if (in_array($_k, $_BASE_FIELDS)) {
					if (substr($_k, -5) == '_date') {
						$_datValue = $this->_datFromPost($_v);
						if ($_datValue instanceof DateTime) $this->_FIELDS[$_k] = $_datValue;
					} elseif (isset($_v)) {
						$this->_FIELDS[$_k] = $_v;
					}
				}
			}
			switch ($intUpdateType) {
				case 1:
					$this->_FIELDS['status_update_by'] = $this->session->userdata('user_id');
					$this->_FIELDS['status_update_date'] = new DateTime();
					break;
				case 2:
					$this->_FIELDS['deliver_by'] = $this->session->userdata('user_id');
					$this->_FIELDS['deliver_date'] = new DateTime();
					break;
				case 3:
					$this->_FIELDS['account_by'] = $this->session->userdata('user_id');
					$this->_FIELDS['account_date'] = new DateTime();
					break;
			}
//var_dump($this->_FIELDS) . "\n<br>";exit;
			$_blnIsInsert = TRUE;
			if (array_key_exists('rowid', $obj) && (trim($obj['rowid']) > '0')) $_blnIsInsert = FALSE;

			if ($_blnIsInsert) {
/*++ Inferno 20150729 fix duplicate report error */
				//$this->insert();
				$__cols = '';
				$__vals = '';
				$__dupStm = '';
				foreach ($this->_FIELDS as $_k=>$_v) {
					$__cols .= $_k . ',';
					$__dupStm .= $_k . '=VALUES(' . $_k . '),';
					if ($_v instanceof DateTime) {
						$__vals .= 'STR_TO_DATE(' . $this->db->escape($_v->format('Y/m/d H:i:s')) . ", '%Y/%m/%d %H:%i:%s'),";
					} else {
						$__vals .= $this->db->escape($_v) . ',';
					}
				}
				if (strlen($__cols) > 0) {
					$__cols = substr($__cols, 0, -1);
					$__vals = substr($__vals, 0, -1);
					$__dupStm = substr($__dupStm, 0, -1);
				}
				$_insSQL = <<<INSSQL
INSERT INTO {$this->_TABLE_NAME} ($__cols)
VALUES ($__vals)
ON DUPLICATE KEY UPDATE 
$__dupStm
;

INSSQL;
				$this->db->query($_insSQL);
				$this->error_message .= $this->db->_error_message();
/*-- Inferno 20150729 fix duplicate report error */				
			} else {
				$this->update(array('rowid' => $obj['rowid']));
			}
			if ($this->error_message != '') break;
			$this->success_rows += 1;
		}
//echo $this->success_rows . "\n<br>";exit;
		if (($this->db->trans_status() === FALSE) || ($this->error_message != '')) {
			$this->error_message = 'DB Transaction error::' . $this->error_message;
			$this->db->trans_rollback();
			return FALSE;
		} else {
			$this->db->trans_complete();
			return TRUE;
		}	
	}
}