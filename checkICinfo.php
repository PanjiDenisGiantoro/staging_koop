<?php

/*********************************************************************************
 *          Project		:	iKOOP
 *          Filename		: 	checkIC.php
 *          Date 		: 	29/03/2004
 *********************************************************************************/
include("header.php");


$title	= "Permohonan Menjadi Ahli";
$id		= "";
$detail	= "";

if ($_POST["action"] == "SEMAK") {
	if ($_POST["ic"] == "") {

		$detail = '<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                    </button>
                                                    <strong>Ralat!</strong> Sila masukkan nombor kad pengenalan anda.
                                                </div>';
	} else {
		$id = dlookup("userdetails", "userID", "newIC=" . tosql($ic, "Text"));
		if ($id == "") {
			$id1 = dlookup("userdetails", "userID", "oldIC=" . tosql($ic, "Text"));
		}

		if ($id <> "") {
			$memberID 	= dlookup("userdetails", "memberID", "userID=" . tosql($id, "Text"));
			$name		= dlookup("users", "name", "userID=" . tosql($id, "Text"));
			$status 	= dlookup("userdetails", "status", "userID=" . tosql($id, "Text"));
			$sqlterm	= "SELECT * FROM userdetails WHERE STATUS = 3 and userID = '" . $id . "'";
			$rs			= $conn->Execute($sqlterm);

			$detail =
				'<div>&nbsp;</div>';


			if ($rs->RowCount() <> 0) {
				$detail .=
					'<form name="MyForm" action="?vw=memberApply" method="post">'
					. '<table class="table lightgrey" border="0" cellspacing="0" cellpadding="0" width="100%" align="center">'
					. '<tr>'
					. '<td class="borderallgreen" align="center" valign="middle"><div class="headerred"><b>MAKLUMAT AHLI BERDAFTAR</b></div></td>'
					. '</tr>'
					. '<tr>'
					. '<td class="borderleftrightbottomred">'
					. '<table border="0" cellpadding="0" cellspacing="6" width="100%" align="center">'
					. '<tr>'
					. '<td class="textFont" colspan="3" height="30">'
					. 'Maklumat tertera di bawah didapati telah berhenti dalam sistem iKOOP - [NAMA KOPERASI]<br>'
					. 'Sila rujuk pada pihak [NAMA KOPERASI] untuk tindakan susulan.'
					. '</td>'
					. '</tr>'
					. '<tr><td class="textFont">Nombor Anggota</td><td>:</td><td class="textFont">' . strtoupper($memberID) . '</td></tr>'
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
					. '<td class="borderall" align="center" valign="middle"><div class="headerred"><b>MAKLUMAT AHLI BERDAFTAR</b></div></td>'
					. '</tr>'
					. '<tr class="table-light">'
					. '<td class="borderleftrightbottomred">'
					. '<table border="0" cellpadding="0" cellspacing="6" width="100%" align="center">'
					. '<tr>'
					. '<td class="textFont" colspan="3" height="30">'
					. 'Maklumat tertera di bawah didapati telah berdaftar dalam sistem iKOOP-[NAMA KOPERASI] <br><br>'
					. '*Jika Status Anda "Dalam Proses" (SILA BUAT PEMBAYARAN PENDAFTARAN). Sekiranya Bayaran telah dibuat, [NAMA KOPERASI] akan memproses permohonan anda dalam <b>7-8 HARI WAKTU BEKERJA</b>.<br><br>'
					. 'Sila rujuk pada pihak [NAMA KOPERASI] untuk tindakan susulan atau meminta <b>ID PENGGUNA @ KATA LALUAN </b> (sekiranya masih tiada / terlupa). <br><br>'
					. 'Bagi Anggota yang telah daftar sila gunakan ID PENGGUNA (Username) = pendaftaran awal, KATA LALUAN (Password) = Kata laluan yang telah didaftarkan di dalam sistem iKOOP <br><br>'

					. '</td>'
					. '</tr>'
					. '<tr><td class="textFont">Nama/Nombor Anggota </td><td>:</td><td class="textFont"><b>' . strtoupper($name) . ' - (' . $memberID . ')</b></td></tr>'
					. '<tr><td class="textFont">Status Keanggotaan </td><td>:</td><td class="greenText"><b>' . strtoupper($statusList[$status]) . '</b></td></tr>'
					. '</table>'
					. '</td>'
					. '</tr>'
					. '</table>';
			}

			//$detail .= '</div>';
		} else {
			$detail .=
				'<form name="MyForm">'
				. '<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                                                                    </button>
                                                                                    <strong>Ralat!</strong> Keanggotaan anda tiada rekod di dalam sistem iKOOP/[NAMA KOPERASI], Sekiranya anda masih menjadi anggota (aktif) Koperasi [NAMA KOPERASI], Sila Hubungi Pihak [NAMA KOPERASI] untuk pengesahan atau membuat permohonan baru sebagai anggota, Segala kesulitan amat dikesali. Terima Kasih.
                                                                                    
                                                                                </div> 
                                                                                
				</form>

				';
		}
	}
}
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
							<h6 class="card-subtitle">NOMBOR KAD PENGENALAN BARU</h6>
						</td>
					</tr>
					<tr>
						<td align="center"><input type="text" placeholder="cth: 990622074352" class="form-controlx" name="ic" size="20" value="<? print $ic; ?>" maxlength="12"></td>
					</tr>
					<tr>
						<td class="textFont" align="center" colspan="3"><input type="button" class="btn btn-secondary waves-effect waves-light" onClick="window.location.href='index.php'" value="<<" size="50">
							<input type="submit" class="btn btn-primary w-md waves-effect waves-light" name="action" value="SEMAK" size="50">
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
//if($ada_err>0) {
print '<div class="row m-4">' . $detail . '</div>';
//}
?>

<?php
include("footer.php");
print '
<script language="JavaScript">
	var allChecked=false;
	
	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
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
			alert(\'Sila pastikan nama form diwujudkan.!\');
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
	          //alert(\'Sila pilih rekod yang hendak di\' + v + \'kan.\');
	        } else {
	          //if(confirm(count + \' rekod hendak di\' + v + \'kan?\')) {
	          //e.submit();
	          //window.location.href ="memberApply.php?pk=" + strStatus;
	          //window.location.href ="memberApply.php";
			  //}
	        }
	      }
	    }

	function ITRActionButtonStatus() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Sila pilih satu rekod sahaja untuk kemaskini status\');
			} else {
				window.location.href = "memberStatus.php?pk=" + pk;
			}
		}
	}
</script>';
?>