<?php
/***************************
*          Project		:	iKOOP.com.my
*          Filename		: 	product.php
*          Date 		: 	26/07/2024
***************************/
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     echo '<pre>' . print_r($_POST, true) . '</pre>';
// }


if (!isset($mm))    $mm="ALL";
if (!isset($yy))    $yy=date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
date_default_timezone_set("Asia/Jakarta"); 

if (!isset($StartRec))	$StartRec= 1; 
if (!isset($pg))		$pg= 30;
if (!isset($q))			$q="";
if (!isset($code))		$code="ALL";
if (!isset($filter))	$filter="0";

include("header.php");	
include("koperasiQry.php");	
include("forms.php");
date_default_timezone_set("Asia/Jakarta");

// if ($SubmitForm <> "") {
//     echo '<pre>' . print_r($_POST, true) . '</pre>'; // Check all posted data

//     // Add debugging code to check each form element value
//     for ($i = 1; $i <= count($FormLabel); $i++) {
//         echo '<pre>' . $FormElement[$i] . ': ' . print_r($$FormElement[$i], true) . '</pre>';
//     }
// }

$Cookie_userID = get_session('Cookie_userID');
$Cookie_userName = get_session("Cookie_userName");
$sFileName		= "?vw=product&mn=$mn";
$sActionFileName= "?vw=productServiceList";
$title     		= "Tambah Produk/Servis";

$a = 1;
$FormLabel[$a]   	= "* Nama Lengkap";
$FormElement[$a] 	= "product_name";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "50";

$a++;
$FormLabel[$a]   	= "SKU";
$FormElement[$a] 	= "sku";
$FormType[$a]	  	= "text";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "15";
$FormLength[$a]  	= "10";

$cartaList = Array();
$cartaVal  = Array();
$Getcarta = ctGeneralACC("","AA");
if ($Getcarta->RowCount() <> 0){
    while (!$Getcarta->EOF) {
        array_push ($cartaList, $Getcarta->fields('code').' - '.$Getcarta->fields('name'));
        array_push ($cartaVal, $Getcarta->fields('ID'));
        $Getcarta->MoveNext();
    }
}

$a++;
$FormLabel[$a]   	= "Akaun Inventori";
$FormElement[$a] 	= "kodGL";
$FormType[$a]	  	= "select";
$FormData[$a]   	= $cartaList;
$FormDataValue[$a]	= $cartaVal;
$FormCheck[$a]   	= array(CheckBlank);
$FormSize[$a]    	= "1";
$FormLength[$a]  	= "1";

$a++;
$FormLabel[$a]   	= "Catatan";
$FormElement[$a] 	= "description";
$FormType[$a]	  	= "textarea";
$FormData[$a]   	= "";
$FormDataValue[$a]	= "";
$FormCheck[$a]   	= array();
$FormSize[$a]    	= "30";
$FormLength[$a]  	= "3";

$groupProdList = Array();
$groupProdVal  = Array();
$GetGroupProd = ctGeneralACC("","AK");
if ($GetGroupProd->RowCount() <> 0){
    while (!$GetGroupProd->EOF) {
        array_push ($groupProdList, $GetGroupProd->fields('code').' - '.$GetGroupProd->fields('name'));
        array_push ($groupProdVal, $GetGroupProd->fields('ID'));
        $GetGroupProd->MoveNext();
    }
}

$a++;
$FormLabel[$a]    = "Kumpulan";
$FormElement[$a]  = "product_group";
$FormType[$a]     = "select";
$FormData[$a]     = $groupProdList;
$FormDataValue[$a]= $groupProdVal; 
$FormCheck[$a]    = array();
$FormSize[$a]     = "1";
$FormLength[$a]   = "1";

$classList = Array();
$classVal  = Array();
$GetClass = ctGeneralACC("","AJ");
if ($GetClass->RowCount() <> 0){
    while (!$GetClass->EOF) {
        array_push ($classList, $GetClass->fields('code').' - '.$GetClass->fields('name'));
        array_push ($classVal, $GetClass->fields('ID'));
        $GetClass->MoveNext();
    }
}

$a++;
$FormLabel[$a]      = "Klasifikasi";
$FormElement[$a] 	= "klasifikasi";
$FormType[$a]	  	= "selectx";
$FormData[$a]       = $classList;
$FormDataValue[$a]  = $classVal; 
$FormCheck[$a]      = array();
$FormSize[$a]       = "1";
$FormLength[$a]     = "1";

$a++;
$FormLabel[$a]    = "Harga Jualan";
$FormElement[$a]  = "s_price";
$FormType[$a]     = "text";
$FormData[$a]     = "";
$FormDataValue[$a]= "";
$FormCheck[$a]    = array();
$FormSize[$a]     = "30"; 
$FormLength[$a]   = "25";

$cartaList = Array();
$cartaVal  = Array();
$Getcarta = ctGeneralACC("","AA");
if ($Getcarta->RowCount() <> 0){
    while (!$Getcarta->EOF) {
        array_push ($cartaList, $Getcarta->fields('code').' - '.$Getcarta->fields('name'));
        array_push ($cartaVal, $Getcarta->fields('ID'));
        $Getcarta->MoveNext();
    }
}

$a++;
$FormLabel[$a]    = "Akaun Pendapatan";
$FormElement[$a]  = "s_deductID";
$FormType[$a]     = "select";
$FormData[$a]   	= $cartaList;
$FormDataValue[$a]	= $cartaVal;
$FormCheck[$a]    = array();
$FormSize[$a]     = "30";
$FormLength[$a]   = "25";

$a++;
$FormLabel[$a]    = "Cukai Jualan";
$FormElement[$a]  = "s_taxCode"; 
$FormType[$a]     = "text";
$FormData[$a]     = "";
$FormDataValue[$a]= "";
$FormCheck[$a]    = array();
$FormSize[$a]     = "30";
$FormLength[$a]   = "25";


$a++;
$FormLabel[$a]    = "Penerangan";
$FormElement[$a]  = "s_description";
$FormType[$a]     = "textarea";
$FormData[$a]     = "";
$FormDataValue[$a]= "";
$FormCheck[$a]    = array();
$FormSize[$a]     = "30";
$FormLength[$a]   = "3";

$a++;
$FormLabel[$a]    = "Harga Pembelian";
$FormElement[$a]  = "b_price";
$FormType[$a]     = "text";
$FormData[$a]     = "";
$FormDataValue[$a]= "";
$FormCheck[$a]    = array();
$FormSize[$a]     = "30";
$FormLength[$a]   = "25";

$cartaList = Array();
$cartaVal  = Array();
$Getcarta = ctGeneralACC("","AA");
if ($Getcarta->RowCount() <> 0){
    while (!$Getcarta->EOF) {
        array_push ($cartaList, $Getcarta->fields('code').' - '.$Getcarta->fields('name'));
        array_push ($cartaVal, $Getcarta->fields('ID'));
        $Getcarta->MoveNext();
    }
}

$a++;
$FormLabel[$a]    = "Akaun Kos Jualan";
$FormElement[$a]  = "b_deductID";
$FormType[$a]     = "select";
$FormData[$a]   	= $cartaList;
$FormDataValue[$a]	= $cartaVal;
$FormCheck[$a]    = array();
$FormSize[$a]     = "30";
$FormLength[$a]   = "25";

$a++;
$FormLabel[$a]    = "Cukai Pembelian";
$FormElement[$a]  = "b_taxCode";
$FormType[$a]     = "text";
$FormData[$a]     = "";
$FormDataValue[$a]= "";
$FormCheck[$a]    = array();
$FormSize[$a]     = "30";
$FormLength[$a]   = "25";

$a++;
$FormLabel[$a]    = "Penerangan";
$FormElement[$a]  = "b_description";
$FormType[$a]     = "textarea";
$FormData[$a]     = "";
$FormDataValue[$a]= "";
$FormCheck[$a]    = array();
$FormSize[$a]     = "30";
$FormLength[$a]   = "3";


if ($SubmitForm <> "") {
    if (!$product_name) {
        print '<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>Semua informasi mesti dilengkapkan.</strong>
            </div>';
    }

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

    if (!isset($Cookie_userID)) $ID = $ID; else $ID = $Cookie_userID;
    if (!isset($Cookie_userName)) $name = $ID; else $name = $Cookie_userName;
    $activity = "Tambah Produk /Servis";
    
    $createdBy = get_session("Cookie_userName");
    $createdDate = date("Y-m-d H:i:s");

    $sSQL = "INSERT INTO stok (
        name, 
        sku, 
        kodGL, 
        description,
        product_group,
        klasifikasi,
        s_price,  
        s_deductID, 
        s_taxCode,
        s_description,
        b_price,
        b_deductID,
        b_taxCode,
        b_description,
        createdDate, 
        createdBy
    ) VALUES (
        '$product_name', 
        '$sku', 
        '$kodGL', 
        '$description', 
        '$group', 
        '$klasifikasi', 
        '$s_price', 
        '$s_deductID', 
        '$s_taxCode', 
        '$s_description', 
        '$b_price', 
        '$b_deductID', 
        '$b_taxCode', 
        '$b_description', 
        '$createdDate', 
        '$createdBy' 
    );";

    $rs = &$conn->Execute($sSQL);
    if ($rs) {
        activityLog($sSQL, $activity, $ID, $name);
        alert("Produk/Servis ini telah ditambah.");
        gopage("$sActionFileName", 1000);
    } else {
        // Handle the case where the query fails
        echo '<div class="alert alert-danger">Failed to save data: ' . $conn->ErrorMsg() . '</div>';
    }
}
			
?>
<form name="MyForm" action="<?php print $sFileName; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="ID" value="<?php print $ID; ?>">
    <input type="hidden" name="name" value="<?php print $name; ?>">
    <div class="mb-3 row">
        <h5 class="card-title"><?php echo strtoupper($title); ?><br><small></small></h5>
        <?php
       for ($i = 1; $i <= count($FormLabel); $i++) {
        $cnt = $i % 2;
    
        if ($i == 1) print '<div class="card-header mb-3">MAKLUMAT ASAS</div>';
        if ($i == 5) print '<div class="card-header mb-3">LOKASI & KATEGORI</div>';
        if ($i == 7) print '<div class="card-header mb-3">MAKLUMAT PENJUALAN</div>';
        if ($i == 11) print '<div class="card-header mt-3">MAKLUMAT PEMBELIAN</div>';
    
        if ($cnt == 1) print '<div class="m-1 row">';
    
        print '<label class="col-md-2 col-form-label">' . $FormLabel[$i] . '</label>';
        if (in_array($FormElement[$i], $strErrMsg)) print '<div class="col-md-4 bg-danger">';
        else print '<div class="col-md-3">';
    
        $strFormValue = $$FormElement[$i];
        FormEntry($FormLabel[$i], $FormElement[$i], $FormType[$i], $strFormValue, $FormData[$i], $FormDataValue[$i], $FormSize[$i], $FormLength[$i]);
    
        print '</div>';

        if ($FormElement[$i] == "b_taxcode") {
            $j = $i + 1; 
            print '<label class="col-md-2 col-form-label">' . $FormLabel[$j] . '</label>';
            if (in_array($FormElement[$j], $strErrMsg)) print '<div class="col-md-4 bg-danger">';
            else print '<div class="col-md-3">';
    
            $strFormValue = $$FormElement[$j];
            FormEntry($FormLabel[$j], $FormElement[$j], $FormType[$j], $strFormValue, $FormData[$j], $FormDataValue[$j], $FormSize[$j], $FormLength[$j]);
    
            print '</div>';
            $i++;
        }
    
        if ($cnt == 0) print '</div>';
    }
        ?><br>
    </div>
    <div class="mb-4 row">
        <right>
                        <input type="Submit" class="btn btn-primary w-md waves-effect waves-light" name="SubmitForm" value="Kirim">
                        <!-- <input type="Reset" class="btn btn-secondary w-md waves-effect waves-light" name="ResetForm" value="Isi semula"> -->
        </right>
            </div>
        </div>
    </div>
</form>
<?php include("footer.php"); ?>