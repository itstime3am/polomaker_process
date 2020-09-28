<?php

class Mdl_sales_dashboard extends MY_Model {
	function __construct() {
		parent::__construct();
	}

	function list_sales_report($arrObj = array()) {
		include( APPPATH.'config/database.php' );
		$_mainCond = '';
		if (array_key_exists('user_list', $arrObj) && (trim($arrObj['user_list']) != '')) {
			$_arrUsers = explode(',', trim($arrObj['user_list']));
			unset($arrObj['user_list']);
			if (count($_arrUsers) > 0) {
				$_mainCond = "WHERE uc.id IN " . "(" . $this->db->escape_str(implode(',', $_arrUsers)) . ") ";
			}
		}

		$_sql = <<<QUERY
SELECT uc.name AS sales_name, 
SUM(CASE WHEN t.type_id IS NULL THEN 0 ELSE 1 END) AS count_order, 
SUM(COALESCE(t.sum_qty, 0)) as sum_qty, SUM(COALESCE(t.total_price_sum_net, 0)) AS sum_amount 
FROM {$db['joomla']['database']}.{$db['joomla']['dbprefix']}users uc
	LEFT OUTER JOIN v_order_report_status t ON t.create_by = uc.id 
QUERY;
		$_arrSpecSearch = array(
			'date_from' => array('type'=>'dat', 'dbcol'=>'t.raw_order_date', 'operand'=>'>=')
			,'date_to' => array('type'=>'dat', 'dbcol'=>'t.raw_order_date', 'operand'=>'<=')
		);
		$_sql .= $this->_getSearchConditionSQL($arrObj, $_arrSpecSearch);
		
		$_sql .= <<<QRY
$_mainCond
GROUP BY uc.name 
QRY;
	
//		$_sql .= $this->_getCheckAccessRight("t.create_by", "quotation");
//		$_sql .= "GROUP BY uc.name ";
//		$_sql .= "";
//echo $_sql;exit;
		return $this->arr_execute($_sql);
	}
}