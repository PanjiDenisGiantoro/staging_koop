<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	greImportBul.php
*          Date 		: 	28/06/2006
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
						window.location = "greImportBul.php";
					</script>';
					exit;
	}

	 $docNo = dlookup("potBulanan", "yrmth", "yrmth=" . tosql($yrmth, "Text"));
	 if($docNo <> ''){
			print '	<script>
						alert("File telah di import");
						window.location = "transpot.php";
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
<form name="FrmImport" action="greImportBul.php?dir='.$dir.'" method="post" enctype="multipart/form-data">
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
							<input type="hidden" name="yrmth"  value="'.$yrmth.'"><input type="submit" name="action"  value="Papar" size="50" class="but">&nbsp;
							<input type="submit" name="action"  value="Import" size="50" class="but"
							onClick="if(!confirm(\'Pasti import fail ...?\')) {return false} else {window.FrmImport.submit();};">
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

if(dlookup("potBulanan", "yrmth", "yrmth=" . tosql($yrmth, "Text")) <> ''){
		printHeader();
		$sql = "select * from potBulanan where yrmth='".$yrmth."'";
		$rs = $conn->Execute($sql);
		$dataval = dbField();
		$cnt = 1;
		while(!$rs->EOF){
		print '<tr>';
		//${$dataval[$i]} = addslashes(trim($data[$i]));
		print '<td style="font-size: x-small">'.$cnt.'&nbsp;</td>';
		for($i=1;$i<=50;$i++) print '<td>'.tohtml($rs->fields($dataval[$i])).'&nbsp;</td>';
		print '</tr>';
		$cnt++;
		$rs->MoveNext();
		}

		printFooter();
}

if ($action == 'Import' || $action == 'Papar') {
	print $fileimport = $ServerDir.$fileimport;
	print '<hr size="1">';
	if (substr($fileimport,-3,3) == "csv") {
		$filename = $fileimport;
		$fp = fopen($filename, "r");
		$tot = 0;
		$totAmt = 0;
		$totInt = 0;		
		printHeader();
		//$cnt = 1;
		while (!feof($fp)) { 
		    $buffer = fgets($fp, 4096);
			$skip = substr($buffer,0,1);
			if($skip == 'K' || $skip == 'B') $bypass = 0; else $bypass = 1;
			//if ($action == 'Papar'){ //print '<font class="textFont">'.trim($buffer).'</font><br>';
			///print '<font class="textFont">';
			$strval = dbField();
			//$strval = explode(",",$strval);
			if($bypass){
				//$data = explode(",",$buffer);
				$data = quote_explode( ",", $buffer, $encap = '"' );
				print '<tr>';
				for($i=0;$i<=50;$i++){
				${$strval[$i]} = addslashes(trim($data[$i]));
				print '<td>'.$data[$i].'&nbsp;</td>';
				}
				print '</tr>';
				//print '</font><br>';
				}//end b pass
			//}//end papar
			if ($action == 'Import' && $bypass) {
				$sSQL = "";
				$importedBy	= get_session("Cookie_userName");
				$importedDate = date("Y-m-d H:i:s");                 
				$sSQL	= "INSERT INTO potBulanan (" . 
						"yrmth," .		
						"no_ahli," .
						"nama," .
						"jab_caw," .
						"kod_caw," .
						"no_pekerja," .
						"bond_pt," .
						"bond_py," .
						"bond_pb," .
						"bond_pi," .
						"bond_ns," .
						"bond_b2," .
						"bond_b3," .
						"bond_b4," .
						"bond_b5," .
						"bond_pu," .
						"selesai_pt," .
						"selesai_py," .
						"selesai_pb," .
						"selesai_pi," .
						"selesai_pns," .
						"selesai_b2," .
						"selesai_b3," .
						"selesai_b4," .
						"selesai_b5," .
						"selesai_pu," .
						"modal_ya," .
						"modal_syer," .
						"pt_pokok," .
						"pt_untung," .
						"py_pokok," .
						"py_untung," .
						"pb_pokok," .
						"pb_untung," .
						"pi_pokok," .
						"pi_untung," .
						"ns_pokok," .
						"b2_pokok," .
						"b2_untung," .
						"b3_pokok," .
						"b3_untung," .
						"b4_pokok," .
						"b4_untung," .
						"b5_pokok," .
						"b5_untung," .
						"pu_umrah," .
						"pu_untung," .
						"jumlah," .
						"catatan," .
						"pot_lepas," .
						"beza," .
						"createdDate," .
						"createdBy)" .
						" VALUES (" . 
						tosql($yrmth, "Text") . "," .
						tosql($no_ahli, "Text") . "," .
						tosql($nama, "Text") . "," .
						tosql($jab_caw, "Text") . "," .
						tosql($kod_caw, "Text") . "," .
						tosql($no_pekerja, "Text") . "," .
						tosql($bond_pt, "Text") . "," .
						tosql($bond_py, "Text") . "," .
						tosql($bond_pb, "Text") . "," .
						tosql($bond_pi, "Text") . "," .
						tosql($bond_ns, "Text") . "," .
						tosql($bond_b2, "Text") . "," .
						tosql($bond_b3, "Text") . "," .
						tosql($bond_b4, "Text") . "," .
						tosql($bond_b5, "Text") . "," .
						tosql($bond_pu, "Text") . "," .
						tosql($selesai_pt, "Text") . "," .
						tosql($selesai_py, "Text") . "," .
						tosql($selesai_pb, "Text") . "," .
						tosql($selesai_pi, "Text") . "," .
						tosql($selesai_pns, "Text") . "," .
						tosql($selesai_b2, "Text") . "," .
						tosql($selesai_b3, "Text") . "," .
						tosql($selesai_b4, "Text") . "," .
						tosql($selesai_b5, "Text") . "," .
						tosql($selesai_pu, "Text") . "," .
						tosql($modal_ya, "Text") . "," .
						tosql($modal_syer, "Text") . "," .
						tosql($pt_pokok, "Text") . "," .
						tosql($pt_untung, "Text") . "," .
						tosql($py_pokok, "Text") . "," .
						tosql($py_untung, "Text") . "," .
						tosql($pb_pokok, "Text") . "," .
						tosql($pb_untung, "Text") . "," .
						tosql($pi_pokok, "Text") . "," .
						tosql($pi_untung, "Text") . "," .
						tosql($ns_pokok, "Text") . "," .
						tosql($b2_pokok, "Text") . "," .
						tosql($b2_untung, "Text") . "," .
						tosql($b3_pokok, "Text") . "," .
						tosql($b3_untung, "Text") . "," .
						tosql($b4_pokok, "Text") . "," .
						tosql($b4_untung, "Text") . "," .
						tosql($b5_pokok, "Text") . "," .
						tosql($b5_untung, "Text") . "," .
						tosql($pu_umrah, "Text") . "," .
						tosql($pu_untung, "Text") . "," .
						tosql($jumlah, "Text") . "," .
						tosql($catatan, "Text") . "," .
						tosql($pot_lepas, "Text") . "," .
						tosql($beza, "Text") . "," .
						tosql($importedDate, "Text") . "," .
						tosql($importedBy, "Text") . ")";
						$rs = &$conn->Execute($sSQL);
					    //print $sSQL.'<br>';
					    //print '----------'.$cnt.'----------------<br>'.$bil;
						//$cnt++;
			}//end import
		}//end while
		print '</table>';
		fclose ($fp);
			print '	<script>
						alert("File import senarai potongan selesai");
						window.location = "transpot.php";
					</script>';
	} else {
		print '	<script>
					alert("File '.$fileimport.' not allowed to verify / import...!");
					window.location = \''.$PHP_SELF.'\';
				</script>';
	}//end allow csv
	print '<hr size="1">';
} //end action
print '</font></td></tr></table>';
include("footer.php");

function printHeader(){
	$header = 'BIL,NO.AHLI,NAMA,JABATAN/CAWANGAN,KODCAWANGAN,NO.PEKERJA,NO.BONDPT,NO.BONDPY,NO.BONDPB,NO.BONDPI,NO.BONDNS,NO.BONDB2,NO.BONDB3,NO.BONDB4,NO.BONDB5,NO.BONDPU,TARIKHSELESAIPT,TARIKHSELESAIPY,TARIKHSELESAIP.B,TARIKHSELESAIPI,TARIKHSELESAIPNS,TARIKHSELESAIB2,TARIKHSELESAIB3,TARIKHSELESAIB4,TARIKHSELESAIB5,TARIKHSELESAIPU,MODALYA,MODALSYER,PTPOKOK,PTUNTUNG,PYPOKOK,PYUNTUNG,PBPOKOK,PBUNTUNG,PIPOKOK,PIUNTUNG,NSPOKOK,B2POKOK,B2UNTUNG,B3POKOK,B3UNTUNG,B4POKOK,B4UNTUNG,B5POKOK,B5UNTUNG,P.UUMRAH,P.UUNTUNG,JUMLAH,CATATAN,POTLEPAS,BEZA';
	$gheader = explode(",",$header);
	print '<table border="1"  style="color: black; margin-left: 20px"><tr>';
	for($g=0;$g<=50;$g++){
		print '<td style="font-size: x-small">'.$gheader[$g].'</td>';
	}
	print '</tr>';
}

function printFooter(){
	print '</table>';
}

function dbField(){
	$strval = 'bil,no_ahli,nama,jab_caw,kod_caw,no_pekerja,bond_pt,bond_py,bond_pb,bond_pi,bond_ns,bond_b2,bond_b3,bond_b4,bond_b5,bond_pu,selesai_pt,selesai_py,selesai_pb,selesai_pi,selesai_pns,selesai_b2,selesai_b3,selesai_b4,selesai_b5,selesai_pu,modal_ya,modal_syer,pt_pokok,pt_untung,py_pokok,py_untung,pb_pokok,pb_untung,pi_pokok,pi_untung,ns_pokok,b2_pokok,b2_untung,b3_pokok,b3_untung,b4_pokok,b4_untung,b5_pokok,b5_untung,pu_umrah,pu_untung,jumlah,catatan,pot_lepas,beza';

	return explode(",",$strval);
}

function quote_explode( $delim, $string, $encap = '"' ) {
       $parts = explode( $delim, $string );
       $correctParts = array();
       $correctIndex = 0;
       $insideEncaps = false;
       foreach ( $parts as $part ) {
               $numEncaps = substr_count( $part, $encap );
               if ( !$insideEncaps ) {
                       switch ( $numEncaps ) {
                               case 1:
                                       $correctParts[$correctIndex] = str_replace( $encap, '', $part );
                                       $insideEncaps = true;
                                       break;
                               case 0:
                               case 2:
                                       $correctParts[$correctIndex++] = str_replace( $encap, '', $part );
                                       break;
                               default:
                                       //echo 'Found more than 2 encapsulations - this should not happen!';
                       }
               } else {
                       switch ( $numEncaps ) {
                               case 0:
                                       $correctParts[$correctIndex] .= $delim.str_replace( $encap, '', $part );
                                       break;
                               case 1:
                                       $correctParts[$correctIndex++] .= $delim.str_replace( $encap, '', $part );
                                       $insideEncaps = false;
                                       break;
                               default:
                                       //echo 'Found more than 2 encapsulations - this should not happen!';
                       }
               }
       }
       return $correctParts;
}

/*
$str = '';
$str = explode(",",$str);
foreach($str as $value){
 print '<br>'.$value;
}*/
?>
