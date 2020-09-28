<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

function hlpr_displayValue($strToDisplay, $intLimitSize, $blnAllowNewLine = FALSE, $intLimitNewLine = 0, $strElemWidthClass = 'w-all', $intFontSize = 10, $strAddNewRowStyle = '') {
	$_strRemains = '';
	$_ROW_LETTER = 85;
	$_RATIO = 7.5;
	$_str = '';
	if (isset($strToDisplay)) {
		$_str = $strToDisplay;
		if (mb_strlen($strToDisplay) > $intLimitSize) {
			$_str = mb_substr($strToDisplay, 0, $intLimitSize);
			$_strRemains = mb_substr($strToDisplay, $intLimitSize);
		}
	}
	echo <<<ROW
			<div class="pdf-value $strElemWidthClass" style="font-size:{$intFontSize}px;">$_str</div>

ROW;
	if ($blnAllowNewLine) {
		$_intLineCount = 0;
		if ($intFontSize != 10) {
			$_ROW_LETTER = $_ROW_LETTER + floor((10 - $intFontSize) * $_RATIO);
		}
		while (($_strRemains != '') && (($intLimitNewLine == 0) || ($intLimitNewLine > $_intLineCount))) {
			$_str = $_strRemains;
			if (mb_strlen($_strRemains) > $_ROW_LETTER) {
				$_str = mb_substr($_strRemains, 0, $_ROW_LETTER);
				$_strRemains = mb_substr($_strRemains, $_ROW_LETTER);
			} else {
				$_strRemains = '';					
			}
			echo <<<ROW
		</div>
		<div class="pdf-row">
			<div class="ml-0_2 pdf-value w-all" style="font-size:{$intFontSize}px;$strAddNewRowStyle">$_str</div>

ROW;
			$_intLineCount ++;
		}
	}	
}
/*echo "Original: " . $strToDisplay. PHP_EOL . "<br>";
echo "limit: " . $intLimitSize . PHP_EOL . "<br>";
print_r(mb_strpos($strToDisplay, "\\n")). PHP_EOL . "<br>";
echo "strlen: ". strlen($strToDisplay). PHP_EOL . "<br>";
echo "mb_strlen: ". mb_strlen($strToDisplay). PHP_EOL . "<br>";
echo "encoding: ".mb_detect_encoding($strToDisplay, "auto");exit;
*/

function hlpr_displayTitle($strToDisplay, $intLimitSize, $blnAllowNewLine = FALSE, $intLimitNewLine = 0, $strElemWidthClass = 'w-all', $intFontSize = 10, $blnReturn = FALSE) {
	$_strToReturn = '';
	$_strRemains = '';
	$_str = '';
	if (isset($strToDisplay)) {
		$_str = $strToDisplay;
		$_intNLPos = mb_strpos($_str, "\\n");
		if ($_intNLPos !== FALSE) {
			$_strRemains = mb_substr($_str, ($_intNLPos + mb_strlen("\\n")));
			$_str = mb_substr($_str, 0, $_intNLPos);
		}
		if (mb_strlen($_str) > $intLimitSize) {
			$_strRemains = mb_substr($_str, $intLimitSize) . "\\n" . $_strRemains;
			$_str = mb_substr($_str, 0, $intLimitSize);
		}
	}
	$_strToReturn .= <<<ROW
			<div class="pdf-title w-all $strElemWidthClass" style="font-size:{$intFontSize}px;">$_str</div>

ROW;
	if ($blnAllowNewLine) {
		$_intLineCount = 0;
		while (($_strRemains != '') && (($intLimitNewLine == 0) || ($intLimitNewLine > $_intLineCount))) {
			$_str = $_strRemains;
			$_strRemains = '';
			$_intNLPos = mb_strpos($_str, "\\n");
			if ($_intNLPos !== FALSE) {
				$_strRemains = mb_substr($_str, ($_intNLPos + mb_strlen("\\n")));
				$_str = mb_substr($_str, 0, $_intNLPos);
			}
			if (mb_strlen($_str) > $intLimitSize) {
				$_strRemains = trim(mb_substr($_str, $intLimitSize) . "\\n" . $_strRemains);
				$_str = mb_substr($_str, 0, $intLimitSize);
			}
			$_strToReturn .= <<<ROW
		</div>
		<div class="line-row">
			<div class="pdf-title w-all" style="font-size:{$intFontSize}px;">$_str</div>

ROW;
			$_intLineCount ++;
		}
	}
	if ($blnReturn == TRUE) {
		return $_strToReturn;
	} else {
		echo $_strToReturn;
	}
}

function strThaiBaht($dblValue) {
	$_arrNumText = array("ศูนย์", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า", "สิบ");
	$_arrDigitText = array("", "สิบ", "ร้อย", "พัน", "หมื่น", "แสน", "ล้าน");
	$_blnIsMinus = FALSE;
	$_return = "";
	if (! is_numeric($dblValue)) {
		return "-ERR_NUMBER_PARAM-";
	} else {
		$_strNumber = '';
		$_strDigit = '';
		$_strdummy = '';
		if ($dblValue < 0) $_blnIsMinus = TRUE;
		$_strValue = (string) abs($dblValue);
		if (strpos($_strValue, ".") !== FALSE) {
			list($_strNumber, $_strDigit) = explode(".", $_strValue);
		} else {
			$_strNumber = $_strValue;
		}
		if (is_numeric($_strDigit)) {
			if (strlen($_strDigit) >= 2) {
				$_strDigit = substr($_strDigit, 0, 2);
			} else {
				$_strDigit = $_strDigit . '0';
			}
		} else {
			$_strDigit = "";
		}
	
		$_strdummy = $_strNumber;
		while (strlen($_strdummy) > 0) {
			$_strmilset = '';
			$_strTempSet = '';
			if (strlen($_strdummy) > 7) {
				$_strmilset = substr($_strdummy, -6);
				$_strdummy = substr($_strdummy, 0, strlen($_strdummy) - 6);
			} else {
				$_strmilset = $_strdummy;
				$_strdummy = '';
			}
			$_intLength = strlen($_strmilset);
			for ($_i = 0;$_i < $_intLength; $_i++) {
				$_tmp = substr($_strmilset, $_i, 1);
				if ($_tmp != 0) {
					if (($_i == ($_intLength - 1)) && ($_tmp == 1)) {
						$_strTempSet .= "เอ็ด";
					} elseif (($_i == ($_intLength - 2)) && ($_tmp == 2)) {
						$_strTempSet .= "ยี่";
					} elseif (($_i == ($_intLength - 2)) && ($_tmp == 1)) {
						$_strTempSet .= "";
					} else {
						$_strTempSet .= $_arrNumText[$_tmp];
					}
					$_strTempSet .= $_arrDigitText[$_intLength - $_i - 1];
				}
			}
			if (strlen($_strdummy) > 0) {
				$_return = 'ล้าน' . $_strTempSet . $_return;
			} else {
				$_return = $_strTempSet . $_return;
			}
		}
		$_return .= "บาท";
		if (($_strDigit == "") || ((int)$_strDigit == 0)) {
			$_return .= "ถ้วน";
		} else {
			$_intLength = strlen($_strDigit);
			for ($_i = 0; $_i < $_intLength; $_i++) {
				$_tmp = substr($_strDigit, $_i, 1);
				if ($_tmp != 0) {
					if (($_i == ($_intLength - 1)) && ($_tmp == 1)) {
						$_return .= "เอ็ด";
					} elseif (($_i == ($_intLength - 2)) && ($_tmp == 2)) {
						$_return .= "ยี่";
					} elseif (($_i == ($_intLength - 2)) && ($_tmp == 1)) {
						$_return .= "";
					} else {
						$_return .= $_arrNumText[$_tmp];
					}
					$_return .= $_arrDigitText[$_intLength - $_i - 1];
				}
			}
			$_return .= "สตางค์";
		}
		if ($_blnIsMinus == TRUE) $_return = 'ลบ' . $_return;
		return $_return;
	}
}
/* End of file exp_pdf_helper.php */ 
/* Location: ./application/helpers/exp_pdf_helper.php */ 