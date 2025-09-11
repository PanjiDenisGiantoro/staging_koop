<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		:	listledger.php
*		   Description	:	Popup to select Ledger Account
*********************************************************************************/
include ("common.php");
include("koperasiQry.php");	

$q = isset($_GET['q']) ? $_GET['q'] : "";
$cat = "AA";
$sSQL = "";
$sWhere = "";

// Build the WHERE clause
$sWhere = "category = ".tosql($cat,"Text");
if ($q != "") {
    $sWhere .= " AND (code LIKE ".tosql("%".$q."%","Text")." OR name LIKE ".tosql("%".$q."%","Text").")";
}

$sSQL = "SELECT * FROM generalacc";
if ($sWhere != "") {
    $sSQL .= " WHERE ".$sWhere;
}
$sSQL .= " ORDER BY code";

$GetLedger = &$conn->Execute($sSQL);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>'.$emaNetis.' - Pilih Akaun Ledger</title>
    <meta name="GENERATOR" content="'.$yVZcSz2OuGE5U.'">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0"> 
    <meta http-equiv="cache-control" content="no-cache">
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 10px; }
        .table { font-size: 12px; }
        .table th { background-color: #f8f9fa; }
        .highlight { background-color: #e9ecef; }
    </style>
</head>
<script language="JavaScript">
    function selectLedger(id, code, name) {
        if (window.opener && !window.opener.closed) {
            window.opener.document.MyForm.loanCode.value = code;
            window.opener.document.getElementById("loanCode").value = code;
            window.opener.document.getElementById("loanName").value = name;
            window.close();
        }
    }
    
    function searchLedger() {
        var q = document.getElementById("searchText").value;
        window.location.href = "listledger.php?q=" + encodeURIComponent(q);
        return false;
    }
</script>
<body>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4>Pilih Akaun Ledger</h4>
            <form onsubmit="return searchLedger();" class="form-inline mb-3">
                <div class="input-group">
                    <input type="text" id="searchText" name="q" value="'.htmlspecialchars($q).'" class="form-control form-control-sm" placeholder="Cari kod atau nama...">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Kod</th>
                            <th>Nama Akaun</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>';

if ($GetLedger && $GetLedger->RecordCount() > 0) {
    while (!$GetLedger->EOF) {
        $code = $GetLedger->fields['code'];
        $name = $GetLedger->fields['name'];
        $id = $GetLedger->fields['ID'];
        
        print '
                        <tr>
                            <td>'.htmlspecialchars($code).'</td>
                            <td>'.htmlspecialchars($name).'</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick="selectLedger('.$id.', \''.addslashes($code).'\', \''.addslashes($name).'\')">Pilih</button>
                            </td>
                        </tr>';
        $GetLedger->MoveNext();
    }
} else {
    print '
                        <tr>
                            <td colspan="3" class="text-center">Tiada rekod dijumpai</td>
                        </tr>';
}

print '
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>';

// Close the database connection
if ($GetLedger) $GetLedger->Close();
?>
