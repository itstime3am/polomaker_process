<html>
<head>
	<title><?php echo (isset($title)) ? $title : 'quotation' ?></title>
	<style>
		.cls-details-container {}
			.cls-detail-row {}
				.cls-dtl-col {vertical-align:top;}
		
	</style>
</head>
<body>
<?php
	if (isset($data)) {
		$_sectionHead =<<<HTML
<div style="position:absolute;top:57px;left:30px;font-weight:bold;width:500px;">{$data['disp_header_title']}</div>
<div style="position:absolute;top:73px;left:30px;width:500px;">{$data['disp_header_address']}</div>
<div style="position:absolute;top:40px;left:610x">{$data['qo_number']}</div>
<div style="position:absolute;top:57px;left:610px">{$data['disp_start_date']}</div>
<div style="position:absolute;top:73px;left:610px">{$data['create_user']}</div>
<div style="position:absolute;top:104px;left:575px;width:2em;">{$data['disp_day_limit']}</div>
<div style="position:absolute;top:104px;left:680px">{$data['disp_end_date']}</div>
<div style="position:absolute;top:121px;left:630px">{$data['payment_condition']}</div>
<div style="position:absolute;top:160px;left:525px;">
	<table style="width:95%">
		<tr>
			<td style="line-height:1.4em";>{$data['disp_pdf_bank_account']}</td>
		</tr>
	</table>
</div>
<div style="position:absolute;top:199px;left:70px;">
	<table style="width:80%">
		<tr>
			<td style="line-height:0.9em";>{$data['customer_address']}</td>
		</tr>
	</table>
</div>
<div style="position:absolute;top:140px;left:80px">{$data['customer']}</div>
<div style="position:absolute;top:154px;left:80px">{$data['customer_company']}</div>
<div style="position:absolute;top:170px;left:80px">{$data['customer_contact_number']}</div>
<div style="position:absolute;top:183px;left:80px">{$data['customer_email']}</div>
<div style="position:absolute;top:232px;left:120px">{$data['customer_tax_id']}</div>
<div style="position:absolute;top:232px;left:320px">{$data['customer_tax_branch']}</div>
<div class="cls-details-container" style="position:absolute;display:block;top:310px;left:25px;width:auto;margin:0px;padding:0px;">
	<table style="width:720px;">
HTML;

		$_dispIsVAT = '';
		if (isset($data['is_vat']) && ($data['is_vat'] > 0)) {
			$_dispIsVAT =<<<ISVAT
<div style="position:absolute;top:985px;left:535px;width:10em;text-align:right;">ภาษีมูลค่าเพิ่ม 7%</div>
<div style="position:absolute;top:985px;left:680px;width:85px;text-align:right;font-weight:bold;">
	{$data['disp_sum_vat']}
</div>

ISVAT;
		}
		$_dblVal = (isset($data['sum_amount']) && is_numeric($data['sum_amount'])) ? (float)$data['sum_amount']: 0;
		$_strDisplayThaiBaht = strThaiBaht($_dblVal);
		$_remark = "- สินค้าสั่งตัดมัดจำ 50% อีก 50% ชำระวันรับสินค้า/พร้อมส่งมอบสินค้า<br>- ในกรณีสินค้าในสต็อคชำระ 100% ก่อนส่งมอบสินค้า ราคานี้ไม่รวมค่าขนส่ง";
		if (isset($data['is_vat']) && ($data['is_vat'] == 1) && isset($data['is_disp_notice']) && ($data['is_disp_notice'] == 1)) {
			$_remark .= "<br>- บริษัทเป็นผู้ผลิตไม่สามารถหัก ณ ที่จ่ายได้";
			$_tag = 'ทางบริษัทฯ หวังเป็นอย่างยิ่งว่าจะได้ให้บริการท่านในเร็ววันนี้';
		} else {
			//$_remark .= "<br>";
			$_tag = 'ทางร้านหวังเป็นอย่างยิ่งว่าจะได้ให้บริการท่านในเร็ววันนี้';
		}
		$_remark .= "<br>- ทางบริษัทไม่อนุญาตให้หักค่าธรรมเนียมการโอนเงิน";
		$_sectionBottom =<<<HTML
	</table>
</div>
<div style="position:absolute;top:922px;left:680px;width:85px;text-align:right;font-weight:bold;">{$data['disp_sum_net']}</div>
<div style="position:absolute;top:945px;left:625px;font-size:12px;width:3.5em;text-align:right;font-weight:bold;">
	{$data['disp_percent_discount']}
</div>
<div style="position:absolute;top:943px;left:680px;width:85px;text-align:right;font-weight:bold;">
	{$data['disp_sum_discount']}
</div>
<div style="position:absolute;top:964px;left:680px;width:85px;text-align:right;font-weight:bold;">
	{$data['disp_sum_after_discount']}
</div>
{$_dispIsVAT}
<div style="position:absolute;top:1006px;left:680px;width:85px;text-align:right;font-weight:bold;">
	{$data['disp_sum_amount']}
</div>
<div style="position:absolute;top:935px;left:30px;line-height:1;">{$_remark}</div>
<div style="position:absolute;top:1078px;left:420px;width:320px;text-align:center;font-weight:bold;font-size:8pt;">{$_tag}</div>

HTML;

		if (array_key_exists('details', $data) && is_array($data['details'])) {
			$arrDetails = $data['details'];
			$_num = 0;
			$_dispNum = '';
			$_qty;
			$_price;
			$_amount;
			$_strDtls = '';
			for($_i=0;$_i<count($arrDetails);$_i++) {
				$_ea = $arrDetails[($_i)];
				if (isset($_ea['sub_seq']) && ($_ea['sub_seq'] == 0)) {
					$_num = $_num + 1;
					$_dispNum = $_num . '.';
				} else {
					$_dispNum = '';
				}
				$_desc = trim($_ea['title']); // . ': ' . $_ea['description']
				$_qty = $_ea['qty'];
				$_price = $_ea['disp_price'];
				$_amount = $_ea['disp_amount'];

				$_strDtls .= <<<ROW
		<tr class="cls-detail-row">
			<td class="cls-dtl-col cls-dtl-col-indx" style="width:50px;text-align:center;">{$_dispNum}</td>
			<td class="cls-dtl-col cls-dtl-col-desc" style="width:445px;">{$_desc}</td>
			<td class="cls-dtl-col cls-dtl-col-qty" style="width:80px;text-align:center;">{$_qty}</td>
			<td class="cls-dtl-col cls-dtl-col-price" style="width:70px;text-align:right;">{$_price}</td>
			<td class="cls-dtl-col cls-dtl-col-amount" style="width:95px;text-align:right;">{$_amount}</td>
		</tr>

ROW;
				if (($_i > 15) && (($_i % 16) == 0)) {
					echo $_sectionHead . $_strDtls . $_sectionBottom . "\n<pagebreak>\n";
					$_strDtls = '';
				}
			}
			if (strlen(trim($_strDtls)) > 0) {
				echo $_sectionHead . $_strDtls . $_sectionBottom;
			}
		}
	}
?>
</body>
</html>
