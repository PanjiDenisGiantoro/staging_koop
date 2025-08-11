<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	greImportPot.php
*          Date 		: 	13/06/2006
*********************************************************************************/


include ("header.php");	

if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2) {
	print '<script>top.location="index.php";</script>';
}

$title     = 'Import Fail Potongan';

if (!isset($action)) $action = "";
if (!isset($dir)) 	 $dir = "textfile/";
if (!isset($msg)) 	 $msg = "";
if (!isset($mth)) $mth	= date("n");                 		
if (!isset($yr)) $yr	= date("Y");
$yrmth = sprintf("%04d%02d", $yr, $mth);

$ListThisDir = $PATH_TRANSLATED;
$ListThisDir = str_replace(basename($PATH_TRANSLATED), "", $ListThisDir);
$ListThisDir = $ListThisDir . "textfile/";

if ($action == "Semak") {
	$uploadFile =  $HTTP_POST_FILES['uploadFile']['tmp_name'];
//	$toFile = $ListThisDir.'\\'.$uploadFile_name;  
	$toFile = $ListThisDir.'/'.$uploadFile_name;   
	print '     ---------'.$uploadFile_name;  
	if ($uploadFile <> "none"){
		if (!copy( $uploadFile, $toFile ) ) 
             $msg =  'File could not be uploaded'; 
	} else
		$msg = 'File cannot be empty';
	print '<script>';
	if ($msg <> "") 
		print 'alert("'.$msg.'");';
	else {
		//print ' opener.document.location = "greFilePreview.php";
		//		window.close();';
		print 'alert("Upload done!");';
		$fileimport = $uploadFile_name;   

	}
	print '</script>';
}


if ($action == 'Import') {
	if ($fileimport == '') {
			print '	<script>
						alert("Masukkan nama file..");
						window.location = "import.php";
					</script>';
					exit;
	}

	 $docNo = dlookup("angkasa", "docNo", "docNo=" . tosql($fileimport, "Text"));
	 if($docNo==$fileimport){
			print '	<script>
						alert("File '.$fileimport.' telah di import");
						window.location = "import.php";
					</script>';
			exit;
	}
}

if (!isset($_SERVER["PATH_TRANSLATED"]))
	$ServerDir = '';
else
	$ServerDir = $_SERVER["PATH_TRANSLATED"];
$ServerDir = str_replace(basename($ServerDir), "", $ServerDir);
//$ServerDir = $ServerDir.'angkasa\\';
$ServerDir = $ServerDir.'textfile/';

//<form  action="greImportPot.php" method="post">

print '
<form name="FrmImport" action="greImportPot.php?dir='.$dir.'" method="post" enctype="multipart/form-data">
<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="3" width="95%" align="center">
	<tr>
		<td	 colspan="2"><b class="maroonText">' . strtoupper($title) . '</b></td>
	</tr>
	<tr>
		<td><br>
		<table border="0" cellpadding="3" cellspacing="1" width="500" align="center" class="lineBG">
			<tr>
				<td class=Header>Import Fail Potongan :</td>
			</tr>
			<tr>
				<td class="Data">
					<table width="100%">
						<tr><td class="textFont">Sila pilih fail :
							<input type="file" name="uploadFile" size="15" value="">&nbsp;
							<input type="text" name="fileimport" size="25" value="'.$uploadFile_name.'">
							<!--input type="button" class="but" value="Pilih" onclick="window.open(\'greFilePreview.php\',\'sel\',\'top=100,left=100,width=600,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');"--><input type="submit" name="action"  value="Semak" size="50" class="but">
							</td>
						</tr>
						<tr><td class="textFont" align="center">
							&nbsp;Pilih Bulan : 
							<select name="mth" class="data" onchange="document.FrmImport.submit();">';
							for ($j = 1; $j < 13; $j++) {
								print '			<option value="'.$j.'"';
								if ((intval($mth)) == $j) print 'selected';
								print 			'>'.$j;
							}
							print '</select>&nbsp;&nbsp; Tahun :&nbsp;&nbsp;&nbsp;<input type="text" name="yr" size="5" maxlength="4" value="'.$yr.'" class="data">
							</td>
						</tr>						
						<tr>
							<td class="textFont" align="center">
							&nbsp;
							<input type="submit" name="action"  value="Papar" size="50" class="but">&nbsp;
							<input type="submit" name="action"  value="Import" size="50" class="but"
							onClick="if(!confirm(\'Pasti import fail angkasa...?\')) {return false} else {window.FrmImport.submit();};">
							</td>						
						</tr>			
					</table>
				</td>
			</tr>
		</table>		
		</td>
	</tr>
</table>
</form>
<table border="0" cellspacing="1" cellpadding="10" width="95%" align="center">
<tr><td><font class="textFont">';
//$conn->debug = true;
//print 'action '.$action;
if ($action == 'Import' || $action == 'Papar') {
	$fileimport = $ServerDir.$fileimport;
	print '<hr size="1">';
	if (substr($fileimport,-4,1) <> ".") {
		$filename = $fileimport;
		$fp = fopen($filename, "r");
		$tot = 0;
		$totAmt = 0;
		$totInt = 0;		
		while (!feof($fp)) { 
		    $buffer = fgets($fp, 4096);
			if ($action == 'Papar') print '<font class="textFont">'.trim($buffer).'</font><br>';
			if ($buffer <> "" AND substr($buffer,0,1) == '0') {
				$docNo	= substr($buffer,85,5);
				$yymm 	= substr($buffer,31,6);
			}
			if ($buffer <> "" AND substr($buffer,0,1) == '1') {
				$member 	= 0;
				$status		= 0;
				$ic			= trim(substr($buffer,2,12));
				$memberID 	= substr($buffer,19,12);
				$memberID 	= (int)$memberID;
				$memberID = sprintf("%05s",  $memberID);
				//repeat read string for cent on 45 position - duplicate number result 
				$amt		= sprintf(number_format(substr($buffer,41,5)).'.'.substr($buffer,46,2));
				$interest 	= sprintf(number_format(substr($buffer,48,5)).'.'.substr($buffer,53,2));
				$deductCode	= substr($buffer,37,4);
				$locateID_1	= dlookup("userdetails", "memberID", "newIC=" . tosql($ic), "Text");
				if ($locateID_1 == $memberID) {
					$member = 1;
					$userID= dlookup("userdetails", "userID", "newIC=" . tosql($ic), "Text");
				} else {
					$locateID_1	= dlookup("userdetails", "memberID", "oldIC=" . tosql($ic), "Text");
					if ($locateID_1 == $memberID) {
						$member = 1;
					    $userID= dlookup("userdetails", "userID", "oldIC=" . tosql($ic), "Text");
					} else {
						$locateID_1	= dlookup("userdetails", "memberID", "memberID=" . tosql($memberID), "Text");
						if ($locateID_1 == $memberID) {
						    $userID= dlookup("userdetails", "userID", "memberID=" . tosql($memberID), "Text");
							$member = 1;
						} else {
							$status = 2;
						}					
					}

				}
				
				if($memberID == '00000'){
					$memberID = 0;
					$userID = 0;
					$member = 0;
					$status = 2;
				}

				if($member == 0){
					$userID = 0;
					$status = 2;
				}
				
				//if ($member == 0) print '<br />'.$memberID.' bukan. userID - '.$userID;
				//if ($member == 1) print '<br />'.$memberID.' member. userID - '.$userID;

				//--- BEGIN : Perform import function ------------------------------------------------------------

				if ($action == 'Import') {
					//--- BEGIN	:	Create Angkasa file
					$sSQL = "";
					$importedBy	= get_session("Cookie_userName");
					$importedDate = date("Y-m-d H:i:s");                 
					$sSQL	= "INSERT INTO angkasa (" . 
								          "docNo," . 
								          "yymm," . 
								          "ic," . 
								          "memberID," . 
								          "userID," . 
								          "amt," . 
								          "interest," . 
								          "deductCode," . 
								          "member," . 
								          "status," . 
								          "importedDate," . 
								          "importedBy)" . 	
								          " VALUES (" . 
								          tosql($docNo, "Text") . "," .			  
								          tosql($yymm, "Number") . "," .			  
								          tosql($ic, "Text") . ",'" .			  
								          $memberID . "','" .
								          $userID . "'," .
										  tosql($amt, "Text") . "," .
								          tosql($interest, "Text") . ",".
										  tosql($deductCode, "Text") . "," .
								          $member. ",".
								          $status. ",".
										  tosql($importedDate, "Text") . "," .
								          tosql($importedBy, "Text") . ")";
					$rs = &$conn->Execute($sSQL);
					//print $sSQL.'<br>';
				
				$tot = $tot + 1 ;
				$totAmt = $totAmt + $amt;
				$totInt = $totInt + $interest;
				}
				//--- END   : Perform import function ------------------------------------------------------------	
			}
		}
		if ($action == 'Import') {
					//print '<br />interest '.$totInt;
					$importedDate = date("Y-m-d H:i:s");                 
					$sSQL	= "INSERT INTO angtrans (" . 
								          "docNo," . 
								          "yymm," . 
						  				  "angDate," . 
								          "angBy," . 
								          "angRekod," . 
								          "angAmt," . 
								          "angInterest)" . 	
								          " VALUES (" . 
								          tosql($docNo, "Text") . "," .			  
								          tosql($yymm, "Text") . "," .			  
								          tosql($importedDate, "Text") . "," .			  
								          tosql($importedBy, "Text") . "," .			  
								          $tot . "," .
										  $totAmt . "," .
								          $totInt . ")";
					$rs = &$conn->Execute($sSQL);
					//print $sSQL.'<br>';

			print '	<script>
						alert("Fail import berjaya...!");
					</script>';		
		}
		fclose ($fp);
	} else {
		print '	<script>
					alert("File '.$fileimport.' not allowed to verify / import...!");
					window.location = \''.$PHP_SELF.'\';
				</script>';
	}
	print '<hr size="1">';
}
print '</td></tr></table></font>';
include("footer.php");
?>
