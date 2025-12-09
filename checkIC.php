<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	checkIC.php
 *          Date 		: 	24/11/2023
 *********************************************************************************/
include("header.php");
$title	= "Permohonan Menjadi Ahli";
$id		= "";
$detail	= "";

if ($_POST["action"] == "CEK") {
	if ($_POST["newIC"] == "") {
		$detail = '<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>Kesalahan!</strong> Silakan masukkan nomor kartu identitas Anda.
                                                </div>';
	} else {
		$id = dlookup("userdetails", "userID", "newIC=" . tosql($newIC, "Text"));
		if ($id == "") {
			$id1 = dlookup("userdetails", "userID", "oldIC=" . tosql($newIC, "Text"));
		}

		if ($id <> "") {
			$memberID 	= dlookup("userdetails", "memberID", "userID=" . tosql($id, "Text"));
			$name		= dlookup("users", "name", "userID=" . tosql($id, "Text"));
			$status 	= dlookup("userdetails", "status", "userID=" . tosql($id, "Text"));
			$kandungan		= dlookup("kandungan", "kandungan", "ID=" . tosql($id, "Text"));
			$sqlterm	= "SELECT * FROM userdetails WHERE STATUS = 3 and userID = '" . $id . "'";
			$rs			= $conn->Execute($sqlterm);

			$detail =
				'<div>&nbsp;</div>';

			if ($rs->RowCount() <> 0) {
				$detail .=
					'<form name="MyForm" action="?vw=memberApply" method="post">'
					. '<table class="lightgrey" border="0" cellspacing="0" cellpadding="0" width="100%" align="center">'
					. '<tr>'
					. '<td class="borderallred" align="center" valign="middle"><div class="headerred"><b>INFORPASI ANGGOTA TERDAFTAR</b></div></td>'
					. '</tr>'
					. '<tr>'
					. '<td class="borderleftrightbottomred">'
					. '<table border="0" cellpadding="0" cellspacing="6" width="100%" align="center">'
					. '<tr>'
					. '<td class="textFont" colspan="3" height="30">'
					. 'Informasi tertera di bawah didapati telah dihentikan dalam sistem [NAMA KOPERASI]<br>'
					. 'Silakan rujuk pada pihak [NAMA KOPERASI] untuk tindakan selanjutnya atau meminta Login password (jika masih belum ada / lupa).'
					. '</td>'
					. '</tr>'
					. '<tr><td class="textFont">Nomor Anggota</td><td>:</td><td class="textFont">' . strtoupper($memberID) . '</td></tr>'
					. '<tr><td class="textFont">Nama Anggota </td><td>:</td><td class="textFont">' . strtoupper($name) . '</td></tr>'
					. '<tr>'
					. '<td class="textFont" align="center" colspan="3"><br /><input type="submit" name="action"  value="Daftar Semula" size="50"><br />&nbsp;</td>'
					. '</tr>'
					. '</table>'
					. '</td>'
					. '</tr>'
					. '</table>'
					. '</form>';
			} else {
				$detail .=
					'<table class="table lightgrey" border="0" cellspacing="0" cellpadding="0" width="100%" align="center">'
					. '<tr class="table-primary">'
					. '<td class="borderall" align="center" valign="middle"><div class="headerred"><b>INFORPASI ANGGOTA TERDAFTAR</b></div></td>'
					. '</tr>'
					. '<tr class="table-light">'
					. '<td class="borderleftrightbottomred">'
					. '<table border="0" cellpadding="0" cellspacing="6" width="100%" align="center">'
					. '<tr>'
					. '<td class="textFont" colspan="3" height="30">'
					. 'Informasi tertera di bawah didapati telah terdaftar dalam sistem iKOOP [NAMA KOPERASI]<br>'
					. 'Silakan rujuk pada pihak [NAMA KOPERASI] untuk tindakan selanjutnya atau meminta <b>KATA SANDI PENGGUNA</b> (jika masih belum ada / lupa).'
					. '</td>'
					. '</tr>'

					. '<tr><td class="textFont">Nama Anggota </td><td>:</td><td class="textFont"><b>' . strtoupper($name) . ' - (' . $memberID . ')</b></td></tr>'
					. '<tr><td class="textFont">Status Keanggotaan </td><td>:</td><td class="greenText"><b>' . strtoupper($statusList[$status]) . '</b></td></tr>'
					. '</table>'
					. '</td>'
					. '</tr>'
					. '</table>';
			}

			//$detail .= '</div>';
		} else {

			$detail .=
				'<form name="MyForm" action="?vw=memberApply" method="post">                                                                             
			<div class="row "><center>                                                                                   
			<textarea class="form-control" cols="" rows="7" wrap="hard" name="syarat" readonly>Dengan ini saya setuju untuk mematuhi semua syarat dan peraturan keanggotaan [NAMA KOPERASI]. Saya juga mengakui bahwa semua informasi yang diberikan di sini adalah benar. Saya bersedia untuk dikenakan tindakan oleh [NAMA KOPERASI] jika terdapat informasi yang tidak benar. ID PENGGUNA dan KATA SANDI ini adalah milik serta tanggung jawab saya. Saya juga bertanggung jawab atas transaksi internet di situs web ini.</textarea>
			</center>
			</div>
		
			<div class="row m-3"><center>                                                                                   
			<input type="checkbox" class="form-check-input" name="pk[]" id="pk[]" onchange="document.querySelector("proses").disabled = true;">&nbsp;Saya setuju dengan<a href="manual_tawaransah.pdf">&nbsp;SYARAT & KETENTUAN</a>
			</center>
			</div>
			<div class="row m-2 mb-4">
			<center>																			
			<div class="col-md-3">                                              
			<input type="button" class="btn btn-primary w-md waves-effect waves-light" id="proses" name="action"  value="PROSES" size="50" onClick="ITRActionButtonClickStatus(\'proses\');">																			
			</div>
			</center>
			</div>'
				. '</form>';
		}
	}
}

?>
<div align="center">
	<?php
	$tajuk = dlookup("syarat", "tajuk", "ID=" . tosql(999, "Text"));
	$syarat = dlookup("syarat", "kandungan", "ID=" . tosql(999, "Text"));
	echo '<table class="" width="">
    <tr>
      <td width="21">&nbsp;</td>
      <td><p><strong><u>' . $tajuk . '</u></strong></p>
			' . $syarat . '
        </td>
      <td width="10">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>';
	?>
	<form name="MyForm1" action="" method="post">
		<input type="hidden" name="action">

		<table class="table table-sm mb-3">
			<tr class="table-light">
				<td>
					<table border="0" cellspacing="6" cellpadding="4" align="center">
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td class="borderallteal" align="center" valign="middle">
								<h6 class="card-subtitle">NOMOR KARTU PENGENAL BARU</h6>
							</td>
						</tr>
						<tr>
							<td align="center"><input type="text" placeholder="cth: 990622074352" class="form-controlx" name="newIC" maxlength="12" size="20" value="<? print $newIC; ?>"></td>
						</tr>
						<tr>
							<td class="textFont" align="center" colspan="3"><input type="button" class="btn btn-secondary waves-effect waves-light" onClick="window.location.href='index.php'" value="<<">
								<input type="submit" class="btn btn-primary w-md waves-effect waves-light" name="action" value="CEK">
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>

		</table>
	</form>

	<?php
	print '<div class="row m-4">' . $detail . '</div>';
	?>
</div>
<?php
include("footer.php");
print '
<script language="JavaScript">
	var allChecked=false;
	
	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	    }
		
	function ITRActionButtonClickStatus(v) {
	      var strStatus="";
		  e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        j=0;
			for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
				pk = e.elements[c].value;
				//strStatus = strStatus + ":" + pk;
				count++;
	          }
	        }
	        
	        if(count==0) {
	          //alert(\'Silakan pilih data/rekaman yang ingin di\' + v + \'kan.\');
	        } else {
	          //if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          //window.location.href ="memberApply.php?pk=" + strStatus;
	          window.location.href ="?vw=memberApplyL&newIC=' . $newIC . '";
			  //}
	        }
	      }
	    }

	function ITRActionButtonStatus() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Silakan pilih satu data saja untuk memperbarui status\');
			} else {
				window.location.href = "?vw=memberStatus&pk=" + pk;
			}
		}
	}



</script>';
?>