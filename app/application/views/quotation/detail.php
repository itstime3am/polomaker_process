<?php
	$_index = isset($index) ? $index : 1;
?>
<div id="divInfo" class="cls-div-info" index="<?php echo $_index; ?>"></div>
<ul class="ul-vldr-error-msg" index="<?php echo $_index; ?>"></ul>
<form id="frm_edit_quotation_detail" controller="quotation_detail" class="cls-frm-edit" index="<?php echo $_index; ?>">
	<div id="divDetailTabs" class="cls-tab-container">
		<ul>
			<li><a href="#divMain">ข้อมูลหลัก</a></li>
			<li id="tabMnuDetail" class="hidden"><a href="#divDetail">รายละเอียด</a></li>
			<li id="tabMnuOthers" class="hidden"><a href="#divOthers">อื่นๆ</a></li>
			<li><a href="#divImages">รูปประกอบ</a></li>
		</ul>
		<div id="divMain">
			<input type="hidden" id="hdn-rowid" value="" class="user-input data-container" data="rowid">
			<input type="hidden" id="hdn-quotation_rowid" value="-1" class="user-input" data="quotation_rowid">
			<table id="tbl_edit" class="rounded-corner cls-tbl-edit" autofocus > <!-- to prevent focus on first input that cause problem when have scroll bar (back to top after blur lower elements) -->
			<thead>
				<tr>
					<th class="rounded-top-left" style="width:30%"></th>
					<th style="width:30%">&nbsp;</th>
					<th class="rounded-top-right" style="width:40%"></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
				<td colspan="2" class="rounded-foot-left"></td>
				<td class="rounded-foot-right">&nbsp;</td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td class="table-title">หัวข้อ :</td>
					<td class="table-value">
						<select id="sel-title_rowid" class="user-input" data="title_rowid">
<?php
						if (isset($listDetailTitle)) {
							foreach ($listDetailTitle as $row) {
								echo '<option value="' . $row['rowid'] . '" order_type_id="' . (isset($row['order_type_id']) ? $row['order_type_id'] : '') . '">' . $row['name'] . '</option>' . "\n";
							}
						}
?>
						</select>
						<!-- input type="hidden" id="hdn-title" class="user-input" data="title" /-->
					</td>
					<td></td>
				</tr>
				<tr>
					<td class="table-title">คำอธิบาย :</td>
					<td class="table-value">
						<textarea id="txa-description" class="user-input" rows="3" maxlength="500" data="description"></textarea>
					</td>
					<td></td>
				</tr>
				<tr>
					<td class="table-title">จำนวน :</td>
					<td class="table-value">
						<input type="text" id="txt-qty" value="" class="user-input input-format-integer input-integer" data="qty">
					</td>
					<td></td>
				</tr>
				<!--tr>
					<td class="table-title">ราคาต่อหน่วย :</td>
					<td class="table-value">
						<input type="text" id="txt-price" value="" class="user-input input-double" data="price">
					</td>
					<td></td>
				</tr-->
				<tr>
					<td class="table-title">ราคารวม :</td>
					<td class="table-value">
						<input type="text" id="txt-amount" value="" class="user-input input-format-number input-double" data="amount">
					</td>
					<td></td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="frm-edit-row-group" id="div_PO_props" style="display:none;">
							<span class="group-title">ส่วนใบงานผลิต</span>
							<div class="frm-edit-row cls-row">
								<div class="table-title frm-edit-row-title cls-row-title" style="width:20%;">วันที่สั่งงาน</div>
								<div class="table-value frm-edit-row-value cls-row-value" style="width:25%;">
									<input type="text" id="txt-po_order_date" class="set-disabled user-input data-container" readonly data="po_order_date">
								</div>
								<div class="table-title frm-edit-row-title cls-row-title" style="width:20%;">วันที่ครบกำหนด</div>
								<div class="table-value frm-edit-row-value cls-row-value" style="width:25%;">
									<input type="text" id="txt-po_due_date" class="user-input data-container" data="po_due_date">
								</div>
							</div>
							<div class="frm-edit-row">
								<div class="table-title frm-edit-row-title cls-row-title" style="width:20%;">วันที่ส่งลูกค้า</div>
								<div class="table-value frm-edit-row-value cls-row-value" style="width:25%;">
									<input type="text" id="txt-po_deliver_date" class="user-input data-container" data="po_deliver_date">
								</div>
								<div class="table-title frm-edit-row-title cls-row-title" style="width:20%;">สั่งจาก</div>
								<div class="table-value frm-edit-row-value cls-row-value" style="">
<?php
						if (isset($listSupplier)) {
							foreach ($listSupplier as $_row) {
								echo <<<RDO
							<input type="radio" name="po_supplier_rowid" id="rdo-po_supplier_{$_row['name_en']}" value="{$_row['rowid']}" class="user-input" data="po_supplier_rowid" />
							<label for="rdo-po_supplier_{$_row['name_en']}">{$_row['name']}</label>

RDO;
							}
						}
?>
								</div>
							</div>
							<!--div class="frm-edit-row cls-row">
								<div class="table-title frm-edit-row-title cls-row-title" style="width:20%;">หมายเหตุ</div>
								<div class="table-value frm-edit-row-value cls-row-value" style="width:70%;">
									<textarea id="txa-po_remark" class="user-input data-container" rows="3" data="po_remark"></textarea>
								</div>
							</div-->
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="frm-edit-row-group" id="div_PO_remarks" style="display:none;width:50%;margin:0 auto;border:0px;">
							<span class="table-title">หมายเหตุ</span>
							<div class="frm-edit-row cls-row">
								<textarea id="txa-remark1" class="user-input" rows="3" maxlength="500" data="remark1"></textarea>
							</div>
							<div class="frm-edit-row">
								<textarea id="txa-remark2" class="user-input" rows="3" maxlength="500" data="remark2"></textarea>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="3">
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<div id="divDetail">
			<div id="divPoloDetailPanel" class="cls-detail-panel hidden">
<?php echo (isset($polo_panel['detail_panel'])) ? $polo_panel['detail_panel'] : ''; ?>
			</div>
			<div id="divTshirtDetailPanel" class="cls-detail-panel hidden">
<?php echo (isset($tshirt_panel['detail_panel'])) ? $tshirt_panel['detail_panel'] : ''; ?>
			</div>
			<div id="divOtherDetailPanel" class="cls-detail-panel hidden">
<?php echo (isset($other_panel['detail_panel'])) ? $other_panel['detail_panel'] : ''; ?>
			</div>
			<div id="divPremadePoloDetailPanel" class="cls-detail-panel hidden">
<?php echo (isset($premade_polo_panel['detail_panel'])) ? $premade_polo_panel['detail_panel'] : ''; ?>
			</div>
			<div id="divPremadeTshirtDetailPanel" class="cls-detail-panel hidden">
<?php echo (isset($premade_tshirt_panel['detail_panel'])) ? $premade_tshirt_panel['detail_panel'] : ''; ?>
			</div>
			<div id="divPremadeCapDetailPanel" class="cls-detail-panel hidden">
<?php echo (isset($premade_cap_panel['detail_panel'])) ? $premade_cap_panel['detail_panel'] : ''; ?>
			</div>
			<div id="divPremadeJacketDetailPanel" class="cls-detail-panel hidden">
<?php echo (isset($premade_jacket_panel['detail_panel'])) ? $premade_jacket_panel['detail_panel'] : ''; ?>
			</div>
			<div id="divPremadeOtherDetailPanel" class="cls-detail-panel hidden">
<?php echo (isset($premade_other_panel['detail_panel'])) ? $premade_other_panel['detail_panel'] : ''; ?>
			</div>
			<!--div id="divCapDetailPanel" class="cls-detail-panel hidden">
<?php //echo (isset($cap_panel['detail_panel'])) ? $cap_panel['detail_panel'] : ''; ?>
			</div>
			<div id="divJacketDetailPanel" class="cls-detail-panel hidden">
<?php //echo (isset($jacket_panel['detail_panel'])) ? $jacket_panel['detail_panel'] : ''; ?>
			</div-->
		</div>
		<div id="divOthers">
			<div id="divPoloOthersPanel" class="cls-others-panel hidden">
<?php echo (isset($polo_panel['others_panel'])) ? $polo_panel['others_panel'] : ''; ?>
			</div>
			<div id="divTshirtOthersPanel" class="cls-others-panel hidden">
<?php echo (isset($tshirt_panel['others_panel'])) ? $tshirt_panel['others_panel'] : ''; ?>
			</div>
			<div id="divOtherOthersPanel" class="cls-others-panel hidden">
<?php echo (isset($other_panel['others_panel'])) ? $other_panel['others_panel'] : ''; ?>
			</div>
			<!--div id="divJacketOthersPanel" class="cls-others-panel hidden">
<?php //echo (isset($jacket_panel['others_panel'])) ? $jacket_panel['others_panel'] : ''; ?>
			</div-->
		</div>
		<div id="divImages">
			<input type="hidden" id="hdn-old_file_image1" class="user-input" />
			<input type="hidden" id="hdn-old_file_image2" class="user-input" />
			<input type="hidden" id="hdn-old_file_image3" class="user-input" />
			<input type="hidden" id="hdn-old_file_image4" class="user-input" />
			<input type="hidden" id="hdn-old_file_image5" class="user-input" />
			<input type="hidden" id="hdn-old_file_image6" class="user-input" />
			<input type="hidden" id="hdn-old_file_image7" class="user-input" />
			<input type="hidden" id="hdn-old_file_image8" class="user-input" />
			<input type="hidden" id="hdn-old_file_image9" class="user-input" />
			<table class="rounded-corner cls-tbl-edit">
				<tbody>
				<tr>
					<td colspan="3" class="td-align-center">
						<div class="form-edit-elem-container">
							<div class="frm-edit-row-group" >
								<div class="frm-edit-row" >
									<div class="progress">
										<div class="bar"></div >
										<div class="percent">0%</div >
									</div>
								</div>
								<div class="frm-edit-row" style="display:inline-block;" >
									<div role="img" aria-label="" class="frm-edit-row-value div-disp-img-upload display-upload disp-upload-main" id="div_fmg_file_image1" >
										<div class="input-controller fmg-controller eventView-hide" title="remove image, prevent add/edit.">
											<input type="checkbox" class="fmg-no-image input-controller no-commit no-validate"><label>remove</label>
										</div>
										<span class="spn-image-select eventView-hide">
											เพิ่ม/แก้ไขรูปภาพประกอบ 1
											<input type="file" id="fmg-file_image1" name="image" class="user-input input-file-upload" data="file_image1">
										</span>
										<input type="hidden" id="hdn-file_image1" class="user-input fmg-value" />
									</div>
								</div>
<?php $_currIndex = 1; ?>
<?php for($_r=0;$_r<2;$_r++): ?>
								<div class="frm-edit-row" style="display:inline-block;" >
<?php 	for($_c=1;$_c<=4;$_c++): ?>
<?php		$_id = $_currIndex + (($_r * 4) + $_c); ?>
									<div role="img" aria-label="" class="frm-edit-row-value div-disp-img-upload display-upload disp-upload-sub" id="div_fmg_file_image<?php echo $_id; ?>" >
										<div class="input-controller fmg-controller eventView-hide" title="remove image, prevent add/edit.">
											<input type="checkbox" class="fmg-no-image input-controller no-commit no-validate"><label>remove</label>
										</div>
										<span class="spn-image-select eventView-hide">
											เพิ่ม/แก้ไขรูปภาพประกอบ <?php echo $_id; ?>
											<input type="file" id="fmg-file_image<?php echo $_id; ?>" name="image" class="user-input input-file-upload" data="file_image<?php echo $_id; ?>">
										</span>
										<input type="hidden" id="hdn-file_image<?php echo $_id; ?>" class="user-input fmg-value" />
									</div>
<?php 	endfor; ?>
								</div>
<?php endfor; ?>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="td-align-center"></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="cls-button-container">
		<input type="button" id="btnFormSubmit" class="cls-btn-form-submit" value="บันทึก"/>
		<input type="button" id="btnFormReset" class="cls-btn-form-reset" value="ค่าเริ่มต้น" />
		<input type="button" id="btnFormCancel"  class="cls-btn-form-cancel"value="ยกเลิก"/>
	</div>
</form>
<form id="frm_upload_image" action="upload_temp_image" method="post" enctype="multipart/form-data">
	<input type="hidden" id="element_id" name="element_id" >
</form>
<br style="clear:both">
