<?php
/*********************************************************************************
*          Project		:	iKOOP.com.my
*          Filename		: 	cmslist.php
*          Date 		: 	03/04/2006
*********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 10;

if (get_session("Cookie_groupID") <> 0 AND get_session("Cookie_groupID") <> 1 AND get_session("Cookie_groupID") <> 2) {
	print '<script>alert("'.$errPage.'");top.location="index.php";</script>';
}

$title     = "Buletin Terkini";

if ($_GET['del']) {
	$ID = $_GET['del'];
	$sqld = "DELETE FROM kandungan WHERE ID = ".$ID;
	$conn->Execute($sqld);
}

$sqlcms = "SELECT * FROM kandungan WHERE ID = 1";
$rsFirst = &$conn->Execute($sqlcms);

$sqlcms = "SELECT * FROM kandungan order by postedDate desc";
$rsContent = &$conn->Execute($sqlcms);
$rsContent->Move($StartRec-1);

$TotalRec = $rsContent->RowCount();
$TotalPage =  ($TotalRec/$pg);

$temp = '<form name="MyForm" action='.$sFileName.' method="post">';
print $temp;

$temp =	'<div class="maroon"><b>'.strtoupper($rsFirst->fields(tajuk)).'</b></div>'
		.'<div>&nbsp;</div>'
		.'<div class="koperasi" style="width: 700px; text-align:left">'.insertspace($rsFirst->fields(kandungan)).'</div>';
print $temp;

$temp =	'<div>&nbsp;</div>'
		.'<div class="maroon"><b>'.strtoupper("Buletin Terkini").'</b></div>'
		.'<div>&nbsp;</div>'
		.'<div style="width: 700px; text-align:left"><table class="blue" border="0" cellspacing="1" cellpadding="0" width="100%" align="center">';
print $temp;

if ($rsContent->RowCount() <> 0) {  
	$bil = $StartRec;
	$cnt = 1;
	$temp = '<tr valign="top">'
				.'<td valign="top">'
				.'<table border="0" cellspacing="0" cellpadding="0" width="100%">'
				.'<tr>'
					.'<td nowrap valign="middle" align="center" width="4"><img src="images/shade-logo-bkrm-03.gif" width="28" height="24" /></td>'
					.'<td nowrap valign="middle" align="left" width="200"><div class="headerblue">Topik</div></td>'
					.'<td nowrap valign="middle" align="center"><div class="headerblue">Tarikh</div></td>'
					.'<td nowrap valign="middle" align="center"><div class="headerblue">Oleh</div></td>';
	print $temp;

	if (get_session("Cookie_groupID") == 1 OR get_session("Cookie_groupID") == 2) {
		$temp		='<td nowrap valign="top" align="center" width="15%"><div class="headerblue">Aktiviti</div></td>'
				.'</tr>'
				.'<tr valign="top"><td class="Data" colspan="5">&nbsp;</td></tr>';
	}
	else
	{
		$temp = '<tr valign="top"><td class="Data" colspan="4">&nbsp;</td></tr>';
	}

	print $temp;

	while (!$rsContent->EOF && $cnt <= $pg) {
		$temp = '<tr>'
					.'<td class="Data" align="right"><div class="navmain">'.$bil.'.</div></td>'
					.'<td class="Data" align="left"><div class="navmain"><a href="cmsView.php?id='.$rsContent->fields(ID).'">'.$rsContent->fields(tajuk).'</a></div></td>'
					.'<td class="Data" align="center"><div class="navmain">'.todate('/',$rsContent->fields(postedDate)) . '</div></td>'
					.'<td class="Data" align="center"><div class="navmain">'.$rsContent->fields(postedBy).'</div></td>';
		print $temp;

		if (get_session("Cookie_groupID") == 1 OR get_session("Cookie_groupID") == 2) {
			$temp	='<td class="Data" align="center"><div class="blue">[<a href="cmsMain.php?id='.$rsContent->fields(ID).'">ubah</a>|<a href="index.php?action=laman_utama&del='.$rsContent->fields(ID).'" onClick="return confirm(\'Anda pasti padam kandungan ini?\')">padam</a>]</div></td>'
				.'</tr>';
			print $temp;
		}
		$cnt++;
		$bil++;
		$rsContent->MoveNext();
	}

	$temp = '<tr><td class="Data" colspan="5">';
	print $temp;

	$temp =	'<div>';
	print $temp;
			if ($TotalRec > $pg) {
				print '
				<table border="0" cellspacing="5" cellpadding="0" class="textFont" width="100%">';
				if ($TotalRec % $pg == 0) {
					$numPage = $TotalPage;
				} else {
					$numPage = $TotalPage + 1;
				}
				print '<tr><td class="textFont" valign="top" align="left" "><div class="navmain">Rekod Dari : <br>';
				for ($i=1; $i <= $numPage; $i++) {
					print '<A href="'.$sFileName.'?&StartRec='.(($i * $pg) + 1 - $pg).'&pg='.$pg.'&filter='.$filter.'">';
					print '<b><u>'.(($i * $pg) - $pg + 1).'-'.($i * $pg).'</u></b></a> &nbsp; &nbsp; ';
				}
				$temp =	'</div>'
						.'</td>'
					.'</tr>'
				.'</table>';
				print $temp;
			}
	$temp =	'</div>';
	print $temp;

	$temp =	'<div>&nbsp;</div>'
			.'<div class="navmain">Jumlah Rekod : <b>' . $rsContent->RowCount() . '</b></div>';
	print $temp;

	$temp =	'</td></tr>';
	print $temp;

	print ' </table>
		</td>
	</tr>';
} else {
	if ($q == "") {
		print '
		<tr><td class="Data" align="center"><hr size=1"><div class="navmain"><b class="textFont">- Tiada Rekod Untuk '.$title.'  -</b></div><hr size=1"></td></tr>';
	} else {
		print '
		<tr><td class="Data" align="center"><hr size=1"><div class="navmain"><b class="textFont">- Carian rekod "'.$q.'" tidak jumpa  -</b></div><hr size=1"></td></tr>';
	}
}
$temp = '</table></div>';
print $temp;

$temp =	'</form>';
print $temp;
?>