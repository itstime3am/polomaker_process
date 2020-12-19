<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_weave_process extends MY_Model
{
	function __construct()
	{
		parent::__construct();
		if (!isset($this->db)) {
			$this->db = $this->load->database('default', TRUE);
		}

		$this->_TABLE_NAME = 'pm_t_manu_weave_production';
		$this->_AUTO_FIELDS = array(
			'rowid' => ''
		);
	}

	function search($arrObj = array())
	{
		$_userid = $this->session->userdata('user_id');
		$_sql = <<<EOT
		-- WEAVE SQL
		select  DISTINCT ON (o.job_number, d.seq, o.order_date)
		o.job_number, o.customer , CONCAT(o.type, ' [ ', o.category, ' ] ') as disp_order , o.standard_pattern as pattern, osd.start_ps_date
		, d.position, o.fabric, o.total_qty as qty, d.detail, d.size, d.job_hist, s.screen_type, s.name AS disp_type,  o.type_id, o.company
		, tmp.rowid  as prod_id, tmp.prod_status  as status_rowid, ss.name  as disp_status, tmp.weave_type as type_rowid, mst.name as disp_weave_type
		, tmp.width , tmp.height, tmp.fabric_date , tmp.eg_date, tmp.block_emp , tmp.block_number , tmp.stitch_number , tmp.color_silk_qty, tmp.prod_cost, tmp.img, tmp.eg_remark
		,d.order_rowid, d.order_screen_rowid as order_s_rowid, d.seq, tmp.prod_cost, tmp.is_cancel as is_cancel, tmp.status_remark, tmp.approve_date, tmp.order_remark
		, fs.name as disp_fabric_status
		, ARRAY_TO_JSON(ARRAY(
			SELECT UNNEST(fnc_manu_weave_avai_status(tmp.prod_status)) 
			INTERSECT 
			SELECT UNNEST(uac.arr_avail_status)
		)) AS arr_avail_status
		, ARRAY_TO_JSON(ARRAY(
			SELECT UNNEST(fnc_manu_weave_avai_action(tmp.prod_status)) 
			INTERSECT 
			SELECT UNNEST(uac.arr_avail_action)
		)) AS arr_avail_action
		, ARRAY_TO_JSON(ARRAY(
			SELECT UNNEST(fnc_manu_fabric_avai_status(tmp.fabric_status)) 
			INTERSECT 
			SELECT UNNEST(uacf.arr_avail_status)
		)) AS arr_avail_fabric
		FROM v_order_report o 
			INNER JOIN fnc_listmanu_weave_accright_byuser($_userid) uac ON True 
			INNER JOIN fnc_listmanu_fabric_accright_byuser($_userid) uacf ON True 
			INNER JOIN (
				SELECT 1 AS type_id, order_rowid, order_screen_rowid, position, detail, size, job_hist, price, seq
				FROM pm_t_order_screen_polo 
				UNION ALL
				SELECT 2 AS type_id, order_rowid, order_screen_rowid, position, detail, size, job_hist, price, seq
				FROM pm_t_order_screen_tshirt
				UNION ALL
				SELECT 3 AS type_id, order_rowid, order_screen_rowid, position, detail, size, job_hist, price, seq
				FROM pm_t_order_premade_screen_polo
				UNION ALL
				SELECT 4 AS type_id, order_rowid, order_screen_rowid, position, detail, size, job_hist, price, seq
				FROM pm_t_order_premade_screen_tshirt
				UNION ALL
				SELECT 5 AS type_id, order_rowid, order_screen_rowid, position, detail, size, job_hist, price, seq
				FROM t_order_screen_cap
				UNION ALL
				SELECT 6 AS type_id, order_rowid, order_screen_rowid, position, detail, size, job_hist, price, seq
				FROM t_order_screen_jacket
				UNION ALL
				SELECT 7 AS type_id, order_rowid, order_screen_rowid, position, detail, size, job_hist, price, seq
				FROM t_order_premade_screen_cap
				UNION ALL
				SELECT 8 AS type_id, order_rowid, order_screen_rowid, position, detail, size, job_hist, price, seq
				FROM t_order_premade_screen_jacket
				UNION ALL
				SELECT o.product_type_rowid AS type_id, s.order_rowid, s.order_screen_rowid, s.position, s.detail, s.size, s.job_hist, s.price, s.seq
				FROM t_order_other o
					INNER JOIN t_order_screen_other s ON s.order_rowid = o.rowid
				UNION ALL
				SELECT o.product_type_rowid AS type_id, s.order_rowid, s.order_screen_rowid, s.position, s.detail, s.size, s.job_hist, s.price, s.seq
				FROM t_order_premade_other o
					INNER JOIN t_order_premade_screen_other s ON s.order_rowid = o.rowid
			) d 
				ON d.type_id = o.type_id
				AND d.order_rowid = o.order_rowid
			INNER JOIN pm_m_order_screen s on s.rowid = d.order_screen_rowid
			LEFT JOIN pm_t_manu_weave_production tmp on tmp.order_weave_rowid = d.order_screen_rowid and tmp.order_rowid = d.order_rowid and tmp.seq = d.seq and tmp.type_id = o.type_id
			LEFT JOIN m_manu_weave_status ss ON ss.rowid = tmp.prod_status
			LEFT JOIN m_manu_weave_type mst ON mst.rowid = tmp.weave_type
			LEFT JOIN v_order_start_date osd ON osd.job_number = o.job_number
			LEFT JOIN m_fabric_status fs ON tmp.fabric_status = fs.rowid 
		WHERE s.screen_type = 1
		AND COALESCE(o.is_cancel, 0) < 1
EOT;

		if (isset($arrObj['is_reply_status']) && ($arrObj['is_reply_status'])) {
			$_sql .= "\n AND tmp.prod_status = 40 OR tmp.prod_status = 110 \n";
			unset($arrObj['is_reply_status']);
		}

		$_arrSpecSearch = array(
			'job_number' => array("type"=>"txt", 'dbcol'=>'o.job_number',)
			, 'date_from' => array('type'=>'dat', 'dbcol'=>'o.order_date', 'operand'=>'>=')
			, 'date_to' => array('type'=>'dat', 'dbcol'=>'o.order_date', 'operand'=>'<=')
		);
		$_sql .= $this->_getSearchConditionSQL($arrObj, $_arrSpecSearch);

		//search order all ( include not active )
		if (isset($arrObj['is_order_active']) && ($arrObj['is_order_active'])) {
			if(strpos($_sql,"t.is_order_active LIKE CONCAT('%', '1', '%')")){
				$_sql = str_replace("t.is_order_active LIKE CONCAT('%', '1', '%')", " o.ps_rowid != 60 AND o.ps_rowid >= 30 ", $_sql);
			}else{
				$_sql .= "\n AND o.ps_rowid >= 30";
			}
		}

		//search by customer_nanme ( lik condition )
		if (isset($arrObj['customer_name']) && ($arrObj['customer_name'])) {
			$searchStr = "t.customer_name LIKE CONCAT('%', '".$arrObj['customer_name']."', '%')";
			if(strpos($_sql,$searchStr)){
				$_sql = str_replace($searchStr, " ( o.customer_name like '%".$arrObj['customer_name']."%' OR o.company like '%".$arrObj['customer_name']."%')", $_sql);
			}
		}

		//search by customer_company ( lik condition )
		// if (isset($arrObj['customer_company']) && ($arrObj['customer_company'])) {
		// 	$searchStr = "t.customer_company LIKE CONCAT('%', '".$arrObj['customer_company']."', '%')";
		// 	if(strpos($_sql,$searchStr)){
		// 		$_sql = str_replace($searchStr, " o.company like '%".$arrObj['customer_company']."%'", $_sql);
		// 	}
		// }
		
		$_sql .= "\n ORDER BY o.order_date DESC LIMIT 2000";
		return $this->arr_execute($_sql);
	}

	function is_file_exits($rowid, $file_name){
		$_sql =<<<EOT
		SELECT img FROM pm_t_manu_weave_production WHERE rowid = '$rowid' AND img = '$file_name'
EOT;
		$arrData = $this->arr_execute($_sql);

		if(is_array($arrData)){
			if(count($arrData) > 0 ){
				return true;
			}else{
				return false;
			}
	}
}

	function update_data_by_id($_arrData)
	{	
		$_user_id = $this->db->escape((int)$this->session->userdata('user_id'));
		$_rowid =  $_arrData[0]['rowid'];
		$_timestamp = $_arrData[0]['timestamp'];

		if($_timestamp && $_rowid){
			if(!$this->_checkUpdateTime($_rowid, $_timestamp)){
				return false;
			};
		}

		for ( $i = 0 ; $i < count($_arrData) ; $i++){
			if($_arrData[$i]['rowid'] > 0){	
				foreach ($_arrData[$i] as $_col => $_val){
						if( strlen($_val) > 0){
							if($_col != 'timestamp'){
								$this->db->set('"'. $_col .'"', $_val);
							}
						}	
					}
			}
			$this->db->set('update_by', $_user_id);
			$this->db->where('rowid', $_arrData[$i]['rowid']);
			$this->db->update($this->_TABLE_NAME);
		}

		$this->error_message = $this->db->error()['message'];
		if($this->error_message == ''){
			$this->save_log($_user_id, 'UPDATE', $_arrData);
			return true;
		}else{
			return false;
		}
	}

	function change_status_by_id($rowid, $status_rowid, $status_remark = FALSE, $order_rowid, $order_s_rowid, $seq, $job_number, $typeid, $_timestamp)
	{
		$_rowid = $this->db->escape((int) $rowid);
		$status_rowid = $this->db->escape((int) $status_rowid);
		$_userid = $this->db->escape((int)$this->session->userdata('user_id'));
		if ($status_remark) $this->db->set('status_remark', $status_remark);

		if ($order_rowid && $order_s_rowid && $seq && $typeid) {
			if(!$this->_checkIsExits($order_rowid,  $seq)){
				return false;
			}
			$data = array(
				'order_rowid' => $order_rowid,
				'order_weave_rowid' =>  $order_s_rowid,
				'width' => '0',
				'height' => '0',
				'block_number' => '0',
				'stitch_number' => '0',
				'color_silk_qty' => '0',
				'eg_count' => '0',
				'prod_cost' => '0',
				'job_number' => $job_number,
				'is_cancel' => '0',
				'type_id' => $typeid,
				'seq' => $seq,
				'create_by' => $_userid,
				'prod_status' => $status_rowid,
				'fabric_status' => '10'
			);
			$this->db->insert($this->_TABLE_NAME, $data);

			// echo $this->db->last_query();

			$data['timestamp'] = $_timestamp;
			$this->save_log($_userid,'INSERT', $data);
		} else {

			if($_timestamp && $rowid){
				$_rowid =  $rowid;
				if(!$this->_checkUpdateTime($_rowid, $_timestamp)){
					return false;
				};
			}

			$this->db->set('prod_status', $status_rowid);
			$this->db->set('update_by', $this->db->escape((int)$this->session->userdata('user_id')));
			$this->db->where('rowid', $_rowid);
			$this->db->update($this->_TABLE_NAME);
			$data = array(
				'rowid' => $rowid,
				'prod_status' => $status_rowid,
				'update_by' => $_userid
			);

			$this->save_log($_userid,'UPDATE', $data);
		}
		$this->error_message = $this->db->error()['message'];
		return true;
	}

	function change_status_fabric_by_id($rowid, $status_rowid, $_timestamp)
	{
		$_rowid = $this->db->escape((int) $rowid);
		$status_rowid = $this->db->escape((int) $status_rowid);
		$_userid = $this->db->escape((int)$this->session->userdata('user_id'));

			if($_timestamp && $rowid){
				$_rowid =  $rowid;
				if(!$this->_checkUpdateTime($_rowid, $_timestamp)){
					return false;
				};
			}
			$this->db->set('fabric_status', $status_rowid);
			$this->db->set('update_by', $this->db->escape((int)$this->session->userdata('user_id')));
			$this->db->where('rowid', $_rowid);
			$this->db->update($this->_TABLE_NAME);
			$data = array(
				'rowid' => $rowid,
				'fabric_status' => $status_rowid,
				'update_by' => $_userid
			);
			$this->save_log($_userid,'UPDATE', $data);
		$this->error_message = $this->db->error()['message'];
		return true;
	}

	
	function _checkUpdateTime($_rowid, $_timestamp){
				$_timestamp = str_replace('/','-',$_timestamp);
				$_date = strtotime($_timestamp);
				$_sql_date = date('Y-m-d H:i:s', $_date);
	
				$_sql = "SELECT COUNT(rowid) FROM pm_t_manu_weave_production WHERE rowid = $_rowid";
				if($_timestamp){
					$_sql .= "AND update_date > '$_sql_date'";
				}
				$arrData_count = $this->arr_execute($_sql);
				if( $arrData_count[0]['count'] < 1 ){ 
					return true;
				}else{
					return false;
				}
	}

	function _checkIsExits($_order_rowid, $_seq){

		$_sql = "SELECT COUNT(rowid) FROM pm_t_manu_weave_production WHERE order_rowid = $_order_rowid AND seq = $_seq";
		$arrData_count = $this->arr_execute($_sql);
		if( $arrData_count[0]['count'] < 1 ){ 
			return true;
		}else{
			return false;
		}
	}

	
	function save_log($_userid, $_action, $_arrData){
		$_data_json = json_encode( $_arrData, JSON_UNESCAPED_UNICODE );
		$_sql_log =<<<EOT
				INSERT INTO public.pm_t_manu_weave_production_log 
				( create_by, "action", "data")
				VALUES( $_userid, '$_action', '$_data_json');
EOT;
		$this->db->query($_sql_log);
	}

}
