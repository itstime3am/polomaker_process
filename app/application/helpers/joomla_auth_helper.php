<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

// echo (JPATH_BASE.DS.'includes'.DS.'defines.php');exit; 	
require_once ( JPATH_BASE.DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE.DS.'includes'.DS.'framework.php' );
/**
 * Program Name
 *
 * @package		Polomaker
 * @author		Inferno
 */

// ------------------------------------------------------------------------

/**
 * Joomla Authentication Helper
 *
 * Provide helper functions for common display operations.
 *
 * @package		Polomaker
 * @subpackage	Helper
 * @category	Authentication
 * @author		Inferno
 */
/*
function userinfo($name) {
	if (validate_session()) {
		$CI =& get_instance();
		return $CI->session->userdata($name);
	}
}
*/

function _checkSessionAuth($pageName = '', &$clsAccessControl = NULL) {
	$_page_name = strtolower(trim($pageName));
	JFactory::getApplication('site')->initialise();
	$_user = JFactory::getUser();

//print_r(JAccess::getGroupsByUser($_user->id));exit;
//print_r(implode(",", array_unique(JAccess::getAuthorisedViewLevels($_user->id))));exit;
//echo "USER ID = " . $_user->id . "\n<br>";
//var_dump($CI->session->userdata);exit;
// var_dump($_user);exit;
/* ++ TEST 
$_user = (object) array( "id"=>1, "username"=>"test", "name"=>"Test", "email"=> "test@test.com", "groups"=>array() );
/* -- TEST */
	if ((! isset($_user->id)) || empty($_user->id)) {
		if ( $_page_name != 'home' ) exit('Session invalid, plrease login.'); //redirect(prep_url(JOOMLA_URL));
	} else {
		$_arrUserData = array(
			'user_id' => $_user->id,
			'user_name' => $_user->username,
			'user_display' => $_user->name,
			'user_email' => $_user->email,
			'user_groups' => $_user->groups
		);
		$_CI =& get_instance();
		$_CI->load->library('joomla_session', NULL, 'session');
		$_CI->session->set_userdata($_arrUserData);

		if (isset($clsAccessControl)) {
			$clsAccessControl->_user_id = $_user->id;
			$clsAccessControl->_user_name = $_user->username;
			$clsAccessControl->_user_display = $_user->name;
			$clsAccessControl->_email = $_user->email;
			$clsAccessControl->_loadPageAccessRight($_user->id);
			
			$_arrUserData['_AC'] = $clsAccessControl;
		}		
	}
}
/* End of file joomla_auth_helper.php */ 
/* Location: ./application/helpers/joomla_auth_helper.php */ 