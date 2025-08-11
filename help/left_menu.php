<?
include "common.php";
//$conn->debug = true; 
//echo "display kat sini";

//---get first level and push into array-----
$sqlkandungan="SELECT * FROM kandungan WHERE parentMenuID = 0 order by seqNB";
$rsKandungan = mysql_query($sqlkandungan) or die(mysql_error());
$i=0;
while ($rowKandungan = mysql_fetch_assoc($rsKandungan)) {
	$kandungan[$i] = $rowKandungan['Menu'];
	$parentID[$i] = $rowKandungan['MenuID'];
	$i++;
}
?>
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="tree_styles.css"/>
<script type = "text/javascript" src="cooltree.js"></script>
<script type = "text/javascript" src="tree_format.js"></script>
<? 
//-------insert php to javascript----------
//print $kandungan[0]." kandugan sebelum masuk script<br>";
//utk debug comment line 27,28,29 dan run. akan nampak structure tree asal
print '<script>
//-----------start declaration nodes--------------------------------------
var TREE_NODES = [';
$count=0;
$x=0;

//----------looping array to start draw tree with menu where parent=0
//print $kandungan." kandugan selepas script<br>";
foreach($kandungan as $parent) { 
//	print "parent".$parent;
	print '
	["<a href=\"page.php?idmenu='.$parentID[$x].'\" class=\"link\" target=\"mainFrame\">'.$parent.'</a>", null, null,';
	//----get second level -----------------------------------------------
	$count++;
	$getSub = "SELECT * FROM kandungan WHERE parentMenuID = ".$parentID[$x]." order by seqNB";
	$rsSub = mysql_query($getSub) or die($getSub . mysql_error());
	$j=mysql_num_rows($rsSub);
	$count2=0;
	//------draw second level------------
	while ($rowSub = mysql_fetch_assoc($rsSub)) {
		$count2++;
		$kandunganSub = $rowSub['Menu'];
		$subID = $rowSub['MenuID'];
		$helpcont = $rowSub['kandunganID']; 
		print'["<a href=\"page.php?idhelp='.$helpcont.'&idmenu='.$subID.'\" class=\"link\" target=\"mainFrame\" >'.$kandunganSub.'</a>", null, null,';			
				// ---start third level ---------------------------------------------
				$getSub2 = "SELECT * FROM kandungan WHERE parentMenuID = ".$subID." order by seqNB";
				$rsSub2 = mysql_query($getSub2) or die($getSub2 . mysql_error());
				$k=mysql_num_rows($rsSub2);
				$count3=0;
				//-------draw third level--------------
				while ($rowSub2 = mysql_fetch_assoc($rsSub2)) {
					$count3++;
					$kandunganSub2 = $rowSub2['Menu'];
					$subID2 = $rowSub2['MenuID'];
					print '["<a href=\"page.php?idhelp='.$helpcont.'&idmenu='.$subID2.' \" class=\"link\" target=\"mainFrame\">'.$kandunganSub2.'</a>", null, null,';

						// ---start fourth level ---------------------------------------------
						$getSub4 = "SELECT * FROM kandungan WHERE parentMenuID = ".$subID2." order by seqNB";
						$rsSub4 = mysql_query($getSub4) or die($getSub4 . mysql_error());
						$l=mysql_num_rows($rsSub4);
						$count4=0;
						//-------draw fourth level--------------
						while ($rowSub4 = mysql_fetch_assoc($rsSub4)) {
							$count4++;
							$kandunganSub4 = $rowSub4['Menu'];
							$subID4 = $rowSub4['MenuID'];
							print '["<a href=\"page.php?idhelp='.$helpcont.'&idmenu='.$subID4.' \" class=\"link\" target=\"mainFrame\">'.$kandunganSub4.'</a>", null, null,';

								// ---start fifth level ---------------------------------------------
								$getSub5 = "SELECT * FROM kandungan WHERE parentMenuID = ".$subID4." order by seqNB";
								$rsSub5 = mysql_query($getSub5) or die($getSub5 . mysql_error());
								$m=mysql_num_rows($rsSub5);
								$count5=0;
								//-------draw fifth level--------------
								while ($rowSub5 = mysql_fetch_assoc($rsSub5)) {
									$count5++;
									$kandunganSub5 = $rowSub2['Menu'];
									$subID5 = $rowSub5['MenuID'];
									print '["<a href=\"page.php?idhelp='.$helpcont.'&idmenu='.$subID5.' \" class=\"link\" target=\"_blank\">'.$kandunganSub5.'</a>", null, null,';

										// ---start sixth level ---------------------------------------------
										$getSub6 = "SELECT * FROM kandungan WHERE parentMenuID = ".$subID5." order by seqNB";
										$rsSub6 = mysql_query($getSub6) or die($getSub6 . mysql_error());
										$n=mysql_num_rows($rsSub6);
										$count6=0;
										//-------draw sixth level--------------
										while ($rowSub6 = mysql_fetch_assoc($rsSub6)) {
											$count6++;
											$kandunganSub6 = $rowSub6['Menu'];
											echo $subID6 = $rowSub['MenuID'];
											print '["<a href=\"page.php?idhelp='.$helpcont.'&idmenu='.$subID6.' \" class=\"link\" target=\"_blank\">'.$kandunganSub6.'</a>", null, null,';
											if ($count6<$n) print ',';
										}
										//----end sixth level---------------------
										
									print ']';
									if ($count5<$m) print ',';
								}
								//----end fifth level---------------------
				
							print ']';
							if ($count4<$l) print ',';
						}
						//----end fourth level---------------------
				
					print ']';
					if ($count3<$k) print ',';
				}
				//----end third level---------------------
				
			print ']';
		if ($count2<$j) print ',';
	} // end of while rowSub
	print ']';
	if ($count<$i) print ',';
	$x++;
} //end of foreach
print '
];
//-----------end declaration nodes--------------------------------------
</script>';
//echo "hujung script"; 
?>
</head>
<body bgcolor="#f0f0f0">
<script>
//display tree in javascript
var tree1 = new COOLjsTree("tree1", TREE_NODES, TREE_FORMAT);
</script>
</body>
</html>