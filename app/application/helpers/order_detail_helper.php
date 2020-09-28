<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

function hlpr_get_OrderPolo_ViewParams() {
	$CI = get_instance();
	$CI->load->helper('crud_controller_helper');
	$_arrSelOptions = hlpr_prepareMasterTableSelectOptions(array(
		'standard_pattern'=>array('where'=>array('is_cancel'=>0,'is_polo'=>1),'order_by'=>'sort_index')
		,'fabric'=>array('where'=>array('is_cancel'=>0,'is_polo'=>1),'order_by'=>'sort_index')
		,'neck_type'=>array('where'=>array('is_cancel'=>0,'is_polo'=>1),'order_by'=>'sort_index')
		,'neck_hem'=>array('table_prefix'=>'m_','where'=>array('is_cancel'=>0),'order_by'=>'sort_index')
		,'m_shape'=>array('table_name'=>'m_base_shape','where'=>array('is_cancel'=>0,'is_polo'=>1,'is_male'=>1),'order_by'=>'sort_index')
		,'f_shape'=>array('table_name'=>'m_base_shape','where'=>array('is_cancel'=>0,'is_polo'=>1,'is_female'=>1),'order_by'=>'sort_index')
		,'main_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND polo_cols LIKE '%,main,%'",'order_by'=>'sort_index')
		,'line_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND polo_cols LIKE '%,sub,%'",'order_by'=>'sort_index')
		,'sub_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND polo_cols LIKE '%,line,%'",'order_by'=>'sort_index')
		,'option_hem'=>array('table_prefix'=>'m_','where'=>array('is_cancel'=>0),'order_by'=>'sort_index')
		,'option_hem_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND polo_cols LIKE '%,hem,%'",'order_by'=>'sort_index')
		,'clasper_type'=>array('where'=>array('is_cancel'=>0,'is_polo'=>1),'order_by'=>'sort_index')
		,'clasper_ptrn'=>array('where'=>array('is_cancel'=>0,'is_polo'=>1),'order_by'=>'sort_index')
		,'collar_type'=>array('where'=>array('is_cancel'=>0,'is_polo'=>1),'order_by'=>'sort_index')
		,'sleeves_type'=>array('where'=>array('is_cancel'=>0,'is_polo'=>1),'order_by'=>'sort_index')
		,'flap_type'=>array('where'=>array('is_cancel'=>0,'is_polo'=>1),'order_by'=>'sort_index')
		,'flap_side_ptrn'=>array('where'=>array('is_cancel'=>0,'is_polo'=>1),'order_by'=>'sort_index')
		,'pocket_type'=>array('where'=>array('is_cancel'=>0,'is_polo'=>1),'order_by'=>'sort_index')
		,'pen_pattern'=>array('where'=>array('is_cancel'=>0,'is_polo'=>1),'order_by'=>'sort_index')
		,'order_screen'=>array('where'=>array('is_cancel'=>0,'is_polo'=>1),'order_by'=>'sort_index')
		,'weave_screen_position'=>array('table_name'=>'m_weave_screen_position','where'=>array("is_polo"=>1),'order_by'=>'sort_index')
		,'supplier'=>array('table_name'=>'m_order_supplier','where'=>array('is_cancel'=>0),'no_feed_row'=>TRUE,'order_by'=>'sort_index')
		//,'pen_position'=>array('where'=>array('is_polo'=>1))
	));
	$CI->load->model('Mdl_polo_pattern', 'p1');
	$_temp = $CI->p1->search();
	$_arrSelOptions['polo_pattern'] = array(array('rowid'=>'-1', 'code'=>'- custom -'));
	foreach ($_temp as $_row) {
		$_each = array();
		foreach ($_row as $_key => $_value) {
			if (strpos($_key, 'remark') === 0) {
				$_each['detail_' . $_key] = $_value;
			} else {
				$_each[$_key] = $_value;
			}
		}
		array_push($_arrSelOptions['polo_pattern'], $_each);
	}
	
	$_temp = hlpr_prepareControlsDefault('Mdl_order_detail_polo', $_arrSelOptions);
	$_details = array();
	foreach ($_temp as $_key => $_obj) {
		if (strpos($_key, 'remark') === 0) {
			$_obj['form_edit']['name'] = 'detail_' . $_obj['form_edit']['name'];
			$_details['detail_' . $_key] = $_obj;				
		} else {
			$_details[$_key] = $_obj;
		}
	}
	hlpr_setController($_details, 'order_rowid', '', array('type'=>'hdn'));
	hlpr_setController($_details, 'standard_pattern_rowid', '');
	hlpr_setController($_details, 'fabric_rowid', '');
	hlpr_setController($_details, 'neck_type_rowid', '');
	hlpr_setController($_details, 'neck_type_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140));
	hlpr_setController($_details, 'neck_hem_rowid', ''); //- new 
	hlpr_setController($_details, 'neck_hem_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140)); //- new
	hlpr_setController($_details, 'm_shape_rowid', 'ทรงเสื้อชาย'); //- new 
	hlpr_setController($_details, 'f_shape_rowid', 'ทรงเสื้อหญิง'); //- new 
	hlpr_setController($_details, 'main_color_rowid', 'สีผ้าหลัก'); //- new 
	hlpr_setController($_details, 'line_color_rowid', 'สีวิ่งเส้น'); //- new 
	hlpr_setController($_details, 'sub_color1_rowid', 'สีรอง1', array('sel_options'=>$_arrSelOptions['sub_color'])); //- new 
	hlpr_setController($_details, 'sub_color2_rowid', 'สีรอง2', array('sel_options'=>$_arrSelOptions['sub_color'])); //- new 
	hlpr_setController($_details, 'sub_color3_rowid', 'สีรอง3', array('sel_options'=>$_arrSelOptions['sub_color'])); //- new 
	hlpr_setController($_details, 'color_detail', '', array('type'=>'txa', 'rows'=>1)); //- new
	hlpr_setController($_details, 'collar_type_rowid', 'รูปแบบปก');
	hlpr_setController($_details, 'collar_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140));
	hlpr_setController($_details, 'collar_detail2', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140));  //- new
	hlpr_setController($_details, 'm_clasper_type_rowid', 'ทรงสาบเสื้อชาย', array('sel_options'=>$_arrSelOptions['clasper_type']));
	hlpr_setController($_details, 'f_clasper_type_rowid', 'ทรงสาบเสื้อหญิง', array('sel_options'=>$_arrSelOptions['clasper_type']));
	hlpr_setController($_details, 'clasper_ptrn_rowid', 'รูปแบบสาบกระดุม');
	hlpr_setController($_details, 'clasper_detail', 'กระดุม/สีกระดุม(ระบุพิเศษ)', array('type'=>'txa', 'rows'=>1, 'maxlength'=>30));
	hlpr_setController($_details, 'clasper_detail2', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140)); //- new
	hlpr_setController($_details, 'm_sleeves_type_rowid', 'แขนเสื้อชาย', array('sel_options'=>$_arrSelOptions['sleeves_type']));
	hlpr_setController($_details, 'f_sleeves_type_rowid', 'แขนเสื้อหญิง', array('sel_options'=>$_arrSelOptions['sleeves_type']));
	hlpr_setController($_details, 'sleeves_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140));
	hlpr_setController($_details, 'flap_type_rowid', 'รูปแบบชายเสื้อ');
	hlpr_setController($_details, 'flap_type_detail', '', array('type'=>'txt', 'maxlength'=>30));
	hlpr_setController($_details, 'flap_side_ptrn_rowid', '');
	hlpr_setController($_details, 'flap_side_ptrn_detail', '', array('type'=>'txt', 'maxlength'=>30));
	hlpr_setController($_details, 'm_flap_side', 'เสื้อผู้ชาย', array('type'=>'chk'));
	hlpr_setController($_details, 'f_flap_side', 'เสื้อผุ้หญิง', array('type'=>'chk'));
	hlpr_setController($_details, 'pocket_type_rowid', '');
	hlpr_setController($_details, 'pocket_type_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>60));
	hlpr_setController($_details, 'm_pocket', 'กระเป๋าเสื้อผู้ชาย', array('type'=>'chk'));
	hlpr_setController($_details, 'f_pocket', 'กระเป๋าเสื้อผู้หญิง', array('type'=>'chk'));
	hlpr_setController($_details, 'pen_pattern_rowid', '');
	hlpr_setController($_details, 'pen_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>60));
	hlpr_setController($_details, 'm_pen', 'เสื้อผู้ชาย', array('type'=>'chk'));
	hlpr_setController($_details, 'f_pen', 'เสื้อผุ้หญิง', array('type'=>'chk'));
	hlpr_setController($_details, 'is_pen_pos_left', 'แขนซ้าย', array('type'=>'chk')); //- new
	hlpr_setController($_details, 'is_pen_pos_right', 'แขนขวา', array('type'=>'chk')); //- new
	hlpr_setController($_details, 'detail_remark1', '', array('type'=>'txa','rows'=>2,'maxlength'=>140));
	hlpr_setController($_details, 'detail_remark2', '', array('type'=>'txa','rows'=>2,'maxlength'=>140));

	// ++ option table join ++
	hlpr_setController($_details, 'option_hem_rowid', 'เพิ่มกุ๊น', array('type'=>'rdo', 'sel_options'=>$_arrSelOptions['option_hem'])); //- new 
	hlpr_setController($_details, 'option_hem_color_rowid', 'สีกุ๊น', array('type'=>'sel', 'sel_options'=>$_arrSelOptions['option_hem_color'])); //- new 
	hlpr_setController($_details, 'option_is_mfl', 'เสื้อผู้ชาย', array('type'=>'chk','class'=>'set-disabled')); //new
	hlpr_setController($_details, 'option_male_fix_length', '', array('type'=>'txt', 'maxlength'=>30)); //new
	hlpr_setController($_details, 'option_is_ffl', 'เสื้อผู้หญิง', array('type'=>'chk','class'=>'set-disabled')); //new
	hlpr_setController($_details, 'option_female_fix_length', '', array('type'=>'txt', 'maxlength'=>30)); //new
	hlpr_setController($_details, 'option_is_no_neck_tag', 'ไม่ติดป้ายคอใดๆทั้งสิ้น', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_customer_size_tag', 'ติดป้ายไซส์ของลูกค้า', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_no_plmk_size_tag', 'ติดป้ายไซส์ ไม่เอา POLOMAKER', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_no_back_clasper', 'ไม่เอาสาบหลัง', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_pakaging_tpb', 'พับแพ็คเสื้อใส่ถุงใส', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_no_packaging_sep_tpb', 'ไม่ต้องพับแพ็ค-แต่ขอถุงเสื้อแยกมา', array('type'=>'chk')); //new
	// -- option table join --

	/*
	// ++ Obsoleted ++
	hlpr_setController($_details, 'base_pattern_rowid', '');
	hlpr_setController($_details, 'base_pattern_detail', '', array('type'=>'txa', 'maxlength'=>140));
	hlpr_setController($_details, 'color', ''); 
	hlpr_setController($_details, 'color_add1', ''); 
	hlpr_setController($_details, 'color_add2', ''); 
	hlpr_setController($_details, 'pen_position_rowid', '', array('type'=>'rdo'));
	// -- Obsoleted --
	*/

	$_arrDetailsLayout = array(
		'แบบแพทเทิร์น' => array('standard_pattern_rowid', '')
		,'ชนิดผ้า' => array('fabric_rowid', '')
		,'แบบคอเสื้อ' => array('neck_type_rowid', 'neck_type_detail')
		,'แบบกุ๊นคอเสื้อ' => array('neck_hem_rowid', 'neck_hem_detail')
		,'แบบทรงเสื้อ' => array('m_shape_rowid', 'f_shape_rowid')
		,'สีเสื้อ' => array(
			array('main_color_rowid', 'line_color_rowid')
			,array('sub_color1_rowid', 'sub_color2_rowid', 'sub_color3_rowid')
			,array('option_hem_rowid', 'option_hem_color_rowid')
			,array('color_detail')
		)
		,'แบบปก' => array(
			array('collar_type_rowid', '')
			,array('collar_detail')
			,array('collar_detail2')
		)
		,'แบบสาบกระดุม' => array(
			array('m_clasper_type_rowid', 'f_clasper_type_rowid')
			,array('clasper_ptrn_rowid', 'clasper_detail')
			,array('clasper_detail2')
		)
		,'แบบแขนเสื้อ' => array(
			array('m_sleeves_type_rowid', 'f_sleeves_type_rowid')
			,array('sleeves_detail')
		)
		,'แบบชายเสื้อ' => array(
			array('flap_type_rowid', 'flap_type_detail')
			,array('return <span class="table-title frm-edit-row-title">กำหนดความยาวเสื้อ</span>', 'option_is_mfl', 'option_male_fix_length')
			,array('', 'option_is_ffl', 'option_female_fix_length')
		)
		,'แบบผ่าข้าง' => array(
			array('flap_side_ptrn_rowid', 'flap_side_ptrn_detail')
			,array('', 'm_flap_side', 'f_flap_side', '')
		)
		,'แบบกระเป๋า' => array(
			array('pocket_type_rowid', 'pocket_type_detail')
			,array('', 'm_pocket', 'f_pocket', '')
		)
		,'แบบที่เสียบปากกา' => array(
			array('pen_pattern_rowid', 'pen_detail')
			,array('return <span class="table-title frm-edit-row-title">ที่เสียบปากกา</span>', 'm_pen', 'f_pen', '')
			,array('return <span class="table-title frm-edit-row-title">ตำแหน่งที่เสียบปากกา</span>', 'is_pen_pos_left', 'is_pen_pos_right', '')
		)
		,'ข้อมูลพิเศษอื่นๆ' => array(
			array('option_is_no_neck_tag', 'option_is_customer_size_tag', 'option_is_no_plmk_size_tag')
			,array('option_is_no_back_clasper', 'option_is_pakaging_tpb', 'option_is_no_packaging_sep_tpb')
		)
		,'รายละเอียดเพิ่มเติม' => array(
			array('detail_remark1')
			,array('detail_remark2')
		)
	);

	$_editFormParams['details_panel'] = $CI->add_view('order/_detail', array(
			'controls' => hlpr_arrGetEditControls($_details),
			'layout' => $_arrDetailsLayout
		), TRUE);
	
	//++ size_quan panel form parts
	$CI->load->model('Mdl_order_polo', 'order1');
	$_editFormParams['size_quan_panel'] = $CI->add_view('order/_size_quan', array(
			'size_quan_matrix' => $CI->order1->list_size_quan()
		), TRUE);
	//-- size_quan panel form parts
	
	//++ others_price panel form parts
	$_editFormParams['others_price_panel'] = $CI->add_view('order/_others_price', array(), TRUE);
	//-- others_price panel form parts

	//++ screen panel form parts
	$_editFormParams['screen_panel'] = $CI->add_view('order/_screen', array(
			'order_screen' => $_arrSelOptions['order_screen']
			,'arr_position_list' => $_arrSelOptions['weave_screen_position']
		), TRUE);
	//-- screen panel form parts

	return array(
		"detail_panel" => $CI->add_view('order/_order_tab_detail', $_editFormParams, TRUE)
		, "others_panel" => $CI->add_view('order/_order_tab_others', $_editFormParams, TRUE)
	);
}

function hlpr_get_OrderTshirt_ViewParams() {
	$CI = get_instance();
	$CI->load->helper('crud_controller_helper');
	$_arrSelOptions = hlpr_prepareMasterTableSelectOptions(array(
		'standard_pattern'=>array('where'=>array('is_cancel'=>0,'is_tshirt'=>1),'order_by'=>'sort_index')
		,'neck_type'=>array('where'=>array('is_cancel'=>0,'is_tshirt'=>1),'order_by'=>'sort_index')
		,'fabric'=>array('where'=>array('is_cancel'=>0,'is_tshirt'=>1),'order_by'=>'sort_index')
		,'main_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND tshirt_cols LIKE '%,main,%'",'order_by'=>'sort_index')
		,'line_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND tshirt_cols LIKE '%,line,%'",'order_by'=>'sort_index')
		,'sub_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND tshirt_cols LIKE '%,sub,%'",'order_by'=>'sort_index')
		,'option_hem'=>array('table_prefix'=>'m_','where'=>array('is_cancel'=>0),'order_by'=>'sort_index')
		,'option_hem_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND tshirt_cols LIKE '%,hem,%'",'order_by'=>'sort_index')
		,'collar_type'=>array('where'=>array('is_cancel'=>0,'is_tshirt'=>1),'order_by'=>'sort_index')
		,'sleeves_type'=>array('where'=>array('is_cancel'=>0,'is_tshirt'=>1),'order_by'=>'sort_index')
		,'flap_type'=>array('where'=>array('is_cancel'=>0,'is_tshirt'=>1),'order_by'=>'sort_index')
		,'flap_side_ptrn'=>array('where'=>array('is_cancel'=>0,'is_tshirt'=>1),'order_by'=>'sort_index')
		,'pocket_type'=>array('where'=>array('is_cancel'=>0,'is_tshirt'=>1),'order_by'=>'sort_index')
		,'pen_pattern'=>array('where'=>array('is_cancel'=>0,'is_tshirt'=>1),'order_by'=>'sort_index')
		,'order_screen'=>array('where'=>array('is_cancel'=>0,'is_tshirt'=>1),'order_by'=>'sort_index')
		,'weave_screen_position'=>array('table_name'=>'m_weave_screen_position','where'=>array("is_tshirt"=>1),'order_by'=>'sort_index')
		,'supplier'=>array('table_name'=>'m_order_supplier','where'=>array('is_cancel'=>0),'no_feed_row'=>TRUE,'order_by'=>'sort_index')
		//,'pen_position'=>array('where'=>array('is_cancel'=>0,'is_tshirt'=>1),'order_by'=>'sort_index')
	));
	$CI->load->model('Mdl_tshirt_pattern', 'p2');
	$_temp = $CI->p2->search();
	$_arrSelOptions['tshirt_pattern'] = array(array('rowid'=>'-1', 'code'=>'- custom -'));
	foreach ($_temp as $_row) {
		$_each = array();
		foreach ($_row as $_key => $_value) {
			if (strpos($_key, 'remark') === 0) {
				$_each['detail_' . $_key] = $_value;				
			} else {
				$_each[$_key] = $_value;
			}
		}
		array_push($_arrSelOptions['tshirt_pattern'], $_each);
	}

	$_temp = hlpr_prepareControlsDefault('Mdl_order_detail_tshirt', $_arrSelOptions);
	$_details = array();
	foreach ($_temp as $_key => $_obj) {
		if (strpos($_key, 'remark') === 0) {
			$_obj['form_edit']['name'] = 'detail_' . $_obj['form_edit']['name'];
			$_details['detail_' . $_key] = $_obj;
		} else {
			$_details[$_key] = $_obj;
		}
	}

	hlpr_setController($_details, 'order_rowid', '', array('type'=>'hdn'));
	hlpr_setController($_details, 'standard_pattern_rowid', ''); //แบบทรงเสื้อ
	hlpr_setController($_details, 'fabric_rowid', ''); //ชนิดผ้า
	hlpr_setController($_details, 'neck_type_rowid', ''); //แบบคอเสื้อ
	hlpr_setController($_details, 'neck_type_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140));
	hlpr_setController($_details, 'm_collar_type_rowid', 'แบบซกคอชาย', array('sel_options'=>$_arrSelOptions['collar_type'])); //new
	hlpr_setController($_details, 'f_collar_type_rowid', 'แบบซกคอหญิง', array('sel_options'=>$_arrSelOptions['collar_type'])); //new
	hlpr_setController($_details, 'collar_detail', '', array('type'=>'txa', 'maxlength'=>85));
	hlpr_setController($_details, 'main_color_rowid', 'สีผ้าหลัก'); //- new 
	hlpr_setController($_details, 'line_color_rowid', 'สีวิ่งเส้น'); //- new 
	hlpr_setController($_details, 'sub_color1_rowid', 'สีรอง1', array('sel_options'=>$_arrSelOptions['sub_color'])); //- new 
	hlpr_setController($_details, 'sub_color2_rowid', 'สีรอง2', array('sel_options'=>$_arrSelOptions['sub_color'])); //- new 
	hlpr_setController($_details, 'sub_color3_rowid', 'สีรอง3', array('sel_options'=>$_arrSelOptions['sub_color'])); //- new 
	hlpr_setController($_details, 'color_detail', '', array('type'=>'txa', 'rows'=>1)); //- new
	hlpr_setController($_details, 'm_sleeves_type_rowid', 'แขนเสื้อชาย', array('sel_options'=>$_arrSelOptions['sleeves_type'])); //- new 
	hlpr_setController($_details, 'f_sleeves_type_rowid', 'แขนเสื้อหญิง', array('sel_options'=>$_arrSelOptions['sleeves_type'])); //- new 
	hlpr_setController($_details, 'sleeves_detail', '', array('type'=>'txa', 'maxlength'=>85));
	hlpr_setController($_details, 'flap_type_rowid', 'รูปแบบชายเสื้อ');
	hlpr_setController($_details, 'flap_type_detail', '', array('type'=>'txa', 'maxlength'=>85));
	hlpr_setController($_details, 'flap_side_ptrn_rowid', ''); //แบบผ่าข้าง
	hlpr_setController($_details, 'flap_side_ptrn_detail', '', array('type'=>'txt', 'maxlength'=>50));
	hlpr_setController($_details, 'm_flap_side', 'เสื้อผู้ชาย', array('type'=>'chk'));
	hlpr_setController($_details, 'f_flap_side', 'เสื้อผุ้หญิง', array('type'=>'chk'));
	hlpr_setController($_details, 'pocket_type_rowid', ''); //แบบกระเป๋าเสื้อ
	hlpr_setController($_details, 'pocket_type_detail', '', array('type'=>'txt', 'maxlength'=>50));
	hlpr_setController($_details, 'm_pocket', 'กระเป๋าเสื้อชาย', array('type'=>'chk'));
	hlpr_setController($_details, 'f_pocket', 'กระเป๋าเสื้อหญิง', array('type'=>'chk'));
	hlpr_setController($_details, 'pen_pattern_rowid', ''); //new
	hlpr_setController($_details, 'pen_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>60)); //new
	hlpr_setController($_details, 'm_pen', 'เสื้อผู้ชาย', array('type'=>'chk')); //new
	hlpr_setController($_details, 'f_pen', 'เสื้อผุ้หญิง', array('type'=>'chk')); //new
	hlpr_setController($_details, 'is_pen_pos_left', 'แขนซ้าย', array('type'=>'chk')); //new
	hlpr_setController($_details, 'is_pen_pos_right', 'แขนขวา', array('type'=>'chk')); //new
	hlpr_setController($_details, 'detail_remark1', '', array('type'=>'txa','rows'=>2, 'maxlength'=>140));
	hlpr_setController($_details, 'detail_remark2', '', array('type'=>'txa','rows'=>2, 'maxlength'=>140));

	// ++ option table join ++
	hlpr_setController($_details, 'option_hem_rowid', 'เพิ่มกุ๊น', array('type'=>'rdo', 'sel_options'=>$_arrSelOptions['option_hem'])); //- new 
	hlpr_setController($_details, 'option_hem_color_rowid', 'สีกุ๊น', array('type'=>'sel', 'sel_options'=>$_arrSelOptions['option_hem_color'])); //- new 
	hlpr_setController($_details, 'option_is_mfl', 'เสื้อผู้ชาย', array('type'=>'chk','class'=>'set-disabled')); //new
	hlpr_setController($_details, 'option_male_fix_length', '', array('type'=>'txt', 'maxlength'=>30)); //new
	hlpr_setController($_details, 'option_is_ffl', 'เสื้อผู้หญิง', array('type'=>'chk','class'=>'set-disabled')); //new
	hlpr_setController($_details, 'option_female_fix_length', '', array('type'=>'txt', 'maxlength'=>30)); //new
	hlpr_setController($_details, 'option_is_no_neck_tag', 'ไม่ติดป้ายคอใดๆทั้งสิ้น', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_customer_size_tag', 'ติดป้ายไซส์ของลูกค้า', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_no_plmk_size_tag', 'ติดป้ายไซส์ ไม่เอา POLOMAKER', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_pakaging_tpb', 'พับแพ็คเสื้อใส่ถุงใส', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_no_packaging_sep_tpb', 'ไม่ต้องพับแพ็ค-แต่ขอถุงเสื้อแยกมา', array('type'=>'chk')); //new
	// -- option table join --

	$_arrDetailsLayout = array(
		'แบบแพทเทิร์น' => array('standard_pattern_rowid', '')
		,'ชนิดผ้า' => array('fabric_rowid', '')
		,'แบบคอเสื้อ' => array('neck_type_rowid', 'neck_type_detail')
		,'แบบซก/ริบคอ' => array(
			array('m_collar_type_rowid', 'f_collar_type_rowid')
			,array('collar_detail')
		)
		,'สีเสื้อ' => array(
			array('main_color_rowid', 'line_color_rowid')
			,array('sub_color1_rowid', 'sub_color2_rowid', 'sub_color3_rowid')
			,array('option_hem_rowid', 'option_hem_color_rowid')
			,array('color_detail')
		)
		,'แบบแขนเสื้อ' => array(
			array('m_sleeves_type_rowid', 'f_sleeves_type_rowid')
			,array('sleeves_detail')
		)
		,'แบบชายเสื้อ' => array(
			array('flap_type_rowid', 'flap_type_detail')
			,array('return <span class="table-title frm-edit-row-title">เพิ่มความยาวเสื้อ</span>', 'option_is_mfl', 'option_male_fix_length')
			,array('', 'option_is_ffl', 'option_female_fix_length')
		)
		,'แบบผ่าข้าง' => array(
			array('flap_side_ptrn_rowid', 'flap_side_ptrn_detail')
			,array('', 'm_flap_side', 'f_flap_side', '')
		)
		,'แบบกระเป๋า' => array(
			array('pocket_type_rowid', 'pocket_type_detail')
			,array('', 'm_pocket', 'f_pocket', '')
		)
		,'แบบที่เสียบปากกา' => array(
			array('pen_pattern_rowid', 'pen_detail')
			,array('return <span class="table-title frm-edit-row-title">ที่เสียบปากกา</span>', 'm_pen', 'f_pen', '')
			,array('return <span class="table-title frm-edit-row-title">ตำแหน่งที่เสียบปากกา</span>', 'is_pen_pos_left', 'is_pen_pos_right', '')
		)
		,'ข้อมูลพิเศษอื่นๆ' => array(
			array('option_is_no_neck_tag', 'option_is_customer_size_tag', 'option_is_no_plmk_size_tag')
			,array('option_is_pakaging_tpb', 'option_is_no_packaging_sep_tpb', '')
		)
		,'รายละเอียดเพิ่มเติม' => array(
			array('detail_remark1')
			,array('detail_remark2')
		)
	);

	$_editFormParams['details_panel'] = $CI->add_view('order/_detail', array(
			'controls' => hlpr_arrGetEditControls($_details),
			'layout' => $_arrDetailsLayout
		), TRUE);

	//++ size_quan panel form parts
	$CI->load->model('Mdl_order_tshirt', 'order2');
	$_editFormParams['size_quan_panel'] = $CI->add_view('order/_size_quan', array(
			'size_quan_matrix' => $CI->order2->list_size_quan()
		), TRUE);
	//-- size_quan panel form parts

	//++ others_price panel form parts
	$_editFormParams['others_price_panel'] = $CI->add_view('order/_others_price', array(), TRUE);
	//-- others_price panel form parts

	//++ screen panel form parts
	$_editFormParams['screen_panel'] = $CI->add_view('order/_screen', array(
			'order_screen' => $_arrSelOptions['order_screen']
			,'arr_position_list' => $_arrSelOptions['weave_screen_position']
		), TRUE);
	//-- screen panel form parts

	return array(
		"detail_panel" => $CI->add_view('order/_order_tab_detail', $_editFormParams, TRUE)
		, "others_panel" => $CI->add_view('order/_order_tab_others', $_editFormParams, TRUE)
	);
}

function hlpr_get_OrderOther_ViewParams() {
	$CI = get_instance();
	$CI->load->helper('crud_controller_helper');
	$_arrSelOptions = hlpr_prepareMasterTableSelectOptions(array(
		'product_type'=>array('table_name'=>'m_other_product_type', 'where'=>'COALESCE(is_cancel, 0) < 1', 'order_by'=>'sort_index')
		,'fabric_type'=>array('table_name'=>'m_other_fabric_type', 'where'=>'COALESCE(is_cancel, 0) < 1', 'order_by'=>'sort_index')
		,'color'=>array('table_name'=>'m_color','where'=>"COALESCE(is_cancel, 0) < 1",'order_by'=>'sort_index')
		,'pattern'=>array('table_name'=>'m_other_pattern', 'where'=>'COALESCE(is_cancel, 0) < 1', 'order_by'=>'sort_index')
		,'detail1'=>array('table_name'=>'m_other_detail', 'where'=>'COALESCE(is_cancel, 0) < 1 AND group_id = 1', 'order_by'=>'sort_index')
		,'detail2'=>array('table_name'=>'m_other_detail', 'where'=>'COALESCE(is_cancel, 0) < 1 AND group_id = 2', 'order_by'=>'sort_index')
		,'order_screen'=>array('where'=>array('is_cancel'=>0),'order_by'=>'sort_index')
		,'weave_screen_position'=>array('table_name'=>'m_weave_screen_position','where'=>array("is_cancel"=>0),'order_by'=>'sort_index')
		,'supplier'=>array('table_name'=>'m_order_supplier','where'=>array('is_cancel'=>0),'no_feed_row'=>TRUE,'order_by'=>'sort_index')
	));

	$_details = array();
	hlpr_setController($_details, 'product_type_rowid', 'ประเภทสินค้า', array('type'=>'sel', "sel_options"=>$_arrSelOptions["product_type"], "sel_attr"=>array("product_type"=>"rowid"), "sel_val"=>"rowid", "sel_text"=>"name"));
	hlpr_setController($_details, 'fabric_type', 'ชนิดผ้า', array('type'=>'sel', "sel_options"=>$_arrSelOptions["fabric_type"], "sel_val"=>"name", "sel_text"=>"name", "sel_attr"=>array("product_type"=>"product_type_rowid"), "allow_new"=>TRUE));
	hlpr_setController($_details, 'main_color_rowid', 'สีผ้าหลัก', array('type'=>'sel', "sel_options"=>$_arrSelOptions["color"])); 
	hlpr_setController($_details, 'sub_color1_rowid', 'สีผ้าตัดต่อ', array('type'=>'sel', "sel_options"=>$_arrSelOptions["color"]));
	hlpr_setController($_details, 'color_detail', 'สีเพิ่มเติม', array('type'=>'txa', 'rows'=>1));
	hlpr_setController($_details, 'pattern', 'ทรง', array('type'=>'sel', "sel_options"=>$_arrSelOptions["pattern"], "sel_val"=>"name", "sel_text"=>"name", "sel_attr"=>array("product_type"=>"product_type_rowid"), "allow_new"=>TRUE));
	hlpr_setController($_details, 'detail1', 'ลักษณะพิเศษ', array('type'=>'sel', "sel_options"=>$_arrSelOptions["detail1"], "sel_val"=>"name", "sel_text"=>"name", "sel_attr"=>array("product_type"=>"product_type_rowid"), "allow_new"=>TRUE));
	hlpr_setController($_details, 'detail2', 'รายละเอียด', array('type'=>'sel', "sel_options"=>$_arrSelOptions["detail2"], "sel_val"=>"name", "sel_text"=>"name", "sel_attr"=>array("product_type"=>"product_type_rowid"), "allow_new"=>TRUE));
	hlpr_setController($_details, 'remark1', '', array('type'=>'txa','rows'=>2,'maxlength'=>140));
	hlpr_setController($_details, 'remark2', '', array('type'=>'txa','rows'=>2,'maxlength'=>140));

/*
	// ++ option table join ++
	hlpr_setController($_details, 'option_hem_rowid', 'เพิ่มกุ๊น', array('type'=>'rdo', 'sel_options'=>$_arrSelOptions['option_hem'])); //- new 
	hlpr_setController($_details, 'option_hem_color_rowid', 'สีกุ๊น', array('type'=>'sel', 'sel_options'=>$_arrSelOptions['option_hem_color'])); //- new 
	hlpr_setController($_details, 'option_is_mfl', 'เสื้อผู้ชาย', array('type'=>'chk','class'=>'set-disabled')); //new
	hlpr_setController($_details, 'option_male_fix_length', '', array('type'=>'txt', 'maxlength'=>30)); //new
	hlpr_setController($_details, 'option_is_ffl', 'เสื้อผู้หญิง', array('type'=>'chk','class'=>'set-disabled')); //new
	hlpr_setController($_details, 'option_female_fix_length', '', array('type'=>'txt', 'maxlength'=>30)); //new
	hlpr_setController($_details, 'option_is_no_neck_tag', 'ไม่ติดป้ายคอใดๆทั้งสิ้น', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_customer_size_tag', 'ติดป้ายไซส์ของลูกค้า', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_no_plmk_size_tag', 'ติดป้ายไซส์ ไม่เอา POLOMAKER', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_no_back_clasper', 'ไม่เอาสาบหลัง', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_pakaging_tpb', 'พับแพ็คเสื้อใส่ถุงใส', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_no_packaging_sep_tpb', 'ไม่ต้องพับแพ็ค-แต่ขอถุงเสื้อแยกมา', array('type'=>'chk')); //new
	// -- option table join --
*/
	$_arrDetailsLayout = array(
		array('product_type_rowid', 'fabric_type')
		,array('main_color_rowid', 'sub_color1_rowid', 'color_detail')
		,array('pattern')
		,array('detail1')
		,array('detail2')
		,'รายละเอียดเพิ่มเติม' => array(
			array('remark1')
			,array('remark2')
		)
	);

	$_editFormParams['details_panel'] = $CI->add_view(
		'order/_detail'
		, array(
			'controls' => hlpr_arrGetEditControls($_details)
			, 'layout' => $_arrDetailsLayout
		)
		, TRUE
	);
	
	//++ size_quan panel form parts
	$_editFormParams['size_quan_panel'] = $CI->add_view('order/_size_quan', array("size_quan_matrix"=>"ขนาด/จำนวน"), TRUE);
	//-- size_quan panel form parts

	//++ others_price panel form parts
	$_editFormParams['others_price_panel'] = $CI->add_view('order/_others_price', array(), TRUE);
	//-- size_quan panel form parts

	//++ screen panel form parts
	$_editFormParams['screen_panel'] = $CI->add_view(
		'order/_screen'
		, array(
			'order_screen' => $_arrSelOptions['order_screen']
			,'arr_position_list' => $_arrSelOptions['weave_screen_position']
		)
		, TRUE
	);
		//-- screen panel form parts

	return array(
		"detail_panel" => $CI->add_view('order/_order_tab_detail', $_editFormParams, TRUE)
		, "others_panel" => $CI->add_view('order/_order_tab_others', $_editFormParams, TRUE)
	);
}

function hlpr_get_OrderPremadePolo_ViewParams() {
	$CI = get_instance();
	$CI->load->model('Mdl_order_premade_polo', 'order3');
	$CI->load->model('mdl_polo_pattern', 'p3');
	$_arrPattern = $CI->p3->search();
	array_unshift($_arrPattern, array('rowid'=>'', 'code'=>'', 'color'=>''));

	$_arrSelOptions = hlpr_prepareMasterTableSelectOptions(array(
		'order_screen'=>array('where'=>array('is_cancel'=>0, 'is_polo'=>1))
		,'weave_screen_position'=>array('table_name'=>'m_weave_screen_position','where'=>array("is_polo"=>1),'order_by'=>'sort_index')
	));
	
	$_editFormParams['type_premade_order'] = TRUE;
	$_editFormParams['size_quan_panel'] = '';

	$_editFormParams['details_panel'] = $CI->add_view('order/_detail_premade', array(
		'size_quan_matrix' => $CI->order3->list_size_quan(),
		'pattern_list' => $_arrPattern
	), TRUE);
	
	//++ others_price panel form parts
	$_editFormParams['others_price_panel'] = $CI->add_view('order/_others_price', array(), TRUE);
	//-- others_price panel form parts

	//++ screen panel form parts
	$_editFormParams['screen_panel'] = $CI->add_view('order/_screen', array(
		'order_screen' => $_arrSelOptions['order_screen']
		,'arr_position_list' => $_arrSelOptions['weave_screen_position']
	), TRUE);
	//-- screen panel form parts
	
	return array(
		"detail_panel" => $CI->add_view('order/_order_tab_detail', $_editFormParams, TRUE)
	);
}

function hlpr_get_OrderPremadeTshirt_ViewParams() {
	$CI = get_instance();
	//$CI->load->helper('crud_controller_helper');
	$CI->load->model('Mdl_order_premade_tshirt', 'order4');
	$CI->load->model('Mdl_tshirt_pattern', 'p4');
	$_arrPattern = $CI->p4->search();
	array_unshift($_arrPattern, array('rowid'=>'', 'code'=>'', 'color'=>''));

	$_arrSelOptions = hlpr_prepareMasterTableSelectOptions(array(
		'order_screen'=>array('where'=>array('is_cancel'=>0, 'is_tshirt'=>1))
		,'weave_screen_position'=>array('table_name'=>'m_weave_screen_position','where'=>array("is_tshirt"=>1),'order_by'=>'sort_index')
	));
	
	$_editFormParams['type_premade_order'] = TRUE;
	$_editFormParams['size_quan_panel'] = '';

	$_editFormParams['details_panel'] = $CI->add_view('order/_detail_premade', array(
		'size_quan_matrix' => $CI->order4->list_size_quan()
		, 'pattern_list' => $_arrPattern
	), TRUE);
	
	//++ others_price panel form parts
	$_editFormParams['others_price_panel'] = $CI->add_view('order/_others_price', array(), TRUE);
	//-- others_price panel form parts

	//++ screen panel form parts
	$_editFormParams['screen_panel'] = $CI->add_view('order/_screen', array(
		'order_screen' => $_arrSelOptions['order_screen']
		,'arr_position_list' => $_arrSelOptions['weave_screen_position']
	), TRUE);
	//-- screen panel form parts
	
	return array(
		"detail_panel" => $CI->add_view('order/_order_tab_detail', $_editFormParams, TRUE)
	);
}

function hlpr_get_OrderPremadeCap_ViewParams() {
	$CI = get_instance();
	//$CI->load->helper('crud_controller_helper');
	$CI->load->model('Mdl_order_premade_cap', 'order7');
	$CI->load->model('Mdl_cap_pattern', 'p7');
	$_arrPattern = $CI->p7->search();
	array_unshift($_arrPattern, array('rowid'=>'', 'code'=>'', 'color'=>''));

	$_arrSelOptions = hlpr_prepareMasterTableSelectOptions(array(
		'order_screen'=>array('where'=>array('is_cap'=>1))
		,'weave_screen_position'=>array('table_name'=>'m_weave_screen_position','where'=>array("is_cap"=>1),'order_by'=>'sort_index')
	));
	
	$_editFormParams['type_premade_order'] = TRUE;
	$_editFormParams['size_quan_panel'] = '';
	$_editFormParams['details_panel'] = $CI->add_view('order/_detail_premade_single_size', array('pattern_list' => $_arrPattern), TRUE);
	
	//++ others_price panel form parts
	$_editFormParams['others_price_panel'] = $CI->add_view('order/_others_price', array(), TRUE);
	//-- others_price panel form parts

	//++ screen panel form parts
	$_editFormParams['screen_panel'] = $CI->add_view('order/_screen', array(
		'order_screen' => $_arrSelOptions['order_screen']
		,'arr_position_list' => $_arrSelOptions['weave_screen_position']
	), TRUE);
	//-- screen panel form parts
	return array(
		"detail_panel" => $CI->add_view('order/_order_tab_detail', $_editFormParams, TRUE)
	);
}

function hlpr_get_OrderPremadeJacket_ViewParams() {
	$CI = get_instance();
	//$CI->load->helper('crud_controller_helper');
	$CI->load->model('Mdl_order_premade_jacket', 'order8');
	$CI->load->model('Mdl_jacket_pattern', 'p8');
	$_arrPattern = $CI->p8->search();
	array_unshift($_arrPattern, array('rowid'=>'', 'code'=>'', 'color'=>''));

	$_arrSelOptions = hlpr_prepareMasterTableSelectOptions(array(
		'order_screen'=>array('where'=>array('is_jacket'=>1))
		,'weave_screen_position'=>array('table_name'=>'m_weave_screen_position','where'=>array("is_jacket"=>1),'order_by'=>'sort_index')
	));
	
	$_editFormParams['type_premade_order'] = TRUE;
	$_editFormParams['size_quan_panel'] = '';
	$_editFormParams['details_panel'] = $CI->add_view('order/_detail_premade', array(
		'size_quan_matrix' => $CI->order8->list_size_quan()
		, 'pattern_list' => $_arrPattern
	), TRUE);
	
	//++ others_price panel form parts
	$_editFormParams['others_price_panel'] = $CI->add_view('order/_others_price', array(), TRUE);
	//-- others_price panel form parts

	//++ screen panel form parts
	$_editFormParams['screen_panel'] = $CI->add_view('order/_screen', array(
		'order_screen' => $_arrSelOptions['order_screen']
		,'arr_position_list' => $_arrSelOptions['weave_screen_position']
	), TRUE);
	//-- screen panel form parts
	
	return array(
		"detail_panel" => $CI->add_view('order/_order_tab_detail', $_editFormParams, TRUE)
	);
}

function hlpr_get_OrderPremadeOther_ViewParams() {
	$CI = get_instance();
	$CI->load->model('Mdl_order_premade_other', 'order4');

	$_arrSelOptions = hlpr_prepareMasterTableSelectOptions(array(
		'order_screen'=>array('where'=>array('is_cancel'=>0), 'order_by'=>'sort_index')
		,'weave_screen_position'=>array('table_name'=>'m_weave_screen_position','where'=>array("is_cancel"=>0),'order_by'=>'sort_index')
	));
	$CI->load->model('Mdl_master_table', 'mst');
	$_arrPattern = $CI->mst->list_where('other_premade_pattern', array("is_cancel"=>0), 'sort_index', "m_");
	array_unshift($_arrPattern, array('rowid'=>'', 'product_type_rowid'=>'', 'color'=>'', 'code'=>'', 'name'=>''));
	
	$_editFormParams['type_premade_order'] = TRUE;
	$_editFormParams['size_quan_panel'] = '';
	$_editFormParams['details_panel'] = $CI->add_view('order/_detail_premade_single_size', array('pattern_list' => $_arrPattern), TRUE);
	
	//++ others_price panel form parts
	$_editFormParams['others_price_panel'] = $CI->add_view('order/_others_price', array(), TRUE);
	//-- others_price panel form parts

	//++ screen panel form parts
	$_editFormParams['screen_panel'] = $CI->add_view('order/_screen', array(
		'order_screen' => $_arrSelOptions['order_screen']
		,'arr_position_list' => $_arrSelOptions['weave_screen_position']
	), TRUE);
	//-- screen panel form parts
	
	return array(
		"detail_panel" => $CI->add_view('order/_order_tab_detail', $_editFormParams, TRUE)
	);
}

/*
function hlpr_get_OrderCap_ViewParams() {
	$CI = get_instance();
	$CI->load->helper('crud_controller_helper');
	$_arrSelOptions = hlpr_prepareMasterTableSelectOptions(array(
		'standard_pattern'=>array('where'=>array('is_cancel'=>0,'is_cap'=>1),'order_by'=>'sort_index')
		//,'cap_type'=>array('where'=>array('is_cancel'=>0),'order_by'=>'sort_index')
		,'fabric'=>array('table_name'=>'pm_m_cap_fabric_type','where'=>array('is_cancel'=>0),'order_by'=>'sort_index')
		,'front_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND cap_cols LIKE '%,front,%'",'order_by'=>'sort_index')
		,'back_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND cap_cols LIKE '%,back,%'",'order_by'=>'sort_index')
		,'brim_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND cap_cols LIKE '%,brim,%'",'order_by'=>'sort_index')
		,'button_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND cap_cols LIKE '%,button,%'",'order_by'=>'sort_index')
		,'swr_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND cap_cols LIKE '%,swr,%'",'order_by'=>'sort_index')
		,'afh_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND cap_cols LIKE '%,afh,%'",'order_by'=>'sort_index')
		,'cap_belt_type'=>array('where'=>array('is_cancel'=>0),'order_by'=>'sort_index')
		,'order_screen' => array('where' => array('is_cancel'=>0,'is_cap'=>1),'order_by'=>'sort_index')
		,'weave_screen_position'=>array('table_name'=>'m_weave_screen_position','where'=>array("is_cap"=>1),'order_by'=>'sort_index')
		,'supplier'=>array('table_name'=>'m_order_supplier','where'=>array('is_cancel'=>0),'no_feed_row'=>TRUE,'order_by'=>'sort_index')
	));
	$CI->load->model('Mdl_cap_pattern', 'p5');
	$_temp = $CI->p5->search();
	$_arrSelOptions['cap_pattern'] = array(array('rowid'=>'-1', 'code'=>'- กำหนดเอง -'));
	foreach ($_temp as $_row) {
		$_each = array();
		foreach ($_row as $_key => $_value) {
			if (strpos($_key, 'remark') === 0) {
				$_each['detail_' . $_key] = $_value;
			} else {
				$_each[$_key] = $_value;
			}
		}
		array_push($_arrSelOptions['cap_pattern'], $_each);
	}
	
	$_temp = hlpr_prepareControlsDefault('Mdl_order_detail_cap', $_arrSelOptions);
	$_details = array();
	foreach ($_temp as $_key => $_obj) {
		if (strpos($_key, 'remark') === 0) {
			$_obj['form_edit']['name'] = 'detail_' . $_obj['form_edit']['name'];
			$_details['detail_' . $_key] = $_obj;				
		} else {
			$_details[$_key] = $_obj;
		}
	}
	hlpr_setController($_details, 'order_rowid', '', array('type'=>'hdn'));
	//hlpr_setController($_details, 'cap_type_rowid', '');
	//hlpr_setController($_details, 'cap_type_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>60));
	hlpr_setController($_details, 'standard_pattern_rowid', '');
	hlpr_setController($_details, 'standard_pattern_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>60));
	hlpr_setController($_details, 'fabric_rowid', '');
	hlpr_setController($_details, 'front_color_rowid', 'สีหน้าหมวก');
	hlpr_setController($_details, 'back_color_rowid', 'สีหลังหมวก');
	hlpr_setController($_details, 'brim_color_rowid', 'สีปีกหมวก');
	hlpr_setController($_details, 'button_color_rowid', 'สีกระดุมหมวก');
	hlpr_setController($_details, 'is_sandwich_rim', 'มีกุ๊นขอบแซนวิช', array('type'=>'chk'));
	hlpr_setController($_details, 'swr_color_rowid', 'สีกุ๊นขอบแซนวิช');
	hlpr_setController($_details, 'is_air_flow', 'มีเจาะรูตาไก่', array('type'=>'chk'));
	hlpr_setController($_details, 'air_flow_holes_number', 'จำนวนรู', array('add_class'=>'input-integer'));
	hlpr_setController($_details, 'afh_color_rowid', 'สีรูตาไก่');
	hlpr_setController($_details, 'cap_belt_type_rowid', '');
	hlpr_setController($_details, 'cap_belt_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>60));
	hlpr_setController($_details, 'detail_remark1', '', array('type'=>'txa', 'rows'=>2, 'maxlength'=>140));
	hlpr_setController($_details, 'detail_remark2', '', array('type'=>'txa', 'rows'=>2, 'maxlength'=>140));
	
	$_arrDetailsLayout =  array(
		//"แบบแพทเทิร์น" => array('cap_type_rowid', 'cap_type_detail')
		'แบบแพทเทิร์น' => array('standard_pattern_rowid', 'standard_pattern_detail')
		,'ชนิดผ้าหมวก' => array('fabric_rowid', '')
		,'สีหมวก' => array(
			array('front_color_rowid', '') 
			,array('back_color_rowid', '')
			,array('brim_color_rowid', '')
			,array('button_color_rowid', '')
		),
		'กุ๊นขอบแซนวิช' => array(
			array('is_sandwich_rim', 'swr_color_rowid', '')
		)
		,'รูตาไก่หมวก' => array(
			array('is_air_flow', 'air_flow_holes_number', 'afh_color_rowid')
		)
		,'อะไหล่หมวกด้านหลัง' => array(
			array('cap_belt_type_rowid', 'cap_belt_detail')
		)
		,'รายละเอียดเพิ่มเติม' => array(
			array('detail_remark1')
			,array('detail_remark2')
		)
	);

	$_editFormParams['is_cap_order'] = TRUE;
	$_editFormParams['size_quan_panel'] = '';
	$_editFormParams['details_panel'] = $CI->add_view('order/_detail', array(
			'controls' => hlpr_arrGetEditControls($_details)
			, 'layout' => $_arrDetailsLayout
		), TRUE);
	
	//++ others_price panel form parts
	$_editFormParams['others_price_panel'] = $CI->add_view('order/_others_price', array(), TRUE);
	//-- size_quan panel form parts

	//++ screen panel form parts
	$_editFormParams['screen_panel'] = $CI->add_view('order/_screen', array(
			'order_screen' => $_arrSelOptions['order_screen']
			,'arr_position_list' => $_arrSelOptions['weave_screen_position']
		), TRUE);
	//-- screen panel form parts

	$CI->add_onload_js_file('public/js/order/_cap.js');
	return array(
		"detail_panel" => $CI->add_view('order/_order_tab_detail', $_editFormParams, TRUE)
	);
}

function hlpr_get_OrderJacket_ViewParams() {
	$CI = get_instance();
	$CI->load->helper('crud_controller_helper');
	$_arrSelOptions = hlpr_prepareMasterTableSelectOptions(array(
		'standard_pattern'=>array('where'=>'is_jacket = 1 AND COALESCE(is_cancel, 0) < 1','order_by'=>'sort_index')
		//,'jacket_pattern_type'=>array('where'=>array('is_cancel'=>0),'order_by'=>'sort_index')
		,'lining_type'=>array('where'=>array('is_cancel'=>0),'order_by'=>'sort_index')
		,'fabric'=>array('where'=>array('is_cancel'=>0,'is_jacket'=>1),'order_by'=>'sort_index')
		,'neck_type'=>array('where'=>array('is_cancel'=>0,'is_jacket'=>1),'order_by'=>'sort_index')
		,'m_shape'=>array('table_name'=>'m_base_shape','where'=>array('is_cancel'=>0,'is_jacket'=>1,'is_male'=>1),'order_by'=>'sort_index')
		,'f_shape'=>array('table_name'=>'m_base_shape','where'=>array('is_cancel'=>0,'is_jacket'=>1,'is_female'=>1),'order_by'=>'sort_index')
		,'main_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND jacket_cols LIKE '%,main,%'",'order_by'=>'sort_index')
		,'line_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND jacket_cols LIKE '%,sub,%'",'order_by'=>'sort_index')
		,'sub_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND jacket_cols LIKE '%,line,%'",'order_by'=>'sort_index')
		,'option_hem'=>array('table_prefix'=>'m_','where'=>array('is_cancel'=>0),'order_by'=>'sort_index')
		,'option_hem_color'=>array('table_name'=>'m_color','where'=>"is_cancel = 0 AND jacket_cols LIKE '%,hem,%'",'order_by'=>'sort_index')
		,'clasper_type'=>array('where'=>array('is_cancel'=>0,'is_jacket'=>1),'order_by'=>'sort_index')
		,'clasper_ptrn'=>array('where'=>array('is_cancel'=>0,'is_jacket'=>1),'order_by'=>'sort_index')
		,'collar_type'=>array('where'=>array('is_cancel'=>0,'is_jacket'=>1),'order_by'=>'sort_index')
		,'sleeves_type'=>array('where'=>array('is_cancel'=>0,'is_jacket'=>1),'order_by'=>'sort_index')
		,'flap_type'=>array('where'=>array('is_cancel'=>0,'is_jacket'=>1),'order_by'=>'sort_index')
		,'flap_side_ptrn'=>array('where'=>array('is_cancel'=>0,'is_jacket'=>1),'order_by'=>'sort_index')
		,'pocket_position'=>array('where'=>array('is_cancel'=>0),'order_by'=>'sort_index')
		,'pocket_type'=>array('where'=>array('is_cancel'=>0,'is_jacket'=>1),'order_by'=>'sort_index')
		,'pen_pattern'=>array('where'=>array('is_cancel'=>0,'is_jacket'=>1),'order_by'=>'sort_index')
		,'order_screen'=>array('where'=>array('is_cancel'=>0,'is_jacket'=>1),'order_by'=>'sort_index')
		,'weave_screen_position'=>array('table_name'=>'m_weave_screen_position','where'=>array("is_jacket"=>1),'order_by'=>'sort_index')
		,'supplier'=>array('table_name'=>'m_order_supplier','where'=>array('is_cancel'=>0),'no_feed_row'=>TRUE,'order_by'=>'sort_index')
	));
	$CI->load->model('Mdl_jacket_pattern', 'p6');
	$_temp = $CI->p6->search();
	$_arrSelOptions['standard_pattern'] = array(array('rowid'=>'-1', 'code'=>'- กำหนดเอง -'));
	foreach ($_temp as $_row) {
		$_each = array();
		foreach ($_row as $_key => $_value) {
			if (strpos($_key, 'remark') === 0) {
				$_each['detail_' . $_key] = $_value;				
			} else {
				$_each[$_key] = $_value;
			}
		}
		array_push($_arrSelOptions['standard_pattern'], $_each);
	}

	$_temp = hlpr_prepareControlsDefault('Mdl_order_detail_jacket', $_arrSelOptions);
	$_details = array();
	foreach ($_temp as $_key => $_obj) {
		if (strpos($_key, 'remark') === 0) {
			$_obj['form_edit']['name'] = 'detail_' . $_obj['form_edit']['name'];
			$_details['detail_' . $_key] = $_obj;				
		} else {
			$_details[$_key] = $_obj;
		}
	}
	hlpr_setController($_details, 'order_rowid', '', array('type'=>'hdn'));
	//hlpr_setController($_details, 'jacket_pattern_type_rowid', '');
	hlpr_setController($_details, 'standard_pattern_rowid', '', array("sel_text"=>"code"));
	hlpr_setController($_details, 'fabric_rowid', '');
	hlpr_setController($_details, 'neck_type_rowid', '');
	hlpr_setController($_details, 'neck_type_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140));
	hlpr_setController($_details, 'lining_type_rowid', ''); //- new 
	hlpr_setController($_details, 'lining_type_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140)); //- new
	hlpr_setController($_details, 'm_shape_rowid', 'ทรงเสื้อชาย'); //- new 
	hlpr_setController($_details, 'f_shape_rowid', 'ทรงเสื้อหญิง'); //- new 
	hlpr_setController($_details, 'main_color_rowid', 'สีผ้าหลัก'); //- new 
	hlpr_setController($_details, 'line_color_rowid', 'สีวิ่งเส้น'); //- new 
	hlpr_setController($_details, 'sub_color1_rowid', 'สีรอง1', array('sel_options'=>$_arrSelOptions['sub_color'])); //- new 
	hlpr_setController($_details, 'sub_color2_rowid', 'สีรอง2', array('sel_options'=>$_arrSelOptions['sub_color'])); //- new 
	hlpr_setController($_details, 'sub_color3_rowid', 'สีรอง3', array('sel_options'=>$_arrSelOptions['sub_color'])); //- new 
	hlpr_setController($_details, 'color_detail', '', array('type'=>'txa', 'rows'=>1)); //- new
	hlpr_setController($_details, 'collar_type_rowid', 'รูปแบบปก');
	hlpr_setController($_details, 'collar_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140));
	hlpr_setController($_details, 'collar_detail2', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140));  //- new
	hlpr_setController($_details, 'm_clasper_type_rowid', 'ทรงสาบเสื้อชาย', array('sel_options'=>$_arrSelOptions['clasper_type']));
	hlpr_setController($_details, 'f_clasper_type_rowid', 'ทรงสาบเสื้อหญิง', array('sel_options'=>$_arrSelOptions['clasper_type']));
	hlpr_setController($_details, 'clasper_ptrn_rowid', 'รูปแบบสาบกระดุม');
	hlpr_setController($_details, 'clasper_ptrn_rowid', 'รูปแบบสาบกระดุม');
	hlpr_setController($_details, 'clasper_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>30));
	hlpr_setController($_details, 'clasper_detail2', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140)); //- new
	hlpr_setController($_details, 'm_sleeves_type_rowid', 'แขนเสื้อชาย', array('sel_options'=>$_arrSelOptions['sleeves_type']));
	hlpr_setController($_details, 'f_sleeves_type_rowid', 'แขนเสื้อหญิง', array('sel_options'=>$_arrSelOptions['sleeves_type']));
	hlpr_setController($_details, 'sleeves_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>140));
	hlpr_setController($_details, 'flap_type_rowid', 'รูปแบบชายเสื้อ');
	hlpr_setController($_details, 'flap_type_detail', '', array('type'=>'txt', 'maxlength'=>30));
	hlpr_setController($_details, 'flap_side_ptrn_rowid', '');
	hlpr_setController($_details, 'flap_side_ptrn_detail', '', array('type'=>'txt', 'maxlength'=>30));
	hlpr_setController($_details, 'm_flap_side', 'เสื้อผู้ชาย', array('type'=>'chk'));
	hlpr_setController($_details, 'f_flap_side', 'เสื้อผุ้หญิง', array('type'=>'chk'));
	hlpr_setController($_details, 'pocket_position_rowid', 'ตำแหน่งกระเป๋า');
	hlpr_setController($_details, 'pocket_position_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>60));
	hlpr_setController($_details, 'pocket_type_rowid', 'ทรงกระเป๋า');
	hlpr_setController($_details, 'pocket_type_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>60));
	hlpr_setController($_details, 'm_pocket', 'กระเป๋าเสื้อผู้ชาย', array('type'=>'chk'));
	hlpr_setController($_details, 'f_pocket', 'กระเป๋าเสื้อผู้หญิง', array('type'=>'chk'));
	hlpr_setController($_details, 'pen_pattern_rowid', '');
	hlpr_setController($_details, 'pen_detail', '', array('type'=>'txa', 'rows'=>1, 'maxlength'=>60));
	hlpr_setController($_details, 'm_pen', 'เสื้อผู้ชาย', array('type'=>'chk'));
	hlpr_setController($_details, 'f_pen', 'เสื้อผุ้หญิง', array('type'=>'chk'));
	hlpr_setController($_details, 'is_pen_pos_left', 'แขนซ้าย', array('type'=>'chk')); //- new
	hlpr_setController($_details, 'is_pen_pos_right', 'แขนขวา', array('type'=>'chk')); //- new
	hlpr_setController($_details, 'detail_remark1', '', array('type'=>'txa','rows'=>2,'maxlength'=>140));
	hlpr_setController($_details, 'detail_remark2', '', array('type'=>'txa','rows'=>2,'maxlength'=>140));

	// ++ option table join ++
	hlpr_setController($_details, 'option_hem_rowid', 'เพิ่มกุ๊น', array('type'=>'rdo', 'sel_options'=>$_arrSelOptions['option_hem'])); //- new 
	hlpr_setController($_details, 'option_hem_color_rowid', 'สีกุ๊น', array('type'=>'sel', 'sel_options'=>$_arrSelOptions['option_hem_color'])); //- new 
	hlpr_setController($_details, 'option_is_mfl', 'เสื้อผู้ชาย', array('type'=>'chk','class'=>'set-disabled')); //new
	hlpr_setController($_details, 'option_male_fix_length', '', array('type'=>'txt', 'maxlength'=>30)); //new
	hlpr_setController($_details, 'option_is_ffl', 'เสื้อผู้หญิง', array('type'=>'chk','class'=>'set-disabled')); //new
	hlpr_setController($_details, 'option_female_fix_length', '', array('type'=>'txt', 'maxlength'=>30)); //new
	hlpr_setController($_details, 'option_is_no_neck_tag', 'ไม่ติดป้ายคอใดๆทั้งสิ้น', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_customer_size_tag', 'ติดป้ายไซส์ของลูกค้า', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_no_plmk_size_tag', 'ติดป้ายไซส์ ไม่เอา POLOMAKER', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_no_back_clasper', 'ไม่เอาสาบหลัง', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_pakaging_tpb', 'พับแพ็คเสื้อใส่ถุงใส', array('type'=>'chk')); //new
	hlpr_setController($_details, 'option_is_no_packaging_sep_tpb', 'ไม่ต้องพับแพ็ค-แต่ขอถุงเสื้อแยกมา', array('type'=>'chk')); //new
	// -- option table join --

	$_arrDetailsLayout = array(
		//'แบบแพทเทิร์น' => array('jacket_pattern_type_rowid', '')
		'แบบแพทเทิร์น' => array('standard_pattern_rowid', '')
		,'ชนิดผ้า' => array('fabric_rowid', '')
		,'แบบคอเสื้อ' => array('neck_type_rowid', 'neck_type_detail')
		,'แบบผ้าซับใน' => array('lining_type_rowid', 'lining_type_detail')
		,'แบบทรงเสื้อ' => array('m_shape_rowid', 'f_shape_rowid')
		,'สีเสื้อ' => array(
			array('main_color_rowid', 'line_color_rowid')
			,array('sub_color1_rowid', 'sub_color2_rowid', 'sub_color3_rowid')
			,array('option_hem_rowid', 'option_hem_color_rowid')
			,array('color_detail')
		)
		,'แบบปก' => array(
			array('collar_type_rowid', '')
			,array('collar_detail')
			,array('collar_detail2')
		)
		,'แบบสาบกระดุม' => array(
			array('m_clasper_type_rowid', 'f_clasper_type_rowid')
			,array('clasper_ptrn_rowid', 'clasper_detail')
			,array('clasper_detail2')
		)
		,'แบบแขนเสื้อ' => array(
			array('m_sleeves_type_rowid', 'f_sleeves_type_rowid')
			,array('sleeves_detail')
		)
		,'แบบชายเสื้อ' => array(
			array('flap_type_rowid', 'flap_type_detail')
			,array('return <span class="table-title frm-edit-row-title">กำหนดความยาวเสื้อ</span>', 'option_is_mfl', 'option_male_fix_length')
			,array('', 'option_is_ffl', 'option_female_fix_length')
		)
		,'แบบผ่าข้าง' => array(
			array('flap_side_ptrn_rowid', 'flap_side_ptrn_detail')
			,array('', 'm_flap_side', 'f_flap_side', '')
		)
		,'แบบกระเป๋า' => array(
			array('pocket_position_rowid', 'pocket_position_detail')
			,array('pocket_type_rowid', 'pocket_type_detail')
			,array('', 'm_pocket', 'f_pocket', '')
		)
		,'แบบที่เสียบปากกา' => array(
			array('pen_pattern_rowid', 'pen_detail')
			,array('return <span class="table-title frm-edit-row-title">ที่เสียบปากกา</span>', 'm_pen', 'f_pen', '')
			,array('return <span class="table-title frm-edit-row-title">ตำแหน่งที่เสียบปากกา</span>', 'is_pen_pos_left', 'is_pen_pos_right', '')
		)
		,'ข้อมูลพิเศษอื่นๆ' => array(
			array('option_is_no_neck_tag', 'option_is_customer_size_tag', 'option_is_no_plmk_size_tag')
			,array('option_is_no_back_clasper', 'option_is_pakaging_tpb', 'option_is_no_packaging_sep_tpb')
		)
		,'รายละเอียดเพิ่มเติม' => array(
			array('detail_remark1')
			,array('detail_remark2')
		)
	);

	$_editFormParams['details_panel'] = $CI->add_view('order/_detail', array(
			'controls' => hlpr_arrGetEditControls($_details)
			, 'layout' => $_arrDetailsLayout
		), TRUE);
	
	//++ size_quan panel form parts
	$CI->load->model('Mdl_order_jacket', 'order6');
	$_editFormParams['size_quan_panel'] = $CI->add_view('order/_size_quan', array(
			'size_quan_matrix' => $CI->order6->list_size_quan() // "ขนาดเสื้อสำเร็จรูปแจ็คเก็ต" //
		), TRUE);
	//-- size_quan panel form parts

	//++ others_price panel form parts
	$_editFormParams['others_price_panel'] = $CI->add_view('order/_others_price', array(), TRUE);
	//-- size_quan panel form parts

	//++ screen panel form parts
	$_editFormParams['screen_panel'] = $CI->add_view('order/_screen', array(
			'order_screen' => $_arrSelOptions['order_screen']
			,'arr_position_list' => $_arrSelOptions['weave_screen_position']
		), TRUE);
		//-- screen panel form parts

	return array(
		"detail_panel" => $CI->add_view('order/_order_tab_detail', $_editFormParams, TRUE)
		, "others_panel" => $CI->add_view('order/_order_tab_others', $_editFormParams, TRUE)
	);
}
*/