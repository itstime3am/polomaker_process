<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_weave_process2 extends MY_Model
{
	function __construct()
	{
		parent::__construct();
		if (!isset($this->db)) {
			$this->db = $this->load->database('default', TRUE);
		}
	}

	function search($arrObj = array())
	{

				$_sql = <<<EOT
				-- WEAVE SQL
				select o.job_number, o.customer , CONCAT(o.type, ' [ ', o.category, ' ] ') as disp_order , o.standard_pattern as pattern
						, d.position, o.fabric, o.sum_qty as qty, d.detail, d.size, d.job_hist, s.screen_type, s.name AS disp_type, ss."name"  as disp_status,
						 tmp.rowid  as prod_id, tmp.prods_rowid  as prods_rowid, o.quotation_detail_rowid ,d.type_id, d.order_rowid, d.seq
						, ARRAY_TO_JSON(ARRAY(
							SELECT UNNEST(fnc_manu_screen_avai_status(tmp.prods_rowid)) 
							INTERSECT 
							SELECT UNNEST(uac.arr_avail_status)
						)) AS arr_avail_status
						, ARRAY_TO_JSON(ARRAY(
							--SELECT UNNEST(fnc_quotation_avai_action(GREATEST(t.deliver_status_rowid, t.produce_status_rowid))) 
							SELECT UNNEST(fnc_manu_screen_avai_action(tmp.prods_rowid)) 
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
							LEFT JOIN pm_t_manu_production tmp on tmp.type_id = d.type_id and  tmp.order_rowid = d.order_rowid
							LEFT JOIN m_manu_screen_status ss ON ss.rowid = tmp.prods_rowid 
						WHERE o.ps_rowid = 10 
						AND s.screen_type = 2
						AND COALESCE(o.is_cancel, 0) < 1
EOT;
		/*
		if (isset($arrObj['is_active_status']) && ($arrObj['is_active_status'])) {
			$_sql .= "\nAND (COALESCE(v.ps_rowid, 1) >= 10 AND (v.ps_rowid != 60))\n";
			unset($arrObj['is_active_status']);
		}
		$_sql .= $this->_getSearchConditionSQL($arrObj, 
			array(
				'job_number' => array("type"=>"txt", 'dbcol'=>'v.job_number'),
				'date_from' => array('type'=>'dat', 'dbcol'=>'v.order_date', 'operand'=>'>='),
				'date_to' => array('type'=>'dat', 'dbcol'=>'v.order_date', 'operand'=>'<=')
			)
		);
		$_sql .= $this->_getCheckAccessRight("v.create_by", "order");
		$_sql .= ' LIMIT 3000';
*/
		$_sql .= "\n ORDER BY d.type_id, d.order_rowid, d.seq ";

		return $this->arr_execute($_sql);
	}

	function change_status_by_id($rowid, $ps_rowid, $status_remark = FALSE)
	{
		$_rowid = $this->db->escape((int) $rowid);
		$_ps_rowid = $this->db->escape((int) $ps_rowid);

		if ($status_remark) $this->db->set('status_remark', $status_remark);
		$this->db->set('ps_rowid', $_ps_rowid);
		$this->db->set('update_by', $this->db->escape((int)$this->session->userdata('user_id')));
		$this->db->where('rowid', $_rowid);
		$this->db->update($this->_TABLE_NAME);

		$this->error_message = $this->db->error()['message'];
		return true;
	}
}