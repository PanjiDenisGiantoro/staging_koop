<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	
 *          Date 		: 	
 *********************************************************************************/
include("header.php");
include("koperasiList.php");
include("koperasiQry.php");
include("forms.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session('Cookie_userID') == "" or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); top.location="index.php";</script>';
}

$sFileName		= "memberUpdate.php";
$sActionFileName = "memberUpdate.php";
$title     		= "Perbarui Data Pembiayaan Anggota";

//--- Begin : Set Form Variables (you may insert here any new fields) ---------------------------->
//--- FormCheck  = CheckBlank, CheckNumeric, CheckDate, CheckEmailAddress
$strErrMsg = array();

//--- Prepare race type
$raceList = array();
$raceVal  = array();
$GetRace = ctGeneral("", "E");
if ($GetRace->RowCount() <> 0) {
	while (!$GetRace->EOF) {
		array_push($raceList, $GetRace->fields(name));
		array_push($raceVal, $GetRace->fields(ID));
		$GetRace->MoveNext();
	}
}

//--- Prepare religion type
$religionList = array();
$religionVal  = array();
$GetReligion = ctGeneral("", "F");
if ($GetReligion->RowCount() <> 0) {
	while (!$GetReligion->EOF) {
		array_push($religionList, $GetReligion->fields(name));
		array_push($religionVal, $GetReligion->fields(ID));
		$GetReligion->MoveNext();
	}
}

//--- Prepare state type
$stateList = array();
$stateVal  = array();
$GetState = ctGeneral("", "H");
if ($GetState->RowCount() <> 0) {
	while (!$GetState->EOF) {
		array_push($stateList, $GetState->fields(name));
		array_push($stateVal, $GetState->fields(ID));
		$GetState->MoveNext();
	}
}

/*/--- Prepare department type
$deptList = Array();
$deptVal  = Array();
$GetDept = ctGeneral("","B");
if ($GetDept->RowCount() <> 0){
	while (!$GetDept->EOF) {
//		array_push ($deptList, $GetDept->fields(name));
		array_push ($deptList, $GetDept->fields(code));
		array_push ($deptVal, $GetDept->fields(ID));
		$GetDept->MoveNext();
	}
}	
/*/
//--- Prepare department type
$deptList = array();
$deptVal  = array();
$GetDept = ctGeneral("", "B");
if ($GetDept->RowCount() <> 0) {
	while (!$GetDept->EOF) {
		array_push($deptList, $GetDept->fields(name));
		array_push($deptVal, $GetDept->fields(ID));
		$GetDept->MoveNext();
	}
}

//--- Prepare society
$societyList = array();
$societyVal  = array();
$GetSociety = ctGeneral("", "L");
if ($GetSociety->RowCount() <> 0) {
	while (!$GetSociety->EOF) {
		array_push($societyList, $GetSociety->fields(name));
		array_push($societyVal, $GetSociety->fields(ID));
		$GetSociety->MoveNext();
	}
}

//--- Prepare payment type
$pymtList = array();
$pymtVal  = array();
$GetPymt = ctGeneral("", "K");
if ($GetPymt->RowCount() <> 0) {
	while (!$GetPymt->EOF) {
		array_push($pymtList, $GetPymt->fields(name));
		array_push($pymtVal, $GetPymt->fields(ID));
		$GetPymt->MoveNext();
	}
}

$a = 1;
$FormLabel[$a]   	= "* Nama Lengkap";
$FormElement[$a] 	= "name";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "* No KTP Baru";
$FormElement[$a] 	= "newIC";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckNumeric);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "12";

$a++;
$FormLabel[$a]   	= "* Alamat";
$FormElement[$a] 	= "address";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "* Poskod";
$FormElement[$a] 	= "postcode";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckNumeric);
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "5";

$a++;
$FormLabel[$a]   	= "Bandar";
$FormElement[$a] 	= "city";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "* Negeri";
$FormElement[$a] 	= "stateID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $stateList;
$FormDataValue[$a]	= $stateVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Umur";
$FormElement[$a] 	= "umur";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Nomor Anggota";
$FormElement[$a] 	= "memberID";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Jawatan";
$FormElement[$a] 	= "job";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";


$a++;
$FormLabel[$a]   	= "Tanggal Menjadi Anggota";
$FormElement[$a] 	= "approvedDate";
$FormType[$a]	  	= "hiddenDate";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "10";

$a++;
$FormLabel[$a]   	= "Jabatan/Cawangan";
$FormElement[$a] 	= "departmentID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $deptList;
$FormDataValue[$a]	= $deptVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Syer bulanan";
$FormElement[$a] 	= "sahammonth";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";


$a++;
$FormLabel[$a]   	= "Nama Suami/isteri";
$FormElement[$a] 	= "spouse";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Pekerjaan";
$FormElement[$a] 	= "sjob";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Majikan";
$FormElement[$a] 	= "semployer";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Bil tanggungan";
$FormElement[$a] 	= "tangung";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "* Alamat Surat Menyurat";
$FormElement[$a] 	= "addressSurat";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "* Poskod";
$FormElement[$a] 	= "postcodeSurat";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckNumeric);
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "5";

$a++;
$FormLabel[$a]   	= "Bandar";
$FormElement[$a] 	= "citySurat";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "* Negeri";
$FormElement[$a] 	= "stateIDSurat";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $stateList;
$FormDataValue[$a]	= $stateVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Bil. sekolah";
$FormElement[$a] 	= "totsek";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "25";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Kod Pinjaman";
$FormElement[$a] 	= "loanCode";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "20";

$a++;
$FormLabel[$a]   	= "Caj Pinjaman (%)";
$FormElement[$a] 	= "loanCaj";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "2";

$a++;
$FormLabel[$a]   	= "Proses Pinjaman";
$FormElement[$a] 	= "proPinjam";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "Overlapping";
$FormElement[$a] 	= "overlap";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "Jangka Waktu Pembayaran";
$FormElement[$a] 	= "loanPeriod";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "Jumlah Pinjaman";
$FormElement[$a] 	= "loanAmt";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank, CheckDecimal);
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "Bayar Balik (Bulan)";
$FormElement[$a] 	= "monthlyPymt";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "20";
$FormLength[$a]  	= "13";

$a++;
$FormLabel[$a]   	= "Bil bulan potongan awal";
$FormElement[$a] 	= "earlyMonth";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "5";
$FormLength[$a]  	= "2";

$a++;
$FormLabel[$a]   	= "Ada Pinjaman Rumah?";
$FormElement[$a] 	= "houseLoan";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= array('Tak Ada', 'Ada');
$FormDataValue[$a]	= array('0', '1');
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Bentuk Pembayaran";
$FormElement[$a] 	= "paymentID";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $pymtList;
$FormDataValue[$a]	= $pymtVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Tujuan Pinjaman";
$FormElement[$a] 	= "purpose";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]	  	= "Catatan";
$FormElement[$a] 	= "catatan";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

$a++;
$FormLabel[$a]   	= "I. Pendapatan";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "II. Perbelanjaan";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Gaji";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Saraan Keluarga";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Elaun";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Pelajaran";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Gaji suami/isteri";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Pinjaman";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Elaun";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "i) Perumahan";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Lain-lain
";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "ii) Kereta";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "i)";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "iii) Lain-lain";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "ii)";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "EPF/Cukai/Socso";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jumlah";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";


//55
$a++;
$FormLabel[$a]	  	= "Jumlah kelayakan";
$FormElement[$a] 	= "updatedBy";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Bulan gaji";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jumlah yuran berbayar";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "i) Jumlah";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]	  	= "Tanggungan";
$FormElement[$a] 	= "updatedBy";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Pembiayaan sedia ada";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jumlah";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "iii) Jumlah Tangungan yang dibenarkan";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "iv) Pembiayaan dipohon";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "v) Anggaran Ansuran Bulanan Pembiayaan ini";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "vi) 50% / 75% dari gaji";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

//
$a++;
$FormLabel[$a]   	= "Bulan gaji";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Yuran terkumpul";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "jumlah";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]	  	= "Tanggungan";
$FormElement[$a] 	= "updatedBy";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Pembiayaan sedia ada";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Tanggungan dibenarkan";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "i-ii)";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

//
$a++;
$FormLabel[$a]   	= "Bulan gaji";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Yuran terkumpul";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "jumlah";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]	  	= "Tanggungan";
$FormElement[$a] 	= "updatedBy";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Pembiayaan sedia ada";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Tanggungan dibenarkan";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "i-ii)";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

//

$a++;
$FormLabel[$a]   	= "Bulan gaji";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Yuran terkumpul";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "jumlah";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]	  	= "Tanggungan";
$FormElement[$a] 	= "updatedBy";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Pembiayaan sedia ada";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Jaminan ke atas";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "Tanggungan dibenarkan";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

$a++;
$FormLabel[$a]   	= "i-ii)";
$FormElement[$a] 	= "";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "10";
$FormLength[$a]  	= "15";

//
$a++;
$FormLabel[$a]  	= "Tanggal Pengajuan";
$FormElement[$a] 	= "applyDate";
$FormType[$a]  		= "hiddenDate";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]  	= "Status";
$FormElement[$a] 	= "status";
$FormType[$a]  		= "hidden";
$FormData[$a]    	= $statusList;
$FormDataValue[$a]	= $statusVal;
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]  	= "Tarikh Diluluskan";
$FormElement[$a] 	= "approvedDate";
$FormType[$a]  		= "hiddenDate";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]  	= "Tarikh Ditolak";
$FormElement[$a] 	= "rejectedDate";
$FormType[$a]  		= "hiddenDate";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]  	= "Tarikh Kemaskini";
$FormElement[$a] 	= "updatedDate";
$FormType[$a]  		= "hiddenDate";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]	  	= "Kemaskini Oleh";
$FormElement[$a] 	= "updatedBy";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]	  	= "Catatan";
$FormElement[$a] 	= "remark";
$FormType[$a]	  	= "hidden";
$FormData[$a]    	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "&nbsp;";
$FormElement[$a] 	= "test";
$FormType[$a]	  	= "hidden";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

//--- End   :Set the listing list (you may insert here any new listing) -------------------------->
//$conn->debug=1;
$pk = get_session('Cookie_userID');
$strMember = "SELECT a . * , b . * FROM users a, userdetails b WHERE a.userID = '" . $pk . "' AND a.userID = b.userID";
$GetMember = &$conn->Execute($strMember);

//--- Begin : Form Validation Field / Add / Update ---------------------------------------------->
if ($SubmitForm <> "") {
	//--- Begin : Call function FormValidation ---  
	for ($i = 1; $i <= count($FormLabel); $i++) {
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
	$memberDate = substr($memberDate, 6, 4) . '-' . substr($memberDate, 3, 2) . '-' . substr($memberDate, 0, 2);
	$dateBirth = substr($dateBirth, 6, 4) . '-' . substr($dateBirth, 3, 2) . '-' . substr($dateBirth, 0, 2);
	$dateStarted   = substr($dateStarted, 6, 4) . '-' . substr($dateStarted, 3, 2) . '-' . substr($dateStarted, 0, 2);
	if (count($strErrMsg) == "0") {
		$updatedBy 	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL = "";
		$sWhere = "";
		$sWhere = "userID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE users SET " .
			"name=" . tosql($name, "Text") .
			",email=" . tosql($email, "Text") .
			",updatedDate=" . tosql($updatedDate, "Text") .
			",updatedBy=" . tosql($updatedBy, "Text");
		$sSQL = $sSQL . $sWhere;
		//		print $sSQL.'<br>';
		$rs = &$conn->Execute($sSQL);
		$activity = "Mengemaskini Maklumat Anggota - " . $pk;
		if ($rs) activityLog($sSQL, $activity, get_session('Cookie_userID'), get_session("Cookie_userName"), 1);

		//	if ($address <> "") $address = '<pre>'.$address.'</pre>';
		//if ($w_address <> "") $w_address = '<pre>'.$w_address.'</pre>';
		//	if ($w_address1 <> "") $w_address1 = '<pre>'.$w_address1.'</pre>';
		//	if ($w_address2 <> "") $w_address2 = '<pre>'.$w_address2.'</pre>';
		///	if ($w_address3 <> "") $w_address3 = '<pre>'.$w_address3.'</pre>';
		$sSQL = "";
		$sWhere = "";
		$sWhere = "userID=" . tosql($pk, "Text");
		$sWhere = " WHERE (" . $sWhere . ")";
		$sSQL	= "UPDATE userdetails SET " .
			" memberID=" . tosql($memberID, "Text") .
			//     ", memberDate=" . tosql($memberDate, "Text").
			//     ", staftNo=" . tosql($staftNo, "Text").
			", picture=" . tosql($picture, "Text") .
			", newIC=" . tosql($newIC, "Text") .
			", dateBirth=" . tosql($dateBirth, "Text") .
			", sex=" . tosql($sex, "Number") .
			", raceID=" . tosql($raceID, "Number") .
			", religionID=" . tosql($religionID, "Number") .
			", maritalID=" . tosql($maritalID, "Number") .
			", job=" . tosql($job, "Text") .
			// 	  ", accTabungan=" . tosql($accTabungan, "Number").
			", grossPay=" . tosql($grossPay, "Number") .
			", address=" . tosql($address, "Text") .
			", city=" . tosql($city, "Text") .
			", postcode=" . tosql($postcode, "Text") .
			", stateID=" . tosql($stateID, "Number") .
			", homeNo=" . tosql($homeNo, "Number") .
			", mobileNo=" . tosql($mobileNo, "Number") .
			", addressSurat=" . tosql($addressSurat, "Text") .
			", citySurat=" . tosql($citySurat, "Text") .
			", postcodeSurat=" . tosql($postcodeSurat, "Text") .
			", stateIDSurat=" . tosql($stateIDSurat, "Number") .
			", departmentID=" . tosql($departmentID, "Number") .
			//   ", totPay=" . tosql($totPay, "Number").
			/*  user cant update this
		          ", w_name1=" . tosql($w_name1, "Text").
				  ", w_ic1=" . tosql($w_ic1, "Text").
		          ", w_relation1=" . tosql($w_relation1, "Text").
		          ", w_address1=" . tosql($w_address1, "Text").
		          ", w_contact1=" . tosql($w_contact1, "Text").
		          ", w_name2=" . tosql($w_name2, "Text").
		          ", w_ic2=" . tosql($w_ic2, "Text").
		          ", w_relation2=" . tosql($w_relation2, "Text").
		          ", w_address2=" . tosql($w_address2, "Text").
		          ", w_contact2=" . tosql($w_contact2, "Text").
		          ", w_name3=" . tosql($w_name3, "Text").
		          ", w_ic3=" . tosql($w_ic3, "Text").
		          ", w_relation3=" . tosql($w_relation3, "Text").
		          ", w_address3=" . tosql($w_address3, "Text").
		          ", w_contact3=" . tosql($w_contact3, "Text"). 
		          ", saksi1=" . tosql($saksi1, "Text").
		          ", saksiIC1=" . tosql($saksiIC1, "Text").
		          ", saksi2=" . tosql($saksi2, "Text").
		          ", saksiIC2=" . tosql($saksiIC2, "Text").*/
			", updatedDate=" . tosql($updatedDate, "Text") .
			", updatedBy=" . tosql($updatedBy, "Text");
		$sSQL = $sSQL . $sWhere;
		$rs = &$conn->Execute($sSQL);
		$activity = "Mengemaskini Maklumat Anggota - " . $pk;
		if ($rs) activityLog($sSQL, $activity, get_session('Cookie_userID'), get_session("Cookie_userName"), 1);
		print '<script>
					alert ("Maklumat anggota telah dikemaskinikan ke dalam sistem.");
					window.location.href = "' . $sActionFileName . '";
				</script>';
	}
}
//--- End   : Form Validation Field / Add / Update ---------------------------------------------->

print '
<form name="MyForm" action=' . $sFileName . ' method=post>
<input type="hidden" name="picture" value="' . $pic . '">
<table border="0" cellpadding="3" cellspacing="0" width="100%" align="center" class="lineBG">
	<tr>
		<td colspan="4" class="Data"><b class="maroonText">' . strtoupper($title) . '</b></td></tr>';

//--- Begin : Looping to display label -------------------------------------------------------------
for ($i = 1; $i <= count($FormLabel); $i++) {
	$cnt = $i % 2;
	if ($i == 1) print '<tr><td class=Header colspan=4>A. BUTIR-BUTIR PERMOHONAN</td></tr>';
	if ($i == 25) print '<tr><td class=Header colspan=4>B. BUTIR-BUTIR PEMBIAYAAN</td></tr>';
	if ($i == 37) print '<tr><td class=Header colspan=4>C. PENYATA PENDAPATAN/PERBELANJAAN</td></tr>';
	if ($i == 55) print '<tr><td class=Header colspan=4>D. MAKLUMAT KELAYAKAN PEMOHON</td></tr>';
	if ($i == 71) print '<tr><td class=Header colspan=4>E. HAD TANGGUNGAN PENJAMIN</td></tr>';
	if ($i == 71) print '<tr><td class=Header colspan=4>i) BUTIR-BUTIR PENJAMIN 1</td></tr>';
	if ($i == 83) print '<tr><td class=Header colspan=4>ii) BUTIR-BUTIR PENJAMIN 2</td></tr>';
	if ($i == 95) print '<tr><td class=Header colspan=4>iii) BUTIR-BUTIR PENJAMIN 3</td></tr>';

	if ($i == 107) print '<tr><td class=Header colspan=4>F: MAKLUMAT KELAYAKAN PEMOHON</td></tr>';

	//	if ($i == 37) print '<tr><td class=Header colspan=4>Maklumat Waris:</td></tr>';
	//	if ($i == 43) print '<tr><td class=Header colspan=4>Pembelian Syer Koperasi :</td></tr>';
	$addr = str_replace("<pre>", "", $GetMember->fields('w_address1'));
	$addr1 = str_replace("</pre>", "", $addr);
	$addr = str_replace("<pre>", "", $GetMember->fields('w_address2'));
	$addr2 = str_replace("</pre>", "", $addr);
	$addr = str_replace("<pre>", "", $GetMember->fields('w_address3'));
	$addr3 = str_replace("</pre>", "", $addr);
	if ($i == 313) {
		print '<tr><td class=Header colspan=4>C. PENAMA:</td></tr>';
		print '<tr class="Data">
					<td colspan="4">
						<table width="100%">
							<tr class="DataB">
								<td>&nbsp;</td>	
								<td>Nama</td>
								<td>No KP</td>
								<td>Hubungan</td>
								<td>Alamat</td>
								<td>% Pembahagian</td>
							</tr>
						<tr class="Data">
								<td valign="top">1.&nbsp;</td>	
								<td valign="top"><input type="text" name="w_name1" value="' . tohtml($GetMember->fields('w_name1')) . '" size=30 maxlength=50 readonly></td>
								<td valign="top"><input type="text" name="w_ic1" value="' . tohtml($GetMember->fields('w_ic1')) . '" size=15 maxlength=14 readonly></td>
								<td valign="top"><input type="text" name="w_relation1" value="' . tohtml($GetMember->fields('w_relation1')) . '" size=15 maxlength=15 readonly></td>
								<td valign="top"><textarea cols=30 rows=3 wrap="hard" name="w_address1" readonly>' . $addr1 . '</textarea></td>
								<td valign="top"><input type="text" name="w_contact1" value="' . tohtml($GetMember->fields('w_contact1')) . '" size=15 maxlength=15 readonly></td>
							</tr>  
					<tr class="Data">
								<td valign="top">2.&nbsp;</td>	
								<td valign="top"><input type="text" name="w_name2" value="' . tohtml($GetMember->fields('w_name2')) . '" size=30 maxlength=50 readonly></td>
								<td valign="top"><input type="text" name="w_ic2" value="' . tohtml($GetMember->fields('w_ic2')) . '" size=15 maxlength=14 readonly></td>
								<td valign="top"><input type="text" name="w_relation2" value="' . tohtml($GetMember->fields('w_relation2')) . '" size=15 maxlength=15 readonly></td>
								<td valign="top"><textarea cols=30 rows=3 wrap="hard" name="w_address2" readonly>' . $addr2 . '</textarea></td>
								<td valign="top"><input type="text" name="w_contact2" value="' . tohtml($GetMember->fields('w_contact2')) . '" size=15 maxlength=15 readonly></td>
							</tr> 
					<tr class="Data">
								<td valign="top">3.&nbsp;</td>	
								<td valign="top"><input type="text" name="w_name3" value="' . tohtml($GetMember->fields('w_name3')) . '" size=30 maxlength=50 readonly></td>
								<td valign="top"><input type="text" name="w_ic3" value="' . tohtml($GetMember->fields('w_ic3')) . '" size=15 maxlength=14 readonly></td>
								<td valign="top"><input type="text" name="w_relation3" value="' . tohtml($GetMember->fields('w_relation3')) . '" size=15 maxlength=15 readonly></td>
								<td valign="top"><textarea cols=30 rows=3 wrap="hard" name="w_address3" readonly>' . $addr3 . '</textarea></td>
								<td valign="top"><input type="text" name="w_contact3" value="' . tohtml($GetMember->fields('w_contact3')) . '" size=15 maxlength=15 readonly></td>
							</tr> 
					</table>
					</td>
			   </tr>
		       <tr><td class=Header colspan=4>D. SAKSI :</td></tr>';
	}
	//	if ($i == 47) print '<tr><td class=Header colspan=4>Maklumat Bank</td></tr>';
	//	if ($i == 41) print '<tr><td class=Header colspan=4>Maklumat Bank</td></tr>';
	//	if ($i == 51) print '<tr><td class=Header colspan=4>Audit Informasi :</td></tr>';
	if ($i == 137) print '<tr><td class=Header colspan=4>Audit Informasi :</td></tr>';

	if ($cnt == 1) print '<tr valign=top>';
	print '<td class=Data align=right>' . $FormLabel[$i];
	//if (!($i == 1 or $i == 2 or $i == 8 or $i ==30 or $i == 32)) 
	print ':';
	print ' </td>';

	if (in_array($FormElement[$i], $strErrMsg))
		print '<td class=errdata>';
	else
		print '<td class=Data>';
	//--- Begin : Call function FormEntry ---------------------------------------------------------  
	$strFormValue = tohtml($GetMember->fields($FormElement[$i]));
	if ($FormType[$i] == 'textarea') {
		$strFormValue = str_replace("<pre>", "", $GetMember->fields($FormElement[$i]));
		$strFormValue = str_replace("</pre>", "", $strFormValue);
	}

	if ($i == 58) {
		//echo str_repeat("&nbsp;", 25);
	}

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

	if ($i == 1) {
	}
	//--- End   : Call function FormEntry ---------------------------------------------------------  
	print '&nbsp;</td>';
	if ($cnt == 0) print '</tr>';
}

print '<tr><td colspan=4 align=center class=Data>
			<input type="hidden" name="pk" value="' . $pk . '"
			<input type=Reset name=ResetForm class="but" value=Clear Form>
			<input type=Submit name=SubmitForm class="but" value=Kemaskini>
			</td>
		</tr>
</table>
</form>';

include("footer.php");
