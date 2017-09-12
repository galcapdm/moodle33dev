<?php
    
/*
require_once('../../../../config.php');
require_once($CFG->libdir.'/filelib.php');
*/
require_once($CFG->libdir.'/tcpdf/tcpdf.php');

// Generate a customised Journal-style Digital Workbook
// CAPDM: KWC 7-Jun-2016

class dwb_workbook_journal extends dwb_workbook_type {
    private $dwbrole = "journal";
    
    /**
     * @return nothing
     */
    public function render() {

	global $CFG, $USER, $DB, $PAGE;
	$cm  = $this->cm;   $course = $this->course;    $capdmdwb = $this->capdmdwb;
	$sid = $this->sid;  // This may be 0

	$nm = $USER->firstname." ".$USER->lastname;

	$_CELLSIZE      = 5;  // Cell heights
	$_SMALLCELLSIZE = 1;  
	$_COL1          = 40;  $_COL2     = 140;  $_COL3  = 10;  $_COL4  = 5;  $_COL5   = 125;  
	$_LCOL          = 0;   $_RCOL     = 1;    $_ACOL  =  2;   $_BCOL  = 3;   $_CCOL  = 4;    
	$_FONTSIZE      = 0;  // Will be used later
	
	// Tables used
	$wrappertable = $CFG->prefix."capdmdwb_wrapper";
	$inputtable = $CFG->prefix."capdmdwb_response";
	$activitytable = $CFG->prefix."capdmdwb_activity";
	
	
	// Create the document
        $doc = new pdfDWB(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 

	// set document information
	$doc->SetCreator(PDF_CREATOR);
	$doc->SetAuthor($nm);
	$doc->SetTitle(get_string('pdftitle', 'capdmdwb').$course->fullname);
	$doc->SetSubject(get_string('course', 'capdmdwb').$course->shortname);
	$doc->SetKeywords('TCPDF, PDF, example, test, guide');
	
	//$doc->print_header = true;
	//$doc->print_footer = true;
	
	//set margins
	$doc->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+10, PDF_MARGIN_RIGHT);
	$doc->SetHeaderMargin(PDF_MARGIN_HEADER);
	$doc->SetFooterMargin(PDF_MARGIN_FOOTER);
	
	//set auto page breaks
	$doc->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); 
	$doc->AddPage();
	
	// Middle align the image 100 mm from the top
	$doc->Image('pix/logo.jpg', 8, 8, 'M');  // Theme logo
	$doc->Image('pix/journal.jpg', 20, 50, 'M'); // Image of Journal
	
	$doc->setY(-70);  // Up from the bottom
	$doc->SetDrawColor(103, 103, 103);  // border color
	$doc->SetLineWidth(0.5);  // For the table cells.
	
	$doc->SetFillColor(255, 255, 255);  $doc->SetTextColor(0);  // black
	$doc->setFontSize(11);
	
	// need to set this as it may not always be the current user we want to view the PDF workbook for
	// Teachers may be looking at somone else
	// We can protect with some MD5 hashing but can also use that to report problems when displaying the PDF
	if ($_GET['checksum'] == md5($_GET['dwb'].'this is unguessible')){
	    $userid = $_GET['dwb'];
	} else {
	    $userid = '0';
	}

	$topic = -1;
	if ($_GET['topic']) $topic = $_GET['topic'];  // -1 signifies ALL topics, otherwise an individual topic.
	
	// Get width in between margins
	$w = $doc->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT;
	$doc->MultiCell($w, $_CELLSIZE, get_string('course','capdmdwb').": ".$course->fullname, 'LTR', 'L', 1, 1);
	if ($userid > 0){
	    $doc->MultiCell($w, $_CELLSIZE, get_string('journalfor','capdmdwb').': '.$nm, 'LR', 'L', 1, 1);
	} else {
	    $doc->MultiCell($w, $_CELLSIZE, get_string('journalfor','capdmdwb').': '.get_string('md5problem','capdmdwb'), 'LR', 'L', 1, 1);
	}
	$doc->MultiCell($w, $_CELLSIZE, get_string('date','capdmdwb').': '.date('D, d-M-Y'), 'LBR', 'L', 1, 1);
	
	
	$doc->AddPage();  // PUT UP A PROGRESS PAGE
	
	// Query is the same as in capdmdwb and capdmcert with the addition of the first two fields wrap.title and wrap.user_level being added to the list of fields returned
	$where = ($topic == -1) ? "" : " AND wrap.topic_no = ".$topic." ";
	$strSQL = "SELECT wrap.title AS topic_title, wrap.user_level, act.id, wrap.topic_no, wrap.session_no, act.course, wrap.mod_id, wrap.wrapper_id, 
                          act.activity_id, act.data_type, act.qpart, wrap.title, wrap.preamble, act.run_order, resp.data_id, resp.data_value, 
			  resp.data_option, resp.data_explanation, resp.response_include 
			  FROM {capdmdwb_activity} act 
			  LEFT JOIN (SELECT data_id, data_value, data_option, data_explanation, response_include 
			             FROM {capdmdwb_response} 
				     WHERE course = ".$course->id." AND user_id = ".$userid.") resp 
		          ON act.activity_id = resp.data_id 
			  LEFT JOIN {capdmdwb_wrapper} wrap ON act.wrapper_id = wrap.wrapper_id 
			  WHERE wrap.course = ".$course->id."  AND wrap.role_id='".$this->dwbrole."' ".$where.
			  "ORDER BY wrap.topic_no, wrap.session_no, act.run_order";
	
	$rst = $DB->get_recordset_sql($strSQL);
	
	$topic_no = -99;  $topic_completed = true; $runorder = 0;
	//          $rt = rs_fetch_record($rst);  // Pick up the first task record
	
	// Adjust Col 2 now that we've set margins
	$_COL2 = $doc->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - $_COL1;
	$cwidths = array($_COL1, $_COL2, $_COL3, $_COL4, $_COL5);
	
	$_FONTSIZE = $doc->getFontSizePt();
	$doc->setFontSize(18);  $doc->SetFont('', 'B');
	$doc->SetFillColor(0);  
	$doc->SetTextColor(54, 102, 152);  // #3f89c3 darker colour (blue)

	$doc->MultiCell(array_sum($cwidths), $_CELLSIZE, get_string('courseworktitle', 'capdmdwb'), 0, 'L', 0, 1, '', '', true);
	$doc->setFontSize($_FONTSIZE);
	
	$doc->MultiCell($_COL4*2, $_CELLSIZE, "", 0, 'C', 0, 1);  // Spacer at the top
	$doc->MultiCell($_COL4*2, $_CELLSIZE, "", 0, 'C', 0, 1);  
	
	$done = 0;  $notdone = 0;  $boxes = 0;  // Count how many we put 0ut
	
	// Loop for activities summary block start
	foreach ($rst as $rt) {  //while ($rst->valid()) 
  	    if ($rt->topic_no != $topic_no) {  // Tick over to a new topic
		$runorder = 0;
		if ($topic_no != -99) {
		    
		    $doc->MultiCell($_COL4*2, $_CELLSIZE, "", 0, 'C', 0, 1);  // Go to the next line * 2
		    $doc->MultiCell($_COL4*2, $_CELLSIZE, "", 0, 'C', 0, 1); 
		    $boxes = 0;  // Reset per Topic
		}
		
		$topic_no = $rt->topic_no;  // Pick up the real topic no.
		$topic_completed = true;    // Reset this
		
		$_FONTSIZE = $doc->getFontSizePt();
		$doc->setFontSize(14);  $doc->SetFont('', 'B');
		$doc->SetTextColor(0);
		$doc->MultiCell(0, $_CELLSIZE, get_string('topic','capdmdwb')." ".$topic_no.": ".$rt->topic_title, 0, 'L', 0, 1, '', '', true);
	    }
	    
	    $pf = ($rt->user_level == 0) ? "" : "-".$rt->user_level;
	    if ($rt->data_value != '') $done++; else $notdone++;
	    $boxes++;
	}

	$str_summary = ($topic == -1) ? get_string('summaryalljournalinfo', 'capdmdwb'): get_string('summaryjournalinfo', 'capdmdwb');
	$str_summary = str_replace('%%done%%', $done, $str_summary);
	$str_summary = str_replace('%%acttotal%%', ($done + $notdone), $str_summary);
	
	$doc->Ln();
	
	$_FONTSIZE = $doc->getFontSizePt();
	$doc->setFontSize(14);  
	$doc->SetCellPadding(5);
	$doc->SetFont('', '');
	$doc->SetTextColor(203, 203, 152);  // Blue
	$doc->SetFillColor(0);  
	$doc->MultiCell(0, $_CELLSIZE, $str_summary, 0, 'L', 1, 2, '', '', true);
	$doc->setFontSize($_FONTSIZE);
	$doc->SetCellPadding(1);
	
	// CONTENT PROPER  
	$where = ($topic == -1) ? "" : " AND wrap.topic_no = ".$topic." ";
	$strSQL = "SELECT wrap.title AS topic_title, wrap.user_level, act.id, wrap.topic_no, wrap.session_no, wrap.session_title, wrap.mod_id, 
	                  wrap.wrapper_id, act.ansopt, act.activity_id, act.data_type, act.qpart, wrap.title, wrap.preamble, act.run_order, 
			  act.course, resp.data_id,resp.data_value, resp.data_option, resp.data_explanation, resp.response_include
			  FROM {capdmdwb_activity} act 
			  LEFT JOIN (SELECT data_id, data_value, data_option, data_explanation, response_include 
			             FROM {capdmdwb_response} 
				     WHERE course = ".$course->id." AND user_id = ".$userid.") resp 
                          ON act.activity_id = resp.data_id 
			  LEFT JOIN {capdmdwb_wrapper} wrap ON act.wrapper_id = wrap.wrapper_id 
			  WHERE wrap.course = ".$course->id." AND wrap.role_id='".$this->dwbrole."' ".$where.
			  "ORDER BY wrap.topic_no, wrap.session_no, act.run_order";
	
	$rs = $DB->get_recordset_sql($strSQL);
	
	if ($rs->valid()) {
	    $topic_no = -99;  $session_no = -99;  $wrapper_id = "";
	}
	
	foreach ($rs as $r) {
	    if ($r->topic_no != $topic_no) {
		if ($session_no != -99) ; //$doc->Cell(array_sum($cwidths), 0, '', 'T');
		
		$topic_no = $r->topic_no;  $session_no = -99;  // Reset this value
		$doc->AddPage(); 
		$doc->Ln();
		
		// Topic header section
		// Up the point size for the Topic
		$_FONTSIZE = $doc->getFontSizePt();
		$doc->setFontSize(14);    $doc->SetFont('', 'B');
		$doc->SetFillColor(255);  $doc->SetTextColor(54, 102, 152);  
		$doc->MultiCell(0, $_CELLSIZE, get_string('topic','capdmdwb')." ".$topic_no.": ".$r->topic_title, 0, 'L', 1, 1, '', 8, true);
		$doc->setFontSize($_FONTSIZE);
		
		$doc->Ln();
	    }
	    
	    if ($session_no != $r->session_no) {
		if ($session_no != -99) $doc->Ln();
		
		// Colors, line width and bold font
		$doc->SetTextColor(0);  $doc->SetFont('', 'B');
		$doc->SetDrawColor(90, 90, 90); 
		
		// Session No.
		$doc->SetTextColor(152, 152, 54);  
		$doc->MultiCell($cwidths[$_LCOL], $_CELLSIZE, get_string('session','capdmdwb')." ".$r->session_no.": ", 0, 'L', 1, 0, '', '', true);
		$doc->MultiCell($cwidths[$_RCOL], $_CELLSIZE, $r->session_title, 0, 'L', 1, 1, '', '', true);

		// Preamble?
		$doc->SetFillColor(241, 241, 241);  $doc->SetTextColor(0);
		if (!is_null($r->preamble) && strlen($r->preamble) != 0) {
		    $str = preg_replace( "/\s+/", " ", ltrim($r->preamble) );
		    $doc->MultiCell($cwidths[$_LCOL]+$cwidths[$_RCOL], $_CELLSIZE, $str, 0, 'L', 1, 1, '', '', true);
		}
		
		// Color and font restoration
		$doc->SetTextColor(0);  $doc->SetFillColor(225, 225, 225); // #E1E1E1
		$doc->SetFont('');
		
		$session_no = $r->session_no;
	    }
	    
	    // Now the real data
	    $doc->SetFillColor(255, 255, 255); // #E1E1E1
	    $doc->MultiCell($cwidths[$_LCOL]+$cwidths[$_RCOL], $_CELLSIZE, $r->qpart, 0, 'L', 1, 1, '', '', true);
	    
            if (!is_null($r->ansopt) && strlen($r->ansopt) > 0) {
		$doc->SetFillColor(233, 233, 233); // #E1E1E1
		$doc->MultiCell($cwidths[$_LCOL], $_CELLSIZE, "Option: ", 0, 'R', 0, 0, '', '', true);
		$doc->MultiCell($cwidths[$_RCOL], $_CELLSIZE, $r->ansopt, 0, 'L', 1, 1, '', '', true);
            }
	    
	    // Finally ... my answer
//	    $doc->SetFillColor(241, 241, 241); // #F1F1F1
//	    $doc->MultiCell($cwidths[$_LCOL], $_CELLSIZE, get_string('myanswer','capdmdwb').": ", 0, 'R', 0, 0, '', '', true);
	    $doc->SetFont('', 'I');  // Set my typing in Italics
	    
	    if (stripos($r->data_type, 'highlight') === false) {  // Was qpart for some reason
		if ($r->data_type == "mcq") {
	            $doc->MultiCell($cwidths[$_ACOL], $_CELLSIZE, nl2br($r->data_value), 0, 'C', 1, 0, '', '', true, 0, true);
	            // Insert a blank to act as a placeholder for the image
	            $doc->Image($CFG->dirroot.'/mod/capdmdwb/pix/icons/radio_icon.jpg');
	            $doc->MultiCell($cwidths[$_BCOL], $_CELLSIZE, "", 0, 'C', 1, 0, '', '', true, 0, true);
                    //$doc->RadioButton("mcq", $cwidths[$_BCOL]);
	            $doc->MultiCell($cwidths[$_CCOL], $_CELLSIZE, nl2br($r->data_explanation), 0, 'L', 1, 1, '', '', true, 0, true);
		}
		else if ($r->data_type == "mansopt") {
	            $doc->MultiCell($cwidths[$_ACOL], $_CELLSIZE, "", 0, 'C', 1, 0, '', '', true, 0, true);
	            $doc->Image($CFG->dirroot.'/mod/capdmdwb/pix/icons/radio_icon.jpg');
	            $doc->MultiCell($cwidths[$_BCOL], $_CELLSIZE, "", 0, 'C', 1, 0, '', '', true, 0, true);
	            $doc->MultiCell($cwidths[$_CCOL], $_CELLSIZE, nl2br($r->data_explanation), 0, 'L', 1, 1, '', '', true, 0, true);			
		}
		else if ($r->data_type == "mrq") {
	            $doc->MultiCell($cwidths[$_ACOL], $_CELLSIZE, nl2br($r->data_value), 0, 'C', 1, 0, '', '', true, 0, true);
	            // Insert a blank to act as a placeholder for the image
	            $doc->MultiCell($cwidths[$_BCOL], $_CELLSIZE, "", 0, 'C', 1, 0, '', '', true, 0, true);
	            //$doc->CheckBox("mrq", $cwidths[$_BCOL], true);
	            $doc->MultiCell($cwidths[$_CCOL], $_CELLSIZE, nl2br($r->data_explanation), 0, 'L', 1, 1, '', '', true, 0, true);
		}
		else if ($r->data_type == "select") {
		    $doc->MultiCell($cwidths[$_ACOL], $_CELLSIZE, nl2br($r->data_value), 0, 'C', 1, 0, '', '', true, 0, true);
	            // Insert a blank to act as a placeholder for the image
	            $doc->Image($CFG->dirroot.'/mod/capdmdwb/pix/icons/select_icon.jpg');
	            $doc->MultiCell($cwidths[$_BCOL], $_CELLSIZE, "", 0, 'C', 1, 0, '', '', true, 0, true);
	            //$aa = array(0=>$r->data_value,1=>"one", 2=>"two");
	            //$doc->ComboBox("select", $cwidths[$_BCOL]*4, $cwidths[$_BCOL], $aa);
	            $doc->MultiCell($cwidths[$_CCOL], $_CELLSIZE, nl2br($r->data_option), 0, 'L', 1, 1, '', '', true, 0, true);
		}
		else {  // Default for text and highlight types
		    if(nl2br($r->data_value) == ''){
			$textresponse = get_string('notyetanswered','capdmdwb');
		    } else {
			$textresponse = nl2br($r->data_value);
		    }
	            $doc->MultiCell($cwidths[$_LCOL]+$cwidths[$_RCOL], $_CELLSIZE, $textresponse, 
				    array('LTRB' => array('width' => 0.5, 'cap' => 'butt', 'color' => array(54, 102, 152))),
				    'L', 1, 1, '', '', true, 0, true);
		}
            }
	    else {  // Need to check if this is an HTML-based answer.
		// Write and empty string and go to start of next line
		$doc->MultiCell($cwidths[$_RCOL], $_CELLSIZE, '', 0, 'L', 1, 1, '', '', true);
		
		$b_pattern = '/<em class="bold">([^<]+)<\/em>/';
		$b_replace = '<b>$1</b>';
		$r->data_value = preg_replace($b_pattern, $b_replace, $r->data_value);
		
		$h1_pattern = '/<span class="?wb_hl_1"?>([^<]+)<\/span>/i';
		$h1_replace = '<font bgcolor="yellow">$1</font>';
		$r->data_value = preg_replace($h1_pattern, $h1_replace, $r->data_value);
		$h2_pattern = '/<span class="?wb_hl_2"?>([^<]+)<\/span>/i';
		$h2_replace = '<font bgcolor="red">$1</font>';
		$r->data_value = preg_replace($h2_pattern, $h2_replace, $r->data_value);
		$h3_pattern = '/<span class="?wb_hl_3"?>([^<]+)<\/span>/i';
		$h3_replace = '<font bgcolor="blue">$1</font>';
		$r->data_value = preg_replace($h3_pattern, $h3_replace, $r->data_value);
		$h4_pattern = '/<span class="?wb_hl_4"?>([^<]+)<\/span>/i';
		$h4_replace = '<font bgcolor="green">$1</font>';
		$r->data_value = preg_replace($h4_pattern, $h4_replace, $r->data_value);
		
		$doc->MultiCell($cwidths[$_LCOL], $_CELLSIZE, "", 0, 'R', 0, 0, '', '', true);
		$doc->MultiCell($cwidths[$_RCOL], $_CELLSIZE, $r->data_value, 0, 'L', 1, 1, '', '', true, 0, true);
		//$doc->writeHTMLCell(0, $_CELLSIZE, $doc->GetX(), $doc->GetY(), $r->data_value, 'LTRB', 0);
	    }
	    
	    $doc->SetFont('');  // Set it back
            $doc->Ln();
	}
	
	$rs->close();
	
	// Finally, output the document
	$doc->Output();
    }

}  /// ================= End of Class ====================

// Extend the TCPDF class to create custom Header and Footer
class pdfDWB extends TCPDF
// ======== class ========
{
    //Page header
    public function Header() 
    //----------------------
    {
	global $CFG;
	// CIS Logo
	$image_width = 0;
	// Line break
	$this->Ln(30);
    }
    
    // Page footer
    public function Footer() 
    //----------------------
    {
	global $CFG, $dwb, $nm, $course;
	$_CELLSIZE      = 5;  // Cell heights
	
	// Position at 1.5 cm from bottom
	$this->SetY(-4 * $_CELLSIZE);
	// Set font
	$this->SetFont('FreeSans', 'I', 8);
	
	// Page number
	$this->SetFillColor(54, 102, 152);  // #3f89c3 darker colour (blue)
	$this->SetTextColor(255);
	if ($this->getPage() != 1) {
	    $this->setCellPadding(1);
	    $this->MultiCell(105-PDF_MARGIN_LEFT, $_CELLSIZE, get_string('course', 'capdmdwb').": ".$course->fullname, 0, 'L', 1, 0);
	    $this->MultiCell(105-PDF_MARGIN_RIGHT, $_CELLSIZE, '', 0, 'R', 1, 1);
	    $this->MultiCell(105-PDF_MARGIN_LEFT, $_CELLSIZE, get_string('journalfor', 'capdmdwb').": ".$nm, 0, 'L', 1, 0);
	    $this->MultiCell(105-PDF_MARGIN_RIGHT, $_CELLSIZE, get_string('page', 'capdmdwb').$this->getAliasNumPage().get_string('of', 'capdmdwb').$this->getAliasNbPages(), 0, 'R', 1, 1);
	    $this->MultiCell(105-PDF_MARGIN_LEFT, $_CELLSIZE, get_string('date', 'capdmdwb').": ".date('D, d-M-Y'), 0, 'L', 1, 0);
	    $this->MultiCell(105-PDF_MARGIN_RIGHT, $_CELLSIZE, '', 0, 'R', 1, 1);
	}
	else { // Nothing
	}
    }
    
}  // ============== End of Class ====================

?>
