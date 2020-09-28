<?php
class Mdl_master_table extends MY_Model { //extens CI_Model

	function __construct() {
		parent::__construct();
	}

	function list_all_province() {
		//return $this->db->order_by('name_th')->get('pm_m_province')->result_array();//order_by("title",	"[asc/desc]");	
		$_sql = 'SELECT * FROM pm_m_province ORDER BY name_th COLLATE "th-TH-x-icu"'; //real server use "th_TH", local server user "th-TH-x-icu"
		return $this->db->query($_sql)->result_array();
	}

	function list_joomla_users() {
		include ( APPPATH.'config'.DS.'database.php' );
		$_jdb = $this->load->database($db['joomla'], TRUE);
		return $_jdb->order_by('id')->get('users')->result_array();
	}
	
	function list_joomla_all_branch() {
		include ( APPPATH.'config'.DS.'database.php' );
		$_jdb = $this->load->database($db['joomla'], TRUE);
		return $_jdb->where("title LIKE 'สาขา %'", NULL, FALSE)->order_by('id')->get('usergroups')->result_array();
	}

	function list_joomla_user_branch() {
		include ( APPPATH.'config'.DS.'database.php' );
		
		$_arrReturn = FALSE;
		$_arr = $this->db->query("SELECT * FROM v_joomla_admin_users WHERE id = ?", array($this->session->userdata('user_id')))->result_array();
		if (is_array($_arr) && (count($_arr) > 0)) {
			$_arrReturn = $this->list_joomla_all_branch();
		} else {
			$_sql = <<<SQL
SELECT ug.id, ug.title
	FROM {$db['joomla']['database']}.{$db['joomla']['dbprefix']}usergroups ug
	INNER JOIN {$db['joomla']['database']}.{$db['joomla']['dbprefix']}user_usergroup_map ugm ON ugm.group_id = ug.id
WHERE ugm.user_id = ?
AND ug.title LIKE 'สาขา %'
GROUP BY ug.id, ug.title
SQL;
			$_arrReturn = $this->db->query($_sql, array($this->session->userdata('user_id')))->result_array();
		}
//echo $this->db->last_query();exit;
		return $_arrReturn;
	}
	
	function list_joomla_users_in_branch($branch_id) {
		include ( APPPATH.'config'.DS.'database.php' );
		$_jdb = $this->load->database($db['joomla'], TRUE);
		return $_jdb->select('users.id AS id, users.name AS name')
			->from('users')
				->join('user_usergroup_map', 'users.id = user_usergroup_map.user_id')
			->where("user_usergroup_map.group_id", $branch_id)
			->order_by('users.name')
			->get()->result_array();
	}
	
	function list_available_joomla_users_sales_report($branch_id, $date_from, $date_to) {
		include ( APPPATH.'config/database.php' );
		$_strDataDB = $db['default']['database'];
		$_strJoomlaDB = $db['joomla']['database'];
		$_strJoomlaPF = $db['joomla']['dbprefix'];
		$_dtFrom = ($date_from instanceof Datetime) ? $date_from : $this->_datFromPost($date_from);
		$_dtTo = ($date_to instanceof Datetime) ? $date_to : $this->_datFromPost($date_to);
//var_dump($branch_id);var_dump($_dtFrom->format('Ymd'));var_dump($_dtTo->format('Ymd'));exit;
		if (($_dtFrom) && (!($_dtFrom instanceof Datetime))) return FALSE;
		if (($_dtTo) && (!($_dtTo instanceof Datetime))) $_dtTo = new DateTime();
//var_dump($branch_id);var_dump($_dtFrom->format('Ymd'));var_dump($_dtTo->format('Ymd'));exit;
/*
		$_jdb = $this->load->database($db['joomla'], TRUE);
		return $_jdb->select('users.id AS id, users.name AS name')
			->from('users u')
				->join('user_usergroup_map ugm', 'u.id = ugm.user_id')
				->join($_strDataDB.'pm_v_order_report o', 'o.user_id = u.id')
			->where("ugm.group_id", $branch_id)
			->where(" >=", $date_from, FALSE)
			->where("DATE_FORMAT(o.order_date, '%m/%d/%Y') <", $date_to, FALSE)
			->order_by('u.name')
			->get()->result_array();
*/
		$_sql = <<<SQL
SELECT u.id AS id, u.name AS name
	FROM {$_strJoomlaDB}.{$_strJoomlaPF}users u
	INNER JOIN {$_strJoomlaDB}.{$_strJoomlaPF}user_usergroup_map ugm ON ugm.user_id = u.id
	INNER JOIN v_order_report o ON u.id = o.create_by
WHERE ugm.group_id = ?
AND TO_CHAR(o.order_date, 'YYYYMMDD') BETWEEN ? AND ?
GROUP BY u.id, u.name
SQL;
		$_q = $this->db->query($_sql, array($branch_id, $_dtFrom->format('Ymd'), $_dtTo->format('Ymd')));
//echo $this->db->last_query();exit;
		return $_q->result_array();
	}
	
	function get_branch_name($branch_id) {
		include ( APPPATH.'config/database.php' );
		$_jdb = $this->load->database($db['joomla'], TRUE);
		$_arr = $_jdb->select('title')->where("id", $branch_id)->get('usergroups')->result_array();
		if (is_array($_arr) && (count($_arr) > 0)) {
			return $_arr[0]['title'];
		} else {
			return '';
		}
	}
	
	function list_all() {
		$_iargs = func_num_args();
		$tablePrefix = ($_iargs > 2) ? func_get_arg(2) : 'pm_m_';
		$orderBy = ($_iargs > 1) ? func_get_arg(1) : NULL;
		$tableName = ($_iargs > 0) ? func_get_arg(0) : FALSE;
		if ($tableName == FALSE) return FALSE;
		
		$_db = $this->db;
		if ($orderBy) {
			$_db = $this->db->order_by($orderBy);
		}
		return $_db->get($tablePrefix . $tableName)->result_array();
	}
	
	function list_where($tableName, $where, $orderBy = NULL, $tablePrefix = 'pm_m_') {
		$_db = $this->db->where($where); 
		if ($orderBy) {
			$_db = $this->db->order_by($orderBy);
		}
		return $_db->get($tablePrefix . $tableName)->result_array();
	}
	
	function int_exists_job_number($job_number, $order_type_id, $order_rowid) {
		$_q = $this->db->query('SELECT fnc_check_dup_job_number(?, ?, ?) AS dup_count;', array($job_number, $order_type_id, $order_rowid));
		$_row = $_q->row();
		return $_row->dup_count;
	}

	function list_message_notification() {
		if (is_null($this->session)) return -7;
		$_user_id = $this->db->escape((int)$this->session->userdata('user_id'));
		return $this->arr_execute("SELECT * FROM fnc_list_notify__quotation(?)", array($_user_id));		
	}
}