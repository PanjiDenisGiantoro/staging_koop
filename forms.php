<?php
/*********************************************************************************
*          Project		:	e-ITEM BANK - Version 2.0. 
*							(C) Copyright 2003 by ITR e-Solutions Sdn Bhd
*          Filename		:	forms.php
*          Date 		:	27/02/2003
*		   Amended		:	19/06/2003 - cater for selection hidden on SQ
*********************************************************************************/
print '
<script>
	function dispName(field) {
		e = document.MyForm;
	  	for(c=0; c<e.elements.length; c++) {
	    	if(e.elements[c].name==field) {
				pk = e.elements[c].value;
				alert (pk);
				e.display.value = pk;
			}
		}		
	}	
</script>
';

function FormEntry($strFormName, $strFormElement, $strFormType, $strFormValue, $strFormData, $strFormDataValue, $strFormSize, $strFormLength, $strFormStyle = "")
{
	//--- text & password ---
	if (($strFormType == "text") || ($strFormType == "password"))
		//print '<input name='.$strFormElement.' type='.$strFormType.' size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'">';
                                    print '<input name='.$strFormElement.' type='.$strFormType.' size='.$strFormSize.' class="form-control" maxlength='.$strFormLength.' value="'.$strFormValue.'">';
        
                   if (($strFormType == "text-sm") || ($strFormType == "password-sm"))
		//print '<input name='.$strFormElement.' type='.$strFormType.' size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'">';
                                    print '<input name='.$strFormElement.' type="text" size='.$strFormSize.' class="form-control-sm" maxlength='.$strFormLength.' value="'.$strFormValue.'">';
                   
                   if (($strFormType == "textx") || ($strFormType == "passwordx"))
		//print '<input name='.$strFormElement.' type='.$strFormType.' size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'">';
                                    print '<input name='.$strFormElement.' type="text" size='.$strFormSize.' class="form-controlx" maxlength='.$strFormLength.' value="'.$strFormValue.'">';
        

	//--- hidden ---
	if (($strFormType == "hiddentext"))
		print '<input name='.$strFormElement.' class="form-control-sm btn-light" type="text" size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'" onfocus="this.blur()">';	
                                    
	//--- submit button ---
	if (($strFormType == "submit"))
		//print '<input name='.$strFormElement.' type='.$strFormType.' value="'.$strFormElement.'">';
                                    print '<input name='.$strFormElement.' type='.$strFormType.' value="'.$strFormElement.'" class="btn btn-primary w-sm btn-sm waves-effect waves-light">';

		
	//--- displayonly ---
	if ($strFormType == "displayonly") {
		if ($strFormData == "") { 
			print ' <input class="inputDisable form-controlx" name='.$strFormElement.' type="text" size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'" onfocus="this.blur()">';
		} else {
			print ' <input class="inputDisable form-controlx" name='.$strFormElement.' type="text" size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'" onfocus="this.blur()" onselect="dispName(\''.$strFormElement.'\');">';
			print ' <input class="inputDisable form-controlx" name="display" type="text" size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'" onfocus="this.blur()">';
//			print ' <input class="inputDisable" name="display" type="text" size='.$strFormSize.' maxlength='.$strFormLength.' value="'.$strFormValue.'" onfocus="this.blur()">';
//			print '<b>'.$strFormData[array_search($strFormValue,$strFormDataValue)].'</b>';
		}
	}
		
	//--- hidden & file ---
	if (($strFormType == "hidden") || ($strFormType == "file")) {
		print '<input name='.$strFormElement.' type='.$strFormType.' value='.$strFormValue.'><font class="textFont">&nbsp;';
                                    //print '<input name='.$strFormElement.' type='.$strFormType.' class="form-control-sm" value='.$strFormValue.'><font class="textFont">&nbsp;';
		if ($strFormData == "") { 
			print '<b>'.$strFormValue.'</b></font>';
		} else {
			if ($strFormValue <> "") {
				print '<b>'.$strFormData[array_search($strFormValue,$strFormDataValue)].'</b></font>';
			}
		}
	}
		
	//--- checkbox ---
	if ($strFormType == "checkbox"){
//		print '<input name="'.$strFormElement.'" type="'.$strFormType.'" value="'.$strFormValue.'" ';
		print '<input class="form-check-input" name="'.$strFormElement.'" type="'.$strFormType.'" value="1" ';
		if ($strFormValue == 1) print 'checked';
		print '>';
	}

	if ($strFormType == "hiddencheckbox"){
		print '<input class="none" name="'.$strFormElement.'" type="hidden" value="'.$strFormValue.'" >&nbsp;';
		if ($strFormValue == 1) print '<b>Yes</b>'; else print '<b>No</b>';
	}
	
	//--- readonly - especially for password display ---
	if ($strFormType == "readonly"){
		print '<input class="none" name='.$strFormElement.' type="hidden" value='.$strFormValue.'>&nbsp;';
		print '<b>******************</b>';
	}
	
	//--- Hidden Date ---
	if ($strFormType == "hiddenDate"){
		if (strlen($strFormValue) <> 0)
//			$strDate = substr($strFormValue,8,2).'/'.substr($strFormValue,5,2).'/'.substr($strFormValue,0,4);
			$strDate = toDate("d/m/Y",$strFormValue);
		else
			$strDate = $strFormValue;
		print '<input class="none" name='.$strFormElement.' type="hidden" value='.$strDate.'>&nbsp;';
		print '<b>'.$strDate.'</b>';
	}	
	//--- Hidden Date Time---
	if ($strFormType == "hiddenDateTime"){
		if (strlen($strFormValue) <> 0)
//			$strDate = substr($strFormValue,8,2).'/'.substr($strFormValue,5,2).'/'.substr($strFormValue,0,4).' '.substr($strFormValue,11,8);
			$strDate = toDate("d/m/Y H:i:s A",$strFormValue);
		else
			$strDate = $strFormValue;
		print '<input class="none" name='.$strFormElement.' type="hidden" value='.$strDate.'>&nbsp;';
		print '<b>'.$strDate.'</b>';
	}	
	//--- Date ---
                  if ($strFormType == "date2"){
		if (strlen($strFormValue) <> 0)
			$strDate = substr($strFormValue,8,2).'/'.substr($strFormValue,5,2).'/'.substr($strFormValue,0,4);
		else
			$strDate = $strFormValue;
		print '<input name='.$strFormElement.' type="text" class="form-control" size="12" maxlength="10" value="'.$strDate.'">';

	}
                elseif ($strFormType == "datex"){
		if (strlen($strFormValue) <> 0)
			$strDate = substr($strFormValue,8,2).'/'.substr($strFormValue,5,2).'/'.substr($strFormValue,0,4);
		else
			$strDate = $strFormValue;
		print '<input name='.$strFormElement.' type="text" class="form-controlx" size="12" maxlength="10" value="'.$strDate.'">';

	}
	elseif ($strFormType == "date"){
		if (strlen($strFormValue) <> 0)
			$strDate = substr($strFormValue,8,2).'/'.substr($strFormValue,5,2).'/'.substr($strFormValue,0,4);
		else
			$strDate = $strFormValue;
		//print '<input name='.$strFormElement.' type="text" class="form-controlx" size="12" maxlength="10" value="'.$strDate.'">';
                                    //print '<input name='.$strFormElement.' type="text" class="form-controlx" size="12" maxlength="10" value="'.$strDate.'">';
                print '<div class="input-group" id="'.$strFormElement.'">
                                                                <input type="text" name='.$strFormElement.' class="form-controlx" placeholder="dd/mm/yyyy"
                                                                    data-provide="datepicker" data-date-container="#'.$strFormElement.'"
                                                                    data-date-autoclose="true" value="'.$strDate.'">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><i
                                                                            class="mdi mdi-calendar"></i></span>
                                                                </div>
                                                            </div>';
	}
	elseif ($strFormType == "date3"){
		if (strlen($strFormValue) <> 0)
			$strDate = substr($strFormValue, 0, 10); // "YYYY-MM-DD"
		else
			$strDate = "";
		print'
			<div class="form-group">
            	<input type="date" class="form-control" id="'.$strFormElement.'" name="'.$strFormElement.'" value="'.$strDate.'" '.$strFormStyle.'>
          	</div>
		  ';
	}

	//--- TextArea ---
	if ($strFormType == "textarea")
		//print '<textarea cols="'.$strFormSize.'" rows="'.$strFormLength.'" name="'.$strFormElement.'">'.$strFormValue.'</textarea>';
                                    print '<textarea class="form-control" rows="'.$strFormLength.'" name="'.$strFormElement.'">'.$strFormValue.'</textarea>';

	if ($strFormType == "textarea-sm")
		//print '<textarea cols="'.$strFormSize.'" rows="'.$strFormLength.'" name="'.$strFormElement.'">'.$strFormValue.'</textarea>';
                                    // print '<textarea class="form-control-sm" rows="'.$strFormLength.'" name="'.$strFormElement.'">'.$strFormValue.'</textarea>';
        								print '<textarea class="form-control-sm" cols="'.$strFormSize.'" rows="'.$strFormLength.'" name="'.$strFormElement.'">'.$strFormValue.'</textarea>';

                  if ($strFormType == "textareax")
		//print '<textarea cols="'.$strFormSize.'" rows="'.$strFormLength.'" name="'.$strFormElement.'">'.$strFormValue.'</textarea>';
                                    print '<textarea class="form-controlx" cols="'.$strFormSize.'" rows="'.$strFormLength.'" name="'.$strFormElement.'">'.$strFormValue.'</textarea>';

        
                //--- Radio ---
	if ($strFormType == "radio"){
		for ($cnt = 0; $cnt < count($strFormData); $cnt++) {
                    /*
			print '<input class="none" type="radio" name='.$strFormElement.' value="'.$strFormDataValue[$cnt].'" ';
			if ($strFormValue == $strFormDataValue[$cnt]) print 'checked="checked"';
			print '>'.$strFormData[$cnt]; */
                    print '<div class="form-check mb-2"><input class="form-check-input" type="radio" name="'.$strFormElement.'" id="'.$strFormElement.$cnt.'" value="'.$strFormDataValue[$cnt].'" ';
			if ($strFormValue == $strFormDataValue[$cnt]) print 'checked="checked"';
			print '><label class="form-check-label" for="'.$strFormElement.$cnt.'">'.$strFormData[$cnt].'</label></div>';       
		}
	}
	
	//--- Select Box ---
	if (($strFormType == "select") || ($strFormType == "selectx") || ($strFormType == "hiddenSelect")) {
		if ($strFormType == "hiddenSelect") {
			print '<select name='.$strFormElement.' class="form-select inputDisable">
					<option value="">';
		} if ($strFormType == "selectx") {
			print '<select class="form-selectx" name="'.$strFormElement.'" '.$strFormStyle.'>
					<option value="">- pilih '.$strFormName.' -';		
		} else {
			print '<select class="form-selectx" name="'.$strFormElement.'" '.$strFormStyle.'>
					<option value="">- pilih '.$strFormName.' -';		
		}
		for ($cnt = 0; $cnt < count($strFormData); $cnt++) {
			print '<option value="'.$strFormDataValue[$cnt].'" ';
			if ($strFormValue == $strFormDataValue[$cnt]) print 'selected';
			print '>'.$strFormData[$cnt];
		}
		print '</select>';
	}
        
                if (($strFormType == "select-sm") || ($strFormType == "hiddenSelect-sm")) {
		if ($strFormType == "hiddenSelect") {
			print '<select name='.$strFormElement.' class="form-select-sm inputDisable">
					<option value="">';
		} else {
			print '<select class="form-select-sm" name="'.$strFormElement.'" '.$strFormStyle.'>
					<option value="">- pilih '.$strFormName.' -';
		}
		for ($cnt = 0; $cnt < count($strFormData); $cnt++) {
			print '<option value="'.$strFormDataValue[$cnt].'" ';
			if ($strFormValue == $strFormDataValue[$cnt]) print 'selected';
			print '>'.$strFormData[$cnt];
		}
		print '</select>';
	}

	if ($strFormType == "custom") {
		if ($strFormElement == "month_day") {
			$selectedMonth = isset($_POST['month']) ? $_POST['month'] : '';
			$selectedDay   = isset($_POST['day']) ? $_POST['day'] : '';

			echo "<label>Months:</label> ";
			echo "<select name='month'>";
			for ($m = 0; $m <= 24; $m++) { // allow up to 2 years duration
				$sel = ($m == $selectedMonth) ? 'selected' : '';
				echo "<option value='$m' $sel>$m</option>";
			}
			echo "</select> ";

			echo "<label>Days:</label> ";
			echo "<select name='day'>";
			for ($d = 0; $d <= 31; $d++) {
				$sel = ($d == $selectedDay) ? 'selected' : '';
				echo "<option value='$d' $sel>$d</option>";
			}
			echo "</select>";
		} else {
			// Handle other custom form elements
			eval('global $FormCustomHTML;');
			if (isset($FormCustomHTML) && is_array($FormCustomHTML) && isset($FormCustomHTML[$GLOBALS['i']])) {
				echo $FormCustomHTML[$GLOBALS['i']];
			}
		}
	}
	
}

function FormValidation($strFormName, $strFormElement, $strFormValue, $strFormValidMethod, $errCount)
{
	global $strErrMsg;
	
	if ($strFormValue == "") {
		if ($strFormValidMethod == "CheckBlank") {
			array_push ($strErrMsg, $strFormElement);
			//print '- <font class=redText>'.$strFormName.' Tidak boleh dikosongkan.</font><br>';
                        echo '                                                     
                                                        <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                        </button>
                                                        <strong>'.$strFormName.'</strong> Tidak boleh dikosongkan.
                                                    </div>';
		}
	} else {
		if ($strFormValidMethod == "CheckNumeric") {
			if(eregi("[^0-9]",$strFormValue)) { 
				array_push ($strErrMsg, $strFormElement);
				//print '- <font class=redText>'.$strFormName.' Mestilah Nombor sahaja.</font><br>';
                                                                        echo '                                                     
                                                                            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                                            </button>
                                                                            <strong>'.$strFormName.'</strong> Mestilah Nombor sahaja.
                                                                        </div>';
			}
		}
		if ($strFormValidMethod == "CheckDecimal") {
			if(eregi("[^-0-9.^0-9]",$strFormValue)) { 
				array_push ($strErrMsg, $strFormElement);
				//print '- <font class=redText>'.$strFormName.' Mestilah nombor sahaja.</font><br>';
                                                                        echo '                                                     
                                                                            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                                            </button>
                                                                            <strong>'.$strFormName.'</strong> Mestilah Nombor sahaja.
                                                                        </div>';
			}
		}
		if ($strFormValidMethod == "CheckEmailAddress") {
			if (!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\.)+[a-z]{2}[mtgvu]?$", $strFormValue)) {
				array_push ($strErrMsg, $strFormElement);
				//print '- <font class=redText>Pastikan Alamat Emel adalah sah.</font><br>';
                                                                        echo '                                                     
                                                                            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                                            </button>
                                                                            <strong>Pastikan Alamat Emel adalah sah.</strong> 
                                                                        </div>';
			}
		}
		if ($strFormValidMethod == "CheckDate") {
			if (ValidDate($strFormValue) == "N") {
				array_push ($strErrMsg, $strFormElement);
				//print '- <font class=redText>Tanggal tidak sah.</font><br>';
                                                                        echo '                                                     
                                                                            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                                            </button>
                                                                            <strong>Tanggal tidak sah.</strong> 
                                                                        </div>';
			}
		}
	}

}

/* check if a date in the format "DD/MM/YYYY" is valid. returns "Y" if valid, "N" if invalid */ 
function ValidDate($value) { 
	$strData = strtok($value, "/"); 
	$intCount = 1; 
	while ($strData) { 
		if ($intCount == 1) $tmpday = $strData; 
		if ($intCount == 2) $tmpmonth = $strData; 
		if ($intCount == 3) $tmpyear = $strData; 
		$intCount = $intCount + 1; 
		$strData = strtok("/"); 
	}
	if (checkdate($tmpmonth,$tmpday,$tmpyear)) return "Y";  else return "N"; 
} 
?>