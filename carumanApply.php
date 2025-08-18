<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	carumanApply.php
 *          Date 		: 	22/03/2024
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("forms.php");

date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}

if (get_session("Cookie_groupID") == 0) {
	$userID		= get_session('Cookie_userID');
	$memberID	= dlookup("userdetails", "memberID", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
	$userName	= get_session('Cookie_fullName');
	$newIC		= dlookup("userdetails", "newIC", "userID=" . tosql(get_session('Cookie_userID'), "Text"));
}

if (get_session("Cookie_groupID") == 0) {
	$ID = get_session("Cookie_userID");
	$sFileName		= "?vw=carumanApply&mn=1";
	$sActionFileName = "?vw=carumanApply&mn=1";
} else {
	$sFileName		= "?vw=carumanApply&mn=905";
	$sActionFileName = "?vw=carumanApply&mn=905";
}

$title     		= "Pengajuan Pengeluaran Iuran";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

//--- Prepare caruman type
$carumanList = array();
$carumanVal  = array();
$GetCaruman = "SELECT name AS carumanName, ID AS carumanID FROM general WHERE category = 'J' AND j_Aktif IN (1) ORDER BY ID ASC";
$rsCaruman = &$conn->Execute($GetCaruman);
if ($rsCaruman->RowCount() <> 0) {
	while (!$rsCaruman->EOF) {
		$id = $rsCaruman->fields(carumanID);
		$index = ($id == 1595) ? "1" : "2";
		// $nm = $rsCaruman->fields(carumanName);
		// $index = ($nm == "YURAN") ? "1" : (($nm == "SYER") ? "2" : "0");
		if ($id == 1595 || $id == 1596) {
			array_push($carumanList, $rsCaruman->fields(carumanName));
			array_push($carumanVal, $index);
		}
		$rsCaruman->MoveNext();
	}
}
//1 => YURAN, 2 => SYER

$a = 0;
$FormLabel[$a]   	= "Nomor Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a = $a + 1;
$FormLabel[$a]   	= "Nama Anggota";
$FormElement[$a] 	= "userName";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "No Kartu Identitas";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hiddentext";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "* Jenis";
$FormElement[$a] 	= "type";
$FormType[$a]	  	= "selectx";
$FormData[$a]   	= $carumanList;
$FormDataValue[$a]	= $carumanVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "2";
$FormLength[$a]  	= "2";

$a++;
$FormLabel[$a]   	= "Jumlah Pengeluaran (RP)";
$FormElement[$a] 	= "withdrawAmt";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->

$selectedJenis 	= $_POST['type'];
$sSQL 			= "SELECT * FROM usercaruman where type = '$selectedJenis' and userID = '$userID' and `status` not in (2,3) LIMIT 1";
$GetLimit 		= &$conn->Execute($sSQL);
$limit 			= $GetLimit->fields(ID);
$typeCaruman	= $GetLimit->fields('type');

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> "") {
	$selectedJenis = $_POST['type'];
	$sSQL = "";
	$sWhere = " a.userID = b.userID AND b.status <> 0";
	if ($ID) {
		$sWhere .= " AND b.userID = " . tosql($ID, "Text");
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT DISTINCT a.*, b.* FROM users a, userdetails b";
	$sSQL = $sSQL . $sWhere . " order by CAST( b.memberID AS SIGNED INTEGER ) desc";
	$GetMember = &$conn->Execute($sSQL);
	$totalFees = getFees($GetMember->fields(userID), $yr);
	$totalSharesTK = getSharesterkini($GetMember->fields(userID), $yr);
	// $totalDepo = getDepoKhasAll($GetMember->fields(userID), $yr);

	//--- Begin : Call function FormValidation ---  
	for ($i = 0; $i < count($FormLabel); $i++) {
		for ($j = 0; $j < count($FormCheck[$i]); $j++) {
			FormValidation(
				$FormLabel[$i],
				$FormElement[$i],
				$$FormElement[$i],
				$FormCheck[$i][$j],
				$i
			);
		}
	}
	//--- End   : Call function FormValidation ---  
	//--- BEGIN	: Checking member id ---
	if (get_session("Cookie_groupID") == 0) {
		$totalFees = $totalFees ? $totalFees : "0.00";
		$totalSharesTK = $totalSharesTK ? $totalSharesTK : "0.00";
		// $totalDepo = $totalDepo ? $totalSharesTK : "0.00";
	} else {
		$totalFees = isset($_POST["totalFees"]) ? $_POST["totalFees"] : "-";
		$totalSharesTK = isset($_POST["totalSharesTK"]) ? $_POST["totalSharesTK"] : "-";
		// $totalDepo = isset($_POST["totalDepo"]) ? $_POST["totalDepo"] : "-";
	}
	$totalFees = str_replace(',', '', $totalFees);
	$totalSharesTK = str_replace(',', '', $totalSharesTK);
	// $totalDepo = str_replace(',', '', $totalDepo);
	$withdrawAmt = str_replace(',', '', $withdrawAmt);

	if ($memberID <> "") {
		if (dlookup("userdetails", "userID", "memberID=" . tosql($memberID, "Text")) == "") {
			array_push($strErrMsg, 'memberID');
			print '- <font class=redText>Nomor Anggota - ' . $memberID . ' tidak wujud...!</font><br>';
			$userName = "";
			$newIC = "";
		} else {
			$userID = dlookup("userdetails", "userID", "memberID=" . tosql($memberID, "Text"));
			$userName 	= dlookup("users", "name", "userID=" . tosql($userID, "Text"));
			$newIC 	= dlookup("userdetails", "newIC", "userID=" . tosql($userID, "Text"));
		}
	}
	//--- END  	: Checking member id ---
	if ($selectedJenis == "1") { //yuran
		if ($withdrawAmt > $totalFees) {
			array_push($strErrMsg, 'withdrawAmt');
			print '<div class="text-danger">Withdrawal amount cannot exceed ' . $totalFees . '</div>';
		} elseif ($withdrawAmt <= 0) {
			array_push($strErrMsg, 'withdrawAmt');
			print '<div class="text-danger">Withdrawal amount cannot be ' . $withdrawAmt . '</div>';
		} else {
			$withdrawAmt = $withdrawAmt;
		}
	} elseif ($selectedJenis == "2") { //syer
		if ($withdrawAmt > $totalSharesTK) {
			array_push($strErrMsg, 'withdrawAmt');
			print '<div class="text-danger">Withdrawal amount cannot exceed ' . $totalSharesTK . '</div>';
		} elseif ($withdrawAmt <= 0) {
			array_push($strErrMsg, 'withdrawAmt');
			print '<div class="text-danger">Withdrawal amount cannot be ' . $withdrawAmt . '</div>';
		} else {
			$withdrawAmt = $withdrawAmt;
		}
	}
	// elseif ($selectedJenis == "2") {
	// 	if ($withdrawAmt > $totalDepo) {
	// 		array_push($strErrMsg, 'withdrawAmt');
	// 		print '<div class="text-danger">Withdrawal amount cannot exceed ' . $totalDepo . '</div>';	
	// 	} elseif ($withdrawAmt <= 0) {
	// 		array_push($strErrMsg, 'withdrawAmt');
	// 		print '<div class="text-danger">Withdrawal amount cannot be ' . $withdrawAmt . '</div>';	
	// 	} else {
	// 		$withdrawAmt = $withdrawAmt;
	// 	}
	// }

	if ($selectedJenis) {
		if ($limit != null) {
			array_push($strErrMsg, 'limit');
			print '<div class="text-danger">Anggota ini terdapat permohonan ' . $carumanTypeList[$typeCaruman] . ' yang belum diproses</div>';
		}
	}

	if (count($strErrMsg) == "0") {
		$applyDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sSQL	= "INSERT INTO usercaruman (" .
			"userID," .
			"applyDate," .
			"withdrawAmt," .
			"type)" .
			" VALUES (" .
			tosql($userID, "Text") . "," .
			tosql($applyDate, "Text") . "," .
			tosql($withdrawAmt, "Text") . "," .
			tosql($type, "Text") . ")";
		$rs = &$conn->Execute($sSQL);
		print '<script>
					alert ("Permohonan Pengeluaran Caruman telah didaftarkan ke dalam sistem.");
					window.location.href="' . $sActionFileName . '";
				</script>';
	}
}

//--- End   : Form Validation Field / Add / Update ---------------------------------------------->
print '
<h4 class="card-title">' . strtoupper($title) . '</h4>

<form name="MyForm" action= ' . $sFileName . ' method=post>
<input type="hidden" name="userID" value="' . $userID . '">
';

if (get_session("Cookie_groupID") == 0) {
	$uid = get_session('Cookie_userID');
	$pk	= dlookup("usercaruman", "ID", "userID=" . tosql($uid, "Text"));
}

// if($pk) {
/* 
    <div class="">
                            <div class="alert alert-success mb-0" role="alert">
                                <h4 class="alert-heading font-size-18">Makluman!</h4>
                                <p>Permohonan Pengeluaran Caruman Anggota Telah Dibuat.</p>
                            </div>
                        </div>

 */
// } else {
include("carumanStmtUser.php");

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 0; $i < count($FormLabel); $i++) {
	print '<div class="mb-2 row"><label class="col-md-2 col-form-label">' . $FormLabel[$i] . '</label>';
	if (in_array($FormElement[$i], $strErrMsg))
		print '<div class="col-md-8 bg-danger">';
	else
		print '<div class="col-md-8">';

	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	if ($FormElement[$i] == "withdrawAmt") {
		// Modify the HTML output for 'withdrawAmt' to adjust its size
		print '<input type="text" name="' . $FormElement[$i] . '" class="form-control form-control" value="' . $$FormElement[$i] . '" style="width: 150px;">';
	} else {
		// For other elements, use the standard FormEntry function
		$strFormValue = $$FormElement[$i];
		FormEntry(
			$FormLabel[$i],
			$FormElement[$i],
			$FormType[$i],
			$strFormValue,
			$FormData[$i],
			$FormDataValue[$i],
			$FormSize[$i],
			$FormLength[$i]
		);
	}

	if ($i == 0) {
		if (get_session("Cookie_groupID") == 1 || get_session("Cookie_groupID") == 2) {
			print '
			&nbsp;<input type="button" class="btn btn-sm btn-info" value="Pilih" onclick="window.open(\'selMemberCaruman.php\',\'sel\',\'top=10,left=10,width=950,height=500,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');">';
		}
	}

	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '</div></div>';
}
// }
// if(!$pk) {
print '
    <div class="mb-3 mt-3 row">
                                <label class="col-md-2 col-form-label"></label>
                                <div class="col-md-8">
								<input type="Submit" name="SubmitForm" class="btn btn-primary w-md waves-effect waves-light" value="Kirim">
                                </div>
                            </div>
';
// }

print '
</form>
</div>
';
include("footer.php");
