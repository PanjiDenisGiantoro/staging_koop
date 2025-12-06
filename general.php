<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	general.php
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
if(@$_POST['selCode']!=''){
    $cat=@$_POST['selCode'];
    $xlink = "&selCode=".@$_POST['selCode']; 
} else {
    $cat=@$_REQUEST['cat'];   
    $xlink = "&selCode=".@$_REQUEST['cat']; 
}


if (!isset($StartRec))	$StartRec= 1; 
if (!isset($cat))		$cat="";

include("header.php");	

if (get_session("Cookie_groupID") <> 2) {
	print '<script>alert("'.$errPage.'");parent.location.href = "index.php";</script>';
}

if (!(in_array($cat,$basicVal))) {
	print '	<script>
				alert ("'.$cat.' - Kategori ini tidak muncul...!");
				window.location = "index.php";
			</script>';
}
$sFileName = 'general.php';
$sFileRef  = 'generalAddUpdate.php';
$title     =  $basicList[array_search($cat,$basicVal)];

//--- Begin : deletion based on checked box -------------------------------------------------------
if ($action == "hapus") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {
	    $sWhere = "ID=" . tosql($pk[$i], "Number");
		$sSQL = "DELETE FROM general WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);
		
		$sWhere = "parentID=" . tosql($pk[$i], "Number");
		$sSQL = "DELETE FROM general WHERE " . $sWhere;
		$rs = &$conn->Execute($sSQL);
	}
}

//--- End   : deletion based on checked box -------------------------------------------------------

$GetGeneral = ctGeneral("ALL",$cat);
$GetGeneral->Move($StartRec-1);
print '
<form name="ITRViewResults" action="?vw=general&mn=903'.$xlink.'" method="post">
<input type="hidden" name="action">
<input type="hidden" name="cat" value="'.$cat.'">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<h5 class="card-title">' . strtoupper($title) . '</h5>';
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
                            print '</td>
						</tr>
					</table>
				</td></tr></table>
			</td>
		</tr>
		<tr>
			<td class="textFont">Jumlah Data : <b>'.$RecNum.'</b></td>
		</tr>';
	} else {
		print '
	    <tr valign="top" class="Header">
		   	<td align="center" >
				<input type="button" value="tambah" class="btn btn-sm btn-primary" onclick=Javascript:window.open("' . $sFileRef . '?action=tambah&cat='.$cat.'","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>
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
	      e = document.ITRViewResults;
	      if(e==null) {
	        alert(\'Cannot \' + v + \'. Find must return some result to perform the operation.\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Select the row(s) to \' + v + \'.\');
	        } else {
	          if(confirm(v + \' \' + count + \' Data ?\')) {
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
				window.open("'.$sFileRef.'?action=tambah&cat='.$cat.'&sub=" + pk,"sort","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");
	        }
	      }
	    }
</script>';

function ctGeneral($id, $cat) {
    global $conn;
    $sSQL = "";
    $sWhere = "";
    $sWhere = "g.category = " . tosql($cat, "Text");

    if ($id == "ALL") {
        $sWhere .= " AND g.parentID = 0";
    } else {
        $sWhere .= " AND g.parentID = " . tosql($id, "Number");
    }

    $sSQL = "SELECT g.*, ga.name as loanName 
             FROM general g 
             LEFT JOIN generalacc ga ON g.loanCode = ga.code 
             WHERE " . $sWhere . " 
             ORDER BY g.code";

    $rs = &$conn->Execute($sSQL);
    return $rs;
}

function listGeneral($id, $level) {
	global $setLevel;
	global $sFileName;
	global $sFileRef;
	global $RecNum;
	global $cat;

	$GetGeneral	= '$GetGeneral'.$level;
	$generalID	= '$generalID'.$level;

	$generalID = array();
	$generalCode = array();
	$generalName = array();
	$generalParentID = array();
    $generalActiveSimpanan = array();
    $generalKodeSimpanan = array();
    $loanCode = array();
    $loanName = array();
    $jenisSimpanan = array();
    $setoranSimpananPokok = array();
    $deskripsiSimpanan = array();
	$GetGeneral = ctGeneral($id,$cat);
	if ($GetGeneral->RowCount() <> 0) {
		$RecNum = $RecNum + $GetGeneral->RowCount();
		while (!$GetGeneral->EOF) {
			array_push ($generalID, $GetGeneral->fields(ID));
			array_push ($generalCode, $GetGeneral->fields(code));
			array_push ($generalName, $GetGeneral->fields(name));
			array_push ($generalParentID, $GetGeneral->fields(parentID));

            // Inside your while loop where you process results
            if ($cat == 'Y') {
                array_push($generalActiveSimpanan, $GetGeneral->fields('status_active_simpanan'));
                array_push($loanCode, $GetGeneral->fields('loanCode'));
                array_push($loanName, $GetGeneral->fields('loanName'));
                array_push($generalKodeSimpanan, $GetGeneral->fields('kode_simpanan'));
                array_push($jenisSimpanan, $GetGeneral->fields('jenis_simpanan'));
                array_push($setoranSimpananPokok, $GetGeneral->fields('setoran_simpanan_pokok'));
                array_push($deskripsiSimpanan, $GetGeneral->fields('deskripsi_simpanan'));
            }
			$GetGeneral->MoveNext();
		}
	}

    // Untuk kategori Y, gunakan tampilan table
    if ($cat == 'Y' && $id == 'ALL') {
        print '
        <table class="table table-bordered table-hover table-striped table-sm" style="font-size:10pt;">
            <thead class="table-primary">
                <tr>
                    <th width="5%" class="text-center">
                        <input type="checkbox" class="form-check-input" onclick="ITRViewSelectAll()">
                    </th>
                    <th width="10%">Kode</th>
                    <th width="20%">Nama Simpanan</th>
                    <th width="12%">Jenis</th>
                    <th width="15%">Kode Master Ledger</th>
                    <th width="13%" class="text-end">Setoran Pokok</th>
                    <th width="15%">Deskripsi</th>
                    <th width="10%" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>';

        for ($i=0; $i < count($generalID); $i++) {
            $IDName = get_session("Cookie_userName");
            $disableDelete = false;
            $class = '';
            $title = '';

            $statusBadge = $generalActiveSimpanan[$i] == 1
                ? '<span class="badge bg-success">Aktif</span>'
                : '<span class="badge bg-secondary">Tidak Aktif</span>';

            $jenisLabel = '';
            if ($jenisSimpanan[$i] == 'pokok') {
                $jenisLabel = '<span class="badge bg-primary">Simpanan Pokok</span>';
            } elseif ($jenisSimpanan[$i] == 'wajib') {
                $jenisLabel = '<span class="badge bg-info">Simpanan Wajib</span>';
            }

            $setoran = number_format($setoranSimpananPokok[$i], 2, ',', '.');
            $deskripsi = $deskripsiSimpanan[$i] ? substr($deskripsiSimpanan[$i], 0, 50) . '...' : '-';

            print '
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input" name="pk[]" value="' . $generalID[$i] . '" data-disable-delete="false">
                    </td>
                    <td><font class="redText">' . $generalCode[$i] . '</font></td>
                    <td>
                        <a href="#" onclick="window.open(\'' . $sFileRef . '?action=kemaskini&cat=' . $cat . '&pk=' . $generalID[$i] . '&sub=' . $generalParentID[$i] . '\',\'pop\',\'top=50,left=50,width=900,height=600,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\'); return false;">
                            <font class="blueText"><strong>' . $generalName[$i] . '</strong></font>
                        </a>
                    </td>
                    <td>' . $jenisLabel . '</td>
                    <td>
                        <small class="text-muted">' . $loanCode[$i] . '</small><br>
                        <small>' . $loanName[$i] . '</small>
                    </td>
                    <td class="text-end"><strong>Rp ' . $setoran . '</strong></td>
                    <td><small class="text-muted">' . $deskripsi . '</small></td>
                    <td class="text-center">' . $statusBadge . '</td>
                </tr>';
        }

        print '
            </tbody>
        </table>';

    } else {
        // Tampilan list default untuk kategori lain
        print '<ul>';
        $level++;
        $i = '$i'.$level;
        for ($i=0;$i < count($generalID);$i++){
            $IDName = get_session("Cookie_userName");

            if ($IDName != 'superadmin'){
                if ($cat == "C" || $cat == "J") {
                    $disableDelete = hasTransactions($generalID[$i]) || hasChildrenWithTransactions($generalID[$i]);
                    $class = $disableDelete ? ' nonDeletable' : '';
                }
            }

            if ($id == "ALL") {
                print '<li id="foldlist" class="' . $class . '"><b>';
            } else {
                print '<li id="node" class="' . $class . '"><b>';
            }

            if ($level <= $setLevel) {
            // Add a title attribute for nonDeletable items to show the hover message
            $title = $disableDelete ? ' title="Kode ini mempunyai transaksi"' : '';
                print '<input type="checkbox" class="form-check-input" name="pk[]" value="' . $generalID[$i] . '" data-disable-delete="' . ($disableDelete ? 'true' : 'false') . '"' . $title . '>';
            } else {
                print '&nbsp;&nbsp;&nbsp;';
            }
            print '
             <font class="redText"' . $title . '>'.$generalCode[$i].'</font>&nbsp;-&nbsp;
             <a ' . $title . ' onclick=Javascript:window.open("'. $sFileRef . '?action=kemaskini&amp;cat='.$cat.'&amp;pk='.$generalID[$i].'&amp;sub='.$generalParentID[$i].'","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>
             <font class="blueText">'.$generalName[$i].'</font></a></b>&nbsp;-&nbsp;
             ';
                if($cat == 'Y'){
                if($generalActiveSimpanan[$i] == 1){
                    print 'Aktif';
                }else{
                    print 'Tidak Aktif';
                }
                print '&nbsp;-&nbsp;'.$loanName[$i];
                }
                '</font>
                <font class="blueText">' . $generalParentID[$i] . '</font>
                <font class="blueText">' . $generalID[$i] . '</font>
             </li>';
            if 	($level <= $setLevel){
                listGeneral($generalID[$i], $level);
            }
        }
        print '</ul>';
    }
}

// Checks if there are any records in the transactionacc, transaction, and loans tables where deductID, MdeductID, JdeductID, and loanType match the given $generalID
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
            +
            (SELECT COUNT(*) FROM loans 
             WHERE loanType = " . tosql($generalID, "Number") . " )
        AS total_count";
    
    $rs = &$conn->Execute($sSQL);
    
    return ($rs->fields("total_count") > 0);
}


// Recursively checks if the current item or any of its children (or grandchildren, etc.) have transactions.
function hasChildrenWithTransactions($generalID) {
    global $conn;
    $sSQL 	= "SELECT ID FROM general WHERE parentID = " . tosql($generalID, "Number");
    $rs 	= &$conn->Execute($sSQL);
    
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