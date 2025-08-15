<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	generalACC.php
*          Date 		: 	13/7/06
*********************************************************************************/
print'
<style>
.nonDeletable {
    color: #888; /* Light gray color */
}
</style>
';

$xlink = "";
if(@$_POST['selCodeACC']!=''){
    $cat=@$_POST['selCodeACC'];
    $xlink = "&selCodeACC=".@$_POST['selCodeACC']; 
} else {
    $cat=@$_REQUEST['cat'];
    $xlink = "&selCodeACC=".@$_REQUEST['cat']; 
}

if (!isset($StartRec))	$StartRec= 1; 
if (!isset($cat))		$cat="";

include("header.php");	

if (get_session("Cookie_groupID") <> 2) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
}
if (!(in_array($cat,$basicValACC))) {
	print '	<script>
				alert ("'.$cat.' - Kategori ini tidak tersedia...!");
				window.location = "index.php";
			</script>';
}

$sFileName = '?vw=generalACC&mn=904'.$xlink;
$sFileRef  = 'generalAddUpdateACC.php';
$title     =  $basicListACC[array_search($cat,$basicValACC)];

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "hapus") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {
	    $sWhere = "ID=" . tosql($pk[$i], "Number");
		$sSQL = "DELETE FROM generalacc WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);
		
		$sWhere = "parentID=" . tosql($pk[$i], "Number");
		$sSQL = "DELETE FROM generalacc WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);
	}
}
//--- End   : deletion based on checked box -------------------------------------------------------

$GetGeneral = ctGeneralACC("ALL",$cat);
$GetGeneral->Move($StartRec-1);

print '
<form name="ITRViewResults" action=' .$sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="cat" value="'.$cat.'">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">';

if ($basicListACC[array_search($cat, $basicValACC)] <> 'Kode Penghutang' && $basicListACC[array_search($cat, $basicValACC)] <> 'Kod Pemiutang') {
	print '<h5 class="card-title">' . strtoupper($title) . '</h5>';
} else if ($basicListACC[array_search($cat, $basicValACC)] == 'Kode Debitur') {
    print '<h5 class="card-title">' . strtoupper($title . ' (Orang Yang Berhutang Dengan Koperasi/Pelanggan(Client))') . '</h5>';
} else if ($basicListACC[array_search($cat, $basicValACC)] == 'Kode Kreditur'){
	print '<h5 class="card-title">' . strtoupper($title . ' (Pemberi Hutang Kepada Koperasi/Pembekal(Supplier))') . '</h5>';
}

	if ($GetGeneral->RowCount() <> 0) {  
		print '    
	    <tr valign="top" class="Header">
		   	<td align="left">';
		//if ($cat <> 'O') {
			print '
				<input type="button" value="Tambah" class="btn btn-sm btn-primary" onClick="ITRAddButtonClick(\'tambah\');">
		        <input type="button" class="btn btn-sm btn-danger" id="hapusButton" value="Hapus" onClick="ITRActionButtonClick(\'hapus\');"> ';
		//}
		print '           
			</td>
		</tr>
	    <tr valign="top" >
	    	<td>
				<table border="0" cellspacing="0" cellpadding="3" width="100%"><tr><td>
						<table border="0" cellspacing="0" cellpadding="3" width="100%">
						<tr>
							<td class="textFont">';
							listGeneral("ALL",1);			
		print '				</td>
						</tr>
					</table>
				</td></tr></table>
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Rekod : <b>'.$RecNum.'</b></td>
		</tr>';
	} else {
		print '
	    <tr valign="top" class="Header">
		   	<td align="center" >
				<input type="button" value="tambah" class="btn btn-sm btn-primary" onclick=Javascript:window.open("' . $sFileRef . '?action=tambah&selCodeACC='.$cat.'&cat='.$cat.'","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>
			</td>
		</tr>		
		<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data -</b><hr size=1"></td></tr>';
	}
print ' 
</table>
</form>';

include("footer.php");	

print '
<script language="JavaScript">
	var allChecked=false;
	function ITRViewSelectAll() {
	    e = document.ITRViewResults.elements;
	    allChecked = !allChecked;
	    for(c=0; c< e.length; c++) {
	      if(e[c].type=="checkbox" && e[c].name!="all") {
	        e[c].checked = allChecked;
	      }
	    }
	}

	document.addEventListener("DOMContentLoaded", function() {
    var form = document.ITRViewResults;
    var hapusButton = document.getElementById("hapusButton");

    function updateButtonState() {
        var disableDelete = false;
        var checkboxes = form.querySelectorAll("input[name=\'pk[]\']");
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked && checkbox.getAttribute("data-disable-delete") === "true") {
                disableDelete = true;
            }
        });

        hapusButton.disabled = disableDelete;
    }

    // Add event listeners to checkboxes
    var checkboxes = form.querySelectorAll("input[name=\'pk[]\']");
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener("change", updateButtonState);
    });

    // Initial button state update
    updateButtonState();
});

function ITRActionButtonClick(v) {
    var e = document.ITRViewResults;
    if (e == null) {
        alert("Cannot " + v + ". Find must return some result to perform the operation.");
    } else {
        var count = 0;
        for (var c = 0; c < e.elements.length; c++) {
            if (e.elements[c].name == "pk[]" && e.elements[c].checked) {
                count++;
            }
        }

        if (count == 0) {
            alert("Select the row(s) to " + v + ".");
        } else {
            if (confirm(v + " " + count + " rekod ?")) {
                e.action.value = v;
                e.submit();
            }
        }
    }
}
		
	function ITRAddButtonClick(v) {
	      e = document.ITRViewResults;
		  pk = "";
	      if(e==null) {
	        alert(\'Cannot \' + v + \'. Find must return some result to perform the operation.\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
				pk = e.elements[c].value;
	          }
	        }
	        
	        if(count > 1) {
	          alert(\'Select one row only to \' + v + \'.\');
	        } else {
				window.open("'.$sFileRef.'?action=tambah&cat='.$cat.'&sub=" + pk,"sort","top=50,left=50,width=700,height=650,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
	        }
	      }
	    }
</script>';

function ctGeneralACC($id,$cat) {
	global $conn;
	$sSQL = "";
	$sWhere = "";		
	$sWhere = "category = " . tosql($cat,"Text");
	if ($id == "ALL") {
		$sWhere .= " AND parentID = 0";
	} else {
		$sWhere .= " AND parentID = " . tosql($id,"Number");
	}
	$sWhere = " WHERE (" . $sWhere . ")";
	$sSQL = "SELECT	 * FROM generalacc";
	$sSQL = $sSQL . $sWhere . ' ORDER BY code';
	$rs = &$conn->Execute($sSQL);
	return $rs;
}

function listGeneral($id, $level) {
	global $setLevel, $sFileName, $sFileRef, $RecNum, $cat, $conn;
	
    $GetGeneral = '$GetGeneral'.$level;
    $generalID = '$generalID'.$level;
	
	$generalID = array();
	$generalCode = array();
	$generalName = array();
	$generalParentID = array();
    $GetGeneral = ctGeneralACC($id, $cat);

	if ($GetGeneral->RowCount() <> 0) {
		$RecNum = $RecNum + $GetGeneral->RowCount(); 
		while (!$GetGeneral->EOF) {
            array_push($generalID, $GetGeneral->fields("ID"));
            array_push($generalCode, $GetGeneral->fields("code"));
            array_push($generalName, $GetGeneral->fields("name"));
            array_push($generalParentID, $GetGeneral->fields("parentID"));
			$GetGeneral->MoveNext();
		}
	}	

	print '<ul>';
	$level++;
	$i = '$i'.$level;
	for ($i = 0; $i < count($generalID); $i++) {
        $IDName = get_session("Cookie_userName");

        if ($IDName != 'superadmin'){

        // List of IDs that should be permanently disabled
        $permanentlyDisabledIDs = array(8, 348, 379, 12, 500, 508, 10, 13, 1172, 1140, 1121); // all parents, keuntungan terkumpul and its parent

        // Check if the ID is permanently disabled
        $isPermanentlyDisabled = in_array($generalID[$i], $permanentlyDisabledIDs);

        // Check if the ID has transactions or children with transactions
        $hasTransactions = hasTransactions($generalID[$i]) || hasChildrenWithTransactions($generalID[$i]);

        // Set delete disable flag
        $disableDelete = $isPermanentlyDisabled || $hasTransactions;
        
        // Apply lighter text css based on whether deletion is disabled
        $class = $disableDelete ? ' nonDeletable' : '';
        }

		if ($id == "ALL") {
            print '<li id="foldlist" class="' . $class . '"><b>';
        } else {
            print '<li id="node" class="' . $class . '"><b>';
        }

		// Set hover message based on condition
		if ($isPermanentlyDisabled) {
			$title = ' title="Kode ini tidak boleh dihapuskan"';
		} elseif ($hasTransactions) {
			$title = ' title="Kode ini mempunyai transaksi"';
		} else {
			$title = ''; // No hover message if deletable
		}
		
        print '<input type="checkbox" class="form-check-input" name="pk[]" value="' . $generalID[$i] . '" data-disable-delete="' . ($disableDelete ? 'true' : 'false') . '"' . $title . '>';
    
        print '
         <font class="redText"' . $title . '>' . $generalCode[$i] . '</font>&nbsp;-&nbsp;
         <a ' . $title . ' onclick=Javascript:window.open("' . $sFileRef . '?action=kemaskini&cat=' . $cat . '&pk=' . $generalID[$i] . '&sub=' . $generalParentID[$i] . '","pop","top=50,left=50,width=700,height=650,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>
         <font class="blueText">' . $generalName[$i] . '</font></a></b></li>';
    
        listGeneral($generalID[$i], $level);
	}

	print '</ul>';
}

// Checks if there are any records in the transactionacc, transaction tables where deductID, MdeductID, and JdeductID match the given $generalID
function hasTransactions($generalID) {
    global $conn;

    $sSQL = "
        SELECT 
            (SELECT COUNT(*) FROM transactionacc 
             WHERE deductID = " . tosql($generalID, "Number") . " 
	            OR MdeductID = " . tosql($generalID, "Number") . "
                OR JdeductID = " . tosql($generalID, "Number") . ") 
            +
            (SELECT COUNT(*) FROM transaction 
             WHERE deductID = " . tosql($generalID, "Number") . " 
                OR MdeductID = " . tosql($generalID, "Number") . ")
        AS total_count";
    
    $rs = &$conn->Execute($sSQL);
    
    return ($rs->fields("total_count") > 0);
}

// Recursively checks if the current item or any of its children (or grandchildren, etc.) have transactions.
function hasChildrenWithTransactions($generalID) {
    global $conn;
    $sSQL = "SELECT ID FROM generalacc WHERE parentID = " . tosql($generalID, "Number");
    $rs = &$conn->Execute($sSQL);
    
    while (!$rs->EOF) {
        $childID = $rs->fields("ID");
        if (hasTransactions($childID) || hasChildrenWithTransactions($childID)) {
            return true;
        }
        $rs->MoveNext();
    }
    
    return false;
}
?>