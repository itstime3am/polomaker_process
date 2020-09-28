<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mpdf8 { 
	function mpdf8() {
        $CI = & get_instance();
		$this->_pdf = null;
		log_message('Debug', 'mPDF class is loaded.');
	}

	function __init($defaultFontSize = 10, $defaultFontName = 'sarabun') { //$defaultFontSize = 14, $defaultFontName = 'sarabun'
		ini_set("memory_limit", "512M");
		require_once APPPATH . '/third_party/mPDF8/autoload.php';
		ob_end_clean();
		$this->_pdf = new \Mpdf\Mpdf([
			"mode" => 'th'
			, 'margin_left' => 5
			, 'margin_right' => 2
			, 'margin_top' => 5
			, 'margin_bottom' => 2
			, 'margin_header' => 0
			, 'margin_footer' => 0
			, 'default_font_size' => $defaultFontSize
			, 'default_font' => $defaultFontName
		]);
		$this->_pdf->SetDisplayMode('fullpage');

		return $this->_pdf;
	}
	function _addPage($htmlView, $m_left = 10, $m_right = 5, $m_top = 5, $m_bottom = 5, $m_header = 0, $m_footer = 0) {
		try {
			if ($this->_pdf === null) {
				$this->__init();
			} else {
				$this->_pdf->AddPage();
			}
			$this->_pdf->WriteHTML($htmlView);
			//echo $htmlView;
		} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
			echo $e->getMessage();
		}
	}
	
	function _export($fileName, $type = 'I') {
		try {
			if (isset($this->_pdf)) {
				/*++ disable page num if page = 1 */
				/*
				if (count($this->_pdf->pages) == 1) {
					$this->_pdf->pagenumPrefix = '';
					$this->_pdf->pagenumSuffix = '';
					$this->_pdf->PageNumSubstitutions[] = array('from'=>1, 'suppress'=>'on');
				} else {
					$this->_pdf->pagenumPrefix = '( หน้าที่ ';
					$this->_pdf->pagenumSuffix = ' )';			
				}
				*/
				/*-- disable page num if page = 1 */
				$this->_pdf->Output($fileName, $type);
			}
		} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
			echo $e->getMessage();
		}
	}

	function exportMPDF($htmView, $fileName = 'mpdf8.pdf') {
		$mpdf = $this->__init();

		$mpdf->WriteHTML($htmView);
		$mpdf->Output($fileName, 'I');
	}
	function exportMPDF_withWaterMark($htmView, $fileName = 'mpdf8.pdf', $waterMarkText = "SAMPLE") {
		$mpdf = $this->__init();

		$mpdf->SetWatermarkText($waterMarkText);
		$mpdf->showWatermarkText = true;

		$mpdf->WriteHTML($htmView);
		$mpdf->Output($fileName, 'I');
	}

	function exportMPDF_Template($htmView, $tmplFile, $fileName = 'mpdf.pdf', $defaultFontSize = 10, $defaultFontName = 'sarabun') {
		$mpdf = $this->__init($defaultFontSize, $defaultFontName);
		$mpdf->SetDocTemplate(FCPATH . '/public/mPDF/templates/' . $tmplFile . '.pdf', TRUE);
		$mpdf->SetDisplayMode('fullpage');

		$mpdf->WriteHTML($htmView);
		$mpdf->Output($fileName, 'I');
	}
	function exportMPDF_Template_withWaterMark($htmView, $tmplFile, $fileName = 'mpdf.pdf', $waterMarkText = "SAMPLE", $defaultFontSize = 10, $defaultFontName = 'sarabun') {
		$mpdf = $this->__init($defaultFontSize, $defaultFontName);
		$mpdf->SetDocTemplate(FCPATH . '/public/mPDF/templates/' . $tmplFile . '.pdf', TRUE);
		$mpdf->SetDisplayMode('fullpage');

		//$mpdf->SetWatermarkImage('../images/background.jpg');
		$mpdf->SetWatermarkText($waterMarkText);
		$mpdf->showWatermarkText = true;

		$mpdf->WriteHTML($htmView);
		$mpdf->Output($fileName, 'I');
	}
}