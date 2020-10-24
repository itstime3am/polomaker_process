<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_screen_process extends MY_Model
{
	function __construct()
	{
		parent::__construct();
		if (!isset($this->db)) {
			$this->db = $this->load->database('default', TRUE);
		}

		$this->_TABLE_NAME = 'pm_t_manu_screen_production';
		$this->_AUTO_FIELDS = array(
			'rowid' => ''
		);
	}

	function search($arrObj = array())
	{

		$_sql = <<<EOT
		-- SCREEN SQL
		select  o.job_number, o.customer , CONCAT(o.type, ' [ ', o.category, ' ] ') as disp_order , o.standard_pattern as pattern
		, d.position, o.fabric, o.sum_qty as qty, d.detail, d.size, d.job_hist, s.screen_type, s.name AS disp_type
		, tmp.rowid  as prod_id, tmp.prod_status  as status_rowid, ss.name  as disp_status, tmp.screen_type as type_rowid, mst.name as disp_screen_type
		, tmp.width , tmp.height, tmp.fabric_date , tmp.block_date , tmp.block_emp , tmp.color_qty, tmp.prod_cost, tmp.img
		,d.order_rowid, d.order_screen_rowid as order_s_rowid, d.seq, tmp.is_cancel as is_cancel
		, ARRAY_TO_JSON(ARRAY(
			SELECT UNNEST(fnc_manu_screen_avai_status(tmp.prod_status)) 
			INTERSECT 
			SELECT UNNEST(uac.arr_avail_status)
		)) AS arr_avail_status
		, ARRAY_TO_JSON(ARRAY(
			--SELECT UNNEST(fnc_quotation_avai_action(GREATEST(t.deliver_status_rowid, t.produce_status_rowid))) 
			SELECT UNNEST(fnc_manu_screen_avai_action()) 
			INTERSECT 
			SELECT UNNEST(uac.arr_avail_action)
		)) AS arr_avail_action
		FROM v_order_report o 
			INNER JOIN fnc_listmanu_screen_accright_byuser(984) uac ON True 
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
			LEFT JOIN pm_t_manu_screen_production tmp on tmp.order_screen_rowid = d.order_screen_rowid and  tmp.order_rowid = d.order_rowid and tmp.seq = d.seq
			LEFT JOIN m_manu_screen_status ss ON ss.rowid = tmp.prod_status
			LEFT join m_manu_screen_type mst on mst.rowid = tmp.screen_type
		WHERE o.ps_rowid = 10 
		AND s.screen_type = 2
		AND COALESCE(o.is_cancel, 0) < 1
EOT;

		$_sql .= "\n ORDER BY d.type_id, d.order_rowid, d.seq ";

		return $this->arr_execute($_sql);
	}

	function update_data_by_id($_arrData)
	{
		for ( $i = 0 ; $i < count($_arrData) ; $i++){
			if($_arrData[$i]['rowid'] > 0){	
				foreach ($_arrData[$i] as $_col => $_val){
						if( strlen($_val) > 0){
							$this->db->set('"'. $_col .'"', $_val);
						}
					}
			}
			$this->db->set('update_by', $this->db->escape((int)$this->session->userdata('user_id')));
			$this->db->where('rowid', $_arrData[$i]['rowid']);
			$this->db->update($this->_TABLE_NAME);
		}

		$this->error_message = $this->db->error()['message'];
		return true;
	}

	function change_status_by_id($rowid, $status_rowid, $status_remark = FALSE, $order_rowid, $order_s_rowid, $seq)
	{
		$_rowid = $this->db->escape((int) $rowid);
		$status_rowid = $this->db->escape((int) $status_rowid);
		if ($status_remark) $this->db->set('status_remark', $status_remark);

		if ($order_rowid && $order_s_rowid && $seq) {
			$data = array(
				'order_rowid' => $order_rowid,
				'order_screen_rowid' =>  $order_s_rowid,
				'width' => '0',
				'height' => '0',
				'color_qty' => '0',
				'prod_cost' => '0',
				'is_cancel' => '0',
				'seq' => $seq,
				'create_by' => $this->db->escape((int)$this->session->userdata('user_id')),
				'prod_status' => '10'
			);
			$this->db->insert($this->_TABLE_NAME, $data);
		} else {
			$this->db->set('prod_status', $status_rowid);
			$this->db->set('update_by', $this->db->escape((int)$this->session->userdata('user_id')));
			$this->db->where('rowid', $_rowid);
			$this->db->update($this->_TABLE_NAME);
		}

		$this->error_message = $this->db->error()['message'];
		return true;
	}
}
