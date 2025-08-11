<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	importPot.php
 *          Date 		: 	28/06/2006
 *		   Programmer   :   mizal
 *		   Purpose      :   import/display data from csv file - loan deduction schedule
 *********************************************************************************/
include("header.php");

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2) {
	print '<script>parent.location.href = "index.php";</script>';
}

if (!isset($action)) $action = "";
if (!isset($dir)) 	 $dir = "importd/";
if (!isset($msg)) 	 $msg = "";

if (!isset($dth)) $dth	= date("d");
if (!isset($mth)) $mth	= date("n");
if (!isset($yr)) $yr	= date("Y");

$yrmth = sprintf("%04d%02d", $yr, $mth);
$opener = get_session("Cookie_userID");
$opendate = date("Y-m-d H:i:s");

$ListThisDir = $PATH_TRANSLATED;
$ListThisDir = str_replace(basename($PATH_TRANSLATED), "", $ListThisDir);
$ListThisDir = $ListThisDir . "importd/";

//--------------------------
$yr = substr($yrmth, 0, 4);
$mth = substr($yrmth, 4, 2);

//-----------------------------
$tranBy = get_session("Cookie_userName");
if (intval($mth) == 2) {
	$tranDate = "$yr-$mth-28 " . date("H:i:s");
} else {
	$tranDate = "$yr-$mth-$dth " . date("H:i:s");
}

if ($action == 'Import') {
	if ($fileimport == '') {
		print '	<script>
						alert("Masukkan nama file..");
						window.location = "?vw=importPot&mn=908";
					</script>';
		exit;
	}

	$docNo = dlookup("transaction", "ID", "docNo='" . substr($fileimport, 0, -4) . "'");
	if ($docNo <> '') {
		print '	<script>
						alert("File telah di import!");
						window.location = \'' . $PHP_SELF . '\';
					</script>';
		exit;
	}
}

if (!isset($_SERVER["PATH_TRANSLATED"]))
	$ServerDir = '';
else
	$ServerDir = $_SERVER["PATH_TRANSLATED"];
$ServerDir = str_replace(basename($ServerDir), "", $ServerDir);
$ServerDir = $ServerDir . 'importd/';

print '
<form name="FrmImport" action="?vw=importPot&mn=908&dir=' . $dir . '" method="post" enctype="multipart/form-data">
<input type="hidden" name="action">
<table border="0" cellspacing="1" cellpadding="3" width="95%" align="center">

	<tr>
		<td><br>
		<table border="0" cellpadding="3" cellspacing="1" width="80%" align="center" class="table table-striped">
			<tr class="table-primary">
				<td class=Header>Import Fail Potongan</td>
			</tr>
			<tr class="table-light">
				<td class="Data">
					<table width="100%">
						<!--tr><td class="textFont">Sila pilih fail :
							<input type="file" name="uploadFile" size="15" value="">&nbsp;
							<input type="text" name="fileimport" size="25" value="' . $uploadFile_name . '">
							<input type="button" class="but" value="Pilih" onclick="window.open(\'greFilePreview.php\',\'sel\',\'top=100,left=100,width=800,height=800,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');"><input type="submit" name="action"  value="Semak" size="50" class="but">
							</td>
						</tr-->
						<tr><td class="textFont" align="center">
							&nbsp;Pilih Jenis 
							<select name="jenis" class="form-select-sm">';
$arrJenis = array('Yuran & Syer & Simpanan Khas', 'Pembiayaan', 'Dividen');
for ($j = 0; $j < 3; $j++) {
	print '<option value="' . $j . '"';
	if ($jenis == $j) print 'selected';
	print '>Potongan ' . $arrJenis[$j];
}

print '</select>&nbsp;Pilih Hari
							<select name="dth" class="form-select-sm">';
for ($j = 1; $j < 32; $j++) {
	print '			<option value="' . $j . '"';
	if ((intval($dth)) == $j) print 'selected';
	print 			'>' . $j;
}

print '</select>&nbsp;Pilih Bulan 
							<select name="mth" class="form-select-sm">';
for ($j = 1; $j < 13; $j++) {
	print '			<option value="' . $j . '"';
	if ((intval($mth)) == $j) print 'selected';
	print 			'>' . $j;
}

print '</select>&nbsp;&nbsp; Tahun&nbsp;&nbsp;<input type="text" name="yr" size="5" maxlength="4" value="' . $yr . '" class="form-control-sm">&nbsp;<input type="text" name="fileimport" size="25" value="' . $uploadFile_name . '" class="form-control-sm">
							</td>
						</tr>						
						<tr>
							<td class="textFont" align="center">
							&nbsp;
							<input type="hidden" name="yrmth" value="' . $yrmth . '" class="form-control-sm">&nbsp;
							<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="window.open(\'greFilePreview.php\',\'sel\',\'top=100,left=100,width=800,height=800,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">&nbsp;<input type="submit" name="action"  value="Import" size="50" class="btn btn-sm btn-primary"
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

if ($action == 'Import' && ($jenis == 0 || $jenis == 1 || $jenis == 2)) {
	$docNo = $fileimport;
	$fileimport = $ServerDir . $fileimport;
	if (substr($fileimport, -3, 3) == "csv" || substr($fileimport, -3, 3) == "CSV") {
		$filename = $fileimport;
		$fp = fopen($filename, "r");
		$tot = 0;
		$totAmt = 0;
		$totInt = 0;
		$baseDocNo = substr($docNo, 0, -4);
		$cnt = 1;
		$objCode = array();
		$arrID = array();
		$j = 0;

		while (!feof($fp)) {
			$buffer = fgets($fp, 4096);
			$data = quote_explode(",", $buffer, $encap = '"');

			if ($jenis == 2) {
				$userID	= dlookup("userdetails", "userID", "memberID=" . tosql(intval($data[0]), "Text"));
				if ($userID) {
					if ($data[1] <> 0) transpay(1780, 1, $userID, $data[1], 0);
				} else {
					if (intval($data[0]) < 1 && (floatval($data[1]) > 0 || floatval($data[2]) > 0)) $data[0] = "NOID";
					elseif (intval($data[0]) < 1) continue;
					$sSQL = "";
					$sSQL	= "INSERT INTO importLog (" .
						"userID, " .
						"fee, " .
						"share, " .
						"remark, " .
						"createdBy, " .
						"createdDate) " .
						" VALUES (" .
						"'" . $data[0] . "', " .
						"'" . floatval($data[1]) . "', " .
						"'" . floatval($data[2]) . "', " .
						"'" . $docNo . "', " .
						"'" . $opener . "', " .
						"'" . $opendate  . "') ";
					$sSQL . '<br />';
					$rs = &$conn->Execute($sSQL);
				}
			}


			if ($jenis == 0) {
				$userID	= dlookup("userdetails", "userID", "memberID=" . tosql(intval($data[0]), "Text"));
				if ($userID) {
					if ($data[1] <> 0) transpay(1595, 1, $userID, $data[1], 0);
					if ($data[2] <> 0) transpay(1596, 1, $userID, $data[2], 0);
					if ($data[3] <> 0) transpay(1963, 1, $userID, $data[3], 0);
				} else {
					if (intval($data[0]) < 1 && (floatval($data[1]) > 0 || floatval($data[2]) > 0 || floatval($data[3]) > 0)) $data[0] = "NOID";
					elseif (intval($data[0]) < 1) continue;
					$sSQL = "";
					$sSQL	= "INSERT INTO importLog (" .
						"userID, " .
						"fee, " .
						"share, " .
						"khas, " .
						"remark, " .
						"createdBy, " .
						"createdDate) " .
						" VALUES (" .
						"'" . $data[0] . "', " .
						"'" . floatval($data[1]) . "', " .
						"'" . floatval($data[2]) . "', " .
						"'" . floatval($data[3]) . "', " .
						"'" . $docNo . "', " .
						"'" . $opener . "', " .
						"'" . $opendate  . "') ";
					$sSQL . '<br />';
					$rs = &$conn->Execute($sSQL);
				}
			} elseif ($jenis == 1) {


				$userID	= dlookup("userdetails", "userID", "memberID=" . tosql(intval($data[0]), "Text"));

				if ($data[0] == "MEMBER" && $cnt == 1) {
					$objCode[0] = $data[0];
					$tot = count($data) - 1;
					$part = $tot / 3;
					//--------
					for ($i = 0; $i < $part; $i++) {
						$j = $i * 3 + 1;
						$objCode[$j] = $data[$j];
						$j = $j + 1;
						$objCode[$j] = dlookup("general", "ID", "code= '" . trim($data[$j]) . "'");
						$j = $j + 1;
						$objCode[$j] = dlookup("general", "ID", "code= '" . trim($data[$j]) . "'");
					}
					//-------- end for
				}

				if ($userID) {
					for ($i = 0; $i < $part; $i++) {
						//echo "<br>i $i - j $j";
						$j = $i * 3 + 1;
						$bond = $data[$j];
						$j = $j + 1;
						if ($data[$j] <> 0) transpay($objCode[$j], 1, $bond, $data[$j]);
						$j = $j + 1;
						if ($data[$j] <> 0) transpay($objCode[$j], 1, $bond, $data[$j]);
					}
				} else {
					if (intval($data[0]) < 1 && (floatval($data[1]) > 0 || floatval($data[2]) > 0)) $data[0] = "NOID";
					elseif (intval($data[0]) < 1) continue;
					$sSQL = "";
					$sSQL	= "INSERT INTO importLog (" .
						"userID, " .
						"fee, " .
						"share, " .
						"remark, " .
						"createdBy, " .
						"createdDate) " .
						" VALUES (" .
						"'" . $data[0] . "', " .
						"'" . floatval($data[1]) . "', " .
						"'" . floatval($data[2]) . "', " .
						"'" . $docNo . "', " .
						"'" . $opener . "', " .
						"'" . $opendate  . "') ";
					$sSQL . '<br />';
					//$rs = &$conn->Execute($sSQL);
				}
				++$cnt;
			}
		} //end while
		fclose($fp);
		if ($jenis == 0) $str = 'yuran';
		else $str = 'Pembiayaan';
		print '	<script>
						alert("Import fail potongan ' . $str . ' berjaya!");
						window.location = \'' . $PHP_SELF . '\';
					</script>';
	} else {
		print '	<script>
					alert("File ' . $docNo . ' not allowed to verify / import...!");
					window.location = \'' . $PHP_SELF . '\';
				</script>';
	} //end allow csv
	print '<hr size="1">';
}


print '</font></td></tr></table>';
include("footer.php");


function getLoanPay(&$data)
{
	for ($i = 6; $i < 16; $i++) {
		if ($data[$i]) {
			$bond = addslashes(trim($data[$i]));
			//p. TUNAI PIN1-- P. YURAN  ---P BARANGAN  --	P. INSURAN	-- PNS PIN8 --	
			//P BARANGAN 2 PB02 --P BARANGAN 3 PB03	--P BARANGAN 4 PB04 --	P BARANGAN 5 PB05	--P UMRAH PU01
			switch ($i) {
				case 6: //pin1 tunai
					$pymtAmt = $data[28];
					$uAmt = $data[29];
					$deductID1 = 1539;
					$deductID2 = 1642;
					break;
				case 7: //yuran
					$pymtAmt = $data[30];
					$uAmt = $data[31];
					$deductID1 = 1626;
					$deductID2 = 1651;
					break;
				case 8: //brg 1 
					$pymtAmt = $data[32];
					$uAmt = $data[33];
					$deductID1 = 1613;
					$deductID2 = 1649;
					break;
				case 9: //ins
					$pymtAmt = $data[34];
					$uAmt = $data[35];
					$deductID1 = 1619;
					$deductID2 = 1652;
					break;
				case 10: //pin8 - n. sek
					$pymtAmt = $data[36];
					$uAmt = 0.0;
					$deductID1 = 1614;
					$deductID2 = 1653;
					break;
				case 11: //pb02 - brgn 2
					$pymtAmt = $data[37];
					$uAmt = $data[38];
					$deductID1 = 1622;
					$deductID2 = 1656;
					break;
				case 12: //pb03 brgn 3
					$pymtAmt = $data[39];
					$uAmt = $data[40];
					$deductID1 = 1623;
					$deductID2 = 1657;
					break;
				case 13: //pb04 brgn 4
					$pymtAmt = $data[41];
					$uAmt = $data[42];
					$deductID1 = 1624;
					$deductID2 = 1658;
					break;
				case 14: //pb05 brgn 5
					$pymtAmt = $data[43];
					$uAmt = $data[44];
					$deductID1 = 1625;
					$deductID2 = 1659;
					break;
				case 15: //pu01 umrah
					$pymtAmt = $data[45];
					$uAmt = $data[46];
					$deductID1 = 1631;
					$deductID2 = 1654;
					break;
			} //end switch
			//echo "<br> $data[1] : $bond - $pymtAmt - $cajAmt <br>";
			if ($pymtAmt) transpay($deductID1, 1, $bond, $pymtAmt, 0.0);
			if ($uAmt) transpay($deductID2, 1, $bond, $uAmt, 0.0);
		} else {
			//
		}
	} //end for
}

function getPay($userID, $pymtAmt, $type)
{
	if ($type == 1) {
		$deductID = 1595;
		$pymtAmt = $data[26];
	} elseif ($type == 2) {
		$deductID = 1596;
		$pymtAmt = $data[27];
	} else {
		$deductID = 1963;
		$pymtAmt = $data[28];
	}
	if ($pymtAmt <> 0) transpay($deductID, 0, $userID, $pymtAmt, 0);
}

function transpay($deductID, $addminus, $pymtRefer, $pymtAmt)
{
	global $conn;
	global $docNo;
	global $yrmth, $userID, $HTTP_COOKIE_VARS;
	global $baseDocNo;
	global $tranDate, $tranBy;

	$sSQL = "";
	$sSQL	= "INSERT INTO transaction (" .
		"docNo," .
		"userID," .
		"yrmth," .
		"deductID," .
		"transID," .
		"addminus," .
		"pymtID," .
		"pymtRefer," .
		"pymtAmt," .
		"createdDate," .
		"createdBy," .
		"updatedDate," .
		"updatedBy)" .
		" VALUES (" .
		tosql($baseDocNo, "Text") . "," .
		tosql($userID, "Text") . "," .
		tosql($yrmth, "Text") . "," .
		tosql($deductID, "Number") . "," .
		tosql(80, "Number") . "," .
		tosql($addminus, "Text") . "," .
		tosql(116, "Number") . "," .
		tosql($pymtRefer, "Text") . "," .
		tosql($pymtAmt, "Number") . "," .
		tosql($tranDate, "Text") . "," .
		tosql($tranBy, "Text") . "," .
		tosql($tranDate, "Text") . "," .
		tosql($tranBy, "Text") . ")";
	$rs = &$conn->Execute($sSQL);
}

function quote_explode($delim, $string, $encap = '"')
{
	$parts = explode($delim, $string);
	$correctParts = array();
	$correctIndex = 0;
	$insideEncaps = false;
	foreach ($parts as $part) {
		$numEncaps = substr_count($part, $encap);
		if (!$insideEncaps) {
			switch ($numEncaps) {
				case 1:
					$correctParts[$correctIndex] = str_replace($encap, '', $part);
					$insideEncaps = true;
					break;
				case 0:
				case 2:
					$correctParts[$correctIndex++] = str_replace($encap, '', $part);
					break;
				default:
					//echo 'Found more than 2 encapsulations - this should not happen!';
			}
		} else {
			switch ($numEncaps) {
				case 0:
					$correctParts[$correctIndex] .= $delim . str_replace($encap, '', $part);
					break;
				case 1:
					$correctParts[$correctIndex++] .= $delim . str_replace($encap, '', $part);
					$insideEncaps = false;
					break;
				default:
			}
		}
	}
	return $correctParts;
}
