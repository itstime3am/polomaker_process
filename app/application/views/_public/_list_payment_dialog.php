	<link href="public/css/jquery/ui/timepicker/1.6.1/jquery-ui-timepicker-addon.min.css" rel="stylesheet" type="text/css">
	<script src="public/js/jquery/ui/timepicker/1.6.1/jquery-ui-timepicker-addon.min.js" type="text/javascript"></script>
	<script src="public/js/_public/_obj_payment_panel.js" type="text/javascript"></script>
	<script src="public/js/_public/_list_payment_dialog.js" type="text/javascript"></script>
<?php 
	$_index = (isset($index)) ? $index : 3;
	$_opts = '<option value="">&nbsp;</option>' . "\n";

	$CI = get_instance();
	$CI->load->model('Mdl_master_table', 'mst');
	$_arr = $CI->mst->list_where('order_payment_route', 'is_cancel=0', 'sort_index', 'm_');
	for ($_i=0;$_i<count($_arr);$_i++) {
		$_row = $_arr[$_i];
		$_opts .= '<option value="' . $_row['rowid'] . '">' . $_row['name'] . '</option>' . "\n";
	}
	
	$_attr_dep = '';
	$_attr_pay = '';
	if (isset($access) && is_array($access)) {
		if (isset($access['deposit']['editable']) && ($access['deposit']['editable'] == FALSE)) $_attr_dep .= 'editable="false" ';
		if (isset($access['deposit']['approveable']) && ($access['deposit']['approveable'] == FALSE)) $_attr_dep .= 'approveable="false" ';
		if (isset($access['payment']['editable']) && ($access['payment']['editable'] == FALSE)) $_attr_pay .= 'editable="false" ';
		if (isset($access['payment']['approveable']) && ($access['payment']['approveable'] == FALSE)) $_attr_pay .= 'approveable="false" ';
	}
?>
	<div id="div_payment_dialog" class="cls-div-payment-list" index="<?php echo $_index; ?>">
		<div id="div_payment_tabs" class="cls-tab-container">
			<ul><li><a href="#divDepositPayment">รายการมัดจำ</a></li><li><a href="#divAfterDepositPayment">รายการชำระ</a></li><!--li><a href="#divSummary">สรุปรายการชำระ</a></li--></ul>
			<div id="divDepositPayment">
				<div class="cls-row">
					<ul class="ul-vldr-error-msg" index="<?php echo $_index; ?>"></ul>
				</div>
				<div class="cls-row cls-tbl-container">
					<table class="cls-tbl-payment cls-tbl-deposit" <?php echo isset($_attr_dep) ? $_attr_dep : ''; ?> 
						_CONST='{"payment_type":0}'
						_COMMIT_URL="quotation_payment_log/commit" 
						_DELETE_URL="quotation_payment_log/cancel" 
						_APPROVE_URL="quotation_payment_log/update_approval_status" 
					autofocus >
						<caption>รายการมัดจำ</caption>
						<thead>
							<tr>
								<th>วันที่/เวลา</th>
								<th>ช่องทางการชำระ</th>
								<th>จำนวนเงิน</th>
								<th>รายละเอียด</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr class="tr-edit-panel">
								<td>
									<input type="text" class="user-input input-required input-datetime cls-payment-datetime" data="payment_datetime">
								</td>
								<td>
									<select class="user-input input-required cls-payment-route" placeholder="ช่องทาง" data="payment_route_rowid">
										<?php echo $_opts; ?>
									</select>
								</td>
								<td>
									<input type="text" class="user-input input-required input-format-number input-double cls-payment-amount" data="amount">
								</td>
								<td>
									<textarea class="user-input cls-payment-description" rows="1" data="description"></textarea>
								</td>
								<td class="control-button">
									<img src="public/images/b_edit.png" class="edit-ctrl cls-btn-submit" act="insert" title="Insert" />
									<img src="public/images/details_close.png" class="edit-ctrl cls-btn-cancel" act="cancel" title="Reset" />
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div class="cls-row cls-right">
					<div class="cls-sum-row cls-grand-total">
						<span class="cls-sum-title">รวมทั้งสิ้น</span><span class="cls-spn-count"></span><span class="cls-sum-title">รายการ จำนวนเงินรวม</span><span class="cls-spn-amount"></span><span class="cls-sum-title">บาท</span>
					</div>
				</div>
				<div class="cls-row cls-right">
					<div class="cls-sum-row cls-approved-total">
						<span class="cls-sum-title">ยืนยันแล้ว</span><span class="cls-spn-count"></span><span class="cls-sum-title">รายการ จำนวนเงินรวม</span><span class="cls-spn-amount"></span><span class="cls-sum-title">บาท</span>
					</div>
				</div>
				<div class="cls-row cls-right">
					<div class="cls-sum-row cls-pending-total">
						<span class="cls-sum-title">รอการยืนยัน</span><span class="cls-spn-count"></span><span class="cls-sum-title">รายการ จำนวนเงินรวม</span><span class="cls-spn-amount"></span><span class="cls-sum-title">บาท</span>
					</div>
				</div>
			</div>
			<div id="divAfterDepositPayment">
				<div class="cls-row">
					<ul class="ul-vldr-error-msg" index="<?php echo ($_index + 1); ?>"></ul>
				</div>
				<div class="cls-row cls-tbl-container">
					<table class="cls-tbl-payment cls-tbl-after-deposit" <?php echo isset($_attr_pay) ? $_attr_pay : ''; ?> 
						_CONST='{"payment_type":1}'
						_COMMIT_URL="quotation_payment_log/commit" 
						_DELETE_URL="quotation_payment_log/cancel" 
						_APPROVE_URL="quotation_payment_log/update_approval_status" 
					autofocus >
						<caption>รายการชำระ</caption>
						<thead>
							<tr>
								<th>วันที่/เวลา</th>
								<th>ช่องทางการชำระ</th>
								<th>จำนวนเงิน</th>
								<th>รายละเอียด</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr class="tr-edit-panel">
								<td>
									<input type="text" class="user-input input-required input-datetime cls-payment-datetime" data="payment_datetime">
								</td>
								<td>
									<select class="user-input input-required cls-payment-route" placeholder="ช่องทาง" data="payment_route_rowid">
										<?php echo $_opts; ?>
									</select>
								</td>
								<td>
									<input type="text" class="user-input input-required input-format-number input-double cls-payment-amount" data="amount">
								</td>
								<td>
									<textarea class="user-input cls-payment-description" rows="1" data="description"></textarea>
								</td>
								<td class="control-button">
									<img src="public/images/b_edit.png" class="edit-ctrl cls-btn-submit" act="insert" title="Insert" />
									<img src="public/images/details_close.png" class="edit-ctrl cls-btn-cancel" act="cancel" title="Reset" />
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div class="cls-row cls-right">
					<div class="cls-sum-row cls-grand-total">
						<span class="cls-sum-title">รวมทั้งสิ้น</span><span class="cls-spn-count"></span><span class="cls-sum-title">รายการ จำนวนเงินรวม</span><span class="cls-spn-amount"></span><span class="cls-sum-title">บาท</span>
					</div>
				</div>
				<div class="cls-row cls-right">
					<div class="cls-sum-row cls-approved-total">
						<span class="cls-sum-title">ยืนยันแล้ว</span><span class="cls-spn-count"></span><span class="cls-sum-title">รายการ จำนวนเงินรวม</span><span class="cls-spn-amount"></span><span class="cls-sum-title">บาท</span>
					</div>
				</div>
				<div class="cls-row cls-right">
					<div class="cls-sum-row cls-pending-total">
						<span class="cls-sum-title">รอการยืนยัน</span><span class="cls-spn-count"></span><span class="cls-sum-title">รายการ จำนวนเงินรวม</span><span class="cls-spn-amount"></span><span class="cls-sum-title">บาท</span>
					</div>
				</div>
			</div>
			<!--div id="divSummary">
				<div class="cls-row">
					<div class="cls-sum-row cls-grand-total">
						<span class="cls-sum-title">ยอดรวมทั้งสิ้น</span><span class="cls-spn-amount"></span><span class="cls-sum-title">บาท</span>
					</div>
				</div>
				<div class="cls-row">
					<div class="cls-sum-row cls-grand-total">
						<span class="cls-sum-title">ยอดมัดจำ</span><span class="cls-spn-amount"></span><span class="cls-sum-title">บาท</span>
					</div>
				</div>
				<div class="cls-row">
					<div class="cls-sum-row cls-grand-total">
						<span class="cls-sum-title">ยอดชำระ</span><span class="cls-spn-amount"></span><span class="cls-sum-title">บาท</span>
					</div>
				</div>
			</div-->
		</div>
	</div>
