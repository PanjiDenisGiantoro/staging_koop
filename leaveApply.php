<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	leaveApply.php
*          Date 		: 	6/11/2024
*********************************************************************************/
if (!isset($StartRec))    $StartRec= 1; 
if (!isset($pg))          $pg= 50;
if (!isset($q))           $q="";
if (!isset($by))          $by="1";
if (!isset($dept))        $dept="";
if (!isset($mth))         $mth=date("n");                 
if (!isset($yr))          $yr=date("Y");
if (!isset($mm))          $mm=date("m");
if (!isset($yy))          $yy=date("Y");

include("header.php");    
include("koperasiQry.php");    
include("koperasiList.php");  
date_default_timezone_set("Asia/Kuala_Lumpur");

$db_koperasiID = dlookup("setup", "koperasiID", "1=1"); 

if (get_session("Cookie_groupID") <> 0 AND 
    get_session("Cookie_groupID") <> 99 AND
    get_session("Cookie_koperasiID") <> $db_koperasiID) {
    print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
    exit;
}

$sFileName = '?vw=leaveApply&mn=7';
$sFileRef  = '?vw=leaveApply&mn=7';
$title     = "Senarai Cuti";

$userID = get_session("Cookie_userID");
$name = dlookup("users", "name", "userID = " . $userID);
$sex = dlookup("staff", "sex", "name = '$name'");
$timeOff = dlookup("sleave_details", "leaveTypeID", "userID = " . $userID);
$timeOffBalance = dlookup("sleave_details", "balanceLeave", "userID = " . $userID . " AND leaveTypeID = " . $timeOff);

$sSQL = "SELECT leaveTypeID, totalLeave, balanceLeave FROM sleave_details WHERE userID = '$userID'";
$rs = &$conn->Execute($sSQL);


$cutiList = Array();
$cutiVal  = Array();
$GetCuti = ctGeneral("","V");
if ($GetCuti->RowCount() <> 0){
	while (!$GetCuti->EOF) {
		array_push ($cutiList, $GetCuti->fields(name));
		array_push ($cutiVal, $GetCuti->fields(ID));
		$GetCuti->MoveNext();
	}
}

$a = 0;

if ($rs && !$rs->EOF) {
    while (!$rs->EOF) {
        $leaveTypeID = $rs->fields['leaveTypeID'];
        $totalLeave = $rs->fields['totalLeave'];
        $balanceLeave = $rs->fields['balanceLeave'];

        $leaveName = dlookup("general", "name", "ID = $leaveTypeID");

        $FormLabel[++$a] = "Jumlah $leaveName";
        $FormElement[$a] = "total$leaveTypeID";
        $FormType[$a] = "text";
        $FormData[$a] = $totalLeave;
        $FormDataValue[$a] = $totalLeave;
        $FormSize[$a] = "10";
        $FormLength[$a] = "5";
        $FormReadOnly[$a] = true;

        $FormLabel[++$a] = "Baki $leaveName";
        $FormElement[$a] = "balance$leaveTypeID";
        $FormType[$a] = "text";
        $FormData[$a] = $balanceLeave;
        $FormDataValue[$a] = $balanceLeave;
        $FormSize[$a] = "10";
        $FormLength[$a] = "5";
        $FormReadOnly[$a] = true;

        $rs->MoveNext();
    }
} else {
    $FormLabel[++$a] = "Cuti belum ditetapkan";
    $FormElement[$a] = "";
    $FormType[$a] = "text";
    $FormData[$a] = "";
    $FormDataValue[$a] = "";
    $FormSize[$a] = "10";
    $FormLength[$a] = "5";
    $FormReadOnly[$a] = true;
}


$FormLabel[++$a]     = "* Jenis Cuti <br> -Sila Pilih-";
$FormElement[$a]     = "leaveType";
$FormType[$a]	  	 = "select";
$FormData[$a]   	 = $cutiList;
$FormDataValue[$a]	 = $cutiVal;
$FormCheck[$a]   	 = array(CheckBlank);
$FormSize[$a]    	 = "1";
$FormLength[$a]  	 = "1";

$FormLabel[++$a]     = "* Sebab Cuti";
$FormElement[$a]     = "reason";
$FormType[$a]        = "textarea";
$FormData[$a]        = "";
$FormDataValue[$a]   = "";
$FormCheck[$a]       = array(CheckBlank);
$FormSize[$a]        = "50";
$FormLength[$a]      = "5";

$FormLabel[++$a]     = "* Tarikh Mula"; 
$FormElement[$a]     = "startLeave"; 
$FormType[$a]        = "date"; 
$FormData[$a]        = ""; 
$FormDataValue[$a]   = ""; 
$FormCheck[$a]   	 = array(CheckBlank);
$FormSize[$a]        = "20"; 
$FormLength[$a]      = "10"; 

$FormLabel[++$a]     = "* Tarikh Tamat "; 
$FormElement[$a]     = "endLeave"; 
$FormType[$a]        = "date"; 
$FormData[$a]        = ""; 
$FormDataValue[$a]   = ""; 
$FormCheck[$a]   	 = array(CheckBlank);
$FormSize[$a]        = "20"; 
$FormLength[$a]      = "10";

// "Jumlah Jam"
$FormLabel[++$a]     = "Jumlah Jam <br><mid>(Hanya untuk Time Off)</mid>"; 
$FormElement[$a]     = "total_hour"; 
$FormType[$a]        = "text"; 
$FormData[$a]        = ""; 
$FormDataValue[$a]   = ""; 
$FormCheck[$a]   	 = array();
$FormSize[$a]        = "10"; 
$FormLength[$a]      = "5"; 

$a++;
$FormLabel[$a]       = "* Muat Naik Dokumen";
$FormElement[$a]     = "leave_img";
$FormType[$a]        = "file";      
$FormData[$a]        = "";
$FormDataValue[$a]   = "";
$FormCheck[$a]   	 = array(CheckBlank);
$FormSize[$a]        = "";         
$FormLength[$a]      = "";          

$a++;
$FormLabel[$a]   	 = "&nbsp;";
$FormElement[$a] 	 = "";
$FormType[$a]	  	 = "hidden";
$FormData[$a]   	 = "";
$FormDataValue[$a]	 = "";
$FormCheck[$a]   	 = array(CheckBlank);
$FormSize[$a]    	 = "1";
$FormLength[$a]  	 = "1";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $leaveType = isset($_POST['leaveType']) ? trim($_POST['leaveType']) : '';
    $leaveReason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    $startLeave = isset($_POST['startLeave']) ? trim($_POST['startLeave']) : '';
    $endLeave = isset($_POST['endLeave']) ? trim($_POST['endLeave']) : '';
    $totalHours = isset($_POST['totalHours']) ? trim($_POST['totalHours']) : '';

    $applyDate = date("Y-m-d H:i:s");
    $applyBy = get_session("Cookie_userName");
    $fileName = '';

    // Handle File Upload
    if (!empty($_FILES['leave_img']['name']) && $_FILES['leave_img']['error'] === UPLOAD_ERR_OK) {
        $leaveUploadDir = $_SERVER['DOCUMENT_ROOT'] . "/stagingtri/upload_leave/";


        // Generate a unique file name
        $originalFileName = basename($_FILES['leave_img']['name']);
        $fileExt = pathinfo($originalFileName, PATHINFO_EXTENSION);
        $fileName = time() . "_" . uniqid() . "." . $fileExt;
        $targetPath = $leaveUploadDir . $fileName;

    } else {
        echo "<p style='color:red;'>⚠️ No file uploaded or an error occurred.</p>";
        print_r($_FILES['leave_img']);
    }

    // SQL Insert Statement
    $sSQL2 = "INSERT INTO sleave (leaveType,userID, reason, startLeave, endLeave, total_hour, leave_img, applyDate, applyBy) 
              VALUES (" . 
              tosql($leaveType, "Text") . ", " . 
              tosql($userID, "Number") . ", " . 
              tosql($leaveReason, "Text") . ", " . 
              tosql($startLeave, "Date") . ", " . 
              tosql(($leaveType == "2063") ? "NULL" : $endLeave, "Date") . ", " . 
              tosql(($leaveType == "2063") ? $totalHours : "NULL", "Text") . ", " . 
              tosql($fileName, "Text") . ", " . 
              tosql($applyDate, "Date") . ", " . 
              tosql($applyBy, "Text") . ")";

    

    // Execute SQL query
    $rs = $conn->execute($sSQL2);

}



print "<form method='POST' action='' enctype='multipart/form-data' name='applyCutiForm' onsubmit='return validateForm();'>
";

for ($i = 1; $i <= count($FormLabel); $i++) {
    $cnt = $i % 2;

    if ($i == 1) {
        print '<div class="card-header">MAKLUMAT CUTI</div>';
    }
    
    if (strpos($FormLabel[$i], "* Jenis Cuti") !== false) {
        print '<div class="card-header">MOHON CUTI</div>';
    }

    if ($cnt == 1) print '<div class="m-1 row">';

    print '<label class="col-md-2 col-form-label">' . $FormLabel[$i] . '</label>';
    
    
    if (in_array($FormElement[$i], $strErrMsg))
        print '<div class="col-md-4 bg-danger">';
    else
        print '<div class="col-md-4">';

switch ($FormType[$i]) {
    case "text":
        print '<input type="text" name="' . $FormElement[$i] . '" value="' . $FormDataValue[$i] . '"';
        if (isset($FormReadOnly[$i]) && $FormReadOnly[$i]) {
            print ' readonly';
        }
        print ' class="form-control" size="' . $FormSize[$i] . '" maxlength="' . $FormLength[$i] . '">';
        break;

    case "textarea":
        print '<textarea name="' . $FormElement[$i] . '" class="form-control" rows="' . $FormLength[$i] . '" cols="' . $FormSize[$i] . '">' . $FormDataValue[$i] . '</textarea>';
        break;

    case "select":
        print '<select name="' . $FormElement[$i] . '" class="form-control">';
        foreach ($FormData[$i] as $index => $option) {
            $selected = ($FormDataValue[$i][$index] == $FormDataValue[$i]) ? ' selected' : '';
            print '<option value="' . $FormDataValue[$i][$index] . '"' . $selected . '>' . $option . '</option>';
        }
        print '</select>';
        break;

    case "date":
        print '<input type="date" name="' . $FormElement[$i] . '" value="' . $FormDataValue[$i] . '" class="form-control">';
        break;

    case "file":
        print '<input type="file" name="' . $FormElement[$i] . '" class="form-control">';
        break;

    case "hidden":
        print '<input type="hidden" name="' . $FormElement[$i] . '" value="' . $FormDataValue[$i] . '">';
        break;

    default:
        print "Unknown form element type!";
        break;
}

print '</div>'; 

if ($cnt == 0) print '</div>';
}

print '
 <div class="row mt-3">
 <div class="col-md-12 text-center">
 <button type="submit" name="SubmitForm" class="btn btn-primary">Hantar Permohonan</button>
 </div>
 </div>
 </form>'; 
?>

<script>
    function validateForm() {
        let form = document.forms["applyCutiForm"];
        let leaveType = form["leaveType"].value.trim();
        let reason = form["reason"].value.trim();
        let startLeave = form["startLeave"].value.trim();
        let endLeave = form["endLeave"].value.trim();
        let totalHours = form["total_hour"].value.trim();
        let fileName = form["leave_img"].value.trim();

        // Normal condition
        if (leaveType === "" || reason === "" || startLeave === "" || fileName === "") {
            alert("Sila isi semua maklumat yang diperlukan.");
            return false;
        }

        if (leaveType === "2063") {  // Hanya untuk Time Off
            let hours = parseFloat(totalHours);
            if (isNaN(hours) || hours < 1 || hours > 4) {
                alert("Jumlah Jam untuk Cuti Rehat (Time Off) mesti antara 1 hingga 4 jam.");
                return false;
            }
            
            if (hours > $timeOffBalance) {
                alert("Jumlah Jam melebihi baki cuti yang tersedia. Sila semak semula.");
                return false;
            }
        } else {  // Normal Leave Validation
            if (endLeave === "") {
                alert("Sila isi Tarikh Tamat.");
                return false;
            }
        }
        return true;
    }
</script>
<?php
include("footer.php");
?>