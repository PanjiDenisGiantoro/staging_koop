<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	welfareStatus.php
 *		   Description	:   Update welfare status
 *          Date 		: 	29/03/2006
 *********************************************************************************/
include("header.php");
include("koperasiQry.php");
include("koperasiList.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '"); parent.location.href = "index.php";</script>';
}


if (!isset($strDate))	$strDate = date("d/m/Y");
if ($action == 'Kemaskini') {
	$pk = explode(",", $pk);
	$str = array();
	foreach ($pk as $val) {
		if ($val) $str[] = "'" . $val . "'";
	}
	$pk = implode(",", $str);

	if ($selStatus <> 0) {
		$strDate = 	substr($strDate, 6, 4) . '-' .
			substr($strDate, 3, 2) . '-' .
			substr($strDate, 0, 2);
		$sSQL = "SELECT a.*
				FROM welfares a ";
		$sWhere = '';
		$sWhere = ' ID  in (' . $pk . ')';
		$sSQL	= ' UPDATE welfares ';
		if ($selStatus == 1) {
			$sSQL	.= ' SET isApproved = 1 ' .
				' ,rnoBond=' . tosql($no_bond, "Text") .
				' ,approvedDate=' . tosql($strDate, "Text") .
				' ,remark=' . tosql($remark, "Text");
		}
		if ($selStatus == 2) {
			$rejectedDate = date("Y-m-d H:i:s");
			$sSQL	.= ' SET isRejected = 1 ' .
				' ,rejectedDate=' . tosql($strDate, "Text") .
				' ,remark=' . tosql($remark, "Text");
		}
		$sSQL .= ' ,status=' . tosql($selStatus, "Number");
		$sSQL .= ' WHERE ' . $sWhere;
		$rs = &$conn->Execute($sSQL);
	}
	print 	'
		<script>
			window.location = "?vw=welfare&mn=920";
		</script>';
	exit;
}

$title = "Status Permohonan Bantuan Kebajikan";

if (isset($pk)) $pkall = explode(":", $pk);
unset($pk);
?>

<div class="table-responsive">
	<h5 class="card-title"><?php echo strtoupper($title); ?></h5>
	<form name="MyForm" action="?vw=welfareStatus&mn=920" method="post">
		<input type="hidden" name="action">
		<input type="hidden" name="pk" value="<? print implode(",", $pkall); ?>">
		<table class="table" border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
			<h6 class="card-subtitle"><b>STATUS ANGGOTA</h6>
			<tr>
				<!-- <td class="borderleftrightbottomteal"> -->
				<table border="0" cellspacing="6" cellpadding="3" width="100%" align="center">
					<?php
					for ($s = 0; $s < count($pkall); $s++) {
						//foreach($pkall as $pk) {
						if ($s > 0) {
							$pk = $pkall[$s];
							$GetUser = ctMember("", $pk);
							if ($GetUser->RowCount() == 0) {
					?>
								<tr>
									<td colspan="3" align="center" height="50" valign="middle">- Tiada Maklumat Mengenai Permohonan Bantuan Kebajikan -</b></td>
								</tr>
							<?php
							} else {
								// Inside this loop, you can access data for each user by querying the "welfares" table.
								// Example:
								$sWhere = " WHERE (" . $sWhere . ") ";


								//fields selection
								$sSQL = "SELECT a.*
FROM welfares a WHERE ID = '" . $pk . "'";
								$GetWelfare = &$conn->Execute($sSQL);

								$GetWelfare->Move($StartRec - 1);

								$TotalRec = $GetWelfare->RowCount();
								$TotalPage =  ($TotalRec / $pg);

								$welfareType = $GetWelfare->fields(welfareType);
								$welfareName = dlookup("general", "name", "ID=" . tosql($welfareType, "Text"));


								$status = $GetWelfare->fields(status);

								$memberID = dlookup("welfares", "userID", "ID=" . tosql($pk, "Text"));
								$rejectedDate = dlookup("welfares", "rejectedDate", "userID=" . tosql($GetWelfare->fields(userID),	"Text"));
								$remark = dlookup("welfares", "remark", "userID=" . tosql($pk, "Text"));
								$name = dlookup("users", "name", "userID=" .	tosql($GetWelfare->fields(userID),	"Text"));
								$applyDate = dlookup("welfares", "applyDate", "userID=" .	tosql($GetWelfare->fields(userID),	"Text"));
								$approvedDate = dlookup("welfares", "applyDate", "userID=" .	tosql($GetWelfare->fields(userID),	"Text"));

								$loan				= dlookup("welfares", "welfareType", "ID= '" . $pk . "'");
								// $codegroup			= dlookup("general", "parentID", "ID= '" . $loan . "'");
								$prefix				= dlookup("general", "code", "ID= '" . $loan . "'");

								$len = strlen($prefix);

								$getNo = "SELECT MAX(CAST(right(  rnoBond  , 5 ) AS SIGNED INTEGER )) AS nombor FROM welfares where rnoBond like '%" . $prefix . "%'";
								$rsNo = $conn->Execute($getNo);
								if ($rsNo) {
									$nombor = intval($rsNo->fields(nombor)) + 1;
									$nombor = sprintf("%05s",  $nombor);
									$no_bond = $prefix . $nombor;
								} else {
									$no_bond = $prefix . '00001';
								}

							?>

								<tr>
									<td>Nomor Anggota</td>
									<td>&nbsp;<b><? print $memberID; ?></b></td>
								</tr>
								<tr>
									<td>Nama Anggota</td>
									<td>&nbsp;<b><? print $name; ?></b></td>
								</tr>
								<tr>
									<td>Tarikh Mohon</td>
									<td>&nbsp;<b><? print toDate("d/m/Y", $applyDate); ?></b></td>
								<tr>
									<td colspan="2">
										<hr size=1>
									</td>
								</tr>
								<tr>
									<td>Jenis Kebajikan Dipohon</td>
									<td>&nbsp;<? print $welfareName; ?></td>
								</tr>

								<tr>
									<td colspan="2">
										<hr size=1>
									</td>
								</tr>
							<? }
						} //end if
					} //end foreach
					//------------------------ 

					if (count($statusList) <> 0) {

						if ($status == 0) {
							?>
							<td>Status</td>
							<td>
								<select class="form-selectx" name="selStatus">
									<?
									for ($i = 0; $i < count($statusList); $i++) {
										if ($statusVal[$i] < 3)
											print '<option value="' . $statusVal[$i] . '">' . $statusList[$i];
									}
									?>
								</select>
							</td>
			</tr>
			<?
						} else {
							if ($status == 1) {
			?>
				<tr>
					<td>Status</td>
					<td>:
						&nbsp;<font class="greenText"><? print $statusList[$status]; ?></font>
					</td>
				</tr>
				<tr>
					<td>Tarikh Diluluskan</td>
					<td>:&nbsp;&nbsp;<? print toDate("d/m/Y", $GetWelfare->fields(approvedDate)); ?></td>
				</tr>
			<?
							}
							if ($status == 2) {
			?>
				<tr>
					<td>Status</td>
					<td>:&nbsp;<font class="redText"><? print $statusList[$status]; ?></font>
					</td>
				</tr>
				<tr>
					<td>Tarikh Ditolak</td>
					<td>:&nbsp;<? print toDate("d/m/Y", $GetWelfare->fields(rejectedDate)); ?></td>
				</tr>
			<?
							}
							if ($status == 3 or $status == 4) {
			?>
				<tr>
					<td>Status</td>
					<td>:&nbsp;<? print $statusList[$status]; ?></td>
				</tr>
			<?
							}
						}
						if ($status == 0) {
			?>
			<tr>
				<td>Tarikh Kemaskini</td>
				<td><input type="text" class="form-controlx" name="strDate" value="<? print $strDate; ?>" size="15" maxlength="10"></td>
			</tr>

			<tr>
				<td>Nombor Bond </td>
				<td><input type="text" class="form-controlx" name="no_bond" value="<? print $no_bond; ?>" size="15" maxlength="10" readonly></td>
			</tr>
			<tr>
				<td>Catatan</td>
				<td><input type="text" class="form-controlx" name="remark" value="" size="50" maxlength="100"></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<div>&nbsp;</div>
					<input type="submit" name="action" class="btn btn-md btn-primary" value="Kemaskini">
					&nbsp;
					<input type="button" name="batal" value="Batal" class="btn btn-md btn-danger" onclick="Javascript:(window.location.href='?vw=welfare&mn=921')">
					<div>&nbsp;</div>
				</td>
			</tr>
		<?
						} else {
		?>
			<tr>
				<td>Catatan</td>
				<td>:</td>
				<td><? print $remark; ?></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<div>&nbsp;</div>
					<input type="button" name="batal" value="Kembali" class="btn btn-md btn-primary" onclick="Javascript:(window.location.href='?vw=welfare&mn=921')">
				</td>
			</tr>
		<?
						}
					} else {
		?>
		<tr>
			<td colspan="3" align="center">
				<hr size="1"><b>- Tiada rekod mengenai status -</b>
				<hr size="1">
			</td>
		</tr>
	<?
					}
	?>
	<!-- </table> -->
	</td>
	</tr>
		</table>
	</form>
</div>
<?
include("footer.php");
?>