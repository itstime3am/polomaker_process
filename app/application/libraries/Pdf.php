<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

define('_MPDF_URI', base_url() . 'public/mPDF/'); // must be  a relative or absolute URI - not a file system path

class Pdf { 
    function Pdf() {
		include_once APPPATH . '/third_party/mPDF/mpdf.php';
		ob_end_clean();

        $CI = & get_instance();
		$this->_pdf = null;
        log_message('Debug', 'mPDF class is loaded.');
    }

 	function exportMPDF($htmView, $fileName = 'mpdf.pdf', $m_left = 5, $m_right = 2, $m_top = 5, $m_bottom = 5, $m_header = 0, $m_footer = 0) {
		ini_set("memory_limit", "512M");
        include_once APPPATH . '/third_party/mPDF/mpdf.php';

		ob_end_clean();

		$mpdf = new mPDF('th', 'A4', 0, '', $m_left, $m_right, $m_top, $m_bottom, $m_header, $m_footer);
		//$mpdf->setAutoTopMargin = 'stretch';
		//$mpdf->autoMarginPadding = 0; //pad between header and top content, default = 2 (mm)
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetAutoFont();

		$mpdf->progbar_heading = 'Generating report progress';
		$mpdf->StartProgressBarOutput(2);

		//$mpdf->mirrorMargins = 1;
		//$mpdf->list_number_suffix = ')';
		//$mpdf->hyphenate = true;
		
		//$mpdf->debug = true;
		//$mpdf->showImageErrors = true;
		//$mpdf->allow_output_buffering = true;
		
		$mpdf->WriteHTML($htmView);
		
		/*++ disable page num if page = 1 */
		if (count($mpdf->pages) == 1) {
			$mpdf->pagenumPrefix = '';
			$mpdf->pagenumSuffix = '';
			$mpdf->PageNumSubstitutions[] = array('from'=>1, 'suppress'=>'on');
		} else {
			$mpdf->pagenumPrefix = '( หน้าที่ ';
			$mpdf->pagenumSuffix = ' )';			
		}
		/*-- disable page num if page = 1 */
		$mpdf->Output($fileName, 'I');
	}

 	function exportMPDF_withTextWaterMark($htmView, $watermark_text = 'SAMPLE', $fileName = 'mpdf.pdf', $m_left = 5, $m_right = 2, $m_top = 5, $m_bottom = 5, $m_header = 0, $m_footer = 0) {
		ini_set("memory_limit", "512M");
        include_once APPPATH . '/third_party/mPDF/mpdf.php';

		ob_end_clean();

		$mpdf = new mPDF('th', 'A4', 0, '', $m_left, $m_right, $m_top, $m_bottom, $m_header, $m_footer);
		//$mpdf->setAutoTopMargin = 'stretch';
		//$mpdf->autoMarginPadding = 0; //pad between header and top content, default = 2 (mm)
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetAutoFont();

		$mpdf->progbar_heading = 'Generating report progress';
		$mpdf->StartProgressBarOutput(2);

		//$mpdf->mirrorMargins = 1;
		//$mpdf->list_number_suffix = ')';
		//$mpdf->hyphenate = true;
		$mpdf->SetWatermarkText($watermark_text);
		$mpdf->showWatermarkText = true;
		
		//$mpdf->debug = true;
		//$mpdf->showImageErrors = true;
		//$mpdf->allow_output_buffering = true;
		
		$mpdf->WriteHTML($htmView);
		
		/*++ disable page num if page = 1 */
		if (count($mpdf->pages) == 1) {
			$mpdf->pagenumPrefix = '';
			$mpdf->pagenumSuffix = '';
			$mpdf->PageNumSubstitutions[] = array('from'=>1, 'suppress'=>'on');
		} else {
			$mpdf->pagenumPrefix = '( หน้าที่ ';
			$mpdf->pagenumSuffix = ' )';			
		}
		/*-- disable page num if page = 1 */
		$mpdf->Output($fileName, 'I');
	}

	function _addPage($htmlView, $m_left = 10, $m_right = 5, $m_top = 5, $m_bottom = 5, $m_header = 0, $m_footer = 0) {
		try {
			if (! isset($this->_pdf)) {
				$this->_pdf = new mPDF('th', 'A4', 0, '', $m_left, $m_right, $m_top, $m_bottom, $m_header, $m_footer);
				$this->_pdf->setAutoTopMargin = 'stretch';
				$this->_pdf->autoMarginPadding = 0;
				$this->_pdf->SetDisplayMode('fullpage');
				$this->_pdf->SetAutoFont();
				
				$this->_pdf->debug = FALSE;
				$this->_pdf->showImageErrors = FALSE;
				$this->_pdf->allow_output_buffering = TRUE;
				
				$this->_pdf->progbar_heading = 'Generating report progress';
				$this->_pdf->StartProgressBarOutput(2);
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
				if (count($this->_pdf->pages) == 1) {
					$this->_pdf->pagenumPrefix = '';
					$this->_pdf->pagenumSuffix = '';
					$this->_pdf->PageNumSubstitutions[] = array('from'=>1, 'suppress'=>'on');
				} else {
					$this->_pdf->pagenumPrefix = '( หน้าที่ ';
					$this->_pdf->pagenumSuffix = ' )';			
				}
				/*-- disable page num if page = 1 */
				$this->_pdf->Output($fileName, $type);
			}
		} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
			echo $e->getMessage();
		}
	}

}