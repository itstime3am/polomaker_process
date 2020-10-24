<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
class MY_Controller extends CI_Controller {
	function __construct() {
		parent::__construct();
		date_default_timezone_set("Asia/Bangkok");
		$this->_DISABLE_ON_LOAD_SEARCH = FALSE;
		
		$this->page_name = $this->page_path = strtolower($this->router->fetch_class()); //strtolower(get_class($this));
		$this->page_method = strtolower($this->router->fetch_method());
		if ($this->page_method != 'index') $this->page_path = $this->page_path . '/' . $this->page_method;

		$this->load->library('access_control', NULL, '_AC');
		$this->load->helper('joomla_auth_helper');

		$this->_AC_NAME = '';
		if (substr($this->page_name, 0, 6) == 'order_') {
			$this->_AC_NAME = 'order';
		} else if (substr($this->page_name, -8) == '_pattern') {
			$this->_AC_NAME = 'pattern';
		} else if (substr($this->page_name, 0, 9) == 'customer_') {
			$this->_AC_NAME = 'customer';
		} else {
			$this->_AC_NAME = $this->page_name;
		}
		
		_checkSessionAuth($this->page_name, $this->_AC);
		if (($this->_blnCheckRight() === FALSE) && ($this->page_name != 'home')) exit(str_replace('v_XX_1', '', $this->_AC->_MSG_ACCESS_NOT_ALLOWED));
		
		//Bypassing all details's access right with master edit right
		if (substr($this->page_name, -7) == '_detail') {
			$_master = str_replace('_detail', '', $this->page_name);
			$this->_AC_NAME = $this->_blnCheckRight('edit', $_master);
		}
		
		$this->_arrPrependHTML = array();
		$this->_arrAttachHTML = array();
		/*++ temp file use for sorting include file during assign step in derived class, before include it in final add view step 
			index: 0 = initial, 1 = declaration 1, 2 = declaration 2, 3 = process step 1, 4 = process step 2
		++*/
		$this->_tmp_header_js = array(
			'file' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array()),
			'ie' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array()),
			'ie6' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array()),
			'custom' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array()),
			'custom_init' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array()),
			'ie_custom' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array()),
			'ie6_custom' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array())
		);
		$this->_tmp_header_css = array(
			'file' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array()),
			'ie' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array()),
			'ie6' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array()),
			'custom' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array()),
			'custom_init' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array()),
			'ie_custom' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array()),
			'ie6_custom' => array(0=>array(), 1=>array(), 2=>array(), 3=>array(), 4=>array())
		);
		/*-- temp file use for sorting include file during assign step in derived class, before include it in final add view step --*/

		$this->_header_js = array(
			'file' => array(),
			'ie' => array(),
			'ie6' => array(),
			'custom' => array(),
			'custom_init' => array(),
			'ie_custom' => array(),
			'ie6_custom' => array()
		);
		$this->_header_css = array(
			'file' => array(),
			'ie' => array(),
			'ie6' => array(),
			'custom' => array(),
			'custom_init' => array(),
			'ie_custom' => array(),
			'ie6_custom' => array()
		);
		/*++ Mostly use in JSPN to add special css and js along with panel to enable them on the fly ++*/
		$this->_onload_css_files = array();
		$this->_onload_js_files = array();
		$this->_onload_css_files_append = array();
		$this->_onload_js_files_append = array();
		/*-- Mostly use in JSPN to add special css and js along with panel to enable them on the fly --*/
		$this->_onload_scripts = array(
			0 => '', //step 0 - declaration
			1 => '', //step 1 - initial
			2 => '',
			3 => '',
			4 => ''
		);

		$this->_add_css(
			array(
				'public/css/main.css'
			)
			, NULL
			, 3);
		
		$this->_add_js(
			array(
				'public/js/jquery/1.11.0/jquery.js',
				'public/js/jquery/ui/1.10.4/jquery-ui.min.js',
				array('var _baseUrl = "' . base_url() .'";', 'custom'),
				array('var CONTROLLER_NAME = "' . strtolower($this->page_name) .'";', 'custom')
			)
			, 'file'
			, 0);
		$this->_add_js(
			array(
				'public/js/jsGlobalConstants.js',
				'public/js/jsGlobal.js',
				'public/js/jsUtilities.js',
				'public/js/message.notification.js',
				array('public/js/ie.js', 'ie'),
				array('public/js/ie6.js', 'ie6'),
				array('alert("You are using IE");', 'ie_custom'),
				array('alert("Stop using IE6 (test period :D)");', 'ie6_custom')
			)
			, 'file'
			, 3);
	}

	function __performPrependContents(&$params) {
		$_returnStr = '';
		if (is_array($params) && is_array($this->_arrPrependHTML)) {
			foreach ($this->_arrPrependHTML as $_key=>$_html) {
				if (! is_integer($_key)) {
					if (array_key_exists($_key, $params)) $params[$_key] .= $_html;
				} else {
					$_returnStr .= "\n" . $_html;		
				}
				unset($this->_arrPrependHTML[$_key]);
			}
		}	
		return $_returnStr;
	}

	function __performAppendContents(&$params) {
		$_returnStr = '';
		if (is_array($params) && is_array($this->_arrAttachHTML)) {
			foreach ($this->_arrAttachHTML as $_key=>$_html) {
				if (! is_integer($_key)) {
					if (array_key_exists($_key, $params)) $params[$_key] .= $_html;
				} else {
					$_returnStr .= "\n" . $_html;		
				}
				unset($this->_arrAttachHTML[$_key]);
			}
		}
		return $_returnStr;
	}

	function add_view($path, $params = array(), $return = FALSE, $with_script_header = FALSE) {
		if (! is_bool($return)) $return = FALSE;
		if (! is_bool($with_script_header)) $with_script_header = FALSE;
		
		if (
			(strpos($path, '_public/_search_panel') === 0) 
			&& (
				((isset($params['list_viewable'])) && ($params['list_viewable'] == FALSE))
				|| ($this->_blnCheckRight('view') == FALSE)
			)
		) {
			if ($return == TRUE) {
				return '&nbsp;';
			} else {
				echo '&nbsp;';
			}
		}
		if (! (isset($params['auto_load_css']) && (($params['auto_load_css'] == FALSE) || ($params['auto_load_css'] == 'n')))) {
			if (file_exists('public/css/' . $path . '.css')) {
				if ((strpos($path, '_public/') === 0)) {
					$this->add_onload_css_file('public/css/' . $path . '.css');
				} else {
					$this->add_onload_css_file('public/css/' . $path . '.css', TRUE);					
				}
			}
		}
		if (! (isset($params['auto_load_js']) && (($params['auto_load_js'] == FALSE) || ($params['auto_load_js'] == 'n')))) {
			$strPath = '';
			if (defined('ENVIRONMENT') && (ENVIRONMENT != 'development') && file_exists('public/js/' . $path . '.obf.js')) {
				$strPath = 'public/js/' . $path . '.obf.js';
			} else if (file_exists('public/js/' . $path . '.js')) {
				$strPath = 'public/js/' . $path . '.js';
			}
			if (!empty($strPath)) {
				if ((strpos($path, '_public/') === 0)) {
					$this->add_onload_js_file($strPath);
				} else {
					$this->add_onload_js_file($strPath, TRUE);
				}
			}
		}

		if ((strpos($path, '_public/_list') == 0) && ($this->__isExistsJs('dataTable/1.10.11', NULL, FALSE) > 0)) $params['jqDataTable'] = '1.10.11';
		
		//++ end scripts
		if (is_array($params) && array_key_exists('end_script', $params) && (strlen(trim($params['end_script'])) > 0)) {
			$this->_onload_scripts[2] .= trim($params['end_script']) . "\n";
			unset($params['end_script']);
		}
		//-- end scripts
		//++ Auto Search Control
		if (is_array($params) && isset($params['autosearch']) && ($params['autosearch'] == FALSE)) {
			$this->_add_js('var _autoSearch_OnLoad = false;', "custom", 0);
			$this->_DISABLE_ON_LOAD_SEARCH = TRUE;
		}
		//-- Auto Search Control
		
		//Add _ACR to view params
		if (((substr($path, 0, 8) == '_public/') && ($path != '_public/_template_main') && (strpos($path, '_sub_template/') == FALSE)) && isset($this->_AC) && isset($this->_AC->_ACR)) $params['_ACR'] = $this->_AC->_ACR;
		if ($with_script_header == TRUE) $params['_scripts_include'] = $this->_get_headers();

		$_content = $this->load->view($path, $params, TRUE);

		$_prepToGlobalHTML = $this->__performPrependContents($params);
		$_appeToGlobalHTML = $this->__performAppendContents($params);
		if (strlen(trim($_prepToGlobalHTML)) > 0) $_content = $_prepToGlobalHTML . $_content;
		if (strlen(trim($_appeToGlobalHTML)) > 0) $_content .= $_appeToGlobalHTML;

		/*++ clear inheritable params cache */
		if (isset($params['layout'])) unset($params['layout']);
		if(!empty($params)) {
			foreach ($params as $key => $value) $params[$key] = NULL;
			$this->load->view($path, $params, TRUE);
		}
		$this->load->clear_vars();
		/*-- clear inheritable params cache */

		if ($return == TRUE) {
			return $_content;
		} else {
			echo $_content;
		}
	}

	function add_view_with_script_header($path, $params = array(), $return = FALSE) {
		if (! is_bool($return)) $return = FALSE;

		if (file_exists('public/css/' . $this->page_name . '/main.css')) $this->add_onload_css_file('public/css/' . $this->page_name . '/main.css');
		if (file_exists('public/css/' . $this->page_path . '/main.css')) $this->add_onload_css_file('public/css/' . $this->page_path . '/main.css');
		if (file_exists('public/js/' . $this->page_name . '/main.js')) $this->add_onload_js_file('public/js/' . $this->page_name . '/main.js');
		if (file_exists('public/js/' . $this->page_path . '/main.js')) $this->add_onload_js_file('public/js/' . $this->page_path . '/main.js');

		$_content = $this->add_view($path, $params, $return, TRUE);
		if ($return == TRUE) {
			return $_content;
		} else {
			echo $_content;
		}		
	}

	function __prepareHeaderFiles() {
		foreach ($this->_tmp_header_css as $_type=>$_arrValueSet) {
			if (is_array($_arrValueSet)) foreach ($_arrValueSet as $_eaValueSet) {
				if (is_array($_eaValueSet)) foreach ($_eaValueSet as $_item) {
					$_item = (isset($_item)) ? trim($_item) : NULL;
					if ((! empty($_item)) && (! in_array($_item, $this->_header_css[$_type]))) array_push($this->_header_css[$_type], $_item);
				}
			}
		}
//var_dump($this->_tmp_header_js);exit;
		foreach ($this->_tmp_header_js as $_type=>$_arrValueSet) {
			if (is_array($_arrValueSet)) foreach ($_arrValueSet as $_eaValueSet) {
				if (is_array($_eaValueSet)) foreach ($_eaValueSet as $_item) {
					$_item = (isset($_item)) ? trim($_item) : NULL;
					if ((! empty($_item)) && (! in_array($_item, $this->_header_js[$_type]))) array_push($this->_header_js[$_type], $_item);
				}
			}
		}
	}

	function __performRemoveExistsJs($item, $type = '') {
		if (empty($item)) return FALSE;
		if (empty($type)) $type = 'file';
		if (is_array($this->_tmp_header_js[$type])) foreach ($this->_tmp_header_js[$type] as $_i => $_eaHayStck) {
			if (is_array($_eaHayStck)) foreach ($_eaHayStck as $_j=>$_eaItem) {
				if (trim(strtolower($_eaItem)) == trim(strtolower($item))) unset($this->_tmp_header_js[$type][$_i][$_j]);
			}
		}
		if (is_array($this->_onload_js_files) && (count($this->_onload_js_files) > 0)) foreach($this->_onload_js_files as $_i => $_ea) {
			if (trim(strtolower($_ea)) == trim(strtolower($item))) {
				unset($this->_onload_js_files[$_i]);
			}
		}
		if (is_array($this->_onload_js_files_append) && (count($this->_onload_js_files_append) > 0)) foreach($this->_onload_js_files_append as $_i => $_ea) {
			if (trim(strtolower($_ea)) == trim(strtolower($item))) {
				unset($this->_onload_js_files_append[$_i]);
			}
		}
	}

	function __performRemoveExistsCss($item, $type = '') {
		if (empty($item)) return FALSE;
		if (empty($type)) $type = 'file';
		if (is_array($this->_tmp_header_css[$type])) foreach ($this->_tmp_header_css[$type] as $_i => $_eaHayStck) {
			if (is_array($_eaHayStck)) foreach ($_eaHayStck as $_j=>$_eaItem) {
				if (trim(strtolower($_eaItem)) == trim(strtolower($item))) unset($this->_tmp_header_css[$type][$_i][$_j]);
			}
		}
		
		if (is_array($this->_onload_css_files) && (count($this->_onload_css_files) > 0)) foreach($this->_onload_css_files as $_i => $_ea) {
			if (trim(strtolower($_ea)) == trim(strtolower($item))) unset($this->_onload_css_files[$_i]);
		}
			
		if (is_array($this->_onload_css_files_append) && (count($this->_onload_css_files_append) > 0)) foreach($this->_onload_css_files_append as $_i => $_ea) {
			if (trim(strtolower($_ea)) == trim(strtolower($item))) unset($this->_onload_css_files_append[$_i]);
		}
	}

	function __isExistsJs($find_string, $type = 'file', $is_match_exact = TRUE) {
		if (empty($find_string) || (! is_string($find_string))) return FALSE;
		if (empty($type)) $type = 'file';
		if (! is_bool($is_match_exact)) $is_match_exact = TRUE;
		
		if (is_array($this->_tmp_header_js[$type])) foreach ($this->_tmp_header_js[$type] as $_i => $_eaItem) {
			if (is_array($_eaItem)) foreach ($_eaItem as $_j => $_eaFile) {
				if ($is_match_exact) {
					if (trim(strtolower($_eaFile)) == trim(strtolower($find_string))) return 1;
				} else { // find any part of string
					if (stristr($_eaFile, trim($find_string))) return 1;
				}
			}
		}

		if (is_array($this->_onload_js_files) && (count($this->_onload_js_files) > 0)) foreach($this->_onload_js_files as $_ea) {
			if ($is_match_exact) {
				if (trim(strtolower($_ea)) == trim(strtolower($find_string))) return 2;
			} else { // find any part of string
				if (stristr($_ea, trim($find_string))) return 2;
			}
		}
		if (is_array($this->_onload_js_files_append) && (count($this->_onload_js_files_append) > 0)) foreach($this->_onload_js_files_append as $_ea) {
			if ($is_match_exact) {
				if (trim(strtolower($_ea)) == trim(strtolower($find_string))) return 3;
			} else { // find any part of string
				if (stristr($_ea, trim($find_string))) return 3;
			}
		}
		return FALSE;
	}
	
	function __isExistsCss($find_string, $type = 'file', $is_match_exact = TRUE) {
		if (empty($find_string) || (! is_string($find_string))) return FALSE;
		
		if (empty($type)) $type = 'file';
		if (! is_bool($is_match_exact)) $is_match_exact = TRUE;
		if (is_array($this->_tmp_header_css[$type])) foreach ($this->_tmp_header_css[$type] as $_i => $_eaHayStck) {
			if (is_array($_eaHayStck)) foreach ($_eaHayStck as $_j => $_eaFile) {
				if ($is_match_exact) {
					if (trim(strtolower($_eaFile)) == trim(strtolower($find_string))) return 1;
				} else { // find any part of string
					if (stristr($_eaFile, trim($find_string))) return 1;
				}
			}
		}

		if (is_array($this->_onload_css_files) && (count($this->_onload_css_files) > 0)) foreach($this->_onload_css_files as $_ea) {
			if ($is_match_exact) {
				if (trim(strtolower($_ea)) == trim(strtolower($find_string))) return 2;
			} else { // find any part of string
				if (stristr($_ea, trim($find_string))) return 2;
			}
		}
		if (is_array($this->_onload_css_files_append) && (count($this->_onload_css_files_append) > 0)) foreach($this->_onload_css_files_append as $_ea) {
			if ($is_match_exact) {
				if (trim(strtolower($_ea)) == trim(strtolower($find_string))) return 3;
			} else { // find any part of string
				if (stristr($_ea, trim($find_string))) return 3;
			}
		}
		return FALSE;
	}

	function _add_js($file = '', $type = 'file', $phase = 4) {
		if (empty($file)) return;
		
		if (empty($type)) $type = 'file';
		if (! is_integer($phase)) $phase = 4;

		if (is_array($file)) {
			foreach($file AS $item) {
				if (is_array($item)) {
					$_type = isset($item[1]) ? $item[1] : $type;
					$this->_add_js($item[0], $_type, $phase);
				} else {
					$this->_add_js($item, $type, $phase);
				}
			}
		} else {
//echo $file.' ( ' . $type . ' > '.$phase.' )'."<br>\r\n";
			if (! $this->__isExistsJs($file)) array_push($this->_tmp_header_js[$type][$phase], $file);
		}
	}

	function _add_css($file='', $type='file', $phase = 4) {
		if (empty($file)) return;

		if (empty($type)) $type = 'file';
		if (! is_integer($phase)) $phase = 4;

		if (is_array($file)) {
			foreach($file AS $item) {
				if (is_array($item)) {
					$_type = isset($item[1]) ? $item[1] : $type;
					$this->_add_css($item[0], $_type, $phase);
				} else {
					$this->_add_css($item, $type, $phase);
				}
			}
		} else {
			//$this->__performRemoveExistsCss($file, $type);
			if (! $this->__isExistsCss($file)) array_push($this->_tmp_header_css[$type][$phase], $file);
		}
	}

	function _prepend_js($file='', $type='file') {
		$this->_add_js($file, $type, 1);
	}
	function _prepend_css($file='', $type='file') {
		$this->_add_css($file, $type, 1);
	}
	function _append_js($file='', $type='file') {
		$this->_add_js($file, $type, 3);
	}
	function _append_css($file='', $type='file') {
		$this->_add_css($file, $type, 3);
	}
	function add_js($file='', $type='file') {
		$this->_add_js($file, $type, 3);
	}
	function add_css($file='', $type='file') {
		$this->_add_css($file, $type, 3);
	}

	function add_onload_js_file($file='', $orderIndex = FALSE) {
		$file = trim($file);
		if ((! empty($file)) && file_exists($file)) {
			if ($this->__isExistsJs($file) > 0) $this->__performRemoveExistsJs($file);
			if ($orderIndex === FALSE) {
				if (! in_array($file, $this->_onload_js_files)) array_push($this->_onload_js_files, $file);
			} else {
				if (is_numeric($orderIndex) && (count($this->_onload_js_files_append) < intval($orderIndex))) {
					array_splice($this->_onload_js_files_append, intval($orderIndex), 0, $file);
				} else {
					$this->_onload_js_files_append[] = $file;					
				}
			}
		}
	}

	function add_onload_css_file($file = '', $orderIndex = FALSE) {
		$file = trim($file);
		if ((! empty($file)) && file_exists($file)) {
			if ($this->__isExistsCss($file) > 0) $this->__performRemoveExistsCss($file);
			if ($orderIndex === FALSE) {
				if (! in_array($file, $this->_onload_css_files)) array_push($this->_onload_css_files, $file);
			} else {
				if (is_numeric($orderIndex) && (count($this->_onload_css_files_append) < intval($orderIndex))) {
					array_splice($this->_onload_css_files_append, intval($orderIndex), 0, $file);
				} else {
					$this->_onload_css_files_append[] = $file;					
				}
			}
		}
	}

	function _getOnloadFiles_CSS() {
		$_str = "";
		$_strAppend = "";
		$_unq = uniqid(); // '';//
		foreach ($this->_onload_css_files_append as $_eaFile) {
			$_i = array_search($_eaFile, $this->_onload_css_files);
			if ($_i !== FALSE) unset($this->_onload_css_files[$_i]);
			if ((strlen(trim($_eaFile)) > 0) && (! in_array(trim($_eaFile), $this->_header_css['file']))) $_strAppend .= '<link rel="stylesheet" href="'.trim($_eaFile).'?'.$_unq.'" charset="utf-8" type="text/css" />'."\n";
		}
		foreach ($this->_onload_css_files as $_eaFile) {
			if ((strlen(trim($_eaFile)) > 0) && (! in_array(trim($_eaFile), $this->_header_css['file']))) $_str .= '<link rel="stylesheet" href="'.trim($_eaFile).'?'.$_unq.'" charset="utf-8" type="text/css" />'."\n";
		}
		return $_str . "\n" . $_strAppend;
	}

	function _getOnloadFiles_JS() {
		$_str = "";
		$_strAppend = "";
		$_unq = uniqid(); // '';//
		foreach ($this->_onload_js_files_append as $_eaFile) {
			$_i = array_search($_eaFile, $this->_onload_js_files);
			if ($_i !== FALSE) unset($this->_onload_js_files[$_i]);
			if ((strlen(trim($_eaFile)) > 0) && (! in_array(trim($_eaFile), $this->_header_js['file']))) $_strAppend .= '<script type="application/javascript" src="'.trim($_eaFile).'?'.$_unq.'" charset="utf-8"></script>'."\n";
		}
		foreach ($this->_onload_js_files as $_eaFile) {
			if ((strlen(trim($_eaFile)) > 0) && (! in_array(trim($_eaFile), $this->_header_js['file']))) $_str .= '<script type="application/javascript" src="'.trim($_eaFile).'?'.$_unq.'" charset="utf-8"></script>'."\n";
		}
		return $_str . "\n" . $_strAppend;
	}

	function _getOnloadScript() {
		$_strReturn = "\n";
		$_onPageLoad = '';
		ksort($this->_onload_scripts);
		foreach ($this->_onload_scripts as $_idx=>$_eaStep) {
			if (is_array($_eaStep)) {
				foreach ($_eaStep as $_eaScript) {
					if (strlen(trim($_eaScript)) > 0) {
						if ($_idx == 0) { //initial level
							$_strReturn .= $_eaScript . "\n";
						} else {
							$_onPageLoad .= $_eaScript . "\n";
						}
					}
				}
			} elseif (strlen(trim($_eaStep)) > 0) {
				if ($_idx == 0) { //initial level
					$_strReturn .= $_eaStep . "\n";
				} else {
					$_onPageLoad .= $_eaStep . "\n";					
				}
			}
		}
		if (strlen(trim($_onPageLoad)) > 0) $_strReturn .= "\t$(function() {\n" . $_onPageLoad . "\n});\n";
		return $_strReturn;
	}

	function _get_headers() {
		$_unq = uniqid(); // '';//
		//++ Auto Search Control
		if (isset($this->_DISABLE_ON_LOAD_SEARCH) && ($this->_DISABLE_ON_LOAD_SEARCH == TRUE)) {
			$this->_onload_scripts[3] .= "\t\tif (typeof doPopulateTable == 'function') doPopulateTable([], true);\n";
		} else {
			$this->_onload_scripts[3] .= "\t\tif ((typeof _autoSearch_OnLoad == 'undefined') || (_autoSearch_OnLoad == true)) $('#btnSearch').trigger('click');\n";
		}
		//-- Auto Search Control

		$str = '';
		$this->__prepareHeaderFiles();

		if (count($this->_header_css['custom_init']) > 0) {
			$str .= '<style type="text/css" media="screen">'."\n";
			foreach($this->_header_css['custom_init'] AS $item) {
				$str .= $item."\n";
			}
			$str .= '</style>'."\n";
		}
		foreach($this->_header_css['file'] AS $item) {
			$str .= '<link rel="stylesheet" href="'.$item.'?'.$_unq.'" charset="utf-8" type="text/css" />'."\n";
		}
		$_files = $this->_getOnloadFiles_CSS();
		if (! empty(trim($_files))) $str .= $_files;
		
		if (count($this->_header_css['ie6']) > 0) {
			$str .= '<!--[if IE 6]>'."\n";
			foreach($this->_header_css['ie6'] AS $item) {
				$str .= '<link rel="stylesheet" href="'.$item.'?'.$_unq.'" media="screen" charset="utf-8" type="text/css" />'."\n";
			}
			$str .= '<![endif]-->'."\n";
		}
		if (count($this->_header_css['ie']) > 0) {
			$str .= '<!--[if IE]>'."\n";
			foreach($this->_header_css['ie'] AS $item) {
				$str .= '<link rel="stylesheet" href="'.$item.'?'.$_unq.'" media="screen" charset="utf-8" type="text/css" />'."\n";
			}
			$str .= '<![endif]-->'."\n";
		}
		if (count($this->_header_css['custom']) > 0) {
			$str .= '<style type="text/css" media="screen">'."\n";
			foreach($this->_header_css['custom'] AS $item) {
				$str .= $item."\n";
			}
			$str .= '</style>'."\n";
		}

		if (count($this->_header_js['custom_init']) > 0) {
			$str .= '<script type="application/javascript" charset="utf-8">'."\n";
			foreach($this->_header_js['custom_init'] AS $item){
				$str .= $item."\n";
			}			
			$str .= '</script>'."\n";		
		}
		$str .= "<!-- header js -->\n";
		foreach($this->_header_js['file'] AS $item){
			$str .= '<script type="application/javascript" src="'.$item.'?'.$_unq.'" charset="utf-8"></script>'."\n";
		}
		$str .= "<!-- header js -->\n";

		$str .= "<!-- onload js -->\n";
		$_files = $this->_getOnloadFiles_JS();
		if (! empty(trim($_files))) $str .= $_files;
		$str .= "<!-- others js -->\n";		

		if (count($this->_header_js['ie6']) > 0) {
			$str .= '<!--[if IE 6]>'."\n";
			foreach($this->_header_js['ie6'] AS $item){
				$str .= '<script type="application/javascript" src="'.$item.'?'.$_unq.'" charset="utf-8"></script>'."\n";
			}
			$str .= '<![endif]-->'."\n";
		}

		if (count($this->_header_js['ie']) > 0) {
			$str .= '<!--[if IE]>'."\n";
			foreach($this->_header_js['ie'] AS $item){
				$str .= '<script type="application/javascript" src="'.$item.'?'.$_unq.'" charset="utf-8"></script>'."\n";
			}
			$str .= '<![endif]-->'."\n";
		}
		$str .= '<script type="application/javascript" charset="utf-8">'."\n";
		if (count($this->_header_js['custom']) > 0) {
			foreach($this->_header_js['custom'] AS $item){
				$str .= $item."\n";
			}			
		}
		$_script = $this->_getOnloadScript();
		if (strlen(trim($_script)) > 0) $str .= $_script;
		$str .= '</script>'."\n";

		return $str;
	}

	function _blnCheckRight($name = '', $group = NULL) {
/* ++ TEST */
return TRUE;
/* -- TEST */
		if ($this->_AC_NAME === TRUE) return TRUE; //Bypass all 
		
		$_group_name = ( ! empty($group) ) ? strtolower($group) : $this->_AC_NAME;
		return $this->_AC->blnCheckRight($name, $_group_name);
	}
	
	function _serviceCheckRight($param) {
		if ($this->_AC_NAME === TRUE) return TRUE; //Bypass all 
		
		$_blnAllow = FALSE;
		if (is_bool($param)) {
			$_blnAllow = (boolean)$param;
		} elseif (is_string($param)) {
			$_blnAllow = $this->_blnCheckRight($param);
		}

		if ($_blnAllow == FALSE) {
			$_strPref = 'ผู้ใช้ "' . $this->session->userdata('user_name') . '"';
			$_json = json_encode(
				array(
					"success" => FALSE,
					"error" => str_replace('v_XX_1', $_strPref, $this->_AC->_MSG_FUNCTION_NOT_ALLOWED)
				)
			);
			header('content-type: application/json; charset=utf-8');
			echo isset($_GET['callback'])? "{" . $_GET['callback']. "}(".$_json.")":$_json;
			exit();
		}
	}

	function __getAjaxPostParams() {
		$json_input_data = json_decode(trim(file_get_contents('php://input')), true); //get json
		$_arrData = (isset($json_input_data))?$json_input_data:$this->input->post(); //or post data submit
		if (is_array($_arrData)) {
			return $_arrData;
		} else {
			return FALSE;
		}
	}
}

//---------------------------------------------------

include('MY_Ctrl_crud.php');
include('MY_Ctrl_master.php');
include('MY_Ctrl_order.php');
/*
	function _get_headers() {
		$str = '';
		foreach($this->_header_css['file'] AS $item) {
			//$str .= '@import url("'.$item.'");'."\n";
			$str .= '<link rel="stylesheet" href="'.trim($item).'" charset="utf-8" type="text/css" />'."\n";
		}
		if (count($this->_header_css['ie6']) > 0) {
			$str .= '<!--[if IE 6]>'."\n";
			foreach($this->_header_css['ie6'] AS $item) {
				$str .= '<link rel="stylesheet" href="'.$item.'" media="screen" charset="utf-8" type="text/css" />'."\n";
			}
			$str .= '<![endif]-->'."\n";
		}
		if (count($this->_header_css['ie']) > 0) {
			$str .= '<!--[if IE]>'."\n";
			foreach($this->_header_css['ie'] AS $item) {
				$str .= '<link rel="stylesheet" href="'.$item.'" media="screen" charset="utf-8" type="text/css" />'."\n";
			}
			$str .= '<![endif]-->'."\n";
		}
		if (count($this->_header_css['custom']) > 0) {
			$str .= '<style type="text/css" media="screen">'."\n";
			foreach($this->_header_css['custom'] AS $item) {
				$str .= $item."\n";
			}
			$str .= '</style>'."\n";
		}

		if (count($this->_header_js['custom_init']) > 0) {
			$str .= '<script type="application/javascript" charset="utf-8">'."\n";
			foreach($this->_header_js['custom_init'] AS $item){
				$str .= $item."\n";
			}
			$str .= '</script>'."\n";
		}
		foreach($this->_header_js['file'] AS $item){
			$str .= '<script type="application/javascript" src="'.$item.'" charset="utf-8"></script>'."\n";
		}
		if (count($this->_header_js['ie6']) > 0) {
			$str .= '<!--[if IE 6]>'."\n";
			foreach($this->_header_js['ie6'] AS $item){
				$str .= '<script type="application/javascript" src="'.$item.'" charset="utf-8"></script>'."\n";
			}
			$str .= '<![endif]-->'."\n";
		}
		if (count($this->_header_js['ie']) > 0) {
			$str .= '<!--[if IE]>'."\n";
			foreach($this->_header_js['ie'] AS $item){
				$str .= '<script type="application/javascript" src="'.$item.'" charset="utf-8"></script>'."\n";
			}
			$str .= '<![endif]-->'."\n";
		}
		if (count($this->_header_js['custom']) > 0) {
			$str .= '<script type="application/javascript" charset="utf-8">'."\n";
			foreach($this->_header_js['custom'] AS $item){
				$str .= $item."\n";
			}
			$str .= '</script>'."\n";
		}
		return $str;
	}
*/
