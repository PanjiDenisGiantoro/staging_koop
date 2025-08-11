<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	importPot.php
*          Date 		: 	28/06/2006
*		   Programmer   :   mizal
*		   Purpose      :   import/display data from csv file - loan deduction schedule

Manual import potongan bulanan anggota

1. Buka fail lotus yang mempunyai senarai potongan gaji anggota, dalam senarai terdapat no. anggota, kandungan nama, modal yuran, modal syer.
2. Buat satu fail baru yang hanya mempunyai no. anggota, potongan yuran dan potongan syer.
3. Senarai tidak perlu mempunyai tajuk kepala contoh
4. Hanya mempunyai tiga lajur sahaja
5. Lajur pertama no. anggota
6. Lajur kedua potongan yuran
7. Lajur ketiga potongan syer
8. Simpan sebagai fail csv
9. Beri nama fail mengikut potongan bulan
10. Contoh fail bulan sembilan.
11. Mulakan nama fail POTYURAN
12. Kemudian Tahun, Bulan
11. POTBULAN200609
12. Selepas simpan sebagai csv akan menjadi POTBULAN200609.csv
13. Upload dahulu fail kedalam sistem.
14. Pilih Transaksi->Import dari fail potongan->Klik Pilih->Klik upload fail->Browse file->Klik OK
15. Klik nama fail yang diimport sebentar tadi.
16. Pilih jenis potongan yuran->Pilih bulan potongan dan masukkan tahun.
17. Klik butang import.
*********************************************************************************/
include ("header.php");
include("koperasiQry.php");
	
if (get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2) {
	print '<script>parent.location.href = "index.php";</script>';
}
//$title     = 'Import Fail Potongan';

if (!isset($action)) $action = "";
if (!isset($dir)) 	 $dir = "importdSpsn/";
if (!isset($msg)) 	 $msg = "";
if (!isset($mth)) $mth	= date("n");                 		
if (!isset($yr)) $yr	= date("Y");
$yrmth = sprintf("%04d%02d", $yr, $mth);
$opener = get_session("Cookie_userID");
$opendate = date("Y-m-d H:i:s");    

$ListThisDir = $PATH_TRANSLATED;
$ListThisDir = str_replace(basename($PATH_TRANSLATED), "", $ListThisDir);
$ListThisDir = $ListThisDir . "importdSpsn/";

//--------------------------
$yr = substr($yrmth, 0,4);
$mth = substr($yrmth, 4,2);
//-----------------------------
$tranBy = get_session("Cookie_userName");
if(intval($mth) == 2){
	$tranDate = "$yr-$mth-28 ".date("H:i:s");
}else{
	$tranDate = "$yr-$mth-30 ".date("H:i:s");
}

if ($action == 'Import') {
	if ($fileimport == '') {
			print '	<script>
						alert("Masukkan nama file..");
						window.location = "importPotSpsn.php";
					</script>';
					exit;
	}

	 $docNo = dlookup("importSPSN", "ID", "docNo='" . substr($fileimport, 0, -4) . "'");
	 if($docNo <> ''){
			print '	<script>
						alert("File telah di import!");
						window.location = \''.$PHP_SELF.'\';
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
$ServerDir = $ServerDir.'importdSpsn/';

//<form  action="greImportPot.php" method="post">
//	<tr>
//		<td	 colspan="2"><b class="maroonText">' . strtoupper($title) . '</b></td>
//	</tr>
print '
<form name="FrmImport" action="importPotSpsn.php?dir='.$dir.'" method="post" enctype="multipart/form-data">
<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="3" width="95%" align="center">

	<tr>
		<td><br>
		<table border="0" cellpadding="3" cellspacing="1" width="80%" align="center" class="lineBG">
			<tr>
				<td class=Header>Import Fail Potongan Spsn :</td>
			</tr>
			<tr>
				<td class="Data">
					<table width="100%">
						<!--tr><td class="textFont">Sila pilih fail :
							<input type="file" name="uploadFile" size="15" value="">&nbsp;
							<input type="text" name="fileimport" size="25" value="'.$uploadFile_name.'">
							<input type="button" class="but" value="Pilih" onclick="window.open(\'greFilePreviewSpsn.php\',\'sel\',\'top=100,left=100,width=600,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');"><input type="submit" name="action"  value="Semak" size="50" class="but">
							</td>
						</tr-->
						<tr><td class="textFont" align="center">
							&nbsp;Pilih Jenis : 
							<select name="jenis" class="data">';
							$arrJenis = array('SPSN');
							for ($j = 0; $j < 1; $j++) {
								print '<option value="'.$j.'"';
								if ($jenis == $j) print 'selected';
								print '>Potongan PGB '.$arrJenis[$j];
							}
							print '</select>&nbsp;Pilih Bulan : 
							<select name="mth" class="data">'; 
							// onchange="document.FrmImport.submit();"
							for ($j = 1; $j < 13; $j++) {
								print '			<option value="'.$j.'"';
								if ((intval($mth)) == $j) print 'selected';
								print 			'>'.$j;
							}
							print '</select>&nbsp;&nbsp; Tahun :&nbsp;&nbsp;<input type="text" name="yr" size="5" maxlength="4" value="'.$yr.'" class="data">&nbsp;<input type="text" name="fileimport" size="25" value="'.$uploadFile_name.'">
							</td>
						</tr>						
						<tr>
							<td class="textFont" align="center">
							&nbsp;
							<input type="hidden" name="yrmth"  value="'.$yrmth.'"><!--input type="submit" name="action"  value="Papar" size="50" class="but"-->&nbsp;
							<input type="button" class="but" value="Pilih" onclick="window.open(\'greFilePreviewSPSN.php\',\'sel\',\'top=100,left=100,width=600,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;<input type="submit" name="action"  value="Import" size="50" class="but"
							onClick="if(!confirm(\'Adakah ada pasti untuk import file ini?\')) {return false} else {window.FrmImport.submit();};">
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
<tr><td>&nbsp;<font class="textFont">';


if($action == 'Import' && ( $jenis == 0 ) ) {
	$docNo = $fileimport;
	$fileimport = $ServerDir.$fileimport;
	if (substr($fileimport,-3,3) == "csv" || substr($fileimport,-3,3) == "CSV") {
		$filename = $fileimport;
		$fp = fopen($filename, "r");
		$tot = 0;
		$totAmt = 0;
		$totInt = 0;		
		$baseDocNo = substr($docNo, 0, -4);
		//printHeader();
		$cnt = 1;
		$objCode = array();
		$arrID =array();
		$j = 0;

		while (!feof($fp)) { 
		    $buffer = fgets($fp, 4096);
			$data = quote_explode( ",", $buffer, $encap = '"' );

			/*for($i=0;$i<=50;$i++){
			${$strval[$i]} = addslashes(trim($data[$i]));
			print '<td>'.$data[$i].'&nbsp;</td>';
			}*///end import
			//------------------------------------------------
			//print $data[1];
			
			
			//if($jenis == 0) {
				$userID	= dlookup("userdetails", "userID", "memberID=" . tosql(intval($data[0]), "Text"));
				if($userID) {
					if($data[1] > -1 ) transpay($userID, $data[1]);
					//if($data[2]<> 0) transpay(1596, 1, $userID, $data[2], 0);
				}else{
				if(intval($data[0]) < 1 && (floatval($data[1])>0 || floatval($data[2])>0)) $data[0] = "NOID";
				elseif(intval($data[0]) < 1) continue;
				$sSQL = "";
				$sSQL	= "INSERT INTO importLog (" . 
							"userID, " .
							"fee, " .
							"share, " .
							"remark, " .
							"createdBy, " .
							"createdDate) " .
							" VALUES (".
							"'". $data[0] . "', ".
							"'". floatval($data[1]) . "', ".
							"'". floatval($data[2]) . "', ".
							"'". $docNo . "', ".
							"'". $opener . "', ".
							"'". $opendate  . "') ";
				$sSQL.'<br />';
				$rs = &$conn->Execute($sSQL);
				}				
			//}
		//	}
		
		
		}//end while
		fclose ($fp);
		
		
		
			if($jenis==0) $str = 'SPSN';
			print '	<script>
						alert("Import fail potongan Spsn '.$str.' berjaya!");
						window.location = \''.$PHP_SELF.'\';
					</script>';
	} else {
		print '	<script>
					alert("File '.$docNo.' not allowed to verify / import...!");
					window.location = \''.$PHP_SELF.'\';
				</script>';
	}//end allow csv
	print '<hr size="1">';

}
print '</font></td></tr></table>';
include("footer.php");


function transpay($userID,$AmountSP){
global $conn;
global $docNo;
global $yrmth,$userID,$HTTP_COOKIE_VARS;
global $baseDocNo; // = basename($docNo, ".csv"); 
global $tranDate, $tranBy;
$NoStaff = dlookup("userdetails", "staftNo", "memberID=" . tosql(($userID), "text"));
           
		$sSQL = "";
		$sSQL	= "INSERT INTO importSPSN (" . 
          "userID," . 
		  "yrmth," .
		  "docNo," .			
          "AmountSP," . 
    	  "status," . 
		  "NoStaff," . 
          "updateDate," . 
          "createdDate)" . 
          " VALUES (" . 
          tosql($userID, "Number") . "," .
          tosql($yrmth, "Text") . "," .
		   tosql($docNo, "Text") . "," .
          tosql($AmountSP, "Number") . ",".
          tosql(1, "Number") . "," .
          tosql($NoStaff, "Text") . ",".
          tosql($tranDate, "Text") . "," .
          tosql($tranDate, "Text") . ")";
		//print '<br>'.$sSQL;
		$rs = &$conn->Execute($sSQL);
		//if(!$rs)	print '<br>'.$sSQL;
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
?>